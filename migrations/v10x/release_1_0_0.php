<?php
/**
*
* @package migration
* @copyright (c) 2012 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

namespace anavaro\zebraenhance\migrations\v10x;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
        {
                return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '1.0.0', '>=');
        }
	static public function depends_on()
        {
                return array('\phpbb\db\migration\data\v310\dev');
        }
		
	public function update_data()
	{
		return array(
			array('config.add', array('zebra_enhance_version', '1.0.0')),
			array('config.add', array('zebra_module_id', 'none')),
		);
	}
	//lets create the needed table	
	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'zebra_confirm'		=> array(
					'COLUMNS'		=> array(
						'user_id'		=> array('UINT:8'),
						'zebra_id'		=> array('UINT:8'),
						'friend'		=> array('UINT:1'),
						'foe'			=> array('UINT:1')
					),
					'PRIMARY_KEY'    => 'user_id, zebra_id',
				),
				$this->table_prefix . 'users_custom'		=> array(
					'COLUMNS'	=> array(
						'user_id'	=> array('UINT'),
					),
					'PRIMARY_KEY'    => 'user_id'
				),
			),
			'add_columns'	=> array(
				ZEBRA_TABLE 	=> array(
					'bff'	=> array('UINT', 0),
				),
				$this->table_prefix . 'users_custom'        => array(
					'profile_friend_show'    => array('UINT', 0),
				)
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'zebra_confirm'
			),
			'drop_columns'          => array(
				ZEBRA_TABLE	=> array(
					'bff',
				),
				$this->table_prefix . 'users_custom'        => array(
					'profile_friend_show',
				)
			),
		);
	}
}