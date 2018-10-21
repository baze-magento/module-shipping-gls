define(
    [
        'jquery',
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/quote',
        'parcelShop'
    ], function ($,
                 wrapper,
                 quote,
                 parcelShop) {

        return function (setShippingInformationAction) {

            var isRelevantMethod = function() {
                return parcelShop.methodCodeIs(quote.shippingMethod().method_code);
            };

            return wrapper.wrap(setShippingInformationAction, function (originalAction) {
                if (isRelevantMethod()) {
                    var shippingAddress = quote.shippingAddress();
                    if (shippingAddress) {
                        shippingAddress['company'] = parcelShop.getLocationName();
                        shippingAddress['city'] = parcelShop.getLocationCity();
                        shippingAddress['street'][0] = parcelShop.getLocationAddress();
                        shippingAddress['postcode'] = parcelShop.getLocationZipcode();
                    }
                }

                return originalAction();
            });
        };
    }
);