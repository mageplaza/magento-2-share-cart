<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\ShareCart\Block\Cart;

use Magento\Customer\Model\Context;
use \Magento\Quote\Api\CartRepositoryInterface;
class Button extends \Magento\Framework\View\Element\Template
{
    /** @var $cartepository */
    protected $cartRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    protected $_storeManager;
    protected $_currency;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    protected $configurable;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CartRepositoryInterface $cartRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        array $data = [])
    {
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;
        $this->cartRepository = $cartRepository;
        $this->checkoutSession =$checkoutSession;
        $this->_productRepository = $productRepository;
        $this->configurable  =$configurable;
        parent::__construct($context, $data);
    }
    /**
     * Get store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->_storeManager->getStore()->getDefaultCurrencyCode();
    }

    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        return $this->_storeManager->getStore()->getAvailableCurrencyCodes($skipBaseNotAllowed);
    }

    /**
     * Get array of installed currencies for the scope
     *
     * @return array
     */
    public function getAllowedCurrencies()
    {
        return $this->_storeManager->getStore()->getAllowedCurrencies();
    }

    /**
     * Get current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyRate();
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



    public function getItems()
    {
        return $this->checkoutSession->getQuote()->getItemsCollection();
    }



    public function getItemName($item)
    {
        if($item->getHasChildren()) {
            $product = $this->_productRepository->get($item->getSku());
            return $product->getName();
        }else{
            return $item->getName();
        }

    }

    public function getParentProductType($item)
    {
        return $this->_productRepository->get($item->getSku())->getTypeId();
    }

    public function checkConfigurableProduct($item)
    {
        return $product = $this->configurable->getParentIdsByChild($item->getProductId());
        if(isset($product[0])){
            return $product[0];
        }
    }

    public function getNameConfigurable($item)
    {
        return $this->_productRepository->get($item->getSku())->getName();
    }

    public function getBaseSubtotal()
    {
        return $this->checkoutSession->getQuote()->getBaseSubtotal();
    }


}