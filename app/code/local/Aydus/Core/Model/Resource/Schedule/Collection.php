<?php

/**
 * Schedule resource collection model
 *
 * @category    Aydus
 * @package     Aydus_Core
 * @author      Aydus <davidt@aydus.com>
 */
	
class Aydus_Core_Model_Resource_Schedule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{

	protected function _construct()
	{
        parent::_construct();
		$this->_init('aydus/schedule');
	}
	
}