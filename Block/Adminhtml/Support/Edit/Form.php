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

namespace Wirecard\CheckoutPage\Block\Adminhtml\Support\Edit;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Wirecard\CheckoutPage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Wirecard\CheckoutPage\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Wirecard\CheckoutPage\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_dataHelper = $dataHelper;
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
                'data' => [
                    'id'     => 'edit_form',
                    //'action' => $this->getData('action'),
                    'action' => $this->getUrl('*/*/sendrequest', array('id' => $this->getRequest()->getParam('id'))),
                    'method' => 'post'
                ]
            ]
        );

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('form_form', array('legend' => $this->_dataHelper->__('Contact Form')));
        $fieldset->addField('to', 'select', [
            'label'    => $this->_dataHelper->__('To'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'to',
            'options'  => array(
                'support.at@wirecard.com' => 'Support Team Wirecard CEE, Austria',
                'support@wirecard.com'    => 'Support Team Wirecard AG, Germany',
                'support.sg@wirecard.com' => 'Support Team Wirecard Singapore'
            )
        ]);

        $fieldset->addField('replyto', 'text', array(
            'label' => $this->_dataHelper->__('Your e-mail address'),
            'class' => 'validate-email',
            'name'  => 'replyto'
        ));

        $fieldset->addField('description', 'textarea', array(
            'label'    => $this->_dataHelper->__('Your message'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'description',
            'style'    => 'height:30em;width:50em'
        ));


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
        return $this->_dataHelper->__('Support Request');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_dataHelper->__('Support Request');
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
