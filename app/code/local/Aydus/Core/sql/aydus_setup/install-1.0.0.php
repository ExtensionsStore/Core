<?php

/**
 * Core setup
 *
 * @category    Aydus
 * @package     Aydus_Core
 * @author      Aydus <davidt@aydus.com>
 */

$installer = $this;
$installer->startSetup();
echo 'Aydus Core setup started ...<br/>';

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_core_schedule')} (
`schedule_id` INT(11) NOT NULL,
`value` TEXT NOT NULL,
PRIMARY KEY ( `schedule_id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

echo 'Aydus Core setup complete.<br/>';
$installer->endSetup();