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
 * @category   Mageplaza
 * @package    Mageplaza_ShareCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Helper;

use Exception;
use Magento\Checkout\CustomerData\Cart as CustomerCart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimeZone;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ShareCart\Model\Template\Processor;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

/**
 * Class PrintProcess
 * @package Mageplaza\PdfInvoice\Helper
 */
class PrintProcess extends Data
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var $templateVars
     */
    protected $templateVars;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var TimeZone
     */
    protected $timezone;

    /**
     * @var Processor
     */
    protected $templateProcessor;


    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     * @param Filesystem $fileSystem
     * @param DirectoryList $directoryList
     * @param DateTime $dateTime
     * @param TimeZone $timezone
     * @param Processor $templateProcessor
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        Filesystem $fileSystem,
        DirectoryList $directoryList,
        DateTime $dateTime,
        TimeZone $timezone,
        Processor $templateProcessor
    ) {
        $this->fileSystem        = $fileSystem;
        $this->directoryList     = $directoryList;
        $this->dateTime          = $dateTime;
        $this->timezone          = $timezone;
        $this->templateProcessor = $templateProcessor;

        parent::__construct(
            $context,
            $objectManager,
            $storeManager,
            $priceCurrency,
        );
    }

    /**
     * @param $relativePath
     *
     * @return string
     * @throws FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFileName()
    {
        $name = $this->getConfigGeneral('file_name') ?: 'cart';
        if ($this->getConfigGeneral('timestamp')) {
            $name .= ' ' . $this->formatDate($this->dateTime->date('Y-m-d H.i'));
        }

        return $name . '.pdf';
    }

    /**
     * @param Quote $quote
     *
     * @return mixed
     * @throws Exception
     */
    public function addCustomTemplateVars($quote)
    {
        $storeId = $quote->getStoreId();

        $templateVars['quote']      = $quote;
        $templateVars['store']      = $quote->getStore();
        $templateVars['vat_number'] = $this->getVATNumber($storeId);
        $templateVars['phone']      = $this->getPhone($storeId);
        $templateVars['contact']    = $this->getEmail($storeId);
        $templateVars['registered'] = $this->getRegisteredNumber($storeId);
        $templateVars['company']    = $this->getCompanyName($storeId);
        $templateVars['address']    = $this->getAddress($storeId);
        $templateVars['message']    = $this->getWarningMessage($storeId);
        $templateVars['timezone']   = $this->formatDate($this->dateTime->gmtDate());

        return $templateVars;
    }

    /**
     * @param $date
     *
     * @return string
     * @throws Exception
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
        } catch (Exception $e) {
            return $dateTime;
        }
    }

    /**
     * Get base template path
     * @return string
     */
    public function getBaseTemplatePath()
    {
        // Get directory of Data.php
        $currentDir = __DIR__;

        // Get root directory(path of magento's project folder)
        $rootPath = $this->directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) == 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) == 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath           = '';
        $rootPathArrCount   = count($rootPathArr);
        $currentDirArrCount = count($currentDirArr);
        for ($i = $rootPathArrCount; $i < $currentDirArrCount - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        return $basePath . 'view/base/templates/';
    }

    /**
     * @param Quote $quote
     *
     * @throws FileSystemException
     * @throws MpdfException
     * @throws Exception
     */
    public function downloadPdf($quote)
    {
        $html = $this->readFile($this->getBaseTemplatePath() . 'template.html');
        $mpdf = new Mpdf(['tempDir' => BP . '/var/tmp']);

        $processor = $this->templateProcessor->setVariable(
            $this->addCustomTemplateVars($quote)
        );
        $processor->setTemplateHtml($html);
        $processor->setStore($quote->getStoreId());
        $html = $processor->processTemplate();
        $mpdf->WriteHTML($html);
        $mpdf->Output($this->getFileName(), 'D');
    }
}
