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
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;

/**
 * Class Button
 * @package Mageplaza\ShareCart\Block\Cart
 */
class Button extends Template
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
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Quote
     */
    protected $_quote;

    /**
     * Button constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param Currency $currency
     * @param ProductRepository $productRepository
     * @param Configurable $configurable
     * @param UrlInterface $urlBuilder
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Currency $currency,
        ProductRepository $productRepository,
        Configurable $configurable,
        UrlInterface $urlBuilder,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    )
    {
        $this->_currency          = $currency;
        $this->checkoutSession    = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->configurable       = $configurable;
        $this->_urlBuilder        = $urlBuilder;
        $this->priceCurrency      = $priceCurrency;

        parent::__construct($context, $data);
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getItems()
    {
        return $this->checkoutSession->getQuote()->getItemsCollection();
    }

    /**
     * @param $item
     * @return array
     */
    public function checkConfigurableProduct($item)
    {
        return $this->configurable->getParentIdsByChild($item->getProductId());
    }

    /**
     * @param $item
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getNameConfigurable($item)
    {
        return $this->_productRepository->get($item->getSku())->getName();
    }

    /**
     * @param $price
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->priceCurrency->format($price, false);
    }

    /**
     * @return float
     */
    public function getBaseSubtotal()
    {
        return $this->formatPrice($this->checkoutSession->getQuote()->getBaseSubtotal());
    }

    /**
     * @codeCoverageIgnore
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount();
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->checkoutSession->getQuote();
        }

        return $this->_quote;
    }

    /**
     * @return string
     */
    public function getLinkDownload()
    {
        return $this->_urlBuilder->getUrl('sharecart/index/download');
    }
}