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

namespace Bss\OneStepCheckout\Block\Adminhtml\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class GiftWrap
 * @package Bss\OneStepCheckout\Block\Adminhtml\Order\Totals
 */
class GiftWrapCheckbox extends Template
{
    protected $currentQuote;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Bss\OneStepCheckout\Helper\Data
     */
    protected $dataHelper;

    /**
     * GiftWrapCheckbox constructor.
     * @param Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        \Bss\OneStepCheckout\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionQuote = $sessionQuote;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return bool
     */
    public function isDisable()
    {
        $quote = $this->sessionQuote->getQuote();
        if (!$quote || $quote->getIsVirtual() == 1 || empty($quote->getAllItems())) {
            return false;
        }
        if ($quote) {
            $this->currentQuote = $quote;
        }
        return true;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        $giftWrapFee = $this->configHelper->getGiftWrap('fee');
        $giftWrapType = $this->configHelper->getGiftWrap('type');
        return $this->dataHelper->getGiftWrapLabel($giftWrapFee, $giftWrapType);
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        if ($this->currentQuote) {
            $quote = $this->currentQuote;
            $giftWrapFeeCurrent = $quote->getOscGiftWrap();
            if ($giftWrapFeeCurrent !== null) {
                return true;
            }
        }
        return false;
    }
}
