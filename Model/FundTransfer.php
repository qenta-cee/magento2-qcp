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

class FundTransfer
{

    /**
     * @var \Wirecard\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @param \Wirecard\CheckoutPage\Helper\Data $dataHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Wirecard\CheckoutPage\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Framework\DataObject $postObject
     *
     * @return \WirecardCEE_QPay_Response_Toolkit_TransferFund
     * @throws \Exception
     */
    public function sendrequest($postObject)
    {
        $backendClient = $this->_dataHelper->getBackendClient();

        $type = strtoupper($postObject['transferType']);

        $client = $backendClient->transferfund($type);

        if (strlen($postObject['orderNumber'])) {
            $client->setOrderNumber($postObject['orderNumber']);
        }
        if (strlen($postObject['orderReference'])) {
            $client->setOrderReference($postObject['orderReference']);
        }
        if (strlen($postObject['creditNumber'])) {
            $client->setCreditNumber($postObject['creditNumber']);
        }

        switch ($type) {
            case \WirecardCEE_QPay_ToolkitClient::$TRANSFER_FUND_TYPE_EXISTING:
                /** @var \WirecardCEE_QPay_Request_Backend_TransferFund_Existing $client */
                if (strlen($postObject['customerStatement'])) {
                    $client->setCustomerStatement($postObject['customerStatement']);
                }

                $ret = $client->send($postObject['amount'], $postObject['currency'], $postObject['orderDescription'],
                    $postObject['sourceOrderNumber']);
                break;

            case \WirecardCEE_QPay_ToolkitClient::$TRANSFER_FUND_TYPE_SKIRLLWALLET:
                /** @var \WirecardCEE_QPay_Request_Backend_TransferFund_SkrillWallet $client */
                $ret = $client->send($postObject['amount'], $postObject['currency'], $postObject['orderDescription'],
                    $postObject['customerStatement'], $postObject['consumerEmail']);
                break;

            case \WirecardCEE_QPay_ToolkitClient::$TRANSFER_FUND_TYPE_MONETA:
                /** @var \WirecardCEE_QPay_Request_Backend_TransferFund_Moneta $client */
                $ret = $client->send($postObject['amount'], $postObject['currency'], $postObject['orderDescription'],
                    $postObject['customerStatement'], $postObject['consumerWalletId']);
                break;

            case \WirecardCEE_QPay_ToolkitClient::$TRANSFER_FUND_TYPE_SEPACT:
                /** @var \WirecardCEE_QPay_Request_Backend_TransferFund_SepaCT $client */
                $ret = $client->send($postObject['amount'], $postObject['currency'], $postObject['orderDescription'],
                    $postObject['bankAccountOwner'],
                    $postObject['bankBic'], $postObject['bankAccountIban']);
                break;

            default:
                throw new \Exception($this->_dataHelper->__('Invalid fund transfer type'));
        }

        return $ret;
    }
}
