define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Codelab_Cashback/js/model/cashback',
], function (Component, quote, cashback) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Codelab_Cashback/checkout/summary/cashback'
        },
        getValue: function() {
            return cashback.getValue();
        }
    });
});
