<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Followupemail
 * @version    3.5.9
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Followupemail_Helper_Image extends Mage_Catalog_Helper_Image
{
    public function processImage()
    {
        // Process Image model
        $this->__toString();
    }

    public function getResizedImageFile()
    {
        return $this->_getModel()->getNewFile();
    }

    public function getFileContent()
    {
        return file_get_contents($this->getResizedImageFile());
    }

    public function getContentType()
    {
        $fileType = pathinfo($this->getResizedImageFile(), PATHINFO_EXTENSION);
        switch (strtolower($fileType)) {
            case 'gif':
                $contentType = 'image/gif';
                break;
            case 'jpg':
            case 'jpeg':
                $contentType = 'image/jpeg';
                break;
            case 'png':
                $contentType = 'image/png';
                break;
        }
        return '';
    }
}