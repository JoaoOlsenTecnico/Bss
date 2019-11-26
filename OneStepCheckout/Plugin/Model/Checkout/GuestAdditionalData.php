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

namespace Bss\OneStepCheckout\Plugin\Model\Checkout;

use Bss\OneStepCheckout\Model\AdditionalData;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Bss\OneStepCheckout\Helper\Config;
use Bss\OneStepCheckout\Helper\Data;

/**
 * Class GuestAdditionalData
 *
 * @package Bss\OneStepCheckout\Plugin\Model\Checkout
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestAdditionalData
{
    /**
     * @var AdditionalData
     */
    private $additionalDataModel;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var Magento\Checkout\Model\SessionFactory
     */
    private $checkoutSession;

    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * GuestAdditionalData constructor.
     * @param AdditionalData $additionalDataModel
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param Config $configHelper
     * @param Data $dataHelper
     */
    public function __construct(
        AdditionalData $additionalDataModel,
        CartRepositoryInterface $cartRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        Config $configHelper,
        Data $dataHelper
    ) {
        $this->additionalDataModel = $additionalDataModel;
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Around Save Payment Information
     *
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getExtensionAttributes() !== null
            && $this->configHelper->isEnabled()
            && $paymentMethod->getExtensionAttributes()->getBssOsc() !== null
        ) {
            $additionalData = $paymentMethod->getExtensionAttributes()->getBssOsc();
            $orderId = $proceed($cartId, $email, $paymentMethod, $billingAddress);
            if (!empty($additionalData) && isset($additionalData['order_comment'])) {
                $this->additionalDataModel->saveComment($orderId, $additionalData);
            }
            if (!empty($additionalData)
                && $this->configHelper->isNewletterField('enable_subscribe_newsletter')
            ) {
                $this->additionalDataModel->subscriber($orderId, $additionalData);
            }
        } else {
            return $proceed($cartId, $email, $paymentMethod, $billingAddress);
        }
    }

    /**
     * Before Save Payment Information
     *
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getExtensionAttributes() !== null
            && $this->configHelper->isEnabled()
            && $paymentMethod->getExtensionAttributes()->getBssOsc() !== null
        ) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
            $additionalData = $paymentMethod->getExtensionAttributes()->getBssOsc();
            if (!empty($additionalData)) {
                if (!$this->dataHelper->isModuleInstall('Bss_OrderDeliveryDate')) {
                    $this->additionalDataModel->saveDelivery($quote, $additionalData);
                }
                $onlineMethodList = [
                    'payflowpro',
                    'payflow_link',
                    'payflow_advanced',
                    'braintree_paypal',
                    'paypal_express_bml',
                    'payflow_express_bml',
                    'payflow_express',
                    'paypal_express',
                    'authorizenet_directpost',
                    'realexpayments_hpp',
                    'braintree'
                ];
                if (in_array($paymentMethod->getMethod(), $onlineMethodList)) {
                    $this->checkoutSession->create()->setBssOscAdditionalData($additionalData);
                }
            }
        }
    }
}
