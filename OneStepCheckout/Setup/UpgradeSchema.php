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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.1.3', '<=')) {
            $columnExist = $installer->getConnection()->tableColumnExists(
                $installer->getTable('sales_order'),
                'shipping_arrival_date'
            );
            $columnExistDiliveryDate = $installer->getConnection()->tableColumnExists(
                $installer->getTable('sales_order'),
                'delivery_date'
            );
            if (!$columnExist) {
                if ($columnExistDiliveryDate) {
                    $installer->getConnection()->changeColumn(
                        $installer->getTable('sales_order'),
                        'delivery_date',
                        'shipping_arrival_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Delivery Date'
                        ]
                    );
                    $installer->getConnection()->changeColumn(
                        $installer->getTable('sales_order'),
                        'delivery_comment',
                        'shipping_arrival_comments',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                            'comment' => 'Delivery Comment'
                        ]
                    );
                    $installer->getConnection()->changeColumn(
                        $installer->getTable('sales_order_grid'),
                        'delivery_date',
                        'shipping_arrival_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Delivery Date'
                        ]
                    );
                    $installer->getConnection()->changeColumn(
                        $installer->getTable('quote'),
                        'delivery_date',
                        'shipping_arrival_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Delivery Date'
                        ]
                    );
                    $installer->getConnection()->changeColumn(
                        $installer->getTable('quote'),
                        'delivery_comment',
                        'shipping_arrival_comments',
                        [
                            'type' => Table::TYPE_TEXT,
                            'length' => 255,
                            'comment' => 'Delivery Comment'
                        ]
                    );
                } else {
                    $installer->getConnection()->addColumn(
                        $installer->getTable('quote'),
                        'shipping_arrival_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Delivery Date'
                        ]
                    );

                    $installer->getConnection()->addColumn(
                        $installer->getTable('quote'),
                        'shipping_arrival_comments',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => 'Delivery Comment'
                        ]
                    );

                    $installer->getConnection()->addColumn(
                        $installer->getTable('sales_order'),
                        'shipping_arrival_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Delivery Date'
                        ]
                    );

                    $installer->getConnection()->addColumn(
                        $installer->getTable('sales_order'),
                        'shipping_arrival_comments',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => 'Delivery Comment'
                        ]
                    );

                    $installer->getConnection()->addColumn(
                        $installer->getTable('sales_order_grid'),
                        'shipping_arrival_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'comment' => 'Delivery Date'
                        ]
                    );
                }
            }
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'base_osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'osc_gift_wrap_type',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'comment' => 'Osc Gift Wrap Type'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'base_osc_gift_wrap_fee_config',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap Fee Config'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'osc_gift_wrap_fee_config',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap Fee Config'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'base_osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'osc_gift_wrap_type',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'comment' => 'Osc Gift Wrap Type'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'base_osc_gift_wrap_fee_config',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap Fee Config'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'osc_gift_wrap_fee_config',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap Fee Config'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_invoice'),
                'base_osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_invoice'),
                'osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_creditmemo'),
                'base_osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_creditmemo'),
                'osc_gift_wrap',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'comment' => 'Osc Gift Wrap'
                ]
            );
        }
        $installer->endSetup();
    }
}
