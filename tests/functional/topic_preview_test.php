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
class topic_preview_test extends \phpbb_functional_test_case
{
	protected static function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function test_topic_previews()
	{
		global $config;

		$db = $this->get_db();
		$sql = 'UPDATE ' . CONFIG_TABLE . " SET config_value = 1 WHERE config_name = 'topic_preview_last_post'";
		$db->sql_query($sql);
		$config['topic_preview_last_post'] = 1;
		$sql = 'UPDATE ' . CONFIG_TABLE . " SET config_value = 1 WHERE config_name = 'topic_preview_avatars'";
		$db->sql_query($sql);
		$config['topic_preview_avatars'] = 1;
		$sql = 'UPDATE ' . CONFIG_TABLE . " SET config_value = 'quote' WHERE config_name = 'topic_preview_strip_bbcodes'";
		$db->sql_query($sql);
		$config['topic_preview_strip_bbcodes'] = 'quote';

		$this->purge_cache();

		$this->login();

		// Create and preview a basic topic
		$post = $this->create_topic(2, 'Test Topic 1', 'This is a test topic posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Create a second post and test last post previews
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

		// Create and preview a topic with a stripped bbcode
		$post4 = $this->create_topic(2, 'Test Topic 4', 'This is a fourth [quote]' . str_repeat('aaa ', 600) . '[/quote] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a fourth posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a stripped nested bbcodes
		$post5 = $this->create_topic(2, 'Test Topic 5', 'This is a fifth [b]test topic[/b] with [quote]nested content inside of [quote][i][b]nested[/b] [u]content[/u][/i][/quote][/quote] content [quote]on top of more content[/quote] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertContains('This is a fifth test topic with content posted by the testing framework.', $crawler->filter('html')->text());

		// Test topic preview avatars
		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
		$this->assertGreaterThan(0, $crawler->filter('.topic_preview_avatar')->count());
		$this->assertContains('topic_preview_no_avatar', $crawler->filter('.topic_preview_avatar > div')->attr('class'));
	}
}
