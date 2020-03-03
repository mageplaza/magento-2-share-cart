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

namespace Mageplaza\ShareCart\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ShareCart\Helper\Data;
use Mageplaza\ShareCart\Api\ShareCartRepositoryInterface;

/**
 * Class Index
 * @package Mageplaza\ShareCart\Controller\Index
 */
class Index extends Action
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var ShareCartRepositoryInterface
     */
    private $shareCartRepository;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param ShareCartRepositoryInterface $shareCartRepository
     */
    public function __construct(
        Context $context,
        Data $helper,
        ShareCartRepositoryInterface $shareCartRepository
    ) {
        $this->helper              = $helper;
        $this->shareCartRepository = $shareCartRepository;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->helper->isEnabled()) {
            $mpShareCartToken = $this->getRequest()->getParam('key');
            try {
                $this->shareCartRepository->share($mpShareCartToken);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
