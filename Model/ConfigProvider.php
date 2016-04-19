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

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Wirecard\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var string[]
     */
    protected $methodCodes = [
        Payment\Select::CODE,
        Payment\Ccard::CODE,
        Payment\Ccardmoto::CODE,
        Payment\Maestro::CODE,
        Payment\Eps::CODE,
        Payment\Ideal::CODE,
        Payment\Giropay::CODE,
        Payment\Tatrapay::CODE,
        Payment\Skrilldirect::CODE,
        Payment\Skrillwallet::CODE,
        Payment\Mpass::CODE,
        Payment\Bmc::CODE,
        Payment\P24::CODE,
        Payment\Poli::CODE,
        Payment\Moneta::CODE,
        Payment\Ekonto::CODE,
        Payment\Trustly::CODE,
        Payment\Paybox::CODE,
        Payment\Paysafecard::CODE,
        Payment\Quick::CODE,
        Payment\Paypal::CODE,
        Payment\Epaybg::CODE,
        Payment\Sepa::CODE,
        Payment\Invoice::CODE,
        Payment\Invoiceb2b::CODE,
        Payment\Installment::CODE,
        Payment\Voucher::CODE,
        Payment\Trustpay::CODE,
        Payment\Sofortbanking::CODE,
    ];

    /**
     * @var \Wirecard\CheckoutPage\Model\AbstractPayment[]
     */
    protected $methods = [];

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @param \Wirecard\CheckoutPage\Helper\Data $helper
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     *
     */
    public function __construct(
        \Wirecard\CheckoutPage\Helper\Data $helper,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->_dataHelper   = $helper;
        $this->paymentHelper = $paymentHelper;
        $this->escaper       = $escaper;
        $this->assetRepo     = $assetRepo;

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $this->paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];

        /*
         * common config data
         */

        foreach ($this->methodCodes as $code) {
            $config['payment'][$code]['instructions'] = $this->getInstructions($code);
            $config['payment'][$code]['displaymode']  = $this->methods[$code]->getDisplayMode();
            $config['payment'][$code]['logo_url']     = $this->getLogoUrl($code);
            $config['payment'][$code]['back_url']     = $this->_dataHelper->getReturnUrl();
        }

        /*
         * EPS financial institutions
         */

        $fis = \WirecardCEE_QPay_PaymentType::getFinancialInstitutions(\WirecardCEE_QPay_PaymentType::EPS);

        $epsFinancialInstitutions = [];
        foreach ($fis as $k => $v) {
            $epsFinancialInstitutions[] = ['value' => $k, 'label' => html_entity_decode($v)];
        }
        array_unshift($epsFinancialInstitutions, ['value' => '', 'label' => $this->_dataHelper->__('Choose your bank...')]);

        $config['payment'][Payment\Eps::CODE]['financialinstitutions'] = $epsFinancialInstitutions;

        /*
         * IDEAL financial institutions
         */

        $fis = \WirecardCEE_QPay_PaymentType::getFinancialInstitutions(\WirecardCEE_QPay_PaymentType::IDL);

        $idealFinancialInstitutions = [];
        foreach ($fis as $k => $v) {
            $idealFinancialInstitutions[] = ['value' => $k, 'label' => htmlspecialchars_decode($v)];
        }
        array_unshift($idealFinancialInstitutions,
            ['value' => '', 'label' => $this->_dataHelper->__('Choose your bank...')]);

        $config['payment'][Payment\Ideal::CODE]['financialinstitutions'] = $idealFinancialInstitutions;

        /*
         * Invoice/installment
         */

        $config['payment'][Payment\Invoice::CODE]['provider']     = $this->methods[Payment\Invoice::CODE]->getProvider();
        $config['payment'][Payment\Invoiceb2b::CODE]['provider']  = $this->methods[Payment\Invoiceb2b::CODE]->getProvider();
        $config['payment'][Payment\Installment::CODE]['provider'] = $this->methods[Payment\Installment::CODE]->getProvider();

        $txt =
            $this->_dataHelper->__('I agree that the data which are necessary for the liquidation of purchase on account and which are used to complete the identy and credit check are transmitted to payolution. My %s can be revoked at any time with effect for the future.');

        $payolutionLink = $this->_dataHelper->getPayolutionLink($this->methods[Payment\Invoice::CODE]->getConfigData('payolution_mid'));
        if ($this->methods[Payment\Invoice::CODE]->getProvider() == 'payolution' && $this->methods[Payment\Invoice::CODE]->getConfigData('payolution_terms')) {
            $config['payment'][Payment\Invoice::CODE]['consenttxt']    = sprintf($txt, $payolutionLink);
            $config['payment'][Payment\Invoiceb2b::CODE]['consenttxt'] = sprintf($txt, $payolutionLink);
        }

        $config['payment'][Payment\Invoice::CODE]['min_age']     = (int) $this->methods[Payment\Invoice::CODE]->getConfigData('min_age');
        $config['payment'][Payment\Installment::CODE]['min_age'] = (int) $this->methods[Payment\Installment::CODE]->getConfigData('min_age');

        if ($this->methods[Payment\Invoice::CODE]->getProvider() == 'payolution') {
            $config['payment'][Payment\Invoice::CODE]['min_age'] = 18;
        }

        if ($this->methods[Payment\Installment::CODE]->getProvider() == 'payolution') {
            $config['payment'][Payment\Installment::CODE]['min_age'] = 18;
        }

        $payolutionLink = $this->_dataHelper->getPayolutionLink($this->methods[Payment\Installment::CODE]->getConfigData('payolution_mid'));
        if ($this->methods[Payment\Installment::CODE]->getProvider() == 'payolution' && $this->methods[Payment\Installment::CODE]->getConfigData('payolution_terms')) {
            $config['payment'][Payment\Installment::CODE]['consenttxt'] = sprintf($txt, $payolutionLink);
        }

        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     *
     * @return string
     */
    protected function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->_dataHelper->__($this->methods[$code]->getInstructions())));
    }

    protected function getLogoUrl($code)
    {
        //$params = array_merge(['_secure' => $this->getRequest()->isSecure()], $params);
        $logo = $this->methods[$code]->getLogo();
        if ($logo === false) {
            return false;
        }

        return $this->assetRepo->getUrlWithParams('Wirecard_CheckoutPage::images/' . $logo, ['_secure' => true]);
    }
}

