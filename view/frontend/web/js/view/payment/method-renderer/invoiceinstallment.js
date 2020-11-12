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
        'Qenta_CheckoutPage/js/view/payment/method-renderer/standard',
        'Qenta_CheckoutPage/js/action/set-payment-method',
        'Qenta_CheckoutPage/js/model/min-age-validator',
        'mage/url',
        'jquery',
        'mage/translate'
    ],
    function (Component, setPaymentMethodAction, minAgeValidator, url, $, $t) {
        return Component.extend({

            customerData: {},
            customerDob: null,
            defaults: {
                template: 'Qenta_CheckoutPage/payment/method-invoiceinstallment'
            },
            initObservable: function () {
                this._super().observe('customerDob');
                return this;
            },
            initialize: function () {
                this._super();
                this.customerData = window.customerData;
                this.customerDob(this.customerData.dob);
                return this;
            },

            getData: function () {
                var parent = this._super(),
                    additionalData = {};

                additionalData.customerDob = this.customerDob();

                return $.extend(true, parent, {'additional_data': additionalData});
            },
            getMinAge: function() {
                if (!window.checkoutConfig.payment[this.getCode()])
                    return 0;

                return window.checkoutConfig.payment[this.getCode()].min_age
            },
            isB2B: function() {
                return this.getCode() == 'qenta_checkoutpage_invoiceb2b';
            },
            validate: function () {
                minAgeValidator.minage = this.getMinAge();
                if (!this.isB2B() && !minAgeValidator.validate(this.customerDob())) {
                    var errorPane = $('#' + this.getCode() + '-dob-error');
                    errorPane.html($t('You have to be %age% years or older to use this payment.'.replace('%age%', minAgeValidator.minage)));
                    errorPane.css('display', 'block');
                    return false;
                }

                var form = $('#' + this.getCode() + '-form');
                return $(form).validation() && $(form).validation('isValid');
            },

            getConsentText: function () {
                return window.checkoutConfig.payment[this.getCode()].consenttxt;
            }

        });
    }
);
