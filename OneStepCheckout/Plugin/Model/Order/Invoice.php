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
namespace Bss\OneStepCheckout\Plugin\Model\Order;

class Invoice
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Invoice constructor.
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $subject
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function beforeRegister(\Magento\Sales\Model\Order\Invoice $subject)
    {
        $order = $subject->getOrder();
        if (!$subject->getId() && !$subject->getOscGiftWrap()) {
            $quote = $this->quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId());
            $giftWrap = (float)$quote->getOscGiftWrap();
            if ($giftWrap > 0 && $order->getInvoiceCollection()->getSize() == 0) {
                $subject->setBaseGrandTotal($subject->getBaseGrandTotal() + $giftWrap);
                $subject->setGrandTotal($subject->getGrandTotal() + $giftWrap);
                $subject->setOscGiftWrap($giftWrap);
            } else {
                $subject->setOscGiftWrap(0);
            }
        }
        return $subject;
    }
}
