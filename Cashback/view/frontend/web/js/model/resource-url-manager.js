/**
 * Aprilo Software.
 *
 * @category Aprilo Software Private Limited
 * @package aprilo_Cashback
 * @author Aprilo
 * @copyright Copyright (c) Aprilo Software Private Limited (https://Aprilo.com)
 * @license https://store.Aprilo.com/license.html
 */
define([], function () {
    'use strict';

    return {
        getUrlForCashbackValue: function(quote) {
            return 'rest/V1/cashback/value/' + quote.getQuoteId();
        }
    };
});
