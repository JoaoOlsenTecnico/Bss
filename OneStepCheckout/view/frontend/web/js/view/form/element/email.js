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
    'Magento_Checkout/js/view/form/element/email',
    'mage/validation'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Bss_OneStepCheckout/form/element/email',
            createNewAccount: false

        },
        checkDelay: 1000,
        initObservable: function () {
            this._super()
                .observe(['email', 'emailFocused', 'isLoading', 'isPasswordVisible', 'createNewAccount']);
            return this;
        },
        createNewAccountConfig: function () {
            return window.checkoutConfig.bssOsc.autoCreateNewAccount.enable;
        },
        createNewAccountChecked: function (element) {
            if ($('#' + element).is(":checked")) {
                this.createNewAccount(true);
            } else {
                this.createNewAccount(false);
            }
        },
        minLength: function () {
            return window.checkoutConfig.bssOsc.autoCreateNewAccount.minLength;
        },
        minCharacterSets: function () {
            return window.checkoutConfig.bssOsc.autoCreateNewAccount.minCharacterSets;
        }
    });
});