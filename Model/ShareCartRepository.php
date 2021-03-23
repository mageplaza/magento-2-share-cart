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
use Magento\Checkout\Model\Cart;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ShareCart\Api\ShareCartRepositoryInterface;
use Mageplaza\ShareCart\Helper\Data;
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
     * @var Data
     */
    protected $helper;

    /**
     * ShareCartRepository constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param Cart $cart
     * @param CartRepositoryInterface $cartRepository
     * @param PrintProcess $printProcess
     * @param Data $helper
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        Cart $cart,
        CartRepositoryInterface $cartRepository,
        PrintProcess $printProcess,
        Data $helper
    ) {
        $this->quoteFactory      = $quoteFactory;
        $this->storeManager      = $storeManager;
        $this->productRepository = $productRepository;
        $this->cart              = $cart;
        $this->cartRepository    = $cartRepository;
        $this->printProcess      = $printProcess;
        $this->helper            = $helper;
    }

    /**
     * {@inheritDoc}
     */
    public function share($mpShareCartToken)
    {
        /** @var Quote $quote */
        $quote = $this->getQuoteByShareCartToken($mpShareCartToken);
        $items = $quote->getItemsCollection();
        foreach ($items as $item) {
            if (!$item->getParentItemId()) {
                $storeId = $quote->getStoreId();
                try {
                    /**
                     * We need to reload product in this place, because products
                     * with the same id may have different sets of order attributes.
                     */
                    $product     = $this->productRepository->getById($item->getProductId(), false, $storeId, true);
                    $options     = $item->getProduct()->getTypeInstance(true)
                        ->getOrderOptions($item->getProduct());
                    $info        = $options['info_buyRequest'];
                    $productType = $item->getProductType();
                    $info['qty'] = $item->getQty();

                    if ($productType === 'configurable' || $productType === 'bundle') {
                        $this->cart->addProduct($product, $info);
                    } else {
                        $this->cart->addProduct($item->getProduct(), $info);
                    }
                } catch (NoSuchEntityException $e) {
                    throw new LocalizedException(__('Can not add product to cart'));
                }
            }
        }
        $this->cart->save();

        return $this->cartRepository->get($this->cart->getQuote()->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function downloadPdf($mpShareCartToken)
    {
        $quote = $this->getQuoteByShareCartToken($mpShareCartToken);
        $this->printProcess->downloadPdf($quote);
    }

    /**
     * @param string $mpShareCartToken
     *
     * @return Quote
     * @throws LocalizedException
     */
    protected function getQuoteByShareCartToken($mpShareCartToken)
    {
        if (!$this->helper->isEnabled()) {
            throw new LocalizedException(__('The Share Cart extension is disable'));
        }

        $quote = $this->quoteFactory->create()->load($mpShareCartToken, 'mp_share_cart_token');
        if (!$quote->getId()) {
            throw new LocalizedException(__('The Cart is not available'));
        }

        return $quote;
    }
}
