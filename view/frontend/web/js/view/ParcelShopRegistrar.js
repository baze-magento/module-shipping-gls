define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Baze_ShippingGLS/js/model/ParcelShopRateProcessor',
        'Magento_Checkout/js/model/shipping-save-processor',
        'Baze_ShippingGLS/js/model/ParcelShopAddressSaveProcessor'
    ],
    function (
        Component,
        shippingRateService,
        customShippingRateProcessor,
        shippingSaveProcessor,
        customShippingSaveProcessor
    ) {
        'use strict';

        /** Register rate processor */
        shippingRateService.registerProcessor('GLSParcelShopAddress', customShippingRateProcessor);

        /** Register save shipping address processor */
        shippingSaveProcessor.registerProcessor('GLSParcelShopAddress', customShippingSaveProcessor);

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
