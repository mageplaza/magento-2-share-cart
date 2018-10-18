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

namespace Mageplaza\ShareCart\Plugin\CustomerData;

use Mageplaza\Core\Helper\AbstractData;
use Magento\Customer\Model\Session;
/**
 * Class Cart
 * @package Mageplaza\RewardPoints\Plugin\CustomerData
 */
class Cart
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote|null
     */
    protected $quoteId = null;
    /**
     * @var \Mageplaza\Core\Helper\AbstractData
     */
    protected $helperData;
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(AbstractData $helperData, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->checkoutSession =$checkoutSession;
        $this->helperData = $helperData;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Add Url data to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        if (null === $this->quoteId) {
            $this->quoteId = $this->checkoutSession->getQuote()->getId();
        }
        $result['quote_url'] = $this->_urlBuilder->getUrl('sharecart/index/index',['quote_id'=> base64_encode($this->quoteId)]);
        return $result;
    }
}
