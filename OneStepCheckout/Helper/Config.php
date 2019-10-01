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
use Magento\Store\Model\ScopeInterface;
use Magento\GiftMessage\Helper\Message;

/**
 * Class Config
 *
 * @package Bss\OneStepCheckout\Helper
 */
class Config extends AbstractHelper
{
    const ENABLE = 'onestepcheckout/general/enable';
    const GENERAL_GROUP = 'onestepcheckout/general/';
    const ENABLE_DISPLAY_FIELD = 'onestepcheckout/display_field/';
    const NEWLETTER_FIELD = 'onestepcheckout/newsletter/';
    const ORDER_DELIVERY_FIELD = 'onestepcheckout/order_delivery_date/';
    const GIFT_MESSAGE_FIELD = 'onestepcheckout/gift_message/';
    const ENABLE_AUTO_COMPLETE = 'onestepcheckout/auto_complete/enable_auto_complete';
    const SUGGESTING_ADDRESS = 'onestepcheckout/auto_complete/';
    const CUSTOM_CSS = 'onestepcheckout/custom_css/';
    const OSC_CONTROLLER_NAME = 'onestepcheckout';
    const GIFT_WRAP_FIELD = 'onestepcheckout/gift_wrap/';

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * Data constructor.
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param ProductMetadataInterface $productMetadata
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct(
            $context
        );
        $this->priceHelper = $priceHelper;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getGeneral($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_GROUP . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $field
     * @param null|int $storeId
     * @return mixed
     */
    public function isDisplayField($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_DISPLAY_FIELD . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $field
     * @param null $storeId
     * @return bool
     */
    public function isNewletterField($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::NEWLETTER_FIELD . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $field
     * @param null $storeId
     * @return bool
     */
    public function isOrderDeliveryField($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::ORDER_DELIVERY_FIELD . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $field
     * @param null $storeId
     * @return bool
     */
    public function isGiftMessageField($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::GIFT_MESSAGE_FIELD . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isAutoComplete($storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if ($this->getAutoCompleteGroup('allowspecific')
            && !$this->getAutoCompleteGroup('specificcountry')
        ) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_AUTO_COMPLETE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getAutoCompleteGroup($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->getValue(
            self::SUGGESTING_ADDRESS . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getCustomCss($field, $storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->getValue(
            self::CUSTOM_CSS . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isMessagesAllowed($store = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getDefaultCustomerGroupId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'customer/create_account/default_group',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isAutoCreateNewAccount($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::GENERAL_GROUP . 'create_new',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isGiftWrapEnable($storeId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::GIFT_WRAP_FIELD . 'enable',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getGiftWrap($field, $storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::GIFT_WRAP_FIELD . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($field == 'fee') {
            if ($value == '') {
                $value = 0;
            } else {
                $value = (float)$value;
                $value = round($value, 2);
            }
        }
        return $value;
    }

    /**
     * @return bool|float|mixed
     */
    public function getGiftWrapFee()
    {
        if (!$this->isGiftWrapEnable()) {
            return false;
        }
        $fee = $this->getGiftWrap('fee');
        return $fee;
    }

    /**
     * @param $number
     * @return float|string
     */
    public function formatCurrency($number)
    {
        return $this->priceHelper->currency($number, false, false);
    }
}
