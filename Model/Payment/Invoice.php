<?php
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

namespace Qenta\CheckoutPage\Model\Payment;

use Qenta\CheckoutPage\Model\AbstractPayment;

class Invoice extends AbstractPayment
{
    const CODE = 'qenta_checkoutpage_invoice';
    protected $_code = self::CODE;

    protected $_paymentMethod = \QentaCEE\Stdlib\PaymentTypeAbstract::INVOICE;

    protected $_autoDepositAllowed = false;

    protected $_logo = 'invoice.png';

    protected $_forceSendAdditionalData = true;


    /**
     * Assign data to info model instance
     *
     * @param array|\Magento\Framework\DataObject $data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        if (!$data instanceof \Magento\Framework\DataObject) {
            $data = new \Magento\Framework\DataObject($data);
        }
        $additionalData = $data->getData('additional_data');

        /** @var \Magento\Quote\Model\Quote\Payment $infoInstance */
        $infoInstance = $this->getInfoInstance();
        $infoInstance->setAdditionalInformation('customerDob', $additionalData['customerDob']);

        return $this;
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

        if ($this->getConfigData('provider') == 'payolution') {
            return $this->_isAvailablePayolution($quote);
        }

        return true;
    }

    /**
     * force transmitting the basket data
     *
     * @return bool
     */
    protected function forceSendingBasket()
    {
        return $this->getConfigData('provider') == 'qenta';
    }


}