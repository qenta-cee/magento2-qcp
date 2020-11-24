<?php


namespace Qenta\CheckoutPage\Controller;

/**
 * Abstract CsrfAwareAction class with CSRF support
 */
abstract class CsrfAwareAction extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    // @codingStandardsIgnoreLine
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return true;
    }

    // @codingStandardsIgnoreLine
    public function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }
}
