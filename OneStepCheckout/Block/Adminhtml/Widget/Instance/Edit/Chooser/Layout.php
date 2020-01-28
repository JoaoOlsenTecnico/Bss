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
 * @category   BSS
 * @package    Bss_OneStepCheckout
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Block\Adminhtml\Widget\Instance\Edit\Chooser;

class Layout extends \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout
{
    /**
     * @param array $pageTypes
     */
    protected function _addPageTypeOptions(array $pageTypes)
    {
        $label = [];
        // Sort list of page types by label
        foreach ($pageTypes as $key => $row) {
            $label[$key] = $row['label'];
        }
        array_multisort($label, SORT_STRING, $pageTypes);

        foreach ($pageTypes as $pageTypeName => $pageTypeInfo) {
            $params = [];
            $this->addOption($pageTypeName, $pageTypeInfo['label'], $params);
        }
        $code = $this->getRequest()->getParam('code');
        if ($code && $code == 'cms_static_block') {
            $this->addOption('bss_osc', 'Bss Osc', []);
        }
    }
}
