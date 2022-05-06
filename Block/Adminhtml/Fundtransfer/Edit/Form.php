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

namespace Qenta\CheckoutPage\Block\Adminhtml\Fundtransfer\Edit;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Qenta\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Qenta\CheckoutPage\Helper\Data $dataHelper
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array $data
     *
     * @internal param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Qenta\CheckoutPage\Helper\Data $dataHelper,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_dataHelper  = $dataHelper;
        $this->_localeLists = $localeLists;
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getUrl('*/*/submit', array('id' => $this->getRequest()->getParam('id'))),
                    'method' => 'post'
                ]
            ]
        );

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('form_form', array('legend' => $this->_dataHelper->__('Fund Transfer')));
        $fieldset->addField('transferType', 'select', [
            'name'     => 'transferType',
            'label'    => $this->_dataHelper->__('Fund transfer type'),
            'class'    => 'required-entry',
            'required' => true,
            'options'  => array(
                ''              => $this->_dataHelper->__('Please select the fund transfer type'),
                'existingorder' => $this->_dataHelper->__('Existing order'),
                'sepa-ct'       => 'SEPA-CT'
            ),
        ]);

        $fieldNoteFmt = sprintf('<a href="https://guides.wirecard.com/doku.php%%s" target="_blank" class="docref">%s</a>',
            $this->_dataHelper->__('See documentation'));

        $fieldset->addField('currency', 'select', [
            'name'     => 'currency',
            'label'    => $this->_dataHelper->__('Currency'),
            'class'    => 'required-entry',
            'required' => true,
            'values'   => $this->_localeLists->getOptionCurrencies(),
            'note'     => sprintf($fieldNoteFmt, '/request_parameters#currency')
        ]);

        $fieldset->addField('amount', 'text', array(
            'name'     => 'amount',
            'label'    => $this->_dataHelper->__('Amount'),
            'class'    => 'validate-greater-than-zero required-entry',
            'required' => true,
            'style'    => 'width: 100px',
            'note'     => sprintf($fieldNoteFmt, '/request_parameters#amount')
        ));

        $fieldset->addField('orderDescription', 'text', array(
            'name'     => 'orderDescription',
            'label'    => $this->_dataHelper->__('Order description'),
            'class'    => 'required-entry',
            'required' => true,
            'note'     => sprintf($fieldNoteFmt, '/request_parameters#orderdescription')
        ));

        $fieldset->addField('customerStatement', 'text', array(
            'name'  => 'customerStatement',
            'label' => $this->_dataHelper->__('Customer statement'),
            'note'  => sprintf($fieldNoteFmt, '/request_parameters#customerstatement')
        ));

        $fieldset->addField('creditNumber', 'text', array(
            'name'  => 'creditNumber',
            'label' => $this->_dataHelper->__('Credit number'),
            'class' => 'validate-greater-than-zero',
            'style' => 'width: 200px'
        ));

        $fieldset->addField('orderNumber', 'text', array(
            'name'  => 'orderNumber',
            'label' => $this->_dataHelper->__('Order number'),
            'class' => 'validate-greater-than-zero',
            'style' => 'width: 200px',
            'note'  => sprintf($fieldNoteFmt, '/request_parameters#ordernumber')
        ));

        $fieldset->addField('orderReference', 'text', array(
            'name'  => 'orderReference',
            'label' => $this->_dataHelper->__('Order reference'),
            'style' => 'width: 200px',
            'note'  => sprintf($fieldNoteFmt, '/request_parameters#orderreference')
        ));

        /* existing order fields */
        $fieldsetExistingOrder = $form->addFieldset('fields-existingorder',
            array(
                'legend' => $this->_dataHelper->__('Existing order data'),
                'class'  => 'transferfund-fieldset'
            ));
        $fieldsetExistingOrder->addField('sourceOrderNumber', 'text', array(
            'name'     => 'sourceOrderNumber',
            'label'    => $this->_dataHelper->__('Source order number'),
            'class'    => 'validate-greater-than-zero required-entry fundtransfer-required',
            'required' => true,
            'style'    => 'width: 200px',
            'note'     => sprintf($fieldNoteFmt, '/request_parameters#ordernumber')
        ));


        /* sepa-ct fields */
        $fieldsetExistingOrder = $form->addFieldset('fields-sepa-ct',
            array(
                'legend' => $this->_dataHelper->__('SEPA-CT data'),
                'class'  => 'transferfund-fieldset'
            ));
        $fieldsetExistingOrder->addField('bankAccountOwner', 'text', array(
            'name'     => 'bankAccountOwner',
            'label'    => $this->_dataHelper->__('Bank account owner'),
            'class'    => 'required-entry fundtransfer-required',
            'required' => true,
            'style'    => 'width: 400px',
            'note'     => sprintf($fieldNoteFmt, '/back-end_operations:functional_wcp_wcs:transaction-based_operations:transferfund#fund_transfer_typesepa-ct')
        ));

        $fieldsetExistingOrder->addField('bankBic', 'text', array(
            'name'     => 'bankBic',
            'label'    => $this->_dataHelper->__('BIC'),
            'class'    => 'required-entry fundtransfer-required',
            'required' => true,
            'style'    => 'width: 400px',
            'note'     => sprintf($fieldNoteFmt, '/back-end_operations:functional_wcp_wcs:transaction-based_operations:transferfund#fund_transfer_typesepa-ct')
        ));

        $fieldsetExistingOrder->addField('bankAccountIban', 'text', array(
            'name'     => 'bankAccountIban',
            'label'    => $this->_dataHelper->__('IBAN'),
            'class'    => 'required-entry fundtransfer-required',
            'required' => true,
            'style'    => 'width: 400px',
            'note'     => sprintf($fieldNoteFmt, '/back-end_operations:functional_wcp_wcs:transaction-based_operations:transferfund#fund_transfer_typesepa-ct')
        ));


        $fieldsetExistingOrder->addField('consumerEmail', 'text', array(
            'name'     => 'consumerEmail',
            'label'    => $this->_dataHelper->__('Consumer e-mail address'),
            'class'    => 'required-entry validate-email fundtransfer-required',
            'required' => true,
            'style'    => 'width: 400px',
            'note'     => sprintf($fieldNoteFmt, '/request_parameters#consumer_billing_data')
        ));

        $form->setValues(['currency' => 'EUR']);

        /** @var \Magento\Framework\DataObject $dataObject */
        $dataObject = $this->_backendSession->getQentaCheckoutPageFundTrandsferFormData();

        if (is_object($dataObject)) {
            $form->setValues($dataObject->getData());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_dataHelper->__('Fund Transfer');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_dataHelper->__('Fund Transfer');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
