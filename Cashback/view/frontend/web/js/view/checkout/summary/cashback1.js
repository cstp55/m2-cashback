/**
 * Aprilo Software.
 *
 * @category Aprilo Software Private Limited
 * @package aprilo_Cashback
 * @author Aprilo
 * @copyright Copyright (c) Aprilo Software Private Limited (https://Aprilo.com)
 * @license https://store.Aprilo.com/license.html
 */
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
                template: 'Aprilo_Cashback/checkout/summary/cashback1'
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
