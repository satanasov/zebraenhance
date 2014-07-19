<?php
/**
*
* @package Zebra Enhance Extension
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace anavaro\zebraenhance\controller;

use Symfony\Component\DependencyInjection\Container;

/**
* Admin controller
*/
class notifyhelper
{
	/**
	* Constructor
	*
	* @param \phpbb\config\config $config                      Config object
	* @param \phpbb\db\driver\driver $db                       Database object
	* @param \phpbb\request\request $request                   Request object
	* @param \phpbb\template\template $template                Template object
	* @param \phpbb\user $user                                 User object
	* @param Container $phpbb_container
	* @param string $root_path                                 phpBB root path
	* @param string $php_ext                                   phpEx
	* @return \phpbb\boardrules\controller\admin_controller
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, Container $phpbb_container, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_container = $phpbb_container;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}
	
	public function test()
	{

	}
	
	/**
	* Main notification function
	* @param type			Type of notification (add/confirm)
	* @param notify_user	User to notify
	* @param action_user	User that trigered the action
	*/
	public function notify($type, $notify_user, $action_user)
	{
		$notification_data = array(
			'user_id'	=> (int) $notify_user,
			'requester_id'	=> (int) $action_user,
		);
		
		
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		
		switch ($type)
		{
			case 'add':
				$phpbb_notifications->add_notifications('zebraadd', $notification_data);
			break;
			case 'confirm':
				$phpbb_notifications->add_notifications('zebraconfirm', $notification_data);
			break;
		}
		
	}
	public function clean($user1, $user2)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		$phpbb_notifications->delete_notifications('zebraadd', $user1, $user2);
		$phpbb_notifications->delete_notifications('zebraadd', $user2, $user1);
		$phpbb_notifications->delete_notifications('zebraconfirm', $user2, $user1);
		$phpbb_notifications->delete_notifications('zebraconfirm', $user1, $user2);
	}
}