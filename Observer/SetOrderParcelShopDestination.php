<?php

namespace Baze\ShippingGLS\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Checkout\Model\Session;
use Baze\ShippingGLS\Helper\Tools;


class SetOrderParcelShopDestination implements ObserverInterface
{

    protected $_checkoutSession;
    protected $_helperTools;

    public function __construct(Session $checkoutSession, Tools $helperTools)
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_helperTools = $helperTools;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order) {
            $info = $this->_checkoutSession->getDestinationShopInformation();
            
            $shippingAddress = $order->getShippingAddress();
            $shippingMethod = $order->getShippingMethod();
            $info = $this->_checkoutSession->getDestinationShopInformation();
            $this->_checkoutSession->setDestinationShopInformation([]);

            // {carriercode}_{methodcode}
            if ($shippingMethod == 'glsparcelshop_glsparcelshop' ) {
                if (!empty($info) && !array_search("", $info)) {
                    $order->setLocationId($info['id']);
                    $shippingAddress->setCompany($info['name']);
                    $shippingAddress->setStreet($info['address']);
                    $shippingAddress->setPostCode($info['zipcode']);
                    $shippingAddress->setCity($info['city']);
                }else {
                    $this->_helperTools->glsLog(__('Error: Can\'t set order location; missing session information.'),
                        'err');
                }
            }
        }else {
            $this->_helperTools->glsLog(__('Error: Can\'t set order location; unable to load order.'),
                'err');
        }
    }
}