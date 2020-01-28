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
namespace Bss\OneStepCheckout\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;

/**
 * Class GiftWrap
 * @package Bss\OneStepCheckout\Model\Total\Invoice
 */
class GiftWrap extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @param Invoice $invoice
     * @return Invoice\Total\AbstractTotal|void
     */
    public function collect(Invoice $invoice)
    {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        if (!$order->getId()) {
            return;
        }
        $giftWrapFee = (float)$order->getBaseOscGiftWrap();
        $giftWrapFeeCurrency = (float)$order->getOscGiftWrap();
        $giftWrapFeeConfig = $order->getBaseOscGiftWrapFeeConfig();
        $giftWrapFeeConfigCurrency = $order->getOscGiftWrapFeeConfig();
        $giftWrapType = $order->getOscGiftWrapType();
        if ($giftWrapType == \Bss\OneStepCheckout\Model\Config\Source\GiftWrapType::PER_ITEMS) {
            $giftWrap = $this->calculatorGiftWrapFee($invoice, $giftWrapFeeConfig);
            $invoice->setOscGiftWrap($giftWrap);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $giftWrap);
            $invoice->setGrandTotal($invoice->getGrandTotal() + $giftWrap);
        } elseif ($giftWrapType == \Bss\OneStepCheckout\Model\Config\Source\GiftWrapType::PER_ORDER) {
            $invoice->setOscGiftWrapType($giftWrapType);
            if (!$invoice->getId() && !empty($order->getInvoiceCollection()->getData())) {
                $invoice->setBaseOscGiftWrapConfig(null);
                $invoice->setOscGiftWrapConfig(null);
                $invoice->setBaseOscGiftWrap(null);
                $invoice->setOscGiftWrap(null);
            } else {
                $invoice->setBaseOscGiftWrapConfig($giftWrapFeeConfig);
                $invoice->setOscGiftWrapConfig($giftWrapFeeConfigCurrency);
                $invoice->setBaseOscGiftWrap($giftWrapFee);
                $invoice->setOscGiftWrap($giftWrapFeeCurrency);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $giftWrapFee);
                $invoice->setGrandTotal($invoice->getGrandTotal() + $giftWrapFeeCurrency);
            }
        }
    }

    /**
     * @param Invoice $invoice
     * @param $giftWrapFeeConfig
     * @return float|int
     */
    protected function calculatorGiftWrapFee($invoice, $giftWrapFeeConfig)
    {
        $giftWrapFee = 0;
        foreach ($invoice->getItems() as $item) {
            $itemDetail = $item->getOrderItem();
            if ($itemDetail->getParentItemId() || $itemDetail->getIsVirtual() == 1) {
                continue;
            }
            $qty = $item->getQty();
            $giftWrapFee = $giftWrapFee + $qty * $giftWrapFeeConfig;
        }
        return $giftWrapFee;
    }
}
