var config = {
    map: {
        '*': {
            'parcelShop': 'Baze_ShippingGLS/js/parcel-shop/parcel-shop'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {'Baze_ShippingGLS/js/parcel-shop/shipping.mix': true},
            'Magento_Checkout/js/action/set-shipping-information': {'Baze_ShippingGLS/js/parcel-shop/set-shipping-information.mix': true}
        }
    }
};
