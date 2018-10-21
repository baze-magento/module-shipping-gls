<?php

namespace Baze\ShippingGLS\Controller\ParcelShop;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Baze\ShippingGLS\Helper\Tools;


class SetSessionSelectedShop extends Action
{

    protected $_checkoutSession;
    protected $_helperTools;


    /**
     * SetRelayInformationSession constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param Tools $helperTools
     */
    public function __construct(Context $context, Session $checkoutSession, Tools $helperTools)
    {
        $this->_checkoutSession = $checkoutSession;
        $this->_helperTools = $helperTools;

        return parent::__construct($context);
    }

    public function execute()
    {
        $info = [];

        $info['id'] = $this->getRequest()->getParam('id') ? : '';
        $info['name'] = $this->getRequest()->getParam('name') ? : '';
        $info['address'] = $this->getRequest()->getParam('address') ? : '';
        $info['zipcode'] = $this->getRequest()->getParam('zipcode') ? : '';
        $info['city'] = $this->getRequest()->getParam('city') ? : '';

        if (!empty($this->_checkoutSession->getDestinationShopInformation())) {
            $this->_checkoutSession->setDestinationShopInformation([]);
        }

        if (!array_search("", $info)) {
            $this->_checkoutSession->setDestinationShopInformation($info);
        }else {
            $this->_helperTools->glsLog(__('Error: Failed to assign shop to session due to missing information. Debug data: ').json_encode($info), 'err');
        }
    }
}