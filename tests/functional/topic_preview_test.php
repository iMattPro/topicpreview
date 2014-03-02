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
	}

	public function test_preview_new_topic()
	{
		// Test creating topic
		$post = $this->create_topic(2, 'Test Topic 1', 'This is a test topic posted by the testing framework.');

		$crawler = self::request('GET', 'viewforum.php?f=2');
		$this->assertGreaterThan(0, $crawler->filter('.topiclist')->count());

//		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
//		$this->assertContains('This is a test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Test creating a post with smiley
//		$post2 = $this->create_topic(2, 'Test Topic 2', 'This is another test topic :) posted by the testing framework.');

//		$crawler = self::request('GET', "viewforum.php?f=2&sid={$this->sid}");
//		$this->assertContains('This is another test topic :) posted by the testing framework.', $crawler->filter('html')->text());

		// Test quoting a message
// 		$crawler = self::request('GET', "posting.php?mode=quote&f=2&t={$post2['topic_id']}&p={$post2['post_id']}&sid={$this->sid}");
// 		$this->assertContains('This is a test post posted by the testing framework.', $crawler->filter('html')->text());
	}
}
