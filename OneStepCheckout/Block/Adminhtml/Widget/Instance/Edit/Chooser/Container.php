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

class Container extends \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container
{
    /**
     * @return \Magento\Framework\View\Element\AbstractBlock|\Magento\Framework\View\Element\Html\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $layoutHandle = $this->getLayoutHandle();
            if ($layoutHandle != 'bss_osc') {
                $layoutMergeParams = ['theme' => $this->_getThemeInstance($this->getTheme())];
                /** @var $layoutProcessor \Magento\Framework\View\Layout\ProcessorInterface */
                $layoutProcessor = $this->_layoutProcessorFactory->create($layoutMergeParams);
                $layoutProcessor->addPageHandles([$this->getLayoutHandle()]);
                $layoutProcessor->addPageHandles(['default']);
                $layoutProcessor->load();

                $pageLayoutProcessor = $this->_layoutProcessorFactory->create($layoutMergeParams);
                $pageLayouts = $this->getPageLayouts();
                foreach ($pageLayouts as $pageLayout) {
                    $pageLayoutProcessor->addHandle($pageLayout);
                }
                $pageLayoutProcessor->load();

                $containers = array_merge($pageLayoutProcessor->getContainers(), $layoutProcessor->getContainers());
                if ($this->getAllowedContainers()) {
                    foreach (array_keys($containers) as $containerName) {
                        if (!in_array($containerName, $this->getAllowedContainers())) {
                            unset($containers[$containerName]);
                        }
                    }
                }
                asort($containers, SORT_STRING);

                $this->addOption('', __('-- Please Select --'));
                foreach ($containers as $containerName => $containerLabel) {
                    $this->addOption($containerName, $containerLabel);
                }
            } else {
                $this->addOption('', __('-- Please Select --'));
                foreach ($this->getPositionList() as $position => $item) {
                    $this->addOption($position, $item);
                }
            }
        }
        return \Magento\Framework\View\Element\Html\Select::_beforeToHtml();
    }

    /**
     * @return array
     */
    public function getPositionList()
    {
        return [
            'under_order_summary' => __('Under Order Summary'),
            'under_payment_method' => __('Under Payment Method'),
            'under_place_order_button' => __('Under Place Order Button')
        ];
    }
}
