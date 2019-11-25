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

use Bss\OneStepCheckout\Api\Data\UpdateItemDetailsInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class GuestUpdateItemManagement
 *
 * @package Bss\OneStepCheckout\Model
 */
class UpdateItemDetails extends AbstractExtensibleModel implements UpdateItemDetailsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods()
    {
        return $this->getData(self::PAYMENT_METHODS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(self::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotals($totals)
    {
        return $this->setData(self::TOTALS, $totals);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethods()
    {
        return $this->getData(self::SHIPPING_METHODS);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethods($shippingMethods)
    {
        return $this->setData(self::SHIPPING_METHODS, $shippingMethods);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getHasError()
    {
        return $this->getData(self::HAS_ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function setHasError($error)
    {
        return $this->setData(self::HAS_ERROR, $error);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftWrapDisplay()
    {
        return $this->getData(self::GIFT_WRAP_DISPLAY);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftWrapDisplay($display)
    {
        return $this->setData(self::GIFT_WRAP_DISPLAY, $display);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftWrapLabel()
    {
        return $this->getData(self::GIFT_WRAP_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftWrapLabel($label)
    {
        return $this->setData(self::GIFT_WRAP_LABEL, $label);
    }
}
