<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2025 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\functional;

/**
 * @group functional
 */
class topic_preview_scrollable_test extends \phpbb_functional_test_case
{
	protected static function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function test_scrollable_tooltip_javascript()
	{
		$this->login();
		$this->admin_login();

		// Enable topic preview using the correct module path
		$crawler = self::request('GET', 'adm/index.php?i=\\vse\\topicpreview\\acp\\topic_preview_module&amp;mode=settings&sid=' . $this->sid);
		$form = $crawler->selectButton('Submit')->form();
		$form['topic_preview_limit'] = 150;
		$form['topic_preview_avatars'] = 1;
		$form['topic_preview_last_post'] = 1;
		self::submit($form);

		$this->logout();

		// Create a test topic with content
		$this->login();
		$this->create_topic(2, 'Test Topic for Scrollable Preview', 'This is a test topic with some content that should appear in the scrollable preview tooltip. It has enough content to test the scrolling functionality.');

		// Visit the forum page
		$crawler = self::request('GET', 'viewforum.php?f=2&sid=' . $this->sid);

		// Check that the topic preview JavaScript is loaded
		$this->assertStringContainsString('topicpreview.js', $crawler->html());

		// Check that the scrollable functionality is available
		$this->assertStringContainsString('topicPreview', $crawler->html());

		// Check that the topic preview container is present
		$this->assertGreaterThan(0, $crawler->filter('.topic_preview_content')->count());
	}

	public function test_no_none_theme_option()
	{
		$this->login();
		$this->admin_login();

		// Visit the ACP settings page using a correct module path
		$crawler = self::request('GET', 'adm/index.php?i=\\vse\\topicpreview\\acp\\topic_preview_module&amp;mode=settings&sid=' . $this->sid);

		// Check that the "none" theme option is not available
		$theme_options = $crawler->filter('select[name*="style_"] option')->extract(array('value'));
		$this->assertNotContains('no', $theme_options);
		$this->assertNotContains('none', $theme_options);

		// Verify that valid themes are still available
		$this->assertContains('light', $theme_options);
	}
}
