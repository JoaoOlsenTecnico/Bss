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
namespace Bss\OneStepCheckout\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Style
 * @package Bss\OneStepCheckout\Block
 */
class Style extends Template
{
    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $helperConfig;

    /**
     * Style constructor.
     * @param Context $context
     * @param \Bss\OneStepCheckout\Helper\Config $helperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Bss\OneStepCheckout\Helper\Config $helperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperConfig = $helperConfig;
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getCustomCss($field)
    {
        return $this->helperConfig->getCustomCss($field);
    }
}
