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


use Magento\Framework\Component\ComponentRegistrarInterface;
use Mageplaza\ShareCart\Helper\Data;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Mpdf\Mpdf;
use Mageplaza\ShareCart\Block\Cart\Button;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area;
use Mageplaza\ShareCart\Model\Template\Processor;
use Magento\Email\Model\Template\Filter;
use Magento\Framework\Stdlib\DateTime\TimeZone;
use \Magento\Framework\Stdlib\DateTime\DateTime;

class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Processor
     */
    protected $templateProcessor;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var TimeZone
     */
    protected $timezone;

    /**
     * @var Button
     */
    protected $block;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * Download constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param Filesystem $fileSystem
     * @param Filter $filter
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Button $block
     * @param Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param State $state
     * @param Processor $templateProcessor
     * @param TimeZone $timezone
     * @param DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Filesystem $fileSystem,
        Filter $filter,
        ComponentRegistrarInterface $componentRegistrar,
        \Magento\Checkout\Model\Session $checkoutSession,
        Button $block,
        Data $helper,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        State $state,
        Processor $templateProcessor,
        TimeZone $timezone,
        DateTime $dateTime)
    {
        $this->filter = $filter;
        $this->timezone = $timezone;
        $this->state                = $state;
        $this->helper=$helper;
        $this->block=$block;
        $this->fileSystem           = $fileSystem;
        $this->componentRegistrar   = $componentRegistrar;
        $this->_pageFactory = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager  = $storeManager;
        $this->dateTime = $dateTime;
        $this->templateProcessor    = $templateProcessor;
        return parent::__construct($context);
    }

    public function execute()
    {
        try {
            $html = $this->readFile('app/code/Mageplaza/ShareCart/view/base/templates/template.html');
            $mpdf = new Mpdf();

            $storeId = $this->checkoutSession->getQuote()->getStoreId();
            $processor = $this->templateProcessor->setVariable(
                $this->addCustomTemplateVars($storeId)
            );
            $processor->setTemplateHtml($html);
            $processor->setStore($storeId);
            $html = $processor->processTemplate();
            $mpdf->WriteHTML($html);
            $mpdf->Output('cart.pdf','D');
        }catch (\Mpdf\MpdfException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $relativePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);
        return $rootDirectory->readFile($relativePath);
    }

    /**
     * @param $storeId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkStoreId($storeId)
    {
        if ($this->state->getAreaCode() == Area::AREA_FRONTEND) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function addCustomTemplateVars($storeId)
    {
        $templateVars['quote'] = $this->checkoutSession->getQuote();
        $templateVars['store']= $this->checkoutSession->getQuote()->getStore();
        $templateVars['vat_number'] = $this->helper->getVATNumber($storeId);
        $templateVars['phone'] = $this->helper->getPhone($storeId);
        $templateVars['contact'] = $this->helper->getEmail($storeId);
        $templateVars['registered'] = $this->helper->getRegisteredNumber($storeId);
        $templateVars['company']   =$this->helper->getCompanyName($storeId);
        $templateVars['address']  = $this->helper->getAddress($storeId);
        $templateVars['message']  = $this->helper->getWarningMessage($storeId);
        $templateVars['timezone']=  $this->formatDate($this->dateTime->gmtDate());
        return $templateVars;
    }

    /**
     * @param $date
     * @return string
     */
    public function formatDate($date)
    {
        $dateTime = $this->timezone->formatDateTime(
            new \DateTime($date),
            2,
            2,
            null,
            $this->timezone->getConfigTimezone('store', $this->storeManager->getStore())
        );

        try {
            $currentDate = (new \DateTime($dateTime));

            return $currentDate->format('Y-m-d H:i');
        } catch (\Exception $e) {
            return $dateTime;
        }
    }

}