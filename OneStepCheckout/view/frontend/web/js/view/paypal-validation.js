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

define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Bss_OneStepCheckout/js/model/paypal-validator'
], function (Component, additionalValidators, paypalValidator) {
    'use strict';

    additionalValidators.registerValidator(paypalValidator);

    return Component.extend({});
});
