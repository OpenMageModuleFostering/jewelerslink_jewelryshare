<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TheJewelerslink jewelryshare codes (attribute map) collection
 *
 * @category    Jewelerslink
 * @package     Jewelerslink_Jewelryshare
 */
class Jewelerslink_Jewelryshare_Model_Resource_Codes_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Local constructor
     *
     */
    protected function _construct()
    {
        $this->_init('jewelerslink_jewelryshare/codes');
    }

    /**
     * Fetch attributes to import
     *
     * @return array
     */
    public function getImportAttributes()
    {
        $this->addFieldToFilter('jewelry_imported', array('eq' => '1'));
        return $this->_toOptionHash('import_code', 'eav_code');
    }

}
