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

namespace Bss\OneStepCheckout\Plugin;

/**
 * Class UpdateFeeForOrder
 * @package Bss\OneStepCheckout\Plugin
 */
class UpdateFeeForOrder
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * UpdateFeeForOrder constructor.
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->quote = $quote;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Before Get All Items
     *
     * @param \Magento\Paypal\Model\Cart $cart
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeGetAllItems(\Magento\Paypal\Model\Cart $cart)
    {
        $quote = $this->_checkoutSession->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();
        $paypalMethodList = [
            'payflowpro',
            'payflow_link',
            'payflow_advanced',
            'braintree_paypal',
            'paypal_express_bml',
            'payflow_express_bml',
            'payflow_express',
            'paypal_express'
        ];
        if (!in_array($paymentMethod, $paypalMethodList)) {
            return;
        }
        $feeAmount = $quote->getBaseOscGiftWrap();
        if ($feeAmount && $feeAmount > 0) {
            $cart->addCustomItem(__("Gift Wrap"), 1, $feeAmount, 'gift_wrap');
            $cart->addSubtotal($feeAmount);
        }
    }

    /**
     * After Get All Items
     *
     * @param \Magento\Paypal\Model\Cart $cart
     * @param array $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllItems(
        \Magento\Paypal\Model\Cart $cart,
        $result
    ) {
        if (empty($result)) {
            return $result;
        }
        $found = false;
        foreach ($result as $key => $item) {
            if ($item->getId() != 'gift_wrap') {
                continue;
            }
            if ($found) {
                unset($result[$key]);
                continue;
            }
            $found = true;
        }
        return $result;
    }
}
