<?php
/**
*
* @package Topic Preview testing
* @copyright (c) 2014 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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

		$this->add_lang_ext('vse/topicpreview', 'info_acp_topic_preview');

		$crawler = self::request('GET', 'ucp.php?i=ucp_prefs&mode=view&sid=' . $this->sid);
		$this->assertContains($this->lang('TOPIC_PREVIEW_DISPLAY'), $crawler->filter('#cp-main')->text());
	}
}
