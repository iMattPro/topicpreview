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
	/** @var \phpbb\avatar\driver\local|\PHPUnit_Framework_MockObject_MockObject */
	protected $avatar_driver;

	/** @var array Avatar data to use in tests below */
	static protected $avatar_data = array(
		'src' => 'avatar.jpg',
		'width' => '60',
		'height' => '60',
	);

	public function setUp()
	{
		parent::setUp();

		global $config, $phpbb_container, $request, $phpbb_root_path;

		// Set up a mock for avatar.driver.local
		$this->config['allow_avatar_local'] = true;
		$this->avatar_driver = $this->getMockBuilder('\phpbb\avatar\driver\local')
			->disableOriginalConstructor()
			->getMock();
		$this->avatar_driver->expects($this->any())
			->method('get_name')
			->will($this->returnValue('avatar.driver.local'));
		$this->avatar_driver->expects($this->any())
			->method('get_config_name')
			->will($this->returnValue('local'));
		$this->avatar_driver->expects($this->any())
			->method('get_data')
			->will($this->returnValue(self::$avatar_data));

		/** @var \phpbb\request\request|\PHPUnit_Framework_MockObject_MockObject $request */
		$request = $this->getMock('\phpbb\request\request');

		$phpbb_path_helper = new \phpbb\path_helper(
			new \phpbb\symfony_request(
				new \phpbb_mock_request()
			),
			new \phpbb\filesystem\filesystem(),
			$request,
			$phpbb_root_path,
			'php'
		);

		$phpbb_container = new \phpbb_mock_container_builder();
		$phpbb_container->set('avatar.manager', new \phpbb\avatar\manager($config, array($this->avatar_driver)));
		$phpbb_container->set('path_helper', $phpbb_path_helper);
	}

	public function topic_preview_display_data()
	{
		global $phpbb_root_path;
		$no_avatar = '<img class="avatar" src="' . $phpbb_root_path . 'styles/prosilver/theme/images/no_avatar.gif" width="' . self::$avatar_data['width'] . '" height="' . self::$avatar_data['height'] . '" alt="User avatar" />';
		$lazy_avatar = '<img class="avatar" src="' . $phpbb_root_path . 'styles/prosilver/theme/images/no_avatar.gif" data-src="%s" width="' . self::$avatar_data['width'] . '" height="' . self::$avatar_data['height'] . '" alt="USER_AVATAR" />';

		return array(
			array(
				array(
					'first_post_text' => 'First message',
					'fp_avatar' => self::$avatar_data['src'],
					'fp_avatar_type' => 'avatar.driver.local',
					'topic_first_post_id' => 1,
					'last_post_text' => '',
					'lp_avatar' => '',
					'lp_avatar_type' => 0,
					'topic_last_post_id' => 1,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'First message',
					'TOPIC_PREVIEW_FIRST_AVATAR' => sprintf($lazy_avatar, self::$avatar_data['src']),
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => $no_avatar,
				),
			),
			array(
				array(
					'first_post_text' => 'Second message [b:3o8ohvlj]with bold text[/b:3o8ohvlj] <!-- s:) --><img src="{SMILIES_PATH}/icon_e_smile.gif" alt=":)" title="Smile" /><!-- s:) --> and smiley',
					'fp_avatar' => self::$avatar_data['src'],
					'fp_avatar_type' => 'avatar.driver.local',
					'topic_first_post_id' => 2,
					'last_post_text' => str_repeat ('a', 155),
					'lp_avatar' => self::$avatar_data['src'],
					'lp_avatar_type' => 'avatar.driver.local',
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
					'first_post_text' => 'Third message with <!-- m --><a class="postlink" href="http://google.com">http://google.com</a><!-- m --> magic url and <!-- e --><a href="mailto:test@google.com">test@google.com</a><!-- e --> email',
					'fp_avatar' => '',
					'fp_avatar_type' => 0,
					'topic_first_post_id' => 4,
					'last_post_text' => str_repeat ('a', 155),
					'lp_avatar' => self::$avatar_data['src'],
					'lp_avatar_type' => 'avatar.driver.local',
					'topic_last_post_id' => 5,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Third message with magic url and test@google.com email',
					'TOPIC_PREVIEW_FIRST_AVATAR' => $no_avatar,
					'TOPIC_PREVIEW_LAST_POST' => str_repeat ('a', 150) . '...',
					'TOPIC_PREVIEW_LAST_AVATAR' => sprintf($lazy_avatar, self::$avatar_data['src']),
				),
			),
			array(
				array(
					'first_post_text' => '',
					'fp_avatar' => '',
					'fp_avatar_type' => 0,
					'topic_first_post_id' => 6,
					'last_post_text' => '',
					'lp_avatar' => '',
					'lp_avatar_type' => 0,
					'topic_last_post_id' => 7,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => '',
					'TOPIC_PREVIEW_FIRST_AVATAR' => $no_avatar,
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => $no_avatar,
				),
			),
			array(
				array(
					'first_post_text' => 'Fourth message [quote:3o8ohvlj]' . str_repeat('aaa ', 600) . '[/quote:3o8ohvlj]',
					'fp_avatar' => null,
					'fp_avatar_type' => 0,
					'topic_first_post_id' => 8,
					'last_post_text' =>'',
					'lp_avatar' => null,
					'lp_avatar_type' => 0,
					'topic_last_post_id' => 8,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Fourth message',
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
		// Disable topic preview avatars
		if ($data['fp_avatar'] === null || $data['lp_avatar'] === null)
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
		$this->assertEquals($expected, $block);
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
		$this->assertEquals($block, $preview_display->display_topic_preview($data, $block));
	}
}
