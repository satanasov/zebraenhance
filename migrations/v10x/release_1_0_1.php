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
}