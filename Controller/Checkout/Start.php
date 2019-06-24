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

namespace Wirecard\CheckoutPage\Controller\Checkout;

use Magento\Checkout\Model\Cart as CheckoutCart;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Wirecard\CheckoutPage\Helper\Data
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
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Wirecard\CheckoutPage\Model\OrderManagement
     */
    protected $_orderManagement;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Wirecard\CheckoutPage\Helper\Data $dataHelper
     * @param \Wirecard\CheckoutPage\Model\OrderManagement $orderManagement
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param CheckoutCart $cart
     * @param \Psr\Log\LoggerInterface $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Wirecard\CheckoutPage\Helper\Data $dataHelper,
        \Wirecard\CheckoutPage\Model\OrderManagement $orderManagement,
        \Magento\Checkout\Helper\Data $checkoutData,
        CheckoutCart $cart,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_dataHelper      = $dataHelper;
        $this->_cart            = $cart;
        $this->_url             = $context->getUrl();
        $this->_logger          = $logger;
        $this->_checkoutData    = $checkoutData;
        $this->_orderManagement = $orderManagement;
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $formKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        $urls = [
            'confirm' => $this->_url->getUrl('wirecardcheckoutpage/checkout/confirm',
                ['_secure' => true, '_nosid' => true, 'form_key' => $formKey->getFormKey()]),
            'return'  => $this->_url->getUrl('wirecardcheckoutpage/checkout/back',
                ['_secure' => true, '_nosid' => true, 'form_key' => $formKey->getFormKey()])
        ];

        $payment = null;
        try {
            if ($this->getCheckoutMethod() == \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST) {
                $this->prepareGuestQuote();
            }

            $customerDob = $this->_cart->getQuote()->getPayment()->getAdditionalInformation('customerDob');
            if (strlen($customerDob)) {
                $this->_cart->getQuote()->setCustomerDob($customerDob);
            }
            /** @var \Wirecard\CheckoutPage\Model\AbstractPayment $payment */
            $payment = $this->_cart->getQuote()->getPayment()->getMethodInstance();

            $init = $payment->initPaymentByCart($this->_cart, $urls,
                new \Magento\Framework\DataObject($payment->getInfoInstance()->getAdditionalInformation()));

            if ($this->_dataHelper->getConfigData('options/order_creation') == 'before') {
                $this->_orderManagement->submitOrder($this->_cart->getQuote());
            }

            $this->getResponse()->setRedirect($init->getRedirectUrl());
        } catch (\Exception $e) {
            $this->_logger->debug(__METHOD__ . ':' . $e->getMessage());
            $this->messageManager->addErrorMessage($this->_dataHelper->__('An error occurred during the payment process'));
            if ($payment === null) {
                $this->_redirect('/');
            } else {
                $this->getResponse()->setRedirect($this->_url->getUrl('wirecardcheckoutpage/checkout/failed',
                    [
                        '_secure' => true,
                        '_query'  => ['iframeused' => (int) ( $payment->getDisplayMode() != 'redirect' )]
                    ]));
            }
        }

    }


    /**
     * Prepare quote for guest checkout order submit
     *
     * @return $this
     */
    protected function prepareGuestQuote()
    {
        $quote = $this->_cart->getQuote();
        $quote->setCustomerId(null)
              ->setCustomerEmail($quote->getBillingAddress()->getEmail())
              ->setCustomerFirstname($quote->getBillingAddress()->getFirstname())
              ->setCustomerLastname($quote->getBillingAddress()->getLastname())
              ->setCustomerIsGuest(true)
              ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);

        return $this;
    }

    /**
     * Get checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->_cart->getCustomerSession()->isLoggedIn()) {
            return \Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER;
        }
        if (!$this->_cart->getQuote()->getCheckoutMethod()) {
            if ($this->_checkoutData->isAllowedGuestCheckout($this->_cart->getQuote())) {
                $this->_cart->getQuote()->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
            } else {
                $this->_cart->getQuote()->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
            }
        }

        return $this->_cart->getQuote()->getCheckoutMethod();
    }
}
