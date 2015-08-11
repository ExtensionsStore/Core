<?php

/**
 *
 * @category    Aydus
 * @package     Aydus_Core
 * @author		Aydus <davidt@aydus.com>
 */

class Aydus_Core_Helper_Data extends Mage_Core_Helper_Abstract 
{
	
    public function getModuleVersion($nsModule)
    {
        $version = 'N/a';
        
        if ($nsModule){
            
            if (count(explode('_',$nsModule)) == 1){
                $nsModule = 'Aydus_'.$nsModule;
            }
            
            $config = Mage::getConfig();
            $modules = $config->getNode('modules')->children();
            $element = $modules->{$nsModule};
            
            if ($element && $element->version){
                
                $version = (string)$element->version;
                
            } else {
                
                $nsModule = strtolower($nsModule);
                
                foreach ($modules as $module => $element){
                
                    $module = strtolower($module);
                
                    if ($nsModule == $module){
                        $version = (string)$element->version;
                        break;
                    }
                
                }                
            }
            
        }
        
        return $version;
    }
    
}