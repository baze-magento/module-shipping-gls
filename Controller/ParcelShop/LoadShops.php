<?php

namespace Baze\ShippingGLS\Controller\ParcelShop;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Baze\ShippingGLS\Helper\Tools;
use Magento\Framework\Controller\Result\JsonFactory;
use Baze\ShippingGLS\Model\ParcelShop\ShopLoaderService;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;

class LoadShops extends Action
{

    protected $_helperTools;
    protected $_resultJsonFactory;
    protected $_service;
    protected $_resultPageFactory;
    protected $_session;

    public function __construct(
        Context $context,
        Tools $helperTools,
        JsonFactory $resultJsonFactory,
        ShopLoaderService $service,
        PageFactory $resultPageFactory,
        Session $session
    ){
        $this->_helperTools = $helperTools;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_service = $service;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_session = $session;

        return parent::__construct($context);
    }

    public function execute()
    {
        $zipCode = $this->getRequest()->getParam('zipCode');
        $address = $this->getRequest()->getParam('address');

        $countryCode = 'DK';

        if (!empty($zipCode)) {
            $locationList = $this->_service->load($zipCode, $address, $countryCode);
            if ($locationList) {
                $outputArray = [];

                foreach ($locationList->SearchNearestParcelShopsResult->parcelshops->PakkeshopData as $location) {

                    if (!$this->isShopValid($location)) {
                        continue;
                    }
                    $outputArray[] = [
                        'Id' => $location->Number,
                        'Name' => $location->CompanyName,
                        'Address' => $location->Streetname,
                        'ZipCode' => $location->ZipCode,
                        'City' => $location->CityName,
                        'Latitude' => $location->Latitude,
                        'Longitude' => $location->Longitude,
                        'OpenHours' => $location->OpeningHours->Weekday
                    ];
                }
            }else {
                $errorText = __("Unable to load the shop list.");
                if (strlen($this->_service->lastError) > 0) {
                    $errorText .= " ({$this->_service->lastError})";
                }
                $outputArray['errorCode'] = '';
                $outputArray['errorDscr'] = $errorText;
            }
        }else {
            $outputArray['errorCode'] = '';
            $outputArray['errorDscr'] = __("A Zipcode or Address must be set.");
        }

        $resultPage = $this->_resultPageFactory->create();

        $block = $resultPage->getLayout()
            ->createBlock('Baze\ShippingGLS\Block\ParcelShopList')
            ->setTemplate('Baze_ShippingGLS::list_parcel_shops.phtml');

        $block->setLocations($outputArray);

        $outputHtml = $block->toHtml();

        $resultJson = $this->_resultJsonFactory->create();

        return $resultJson->setData(['html' => $outputHtml]);
    }

    /**
     * @param $relay
     * @return bool
     */
    private function isShopValid($location)
    {
        if (property_exists($location, 'CompanyName')
            && property_exists($location, 'Number')
            && property_exists($location, 'Streetname')
            && property_exists($location, 'ZipCode')
            && property_exists($location, 'CityName')
            && property_exists($location, 'Latitude')
            && property_exists($location, 'Longitude')
        ) {
            return true;
        }
        return false;
    }
}

