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
	protected static function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function test_acp_pages()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('vse/topicpreview', 'topic_preview_acp');

		$crawler = self::request('GET', 'adm/index.php?i=\vse\topicpreview\acp\topic_preview_module&amp;mode=settings&sid=' . $this->sid);
		$this->assertContainsLang('TOPIC_PREVIEW', $crawler->text());
		$this->assertContainsLang('TOPIC_PREVIEW_EXPLAIN', $crawler->text());
	}
}
