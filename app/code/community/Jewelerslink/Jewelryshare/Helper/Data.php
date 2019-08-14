<?php

class Jewelerslink_Jewelryshare_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Checking if some required attributes missed
	 *
	 * @param array $attributes
	 * @return bool
	 */
	public function checkRequired($attributes)
	{
		return true;
		 
		$attributeConfig = Mage::getConfig()->getNode(Jewelerslink_Jewelryshare_Model_Import::XML_NODE_FIND_FEED_ATTRIBUTES);
		$attributeRequired = array();
		foreach ($attributeConfig->children() as $ac) {
			if ((int)$ac->required) {
				$attributeRequired[] = (string)$ac->label;
			}
		}
	
		//echo "<pre>"; print_r($attributeRequired); exit;
	
		foreach ($attributeRequired as $value) {
			if (!isset($attributes[$value])) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Product entity type
	 *
	 * @return int
	 */
	public function getProductEntityType()
	{
		return Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
	}
	
}