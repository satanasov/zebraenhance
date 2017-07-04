<?php
/**
*
* @package Zebra Enhance Extension
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace anavaro\zebraenhance\notification;

/**
* Board Rules notifications class
* This class handles notifications for Board Rules
*
* @package notifications
*/
class zebraconfirm extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'anavaro.zebraenhance.notification.zebraconfirm';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_ZEBRA_CONFIRM',
	);


	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\config\config */
	protected $config;

	public function set_config(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	/**
	 * Is available
	 */
	public function is_available()
	{
		return true;
	}

	/**
	 * Get the id of the
	 *
	 * @param array $pm The data from the private message
	 * @return int
	 */
	static public function get_item_id($data)
	{
		$uid = '';
		foreach ($data['user_id'] as $id => $string)
		{
			$uid = $id;
		}
		return (int) $uid;
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The data for the updated rules
	 * @return mixed
	 */
	public static function get_item_parent_id($data)
	{
		// No parent
		return 0;
	}

	/**
	* Find the users who will receive notifications
	*
	* @param array $data The data for the updated rules
	*
	* @return array
	*/
	public function find_users_for_notification($data, $options = array())
	{

		$this->user_loader->load_users(array_keys($data['user_id']));

		return $this->check_user_notification_options(array_keys($data['user_id']), $options);
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array($this->get_data('requester_id'));
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('requester_id'), false, true);
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$username = $this->user_loader->get_username($this->get_data('requester_id'), 'no_profile');

		return $this->language->lang('NOTIFICATION_ZEBRA_CONFIRM', $username);
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'ucp.' . $this->php_ext, "i=". $this->config['zebra_module_id']);
	}

	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return false;
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array();
	}

	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param array $data The data for the updated rules
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('requester_id', $data['requester_id']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
