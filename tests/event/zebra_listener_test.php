<?php

/**
 * Created by PhpStorm.
 * User: lucifer
 * Date: 21.6.2017 г.
 * Time: 23:25
 */

/**
 * @group event
 */


namespace anavaro\zebraenhance\tests\event;

class zebra_listener_test extends \phpbb_database_test_case
{
	protected $user_loader;
	protected $auth;
	protected $config;
	protected $db;
	protected $request;
	protected $template;
	protected $user;
	protected $language;
	protected $notifications_helper;

	/**
	 * Define the extensions to be tested
	 *
	 * @return array vendor/name of extension(s) to test
	 */
	static protected function setup_extensions()
	{
		return array('anavaro/zebraenhance');
	}

	/**
	 * Get data set fixtures
	 */
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/fixture.xml');
	}

	/**
	 * Setup test environment
	 */
	public function setUp() : void
	{
		global $phpbb_root_path, $phpEx;

		parent::setUp();

		$this->user_loader = $this->getMockBuilder('\phpbb\user_loader')
			->disableOriginalConstructor()
			->getMock();

		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();

		$this->config = new \phpbb\config\config(array(
			'zebra_module_id'	=> 'none',
		));

		$this->db = $this->new_dbal();

		$this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();;

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();

		$this->user = $this->getMockBuilder('\phpbb\user')
			->setConstructorArgs(array(
				new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
				'\phpbb\datetime'
			))
			->getMock();

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->notifications_helper = $this->getMockBuilder('\anavaro\zebraenhance\controller\notifyhelper')
			->disableOriginalConstructor()
			->getMock();
		/*$this->notifications_helper->method('notify')
			->will($this->returnArgument(0));*/
	}

	/**
	 * Create our controller
	 */
	protected function set_listener()
	{
		$this->listener = new \anavaro\zebraenhance\event\zebra_listener(
			$this->user_loader,
			$this->auth,
			$this->config,
			$this->db,
			$this->request,
			$this->template,
			$this->user,
			$this->language,
			$this->notifications_helper,
			'phpbb_'
		);
	}

	/**
	 * Test the event listener is subscribing events
	 */
	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.user_setup',
			'core.ucp_add_zebra',
			'core.ucp_remove_zebra',
			'core.ucp_display_module_before',
			'core.delete_user_before',
			'core.memberlist_prepare_profile_data',
		), array_keys(\anavaro\zebraenhance\event\zebra_listener::getSubscribedEvents()));
	}

	/**
	 * Test detect module
	 */
	public function test_get_ucp_module_id()
	{
		$this->set_listener();
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.user_setup', array($this->listener, 'load_language_on_setup'));
		$dispatcher->dispatch('core.user_setup');

		$this->assertEquals(200, $this->config['zebra_module_id']);
	}

	public function zebra_confirm_add_data()
	{
		return array(
			'norm'	=> array(
				'friends', // Mode
				array( // actions
					'user_id'	=> 1,
					'zebra_id'	=> 2
				),
				array(//asserts phpbb_zebra_confirm
					array(
						'user_id'	=> 2,
						'zebra_id'	=> 52,
						'friend'	=> 1,
						'foe'		=> 0
					),
					array(
						'user_id'	=> 2,
						'zebra_id'	=> 3,
						'friend'	=> 1,
						'foe'		=> 0
					),
					array(
						'user_id'	=> 1,
						'zebra_id'	=> 2,
						'friend'	=> 1,
						'foe'		=> 0
					)
				),
			),
			'foe_requests_friendship'	=> array(
				'friends', // Mode
				array( // actions
					   'user_id'	=> 5,
					   'zebra_id'	=> 4
				),
				array(//asserts phpbb_zebra_confirm
					  array(
						  'user_id'	=> 2,
						  'zebra_id'	=> 52,
						  'friend'	=> 1,
						  'foe'		=> 0
					  ),
					  array(
						  'user_id'	=> 2,
						  'zebra_id'	=> 3,
						  'friend'	=> 1,
						  'foe'		=> 0
					  ),
					  array(
						  'user_id'	=> 1,
						  'zebra_id'	=> 2,
						  'friend'	=> 1,
						  'foe'		=> 0
					  )
				),
			),
		);
	}

	/**
	 * Test zebra_confirm_add function
	 *
	 * @dataProvider zebra_confirm_add_data
	 */
	public function test_zebra_confirm_add($mode, $actions, $asserts)
	{
		$mode = $mode;
		$sql_ary = array(
			$actions
		);
		$asserts;
		$event_data = array('mode', 'sql_ary');
		$event = new \phpbb\event\data(compact($event_data));
		$this->set_listener();
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.ucp_add_zebra', array($this->listener, 'zebra_confirm_add'));
		$dispatcher->dispatch('core.ucp_add_zebra', $event);

		$sql = 'SELECT * FROM phpbb_zebra_confirm';
		$result = $this->db->sql_query($sql);
		$cnt = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->assertEquals($asserts[$cnt], $row);
			$cnt++;
		}
	}

	/**
	 * Test zebra_confirm_add confirm action
	 */
	public function test_zebra_confirm_add_accept()
	{
		// This is so hacky but it works.
		$sql = 'ALTER TABLE phpbb_zebra ADD COLUMN bff INT(0)';
		$this->db->sql_query($sql);
		$mode = 'friends';
		$sql_ary = array(
			array(
				'user_id'	=> 52,
				'zebra_id'	=> 2
			)
		);
		$event_data = array('mode', 'sql_ary');
		$event = new \phpbb\event\data(compact($event_data));
		$this->set_listener();
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.ucp_add_zebra', array($this->listener, 'zebra_confirm_add'));
		$dispatcher->dispatch('core.ucp_add_zebra', $event);

		$asserts = array(
			array(
				'user_id'	=> 2,
				'zebra_id'	=> 3,
				'friend'	=> 1,
				'foe'		=> 0
			),
			array(
				'user_id'	=> 1,
				'zebra_id'	=> 2,
				'friend'	=> 1,
				'foe'		=> 0
			)
		);

		$sql = 'SELECT * FROM phpbb_zebra_confirm';
		$result = $this->db->sql_query($sql);
		$cnt = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->assertEquals($asserts[$cnt], $row);
			$cnt++;
		}
	}
}
