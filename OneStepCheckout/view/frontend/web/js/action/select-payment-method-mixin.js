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
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry'
], function ($, wrapper, fullScreenLoader, urlBuilder, quote, rateRegistry) {
    'use strict';
    return function (selectPaymentMethod) {
        return wrapper.wrap(selectPaymentMethod, function (originalAction, paymentMethod) {
            var serviceUrl = urlBuilder.build('onestepcheckout/checkout/applyPaymentMethod');
            originalAction(paymentMethod);
            fullScreenLoader.startLoader();
            $.ajax(
                serviceUrl,
                {
                    data: {payment_method: paymentMethod},
                    complete: function () {
                        var address = quote.shippingAddress();
                        if (address != null) {
                            rateRegistry.set(address.getKey(), null);
                            rateRegistry.set(address.getCacheKey(), null);
                            quote.shippingAddress(address);
                        }
                        fullScreenLoader.stopLoader();
                    }
                }
            );
        });
    };
});