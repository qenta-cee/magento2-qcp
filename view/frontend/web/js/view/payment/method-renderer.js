/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Qenta Payment CEE GmbH
 * (abbreviated to Qenta CEE) and are explicitly not part of the Qenta CEE range of
 * products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License Version 2 (GPLv2) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Qenta CEE does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Qenta CEE does not guarantee their full
 * functionality neither does Qenta CEE assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Qenta CEE does not guarantee the full functionality
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
                type: 'qenta_checkoutpage_select',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_ccard',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_masterpass',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_ccardmoto',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_maestro',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_eps',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/eps'
            },
            {
                type: 'qenta_checkoutpage_ideal',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/ideal'
            },
            {
                type: 'qenta_checkoutpage_giropay',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_tatrapay',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_sofortbanking',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_skrillwallet',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_bmc',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_p24',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_poli',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_moneta',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_ekonto',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_trustly',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_paybox',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_paysafecard',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_quick',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_paypal',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_epaybg',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_sepa',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_invoice',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'qenta_checkoutpage_invoiceb2b',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'qenta_checkoutpage_installment',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/invoiceinstallment'
            },
            {
                type: 'qenta_checkoutpage_voucher',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            },
            {
                type: 'qenta_checkoutpage_trustpay',
                component: 'Qenta_CheckoutPage/js/view/payment/method-renderer/standard'
            }
        );

        return Component.extend({

        });
    }
);