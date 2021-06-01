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

namespace Mageplaza\ShareCart\Block\Cart;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Directory\Model\Currency;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\ShareCart\Helper\Data;

/**
 * Class Items
 * @package Mageplaza\ShareCart\Block\Cart
 */
class Items extends Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Currency
     */
    protected $_currency;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Items constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param Currency $currency
     * @param ProductRepository $productRepository
     * @param Configurable $configurable
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Currency $currency,
        ProductRepository $productRepository,
        Configurable $configurable,
        Data $helper,
        array $data = []
    ) {
        $this->_currency          = $currency;
        $this->checkoutSession    = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->configurable       = $configurable;
        $this->helper             = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @param float $price
     *
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->helper->convertPrice($price, true, false);
    }

    /**
     * @param Quote|null $quote
     *
     * @return AbstractCollection|null
     */
    public function getItems($quote = null)
    {
        $quote = $this->getQuote($quote);

        return $quote ? $quote->getItemsCollection() : null;
    }

    /**
     * @param Quote|null $quote
     *
     * @return Quote|null
     */
    public function getQuote($quote = null)
    {
        try {
            $quote = $quote ?: $this->checkoutSession->getQuote();
        } catch (NoSuchEntityException $e) {
            return null;
        } catch (LocalizedException $e) {
            return null;
        }

        return $quote;
    }

    /**
     * @param Item $item
     *
     * @return array
     */
    public function checkConfigurableProduct($item)
    {
        return $this->configurable->getParentIdsByChild($item->getProductId());
    }

    /**
     * @param Item $item
     *
     * @return null|string
     */
    public function getNameConfigurable($item)
    {
        try {
            $product = $this->_productRepository->get($item->getSku());
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $product;
    }

    /**
     * @param Quote|null $quote
     *
     * @return float
     */
    public function getBaseGrandTotal($quote = null)
    {
        $quote = $this->getQuote($quote);

        return $quote ? $quote->getBaseGrandTotal() : null;
    }
}
