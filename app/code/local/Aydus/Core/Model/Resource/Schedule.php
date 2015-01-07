<?php

/**
 * Schedule resource model
 *
 * @category    Aydus
 * @package     Aydus_Core
 * @author		Aydus <davidt@aydus.com>
 */

class Aydus_Core_Model_Resource_Schedule extends Mage_Core_Model_Resource_Db_Abstract
{
	
	protected function _construct()
	{
		$this->_init('aydus/schedule', 'schedule_id');
		$this->_isPkAutoIncrement = false;
	}
	
}

