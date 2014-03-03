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
class phpbb_functional_topic_preview_test extends extension_functional_test_case
{
	public function setUp()
	{
		parent::setUp();
		$this->login();
		$this->admin_login();
		$this->set_extension('vse', 'topicpreview', 'Topic Preview');
		$this->enable_extension();
		$this->enable_last_post_preview();
		$this->enable_avatars();
	}

	public function enable_last_post_preview()
	{
		$this->get_db();

		$sql = "UPDATE phpbb_config
			SET config_value = 1
			WHERE config_name = 'topic_preview_last_post'";

		$this->db->sql_query($sql);

		$this->purge_cache();
	}

	public function enable_avatars()
	{
		$this->get_db();

		$sql = "UPDATE phpbb_config
			SET config_value = 1
			WHERE config_name = 'topic_preview_avatars'";

		$this->db->sql_query($sql);

		$this->purge_cache();
	}

	public function test_topic_previews()
	{
		// Create and preview a basic topic
		$post = $this->create_topic(2, 'Test Topic 1', 'This is a test topic posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Test creating a reply
		$post2 = $this->create_post(2, $post['topic_id'], 'Re: Test Topic 1', 'This is a test post posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a test post posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a smiley
		$post3 = $this->create_topic(2, 'Test Topic 2', 'This is a second test topic :) posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a second test topic :) posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a bbcode
		$post4 = $this->create_topic(2, 'Test Topic 3', 'This is a third [b]test topic[/b] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a third test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Test topic preview avatars
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertGreaterThan(0, $crawler->filter('.topic_preview_avatar')->count());
		$this->assertContains('no_avatar.gif', $crawler->filter('.topic_preview_avatar > img')->attr('src'));
	}
}
