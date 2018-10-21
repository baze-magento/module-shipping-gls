define([
    'jquery',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, checkoutData, quote, confirmation) {
    
    const methodCode = 'glsparcelshop';
    const elementSelectors = Object.freeze({
        popup: '#parcelshop_popup_wrapper',
        addressSearch: '#parcelshop_address',
        zipcodeSearch: '#parcelshop_zipcode',
        loadSpinner: '#parcelshop_popup_wrapper .load_spinner',
        outputContainer: '#parcelshop_popup_wrapper .body',
        methodListLabel: '#label_method_glsparcelshop_glsparcelshop',
        chosenLocationOutput: '#label_method_glsparcelshop_glsparcelshop .chosen_location',
        locationListEntry: '.location',
        locationListName: '.name',
        locationListAddress: '.address',
        locationListCity: '.city',
        locationListZipcode: '.zipcode',
        locationListId: '.id',
        locationSelectButton: 'button.choose'
    });
    const chosenLocationOutputClass = "chosen_location";

    var saveUrl;
    var loadUrl;
    
    var locationId;
    var locationName;
    var locationAddress;
    var locationCity;
    var locationZipcode;
    
    var popup;
    
    // ---- Core functions ----
    
    var initSelectionPopup = function ($, quote, modal, checkoutData, addressList) {
            var zipcode, street, shippingAddress;

            if (addressList().length > 0) {
                shippingAddress = quote.shippingAddress();
            } else if (checkoutData.getShippingAddressFromData()) {
                shippingAddress = checkoutData.getShippingAddressFromData();
            }

            if (shippingAddress != null) {
                zipcode = shippingAddress.postcode;
                street = shippingAddress.street['0'];

                if (zipcode !== undefined) $('#parcelshop_zipcode').val(zipcode);
                if (street !== undefined) $('#parcelshop_address').val(street);
            }
            
            var popup = initPopup(modal);
            if (!popup.options.isOpen) {
                openPopup();
                if (zipcode !== undefined) {
                    search();
                }
            }
            clearChosenLocationText();
    };
    
    var search = function () {
        $.ajax({
            url: getLoadUrl(),
            type: 'POST',
            dataType: 'json',
            data: {
                zipCode: getElement('zipcodeSearch').val(),
                address: getElement('addressSearch').val()
            },
            beforeSend: function () {
                getElement('loadSpinner').show();
            },
            success: function (response) {
                var output = response.html;
                var container = getElement('outputContainer');
                
                if (output.length == 0) {
                    container.html($.mage.__('No relays found'));
                } else {
                    container.html(output);
                }
                getElement('locationListEntry', container).each(function (index, element) {
                    var button = getElement('locationSelectButton', element);
                    var markupIndex = button.attr("data-index");
                    
            
            
                    button.click(function () {
                        confirmSelection(markupIndex);
                    });
                });
            },
            complete: function () {
                getElement('loadSpinner').hide();
            }
        });
    };
    
    var confirmSelection = function (index) {
        console.log(index);
        var locationElement = $("#location_" + index, getElement('popup'));
        console.log(locationElement);
        if (locationElement !== null) {
            var name = getElement('locationListName', locationElement).text();
            var address = getElement('locationListAddress', locationElement).text();
            var city = getElement('locationListCity', locationElement).text();
            var zipcode = getElement('locationListZipcode', locationElement).text();

            confirmation({
                title: $.mage.__('Confirm Location'),
                content: $.mage.__('Please confirm shipping to this location:') +
                    '<br>' + name +
                    '<br>' + address +
                    '<br>' + zipcode + ' ' + city,
                actions: {
                    confirm: function () {
                        selectLocation(index);
                    },
                    cancel: function () {
                    },
                    always: function () {
                    }
                }
            });
        } else console.error($.mage.__('Error : unable to select null location '+index));
    };
    
    var selectLocation = function(index) {
        var locationElement = $("#location_" + index, getElement('popup'));

        if (locationElement !== null) {
            var name = getElement('locationListName', locationElement).text();
            var address = getElement('locationListAddress', locationElement).text();
            var city = getElement('locationListCity', locationElement).text();
            var zipcode = getElement('locationListZipcode', locationElement).text();
            var id = getElement('locationListId', locationElement).text();

            saveLocationToSession(id, name, address, zipcode, city);

            setLocationName(name);
            setLocationAddress(address);
            setLocationCity(city);
            setLocationZipcode(zipcode);
            setLocationId(id);

            addChosenLocationText();

            closePopup();
        } else console.error($.mage.__('Error : unable to select null location '+index));
    };
    
    var saveLocationToSession = function (id, name, address, zipcode, city) {
        if (id.length != 0) {
            $.ajax({
                url: getSaveUrl(),
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    name: name,
                    address: address,
                    zipcode: zipcode,
                    city: city
                },
                complete: function () {
                }
            });
        }
    };
    
    // ---- Helper functions ----
    
    var getElement = function(name, parent) {
        if (parent == null) {
            return $(elementSelectors[name]);
        } else {
            return $(elementSelectors[name], parent);
        }
    };
    
    var methodCodeIs = function(methodCodeInput) {
        return methodCode == methodCodeInput;
    };  
    
    var addChosenLocationText = function() {
        var output = '<p>' + getLocationName() + '</p>' +
            '<p>' + getLocationAddress() + '</p>' +
            '<p>' + getLocationZipcode() + '</p>' +
            '<p>' + getLocationCity() + '</p>' +
            '<p class="link">' + $.mage.__('Change selection') + '</p>';
        
        if (!(getElement('chosenLocationOutput').length)) {
            $("<div>").attr('class', chosenLocationOutputClass).appendTo(elementSelectors.methodListLabel);
        }
        getElement('chosenLocationOutput').html(output);
    };
    var clearChosenLocationText = function() {
        var el = getElement('chosenLocationOutput');
        if (el.length) el.html('');
    };  

    // ---- Popup control functions ----
    
    var initPopup = function(modal) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            buttons: []
        };

        var el = getElement('popup');

        if (popup === undefined) {
            popup = modal(options, el);
        }
        return popup;
    };
    
    var openPopup = function() {
        getElement('popup').modal("openModal");
    };
    
    var closePopup = function() {
        getElement('popup').modal("closeModal");
    };
    
    // ---- I/O functions ----
    
    var getSaveUrl = function() {
        return saveUrl;
    };
    var setSaveUrl = function(input) {
        saveUrl = input;
    };
    var getLoadUrl = function() {
        return loadUrl;
    };
    var setLoadUrl = function(input) {
        loadUrl = input;
    };
    
    var getLocationId = function() {
        return locationId;
    };
    var setLocationId = function(input) {
        locationId = input;
    };
    var clearLocationId = function() {
        locationId = '';
    };
    var isLocationIdSet = function() {
        return (typeof(locationId) == "string" && locationId.length > 0);
    };
    
    var getLocationName = function() {
        return locationName;
    };
    var setLocationName = function(input) {
        locationName = input;
    };
    var getLocationAddress = function() {
        return locationAddress;
    };
    var setLocationAddress = function(input) {
        locationAddress = input;
    };
    var getLocationCity = function() {
        return locationCity;
    };
    var setLocationCity = function(input) {
        locationCity = input;
    };
    var getLocationZipcode = function() {
        return locationZipcode;
    };
    var setLocationZipcode = function(input) {
        locationZipcode = input;
    };

    // Public functions
    return {
        search: search,
        initSelectionPopup: initSelectionPopup,
        
        methodCodeIs: methodCodeIs,
        
        clearChosenLocationText: clearChosenLocationText,
        
        setSaveUrl: setSaveUrl,
        setLoadUrl: setLoadUrl,
        
        getLocationId: getLocationId,
        setLocationId: setLocationId,
        clearLocationId: clearLocationId,
        isLocationIdSet: isLocationIdSet,
        
        getLocationName: getLocationName,
        getLocationAddress: getLocationAddress,
        getLocationCity: getLocationCity,
        getLocationZipcode: getLocationZipcode
    };
});
