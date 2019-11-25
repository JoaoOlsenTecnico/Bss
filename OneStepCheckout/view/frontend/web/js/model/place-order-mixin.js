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

define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'Bss_OneStepCheckout/js/model/additional-data',
    'Bss_OneStepCheckout/js/model/agreements-assigner'
], function ($, _, wrapper, additionalData, agreementsAssigner) {
    'use strict';

    return function (placeOrderAction) {

        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            if (!_.isUndefined(window.checkoutConfig.bssOsc)) {
                additionalData(paymentData);
            }
            agreementsAssigner(paymentData);
            return originalAction(paymentData, messageContainer);
        });
    };
});
