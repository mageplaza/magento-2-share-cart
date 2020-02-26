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

namespace Mageplaza\ShareCart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class QuoteSaveAfter
 * @package Mageplaza\ShareCart\Observer
 */
class QuoteSaveAfter implements ObserverInterface
{
    /**
     * @var Random
     */
    protected $randomDataGenerator;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * QuoteSaveAfter constructor.
     *
     * @param Random $randomDataGenerator
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(Random $randomDataGenerator, CartRepositoryInterface $cartRepository)
    {
        $this->randomDataGenerator = $randomDataGenerator;
        $this->cartRepository      = $cartRepository;
    }

    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        if (!$quote->getMpShareCartToken()) {
            $quote->setMpShareCartToken($this->randomDataGenerator->getUniqueHash());

            $this->cartRepository->save($quote);
        }
    }
}
