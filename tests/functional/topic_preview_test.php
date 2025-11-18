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

	public function test_plain_text_previews()
	{
		$this->login();
		$this->admin_login();

		// Enable topic preview using the correct module path
		$crawler = self::request('GET', 'adm/index.php?i=\\vse\\topicpreview\\acp\\topic_preview_module&amp;mode=settings&sid=' . $this->sid);
		$form = $crawler->selectButton('Submit')->form();
		$form['topic_preview_limit'] = 150;
		$form['topic_preview_avatars'] = 1;
		$form['topic_preview_last_post'] = 1;
		$form['topic_preview_rich_text'] = 0;
		$form['topic_preview_strip_bbcodes'] = 'quote';
		self::submit($form);

		// Create and preview a basic topic
		$post = $this->create_topic(2, 'Test Topic 1', 'This is a test topic posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Create a second post and test last post previews
		$post2 = $this->create_post(2, $post['topic_id'], 'Re: Test Topic 1', 'This is a test post posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a test post posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a smiley
		$post3 = $this->create_topic(2, 'Test Topic 2', 'This is a second test topic :) posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a second test topic :) posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a bbcode
		$post4 = $this->create_topic(2, 'Test Topic 3', 'This is a third [b]test topic[/b] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a third test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a stripped bbcode
		$post4 = $this->create_topic(2, 'Test Topic 4', 'This is a fourth [quote]' . str_repeat('aaa ', 60) . '[/quote] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a fourth posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with a stripped nested bbcodes
		$post5 = $this->create_topic(2, 'Test Topic 5', 'This is a fifth [b]test topic[/b] with [quote]nested content inside of [quote][i][b]nested[/b] [u]content[/u][/i][/quote][/quote] content [quote]on top of more content[/quote] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a fifth test topic with content posted by the testing framework.', $crawler->filter('html')->text());

		// Test topic preview avatars
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertGreaterThan(0, $crawler->filter('.topic_preview_avatar')->count());
		self::assertStringContainsString('topic_preview_no_avatar', $crawler->filter('.topic_preview_avatar > div')->attr('class'));
	}

	public function test_rich_text_previews()
	{
		$this->login();
		$this->admin_login();

		// Enable topic preview using the correct module path
		$crawler = self::request('GET', 'adm/index.php?i=\\vse\\topicpreview\\acp\\topic_preview_module&amp;mode=settings&sid=' . $this->sid);
		$form = $crawler->selectButton('Submit')->form();
		$form['topic_preview_limit'] = 150;
		$form['topic_preview_avatars'] = 1;
		$form['topic_preview_last_post'] = 1;
		$form['topic_preview_rich_text'] = 1;
		$form['topic_preview_strip_bbcodes'] = 'quote';
		self::submit($form);

		// Create and preview a basic topic
		$this->create_topic(2, 'Test Topic 6', 'This is a sixth test topic posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a sixth test topic posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with smiley and emoji
		$this->create_topic(2, 'Test Topic 7', 'This is a seventh test topic with :) and 😀 posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a seventh test topic with <img class="smilies" src="./images/smilies/icon_e_smile.gif" width="15" height="17" alt=":)" title="Smile"> and <img alt="😀" class="emoji smilies" draggable="false" src="//cdn.jsdelivr.net/gh/twitter/twemoji@latest/assets/svg/1f600.svg"> posted by the testing framework.', $crawler->filter('html')->html());

		// Create and preview a topic with a bbcode
		$this->create_topic(2, 'Test Topic 8', 'This is a eighth [b]test topic[/b] with bbcodes posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a eighth <strong class="text-strong">test topic</strong> with bbcodes posted by the testing framework.', $crawler->filter('html')->html());

		// Create and preview a topic with a stripped bbcode
		$this->create_topic(2, 'Test Topic 9', 'This is a ninth test topic [quote]' . str_repeat('aaa ', 60) . '[/quote] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a ninth test topic  posted by the testing framework.', $crawler->filter('html')->text());

		// Create and preview a topic with nested bbcodes
		$this->create_topic(2, 'Test Topic 10', 'This is a tenth [b]test topic[/b] with [b]nested content inside of [b][i][b]nested[/b] [u]content[/u][/i][/b][/b] content [b]on top of more content[/b] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is a tenth <strong class="text-strong">test topic</strong> with <strong class="text-strong">nested content inside of <strong class="text-strong"><em class="text-italics"><strong class="text-strong">nested</strong> <span style="text-decoration:underline">content</span></em></strong></strong> content <strong class="text-strong">on top of more content</strong> posted by the testing framework.', $crawler->filter('html')->html());

		// Create and preview a topic with trimming inside bbcode
		$this->create_topic(2, 'Test Topic 11', 'This is eleventh topic [b]' . str_repeat('foobar ', 40) . '[/b] posted by the testing framework.');
		$crawler = self::request('GET', "viewforum.php?f=2&sid=$this->sid");
		self::assertStringContainsString('This is eleventh topic <strong class="text-strong">' . trim(str_repeat('foobar ', 18)) . '</strong>...', $crawler->filter('html')->html());
	}
}
