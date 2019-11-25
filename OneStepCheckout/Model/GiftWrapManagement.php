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
 * Class GiftWrapManagement
 * @package Bss\OneStepCheckout\Model
 */
class GiftWrapManagement implements \Bss\OneStepCheckout\Api\GiftWrapManagementInterface
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
     * GiftWrapManagement constructor.
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
     * @param int $action
     * @return bool|\Bss\OneStepCheckout\Api\GiftWrapManagementInterface|false|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply($action)
    {
        $quote = $this->checkoutSession->create()->getQuote();
        $storeId = $quote->getStoreId();
        $giftWrapType = $this->configHelper->getGiftWrap('type', $storeId);
        $giftWrapFeeConfig = $this->configHelper->getGiftWrapFee($storeId);
        try {
            if ($action == 0 || $giftWrapFeeConfig === false) {
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
            $quote->collectTotals();
            $quote->save();
            if ($quote->isVirtual() || $giftWrapFeeConfig === false) {
                $response['status'] = 'virtual';
            } else {
                $response['gift_wrap_label'] = $this->dataHelper->getGiftWrapLabel($this->configHelper->getGiftWrap('fee'), $giftWrapType);
                $response['status'] = 'success';
            }
        } catch (\Exception $e) {
            $response['status'] = 'error';
        }
        return $this->serializer->serialize($response);
    }
}
