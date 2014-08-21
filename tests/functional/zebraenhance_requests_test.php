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
		$this->create_user('testuser');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser'));

		$this->login();
		$this->add_lang('ucp');
		$this->add_lang('common');
		
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
	}
	public function test_own_reqest_cancel()
	{
		$this->login();
		$this->add_lang('ucp');
		
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
		
		$link = $crawler->filter('#ze_slef_req')->filter('span')->filter('a')->first()->link()->getUri();
		
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains('testuser', $crawler->filter('html')->text());
	}
	public function test_user_request_cancel()
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
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		
		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(2)->link()->getUri();
		
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$this->assertNotContains($this->lang('L_YOUR_FRIENDS'), $crawler->filter('html')->text());
		
	}
}