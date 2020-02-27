<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\ShareCart\Model;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ShareCart\Api\ShareCartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\Cart;
use Mageplaza\ShareCart\Helper\PrintProcess;

/**
 * Class ShareCartRepository
 * @package Mageplaza\StoreCredit\Model
 */
class ShareCartRepository implements ShareCartRepositoryInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var PrintProcess
     */
    protected $printProcess;

    /**
     * ShareCartRepository constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param Cart $cart
     * @param CartRepositoryInterface $cartRepository
     * @param PrintProcess $printProcess
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        Cart $cart,
        CartRepositoryInterface $cartRepository,
        PrintProcess $printProcess
    ) {
        $this->quoteFactory      = $quoteFactory;
        $this->storeManager      = $storeManager;
        $this->productRepository = $productRepository;
        $this->cart              = $cart;
        $this->cartRepository    = $cartRepository;
        $this->printProcess      = $printProcess;
    }

    /**
     * {@inheritDoc}
     */
    public function share($mpShareCartToken)
    {
        /** @var Quote $quote */
        if ($quote = $this->quoteFactory->create()->load($mpShareCartToken, 'mp_share_cart_token')) {
            $items = $quote->getItemsCollection();
            foreach ($items as $item) {
                if (!$item->getParentItemId()) {
                    $storeId = $quote->getStoreId();
                    try {
                        /**
                         * We need to reload product in this place, because products
                         * with the same id may have different sets of order attributes.
                         */
                        $product = $this->productRepository->getById($item->getProductId(), false, $storeId, true);
                        if ($product) {
                            $options     = $item->getProduct()->getTypeInstance(true)
                                ->getOrderOptions($item->getProduct());
                            $info        = $options['info_buyRequest'];
                            $productType = $item->getProductType();
                            if ($productType === 'configurable' || $productType === 'bundle') {
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

            return $this->cartRepository->get($this->cart->getQuote()->getId());
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function downloadPdf($mpShareCartToken)
    {
        $this->printProcess->downloadPdf($mpShareCartToken);
    }
}
