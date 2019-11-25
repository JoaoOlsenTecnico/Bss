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

define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function (
        $,
        ko,
        Component
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Bss_OneStepCheckout/osc-widget'
            },
            getOscWidget: function (position) {
                var widgetList = window.checkoutConfig.oscWidget,
                    result = ko.observableArray([]);
                if (position == "under_payment_method") {
                    $.each(widgetList.under_payment_method, function (index, value) {
                        result.push(value);
                    });
                }
                if (position == "under_order_summary") {
                    $.each(widgetList.under_order_summary, function (index, value) {
                        result.push(value);
                    });
                }
                if (position == "under_place_order_button") {
                    $.each(widgetList.under_place_order_button, function (index, value) {
                        result.push(value);
                    });
                }
                return result;
            }
        });
    }
);