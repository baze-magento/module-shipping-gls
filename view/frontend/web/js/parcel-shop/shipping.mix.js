define(
    [
        'jquery',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'parcelShop'
    ], function ($,
                 addressList,
                 quote,
                 selectShippingMethodAction,
                 modal,
                 checkoutData,
                 setShippingInformationAction,
                 stepNavigator,
                 parcelShop) {
        'use strict';
        
        // Caution: there are two ways to load "shippingMethod".
        // Class methods receive shippingMethod as an object, but "quote.shippingMethod()" is a FUNCTION that returns that object.
        // Be careful to invoke them correctly.
        
        // ---- Private functions ----
        
        var validateLocationChosen = function(shipping) {
            if (!parcelShop.isLocationIdSet() && parcelShop.methodCodeIs(quote.shippingMethod().method_code)) {
                shipping.errorValidationMessage($.mage.__('Please choose a parcel shop in order to use this shipping method.'));
                return false;
            } else {
                parcelShop.clearLocationId();
                return true;
            }
        };
        
        var checkTelephone = function (shipping) {
            if (parcelShop.methodCodeIs(quote.shippingMethod().method_code) && !quote.shippingAddress().telephone) {
                shipping.errorValidationMessage($.mage.__('You have to set a phone number to ship to a GLS Parcel Shop.'));
                return false;
            }
            return true;
        };
        
        var openPopup = function(shippingMethod) {
            if (shippingMethod !== null && parcelShop.methodCodeIs(shippingMethod.method_code)) {
                parcelShop.initSelectionPopup($, quote, modal, checkoutData, addressList);
            }
        };
        
        // ---- On load, trigger popup if selection is the default ----
        // There's no good 'ready' event in Require, so this references the callback list to determine completion
        
        var checkIfReady = function() {
            if ($.isEmptyObject(require.s.contexts._.registry)) {
                // Defer a little bit longer to give time for the browser to update radios and Magento to catch up
                setTimeout(whenReady, 500);
            }
            else {
                setTimeout(checkIfReady, 100);
            }
        };
        var whenReady = function() {
            openPopup(quote.shippingMethod());
        };
        setTimeout(checkIfReady, 100);
        
        // ---- Class extension declaration ----
        var mixin = {
            // Called whenever the selection radios are interacted with.
            selectShippingMethod: function (shippingMethod) {
                parcelShop.clearLocationId();
                parcelShop.clearChosenLocationText();
                openPopup(shippingMethod);
                return this._super();
            },
            // Called when the step is submitted, and proceeds to the next step.
            setShippingInformation: function () {
                if (this.validateShippingInformation() && checkTelephone(this) && validateLocationChosen(this)) {
                    this._super();
                }
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    });