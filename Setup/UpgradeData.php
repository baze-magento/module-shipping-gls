<?php
namespace Baze\ShippingGLS\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @package Baze\LoginByAttributes\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $lastVersion = $context->getVersion();
        // 1.1.0 adds a customer attribute to disable the GLS method.
        if (!$lastVersion || version_compare($lastVersion, '1.1.0') < 0) {
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $attribute = \Baze\ShippingGLS\Model\ParcelShop\Shipping::CUSTOMER_DISABLE_ATTRIBUTE;

            // Add new customer attribute
            $customerSetup->addAttribute(
                Customer::ENTITY,
                $attribute,
                [
                    'label'                 => 'Disable Denmark GLS Shipping',
                    'type'                  => 'int',
                    'input'                 => 'select',
                    'source'                => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'required'              => false,
                    'default'               => '0',
                    'position'              => 200,
                    'visible'               => true,
                    'system'                => false,
                    'is_used_in_grid'       => true,
                    'is_visible_in_grid'    => true,
                    'is_filterable_in_grid' => true,
                    'is_searchable_in_grid' => true
                ]
            );

            // add attribute to form
            /** @var  $attribute */
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', $attribute);
            $attribute->setData('used_in_forms', ['adminhtml_customer']);
            $attribute->save();
        }

        $setup->endSetup();
    }
}
