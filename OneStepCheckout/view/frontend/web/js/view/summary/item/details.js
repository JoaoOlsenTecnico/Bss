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
    'Magento_Checkout/js/view/summary/item/details',
    'mage/translate',
    'ko',
    'underscore',
    'Magento_Customer/js/customer-data',
    'Bss_OneStepCheckout/js/action/update-item',
    'Magento_Checkout/js/model/quote'
], function ($, Component, $t, ko, _, customerData, updateItemAction, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Bss_OneStepCheckout/summary/item/details'
        },

        titleQtyBox: ko.observable($t('Qty')),

        /**
         * @param {Object} item
         * @returns void
         */
        updateQty: function (item) {
            updateItemAction(item).done(
                function (response) {
                    var totals = response.totals,
                        data = JSON.parse(this.data),
                        itemId = data.itemId,
                        itemsOrigin = [],
                        quoteItemData = window.checkoutConfig.quoteItemData;
                    if (!response.status) {
                        var originItem = _.find(quoteItemData, function (index) {
                            return index.item_id == itemId;
                        });
                        $.each(totals.items, function(index) {
                            if (this.item_id == originItem.item_id) {
                                this.qty = originItem.qty;
                            }
                            itemsOrigin[index] = this;
                        });
                        totals.items = itemsOrigin;
                    } else {
                        customerData.reload('cart');
                    }
                    quote.setTotals(totals);
                }
            );
        },

        /**
         * @param {*} itemId
         * @returns {String}
         */
        getProductUrl: function (itemId) {
            if (_.isUndefined(customerData.get('cart')())) {
                customerData.reload('cart');
            }
            var productUrl = 'javascript:void(0)',
                cartData = customerData.get('cart')(),
                items = cartData.items;

            var item = _.find(items, function (item) {
                return item.item_id == itemId;
            });

            if (!_.isUndefined(item) && item.product_has_url) {
                productUrl = item.product_url;
            }
            return productUrl;
        }
    });
});
