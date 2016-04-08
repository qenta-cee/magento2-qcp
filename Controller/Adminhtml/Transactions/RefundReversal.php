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

namespace Wirecard\CheckoutPage\Controller\Adminhtml\Transactions;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

class RefundReversal extends \Magento\Sales\Controller\Adminhtml\Transactions
{

    /**
     * @return Redirect
     */
    public function execute()
    {
        $txn = $this->_initTransaction();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$txn) {
            return $resultRedirect->setPath('sales/*/');
        }

        /** @var \Magento\Sales\Model\Order\Payment\Interceptor $payment */
        $payment = $this->orderPaymentRepository->get($txn->getPaymentId());

        /** @var \Wirecard\CheckoutPage\Model\AbstractPayment $methodInstance */
        $methodInstance = $payment->getMethodInstance();
        if ($methodInstance instanceof \Wirecard\CheckoutPage\Model\AbstractPayment) {
            try {
                $methodInstance->refundReversal($payment, $txn);
                $this->messageManager->addSuccess(__('The refund has been reverted.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t revert the refund.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }

        return $resultRedirect->setPath('sales/transactions/view', ['_current' => true]);
    }

}
