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
    'ko',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'Bss_OneStepCheckout/js/action/validate-shipping-information',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/shipping-service',
    'underscore',
    'Magento_Ui/js/modal/alert',
    'Magento_Checkout/js/checkout-data'
], function (
    ko,
    $,
    Component,
    registry,
    $t,
    quote,
    validateShippingInformationAction,
    fullScreenLoader,
    selectBillingAddress,
    additionalValidators,
    shippingService,
    _,
    alert,
    checkoutData
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Bss_OneStepCheckout/place-order-btn'
        },

        placeOrderLabel: ko.observable($t('Place Order')),

        isVisible: ko.observable(true),

        isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null && quote.paymentMethod() != null),

        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this;
            quote.billingAddress.subscribe(function (address) {
                if (quote.isVirtual()) {
                    setTimeout(function () {
                        self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null);
                    }, 500);
                } else {
                    self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null && quote.shippingMethod() != null);
                }
            }, this);
            quote.paymentMethod.subscribe(function (newMethod) {
                if (quote.isVirtual()) {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null);
                } else {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null && quote.shippingMethod() != null);
                }
            }, this);
            if (!quote.isVirtual()) {
                quote.shippingMethod.subscribe(function (method) {
                    var availableRate,
                        shippingRates = shippingService.getShippingRates();
                    if (method) {
                        availableRate = _.find(shippingRates(), function (rate) {
                            return rate['carrier_code'] + '_' + rate['method_code'] === method['carrier_code'] + '_' + method['method_code'];
                        });
                    }
                    self.isPlaceOrderActionAllowed(availableRate && quote.paymentMethod() != null && quote.billingAddress() != null);
                }, this);
            }

            if (window.checkoutConfig.magento_version >= "2.3.1") {
                var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();

                if (selectedPaymentMethod == "paypal_express") {
                    self.isVisible(false);
                }

                $(document).on('change', '.payment-method .radio', function() {
                    if ($('.payment-method._active').find('.actions-toolbar').is('#paypal-express-in-context-button')) {
                        self.isVisible(false);
                    } else {
                        self.isVisible(true);
                    }
                });
            }
        },

        placeOrder: function (data, event) {
            var self = this;
            var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
            var billingAddressComponent = registry.get('checkout.steps.billing-step.payment.payments-list.billing-address-form-shared');

            if (event) {
                event.preventDefault();
            }

            if (additionalValidators.validate()) {
                if (quote.isVirtual()) {
                    $('input#' + self.getCode())
                        .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                        .trigger('click');
                } else {
                    if (shippingAddressComponent.validateShippingInformation()) {
                        if (billingAddressComponent.isAddressSameAsShipping()) {
                            fullScreenLoader.startLoader();
                            selectBillingAddress(quote.shippingAddress());
                        }
                        validateShippingInformationAction().done(
                            function () {
                                fullScreenLoader.stopLoader();
                                $('input#' + self.getCode())
                                    .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                                    .trigger('click');
                            }
                        ).fail(
                            function () {
                                fullScreenLoader.stopLoader();
                            }
                        );
                    }
                }
            } else {
                alert({
                    title: $t('Note'),
                    content: $t('Please Enter All Required Field.')
                });
            }
            return false;
        },

        getCode: function () {
            return quote.paymentMethod().method;
        }
    });
});
