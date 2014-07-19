<?php

/**
*
* @package Anavaro.com Zebra Enchance
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
//TODO 2: Make use of ajax requests for canceling requests
//TODO 3: check if Zebra table is cleaned from deletion of user (make it clean if it is not.

namespace anavaro\zebraenhance\event;

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class zebra_listener implements EventSubscriberInterface
{	
	static public function getSubscribedEvents()
    {
		return array(
			//'core.memberlist_prepare_profile_data'	       => 'prepare_medals',
			//'core.user_setup'		=> 'load_language_on_setup',
			//'core.memberlist_view_profile'	      => 'fuunct_one',
			//'core.viewtopic_modify_post_row'	=>	'modify_post_row',
			
			'core.user_setup'		=> 'load_language_on_setup',
			'core.ucp_add_zebra'	=>	'zebra_confirm_add',
			'core.ucp_remove_zebra'	=>	'zebra_confirm_remove',
			'core.ucp_display_module_before'	=>	'module_display',
			'core.delete_user_before'	=> 'delete_users',
			'core.memberlist_prepare_profile_data'	       => 'prepare_friends',
		);
    }
	
	
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
	public function __construct(\phpbb\user_loader $user_loader, \phpbb\auth\auth $auth, \phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \anavaro\zebraenhance\controller\notifyhelper $notifyhelper, $root_path, $php_ext, $table_prefix)
	{
		$this->user_loader = $user_loader;
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->notifyhelper = $notifyhelper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}
	public function load_language_on_setup($event){
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
            'ext_name' => 'anavaro/zebraenhance',
            'lang_set' => 'zebra_enchance',
        );
        $event['lang_set_ext'] = $lang_set_ext;
		$this->notifyhelper->test();
		if ($this->config['zebra_module_id'] == 'none')
		{
			$sql = 'SELECT parent_id FROM ' . MODULES_TABLE . ' WHERE module_basename = \'ucp_zebra\' LIMIT 1';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->config->set('zebra_module_id', $row['parent_id']);
		}

	}


	protected $image_dir = 'ext/anavaro/zebraenhance/images';
	
	public function zebra_confirm_add($event)
	{
		if ($event['mode'] == 'friends')
		{
			foreach($event['sql_ary'] as $VAR) 
			{
				//let's test if we have sent request
				$sql = 'SELECT * FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $VAR['user_id'] . ' AND zebra_id = ' . (int) $VAR['zebra_id'];
				$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
				if (!$result)
				{
					//Let's test if request is pending from the other user
					$sql = 'SELECT * FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $VAR['zebra_id'] . ' AND zebra_id = ' . (int) $VAR['user_id'];
					$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
					//$this->var_display($result);
					if ($result) 
					{
						//so we have incoming request -> we add friends!
						$sql = 'INSERT INTO '. ZEBRA_TABLE .' SET user_id = ' . (int) $VAR['user_id'] . ', zebra_id = ' . (int) $VAR['zebra_id'] . ', friend = 1, foe = 0';
						$this->db->sql_query($sql);
						$sql = 'INSERT INTO '. ZEBRA_TABLE .' SET user_id = ' . (int) $VAR['zebra_id'] . ', zebra_id = ' . (int) $VAR['user_id'] . ', friend = 1, foe = 0';
						$this->db->sql_query($sql);
						
						//let's clean the request table 
						$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $VAR['zebra_id'] . ' AND zebra_id = ' . (int) $VAR['user_id'];
						$this->db->sql_query($sql);
						$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $VAR['user_id'] . ' AND zebra_id = ' . (int) $VAR['zebra_id'];
						$this->db->sql_query($sql);
						$this->notifyhelper->notify('confirm', $VAR['zebra_id'], $VAR['user_id']);
					}
					else 
					{
						//lets see if user is hostile towerds us (if yes - silently drop request)
						$sql = 'SELECT * FROM '. ZEBRA_TABLE .' WHERE user_id = ' . (int) $VAR['zebra_id'] . ' AND zebra_id = ' . (int) $VAR['user_id']. ' AND foe = 1';
						$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
						if (!$result) {
							$sql = 'INSERT INTO ' . $this->table_prefix . 'zebra_confirm SET user_id = ' . (int) $VAR['user_id'] . ', zebra_id = ' . (int) $VAR['zebra_id'] . ', friend = 1, foe = 0';
							$this->db->sql_query($sql);
							$this->notifyhelper->notify('add', $VAR['zebra_id'], $VAR['user_id']);
						}
					}
				}
			}
			$event['sql_ary'] = array();
		}
		if ($event['mode'] == 'foes')
		{
			foreach($event['sql_ary'] as $VAR) 
			{
				//if we add user as foe we have to remove pending requests.
				$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $VAR['zebra_id']. ' AND zebra_id = ' . (int) $VAR['user_id'];
				$this->db->sql_query($sql);
				$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $VAR['user_id'] . ' AND zebra_id = ' . (int) $VAR['zebra_id'];
				$this->db->sql_query($sql);
			}
		}
	}
	
	public function zebra_confirm_remove($event)
	{
		if($event['mode'] == 'friends')
		{
			//let's go for syncronieus remove
			foreach($event['user_ids'] AS $VAR)
			{
				$sql = 'DELETE FROM ' . ZEBRA_TABLE . '
				WHERE user_id = ' . $this->user->data['user_id'] . '
				AND zebra_id = '. $VAR;
				$this->db->sql_query($sql);
				
				$sql = 'DELETE FROM ' . ZEBRA_TABLE . '
				WHERE user_id = ' . $VAR . '
				AND zebra_id = '. $this->user->data['user_id'];
				$this->db->sql_query($sql);
				
				$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm
				WHERE user_id = ' . $this->user->data['user_id'] . '
				AND zebra_id = '. $VAR;
				$this->db->sql_query($sql);
				
				$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm
				WHERE user_id = ' . $VAR . '
				AND zebra_id = '. $this->user->data['user_id'];
				$this->db->sql_query($sql);
				
				$this->notifyhelper->clean($VAR, $this->user->data['user_id']);
			}
			
			$event['user_ids'] = array('0');
		}
	}
	
	
	public function module_display($event)
	{
		$ispending = $iswaiting = '';
		if ($event['id'] == 'ucp_zebra' OR $event['id'] == $this->config['zebra_module_id'])
		{
			$this->template->assign_var('IS_ZEBRA', '1');
			$sql = 'SELECT profile_friend_show FROM ' . $this->table_prefix . 'users_custom WHERE user_id = '. $this->user->data['user_id'];
			$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
			$this->template->assign_var('ZEBRA_ACL', $result['profile_friend_show']);
			//let's get incoming pendings
			$sql_array = array(
				'SELECT'	=> 'zc.*, u.username, u.user_colour',
				'FROM'		=> array(
					$this->table_prefix . 'zebra_confirm'	=>	'zc',
					USERS_TABLE	=> 'u',
				),
				'WHERE'	=> 'zc.user_id = u.user_id AND zc.zebra_id = '.$this->user->data['user_id']
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			
			while($row = $this->db->sql_fetchrow($result))
			{
				$ispending = 1;
				$this->template->assign_block_vars('pending_requests', array(
					'USERNAME'	=> '<a class="username-coloured" style="color: '.$row['user_colour'].'" href="'.append_sid('memberlist.php?mode=viewprofile&u='.$row['user_id']).'">'.$row['username'].'</a>',
					'CONFIRM' => '<a href="./ucp.php?i=zebra&add='.$row['username'].'" data-ajax="true" data-refresh="true"><img src="' . $this->image_dir . '/confirm_16.png"/></a>',
					'CANCEL'	=> '<a href="./ucp.php?i=zebra&remove=1&usernames[]='.$row['user_id'].' data-ajax="true" data-refresh="true""><img src="' . $this->image_dir . '/cancel.gif"/></a>',
				));
			}
			if($ispending)
			{
				$this->template->assign_var('HAS_PENDING', 'yes');
				
			}
			//now, let's get our own requests that are waiting.
			$sql_array = array(
				'SELECT'	=> 'zc.*, u.username, u.user_colour',
				'FROM'		=> array(
					$this->table_prefix . 'zebra_confirm'	=>	'zc',
					USERS_TABLE	=> 'u',
				),
				'WHERE'	=> 'zc.zebra_id = u.user_id AND zc.user_id = '.$this->user->data['user_id']
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			while($row = $this->db->sql_fetchrow($result))
			{
				$iswaiting = 1;
				$this->template->assign_block_vars('pending_awaits', array(
					'USERNAME'	=> '<a class="username-coloured" style="color: '.$row['user_colour'].'" href="'.append_sid('memberlist.php?mode=viewprofile&u='.$row['zebra_id']).'">'.$row['username'].'</a>',
					'CANCEL'	=> '<a href="./ucp.php?i=zebra&remove=1&usernames[]='.$row['zebra_id'].'" data-ajax="true" data-refresh="true"><img src="' . $this->image_dir . '/cancel.gif"/></a>',
				));
			}
			if($iswaiting)
			{
				$this->template->assign_var('HAS_WAITING', 'yes');
			}
			
			
			//let's populate the prity zebra list (bff and all)
			$sql_array = array(
				'SELECT'	=> 'zc.*, u.username, u.user_colour',
				'FROM'	=> array(
					ZEBRA_TABLE	=> 'zc',
					USERS_TABLE	=> 'u',
				),
				'WHERE'	=> 'zc.zebra_id = u.user_id AND zc.user_id = '.$this->user->data['user_id'],
				'ORDER_BY'	=> 'u.username ASC'
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			while($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('prity_zebra', array(
					'USERNAME'	=>	'<a class="username-coloured" style="color: '.$row['user_colour'].'" href="'.append_sid('memberlist.php?mode=viewprofile&u='.$row['zebra_id']).'">'.$row['username'].'</a>',
					'CANCEL' => '<a href="./ucp.php?i=zebra&remove=1&usernames[]='.$row['zebra_id'].'" data-ajax="true" data-refresh="true"><img src="' . $this->image_dir . '/cancel.gif"/></a>',
					'BFF' =>	$row['bff'] ? '<a href="./app.php/zebraenhance/togle_bff/'.$row['zebra_id'].'" data-ajax="togle_bff"><img id="usr_'.$row['zebra_id'].'" src="'. $this->image_dir . '/favorite_remove.png" width="16px" height="16px"/></a>' : '<a href="./app.php/zebraenhance/togle_bff/'.$row['zebra_id'].'" data-ajax="togle_bff"><img id="usr_'.$row['zebra_id'].'" src="'. $this->image_dir . '/favorite_add.png" width="16px" height="16px"/></a>'
				));
			}
			$this->template->assign_var('IMGDIR', $this->image_dir);
		}
	}
	
	
	public function delete_users($event)
	{	
		foreach ($event['user_ids'] AS $VAR)
		{
			$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = '.$VAR.' OR zebra_id = '.$VAR;
			$this->db->sql_query($sql);
			$sql = 'DELETE FROM '. ZEBRA_TABLE .' WHERE user_id = '.$VAR.' OR zebra_id = '.$VAR;
			$this->db->sql_query($sql);
		}
	}
	
	public function prepare_friends($event)
	{
		$sql = 'SELECT profile_friend_show FROM ' . $this->table_prefix . 'users_custom WHERE user_id = '.$this->db->sql_escape($event['data']['user_id']);
		$result = $this->db->sql_query($sql);
		$optResult = $this->db->sql_fetchrow($result);
		$sql = 'SELECT * FROM ' . ZEBRA_TABLE . ' WHERE user_id = '.$this->db->sql_escape($event['data']['user_id']).' AND zebra_id = '.$this->user->data['user_id'];
		$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
		$zebra_state = 0;
		if ($result)
		{
			if ($result['foe'] == 1)
			{
				$zebra_state = 1;
			}
			else
			{
				if ($result['bff'] == '0') {
					$zebra_state = 3;
				}
				else {
					$zebra_state = 4;
				}
			}
		}
		else
		{
			$zebra_state = 2;
		}
		//print_r($zebra_state);
		$users;
		$show = ($optResult['profile_friend_show'] > 0 ? (($optResult['profile_friend_show'] == 1 AND $zebra_state != 1) ? (($optResult['profile_friend_show'] <= $zebra_state) ? true : false) : false) : false);
		if ($event['data']['user_id'] == $this->user->data['user_id'] || $this->auth->acl_get('a_user') || $show)
		{
			$sql = 'SELECT zebra_id FROM ' . ZEBRA_TABLE . ' WHERE user_id = ' . $this->db->sql_escape($event['data']['user_id']);
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$user_id[] = (int) $row['zebra_id'];
			}
			if (!empty($user_id))
			{
				$this->user_loader->load_users($user_id);
				$selector = 0;
				foreach ($user_id as $VAR) {
					$this->template->assign_block_vars('zebra_friendslist', array(
						'USER_LINK'	=> $this->user_loader->get_username($VAR, 'profile'),
						'USER_AVATAR'	=> $this->user_loader->get_avatar($VAR),
						'USERNAME'	=> $this->user_loader->get_username($VAR, 'full'),
						'SELECTOR'	=> $selector,
					));
					if ($selector == 3)
					{
						$selector = 0;
					}
					else
					{
						$selector ++;
					}
				}
			}
		}
		else
		{
			$this->template->assign_var('FRIENDLIST_ERROR_ACCESS', 'yes');
		}
		$this->template->assign_var('FRIENDLIST', 'yes');
	}
	
	protected function var_display($i) 
	{
		echo '<pre>';
		print_r($i);
		echo '</pre>';
		return true;
	}
	
}
