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

namespace Mageplaza\ShareCart\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Checkout\CustomerData\Cart as CustomerCart;

/**
 * Class Data
 * @package Mageplaza\ShareCart\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH   = 'sharecart';
    const BUSINESS_CONFIG_PATH = 'business_information';

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @var CustomerCart
     */
    protected $customerCart;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     * @param CustomerCart $customerCart
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        CustomerCart $customerCart
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->scopeConfig   = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->customerCart  = $customerCart;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getCompanyName($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/company', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getAddress($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/address', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getVATNumber($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/vat_number', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getPhone($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/phone', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getEmail($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/contact', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getRegisteredNumber($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/registered', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getWarningMessage($storeId = null)
    {
        return $this->getModuleConfig(self::BUSINESS_CONFIG_PATH . '/message', $storeId);
    }

    /**
     * @param float $amount
     * @param bool $format
     * @param bool $includeContainer
     * @param null $scope
     *
     * @return float|string
     */
    public function convertPrice($amount, $format = true, $includeContainer = true, $scope = null)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $amount,
                $includeContainer,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $scope
            )
            : $this->priceCurrency->convert($amount, $scope);
    }

    public function isHyvaAvailable()
    {
        if ($this->isEnabled()) {
            $themeId = $this->scopeConfig->getValue(
                'design/theme/theme_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $theme = $this->themeProvider->getThemeById($themeId);

            if ($theme && str_contains($theme->getCode(), 'Hyva')) {
                return true;
            }
        }

        return false;
    }

    public function getShareCartUrl()
    {
        return $this->customerCart->getSectionData();
    }
}
