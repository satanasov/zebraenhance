<?php
/**
*
* @package Anavaro.com Zebra Enchance
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace anavaro\zebraenhance\controller;

class ajaxify
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var string */
	protected $root_path;

	/** @var  */
	protected $table_prefix;

	/**
	 * Constructor
	 * NOTE: The parameters of this method must match in order and type with
	 * the dependencies defined in the services.yml file for this service.
	 *
	 * @param \phpbb\db\driver\driver_interface $db        Database object
	 * @param \phpbb\request\request              $request   Request object
	 * @param \phpbb\user                                        $user      User object
	 * @param \phpbb\language\language                           $language
	 * @param string                                             $root_path phpBB root path
	 * @param                                                    $table_prefix
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\request\request $request,
		\phpbb\user $user, \phpbb\language\language $language,
		$root_path, $table_prefix)
	{
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->lang = $language;
		$this->root_path = $root_path;
		$this->table_prefix = $table_prefix;
	}

	public function base ($action, $userid)
	{
		//load language file
		$this->lang->add_lang('anavaro/zebraenhance', array('zebra_enchance'));
		$confirm = $this->request->variable('confirm', '');
		$u_action = $this->root_path . 'ucp.php?i=168';
		switch ($action)
		{
			case 'cancel_fr':
				// check mode
				if ($confirm)
				{
					$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . (int) $userid . ' AND zebra_id = ' . $this->user->data['user_id'];
					$this->db->sql_query($sql);
					$sql = 'DELETE FROM ' . $this->table_prefix . 'zebra_confirm WHERE user_id = ' . $this->user->data['user_id'] . ' AND zebra_id = ' . (int) $userid;
					$this->db->sql_query($sql);
					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response;
						$json_response->send(array(
							'success' => 1,
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
						trigger_error($this->lang->lang('UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'));
					}
				}
				else
				{
					confirm_box(false, $this->lang->lang('UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'));
				}
			break;
			case 'togle_bff':
				$sql='SELECT bff FROM ' . ZEBRA_TABLE . ' WHERE zebra_id = ' . (int) $userid . ' AND user_id = ' . $this->user->data['user_id'];
				$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
				if ($result)
				{
					if ($result['bff'] == '0')
					{
						$sql = 'UPDATE ' . ZEBRA_TABLE . ' SET bff = 1 WHERE zebra_id = ' . (int) $userid . ' AND user_id = ' . $this->user->data['user_id'];
						$this->db->sql_query($sql);
						$exit = 'add';
					}
					if ($result['bff'] == '1')
					{
						$sql = 'UPDATE ' . ZEBRA_TABLE . ' SET bff = 0 WHERE zebra_id = ' . (int) $userid . ' AND user_id = ' . $this->user->data['user_id'];
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
		}
	}
}
