<?php

// The GLS DK API is exposed in SOAP 1.1, SOAP 1.2, HTTP GET and HTTP POST methods.
// This implementation uses SOAP 1.2.
// See http://www.gls.dk/webservices_v4/wsShopFinder.asmx for API details and test interface.

namespace Baze\ShippingGLS\Model\ParcelShop;

use Baze\ShippingGLS\Helper\Tools;
use \SoapClient;

class ShopLoaderService
{
    public $lastError = '';

    protected $_helperTools;

    const WSDL_URL = "http://www.gls.dk/webservices_v4/wsShopFinder.asmx?WSDL";

    /**
     * Service constructor.
     * @param $_helperTools
     */
    public function __construct(Tools $_helperTools)
    {
        $this->_helperTools = $_helperTools;
    }

    function load($zipCode, $address, $countryCode)
    {
        try{
            $soapClient = new SoapClient(
                self::WSDL_URL,
                ['trace' => true]
            );

            $parameters = [
                'street' => $address,
                'zipcode' => $zipCode,
                'countryIso3166A2' => $countryCode,
                'Amount' => 10
            ];

            $result = $soapClient->__soapCall('SearchNearestParcelShops', [$parameters]);

            return $result;
        }catch (\SoapFault $fault){
            $this->_helperTools->glsLog('Error retrieving GLS Parcel Shops: '.$fault->getMessage(), 'err');
            if ($fault->getMessage() == 'Server was unable to process request. ---> Address not found') {
                $this->lastError = __("Address not found");
            }
            return false;
        }
    }
}