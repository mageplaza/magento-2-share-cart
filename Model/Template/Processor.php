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

namespace Mageplaza\ShareCart\Model\Template;

use Magento\Email\Model\Template;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;

/**
 * Class Processor
 * @package Mageplaza\PdfInvoice\Model\Template
 */
class Processor extends Template
{
    /**
     * @var $storeId
     */
    private $storeId;

    /**
     * @var $designConfig
     */
    private $designConfig;

    /**
     * @var $templateHtml
     */
    private $templateHtml;

    /**
     * @var $variable
     */
    public $variable;

    /**
     * Set template html
     *
     * @param $html
     */
    public function setTemplateHtml($html)
    {
        $this->templateHtml = $html;
    }

    /**
     * Set store
     *
     * @param $storeId
     */
    public function setStore($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * Set variable
     *
     * @param $data
     *
     * @return $this
     */
    public function setVariable($data)
    {
        $this->variable = $data;

        return $this;
    }

    /**
     * Get template html
     * @return mixed
     */
    public function getTemplateHtml()
    {
        return $this->templateHtml;
    }

    /**
     * Process template
     * @return string
     */
    public function processTemplate()
    {
        $store           = $this->variable['store'];
        $this->storeId   = $store->getId();
        $isDesignApplied = $this->applyDesignConfig();

        $processor = $this->getTemplateFilter()
            ->setPlainTemplateMode($this->isPlain())
            ->setIsChildTemplate($this->isChildTemplate())
            ->setTemplateProcessor([$this, 'getTemplateContent']);

        $storeId = $store->getId();
        $processor->setDesignParams($this->getDesignParams());
        $variables         = $this->addEmailVariables($this->variable, $storeId);
        $variables['this'] = $this;
        $processor->setVariables($variables);
        $this->setUseAbsoluteLinks(true);
        $html = $processor->setStoreId($storeId)
            ->setDesignParams([0])
            ->filter((string)__($this->getTemplateHtml()));

        if ($isDesignApplied) {
            $this->cancelDesignConfig();
        }

        return $html;
    }

    /**
     * Get design params
     * @return array
     */
    public function getDesignParams()
    {
        return [
            'area'       => $this->getDesignConfig()->getArea(),
            'theme'      => $this->design->getDesignTheme()->getCode(),
            'themeModel' => $this->design->getDesignTheme(),
            'locale'     => $this->design->getLocale(),
        ];
    }

    /**
     * Get design configuration data
     *
     * @return DataObject
     */
    public function getDesignConfig()
    {
        if ($this->designConfig === null) {
            $this->designConfig = new DataObject(
                ['area' => Area::AREA_FRONTEND, 'store' => $this->storeId]
            );
        }

        return $this->designConfig;
    }
}
