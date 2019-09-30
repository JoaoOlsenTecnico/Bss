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
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, _, urlBuilder, customer, quote) {
    'use strict';

    var oscStoreCode;
    if (!_.isUndefined(window.checkoutConfig.bssOsc.giftOptionsConfig)) {
        oscStoreCode = window.checkoutConfig.bssOsc.giftOptionsConfig.storeCode;
    } else {
        oscStoreCode = window.checkoutConfig.storeCode;
    }

    return $.extend(urlBuilder, {
        storeCode: oscStoreCode,

        /**
         * Get update item url for service.
         *
         * @return {String}
         */
        getUpdateQtyUrl: function () {
            var serviceUrl;
            if (!customer.isLoggedIn()) {
                serviceUrl = this.createUrl('/bss-osc/guest-carts/:cartId/update-item', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = this.createUrl('/bss-osc/carts/mine/update-item', {});
            }
            return serviceUrl;
        }
    });
});
