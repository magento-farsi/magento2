<?php
/**
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Persistent\Controller\Index;

class SaveMethod extends \Magento\Persistent\Controller\Index
{
    /**
     * Save onepage checkout method to be register
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getHelper()->isPersistent()) {
            $this->_getHelper()->getSession()->removePersistentCookie();
            if (!$this->_customerSession->isLoggedIn()) {
                $this->_customerSession->setCustomerId(null)->setCustomerGroupId(null);
            }

            $this->_persistentObserver->setQuoteGuest();
        }

        $checkoutUrl = $this->_redirect->getRefererUrl();
        $this->getResponse()->setRedirect($checkoutUrl . (strpos($checkoutUrl, '?') ? '&' : '?') . 'register');
    }
}
