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
    'underscore',
    'Bss_OneStepCheckout/js/model/url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/totals',
    'Bss_OneStepCheckout/js/model/payment-service',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/payment-service',
    'Bss_OneStepCheckout/js/model/update-item-service',
    'Magento_Ui/js/model/messageList'
], function (
    _,
    urlBuilder,
    storage,
    errorProcessor,
    shippingService,
    totals,
    paymentService,
    url,
    quote,
    methodConverter,
    paymentServiceDefault,
    updateItemService,
    globalMessageList
) {
    'use strict';

    return function (item) {
        var serviceUrl = urlBuilder.getUpdateQtyUrl(),
            address = quote.shippingAddress();

        shippingService.isLoading(true);
        totals.isLoading(true);
        paymentService.isLoading(true);

        return storage.post(
            serviceUrl,
            JSON.stringify({
                address: {
                    'region_id': address.regionId,
                    'region': address.region,
                    'country_id': address.countryId,
                    'postcode': address.postcode
                },
                itemId: parseInt(item.item_id),
                qty: parseFloat(item.qty)
            })
        ).done(function (response) {
            if (response.has_error && response.status) {
                globalMessageList.addSuccessMessage(response);
                window.location.replace(url.build('checkout/cart/'));
            } else {
                if (response.status) {
                    globalMessageList.addSuccessMessage(response);
                    updateItemService.hasUpdateResult(true);
                    shippingService.setShippingRates(response.shipping_methods);
                    paymentServiceDefault.setPaymentMethods(methodConverter(response.payment_methods));
                    updateItemService.hasUpdateResult(false);
                    response.totals.coupon_code ? paymentService.isAppliedCoupon(true) : paymentService.isAppliedCoupon(false);
                    
                } else {
                    globalMessageList.addErrorMessage(response);
                }
            }
        }).fail(function (response) {
            errorProcessor.process(response);
        }).always(function () {
            shippingService.isLoading(false);
            totals.isLoading(false);
            paymentService.isLoading(false);
        });
    };
});
