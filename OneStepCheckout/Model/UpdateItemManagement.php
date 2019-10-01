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

use Bss\OneStepCheckout\Api\UpdateItemManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Bss\OneStepCheckout\Api\Data\UpdateItemDetailsInterfaceFactory;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\Escaper;

/**
 * Class UpdateItemManagement
 *
 * @package Bss\OneStepCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateItemManagement implements UpdateItemManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var UpdateItemDetailsInterfaceFactory
     */
    private $updateItemDetails;

    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Bss\OneStepCheckout\Helper\Data
     */
    private $dataHelper;

    /**
     * UpdateItemManagement constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param UpdateItemDetailsInterfaceFactory $updateItemDetails
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param Escaper $escaper
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        UpdateItemDetailsInterfaceFactory $updateItemDetails,
        ShippingMethodManagementInterface $shippingMethodManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalRepository,
        Escaper $escaper,
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        \Bss\OneStepCheckout\Helper\Data $dataHelper
    ) {
        $this->cartRepository = $cartRepository;
        $this->updateItemDetails = $updateItemDetails;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->escaper = $escaper;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $itemId
     * @param float $qty
     * @return \Bss\OneStepCheckout\Api\Data\UpdateItemDetailsInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function update(
        $cartId,
        \Magento\Quote\Api\Data\EstimateAddressInterface $address,
        $itemId,
        $qty
    ) {
        $status = false;
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        if (!$quote) {
            throw new NoSuchEntityException(__('This quote does not exist.'));
        }
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(__('We can\'t find the quote item.'));
        }
        $storeId = $quote->getStoreId();
        $giftWrapType = $this->configHelper->getGiftWrap('type', $storeId);
        $giftWrapFeeConfig = 0;
        try {
            if (!$qty || $qty <= 0) {
                $quote->removeItem($itemId);
                $this->cartRepository->save($quote);
                $status = true;
                $message = __(
                    '%1 was removed in your shopping cart.',
                    $this->escaper->escapeHtml($quoteItem->getProduct()->getName())
                );
            } else {
                $quoteItem->setQty($qty);
                if ($quoteItem->getHasError()) {
                    throw new CouldNotSaveException(__($quoteItem->getMessage()));
                } else {
                    $quoteItem->save();
                    $status = true;
                    $message = __(
                        '%1 was updated in your shopping cart.',
                        $this->escaper->escapeHtml($quoteItem->getProduct()->getName())
                    );
                }
            }
            $currentGiftWrapFee = $quote->getOscGiftWrap();
            $giftWrapFeeConfig = $this->configHelper->getGiftWrapFee($storeId);
            if ($giftWrapFeeConfig === false || $quote->isVirtual()) {
                $quote->setBaseOscGiftWrapFeeConfig(null);
                $quote->setOscGiftWrapFeeConfig(null);
                $quote->setOscGiftWrapType(null);
                $quote->setBaseOscGiftWrap(null);
                $quote->setOscGiftWrap(null);
            } else {
                $giftWrapFeeConfigCurrency = $this->configHelper->formatCurrency($giftWrapFeeConfig);
                $giftWrapFee = $this->dataHelper->getTotalGiftWrapFee($quote, $giftWrapFeeConfig, $giftWrapType);
                $giftWrapFeeCurrency = $this->configHelper->formatCurrency($giftWrapFee);
                if ($giftWrapFeeCurrency != $currentGiftWrapFee) {
                    $quote->setBaseOscGiftWrapFeeConfig($giftWrapFeeConfig);
                    $quote->setOscGiftWrapFeeConfig($giftWrapFeeConfigCurrency);
                    $quote->setOscGiftWrapType($giftWrapType);
                    $quote->setBaseOscGiftWrap($giftWrapFee);
                    $quote->setOscGiftWrap($giftWrapFeeCurrency);
                }
            }
            $this->cartRepository->save($quote);
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        return $this->getUpdateCartDetails($quote, $address, $cartId, $message, $status, $giftWrapType, $giftWrapFeeConfig);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $quoteId
     * @param string $message
     * @param boolean $status
     * @param int $giftWrapType
     * @param mixed $giftWrapFeeConfig
     * @return \Bss\OneStepCheckout\Api\Data\UpdateItemDetailsInterface
     * @throws NoSuchEntityException
     */
    private function getUpdateCartDetails($quote, $address, $quoteId, $message, $status, $giftWrapType, $giftWrapFeeConfig)
    {
        $cartDetails = $this->updateItemDetails->create();
        $paymentMethods = $this->paymentMethodManagement->getList($quoteId);
        $totals = $this->cartTotalRepository->get($quoteId);
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getCustomerAddressId()) {
            $shippingMethods = $this->shippingMethodManagement->estimateByAddressId(
                $quoteId,
                $shippingAddress->getCustomerAddressId()
            );
        } else {
            $shippingMethods = $this->shippingMethodManagement->estimateByAddress($quoteId, $address);
        }
        $cartDetails->setShippingMethods($shippingMethods);
        $cartDetails->setPaymentMethods($paymentMethods);
        $cartDetails->setTotals($totals);
        $cartDetails->setMessage($message);
        $cartDetails->setStatus($status);

        if ($quote->isVirtual() || !$giftWrapFeeConfig) {
            $display = false;
        } else {
            $display = true;
            $cartDetails->setGiftWrapLabel($this->dataHelper->getGiftWrapLabel($giftWrapFeeConfig, $giftWrapType));
        }
        $cartDetails->setGiftWrapDisplay($display);
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            $cartDetails->setHasError(true);
        }

        return $cartDetails;
    }
}
