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

namespace Wirecard\CheckoutPage\Model;

class Test
{

    /**
     * @var \Wirecard\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @param \Wirecard\CheckoutPage\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Wirecard\CheckoutPage\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {

        $this->_dataHelper = $dataHelper;
        $this->_logger     = $logger;
    }

    public function config($urls)
    {

        //$returnUrl = $this->getUrl('wirecard_checkoutpage/processing/return', array('_secure' => true, '_nosid' => true));

        $returnUrl = $urls['return'];

        $init = new \WirecardCEE_QPay_FrontendClient($this->_dataHelper->getConfigArray());
        $init->setPluginVersion($this->_dataHelper->getPluginVersion());

        $init->setOrderReference('Configtest #' . uniqid());

        if ($this->_dataHelper->getConfigData('options/sendconfirmemail')) {
            $init->setConfirmMail($this->_dataHelper->getStoreConfigData('trans_email/ident_general/email'));
        }

        $consumerData = new \WirecardCEE_Stdlib_ConsumerData();
        $consumerData->setIpAddress($this->_dataHelper->getClientIp());
        $consumerData->setUserAgent($this->_dataHelper->getUserAgent());

        $init->setAmount(10)
             ->setCurrency('EUR')
             ->setPaymentType(\WirecardCEE_QPay_PaymentType::SELECT)
             ->setOrderDescription('Configtest #' . uniqid())
             ->setSuccessUrl($returnUrl)
             ->setPendingUrl($returnUrl)
             ->setCancelUrl($returnUrl)
             ->setFailureUrl($returnUrl)
            //->setConfirmUrl(Mage::getUrl('wirecard_checkoutpage/processing/confirm', array('_secure' => true, '_nosid' => true)))
             ->setConfirmUrl($urls['confirm'])
             ->setServiceUrl($this->_dataHelper->getConfigData('options/service_url'))
             ->setConsumerData($consumerData);

        if (strlen($this->_dataHelper->getConfigData('options/bgcolor'))) {
            $init->setBackgroundColor($this->_dataHelper->getConfigData('options/bgcolor'));
        }

        if (strlen($this->_dataHelper->getConfigData('options/displaytext'))) {
            $init->setDisplayText($this->_dataHelper->getConfigData('options/displaytext'));
        }

        if (strlen($this->_dataHelper->getConfigData('options/imageurl'))) {
            $init->setImageUrl($this->_dataHelper->getConfigData('options/imageurl'));
        }

        $initResponse = $init->initiate();

        if ($initResponse->getStatus() == \WirecardCEE_QPay_Response_Initiation::STATE_FAILURE) {
            $msg = $initResponse->getError()->getConsumerMessage();
            if (!strlen($msg)) {
                $msg = $initResponse->getError()->getMessage();
            }

            throw new \Exception($msg);
        }

        return true;
    }
}
