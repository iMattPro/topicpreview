<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2014 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\core;

class display_topic_preview_test extends base
{
	/** @var \phpbb\avatar\driver\local|\PHPUnit\Framework\MockObject\MockObject */
	protected $avatar_driver;

	/** @var array Avatar data to use in tests below */
	protected static $avatar_data = array(
		'src' => 'some-avatar.jpg',
		'width' => '60',
		'height' => '60',
	);

	protected function setUp(): void
	{
		parent::setUp();

		global $config, $phpbb_container, $request, $phpbb_root_path, $user;

		// Set up a mock for avatar.driver.local
		$this->config['allow_avatar_local'] = true;
		$this->avatar_driver = $this->createMock('\phpbb\avatar\driver\local');
		$this->avatar_driver->method('get_name')
			->willReturn('avatar.driver.local');
		$this->avatar_driver->method('get_config_name')
			->willReturn('local');
		$this->avatar_driver->method('get_data')
			->willReturn(self::$avatar_data);
		$this->avatar_helper->method('get_user_avatar')
			->willReturn(['html' => '<img class="avatar" src="' . $phpbb_root_path . 'styles/prosilver/theme/images/no_avatar.gif" data-src="' . self::$avatar_data['src'] . '" width="' . self::$avatar_data['width'] . '" height="' . self::$avatar_data['height'] . '" alt="USER_AVATAR" />']);

		/** @var \phpbb\request\request|\PHPUnit\Framework\MockObject\MockObject $request */
		$request = $this->createMock('\phpbb\request\request');

		$path_helper = $this->getMockBuilder('\phpbb\path_helper')
			->disableOriginalConstructor()
			->onlyMethods(array('get_web_root_path'))
			->getMock();
		$path_helper->method('get_web_root_path')
			->willReturn($phpbb_root_path);

		/** @var \phpbb\event\dispatcher_interface|\PHPUnit\Framework\MockObject\MockObject $dispatcher */
		$dispatcher = $this->createMock('\phpbb\event\dispatcher_interface');

		$phpbb_container = new \phpbb_mock_container_builder();
		$phpbb_container->set('avatar.manager', new \phpbb\avatar\manager($config, $dispatcher, array($this->avatar_driver)));
		$phpbb_container->set('path_helper', $path_helper);

		$user->style['style_path'] = 'prosilver';
	}

