/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ShareCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function ($, Component, customerData, $t) {
    'use strict';

    var isReload = true;

    return Component.extend({
        defaults: {
            template: 'Mageplaza_ShareCart/minicart'
        },

        initialize: function () {
            this._super();

            if (isReload) {
                customerData.reload(['cart'], false);
                isReload = false;
            }
            this.customer = customerData.get('cart');
        },

        moveShareCart: function(){
            $(document).ready(function () {
                $('.secondary.sharecart').appendTo($('#minicart-content-wrapper .action.viewcart').parent());
            });
        },

        getQuoteId: function () {
            return customerData.get('cart')().quote_url;
        },

        copyQuote: function (object, e) {
            var quoteUrl = document.createElement('textarea');

            quoteUrl.value = customerData.get('cart')().quote_url;
            document.body.appendChild(quoteUrl);
            quoteUrl.select();
            document.execCommand('copy');
            document.body.removeChild(quoteUrl);

            e.currentTarget.setAttribute('class', 'mp-tooltipped');
            e.currentTarget.setAttribute('aria-label', $t('Copied!'));
        },

        leaveQuote: function (object, e) {
            e.currentTarget.removeAttribute('class');
            e.currentTarget.removeAttribute('aria-label');
        },

        isDisplay: function () {
            return customerData.get('cart')().summary_count;
        }
    });
});
