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
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\ShareCart\Helper\Data;

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
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param Currency $currency
     * @param ProductRepository $productRepository
     * @param Configurable $configurable
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Currency $currency,
        ProductRepository $productRepository,
        Configurable $configurable,
        PriceCurrencyInterface $priceCurrency,
        Data $helper,
        array $data = []
    ) {
        $this->_currency          = $currency;
        $this->checkoutSession    = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->configurable       = $configurable;
        $this->priceCurrency      = $priceCurrency;
        $this->helper             = $helper;

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
     * @param Quote|null $quote
     *
     * @return AbstractCollection
     */
    public function getItems($quote = null)
    {
        try {
            $quote = $quote ?: $this->checkoutSession->getQuote();
        } catch (NoSuchEntityException $e) {
            return null;
        } catch (LocalizedException $e) {
            return null;
        }

        return $quote->getItemsCollection();
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
            if ($product = $this->_productRepository->get($item->getSku())) {
                return $product->getName();
            }
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return null;
    }

    /**
     * @param float $price
     *
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->priceCurrency->format($price, false);
    }

    /**
     * @param null $quote
     *
     * @return float|null
     */
    public function getBaseSubtotal($quote = null)
    {
        $quote = $this->getQuote($quote);

        return $quote ? $this->formatPrice($quote->getBaseSubtotal()) : null;
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
     * @return int|mixed|null
     */
    public function getItemsCount()
    {
        $quote = $this->getQuote();

        return $quote ? $quote->getItemsCount() : null;
    }

    /**
     * @return string
     */
    public function getLinkDownload()
    {
        return $this->_urlBuilder->getUrl('sharecart/index/download');
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->helper->isEnabled();
    }
}
