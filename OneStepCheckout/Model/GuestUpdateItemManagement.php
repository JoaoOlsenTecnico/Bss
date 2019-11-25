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

namespace Bss\OneStepCheckout\Model;

use Bss\OneStepCheckout\Api\GuestUpdateItemManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Bss\OneStepCheckout\Api\UpdateItemManagementInterface;

/**
 * Class GuestUpdateItemManagement
 *
 * @package Bss\OneStepCheckout\Model
 */
class GuestUpdateItemManagement implements GuestUpdateItemManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var \Bss\OneStepCheckout\Api\UpdateItemManagementInterface
     */
    private $updateItemManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param UpdateItemManagementInterface $updateItemManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        UpdateItemManagementInterface $updateItemManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->updateItemManagement = $updateItemManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $qty)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quoteId = (int) $quoteIdMask->getQuoteId();
        return $this->updateItemManagement->update($quoteId, $address, $itemId, $qty);
    }
}
