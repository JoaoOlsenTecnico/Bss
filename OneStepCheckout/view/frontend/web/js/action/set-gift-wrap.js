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
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, urlManager, errorProcessor, storage, getPaymentInformationAction, totals, fullScreenLoader) {
        'use strict';

        return function (action) {
            var url = urlManager.build('rest/V1/bss-osc/giftwrap/apply/' + action);
            fullScreenLoader.startLoader();
            return storage.put(
                url,
                {},
                false
            ).done(
                function (response) {
                    var res = JSON.parse(response);
                    if (res.status) {
                        var deferred = $.Deferred();
                        totals.isLoading(true);
                        getPaymentInformationAction(deferred);
                        if (res.status == 'virtual') {
                            $('#giftwrap-checkbox').remove();
                        } else {
                            $('#giftwrap-checkbox label span').text(res.gift_wrap_label);
                        }
                        $.when(deferred).done(function () {
                            fullScreenLoader.stopLoader();
                            totals.isLoading(false);
                        });
                    } else {
                        fullScreenLoader.stopLoader();
                    }
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                }
            );
        };
    }
);