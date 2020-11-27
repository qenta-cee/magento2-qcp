<?php

namespace Qenta\CheckoutPage\Controller;

/**
 * Conditional inclusion of the proper CsrfAwareAction class based on the existence of the CsrfAwareActionInterface
 * interface and the PHP version
 *
 * Magento versions 2.3 and above use the CsrfAwareActionWithCsrfSupport.php while earlier Magento versions use the
 * CsrfAwareActionWithoutCsrfSupport.php file
 */
if (interface_exists('\Magento\Framework\App\CsrfAwareActionInterface') && version_compare(phpversion(), '7.1', '>=')) {
    // @codingStandardsIgnoreLine
    require_once __DIR__ . '/CsrfAwareActionWithCsrfSupport.php';
} else {
    // @codingStandardsIgnoreLine
    require_once __DIR__ . '/CsrfAwareActionWithoutCsrfSupport.php';
}
