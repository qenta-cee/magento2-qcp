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

namespace Qenta\CheckoutPage\Block\Adminhtml\Transactions\Detail;

use \Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;

class Plugin
{
    /**
     * Transaction model
     *
     * @var \Magento\Sales\Model\Order\Payment\Transaction
     */
    protected $_txn;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    private $adminHelper;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Sales\Api\OrderPaymentRepositoryInterface $orderPaymentRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->adminHelper = $adminHelper;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->orderPaymentRepository = $orderPaymentRepository;
    }

    public function beforeGetLayout(\Magento\Sales\Block\Adminhtml\Transactions\Detail $subject)
    {
        $this->_txn = $this->_coreRegistry->registry('current_transaction');
        if (!$this->_txn) {
            return;
        }

        if ($this->_txn->getTxnType() != Transaction::TYPE_REFUND)
            return;

        /** @var \Magento\Sales\Model\Order\Payment\Interceptor $payment */
        $payment = $this->orderPaymentRepository->get($this->_txn->getPaymentId());

        $methodInstance = $payment->getMethodInstance();

        if ($methodInstance instanceof \Qenta\CheckoutPage\Model\AbstractPayment) {
            $addInfo = $this->_txn->getAdditionalInformation('raw_details_info');

            if (isset($addInfo['orderNumber']) && isset($addInfo['creditNumber'])) {
                $fetchUrl = $this->_urlBuilder->getUrl('qentacheckoutpage/transactions/refundreversal', ['_current' => true]);
                $subject->addButton('refundreversal', ['label' => __('Refund Reversal'), 'onclick' => "setLocation('{$fetchUrl}')", 'class' => 'button']);
            }
        }

    }
}
