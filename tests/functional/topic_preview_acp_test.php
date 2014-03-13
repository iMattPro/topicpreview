<?php
/**
*
* @package testing
* @copyright (c) 2014 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @group functional
*/
class extension_functional_topic_preview_acp_test extends extension_functional_test_case
{
	public function setUp()
	{
		parent::setUp();
		$this->login();
		$this->admin_login();
		$this->set_extension('vse', 'topicpreview', 'Topic Preview');
		$this->enable_extension();
	}

	public function test_acp_pages()
	{
		$crawler = self::request('GET', 'adm/index.php?i=\vse\topicpreview\acp\topic_preview_module&amp;mode=settings&sid=' . $this->sid);
	}
}
