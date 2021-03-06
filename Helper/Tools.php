<?php

namespace Baze\ShippingGLS\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Tools extends AbstractHelper
{

    /***********************************************************
     * General tools functions - linked to Magento system
     ***********************************************************/
    /**
     * Get values from the Magento configuration
     * @param string $field : field in the Magento configuration. Get exact name from /etc/adminhtml/system.xml file
     * @param string $group : group the searched field belongs to. Get exact name from /etc/adminhtml/system.xml file
     * @param string $section : section in the Magento configuration.
     * @param $scope : scope of the config value
     * @param null|string $scopeCode : scope of the config value
     * @return mixed : saved value of the field
     */
    public function getConfigValue(
        $field = '',
        $group = '',
        $section = '',
        $scope = ScopeConfigInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        // Construct field path in configuration
        if (empty($section) || empty($group) || empty($field)) {
            return '';
        }
        $configValue = $this->scopeConfig->getValue($section.'/'.$group.'/'.$field, $scope, $scopeCode);

        return $configValue;
    }

    /**
     * Function to log message in a specific GLS file (var/log/gls.log)
     * @param string $message : message to add
     * @param string $messageType : type of the message
     * @param array $details : (context) array of values to add details
     * @param string $fileName : name of the file to log into
     */
    public function glsLog(
        $message,
        $messageType = 'info',
        array $details = [],
        $fileName = 'gls.log'
    ) {
        if (!is_string($message) || !is_string($messageType) || !is_string($fileName)) {
            return;
        }
        $availableTypes = ['info', 'err', 'debug'];
        $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/'.$fileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if (!in_array($messageType, $availableTypes)) {
            $messageType = 'info';
        }
        $logger->$messageType($message, $details);
    }

    public function getCurrentUserAttribute($attributeName)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $session = $objectManager->create('Magento\Customer\Model\Session');
        $customerRepository = $objectManager->create('Magento\Customer\Model\ResourceModel\CustomerRepository');
        $customer = $customerRepository->getById($session->getCustomerId());
        $attribute = $customer->getCustomAttribute($attributeName);
        if ($attribute === null) {
            return false;
        } else {
            return $attribute->getValue();
        }
    }
}
