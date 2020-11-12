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

namespace Qenta\CheckoutPage\Controller\Checkout;

use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class Back extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $_request;

    /**
     * @var \Qenta\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var CheckoutCart
     */
    protected $_cart;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $_quoteManagement;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Qenta\CheckoutPage\Model\OrderManagement
     */
    protected $_orderManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Qenta\CheckoutPage\Helper\Data $helper
     * @param CheckoutCart $cart
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Qenta\CheckoutPage\Model\OrderManagement $orderManagement
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Qenta\CheckoutPage\Helper\Data $helper,
        CheckoutCart $cart,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Qenta\CheckoutPage\Model\OrderManagement $orderManagement
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_dataHelper        = $helper;
        $this->_cart              = $cart;
        $this->_url               = $context->getUrl();
        $this->_orderSender       = $orderSender;
        $this->_logger            = $logger;
        $this->_checkoutSession   = $checkoutSession;
        $this->_quoteManagement   = $quoteManagement;
        $this->_orderManagement   = $orderManagement;
    }

    public function execute()
    {
        $redirectTo = 'checkout/cart';

        $defaultErrorMessage = $this->_dataHelper->__('An error occurred during the payment process.');

        try {

            $this->_logger->debug(__METHOD__ . ':' . print_r($this->_request->getPost()->toArray(), true));

            $this->_cart->getCustomerSession()->unsUniqueId();

            if (!$this->_request->isPost()) {
                throw new \Exception('Not a post request');
            }

            $return = \QentaCEE_QPay_ReturnFactory::getInstance($this->_request->getPost()->toArray(),
                $this->_dataHelper->getConfigData('basicdata/secret'));

            if (!$return->validate()) {
                throw new \Exception('Validation error: invalid response');
            }

            if (!strlen($return->mage_orderId)) {
                throw new \Exception('Magento OrderId is missing');
            }

            if (!strlen($return->mage_quoteId)) {
                throw new \Exception('Magento QuoteId is missing');
            }

            $orderId = $this->_request->getPost('mage_orderId');
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->_objectManager->create('\Magento\Sales\Model\Order');
            $order->loadByIncrementId($orderId);
            $orderExists = (bool) $order->getId();

            if ($return->mage_orderCreation == 'before') {
                if (!$orderExists) {
                    throw new \Exception('Order not found');
                }

                $payment = $order->getPayment();
                if (!strlen($payment->getAdditionalInformation('paymentState'))) {
                    $this->_logger->debug(__METHOD__ . ':order not processed via confirm server2server request, check your packetfilter!');
                    $order = $this->_orderManagement->processOrder($return);
                }
            }

            if ($return->mage_orderCreation == 'after') {

                if (!$orderExists &&
                    ( $return->getPaymentState() == \QentaCEE_QPay_ReturnFactory::STATE_SUCCESS || $return->getPaymentState() == \QentaCEE_QPay_ReturnFactory::STATE_PENDING )
                ) {
                    $this->_logger->debug(__METHOD__ . ':order not processed via confirm server2server request, check your packetfilter!');
                    $order = $this->_orderManagement->processOrder($return);
                }
            }

            switch ($return->getPaymentState()) {
                case \QentaCEE_QPay_ReturnFactory::STATE_SUCCESS:
                case \QentaCEE_QPay_ReturnFactory::STATE_PENDING:

                    if ($return->getPaymentState() == \QentaCEE_QPay_ReturnFactory::STATE_PENDING) {
                        $this->messageManager->addNoticeMessage($this->_dataHelper->__('Your order will be processed as soon as we receive the payment confirmation from your bank.'));
                    }

                    /* needed for success page otherwise magento redirects to cart */
                    $this->_checkoutSession->setLastQuoteId($order->getQuoteId());
                    $this->_checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                    $this->_checkoutSession->setLastOrderId($order->getId());
                    $this->_checkoutSession->setLastRealOrderId($order->getIncrementId());
                    $this->_checkoutSession->setLastOrderStatus($order->getStatus());

                    $redirectTo = 'checkout/onepage/success';
                    break;

                case \QentaCEE_QPay_ReturnFactory::STATE_CANCEL:
                    /** @var \QentaCEE_QPay_Return_Cancel $return */
                    $this->messageManager->addNoticeMessage($this->_dataHelper->__('You have canceled the payment process!'));
                    if ($return->mage_orderCreation == 'before') {
                        $quote = $this->_orderManagement->reOrder($return->mage_quoteId);
                        $this->_checkoutSession->replaceQuote($quote)->unsLastRealOrderId();
                    }
                    break;

                case \QentaCEE_QPay_ReturnFactory::STATE_FAILURE:
                    /** @var \QentaCEE_QPay_Return_Failure $return */
                    $msg = $return->getErrors()->getConsumerMessage();
                    if (!strlen($msg)) {
                        $msg = $defaultErrorMessage;
                    }

                    $this->messageManager->addErrorMessage($msg);

                    if ($return->mage_orderCreation == 'before') {
                        $quote = $this->_orderManagement->reOrder($return->mage_quoteId);
                        $this->_checkoutSession->replaceQuote($quote)->unsLastRealOrderId();
                    }
                    break;

                default:
                    throw new \Exception('Unhandled Qenta Checkout Page payment state:' . $return->getPaymentState());
            }

            if ($this->_request->getPost('iframeUsed')) {
                $redirectUrl = $this->_url->getUrl($redirectTo);

                $page = $this->_resultPageFactory->create();
                $page->getLayout()->getBlock('checkout.back')->addData(['redirectUrl' => $redirectUrl]);

                return $page;

            } else {

                $this->_redirect($redirectTo);
            }
        } catch (\Exception $e) {
            if (!$this->messageManager->getMessages()->getCount()) {
                $this->messageManager->addErrorMessage($defaultErrorMessage);
            }
            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());
            $this->_redirect($redirectTo);
        }
    }


}
