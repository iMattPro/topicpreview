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
class topic_preview_ucp_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function test_ucp_pages()
	{
		$this->login();

		$this->add_lang_ext('vse/topicpreview', 'topic_preview_ucp');

		$crawler = self::request('GET', 'ucp.php?i=ucp_prefs&mode=view&sid=' . $this->sid);
		$this->assertContains($this->lang('TOPIC_PREVIEW_DISPLAY'), $crawler->filter('#cp-main')->text());
	}
}
