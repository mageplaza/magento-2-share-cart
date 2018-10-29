<?php

/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ShareCart
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Controller\Index;


use Magento\Framework\App\Filesystem\DirectoryList;
use Mpdf\Mpdf;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ShareCart\Model\Template\Processor;
use Mageplaza\ShareCart\Helper\PrintProcess;
use \Magento\Checkout\Model\Session;

class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Processor
     */
    protected $templateProcessor;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var PrintProcess
     */
    protected $printProcess;

    /**
     * Download constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Processor $templateProcessor
     * @param PrintProcess $printProcess
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Processor $templateProcessor,
        PrintProcess  $printProcess)
    {
        $this->checkoutSession = $checkoutSession;
        $this->storeManager  = $storeManager;
        $this->templateProcessor    = $templateProcessor;
        $this->printProcess = $printProcess;
        return parent::__construct($context);
    }

    public function execute()
    {
            $html = $this->printProcess->readFile($this->printProcess->getBaseTemplatePath().'template.html');
            $mpdf = new Mpdf();

            $storeId = $this->checkoutSession->getQuote()->getStoreId();
            $processor = $this->templateProcessor->setVariable(
                $this->printProcess->addCustomTemplateVars($storeId)
            );
            $processor->setTemplateHtml($html);
            $processor->setStore($storeId);
            $html = $processor->processTemplate();
            $mpdf->WriteHTML($html);
            $mpdf->Output('cart.pdf','D');
    }

}