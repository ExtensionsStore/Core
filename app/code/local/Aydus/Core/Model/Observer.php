<?php

/**
 * Core observer
 *
 * @category   Aydus
 * @package	   Aydus_Core
 * @author     Aydus <davidt@aydus.com>
 */

class Aydus_Core_Model_Observer
{
    protected $_config;
    
	/**
	 * Group all module system config with section or group names starting with aydus_ 
	 * under the Aydus tab in alphabetical order
	 * 
	 * @param Mage_Core_Model_Observer $observer
	 * @return Aydus_Core_Model_Observer
	 */
	public function groupExtensions($observer)
	{
	    $this->_config = $observer->getConfig();
	    
		$nodes = $this->_getAydusNodes();
		
		if (count($nodes)>0){
		    
		    usort($nodes, array($this, 'sortSections'));
		    $sortOrder = 10000;
		    $sections = $this->_config->getNode('sections');
		    
		    foreach ($nodes as $node){
		    
		        //existing section
		        if ($node->tab){
		            
		            $section = $node;
		    
		            $section->tab = 'aydus';
		            $section->sort_order = $sortOrder++;
		    
		        //add group to tab as a new section
		        } else {
		    
		            $group = $node;
		            $group->expanded = 1;
		            $groupName = $group->getName();
		            $newSectionName = $groupName;
		            $newSection = $sections->addChild($newSectionName);
		    
		            foreach ($group->attributes() as $attribute => $value){
		                $newSection->addAttribute($attribute,$value);
		            }
		    
		            $newSection->tab = 'aydus';
		            $newSection->label = (string)$group->label;
		            $newSection->frontend_type = (string)$group->frontend_type;
		            $newSection->show_in_default = (string)$group->show_in_default;
		            $newSection->show_in_website = (string)$group->show_in_website;
		            $newSection->show_in_store = (string)$group->show_in_store;
		            $newSection->sort_order = $sortOrder++;
		            
		            //if redirected from section
		            $this->_openSectionGroup($group);

		        }
		    
		    }		    
		    
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param Aydus_Core_Model_Observer $observer
	 * @return Aydus_Core_Model_Observer
	 */
	public function addVersionToGroupField($observer)
	{
	    $this->_config = $observer->getConfig();
	    $nodes = $this->_getAydusNodes();
	    
	    foreach ($nodes as $node){
	        
	        $module = $node->getAttribute('module');
	        
	        $fields = @$node->fields;
	        
	        if (!$fields){
	            
	            $groups = @$node->groups;
	             
	            if ($groups && count($groups)>0){
	                 
	                $group = reset($groups);
	                $module = ($group->getAttribute('module')) ? $group->getAttribute('module') : $module;
	                 
	                $fields = @$group->fields;
	            }	            
	        }

            if ($fields && count($fields)>0){
                
                $field = reset($fields);
                $module = ($field->getAttribute('module')) ? $field->getAttribute('module') : $module;
                $versionField = @$fields->version;
                
                if (!$versionField){
                    
                    $versionField = $fields->addChild('version');
                    
                    foreach ($field->attributes() as $attribute => $value){
                        $versionField->addAttribute($attribute,$value);
                    }             
                           
                    $versionField->label = 'Version';
                    $versionField->frontend_type = 'text';
                    $versionField->show_in_default = (string)$field->show_in_default;
                    $versionField->show_in_website = (string)$field->show_in_website;
                    $versionField->show_in_store = (string)$field->show_in_store;
                    $versionField->sort_order = 0;
                }
                
                try {
                    
                    $versionField->module = ucwords($module,'_');
                    
                } catch(Exception $e){
                    
                    $versionField->module = ucwords($module);
                }
                                    
                $versionField->frontend_model = 'aydus/adminhtml_system_config_form_field_version';
	            
            }
	        
	    }
	     
		return $this;
	}
	
	public function getModuleVersion()
	{
	    
	}
	
	/**
	 * Get all tab sections or config groups that have aydus in name
	 * @return SimpleXMLElement
	 */
	protected function _getAydusNodes()
	{
	    if (!$this->_config){
	         
	        $this->_config = Mage::getConfig()->loadModulesConfiguration('system.xml')
	        ->applyExtends();
	
	    }
	     
	    $nodes = $this->_config->getXpath('//*[contains(local-name(), "aydus_") or (tab = "aydus")]');
	     
	    return $nodes;
	}
	
	/**
	 * Go through each config group in section and set expanded or collapsed
	 * 
	 * @param SimpleXMLElement $node
	 */
	protected function _openSectionGroup($node)
	{
	    $domNode = dom_import_simplexml($node);
	    $nodePath = $domNode->getNodePath();
	    $matches = array();
	    preg_match('#/config/sections/(.*)/groups/(.*)#', $nodePath, $matches);
	    
	    if (is_array($matches) && count($matches)>0){
	    
	        $sectionName = $matches[1];
	        $sectionGroupName = $matches[2];
	    
            //collapse
	        $sections = $this->_config->getNode('sections');
            $section = $sections->$sectionName;
            $groups = $section->groups;
            $session = Mage::getSingleton('admin/session');
            $user = $session->getUser();
            $extra = $user->getExtra();
            
            foreach ($groups->children() as $groupName => $group){
                
                $elementId = $sectionName . '_' . $groupName;
                if (isset($extra['configState'][$elementId])) {
                    $extra['configState'][$elementId] = 0;
                }
                
                if ($sectionGroupName == $groupName){
                    
                    $extra['configState'][$elementId] = 1;
                    $group->expanded = 1;
                    
                } 
                
            }
            
            $user->setExtra($extra);
	    	    
	    }	  
	      
	}
			
	/**
	 * Redirect current section to original section
	 * 
	 * @param Mage_Core_Model_Observer $observer
	 * @return Aydus_Core_Model_Observer
	 */
	public function redirectSection($observer)
	{
	    $request = Mage::app()->getRequest();
	    $currentSection = $request->getParam('section');
	    
	    if ($currentSection){
	        	         
	        $nodes = $this->_getAydusNodes();
	         
	        if (count($nodes)>0){
	             
	            foreach ($nodes as $node){
	                 
	                $groupName = $node->getName();
	        
	                if (!$node->tab && $currentSection == $groupName){
	                     
	                   $domNode = dom_import_simplexml($node);
	                   $nodePath = $domNode->getNodePath();
	                   $matches = array();
	                   
	                   preg_match('#/config/sections/(.*)/groups/(.*)#', $nodePath, $matches);
	                   
	                   if (is_array($matches) && count($matches)>0){
	                   
	                       $sectionName = $matches[1];
	                       $groupName = $matches[2];
	                   
	                       $request->setParam('section', $sectionName);
	                   
	                   }
	                   	                                      
	                }
	                 
	            }
	             
	        }	 
	               
	    }
	     	    
	    return $this;
	}
	
	/**
	 * Sort sections by alpha
	 * @todo translate
	 * 
	 * @param SimpleXMLElement $a
	 * @param SimpleXMLElement $b
	 * @return int
	 */
	public function sortSections($a, $b)
	{
	    $aLabel = Mage::helper('core')->__((string)$a->label);
	    $bLabel = Mage::helper('core')->__((string)$b->label);
	     
	    return strcmp($aLabel, $bLabel);
	}
	
}