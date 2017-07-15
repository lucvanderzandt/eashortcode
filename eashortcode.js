var consumer_key = scriptParams.consumer_key;
var consumer_secret = scriptParams.consumer_secret;

var xhr = new XMLHttpRequest();
var url = 'https://viakunst-utrecht.nl/wp-json/wc/v2/products';
var products = [];
var success = false;

xhr.onreadystatechange = function() {
    if( this.readyState == 4) {
       if(this.status == 200 ) {
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

xhr.open('GET', url, true);
xhr.setRequestHeader('Authorization', 'Basic ' + btoa(consumer_key + ':' + consumer_secret));
xhr.send();


(function() {  
    tinymce.create( 'tinymce.plugins.eashortcode', {
        init: function( editor, url ) {            
            editor.addButton('add_product', {
                title: 'Add product',
                image: url + '/img/icon.png',
                cmd: 'choose_product'
            });
            
            editor.addCommand('choose_product', function() {
                if( !success ) {
                    editor.windowManager.alert('The key/secret pair you provided is invalid. Please check your settings');
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
                        editor.insertContent('[add_to_cart id="' + choice.value() + '"]');
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