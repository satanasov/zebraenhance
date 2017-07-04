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
	public function setUp()
	{
		global $phpbb_root_path, $phpEx;

		parent::setUp();

		$this->user_loader = $this->getMockBuilder('\phpbb\user_loader')
			->disableOriginalConstructor()
			->getMock();

		$this->auth = $this->getMock('\phpbb\auth\auth');

		$this->config = new \phpbb\config\config(array(
			'zebra_module_id'	=> 'none',
		));

		$this->db = $this->new_dbal();

		$this->request = $this->getMock('\phpbb\request\request');

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();

		$this->user = $this->getMock('\phpbb\user', array(), array(
			new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
			'\phpbb\datetime'
		));

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->notifications_helper = $this->getMockBuilder('\anavaro\zebraenhance\controller\notifyhelper')
			->disableOriginalConstructor()
			->getMock();
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

	public function test_zebra_confirm_add()
	{
		$data = array(
			'mode'	=> 'friend',
			'sql_ary'	=> array(
				array(
					'user_id'	=> 1,
					'zebra_id'	=> 2
				)
			)
		);
		$event_data = array('data');
		$event = new \phpbb\event\data(compact($event_data));
		$this->set_listener();
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.ucp_add_zebra', array($this->listener, 'zebra_confirm_add'));
		$dispatcher->dispatch('core.ucp_add_zebra', $event);
	}
}