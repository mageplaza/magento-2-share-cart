<?php

/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ShareCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Controller\Index;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ShareCart\Helper\Data;

/**
 * Class Index
 * @package Mageplaza\ShareCart\Controller\Index
 */
class Index extends Action
{
    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param CartRepositoryInterface $cartRepository
     * @param Cart $cart
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        CartRepositoryInterface $cartRepository,
        Cart $cart,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        Data $helper
    )
    {
        $this->cartRepository     = $cartRepository;
        $this->cart               = $cart;
        $this->_productRepository = $productRepository;
        $this->_storeManager      = $storeManager;
        $this->helper             = $helper;

        return parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->helper->isEnabled()) {
            $quoteId = base64_decode($this->getRequest()->getParam('key'), true);
            if ($quoteId) {
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
                            if ($product) {
                                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                                $info    = $options['info_buyRequest'];
                                if ($item->getProductType() == 'configurable' || $item->getProductType() == 'bundle') {
                                    $this->cart->addProduct($product, $info);
                                } else {
                                    $this->cart->addProduct($item->getProduct(), $item->getQty());
                                }
                            }
                        } catch (NoSuchEntityException $e) {
                            return $this;
                        }
                    }
                }
                $this->cart->save();
            }
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}