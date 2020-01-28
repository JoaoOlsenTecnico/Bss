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

namespace Bss\OneStepCheckout\Plugin\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Fieldset
 * @package Bss\OneStepCheckout\Plugin\System\Config\Form
 */
class Fieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var \Bss\OneStepCheckout\Helper\Data
     */
    protected $dataHelper;

    /**
     * Fieldset constructor.
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     */
    public function __construct(
        \Bss\OneStepCheckout\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Around Render
     *
     * @param \Magento\Config\Block\System\Config\Form\Fieldset $subject
     * @param \Closure $proceed
     * @param AbstractElement $element
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        \Magento\Config\Block\System\Config\Form\Fieldset $subject,
        \Closure $proceed,
        $element
    ) {
        $result = $proceed($element);
        if ($this->dataHelper->isModuleInstall('Bss_OrderDeliveryDate')) {
            $subject->setElement($element);
            $header = $subject->_getHeaderHtml($element);

            $elements = $this->_getChildrenElementsHtmlCustom($element);

            $footer = $subject->_getFooterHtml($element);

            return $header . $elements . $footer;
        }
        return $result;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getChildrenElementsHtmlCustom(AbstractElement $element)
    {
        $elements = '';
        $removeField = ['onestepcheckout_order_delivery_date_enable_delivery_date',
                        'onestepcheckout_order_delivery_date_enable_delivery_comment'
        ];
        foreach ($element->getElements() as $field) {
            if (in_array($field->getId(), $removeField)) {
                continue;
            }
            if ($field instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
                $elements .= '<tr id="row_' . $field->getHtmlId() . '">'
                    . '<td colspan="4">' . $field->toHtml() . '</td></tr>';
            } else {
                $elements .= $field->toHtml();
            }
        }

        return $elements;
    }
}
