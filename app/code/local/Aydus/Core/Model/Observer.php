<?php

/**
 * Core observer
 *
 * @category   Aydus
 * @package	   Aydus_Core
 * @author     Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Core_Model_Observer
{
	/**
	 * Group all extensions with section names starting with aydus_ under Aydus tab
	 * 
	 * @param Mage_Core_Model_Observer $observer
	 * @return Aydus_Core_Model_Observer
	 */
	public function groupExtensions($observer)
	{
		$config = $observer->getConfig();
		
		$sections = $config->getNode('sections');

		foreach ($sections->children() as $section => $element){
			
			if (strpos($section, 'aydus_') === 0){
				
				$element->tab = 'aydus';
				
			}
			
		}
		
		return $this;
	}
	
}