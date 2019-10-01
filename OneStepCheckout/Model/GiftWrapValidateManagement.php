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

/**
 * Class GiftWrapValidateManagement
 * @package Bss\OneStepCheckout\Model
 */
class GiftWrapValidateManagement implements \Bss\OneStepCheckout\Api\GiftWrapValidateManagementInterface
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
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * GiftWrapValidateManagement constructor.
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        \Bss\OneStepCheckout\Helper\Data $dataHelper,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
    }

    /**
     * @param float $fee
     * @param int $use
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate($fee, $use)
    {
        $quote = $this->checkoutSession->create()->getQuote();
        $storeId = $quote->getStoreId();
        $giftWrapType = $this->configHelper->getGiftWrap('type', $storeId);
        $giftWrapFeeConfig = $this->configHelper->getGiftWrapFee($storeId);
        $response['gift_wrap_display'] = true;
        $response['gift_wrap_update'] = false;
        $response['gift_wrap_label'] = false;
        try {
            if ($use == 0) {
                $quote->setBaseOscGiftWrapFeeConfig(null);
                $quote->setOscGiftWrapFeeConfig(null);
                $quote->setOscGiftWrapType(null);
                $quote->setBaseOscGiftWrap(null);
                $quote->setOscGiftWrap(null);
            } else {
                if ($giftWrapFeeConfig === false) {
                    $quote->setBaseOscGiftWrapFeeConfig(null);
                    $quote->setOscGiftWrapFeeConfig(null);
                    $quote->setOscGiftWrapType(null);
                    $quote->setBaseOscGiftWrap(null);
                    $quote->setOscGiftWrap(null);
                } else {
                    $giftWrapFeeConfigCurrency = $this->configHelper->formatCurrency($giftWrapFeeConfig);
                    $giftWrapFee = $this->dataHelper->getTotalGiftWrapFee($quote, $giftWrapFeeConfig, $giftWrapType);
                    $giftWrapFeeCurrency = $this->configHelper->formatCurrency($giftWrapFee);
                    $quote->setBaseOscGiftWrapFeeConfig($giftWrapFeeConfig);
                    $quote->setOscGiftWrapFeeConfig($giftWrapFeeConfigCurrency);
                    $quote->setOscGiftWrapType($giftWrapType);
                    $quote->setBaseOscGiftWrap($giftWrapFee);
                    $quote->setOscGiftWrap($giftWrapFeeCurrency);
                }
                if (round($fee, 2) != round($giftWrapFeeCurrency, 2)) {
                    $response['gift_wrap_update'] = true;
                    $response['gift_wrap_label'] = $this->dataHelper->getGiftWrapLabel($giftWrapFeeConfig, $giftWrapType);
                }
            }
            $response['status'] = true;
            $quote->collectTotals();
            $quote->save();
        } catch (\Exception $e) {
            $response['status'] = false;
        }

        if ($quote->isVirtual() && round($fee, 2) >= 0) {
            $response['gift_wrap_display'] = false;
        }
        return $this->serializer->serialize($response);
    }
}
