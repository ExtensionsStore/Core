<?php

/**
 * 
 *
 * @category    Aydus
 * @package     Aydus_Core
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_Core_Block_Adminhtml_System_Config_Form_Field_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_System_Config_Form_Field::_getElementHtml()
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $element->setReadonly(true, true);
        $originalData = $element->getOriginalData();
        $nsModule = $originalData['module'];
        $version = $this->helper('aydus')->getModuleVersion($nsModule);
        $element->setValue($version);
        
        return parent::_getElementHtml($element);
    }
}