	public static function topic_preview_display_data()
	{
		global $phpbb_root_path;
		$lazy_avatar = '<img class="avatar" src="' . $phpbb_root_path . 'styles/prosilver/theme/images/no_avatar.gif" data-src="%s" width="' . self::$avatar_data['width'] . '" height="' . self::$avatar_data['height'] . '" alt="USER_AVATAR" />';

		return array(
			array(
				array(
					'first_post_text' => 'First message',
					'fp_user_avatar' => self::$avatar_data['src'],
					'fp_user_avatar_type' => 'avatar.driver.local',
					'fp_user_avatar_width' => '',
					'fp_user_avatar_height' => '',
					'topic_first_post_id' => 1,
					'last_post_text' => '',
					'lp_user_avatar' => '',
					'lp_user_avatar_type' => 0,
					'topic_last_post_id' => 1,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'First message',
					'TOPIC_PREVIEW_FIRST_AVATAR' => sprintf($lazy_avatar, self::$avatar_data['src']),
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
			array(
				array(
					'first_post_text' => '<r>Second message <B><s>[b]</s>with bold text<e>[/b]</e></B> <E>:)</E> and smiley</r>',
					'fp_user_avatar' => self::$avatar_data['src'],
					'fp_user_avatar_type' => 'avatar.driver.local',
					'fp_user_avatar_width' => '',
					'fp_user_avatar_height' => '',
					'topic_first_post_id' => 2,
					'last_post_text' => str_repeat ('a', 155),
					'lp_user_avatar' => self::$avatar_data['src'],
					'lp_user_avatar_type' => 'avatar.driver.local',
					'lp_user_avatar_width' => '',
					'lp_user_avatar_height' => '',
					'topic_last_post_id' => 3,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Second message with bold text :) and smiley',
					'TOPIC_PREVIEW_FIRST_AVATAR' => sprintf($lazy_avatar, self::$avatar_data['src']),
					'TOPIC_PREVIEW_LAST_POST' => str_repeat ('a', 150) . '...',
					'TOPIC_PREVIEW_LAST_AVATAR' => sprintf($lazy_avatar, self::$avatar_data['src']),
				),
			),
			array(
				array(
					'first_post_text' => '<r>Third message with <URL url="http://google.com">http://google.com</URL> magic url and <EMAIL email="test@google.com">test@google.com</EMAIL> email</r>',
					'fp_user_avatar' => '',
					'fp_user_avatar_type' => 0,
					'fp_user_avatar_width' => '',
					'fp_user_avatar_height' => '',
					'topic_first_post_id' => 4,
					'last_post_text' => str_repeat ('a', 155),
					'lp_user_avatar' => self::$avatar_data['src'],
					'lp_user_avatar_type' => 'avatar.driver.local',
					'lp_user_avatar_width' => '',
					'lp_user_avatar_height' => '',
					'topic_last_post_id' => 5,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Third message with magic url and test@google.com email',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => str_repeat ('a', 150) . '...',
					'TOPIC_PREVIEW_LAST_AVATAR' => sprintf($lazy_avatar, self::$avatar_data['src']),
				),
			),
			array(
				array(
					'first_post_text' => '',
					'fp_user_avatar' => '',
					'fp_user_avatar_type' => 0,
					'fp_user_avatar_width' => '',
					'fp_user_avatar_height' => '',
					'topic_first_post_id' => 6,
					'last_post_text' => '',
					'lp_user_avatar' => '',
					'lp_user_avatar_type' => 0,
					'lp_user_avatar_width' => '',
					'lp_user_avatar_height' => '',
					'topic_last_post_id' => 7,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => '',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
			array(
				array(
					'first_post_text' => '<r>Fourth message <QUOTE><s>[quote]</s>' . str_repeat('aaa ', 60) . '<e>[/quote]</e></QUOTE></r>',
					'fp_user_avatar' => null,
					'fp_user_avatar_type' => 0,
					'fp_user_avatar_width' => '',
					'fp_user_avatar_height' => '',
					'topic_first_post_id' => 8,
					'last_post_text' =>'',
					'lp_user_avatar' => null,
					'lp_user_avatar_type' => 0,
					'lp_user_avatar_width' => '',
					'lp_user_avatar_height' => '',
					'topic_last_post_id' => 8,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Fourth message aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa...',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
		);
	}

	/**
	* @dataProvider topic_preview_display_data
	*/
	public function test_display_topic_preview($data, $expected)
	{
		// add this to prevent undefined key errors in PHP8
		$data['forum_id'] = 1;

		// Disable topic preview avatars
		if ($data['fp_user_avatar'] === null || $data['lp_user_avatar'] === null)
		{
			$this->config['topic_preview_avatars'] = 0;
		}

		// Start with an empty block array
		$block = array();

		// Get an instance of topic preview display class
		$preview_display = $this->get_topic_preview_display();

		// Update the block array with topic preview data
		$block = $preview_display->display_topic_preview($data, $block);

		// Test that we get the expected result
		self::assertEquals($expected, $block);
	}

	public function test_display_topic_preview_disabled()
	{
		// Disable topic preview
		$this->config['topic_preview_limit'] = 0;

		// Start with an empty block and data arrays
		$block = $data = array(0);

		// Get an instance of topic preview display class
		$preview_display = $this->get_topic_preview_display();

		// Test that we get back the unmodified block array
		self::assertEquals($block, $preview_display->display_topic_preview($data, $block));
	}

	public static function topic_preview_display_with_attachments_data()
	{
		return array(
			array(
				array(
					'first_post_text' => '<r>Fourth message <QUOTE><s>[quote]</s>' . str_repeat('aaa ', 60) . '<e>[/quote]</e></QUOTE></r>',
					'fp_user_avatar' => null,
					'fp_user_avatar_type' => 0,
					'fp_user_avatar_width' => '',
					'fp_user_avatar_height' => '',
					'topic_first_post_id' => 8,
					'last_post_text' =>'',
					'lp_user_avatar' => null,
					'lp_user_avatar_type' => 0,
					'lp_user_avatar_width' => '',
					'lp_user_avatar_height' => '',
					'topic_last_post_id' => 8,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Fourth message aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa aaa...',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
		);
	}

	/**
	 * @dataProvider topic_preview_display_with_attachments_data
	 */
	public function test_display_topic_preview_with_attachments($data, $expected)
	{
		$this->config['topic_preview_rich_text'] = 1;
		$this->config['topic_preview_rich_attachments'] = 1;

		// add this to prevent undefined key errors in PHP8
		$data['forum_id'] = 1;

		// Disable topic preview avatars
		if ($data['fp_user_avatar'] === null || $data['lp_user_avatar'] === null)
		{
			$this->config['topic_preview_avatars'] = 0;
		}

		// Start with an empty block array
		$block = array();

		// Get an instance of topic preview display class
		$preview_display = $this->get_topic_preview_display();

		// Assert attachments are enabled
		self::assertTrue($preview_display->attachments_enabled());
		$attachments = [
			1 => [
				['attach_id' => 1, 'real_filename' => 'test1.jpg'],
				['attach_id' => 2, 'real_filename' => 'test2.pdf'],
			],
			2 => [
				['attach_id' => 3, 'real_filename' => 'test3.png'],
			],
		];

		$preview_display->set_attachments_cache($attachments);

		// Verify that attachments are stored in the attachments_cache property
		$reflection = new \ReflectionClass($preview_display);
		$property = $reflection->getProperty('attachments_cache');

		self::assertEquals($attachments, $property->getValue($preview_display));

		// Update the block array with topic preview data
		$block = $preview_display->display_topic_preview($data, $block);

		// Test that we get the expected result
		self::assertEquals($expected, $block);
	}
}
