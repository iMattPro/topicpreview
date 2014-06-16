<?php
/**
*
* Topic Preview
*
* @copyright (c) 2014 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\tests\functional;

/**
* @group functional
*/
class topic_preview_acp_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function test_acp_pages()
	{
		self::request('GET', 'adm/index.php?i=\vse\topicpreview\acp\topic_preview_module&amp;mode=settings&sid=' . $this->sid);
	}
}
