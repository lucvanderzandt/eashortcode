/**
 * Get the consumer keys from plugin settings
 */
var consumer_key = scriptParams.consumer_key;
var consumer_secret = scriptParams.consumer_secret;

/**
 * Setup other variables
 */
var xhr = new XMLHttpRequest();
var url = window.location.origin + '/wp-json/wc/v2/products';
var products = [];
var success = false;

/**
 * Configure XMLHttpRequest to retrieve product categories and put the
 * id and name in a key/value sorted JavaScript array
 */
xhr.onreadystatechange = function() {
    if( this.readyState == 4) {
       if( this.status == 200 ) {
           var result = JSON.parse(this.responseText);
           success = true;
           for( var i = 0; i < result.length; i++ ) {
               products[i] = {
                   value: result[i].id,
                   text: result[i].name
               };
           }
       }
    }
}

/**
 * Do XMLHttpRequest
 */
xhr.open('GET', url, true);
xhr.setRequestHeader('Authorization', 'Basic ' + btoa(consumer_key + 
                     ':' + consumer_secret));
xhr.send();

/**
 * Create TinyMCE interface to select a product and insert its 
 * add_to_chart link in the editor
 */
(function() {  
    tinymce.create( 'tinymce.plugins.eashortcode', {
        init: function( editor, url ) {
            // Add button in TinyMCE
            editor.addButton('add_product', {
                title: 'Add product',
                image: url.replace('js', 'img') + '/icon.png',
                cmd: 'choose_product'
            });
            
            // Add event to show a dialog to choose a product
            editor.addCommand('choose_product', function() {
                if( !success ) { // Couldn't retrieve products
                    editor.windowManager.alert(
                        'The key/secret pair you provided is invalid.' +
                        'Please check your settings'
                    );
                    return;
                }
                var choice = null;
                editor.windowManager.open({
                    title: 'Add product',
                    width: 400,
                    height: 100,
                    body: [{
                        type: 'listbox',
                        name: 'product',
                        label: 'Product',
                        'values': products,
                        onPostRender: function() {
                            choice = this;
                        }
                    }],
                    onsubmit: function( e ) {
                        // Insert the WooCommerce link
                        editor.insertContent('[add_to_cart id="' + 
                            choice.value() + '"]');
                    }
                });
            });
        },
        
        createControl: function(n, cm) {
            return null;
        }
    });
    
    tinymce.PluginManager.add('eashortcode_class', tinymce.plugins.eashortcode);  
    
})();