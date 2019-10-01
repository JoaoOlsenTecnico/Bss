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
    'Magento_Customer/js/model/customer',
    'Bss_OneStepCheckout/js/model/save-new-account-information',
    'mage/validation'
], function ($, customer, saveNewAccountInformation) {
    'use strict';

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function () {
            var validationResult = true,
                createNewAccountCheckBoxId = 'create-new-customer',
                loginFormSelector = 'form[data-role=email-with-possible-login]';

            if (!customer.isLoggedIn() && $(loginFormSelector + ' #'+createNewAccountCheckBoxId).is(":checked")) {
                $(loginFormSelector).validation();
                validationResult = Boolean($(loginFormSelector + ' input[name=newcustomerpassword]').valid());
                if (validationResult == true) {
                    validationResult = Boolean($(loginFormSelector + ' input[name=newcustomerpassword_confirmation]').valid());
                }
            }
            saveNewAccountInformation.ajaxSave();
            return validationResult;
        }
    };
});