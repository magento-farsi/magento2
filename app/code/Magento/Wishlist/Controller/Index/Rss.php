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
namespace Magento\Wishlist\Controller\Index;

use Magento\Wishlist\Controller\IndexInterface;
use Magento\Framework\App\Action;

class Rss extends Action\Action implements IndexInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Wishlist\Helper\Rss
     */
    protected $rssWishlistHelper;

    /**
     * @var \Magento\Rss\Helper\DataFactory
     */
    protected $rssHelperFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Wishlist\Helper\Rss $rssWishlitHelper
     * @param \Magento\Rss\Helper\DataFactory $rssHelperFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Wishlist\Helper\Rss $rssWishlitHelper,
        \Magento\Rss\Helper\DataFactory $rssHelperFactory
    ) {
        $this->customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->scopeConfig = $scopeConfig;
        $this->rssWishlistHelper = $rssWishlitHelper;
        $this->rssHelperFactory = $rssHelperFactory;
        parent::__construct($context);
    }

    /**
     * Wishlist rss feed action
     * Show all public wishlists and private wishlists that belong to current user
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->rssWishlistHelper->isRssAllow()) {
            $this->_forward('noroute');
            return;
        }
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->wishlistProvider->getWishlist();
        if ($wishlist && ($wishlist->getVisibility()
            || $this->customerSession->authenticate($this)
            && $wishlist->getCustomerId() == $this->rssWishlistHelper->getCustomer()->getId())
        ) {
            $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
            $this->_view->loadLayout(false);
            $this->_view->renderLayout();
            return;
        }
        /** @var \Magento\Rss\Helper\Data $rssHelper */
        $rssHelper = $this->rssHelperFactory->create();
        $rssHelper->sendEmptyRssFeed($this->getResponse());
    }
}
