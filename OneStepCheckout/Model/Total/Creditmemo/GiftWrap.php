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
namespace Bss\OneStepCheckout\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class GiftWrap
 * @package Bss\OneStepCheckout\Model\Total\Creditmemo
 */
class GiftWrap extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param Creditmemo $creditmemo
     * @return $this|Creditmemo\Total\AbstractTotal
     */
    public function collect(Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $order = $creditmemo->getOrder();
        if (!$order->getOscGiftWrap()) {
            return $this;
        }
        $giftWrapFeeConfig = $order->getBaseOscGiftWrapFeeConfig();
        $giftWrapType = $order->getOscGiftWrapType();
        if ($giftWrapType == \Bss\OneStepCheckout\Model\Config\Source\GiftWrapType::PER_ITEMS) {
            $giftWrapBalance = $this->calculatorGiftWrapFeePerItems($creditmemo, $giftWrapFeeConfig);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $giftWrapBalance);
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $giftWrapBalance);
            $creditmemo->setOscGiftWrap($giftWrapBalance);
        } elseif ($giftWrapType == \Bss\OneStepCheckout\Model\Config\Source\GiftWrapType::PER_ORDER) {
            if ($this->calculatorGiftWrapFeePerOrder($order, $creditmemo)) {
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $order->getBaseOscGiftWrap());
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $order->getOscGiftWrap());
                $creditmemo->setBaseOscGiftWrapConfig($order->getBaseOscGiftWrapConfig());
                $creditmemo->setOscGiftWrapConfig($order->getOscGiftWrapConfig());
                $creditmemo->setBaseOscGiftWrap($order->getBaseOscGiftWrap());
                $creditmemo->setOscGiftWrap($order->getOscGiftWrap());
            }
        }
        return $this;
    }

    /**
     * @param Creditmemo $creditmemo
     * @param $giftWrapFeeConfig
     * @return float|int
     */
    protected function calculatorGiftWrapFeePerItems($creditmemo, $giftWrapFeeConfig)
    {
        $giftWrapFee = 0;
        foreach ($creditmemo->getItems() as $item) {
            $itemDetail = $item->getOrderItem();
            if ($itemDetail->getParentItemId() || $itemDetail->getIsVirtual() == 1) {
                continue;
            }
            $qty = $item->getQty();
            $giftWrapFee = $giftWrapFee + $qty * $giftWrapFeeConfig;
        }
        return $giftWrapFee;
    }

    /**
     * @param $order
     * @param $creditmemo
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function calculatorGiftWrapFeePerOrder($order, $creditmemo)
    {
        $result = [];
        foreach ($order->getItems() as $item) {
            if ($item->getParentItemId() || $item->getIsVirtual() == 1) {
                continue;
            }
            if ($item->getQtyOrdered() != $item->getQtyRefunded()) {
                $result[$item->getId()] = ['order' => $item->getQtyOrdered(), 'refun' => $item->getQtyRefunded()];
            }
        }
        foreach ($creditmemo->getItems() as $item) {
            $itemDetail = $item->getOrderItem();
            if ($itemDetail->getParentItemId() || $itemDetail->getIsVirtual() == 1) {
                continue;
            }
            if (isset($result[$itemDetail->getId()])) {
                $orderQty = $result[$itemDetail->getId()]['order'];
                $refunQty = $result[$itemDetail->getId()]['refun'];
                if ($orderQty == $refunQty + $item->getQty()) {
                    unset($result[$itemDetail->getId()]);
                }
            }
        }

        if (empty($result)) {
            return true;
        }
        return false;
    }
}
