<?php

namespace Baze\ShippingGLS\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;

class ParcelShopSelector extends \Magento\Framework\View\Element\Template
{
    protected $_template = "parcel_shop_selector.phtml";

    /**
     * Selector constructor.
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function getAjaxLoadUrl()
    {
        return $this->getUrl("gls/parcelshop/LoadShops");
    }

    public function getAjaxSaveUrl()
    {
        return $this->getUrl("gls/parcelshop/SetSessionSelectedShop");
    }
}