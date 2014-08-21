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
class zebraenhance_requests_cancel_test extends zebraenhance_base
{

	public function own_request_test()
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
}