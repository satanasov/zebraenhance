<?php
/**
*
* ZebraEnhance test
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace anavaro\zebraenhance\tests\functional;

/**
* @group functional
*/
class zebraenhance_requests_test extends zebraenhance_base
{

	public function test_request()
	{
		//create new user
		$this->create_user('testuser');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser'));
		$this->create_user('testuser1');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser1'));
		$this->create_user('testuser2');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser2'));
		$this->create_user('testuser3');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser2'));

		//login as admin
		$this->login();
		$this->add_lang('ucp');
		$this->add_lang('common');

		//Send friend request
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");

		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		//Check if user request is there
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
	}
	public function test_own_reqest_cancel()
	{
		//login as admin
		$this->login();
		$this->add_lang('ucp');

		//send friend request
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");

		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());

		//check if friend request is present
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());

		//get request URL
		$link = $crawler->filter('#ze_slef_req')->filter('span')->filter('a')->first()->link()->getUri();

		//cancel friend request
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());

		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		//see if friend reques is canceled
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains('testuser', $crawler->filter('html')->text());
	}
	public function test_user_reqest_cancel()
	{
		$this->login();
		$this->add_lang('ucp');

		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");

		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());

		$this->logout();

		$this->login('testuser');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");

		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(1)->link()->getUri();

		$this->assertContains('2', $link);

		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains($this->lang('UCP_ZEBRA_PENDING_OUT'), $crawler->filter('html')->text());
	}
	public function test_user_reqest_accept()
	{
		$this->login();
		$this->add_lang('ucp');

		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());

		$this->logout();

		$this->login('testuser');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());

		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(0)->link()->getUri();

		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());
		$this->assertContains('admin', $crawler->filter('#ze_ajaxify')->text());
	}
	public function test_remove_friend()
	{
		$this->login();
		$this->add_lang('ucp');

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$link = $crawler->filter('#ze_ajaxify')->filter('a')->eq(2)->link()->getUri();

		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertCount(1,$crawler->filter('html')->text(), 'testuser');
		$this->assertEquals(0, $crawler->filter('#ze_ajaxify')->count());

		$this->logout();

		$this->login('testuser');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains('admin', $crawler->filter('html')->text());
		$this->assertEquals(0, $crawler->filter('#ze_ajaxify')->count());
		$this->logout();
	}

	public function test_togle_bff()
	{
		$this->login();
		//we create friends
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->logout();

		$this->login('testuser');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());
		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(0)->link()->getUri();
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$link = $crawler->filter('#ze_ajaxify')->filter('a')->eq(0)->attr('href');

		//togle bff
		$crw1 = self::request('GET', substr($link, strpos($link, 'app.')), array(), array(), array('CONTENT_TYPE'	=> 'application/json'));

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('favorite_remove.png', $crawler->filter('#ze_ajaxify')->filter('a')->eq(0)->filter('img')->attr('src'));
		$this->logout();

		$this->login();
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$link = $crawler->filter('#ze_ajaxify')->filter('a')->eq(0)->attr('href');

		//togle bff
		$crw1 = self::request('GET', substr($link, strpos($link, 'app.')), array(), array(), array('CONTENT_TYPE'	=> 'application/json'));

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('favorite_remove.png', $crawler->filter('#ze_ajaxify')->filter('a')->eq(0)->filter('img')->attr('src'));
		$this->logout();

		$this->login();
		//we create friends
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser1&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->logout();

		$this->login('testuser1');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());
		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(0)->link()->getUri();
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->logout();

		$this->login();
		//we create friends
		$crawler = self::request('GET', "ucp.php?i=zebra&mode=foes&add=testuser2&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->logout();
	}

	public function list_visibility_data()
	{
		return array(
			'none'	=> array(
				5, //State
				'testuser', // test user
				'You do not have access to see user',
			),
			'bff'	=> array(
				4,
				'testuser',
				'testuser'
			),
			'bff_no'	=> array(
				4,
				'testuser1',
				'You do not have access to see user'
			),
			'friend'	=> array(
				3,
				'testuser1',
				'testuser1'
			),
			'friend_foe'	=> array(
				3,
				'testuser2',
				'You do not have access to see user'
			),
			'friend_reg'	=> array(
				3,
				'testuser3',
				'You do not have access to see user'
			),
			'not_foe'	=> array(
				2,
				'testuser2',
				'You do not have access to see user'
			),
			'not_foe_true' => array(
				2,
				'testuser3',
				'testuser'
			),
		);
	}

	/**
	* Test test_friend_list_visibility
	*
	* @dataProvider list_visibility_data
	*/
	public function test_friend_list_visibility($state, $user, $expected)
	{
		$this->login();
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['zebra_profile_acl'] = $state;
		$crawler = self::submit($form);
		$this->logout();

		$this->login($user);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertContains($expected, $crawler->filter('html')->filter('div#ze_container')->text());
		$this->logout();
	}
}
