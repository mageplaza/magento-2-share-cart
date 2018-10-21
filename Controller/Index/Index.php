<?php

/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ShareCart
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Controller\Index;
use \Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Mageplaza\ShareCart\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /** @var $cartepository */
    protected $cartRepository;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /** @var \Magento\Store\Model\StoreManagerInterface  */
    protected $_storeManager;
    protected $helper;
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param CartRepositoryInterface $cartRepository
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        CartRepositoryInterface $cartRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $helper)
    {
        $this->_pageFactory = $pageFactory;
        $this->cartRepository = $cartRepository;
        $this->cart = $cart;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->helper=$helper;
        return parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if($this->helper->isEnabled()) {
            $quoteId = base64_decode($this->getRequest()->getParam('quote_id'), true);
            $quote = $this->cartRepository->get($quoteId);


            $items = $quote->getItemsCollection();
            foreach ($items as $item) {
                if (!$item->getParentItemId()) {
                    $storeId = $this->_storeManager->getStore()->getId();
                    try {
                        /**
                         * We need to reload product in this place, because products
                         * with the same id may have different sets of order attributes.
                         */
                        $product = $this->_productRepository->getById($item->getProductId(), false, $storeId, true);
                        $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                        $info = $options['info_buyRequest'];
                        if ($item->getProductType() == 'configurable' || $item->getProductType() == 'bundle') {
                            $this->cart->addProduct($product, $info);
                        } else {
                            $this->cart->addProduct($item->getProduct(), $item->getQty());
                        }
                    } catch (NoSuchEntityException $e) {
                        return $this;
                    }
                }

            }
            $this->cart->save();
        }
        return $resultRedirect->setPath('checkout/cart');
    }

}