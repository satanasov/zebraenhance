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
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class zebra_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(


			'core.user_setup'		=> 'load_language_on_setup',
			'core.ucp_add_zebra'	=>	'zebra_confirm_add',
			'core.ucp_remove_zebra'	=>	'zebra_confirm_remove',
			'core.ucp_display_module_before'	=>	'module_display',
			'core.delete_user_before'	=> 'delete_users',
			'core.memberlist_prepare_profile_data'	       => 'prepare_friends',
		);
	}

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \anavaro\zebraenhance\controller\notifyhelper */
	protected $notifyhelper;

	/** @var  */
	protected $table_prefix;
	/**
	 * Constructor
	 *
	 * @param \phpbb\user_loader                            $user_loader
	 * @param \phpbb\auth\auth                              $auth
	 * @param \phpbb\config\config                          $config
	 * @param \phpbb\db\driver\driver_interface             $db
	 * @param \phpbb\request\request                        $request
	 * @param \phpbb\template\template                      $template
	 * @param \phpbb\user                                   $user
	 * @param \phpbb\language\language                      $language
	 * @param \anavaro\zebraenhance\controller\notifyhelper $notifyhelper
	 * @param                                               $table_prefix
	 */
	public function __construct(\phpbb\user_loader $user_loader, \phpbb\auth\auth $auth, \phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template,
		\phpbb\user $user, \phpbb\language\language $language, \anavaro\zebraenhance\controller\notifyhelper $notifyhelper,
		$table_prefix)
	{
		$this->user_loader = $user_loader;
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->lang = $language;
		$this->notifyhelper = $notifyhelper;
		$this->table_prefix = $table_prefix;
	}

	public function load_language_on_setup($event)
	{
		$this->lang->add_lang('anavaro/zebraenhance', array('zebra_enchance'));

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
			foreach ($event['sql_ary'] as $VAR)
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
						$sql = 'INSERT INTO '. ZEBRA_TABLE .' (user_id, zebra_id, friend, foe, bff) VALUES (' .(int) $VAR['user_id'] . ', ' . (int) $VAR['zebra_id'] . ', 1, 0, 0)';
						$this->db->sql_query($sql);
						$sql = 'INSERT INTO '. ZEBRA_TABLE .' (user_id, zebra_id, friend, foe, bff) VALUES (' .(int) $VAR['zebra_id'] . ', ' . (int) $VAR['user_id'] . ', 1, 0, 0)';
						$this->db->sql_query($sql);

						//Let's update zebra_change
						$sql = 'UPDATE ' . USERS_TABLE .' SET zebra_changed = 1 WHERE (user_id =  ' . (int) $VAR['zebra_id'] . ' or user_id =  ' . (int) $VAR['user_id'] . ')';
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
						if (!$result)
						{
							$sql = 'INSERT INTO ' . $this->table_prefix . 'zebra_confirm (user_id, zebra_id, friend, foe) VALUES (' .(int) $VAR['user_id'] . ', ' . (int) $VAR['zebra_id'] . ', 1, 0)';
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
			foreach ($event['sql_ary'] as $VAR)
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
		if ($event['mode'] == 'friends')
		{
			//let's go for syncronieus remove
			foreach ($event['user_ids'] as $VAR)
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

				//Let's update zebra_change
				$sql = 'UPDATE ' . USERS_TABLE .' SET zebra_changed = 1 WHERE (user_id =  ' . (int) $this->user->data['user_id'] . ' or user_id =  ' . (int) $VAR . ')';
				$this->db->sql_query($sql);
			}

			$event['user_ids'] = array('0');
		}
	}

	public function module_display($event)
	{
		$ispending = $iswaiting = '';
		$submit = $this->request->variable('submit', false);
		$default_fla = $this->user->data['profile_friend_show'];
		$friend_list_acl = $this->request->variable('zebra_profile_acl', $default_fla);
		if ($event['id'] == 'zebra' or $event['id'] == 'ucp_zebra' or $event['id'] == $this->config['zebra_module_id'])
		{
			// Are we submiting new form?
			if ($submit == true)
			{
				if ($friend_list_acl > 5)
				{
					$friend_list_acl = 0;
				}
				$sql = 'UPDATE ' . USERS_TABLE .' SET profile_friend_show = ' . $friend_list_acl . ' WHERE user_id = '.$this->user->data['user_id'];
				$this->db->sql_query($sql);
				$this->user->data['profile_friend_show'] = $friend_list_acl;
			}
			$this->template->assign_var('IS_ZEBRA', '1');
			$this->template->assign_var('ZEBRA_ACL', $this->user->data['profile_friend_show']);
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

			while ($row = $this->db->sql_fetchrow($result))
			{
				$ispending = 1;
				$this->template->assign_block_vars('pending_requests', array(
					'USERNAME'	=> '<a class="username-coloured" style="color: '.$row['user_colour'].'" href="'.append_sid('memberlist.php?mode=viewprofile&u='.$row['user_id']).'">'.$row['username'].'</a>',
					'CONFIRM' => '<a href="./ucp.php?i=zebra&add='.$row['username'].'" data-ajax="true" data-refresh="true"><img src="' . $this->image_dir . '/confirm_16.png"/></a>',
					'CANCEL'	=> '<a href="./ucp.php?i=zebra&remove=1&usernames[]='.$row['user_id'].'" data-ajax="true" data-refresh="true""><img src="' . $this->image_dir . '/cancel.gif"/></a>',
				));
			}
			if ($ispending)
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

			while ($row = $this->db->sql_fetchrow($result))
			{
				$iswaiting = 1;
				$this->template->assign_block_vars('pending_awaits', array(
					'USERNAME'	=> '<a class="username-coloured" style="color: '.$row['user_colour'].'" href="'.append_sid('memberlist.php?mode=viewprofile&u='.$row['zebra_id']).'">'.$row['username'].'</a>',
					'CANCEL'	=> '<a href="./ucp.php?i=zebra&remove=1&usernames[]='.$row['zebra_id'].'" data-ajax="true" data-refresh="true"><img src="' . $this->image_dir . '/cancel.gif"/></a>',
				));
			}
			if ($iswaiting)
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
				'WHERE'	=> 'zc.zebra_id = u.user_id AND zc.user_id = '.$this->user->data['user_id'] .' AND zc.friend = 1',
				'ORDER_BY'	=> 'u.username ASC'
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
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
		foreach ($event['user_ids'] as $VAR)
		{
			$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = '.$VAR.' OR zebra_id = '.$VAR;
			$this->db->sql_query($sql);
			$sql = 'DELETE FROM '. ZEBRA_TABLE .' WHERE user_id = '.$VAR.' OR zebra_id = '.$VAR;
			$this->db->sql_query($sql);
		}
	}

	public function prepare_friends($event)
	{
		$optResult['profile_friend_show'] = $event['data']['profile_friend_show'];
		$zebra_state = 0;
		if ($this->auth->acl_get('a_') || $this->auth->acl_get('m_') || $this->user->data['user_id'] == $event['data']['user_id'])
		{
			$state = 5;
		}
		else if ($this->user->data['user_id'] != ANONYMOUS)
		{
			$sql = 'SELECT * FROM ' . ZEBRA_TABLE . ' WHERE user_id = '.$this->db->sql_escape($event['data']['user_id']).' AND zebra_id = '.$this->user->data['user_id'];
			$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
			if ($result)
			{
				if ($result['foe'] == 1)
				{
					$zebra_state = 2;
				}
				else
				{
					if ($result['bff'] == '0')
					{
						$zebra_state = 3;
					}
					else
					{
						$zebra_state = 4;
					}
				}
			}
			else
			{
				$zebra_state = 1;
			}
		}

		$show = (($optResult['profile_friend_show'] != 2) ? (($optResult['profile_friend_show'] <= $zebra_state) ? true : false) : (($optResult['profile_friend_show'] == 2 and $zebra_state > 0 and $zebra_state != 2) ? true : false));
		if ($event['data']['user_id'] == $this->user->data['user_id'] || $this->auth->acl_get('a_user') || $show)
		{
			$sql = 'SELECT zebra_id FROM ' . ZEBRA_TABLE . ' WHERE user_id = ' . $this->db->sql_escape($event['data']['user_id']) . ' AND friend = 1';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$user_id[] = (int) $row['zebra_id'];
			}
			if (!empty($user_id))
			{
				$this->user_loader->load_users($user_id);
				$selector = 0;
				foreach ($user_id as $VAR)
				{
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
