define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'ko'
    ],
    function (Component, quote, totals, ko) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Codelab_Cashback/checkout/summary/cashback1'
            },

            /**
             * @return {Boolean}
             */
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0; // Change the condition as needed
            },

            /**
             * @return {String}
             */
            getCashbackValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('cashback').value;
                }
                return this.getFormattedPrice(price);
            },

            /**
             * @return {Number}
             */
            getPureValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('cashback').value;
                }
                return price;
            }
        });
    }
);
