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

class GiftWrapInfo extends \Magento\Backend\Block\Template
{
    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $configHelper;

    /**
     * GiftWrapInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Bss\OneStepCheckout\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Bss\OneStepCheckout\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this|\Magento\Backend\Block\Template
     */
    protected function _prepareLayout()
    {
        if ($this->configHelper->isEnabled() && $this->configHelper->getGiftwrapFee() !== false) {
            $this->setTemplate('Bss_OneStepCheckout::order/gift-wrap-info.phtml');
        } else {
            $this->setTemplate('');
        }
        return $this;
    }
}
