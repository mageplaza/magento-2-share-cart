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

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\ShareCart\Api\ShareCartRepositoryInterface;
use Mageplaza\ShareCart\Helper\Data;
use Mpdf\MpdfException;

/**
 * Class Download
 * @package Mageplaza\ShareCart\Controller\Index
 */
class Download extends Action
{
    /**
     * @var ShareCartRepositoryInterface
     */
    protected $printProcess;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Download constructor.
     *
     * @param Context $context
     * @param ShareCartRepositoryInterface $printProcess
     * @param Session $checkoutSession
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        ShareCartRepositoryInterface $printProcess,
        Session $checkoutSession,
        Data $helper
    ) {
        $this->printProcess    = $printProcess;
        $this->checkoutSession = $checkoutSession;
        $this->helper            = $helper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws FileSystemException
     * @throws LocalizedException
     * @throws MpdfException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect   = $this->resultRedirectFactory->create();
        if (!$this->helper->isEnabled()) {
            $this->messageManager->addErrorMessage(__('The Share Cart extension is disable'));
            return $resultRedirect->setPath('checkout/cart');
        }
        $mpShareCartToken = $this->checkoutSession->getQuote()->getMpShareCartToken();

        $this->printProcess->downloadPdf($mpShareCartToken);
        return $resultRedirect->setPath('checkout/cart');
    }
}
