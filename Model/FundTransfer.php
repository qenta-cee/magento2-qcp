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

namespace Qenta\CheckoutPage\Model;

class FundTransfer
{

    /**
     * @var \Qenta\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @param \Qenta\CheckoutPage\Helper\Data $dataHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Qenta\CheckoutPage\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Framework\DataObject $postObject
     *
     * @return \QentaCEE\QPay\Response\Toolkit\TransferFund
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
            case \QentaCEE\QPay\ToolkitClient::$TRANSFER_FUND_TYPE_EXISTING:
                /** @var \QentaCEE\QPay\Request\Backend\TransferFund\Existing $client */
                if (strlen($postObject['customerStatement'])) {
                    $client->setCustomerStatement($postObject['customerStatement']);
                }

                $ret = $client->send($postObject['amount'], $postObject['currency'], $postObject['orderDescription'],
                    $postObject['sourceOrderNumber']);
                break;

            case \QentaCEE\QPay\ToolkitClient::$TRANSFER_FUND_TYPE_MONETA:
                /** @var \QentaCEE\QPay\Request\Backend\TransferFund\Moneta $client */
                $ret = $client->send($postObject['amount'], $postObject['currency'], $postObject['orderDescription'],
                    $postObject['customerStatement'], $postObject['consumerWalletId']);
                break;

            case \QentaCEE\QPay\ToolkitClient::$TRANSFER_FUND_TYPE_SEPACT:
                /** @var \QentaCEE\QPay\Request\Backend\TransferFund\SepaCT $client */
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
