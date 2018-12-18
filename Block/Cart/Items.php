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
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

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
     * Items constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param Currency $currency
     * @param ProductRepository $productRepository
     * @param Configurable $configurable
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Currency $currency,
        ProductRepository $productRepository,
        Configurable $configurable,
        array $data = []
    )
    {
        $this->_currency          = $currency;
        $this->checkoutSession    = $checkoutSession;
        $this->_productRepository = $productRepository;
        $this->configurable       = $configurable;

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
     * @return float
     */
    public function getBaseSubtotal()
    {
        return $this->checkoutSession->getQuote()->getBaseSubtotal();
    }
}