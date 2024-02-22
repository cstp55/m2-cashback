define([], function () {
    'use strict';

    return {
        getUrlForCashbackValue: function(quote) {
            return 'rest/V1/cashback/value/' + quote.getQuoteId();
        }
    };
});
