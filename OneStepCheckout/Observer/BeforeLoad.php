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
use Magento\Framework\Event\Observer;

class BeforeLoad implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSession;

    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Bss\OneStepCheckout\Helper\Data
     */
    protected $dataHelper;

    /**
     * BeforeLoad constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        \Bss\OneStepCheckout\Helper\Data $dataHelper
    ) {
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $handle = $this->request->getFullActionName();
        if ($handle == 'onestepcheckout_index_index' || $handle == 'checkout_cart_index') {
            $quote = $this->checkoutSession->create()->getQuote();
            $storeId = $quote->getStoreId();
            $giftWrapFeeConfig = $this->configHelper->getGiftWrapFee($storeId);
            $currentGiftWrapFee = $quote->getOscGiftWrap();
            if ($giftWrapFeeConfig === false || $quote->isVirtual()) {
                $quote->setBaseOscGiftWrapFeeConfig(null);
                $quote->setOscGiftWrapFeeConfig(null);
                $quote->setOscGiftWrapType(null);
                $quote->setBaseOscGiftWrap(null);
                $quote->setOscGiftWrap(null);
                $quote->collectTotals();
                $quote->save();
            } elseif ($currentGiftWrapFee !== null) {
                $giftWrapType = $this->configHelper->getGiftWrap('type', $storeId);
                $giftWrapFeeConfigCurrency = $this->configHelper->formatCurrency($giftWrapFeeConfig);
                $giftWrapFee = $this->dataHelper->getTotalGiftWrapFee($quote, $giftWrapFeeConfig, $giftWrapType);
                $giftWrapFeeCurrency = $this->configHelper->formatCurrency($giftWrapFee);
                if ($giftWrapFeeCurrency != $currentGiftWrapFee) {
                    $quote->setBaseOscGiftWrapFeeConfig($giftWrapFeeConfig);
                    $quote->setOscGiftWrapFeeConfig($giftWrapFeeConfigCurrency);
                    $quote->setOscGiftWrapType($giftWrapType);
                    $quote->setBaseOscGiftWrap($giftWrapFee);
                    $quote->setOscGiftWrap($giftWrapFeeCurrency);
                    $quote->collectTotals();
                    $quote->save();
                }
            }
        }
        return $this;
    }
}
