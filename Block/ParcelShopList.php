<?php

namespace Baze\ShippingGLS\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;


class ParcelShopList extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'list_parcel_shops.phtml';

    private $_locations = [];

    /**
     * ListRelays constructor.
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->_locations;
    }

    /**
     * @param array $listRelays
     */
    public function setLocations(array $locations)
    {
        $this->_locations = $locations;
    }

    /**
     * Formats a time string for readability.
     * Input format is expected as "hh:mm", i.e. 2 PM is "14:00".
     * Output format is "hh:mm".
     *
     * Yes, this means it doesn't do anything. But we have a single point to change if the format is deemed not acceptable.
     *
     * @param string $hour
     */
    public function formatHours($hour)
    {
        if (is_string($hour) && strlen($hour) == 5) {
            return $hour;
        }else {
            return '';
        }
    }
}