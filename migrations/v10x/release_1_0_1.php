<?php
/**
*
* @package migration
* @copyright (c) 2012 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

namespace anavaro\zebraenhance\migrations\v10x;

class release_1_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return !isset($this->config['zebra_enhance_version']);
	}
	
	static public function depends_on()
	{
		return array('\anavaro\zebraenhance\migrations\v10x\release_1_0_0');
	}
	
	//lets create the needed table	
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users_custom'        => array(
					'zebra_changed'    => array('UINT', 0),
				)
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users_custom'        => array(
					'zebra_changed',
				)
			)
		);
	}
	
	public function update_data()
	{
		return array(
			array('config.update', array('zebra_enhance_version', '1.0.1')),
		);
	}
}
