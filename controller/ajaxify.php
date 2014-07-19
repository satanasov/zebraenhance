<?php
/**
*
* @package Anavaro.com Zebra Enchance
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace anavaro\zebraenhance\controller;

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class ajaxify
{
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param \phpbb\auth		$auth		Auth object
	* @param \phpbb\cache\service	$cache		Cache object
	* @param \phpbb\config	$config		Config object
	* @param \phpbb\db\driver	$db		Database object
	* @param \phpbb\request	$request	Request object
	* @param \phpbb\template	$template	Template object
	* @param \phpbb\user		$user		User object
	* @param \phpbb\content_visibility		$content_visibility	Content visibility object
	* @param \phpbb\controller\helper		$helper				Controller helper object
	* @param string			$root_path	phpBB root path
	* @param string			$php_ext	phpEx
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $root_path, $php_ext, $table_prefix)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}
	
	
	public function base ($action, $userid)
	{
	
		//load language file
		
		$this->user->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$confirm = $this->request->variable('confirm', '');
		$u_action = $this->root_path . 'ucp.php?i=168';
		switch ($action)
		{
			
			case 'cancel_fr':
				// check mode
				if ($confirm)
				{
					//$this->var_display($userid);
					//let me delete all requests between you and user id.
					
					$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . $userid . ' AND zebra_id = ' . $this->user->data['user_id'];
					$this->db->sql_query($sql);
					$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . $this->user->data['user_id'] . ' AND zebra_id = ' . $userid;
					$this->db->sql_query($sql);
					$message = '';
					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response;
						$json_response->send(array(
							'success' => $updated,
							'MESSAGE_TITLE' => $this->user->lang['INFORMATION'],
							'MESSAGE_TEXT'  => $this->user->lang['UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'],
							'REFRESH_DATA'  => array(
								'time'  => 3,
								'url'           => $u_action
							)
						));
					}
					else
					{
						meta_refresh(3, $u_action);
						trigger_error($this->user->lang['UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL']);
					}
				}
				else
				{
					confirm_box(false, $this->user->lang['UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK']);
				}
			break;
			case 'togle_bff':
				$sql='SELECT bff FROM ' . ZEBRA_TABLE . ' WHERE zebra_id = ' .$userid. ' AND user_id = ' .$this->user->data['user_id'];
				$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
				if ($result)
				{
					if($result['bff'] == '0')
					{
						$sql = 'UPDATE ' . ZEBRA_TABLE . ' SET bff = 1 WHERE zebra_id = ' . $userid. ' AND user_id = ' .$this->user->data['user_id'];
						$this->db->sql_query($sql);
						$exit = 'add';
					}
					if($result['bff'] == '1')
					{
						$sql = 'UPDATE ' . ZEBRA_TABLE . ' SET bff = 0 WHERE zebra_id = ' .$userid. ' AND user_id = ' .$this->user->data['user_id'];
						$this->db->sql_query($sql);
						$exit = 'rem';
					}
					$json_response = new \phpbb\json_response;
					$json_response->send(array(
						'status'	=> '0',
						'exit'	=>	$exit,
						'user_id'	=>	$userid,
					));
				}
				else
				{
					$json_response = new \phpbb\json_response;
					$json_response->send(array(
						'status'	=> '1',
						'user_id'	=>	$userid,
					));
				}
				
			break;
			case 'change_acl':
				if ($userid > 4)
				{
					$userid = 0;
				}
				$sql = 'UPDATE ' . $this->table_prefix . 'users_custom SET profile_friend_show = ' . $userid . ' WHERE user_id = '.$this->user->data['user_id'];
				$this->db->sql_query($sql);
				$json_response = new \phpbb\json_response;
					$json_response->send(array(
						'status' => 0,
						'exit'	=>	'acl_set'
					));
			break;
		}
	}
	protected function var_display($i) 
	{
		echo '<pre>';
		print_r($i);
		echo '</pre>';
		return true;
	}
	

}