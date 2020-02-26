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

namespace Mageplaza\ShareCart\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;

/**
 * Class UpgradeData
 * @package Mageplaza\ShareCart\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            /** @var QuoteSetup $quoteInstaller */
            $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            $quoteInstaller->addAttribute('quote', 'mp_share_cart_token', ['type' => 'text']);

        }

        $setup->endSetup();
    }
}
