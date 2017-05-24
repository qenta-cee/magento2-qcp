/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Wirecard Central Eastern Europe GmbH
 * (abbreviated to Wirecard CEE) and are explicitly not part of the Wirecard CEE range of
 * products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License Version 2 (GPLv2) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Wirecard CEE does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Wirecard CEE does not guarantee their full
 * functionality neither does Wirecard CEE assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Wirecard CEE does not guarantee the full functionality
 * for customized shop systems or installed plugins of other vendors of plugins within the same
 * shop system.
 *
 * Customers are responsible for testing the plugin's functionality before starting productive
 * operation.
 *
 * By installing the plugin into the shop system the customer agrees to these terms of use.
 * Please do not use the plugin if you do not agree to these terms of use!
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'wirecard_checkoutpage_select',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_ccard',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_masterpass',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_ccardmoto',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_maestro',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_eps',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/eps'
            },
            {
                type: 'wirecard_checkoutpage_ideal',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/ideal'
            },
            {
                type: 'wirecard_checkoutpage_giropay',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_tatrapay',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_sofortbanking',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_skrillwallet',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_bmc',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_p24',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_poli',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_moneta',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_ekonto',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_trustly',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_paybox',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_paysafecard',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_quick',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_paypal',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_epaybg',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_sepa',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_invoice',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'wirecard_checkoutpage_invoiceb2b',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'wirecard_checkoutpage_installment',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'wirecard_checkoutpage_voucher',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'wirecard_checkoutpage_trustpay',
                component: 'Wirecard_CheckoutPage/js/view/payment/method-renderer/standard'
            }
        );

        return Component.extend({

        });
    }
);