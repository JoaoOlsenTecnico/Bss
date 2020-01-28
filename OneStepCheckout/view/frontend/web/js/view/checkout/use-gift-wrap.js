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
        'uiComponent',
        'Bss_OneStepCheckout/js/action/set-gift-wrap',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/quote',
    ],
    function (
        $,
        ko,
        Component,
        setGiftWrapAction,
        totals,
        quote
    ) {
        'use strict';
        return Component.extend({
            totals: quote.getTotals(),
            initObservable: function () {
                this._super()
                    .observe({
                        CheckVal: ko.observable(this.isChecked())
                    });

                this.CheckVal.subscribe(function (value) {
                    if (quote.isVirtual()) {
                        return;
                    }
                    if (value) {
                        setGiftWrapAction(1);
                    } else {
                        setGiftWrapAction(0);
                    }
                });

                return this;
            },
            isDisplayed: function () {
                if (quote.isVirtual()) {
                    return false;
                }
                return true;
            },
            getTitle: function () {
                return ko.observableArray([window.checkoutConfig.bssOsc.giftwrap]);
            },
            isChecked: function () {
                var price = 0,
                    segment;

                if (this.totals) {
                    segment = totals.getSegment('osc_gift_wrap');

                    if (segment) {
                        price = segment.value;
                    }
                }
                if (price == 0) {
                    return false
                }
                return true;
            }
        });
    }
);