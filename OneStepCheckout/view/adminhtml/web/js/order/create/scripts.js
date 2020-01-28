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

/* global AdminOrder */
define([
    'jquery',
    'Magento_Sales/order/create/scripts'
], function (jQuery) {
    'use strict';

    AdminOrder.prototype.itemsUpdate = function () {
        var area = ['sidebar', 'items', 'shipping_method', 'billing_method', 'totals', 'giftmessage', 'gift_wrap'];
        // prepare additional fields
        var fieldsPrepare = {update_items: 1};
        var info = $('order-items_grid').select('input', 'select', 'textarea');
        for (var i = 0; i < info.length; i++) {
            if (!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                fieldsPrepare[info[i].name] = info[i].getValue();
            }
        }
        fieldsPrepare = Object.extend(fieldsPrepare, this.productConfigureAddFields);
        this.productConfigureSubmit('quote_items', area, fieldsPrepare);
        this.orderItemChanged = false;
    };
    AdminOrder.prototype.sidebarApplyChanges = function (auxiliaryParams) {
        if ($(this.getAreaId('sidebar'))) {
            var data = {};
            if (this.collectElementsValue) {
                var elems = $(this.getAreaId('sidebar')).select('input');
                for (var i = 0; i < elems.length; i++) {
                    if (elems[i].getValue()) {
                        data[elems[i].name] = elems[i].getValue();
                    }
                }
            }
            if (auxiliaryParams instanceof Object) {
                for (var paramName in auxiliaryParams) {
                    data[paramName] = String(auxiliaryParams[paramName]);
                }
            }
            data.reset_shipping = true;
            this.loadArea(['sidebar', 'items', 'shipping_method', 'billing_method', 'totals', 'giftmessage', 'gift_wrap'], true, data);
        }
    };
    AdminOrder.prototype.sidebarConfigureProduct = function (listType, productId, itemId) {
        // create additional fields
        var params = {};
        params.reset_shipping = true;
        params.add_product = productId;
        this.prepareParams(params);
        for (var i in params) {
            if (params[i] === null) {
                unset(params[i]);
            } else if (typeof (params[i]) == 'boolean') {
                params[i] = params[i] ? 1 : 0;
            }
        }
        var fields = [];
        for (var name in params) {
            fields.push(new Element('input', {type: 'hidden', name: name, value: params[name]}));
        }
        // add additional fields before triggered submit
        productConfigure.setBeforeSubmitCallback(listType, function () {
            productConfigure.addFields(fields);
        }.bind(this));
        // response handler
        productConfigure.setOnLoadIFrameCallback(listType, function (response) {
            if (!response.ok) {
                return;
            }
            this.loadArea(['items', 'shipping_method', 'billing_method', 'totals', 'giftmessage', 'gift_wrap'], true);
        }.bind(this));
        // show item configuration
        itemId = itemId ? itemId : productId;
        productConfigure.showItemConfiguration(listType, itemId);
        return false;
    };
    AdminOrder.prototype.productGridAddSelected = function () {
        if (this.productGridShowButton) Element.show(this.productGridShowButton);
        var area = ['search', 'items', 'shipping_method', 'totals', 'giftmessage', 'billing_method', 'gift_wrap'];
        // prepare additional fields and filtered items of products
        var fieldsPrepare = {};
        var itemsFilter = [];
        var products = this.gridProducts.toObject();
        for (var productId in products) {
            itemsFilter.push(productId);
            var paramKey = 'item[' + productId + ']';
            for (var productParamKey in products[productId]) {
                paramKey += '[' + productParamKey + ']';
                fieldsPrepare[paramKey] = products[productId][productParamKey];
            }
        }
        this.productConfigureSubmit('product_to_add', area, fieldsPrepare, itemsFilter);
        productConfigure.clean('quote_items');
        this.hideArea('search');
        this.gridProducts = $H({});
    }
});