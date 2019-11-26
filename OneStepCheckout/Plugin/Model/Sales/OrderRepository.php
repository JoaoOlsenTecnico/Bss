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

namespace Bss\OneStepCheckout\Plugin\Model\Sales;

class OrderRepository
{
    /**
     * One step checkout helper
     *
     * @var Config
     */
    private $dataHelper;

    /**
     * OrderRepository constructor.
     * @param \Bss\OneStepCheckout\Helper\Data $dataHelper
     */
    public function __construct(
        \Bss\OneStepCheckout\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $entity
    ) {
        $extensionAttributes = $entity->getExtensionAttributes();
        if ($extensionAttributes) {
            if (!$this->dataHelper->isModuleInstall('Bss_OrderDeliveryDate')) {
                $extensionAttributes->setShippingArrivalDate($entity->getShippingArrivalDate());
                $extensionAttributes->setShippingArrivalComments($entity->getShippingArrivalComments());
            }
            $entity->setExtensionAttributes($extensionAttributes);
        }
        return $entity;
    }
}
