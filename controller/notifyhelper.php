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
	 * @param \Symfony\Component\DependencyInjection\Container $phpbb_container
	 */
	public function __construct(Container $phpbb_container)
	{
		$this->phpbb_container = $phpbb_container;
	}

	/**
	 * Main notification function
	 *
	 * @param                                       type            Type of notification (add/confirm)
	 * @param $notify_user
	 * @param $action_user
	 * @internal param \anavaro\zebraenhance\controller\User $notify_user to notify
	 * @internal param \anavaro\zebraenhance\controller\User $action_user that trigered the action
	 */
	public function notify($type, $notify_user, $action_user)
	{
		$notification_data = array(
			'user_id'	=> array($notify_user => 'user_id'),
			'requester_id'	=> (string) $action_user,
		);

		$phpbb_notifications = $this->phpbb_container->get('notification_manager');

		switch ($type)
		{
			case 'add':
				$phpbb_notifications->add_notifications('anavaro.zebraenhance.notification.zebraadd', $notification_data);
			break;
			case 'confirm':
				$phpbb_notifications->add_notifications('anavaro.zebraenhance.notification.zebraconfirm', $notification_data);
			break;
		}
	}
	public function clean($user1, $user2)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		$phpbb_notifications->delete_notifications('anavaro.zebraenhance.notification.zebraadd', $user1, $user2);
		$phpbb_notifications->delete_notifications('anavaro.zebraenhance.notification.zebraadd', $user2, $user1);
		$phpbb_notifications->delete_notifications('anavaro.zebraenhance.notification.zebraconfirm', $user2, $user1);
		$phpbb_notifications->delete_notifications('anavaro.zebraenhance.notification.zebraconfirm', $user1, $user2);
	}
}
