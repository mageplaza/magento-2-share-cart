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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ShareCart\Api\Data;

/**
 * Interface ErrorResponseInterface
 * @package Mageplaza\ShareCart\Api\Data
 */
interface ErrorResponseInterface
{
    const MESSAGE = 'message';
    
    /**
     * @return string
     */
    public function getMessage();
    
    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
}
