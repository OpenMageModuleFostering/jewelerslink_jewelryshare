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
 * TheJewelerslink jewelryshare attribute map grid controller
 *
 * @category    Jewelerslink
 * @package     Jewelerslink_Jewelryshare
 */
class Jewelerslink_Jewelryshare_Adminhtml_Jewelryshare_Codes_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Main index action
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('jewelryshare/adminhtml_list_codes_grid')->toHtml());
    }

    /**
     * Grid edit form action
     *
     */
    public function editFormAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('jewelryshare/adminhtml_edit_codes')->toHtml());
    }

    /**
     * Save grid edit form action
     *
     */
    public function saveFormAction()
    {
        $codeId = $this->getRequest()->getParam('code_id');
        $response = new Varien_Object();
        try {
            $model  = Mage::getModel('jewelerslink_jewelryshare/codes');
            if ($codeId) {
                $model->load($codeId);
            }
            $model->setImportCode($this->getRequest()->getParam('import_code'));
            $model->setEavCode($this->getRequest()->getParam('eav_code'));
            $model->setJewelryImported(intval($this->getRequest()->getParam('jewelry_imported')));
            $model->save();
            $response->setError(0); 
        } catch(Exception $e) {
            $response->setError(1);
            $response->setMessage('Save error');
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Codes (attribute map) list for mass action
     *
     * @return array
     */
    protected function _getMassActionCodes()
    {
        $idList = $this->getRequest()->getParam('code_id');
        if (!empty($idList)) {
            $codes = array();
            foreach ($idList as $id) {
                $model = Mage::getModel('jewelerslink_jewelryshare/codes');
                if ($model->load($id)) {
                    array_push($codes, $model);
                }
            }
            return $codes;
        } else {
            return array();
        }
    }

    /**
     * Set imported codes (attribute map) mass action
     */
    public function massEnableAction()
    {
        $updatedCodes = 0;
        foreach ($this->_getMassActionCodes() as $code) {
            $code->setJewelryImported(1);
            $code->save();
            $updatedCodes++;
        }
        if ($updatedCodes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('jewelryshare')->__("%s codes imported", $updatedCodes));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Set not imported codes (attribute map) mass action
     */
    public function massDisableAction()
    {
        $updatedCodes = 0;
        foreach ($this->_getMassActionCodes() as $code) {
            $code->setJewelryImported(0);
            $code->save();
            $updatedCodes++;
        }
        if ($updatedCodes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('jewelryshare')->__("%s codes not imported", $updatedCodes));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete codes (attribute map) mass action
     */
    public function deleteAction()
    {
        $updatedCodes = 0;
        foreach ($this->_getMassActionCodes() as $code) {
            $code->delete();
            $updatedCodes++;
        }
        if ($updatedCodes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('jewelryshare')->__("%s codes deleted", $updatedCodes));
        }
        $this->_redirect('*/*/index');
    }
}
