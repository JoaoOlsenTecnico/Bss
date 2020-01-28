/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'mage/validation'
], function ($, registry) {
    'use strict';

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function (hideError) {
            var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
            return shippingAddressComponent.validateShippingInformation();
        }
    };
});
