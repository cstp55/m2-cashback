define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Aprilo_Cashback/js/model/cashback',
], function (Component, quote, cashback) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aprilo_Cashback/checkout/summary/cashback'
        },
        getValue: function() {
            return cashback.getValue();
        }
    });
});
