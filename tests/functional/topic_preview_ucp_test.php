<?php
/**
*
* @package Topic Preview testing
* @copyright (c) 2014 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @group functional
*/
class extension_functional_topic_preview_ucp_test extends extension_functional_test_case
{
	public function setUp()
	{
		parent::setUp();
		$this->login();
		$this->admin_login();
		$this->set_extension('vse', 'topicpreview', 'Topic Preview');
		$this->enable_extension();
	}

	public function test_ucp_pages()
	{
		$this->add_lang_ext('info_acp_topic_preview');

		$crawler = self::request('GET', 'ucp.php?i=ucp_prefs&mode=view&sid=' . $this->sid);
		$this->assertContains($this->lang('TOPIC_PREVIEW_DISPLAY'), $crawler->filter('#cp-main')->text());
	}
}
