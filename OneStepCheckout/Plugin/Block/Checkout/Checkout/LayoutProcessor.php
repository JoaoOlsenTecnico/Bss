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

namespace Bss\OneStepCheckout\Plugin\Block\Checkout\Checkout;

use Bss\OneStepCheckout\Helper\Config;
use Bss\OneStepCheckout\Helper\Data;

/**
 * Class LayoutProcessor
 *
 * @package Bss\OneStepCheckout\Plugin\Block\Checkout\Checkout
 */
class LayoutProcessor
{
    /**
     * One step checkout helper
     *
     * @var Config
     */
    protected $configHelper;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * LayoutProcessor constructor.
     * @param Config $configHelper
     * @param Data $dataHelper
     */
    public function __construct(
        Config $configHelper,
        Data $dataHelper
    ) {
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (!$this->configHelper->isEnabled()) {
            return $jsLayout;
        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            $component = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children']
            ['billing-address-form'];
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['billing-address-form']
            );
            $component['component'] = 'Bss_OneStepCheckout/js/view/billing-address';
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']
            ['billing-address-form-shared'] = $component;
        }

        $jsLayout = $this->orderDeliveryDate($jsLayout);

        if (!$this->configHelper->isDisplayField('enable_order_comment')) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['bss_osc_order_comment']
            );
        }

        $jsLayout = $this->newsletter($jsLayout);

        if (!$this->configHelper->isGiftMessageField('enable_gift_message') ||
            !$this->configHelper->isMessagesAllowed()) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['giftmessage']
            );
        }

        if ($this->configHelper->getGiftWrapFee() === false) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']['gift_wrap']
            );
        }

        $jsLayout = $this->discountCode($jsLayout);

        $jsLayout = $this->removeComponent($jsLayout);
        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function orderDeliveryDate($jsLayout)
    {
        if (!$this->configHelper->isOrderDeliveryField('enable_delivery_date') ||
            $this->dataHelper->isModuleInstall('Bss_OrderDeliveryDate')
        ) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['before-shipping-method-form']['children']
                ['bss_osc_delivery_date']
            );
        }

        if (!$this->configHelper->isOrderDeliveryField('enable_delivery_comment') ||
            $this->dataHelper->isModuleInstall('Bss_OrderDeliveryDate')
        ) {
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['before-shipping-method-form']['children']
                ['bss_osc_delivery_comment']
            );
        }
        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function removeComponent($jsLayout)
    {
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['discount']
        );

        unset(
            $jsLayout['components']['checkout']['children']['sidebar']['children']['shipping-information']
        );

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']['before-place-order']
            ['children']['agreements']
        );

        unset($jsLayout['components']['checkout']['children']['progressBar']);
        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function newsletter($jsLayout)
    {
        if (!$this->configHelper->isNewletterField('enable_subscribe_newsletter')) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['subscribe']
            );
        } else {
            $checked = (bool)$this->configHelper->isNewletterField('newsletter_default');
            $jsLayout['components']['checkout']['children']['sidebar']['children']
            ['subscribe']['config']['checked'] = $checked;
        }
        return $jsLayout;
    }

    /**
     * @param $jsLayout
     * @return mixed
     */
    protected function discountCode($jsLayout)
    {
        if ($this->configHelper->isDisplayField('enable_discount_code')) {
            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount'] =
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['discount'];

            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['displayArea'] = 'summary';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['template'] = 'Bss_OneStepCheckout/payment/discount';

            $jsLayout['components']['checkout']['children']['sidebar']['children']['discount']
            ['sortOrder'] = 230;
        }
        return $jsLayout;
    }
}
