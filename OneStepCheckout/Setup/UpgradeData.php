<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeData
 * @package Bss\OneStepCheckout\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.1.3', '<=')) {
            $tables = ['sales_order', 'sales_order_grid', 'quote'];
            foreach ($tables as $table) {
                $installer->getConnection('core_write')->update(
                    $installer->getTable($table),
                    ['shipping_arrival_date' => null],
                    ['shipping_arrival_date = ?' => '0000-00-00 00:00:00']
                );
            }

            $configDataTable = $installer->getTable('core_config_data');
            $updateList = [
                'display_field/enable_delivery_date' => 'order_delivery_date/enable_delivery_date',
                'display_field/enable_delivery_comment' => 'order_delivery_date/enable_delivery_comment',
                'display_field/enable_gift_message' => 'gift_message/enable_gift_message',
                'display_field/enable_subscribe_newsletter' => 'newsletter/enable_subscribe_newsletter',
                'general/newsletter_default' => 'newsletter/newsletter_default',
                'general/tilte' => 'general/title'
            ];
            foreach ($updateList as $oldPath => $newPath) {
                $installer->getConnection('core_write')->update(
                    $configDataTable,
                    ['path' => 'onestepcheckout/' . $newPath],
                    ['path = ?' => 'onestepcheckout/' . $oldPath]
                );
            }
        }
        $installer->endSetup();
    }
}
