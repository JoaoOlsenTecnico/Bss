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
namespace Bss\OneStepCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

/**
 * Class SaveOrderAfterSubmitObserver
 * @package Bss\OneStepCheckout\Observer
 */
class SaveOrderAfterSubmitObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSession;

    /**
     *
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $configHelper;

    /**
     *
     * @var \Bss\OneStepCheckout\Model\AdditionalData
     */
    protected $additionalDataModel;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param \Bss\OneStepCheckout\Model\AdditionalData $additionalDataModel
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        \Bss\OneStepCheckout\Model\AdditionalData $additionalDataModel,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;
        $this->additionalDataModel = $additionalDataModel;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getData('order');
        $this->coreRegistry->register('directpost_order', $order, true);

        $additionalData = $this->checkoutSession->create()->getBssOscAdditionalData();

        if (!empty($additionalData) && isset($additionalData['order_comment'])) {
            $this->additionalDataModel->saveComment($order->getId(), $additionalData);
        }

        if (!empty($additionalData)
            && $this->configHelper->isNewletterField('enable_subscribe_newsletter')
        ) {
            $this->additionalDataModel->subscriber($order->getId(), $additionalData);
        }

        $this->checkoutSession->create()->unsBssOscAdditionalData();

        return $this;
    }
}
