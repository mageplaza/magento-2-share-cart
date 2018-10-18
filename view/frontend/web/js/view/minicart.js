/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component,customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_ShareCart/minicart'
        },

        getQuoteId: function (){
            return customerData.get('cart')().quote_url;
        },

        copyQuote: function(){
            var quoteId=document.getElementById("mpQuote");
            quoteId.select();
            document.execCommand("copy")

        },
    });
});
