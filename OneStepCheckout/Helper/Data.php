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

namespace Bss\OneStepCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Bss\OneStepCheckout\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param ProductMetadataInterface $productMetadata
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        ProductMetadataInterface $productMetadata,
        Context $context
    ) {
        parent::__construct(
            $context
        );
        $this->priceHelper = $priceHelper;
        $this->moduleManager = $moduleManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     *  Compare magento version
     *
     * @param string $version
     * @return bool
     */
    public function validateVersion($version)
    {
        $dataVersion = $this->productMetadata->getVersion();
        return version_compare($dataVersion, $version, '<');
    }

    /**
     * @param /Magento/Sales/Model/Order $order
     * @return string
     */
    public function formatDateTime($order)
    {
        return $order->getShippingArrivalDate();
    }

    /**
     *  Get magento version
     *
     * @return string
     */
    public function getVersion()
    {
        $dataVersion = $this->productMetadata->getVersion();
        return $dataVersion;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isPayPalContext($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'payment/paypal_express/in_context',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isModuleInstall($moduleName)
    {
        if ($this->moduleManager->isEnabled($moduleName)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $giftWrapFee
     * @param $giftWrapType
     * @return \Magento\Framework\Phrase
     */
    public function getGiftWrapLabel($giftWrapFee, $giftWrapType)
    {
        if ($giftWrapType == \Bss\OneStepCheckout\Model\Config\Source\GiftWrapType::PER_ITEMS) {
            return __('Gift Wrap + %1 per items.', $this->priceHelper->currency($giftWrapFee, true, false));
        } else {
            return __('Gift Wrap + %1 per order.', $this->priceHelper->currency($giftWrapFee, true, false));
        }
    }

    /**
     * @param $quote
     * @param $giftWrapFee
     * @param $giftWrapType
     * @return float|int
     */
    public function getTotalGiftWrapFee($quote, $giftWrapFee, $giftWrapType)
    {
        $qty = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            if (!$item->getProduct()->getIsVirtual()) {
                $qty = $qty + (int)$item->getQty();
            }
        }
        if ($giftWrapType == \Bss\OneStepCheckout\Model\Config\Source\GiftWrapType::PER_ITEMS) {
            $giftWrapFee = $giftWrapFee * $qty;
        }
        return $giftWrapFee;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPassPasswordMinLeng($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'customer/password/minimum_password_length',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPassPasswordMinCharacterSets($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'customer/password/required_character_classes_number',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
