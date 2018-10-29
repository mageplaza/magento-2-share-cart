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
 * @package    Mageplaza_PdfInvoice
 * @copyright   Copyright (c) 2017-2018 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Magento\Checkout\Model\Session;
use Mageplaza\ShareCart\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimeZone;
/**
 * Class PrintProcess
 * @package Mageplaza\PdfInvoice\Helper
 */
class PrintProcess extends AbstractData
{
    const MAGEPLAZA_DIR = 'var/mageplaza';

    protected $dateTime;

    /**
     * @var DirectoryList
     */
    protected $directoryList;


    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;


    /**
     * @var $templateVars
     */
    protected $templateVars;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var TimeZone
     */
    protected $timezone;

    /**
     * PrintProcess constructor.
     * @param Context $context
     * @param Order $order
     * @param State $state
     * @param Filesystem $fileSystem
     * @param DirectoryList $directoryList
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        State $state,
        Filesystem $fileSystem,
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        Session $checkoutSession,
        Data $helper,
        DateTime $dateTime,
        TimeZone $timezone
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->state                = $state;
        $this->fileSystem           = $fileSystem;
        $this->directoryList        = $directoryList;
        $this->helper=$helper;
        $this->dateTime= $dateTime;
        $this->timezone = $timezone;
        parent::__construct($context, $objectManager, $storeManager);
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

        $basePath = '';
        for ($i = count($rootPathArr); $i < count($currentDirArr) - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }
        return $basePath . 'view/base/templates/';
    }
}
