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
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_CheckoutAgreements/js/model/agreements-assigner',
        'jquery/ui',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, quote, urlBuilder, url, storage, errorProcessor, customer, fullScreenLoader,agreementsAssigner) {
        'use strict';

        return function (messageContainer, displaymode, iframe) {
            var serviceUrl,
                payload,
                paymentData = quote.paymentMethod(),
                methodeCode = quote.paymentMethod().method,
                checkoutStartUrl = url.build('/wirecardcheckoutpage/checkout/start', {});

            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/set-payment-information', {
                    cartId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    paymentMethod: paymentData,
                    email: quote.guestEmail,
                    billingAddress: quote.billingAddress()
                };
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/set-payment-information', {});
                agreementsAssigner(paymentData);
                payload = {
                    cartId: quote.getQuoteId(),
                    paymentMethod: paymentData,
                    billingAddress: quote.billingAddress()
                };
            }

            fullScreenLoader.startLoader();

            return storage.post(
                serviceUrl, JSON.stringify(payload)
            ).done(
                function () {

                    if (displaymode == 'redirect') {
                        $.mage.redirect(checkoutStartUrl);
                    } else {
                        $('#' + methodeCode + '-button').css('display', 'none');
                        var iframe = $('#' + methodeCode + '-iframe');
                        iframe.css('display', 'block').css('height', '900px').css('width', '640px');

                        if (displaymode == 'iframe')
                            $('html, body').animate({ scrollTop: $('#' + methodeCode).offset().top }, 'slow');

                        if (displaymode == 'popup') {
                            iframe.modal({
                                title: 'Wirecard Checkout Page',
                                autoOpen: true,
                                closed: function() {
                                    var redirectUrl = $('#' + methodeCode + '-back-url').val();

                                    // get rid of onbeforeunload events
                                    document.write("");
                                    location.href = redirectUrl;
                                },
                                closeText: '',
                                buttons: []
                            });
                            $(".modal-header button[data-role='closeBtn']").css('display', 'none');
                        }

                        iframe.one("load", function() {
                            fullScreenLoader.stopLoader();
                        });

                        iframe.attr('src', checkoutStartUrl);
                    }
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
