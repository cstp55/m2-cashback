define([
    'Magento_Checkout/js/model/quote',
    'Codelab_Cashback/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'ko'  
], function (quote, resourceUrlManager, storage, errorProcessor, ko) {  
    'use strict';

    var cache = null;
    var valueObservable = ko.observable(''); 

    return {
        getValue: function() {
            if (cache !== null) {
                return valueObservable(); 
            }
    
            var url = resourceUrlManager.getUrlForCashbackValue(quote);
            var payload = {
                cartId: quote.getQuoteId()
            };
    
            storage.get(url, JSON.stringify(payload))
            .done(function(response) {
                if(response){
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(response, 'text/html');
                    var price = doc.querySelector('.price').textContent;
                    console.log(price);
                    valueObservable(price); 
                    cache = price;
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Request failed:", textStatus, errorThrown);
                console.log(jqXHR.responseText);
            });
            return valueObservable();  
        }
    };
});
