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
 * @category   BSS
 * @package    Bss_OneStepCheckout
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Model\ResourceModel;

/**
 * Class compositeConfig
 * @package Bss\OneStepCheckout\Model\ResourceModel
 */
class compositeConfig
{
    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * compositeConfig constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
    }

    /**
     * Get Osc Widget
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOscWidget()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $select = $this->readAdapter->select()
            ->from(
                ['t1' => $this->getTableName('widget_instance_page')],
                ['t1.block_reference']
            )->joinLeft(
                ['t2' => $this->getTableName('widget_instance')],
                't1.instance_id = t2.instance_id',
                ['t2.widget_parameters']
            )->where("t1.layout_handle = 'bss_osc'")
            ->where("FIND_IN_SET(0, store_ids) OR FIND_IN_SET(" . $storeId . ", store_ids)")
            ->order('t2.sort_order ASC')
            ->order('t2.instance_id ASC');
        // @codingStandardsIgnoreStart
        return $this->readAdapter->fetchAll($select);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get Country
     *
     * @return array
     */
    public function getCountryHasRegion()
    {
        // @codingStandardsIgnoreStart
        $select = $this->readAdapter->select()
            ->from(
                [$this->getTableName('directory_country_region')],
                'country_id'
            )->group('country_id');
        // @codingStandardsIgnoreEnd
        $result = $this->readAdapter->fetchCol($select);
        return $result;
    }

    /**
     * Get Table Name
     *
     * @param String $entity
     * @return bool|mixed
     */
    public function getTableName($entity)
    {
        if (!isset($this->tableNames[$entity])) {
            try {
                $this->tableNames[$entity] = $this->resourceConnection->getTableName($entity);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->tableNames[$entity];
    }
}
