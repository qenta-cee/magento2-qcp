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
use Magento\Store\Model\ScopeInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class Select extends AbstractPayment
{

    const CODE = 'qenta_checkoutpage_select';
    protected $_code = self::CODE;

    protected $_paymentMethod = 'SELECT';

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * @var \Qenta\CheckoutPage\Model\App\Config\ScopePool
     */
    protected $_scopePool;

    /**
     * Payment method factory
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_paymentMethodFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Qenta\CheckoutPage\Helper\Data $helper,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []

    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $helper,
            $transactionBuilder,
            $orderSender,
            $transactionRepository,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_paymentConfig = $paymentConfig;
        $this->_paymentMethodFactory = $paymentMethodFactory;
    }


    /**
     * set payment specific request data
     *
     * @param \QentaCEE\QPay\FrontendClient $init
     * @param \Magento\Checkout\Model\Cart $cart
     */
    protected function setAdditionalRequestData($init, $cart)
    {
        if (!$this->_dataHelper->getConfigData('options/paymenttypesortorder'))
            return;

        $blacklist = [
            'qenta_checkoutpage_DUMMY',
        ];

        // payment type overrides
        $map = [
            'DUMMY' => 'DUMMY'
        ];

        $paymenttypes = array();

        /* read all payment methods, regardless whether they are active or not */
        foreach ($this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null) as $paymentCode => $data) {
            if (isset($data['model']) && isset($data['sort_order']) && $data['sort_order'] != 0) {

                /** @var AbstractPayment $paymentModel */
                if ($paymentCode == $this->getCode()) {
                    continue;
                }

                if (!preg_match('/^qenta_checkoutpage/i', $paymentCode)) {
                    continue;
                }

                if (in_array($paymentCode, $blacklist))
                    continue;

                /** @var AbstractMethod|null $methodModel Actually it's wrong interface */
                $paymentModel = $this->_paymentMethodFactory->create($data['model']);
                $paymentModel->setId($paymentCode);
                $paymentModel->setStore(null);

                $paymenttype = $paymentModel->getPaymentMethod();
                if (isset($map[$paymentModel->getPaymentMethod()])) {
                    $paymenttype = $map[$paymentModel->getPaymentMethod()];
                }

                $paymenttypes[$paymenttype] = $paymentModel->getConfigData('sort_order');
            }
        }

        if (count($paymenttypes)) {
            asort($paymenttypes);
            $init->setPaymenttypeSortOrder(array_keys($paymenttypes));
        }
    }
}