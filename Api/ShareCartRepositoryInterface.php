<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\ShareCart\Api;

/**
 * Interface ShareCartRepositoryInterface
 * @api
 */
interface ShareCartRepositoryInterface
{
    /**
     * Required($mpShareCartToken)
     *
     * @param string|null $mpShareCartToken
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function share($mpShareCartToken);

    /**
     * @param string $token
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Mpdf\MpdfException
     * @throws \Exception
     */
    public function downloadPdf($token);
}
