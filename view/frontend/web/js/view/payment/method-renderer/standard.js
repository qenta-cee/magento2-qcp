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
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Wirecard_CheckoutPage/js/action/set-payment-method',
        'jquery',
        'mage/translate'
    ],
    function (Component, additionalValidators, setPaymentMethodAction, $, $t) {
        return Component.extend({
            defaults: {
                template: 'Wirecard_CheckoutPage/payment/method-standard'
            },
            validate: function () {
                return true;
            },
            getInstructions: function () {
                if (!window.checkoutConfig.payment[this.getCode()])
                    return '';

                return window.checkoutConfig.payment[this.getCode()].instructions;
            },
            getDisplayMode: function() {
                if (!window.checkoutConfig.payment[this.getCode()])
                    return false;

                return window.checkoutConfig.payment[this.getCode()].displaymode;
            },
            getLogoUrl: function() {
                if (!window.checkoutConfig.payment[this.getCode()])
                    return false;

                return window.checkoutConfig.payment[this.getCode()].logo_url
            },
            placeWirecardOrder: function () {

                if (this.validate() && additionalValidators.validate())
                {
                    this.selectPaymentMethod(); // save selected payment method in Quote

                    setPaymentMethodAction(this.messageContainer, this.getDisplayMode());
                }
                return false;
            }
        });
    }
);