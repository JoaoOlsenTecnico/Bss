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
        'Magento_Ui/js/modal/alert',
        'mage/translate'
    ],
    function ($, urlManager, errorProcessor, storage, getPaymentInformationAction, totals, alert, $t) {
        'use strict';

        return function (fee, action) {
            var url = urlManager.build('rest/V1/bss-osc/giftwrap/validate/' + fee + '/' + action);
            return storage.put(
                url,
                {},
                false
            ).done(
                function (response) {
                    var res = JSON.parse(response);
                    if (res.status == true) {
                        var deferred = $.Deferred();
                        totals.isLoading(true);
                        getPaymentInformationAction(deferred);
                        $.when(deferred).done(function () {
                            if (res.gift_wrap_update == true) {
                                $('#giftwrap-checkbox label span').text(res.gift_wrap_label);
                                if (res.gift_wrap_status) {
                                    totals.isLoading(false);
                                    alert({
                                        title: $t('Note'),
                                        content: $t('Gift Wrap update.')
                                    });
                                }
                            }
                            if (res.gift_wrap_display == false && $('#giftwrap-checkbox').length) {
                                $('#giftwrap-checkbox').remove();
                                totals.isLoading(false);
                                alert({
                                    title: $t('Note'),
                                    content: $t('Sorry, can not use gift wrap now!')
                                });
                            }
                        });
                    } else {
                        totals.isLoading(false);
                        alert({
                            title: $t('Note'),
                            content: $t('Error.')
                        });
                    }
                }
            ).fail(
                function (response) {
                    totals.isLoading(false);
                }
            );
        };
    }
);