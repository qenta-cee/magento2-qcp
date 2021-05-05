<?php

namespace Qenta\CheckoutPage\Model\Plugin;

use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;


class FixSession
{
    /**
     * @var Header
     */
    protected $header;

    public function __construct(Header $header)
    {
        $this->header = $header;
    }

    public function beforeSetPublicCookie(
        PhpCookieManager $subject,
        $name,
        $value,
        PublicCookieMetadata $metadata = null
    ) {
        if ($metadata && method_exists($metadata, 'getSameSite') && ($name == 'PHPSESSID')) {
            if ($metadata->getSameSite() != 'None') {
                $metadata->setSecure(true);
                $metadata->setSameSite('None');
            }
        }
        return [$name, $value, $metadata];
    }
}
