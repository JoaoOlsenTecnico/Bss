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
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'mage/translate'
    ],
    function ($, Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,

            isDisplayed: function () {
                if (quote.isVirtual()) {
                    return false;
                }
                return this.isFullMode() && this.getPureValue() !== 0;
            },

            getAmount: function () {
                return this.getPureValue();
            },

            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            },

            getPureValue: function () {
                var price = 0,
                    segment;

                if (this.totals) {
                    segment = totals.getSegment('osc_gift_wrap');

                    if (segment) {
                        price = segment.value;
                    }
                }
                return price;
            }
        });
    }
);