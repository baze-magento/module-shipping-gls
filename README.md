# Shipping-GLS

Includes a shipping method for users to select a GLS Denmark Parcel Shop as their shipping destination.

## Architecture

Standalone module.

## Properties

Module name: Baze_ShippingGLS

Configured through Sales -> Shipping Methods.

## Code Details

The Method is straightforward, with no custom functionality in its declaration. It can be found [here.](https://github.com/baze-magento/module-shipping-gls/blob/master/Model/ParcelShop/Shipping.php)

The front-end is significantly more complex. Major components are as follows:
* Selection list: Declared and added to the store page as the 'ParcelShopSelector' Block. Markup includes event listeners and the popup window, but not the invocation code.
* [Shipping Class Extension:](https://github.com/baze-magento/module-shipping-gls/blob/master/view/frontend/web/js/parcel-shop/shipping.mix.js) Client-side extension. Extends the "SelectShipipngMethod" function to invoke the popup when the chosen method is picked, and the "SetShippingInformation" function to validate necessary information. 
* [Selection list Controller:](https://github.com/baze-magento/module-shipping-gls/blob/master/view/frontend/web/js/parcel-shop/parcel-shop.js) JS class that includes control and helper methods for the popup and the GLS-related logic. Uses AJAX to retrieve data from the API, via the 'ParcelShopList' block.
* Parcel Shop list: Declared as the 'ParcelShopList' Block. Markup is raw HTML, which the block wraps in JSON for returning to an AJAX request.
* [Shipping Function Extension:](https://github.com/baze-magento/module-shipping-gls/blob/master/view/frontend/web/js/parcel-shop/set-shipping-information.mix.js) Client-side extension. Applies the chosen shipping destination when the steps change.
* [Order Creation Event Listener:](https://github.com/baze-magento/module-shipping-gls/blob/master/Observer/SetOrderParcelShopDestination.php) Magento's order scripts don't submit the destination information directly; they try to reference an existing Address attached to the User. In order to not have to add a new user Address for each Parcel Shop, we [save the Shipping Address info to the Session via AJAX,](https://github.com/baze-magento/module-shipping-gls/blob/master/Controller/ParcelShop/SetSessionSelectedShop.php) and the Event Listener updates the Order's Shipping Address once it's submitted.

## Credit

Major functionality is inspired by Acyba's "GLS France" module, which has no public repository available. The invocation of the popup window, and the Event Listener for setting shipping info, are both concepts from Acyba's module.
