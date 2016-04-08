<?php
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

namespace Wirecard\CheckoutPage\Model\Payment;

use Wirecard\CheckoutPage\Model\AbstractPayment;

class Invoiceb2b extends AbstractPayment
{
    const CODE = 'wirecard_checkoutpage_invoiceb2b';
    protected $_code = self::CODE;

    protected $_paymentMethod = \WirecardCEE_Stdlib_PaymentTypeAbstract::INVOICE;

    protected $_logo = 'invoice.png';

    protected $_forceSendAdditionalData = true;

    /**
     * force transmitting the basket data
     *
     * @return bool
     */
    protected function forceSendingBasket()
    {
        return true;
    }

    /**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     *
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $avail = parent::isAvailable($quote);
        if ($avail === false) {
            return false;
        }

        if ($quote === null) {
            return false;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        if ($quote->hasVirtualItems()) {
            return false;
        }

        if ($this->getConfigData('billing_shipping_address_identical') && !$this->compareAddresses($quote)) {
            return false;
        }

        $currencies = explode(',', $this->getConfigData('currency'));
        if (!in_array($quote->getQuoteCurrencyCode(), $currencies)) {
            return false;
        }

        if (strlen($this->getConfigData('shippingcountry'))) {
            $countries = explode(',', $this->getConfigData('shippingcountry'));
            if (!in_array($quote->getShippingAddress()->getCountry(), $countries)) {
                return false;
            }
        }

        $billingAddress = $quote->getBillingAddress();
        if (strlen($billingAddress->getCompany())) {
            return true;
        }

        if (!strlen($billingAddress->getVatId())) {
            return false;
        }

        return true;
    }

}