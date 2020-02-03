<?php
/**
*
* Zebra enhance
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace anavaro\zebraenhance\tests\functional;

/**
* @group functional
*/
class zebraenhance_base extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('anavaro/zebraenhance');
	}

	public function setUp() : void
	{
		parent::setUp();
	}
}
