<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bss\OneStepCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bss\OneStepCheckout\Helper\Config;
use Bss\OneStepCheckout\Helper\Data;

class SalesEventQuoteSubmitBeforeObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    private $oscHelper;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * SalesEventQuoteSubmitBeforeObserver constructor.
     * @param Data $oscHelper
     * @param Config $configHelper
     */
    public function __construct(
        Data $oscHelper,
        Config $configHelper
    ) {
        $this->oscHelper = $oscHelper;
        $this->configHelper = $configHelper;
    }
    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        if ($this->configHelper->isEnabled() && !$this->oscHelper->isModuleInstall('Bss_OrderDeliveryDate')) {
            $order->setShippingArrivalDate($quote->getShippingArrivalDate());
            $order->setShippingArrivalComments($quote->getShippingArrivalComments());
        }
        if ($this->configHelper->getGiftWrapFee() !== false) {
            $order->setBaseOscGiftWrapFeeConfig($quote->getBaseOscGiftWrapFeeConfig());
            $order->setOscGiftWrapFeeConfig($quote->getOscGiftWrapFeeConfig());
            $order->setOscGiftWrapType($quote->getOscGiftWrapType());
            $order->setBaseOscGiftWrap($quote->getBaseOscGiftWrap());
            $order->setOscGiftWrap($quote->getOscGiftWrap());
        }
        return $this;
    }
}
