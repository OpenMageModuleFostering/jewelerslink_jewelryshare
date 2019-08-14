<?php

class Jewelerslink_Jewelryshare_Adminhtml_JewelryshareController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('jewelryshare/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('jewelryshare/jewelryshare')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('jewelryshare_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('jewelryshare/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('jewelryshare/adminhtml_jewelryshare_edit'))
				->_addLeft($this->getLayout()->createBlock('jewelryshare/adminhtml_jewelryshare_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jewelryshare')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	
	public function importFormAction() {
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('jewelryshare_data', $model);

		$this->loadLayout();
		$this->_setActiveMenu('jewelryshare/items');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('jewelryshare/adminhtml_jewelryshare_edit'))
		->_addLeft($this->getLayout()->createBlock('jewelryshare/adminhtml_jewelryshare_edit_tabs'));

		$this->renderLayout();
		
	}
	
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		try
		{
			$resource = Mage::getConfig()->getNode('global/resources')->asArray();
			$magento_db = $resource['default_setup']['connection']['host'];
			$mdb_user = $resource['default_setup']['connection']['username'];
			$mdb_passwd = $resource['default_setup']['connection']['password'];
			$mdb_name = $resource['default_setup']['connection']['dbname'];
			$magento_connection = @mysql_connect($magento_db, $mdb_user, $mdb_passwd);
		
			if (!$magento_connection)
			{
				die('Unable to connect to the database');
			}
			@mysql_select_db($mdb_name, $magento_connection) or die ("Database not found.");
		
			$priceTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_priceincrease');
			mysql_query("TRUNCATE TABLE $priceTable");
		
			for($i = 1; $i<1000; $i++)
			{
				if(isset($_REQUEST['multiline_0-'.$i]) && ($_REQUEST['multiline_0-'.$i] != '')) {
					$price_from_0 = $_REQUEST['multiline_0-'.$i];
					$price_to_1 = $_REQUEST['multiline_1-'.$i];
					$price_increase_percent = $_REQUEST['multiline_2-'.$i];
					$price_increase_2 = $_REQUEST['multiline_2-'.$i]/100;
					$price_to_increase_3 = 	1 + $price_increase_2;
						
					$query_insert_1 = "INSERT INTO $priceTable SET price_from = ".$price_from_0.", price_to = ".$price_to_1.", price_increase = ".$price_increase_percent;
					mysql_query($query_insert_1) or die(mysql_error());
				}
			}
				
			$vendorTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_vendor');
			mysql_query("TRUNCATE TABLE $vendorTable");
				
			for($j = 0; $j<1000; $j++)
			{
				if(isset($_REQUEST['vendor_1-'.$j]) && ($_REQUEST['vendor_1-'.$j] != '')) {
					$vendor_name = $_REQUEST['vendor_1-'.$j];
					$vendor_id = $_REQUEST['vendor_2-'.$j];
					$query_insert = "INSERT INTO $vendorTable SET vendor_name = '".$vendor_name."', vendor_id = '".$vendor_id."'";
					mysql_query($query_insert) or die(mysql_error());
				}
			}
		
			Mage::getSingleton("adminhtml/session")->addSuccess("Rules Saved.");
			$this->_redirect("*/*/importForm");
			return;
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
			
	}
	
	public function deleteDir($dirPath) {
	
		if (! is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
	
	public function getJwCustomerId() {
		try
		{
			$username = Mage::getStoreConfig('jewelryshare/user_detail/ideal_username');
			$password = Mage::getStoreConfig('jewelryshare/user_detail/ideal_password');
	
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,"http://www.jewelerslink.com/jewelry/index/getjwId");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("username"=>$username,"password"=>$password));
			$data = curl_exec($ch);
			curl_close($ch);
			//echo $data;
	
			if($data == "Invalid Login") {
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Unauthenticate Login, Go to ( System > Configuration > Jewelry Config ) and enter Jewelerslink Login Detail"));
				$this->_redirect("*/*/importForm");
				return;
	
			} else {
				//echo $data; exit;
				return $data;
			}
	
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
	
	/**
	 * List import codes (attribute map) model
	 *
	 * @return mixed
	 */
	protected function _getImportAttributes()
	{
		$attributes = Mage::getResourceModel('jewelerslink_jewelryshare/codes_collection')->getImportAttributes();
		return $attributes;
	}
	
	public function getImportCSVAction() {
		try
		{
			$resource = Mage::getConfig()->getNode('global/resources')->asArray();
			$magento_db = $resource['default_setup']['connection']['host'];
			$mdb_user = $resource['default_setup']['connection']['username'];
			$mdb_passwd = $resource['default_setup']['connection']['password'];
			$mdb_name = $resource['default_setup']['connection']['dbname'];
			$magento_connection = @mysql_connect($magento_db, $mdb_user, $mdb_passwd);
				
			if (!$magento_connection)
			{
				die('Unable to connect to the database');
			}
			@mysql_select_db($mdb_name, $magento_connection) or die ("Database not found.");
				
			$vendorTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_vendor');
			$select_vendor = 'select * from `'.$vendorTable.'`';
			$result = mysql_query($select_vendor);
			while($row = mysql_fetch_array($result))
			{
				$vendorArray[] = $row['vendor_name'];
			}
				
			$attributes = $this->_getImportAttributes();
			$mapped_attributes = array_keys($attributes);
			//echo "<pre>";print_r($mapped_attributes); exit;
			$mapped_string = json_encode($mapped_attributes);
				
			$username = Mage::getStoreConfig('jewelryshare/user_detail/ideal_username');
			$password = Mage::getStoreConfig('jewelryshare/user_detail/ideal_password');
				
			$data_string = json_encode($vendorArray);
	
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,"http://www.jewelerslink.com/jewelry/index/getjson");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("username"=>$username,"password"=>$password,"vendors"=>$data_string,"attributes"=>$mapped_string));
			$data = curl_exec($ch);
			curl_close($ch);
			//echo $data; exit;
				
			if($data == "Invalid Login") {
	
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Unauthenticate Login, Go to ( System > Configuration > Jewelry Config ) and enter Jewelerslink Login Detail"));
				$this->_redirect("*/*/importForm");
				return;
	
			} else {
				//echo $data;
	
				$existingProducts = Mage::getModel('catalog/product')->getCollection();
				$existingProducts->addAttributeToSelect('sku');
	
				$existSkus = array();
				foreach($existingProducts as $exists) {
					$existSkus[] = $exists->getSku();
				}
				//echo "<pre>"; print_r($existSkus); exit;
				$jsonData = json_decode($data, true);
	
				$mappedHeader =  array();
				foreach($jsonData[0] as $key => $header){
						
					if($header == 'sku') {
						$skuKey = $key;
					}
					if(isset($attributes[$header]) && $attributes[$header] != "") {
						$mappedHeader[] = $attributes[$header];
					} else {
						$mappedHeader[] = $header;
					}
				}
				$jsonData[0] = $mappedHeader;
				//echo "<pre>"; print_r($jsonData);exit;
	
				$csvData = array();
				foreach($jsonData as $csvRow) {
					if(!in_array($csvRow[$skuKey], $existSkus)) {
						$csvData[] = $csvRow;
					}
				}
	
				//echo "<pre>"; print_r($csvData);exit;
	
				$priceTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_priceincrease');
				$query = "SELECT * FROM $priceTable";
				$result= mysql_query($query);
				while($row = mysql_fetch_array($result)) {
					$price_from[] = $row['price_from'];
					$price_to[] = $row['price_to'];
					$price_increase_per = $row['price_increase']/100 ;
					$price_increase_final[] = 1 + $price_increase_per ;
				}
	
	
				// Apply Price Increase before saving csv because we are not storing jewelry to database like diamonds.
				$row=0;
				$csvRowCnt = 1;
				foreach($csvData as $column) {
	
					if($row==0){
	
						foreach($column as $key => $field) {
							//$priceFields = array('price','special_price','tier_price','msrp','g14_price','g18_price','plat_price','pall_price');
							if($field == 'price') {
								$priceKey = $key;
							}
							if($field == 'special_price') {
								$special_priceKey = $key;
							}
							if($field == 'tier_price') {
								$tier_priceKey = $key;
							}
							if($field == 'msrp') {
								$msrpKey = $key;
							}
							if($field == 'g14_price') {
								$g14_priceKey = $key;
							}
							if($field == 'g18_price') {
								$g18_priceKey = $key;
							}
							if($field == 'plat_price') {
								$plat_priceKey = $key;
							}
							if($field == 'pall_price') {
								$pall_priceKey = $key;
							}
						}
	
						$row++;
						continue;
					}
						
					if(isset($priceKey) && $priceKey != "") {
						$price = $column[$priceKey];
						if($price == 0) $price = "";
						$csvData[$csvRowCnt][$priceKey] = $price;
					}
						
					if(isset($special_priceKey) && $special_priceKey != "") {
						$special_price = $column[$special_priceKey];
						if($special_price == 0) $special_price = "";
						$csvData[$csvRowCnt][$special_priceKey] = $special_price;
					}
						
					if(isset($tier_priceKey) && $tier_priceKey != "") {
						$tier_price = $column[$tier_priceKey];
						if($tier_price == 0) $tier_price = "";
						$csvData[$csvRowCnt][$tier_priceKey] = $tier_price;
					}
						
					if(isset($msrpKey) && $msrpKey != "") {
						$msrp = $column[$msrpKey];
						if($msrp == 0) $msrp = "";
						$csvData[$csvRowCnt][$msrpKey] = $msrp;
					}
						
					if(isset($g14_priceKey) && $g14_priceKey != "") {
						$g14_price = $column[$g14_priceKey];
						if($g14_price == 0) $g14_price = "";
						$csvData[$csvRowCnt][$g14_priceKey] = $g14_price;
					}
						
					if(isset($g18_priceKey) && $g18_priceKey != "") {
						$g18_price = $column[$g18_priceKey];
						if($g18_price == 0) $g18_price = "";
						$csvData[$csvRowCnt][$g18_priceKey] = $g18_price;
					}
						
					if(isset($plat_priceKey) && $plat_priceKey != "") {
						$plat_price = $column[$plat_priceKey];
						if($plat_price == 0) $plat_price = "";
						$csvData[$csvRowCnt][$plat_priceKey] = $plat_price;
					}
						
					if(isset($pall_priceKey) && $pall_priceKey != "") {
						$pall_price = $column[$pall_priceKey];
						if($pall_price == 0) $pall_price = "";
						$csvData[$csvRowCnt][$pall_priceKey] = $pall_price;
					}
						
					for($i=0; $i < count($price_increase_final); $i++) {
	
						if($price_increase_final[$i] != '') {
								
							if(isset($priceKey) && $priceKey != "") {
								if(($price >= $price_from[$i]) && ($price <= $price_to[$i]) && ($price != 0) && ($price != '')) {
									$incPrice = $price*$price_increase_final[$i];
									//echo $incPrice."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$priceKey] = $incPrice;
								}
							}
								
							if(isset($special_priceKey) && $special_priceKey != "") {
								if(($special_price >= $price_from[$i]) && ($special_price <= $price_to[$i]) && ($special_price != 0) && ($special_price != '')) {
	
									$incSpPrice = $special_price*$price_increase_final[$i];
									//echo $incSpPrice."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$special_priceKey] = $incSpPrice;
								}
							}
								
							if(isset($tier_priceKey) && $tier_priceKey != "") {
								if(($tier_price >= $price_from[$i]) && ($tier_price <= $price_to[$i]) && ($tier_price != 0) && ($tier_price != '')) {
	
									$incTrPrice = $tier_price*$price_increase_final[$i];
									//echo $incTrPrice."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$tier_priceKey] = $incTrPrice;
								}
							}
								
							if(isset($msrpKey) && $msrpKey != "") {
								if(($msrp >= $price_from[$i]) && ($msrp <= $price_to[$i]) && ($msrp != 0) && ($msrp != '')) {
	
									$incMSPrice = $msrp*$price_increase_final[$i];
									//echo $incMSPrice."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$msrpKey] = $incMSPrice;
								}
							}
								
							if(isset($g14_priceKey) && $g14_priceKey != "") {
								if(($g14_price >= $price_from[$i]) && ($g14_price <= $price_to[$i]) && ($g14_price != 0) && ($g14_price != '')) {
	
									$incG14Price = $g14_price*$price_increase_final[$i];
									//echo $incG14Price."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$g14_priceKey] = $incG14Price;
								}
							}
								
							if(isset($g18_priceKey) && $g18_priceKey != "") {
								if(($g18_price >= $price_from[$i]) && ($g18_price <= $price_to[$i]) && ($g18_price != 0) && ($g18_price != '')) {
	
									$incG18Price = $g18_price*$price_increase_final[$i];
									//echo $incG18Price."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$g18_priceKey] = $incG18Price;
								}
							}
								
							if(isset($plat_priceKey) && $plat_priceKey != "") {
								if(($plat_price >= $price_from[$i]) && ($plat_price <= $price_to[$i]) && ($plat_price != 0) && ($plat_price != '')) {
	
									$incPtPrice = $plat_price*$price_increase_final[$i];
									//echo $incPtPrice."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$plat_priceKey] = $incPtPrice;
								}
							}
								
							if(isset($pall_priceKey) && $pall_priceKey != "") {
								if(($pall_price >= $price_from[$i]) && ($pall_price <= $price_to[$i]) && ($pall_price != 0) && ($pall_price != '')) {
	
									$incPlPrice = $pall_price*$price_increase_final[$i];
									//echo $incPlPrice."==".$csvRowCnt."<br>";
									$csvData[$csvRowCnt][$pall_priceKey] = $incPlPrice;
								}
							}
								
						}
					}
						
					$csvRowCnt++;
				}
				//echo "<pre>"; print_r($csvData);exit;
	
				$path = Mage::getBaseDir("var") . DS ."import" . DS;
				$fp = fopen($path."jewelerslink_import.csv", "w") or die("can't open file");
				foreach ($csvData as $fields) {
					fputcsv($fp, $fields);
				}
				fclose($fp);
	
				//$this->getImagesAction();
	
				Mage::getSingleton("adminhtml/session")->addSuccess(count($csvData)." New Products CSV Created from Jewelerslink Inventory.");
				$this->_redirect("*/*/importForm");
			}
				
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
	
	public function getUpdateCSVAction() {
		try
		{
			$resource = Mage::getConfig()->getNode('global/resources')->asArray();
			$magento_db = $resource['default_setup']['connection']['host'];
			$mdb_user = $resource['default_setup']['connection']['username'];
			$mdb_passwd = $resource['default_setup']['connection']['password'];
			$mdb_name = $resource['default_setup']['connection']['dbname'];
			$magento_connection = @mysql_connect($magento_db, $mdb_user, $mdb_passwd);
	
			if (!$magento_connection)
			{
				die('Unable to connect to the database');
			}
			@mysql_select_db($mdb_name, $magento_connection) or die ("Database not found.");
	
			$vendorTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_vendor');
			$select_vendor = 'select * from `'.$vendorTable.'`';
			$result = mysql_query($select_vendor);
			while($row = mysql_fetch_array($result))
			{
				$vendorArray[] = $row['vendor_name'];
			}
			$username = Mage::getStoreConfig('jewelryshare/user_detail/ideal_username');
			$password = Mage::getStoreConfig('jewelryshare/user_detail/ideal_password');
	
			$data_string = json_encode($vendorArray);
	
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,"http://www.jewelerslink.com/jewelry/index/getUpdateJson");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("username"=>$username,"password"=>$password,"vendors"=>$data_string));
			$data = curl_exec($ch);
			curl_close($ch);
			//echo $data; exit;
	
			if($data == "Invalid Login") {
	
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Unauthenticate Login, Go to ( System > Configuration > Jewelry Config ) and enter Jewelerslink Login Detail"));
				$this->_redirect("*/*/importForm");
				return;
	
			} else {
				//echo $data;
	
				$existingProducts = Mage::getModel('catalog/product')->getCollection();
				$existingProducts->addAttributeToSelect('sku');
	
				$existSkus = array();
				foreach($existingProducts as $exists) {
					$existSkus[] = $exists->getSku();
				}
				//echo "<pre>"; print_r($existSkus); exit;
				$jsonData = json_decode($data, true);
				//echo "<pre>"; print_r($jsonData);exit;
	
				$csvData = array();
				$rowCnt = 0;
				foreach($jsonData as $csvRow) {
					if($rowCnt==0) {
						$csvData[] = $csvRow;
					} else {
						if(in_array($csvRow[2], $existSkus)) {
							$csvData[] = $csvRow;
						}
					}
					$rowCnt++;
				}
	
				//echo "<pre>"; print_r($csvData);exit;
	
				$attributes = Mage::getResourceModel('jewelerslink_jewelryshare/codes_collection')->getImportAttributes();
				$attributesKey = array_keys($attributes);
				$attributesValue = array_values($attributes);
				//echo "<pre>"; print_r($attributesKey); print_r($attributesValue);print_r($csvData[0]);
	
				// Below Loop == to map header with attribute & update csv title $csvData[0] (header array)
				for($i=0; $i<count($csvData[0]); $i++) {
	
					for($j=0; $j<count($attributesKey); $j++) {
	
						if(trim($attributesKey[$j]) == trim($csvData[0][$i])) {
							//echo $attributesKey[$j]."  ".$attributesValue[$j]."<br>";
							$csvData[0][$i] = $attributesValue[$j];
							break;
						}
					}
	
				}
				//echo "<pre>"; print_r($csvData);exit;
				$priceTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_priceincrease');
				$query = "SELECT * FROM $priceTable";
				$result= mysql_query($query);
				while($row = mysql_fetch_array($result)) {
					$price_from[] = $row['price_from'];
					$price_to[] = $row['price_to'];
					$price_increase_per = $row['price_increase']/100 ;
					$price_increase_final[] = 1 + $price_increase_per ;
				}
	
	
				// Apply Price Increase before saving csv because we are not storing jewelry to database like diamonds.
				$row=0;
				$csvRowCnt = 1;
				foreach($csvData as $column) {
	
					if($row==0){
						$row++;
						continue;
					}
	
					$price = $column[4];
					if($price == 0) $price = "";
					$csvData[$csvRowCnt][4] = $price;
	
					$special_price = $column[5];
					if($special_price == 0) $special_price = "";
					$csvData[$csvRowCnt][5] = $special_price;
	
					$tier_price = $column[6];
					if($tier_price == 0) $tier_price = "";
					$csvData[$csvRowCnt][6] = $tier_price;
	
					$msrp = $column[7];
					if($msrp == 0) $msrp = "";
					$csvData[$csvRowCnt][7] = $msrp;
	
					$g14_price = $column[8];
					if($g14_price == 0) $g14_price = "";
					$csvData[$csvRowCnt][8] = $g14_price;
	
					$g18_price = $column[9];
					if($g18_price == 0) $g18_price = "";
					$csvData[$csvRowCnt][9] = $g18_price;
	
					$plat_price = $column[10];
					if($plat_price == 0) $plat_price = "";
					$csvData[$csvRowCnt][10] = $plat_price;
	
					$pall_price = $column[11];
					if($pall_price == 0) $pall_price = "";
					$csvData[$csvRowCnt][11] = $pall_price;
	
					for($i=0; $i < count($price_increase_final); $i++) {
						if($price_increase_final[$i] != '') {
							//$query_update = "UPDATE diamonds_inventory SET totalprice = totalprice*".$price_increase_final[$i]." where cost between ".$price_from[$i]." AND ".$price_to[$i];
							if(($price >= $price_from[$i]) && ($price <= $price_to[$i]) && ($price != 0) && ($price != '')) {
	
								$incPrice = $price*$price_increase_final[$i];
								//echo $incPrice."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][4] = $incPrice;
							}
	
							if(($special_price >= $price_from[$i]) && ($special_price <= $price_to[$i]) && ($special_price != 0) && ($special_price != '')) {
									
								$incSpPrice = $special_price*$price_increase_final[$i];
								//echo $incSpPrice."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][5] = $incSpPrice;
							}
	
							if(($tier_price >= $price_from[$i]) && ($tier_price <= $price_to[$i]) && ($tier_price != 0) && ($tier_price != '')) {
									
								$incTrPrice = $tier_price*$price_increase_final[$i];
								//echo $incTrPrice."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][6] = $incTrPrice;
							}
	
							if(($msrp >= $price_from[$i]) && ($msrp <= $price_to[$i]) && ($msrp != 0) && ($msrp != '')) {
									
								$incMSPrice = $msrp*$price_increase_final[$i];
								//echo $incMSPrice."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][7] = $incMSPrice;
							}
	
							if(($g14_price >= $price_from[$i]) && ($g14_price <= $price_to[$i]) && ($g14_price != 0) && ($g14_price != '')) {
									
								$incG14Price = $g14_price*$price_increase_final[$i];
								//echo $incG14Price."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][8] = $incG14Price;
							}
	
							if(($g18_price >= $price_from[$i]) && ($g18_price <= $price_to[$i]) && ($g18_price != 0) && ($g18_price != '')) {
									
								$incG18Price = $g18_price*$price_increase_final[$i];
								//echo $incG18Price."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][9] = $incG18Price;
							}
	
							if(($plat_price >= $price_from[$i]) && ($plat_price <= $price_to[$i]) && ($plat_price != 0) && ($plat_price != '')) {
									
								$incPtPrice = $plat_price*$price_increase_final[$i];
								//echo $incPtPrice."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][10] = $incPtPrice;
							}
	
							if(($pall_price >= $price_from[$i]) && ($pall_price <= $price_to[$i]) && ($pall_price != 0) && ($pall_price != '')) {
									
								$incPlPrice = $pall_price*$price_increase_final[$i];
								//echo $incPlPrice."==".$csvRowCnt."<br>";
								$csvData[$csvRowCnt][11] = $incPlPrice;
							}
	
						}
					}
	
					$csvRowCnt++;
				}
				//echo "<pre>"; print_r($csvData);exit;
	
				$path = Mage::getBaseDir("var") . DS ."import" . DS;
				$fp = fopen($path."jewelerslink_update.csv", "w") or die("can't open file");
				foreach ($csvData as $fields) {
					fputcsv($fp, $fields);
				}
				fclose($fp);
	
				//$this->getImagesAction();
	
				Mage::getSingleton("adminhtml/session")->addSuccess(count($csvData)." Update Products CSV Created from Jewelerslink Inventory.");
				$this->_redirect("*/*/importForm");
			}
	
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
	
	public function getImagesAction() {
	
		try {
			$path = Mage::getBaseDir("var") . DS ."import" . DS;
			$fp = fopen($path."jewelerslink_import.csv",'r') or die("can't open file");
			$row=0;
			$count = 1;
			while($csv_line = fgetcsv($fp,1024))
			{
				//echo "<pre>";print_r($csv_line);
				if($row==0){
						
					foreach($csv_line as $key => $field) {
	
						if($field == 'image') {
							$imageKey = $key;
						}
						if($field == 'small_image') {
							$small_imageKey = $key;
						}
						if($field == 'thumbnail') {
							$thumbnailKey = $key;
						}
						if($field == 'gallery') {
							$galleryKey = $key;
						}
	
					}
						
					$row++;
					continue;
				}
	
				if(isset($imageKey) && $imageKey != "") {
					// Main Image Save
					$imagePath = str_replace("/jewelerslink","",$csv_line[$imageKey]);
					$httpUrl = "http://images.jewelerslink.com/jewelry/".$imagePath;
					$imageName = basename($imagePath);
					$localpath = getcwd()."/media/import/jewelerslink/".str_replace($imageName,"",$imagePath)."/";
					if(!is_dir($localpath)) mkdir($localpath,0777,true);
					if(!file_exists($localpath.$imageName))
						copy($httpUrl, $localpath.$imageName);
				}
	
				if(isset($small_imageKey) && $small_imageKey != "") {
					// Small Image Save
					$simagePath = str_replace("/jewelerslink","",$csv_line[$small_imageKey]);
					$shttpUrl = "http://images.jewelerslink.com/jewelry/".$simagePath;
					$simageName = basename($simagePath);
					$slocalpath = getcwd()."/media/import/jewelerslink/".str_replace($simageName,"",$simagePath)."/";
					if(!is_dir($slocalpath)) mkdir($slocalpath,0777,true);
					if(!file_exists($slocalpath.$simageName))
						copy($shttpUrl, $slocalpath.$simageName);
				}
	
				if(isset($thumbnailKey) && $thumbnailKey != "") {
					// Thumbnail Image Save
					$timagePath = str_replace("/jewelerslink","",$csv_line[$thumbnailKey]);
					$thttpUrl = "http://images.jewelerslink.com/jewelry/".$timagePath;
					$timageName = basename($timagePath);
					$tlocalpath = getcwd()."/media/import/jewelerslink/".str_replace($timageName,"",$timagePath)."/";
					if(!is_dir($tlocalpath)) mkdir($tlocalpath,0777,true);
					if(!file_exists($tlocalpath.$timageName))
						copy($thttpUrl, $tlocalpath.$timageName);
				}
	
				if(isset($galleryKey) && $galleryKey != "") {
					// Galley Images Save
					$galleryArray = explode(";",$csv_line[$galleryKey]);
						
					foreach($galleryArray as $galleryImg) {
						$gimagePath = str_replace("/jewelerslink","",$galleryImg);
						$ghttpUrl = "http://images.jewelerslink.com/jewelry/".$gimagePath;
						$gimageName = basename($gimagePath);
						$glocalpath = getcwd()."/media/import/jewelerslink/".str_replace($gimageName,"",$gimagePath)."/";
						if(!is_dir($glocalpath)) mkdir($glocalpath,0777,true);
						if(!file_exists($glocalpath.$gimageName))
							copy($ghttpUrl, $glocalpath.$gimageName);
					}
				}
	
			}
				
			Mage::getSingleton("adminhtml/session")->addSuccess("Images written successfully.");
			$this->_redirect("*/*/importForm");
			return;
				
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
	
	public function importJewelryAction() {
	
		$url = $this->getUrl("*idealAdmin/system_convert_gui/run/", array("id" => 3, "files" => "jewelerslink_import.csv"));
		$url = str_replace("*idealAdmin","idealAdmin", $url);
		?>
		<script type="text/javascript">
			window.location = "<?php echo $url ?>";
		</script> 
		<?php
	}
		
	public function updateJewelryAction() {
		
		$url = $this->getUrl("*idealAdmin/system_convert_gui/run/", array("id" => 3, "files" => "jewelerslink_update.csv"));
		$url = str_replace("*idealAdmin","idealAdmin", $url);
		?>
		<script type="text/javascript">
			window.location = "<?php echo $url ?>";
		</script> 
		<?php
	}
		
	public function disableOlderAction() {
		try
		{
			$resource = Mage::getConfig()->getNode('global/resources')->asArray();
	        $magento_db = $resource['default_setup']['connection']['host'];
	        $mdb_user = $resource['default_setup']['connection']['username'];
	        $mdb_passwd = $resource['default_setup']['connection']['password'];
	        $mdb_name = $resource['default_setup']['connection']['dbname'];
			$magento_connection = @mysql_connect($magento_db, $mdb_user, $mdb_passwd);
	
			if (!$magento_connection)
			{
				die('Unable to connect to the database');
			}
			@mysql_select_db($mdb_name, $magento_connection) or die ("Database not found.");
	
			$vendorTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_vendor');
			$select_vendor = 'select * from `'.$vendorTable.'`';
			$result = mysql_query($select_vendor);
			while($row = mysql_fetch_array($result))
			{
				$vendorArray[] = $row['vendor_name'];
			}
			$username = Mage::getStoreConfig('jewelryshare/user_detail/ideal_username');
			$password = Mage::getStoreConfig('jewelryshare/user_detail/ideal_password');
	
			$data_string = json_encode($vendorArray);
	
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,"http://www.jewelerslink.com/jewelry/index/getUpdateJson");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("username"=>$username,"password"=>$password,"vendors"=>$data_string));
			$data = curl_exec($ch);
			curl_close($ch);
			//echo $data; exit;
	
			if($data == "Invalid Login") {
	
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Unauthenticate Login, Go to ( System > Configuration > Jewelry Config ) and enter Jewelerslink Login Detail"));
				$this->_redirect("*/*/importForm");
				return;
	
			} else {
				//echo $data;
				$jsonData = json_decode($data, true);
				$jwlProducts = array();
				foreach($jsonData as $data) {
					if($data[2] != 'sku')
						$jwlProducts[] = $data[2];
				}
				//echo "<pre>"; print_r($jwlProducts);exit;
				
				$existingProducts = Mage::getModel('catalog/product')->getCollection();
				//$existingProducts->addAttributeToSelect('sku');

				$disable = 0; 
				foreach($existingProducts as $exists) {
					
					$sku = $exists->getSku();
					if(!in_array($sku, $jwlProducts)) {

						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
						if($product) {
							$status = $product->getStatus();
							if($status != 2) {
								$product->setStatus(2);
								$product->save();
							}
						}
						$disable++;
					}
				}
				//echo $disable; exit;

				Mage::getSingleton("adminhtml/session")->addSuccess($disable." Products not in jewelrslink has been disabled.");
				$this->_redirect("*/*/importForm");
			}
	
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
	
	public function restorePriceIncreaseAction() {
	
		try
		{
			$resource = Mage::getConfig()->getNode('global/resources')->asArray();
	        $magento_db = $resource['default_setup']['connection']['host'];
	        $mdb_user = $resource['default_setup']['connection']['username'];
	        $mdb_passwd = $resource['default_setup']['connection']['password'];
	        $mdb_name = $resource['default_setup']['connection']['dbname'];
			$magento_connection = @mysql_connect($magento_db, $mdb_user, $mdb_passwd);
	
			if (!$magento_connection)
			{
				die('Unable to connect to the database');
			}
			@mysql_select_db($mdb_name, $magento_connection) or die ("Database not found.");

			$priceTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_priceincrease');
			mysql_query("TRUNCATE TABLE $priceTable");
				
			mysql_query("INSERT INTO $priceTable SET price_from = 100000.01, price_to = 10000000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 50000.01, price_to = 100000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 30000.01, price_to = 50000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 25000.01, price_to = 30000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 20000.01, price_to = 25000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 15000.01, price_to = 20000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 10000.01, price_to = 15000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 5000.01, price_to = 10000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 3500.01, price_to = 5000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 2000.01, price_to = 3500, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 1000.01, price_to = 2000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 500.01, price_to = 1000, price_increase = 0")or die(mysql_error());
			mysql_query("INSERT INTO $priceTable SET price_from = 1, price_to = 500, price_increase = 0")or die(mysql_error());
				
			Mage::getSingleton("adminhtml/session")->addSuccess("Price Increase Restored to default values.");
			$this->_redirect("*/*/importForm");
	
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
	
	public function getImagesActionOld() {  // Not Using this
		
		$vendors = $this->getvendorIdsAction();
		
		if(count($vendors)>0) {
			
			foreach($vendors as $vendorId) {
				
				//echo $vendorId; exit;
				
				$ftp_host = "images.jewelerslink.com";
				$ftp_username = "images@jewelerslink.com";
				$ftp_password = "jewelerslink123";
			
				// path to remote file
				$server_file = "/jewelry/".$vendorId."/".$vendorId.".zip";
				
				$localDir = getcwd()."/media/import/jewelerslink/".$vendorId."/";
				$local_file = $localDir.$vendorId.".zip";
				
				if (is_dir($localDir)) $this->deleteDir($localDir);
				if (!is_dir($localDir)) mkdir($localDir);
				
				// set up basic connection
				$conn_id = ftp_connect($ftp_host);
				// login with username and password
				$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
		
				// try to download $server_file and save to $local_file
				if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
					
					$zip = new ZipArchive();
					$x = $zip->open($local_file);
					if ($x === true) {
						$zip->extractTo($localDir); // change this to the correct site path
						$zip->close();
						unlink($local_file);
					}
				    Mage::getSingleton("adminhtml/session")->addSuccess("Images written successfully.");
				} else {
				    Mage::getSingleton("adminhtml/session")->addError("There was a problem getting images.");
				}
				// close the connection
				ftp_close($conn_id);
			}
			
		} else {
			Mage::getSingleton("adminhtml/session")->addError("No Vendors found.");
		}

	}
	
	public function getvendorIdsAction() { // Not Using this
	
		try
		{
			$resource = Mage::getConfig()->getNode('global/resources')->asArray();
	        $magento_db = $resource['default_setup']['connection']['host'];
	        $mdb_user = $resource['default_setup']['connection']['username'];
	        $mdb_passwd = $resource['default_setup']['connection']['password'];
	        $mdb_name = $resource['default_setup']['connection']['dbname'];
			$magento_connection = @mysql_connect($magento_db, $mdb_user, $mdb_passwd);
				
			if (!$magento_connection)
			{
				die('Unable to connect to the database');
			}
			@mysql_select_db($mdb_name, $magento_connection) or die ("Database not found.");
				
			$vendorTable = Mage::getSingleton('core/resource')->getTableName('jewelryshare_vendor');
			$select_vendor = 'select * from `'.$vendorTable.'`';
			$result = mysql_query($select_vendor);
			while($row = mysql_fetch_array($result))
			{
				$vendorArray[] = $row['vendor_name'];
			}
			$username = Mage::getStoreConfig('jewelryshare/user_detail/ideal_username');
			$password = Mage::getStoreConfig('jewelryshare/user_detail/ideal_password');
				
			$data_string = json_encode($vendorArray);
			
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,"http://www.jewelerslink.com/jewelry/index/getvendorIdsjson");
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("username"=>$username,"password"=>$password,"vendors"=>$data_string));
			$data = curl_exec($ch);
			curl_close($ch);
			//echo $data;
				
			if($data == "Invalid Login") {
	
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Unauthenticate Login, Go to ( System > Configuration > Jewelry Config ) and enter Jewelerslink Login Detail"));
				$this->_redirect("*/*/importForm");
				return;
	
			} else {
				
				$vendorData = json_decode($data, true);
				//echo "<pre>"; print_r($vendorData);exit;
				return $vendorData;
			}
				
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/importForm");
			return;
		}
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('jewelryshare/jewelryshare');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $jewelryshareIds = $this->getRequest()->getParam('jewelryshare');
        if(!is_array($jewelryshareIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($jewelryshareIds as $jewelryshareId) {
                    $jewelryshare = Mage::getModel('jewelryshare/jewelryshare')->load($jewelryshareId);
                    $jewelryshare->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($jewelryshareIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $jewelryshareIds = $this->getRequest()->getParam('jewelryshare');
        if(!is_array($jewelryshareIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($jewelryshareIds as $jewelryshareId) {
                    $jewelryshare = Mage::getSingleton('jewelryshare/jewelryshare')
                        ->load($jewelryshareId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($jewelryshareIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'jewelryshare.csv';
        $content    = $this->getLayout()->createBlock('jewelryshare/adminhtml_jewelryshare_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'jewelryshare.xml';
        $content    = $this->getLayout()->createBlock('jewelryshare/adminhtml_jewelryshare_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}