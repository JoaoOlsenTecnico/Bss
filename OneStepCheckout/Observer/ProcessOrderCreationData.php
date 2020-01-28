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

namespace Bss\OneStepCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProcessOrderCreationData
 * @package Bss\OneStepCheckout\Observer
 */
class ProcessOrderCreationData implements ObserverInterface
{
    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Bss\OneStepCheckout\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ProcessOrderCreationData constructor.
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        \Bss\OneStepCheckout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
    }

    /**
     * Process admin order creation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $storeId = $quote->getStoreId();
        $giftWrapFeeConfig = $this->configHelper->getGiftWrapFee($storeId);
        $request = $observer->getEvent()->getRequest();
        $giftWrapFeeCurrent = (float)$quote->getOscGiftWrap();
        $giftWrapType = $this->configHelper->getGiftWrap('type', $storeId);
        $giftWrapFeeCurrency = null;
        $giftWrapFee = null;
        $giftWrapFeeConfigCurrency = null;
        if ($giftWrapFeeConfig !== false &&
            ((isset($request['use_giftwrap_action']) && isset($request['giftwrap'])) ||
                (!isset($request['use_giftwrap_action']) && $giftWrapFeeCurrent && $giftWrapFeeCurrent >= 0))
        ) {
            $giftWrapFeeConfigCurrency = $this->configHelper->formatCurrency($giftWrapFeeConfig);
            $giftWrapFee = $this->dataHelper->getTotalGiftWrapFee($quote, $giftWrapFeeConfig, $giftWrapType);
            $giftWrapFeeCurrency = $this->configHelper->formatCurrency($giftWrapFee);
        }
        if ($giftWrapFeeCurrency === null) {
            $giftWrapFeeConfig = null;
            $giftWrapType == null;
        }
        $quote->setBaseOscGiftWrapFeeConfig($giftWrapFeeConfig);
        $quote->setOscGiftWrapFeeConfig($giftWrapFeeConfigCurrency);
        $quote->setOscGiftWrapType($giftWrapType);
        $quote->setBaseOscGiftWrap($giftWrapFee);
        $quote->setOscGiftWrap($giftWrapFeeCurrency);
        try {
            $quote->save();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}