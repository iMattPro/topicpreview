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

class display_topic_preview_test extends topic_preview_base
{
	public function setUp()
	{
		parent::setUp();

		global $config, $phpbb_container;
		$phpbb_container = new \phpbb_mock_container_builder();
		$phpbb_container->set('avatar.manager', new \phpbb\avatar\manager($config, array()));
	}

	public static function topic_preview_display_data()
	{
		return array(
			array(
				array(
					'first_post_text' => 'First message',
					'first_poster_avatar' => '',
					'first_poster_avatar_type' => 0,
					'topic_first_post_id' => 1,
					'last_post_text' => '',
					'last_poster_avatar' => '',
					'last_poster_avatar_type' => 0,
					'topic_last_post_id' => 1,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'First message',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => '',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
			array(
				array(
					'first_post_text' => 'Second message [b:3o8ohvlj]with bold text[/b:3o8ohvlj] <!-- s:) --><img src="{SMILIES_PATH}/icon_e_smile.gif" alt=":)" title="Smile" /><!-- s:) --> and smiley',
					'first_poster_avatar' => '',
					'first_poster_avatar_type' => 0,
					'topic_first_post_id' => 2,
					'last_post_text' => str_repeat ('a', 155),
					'last_poster_avatar' => '',
					'last_poster_avatar_type' => 0,
					'topic_last_post_id' => 3,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Second message with bold text :) and smiley',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => str_repeat ('a', 150) . '...',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
			array(
				array(
					'first_post_text' => 'Third message with <!-- m --><a class="postlink" href="http://google.com">http://google.com</a><!-- m --> magic url and <!-- e --><a href="mailto:test@google.com">test@google.com</a><!-- e --> email',
					'first_poster_avatar' => '',
					'first_poster_avatar_type' => 0,
					'topic_first_post_id' => 4,
					'last_post_text' => str_repeat ('a', 155),
					'last_poster_avatar' => '',
					'last_poster_avatar_type' => 0,
					'topic_last_post_id' => 5,
				),
				array(
					'TOPIC_PREVIEW_FIRST_POST' => 'Third message with magic url and test@google.com email',
					'TOPIC_PREVIEW_FIRST_AVATAR' => '',
					'TOPIC_PREVIEW_LAST_POST' => str_repeat ('a', 150) . '...',
					'TOPIC_PREVIEW_LAST_AVATAR' => '',
				),
			),
			array(
				array(
					'first_post_text' => '',
					'first_poster_avatar' => '',
					'first_poster_avatar_type' => 0,
					'topic_first_post_id' => 6,
					'last_post_text' => '',
					'last_poster_avatar' => '',
					'last_poster_avatar_type' => 0,
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
					'first_post_text' => 'Fourth message [quote:3o8ohvlj]' . str_repeat('aaa ', 600) . '[/quote:3o8ohvlj]',
					'first_poster_avatar' => '',
					'first_poster_avatar_type' => 0,
					'topic_first_post_id' => 8,
					'last_post_text' =>'',
					'last_poster_avatar' => '',
					'last_poster_avatar_type' => 0,
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
		// Start with an empty block array
		$block = array();

		// Get an instance of topic preview class
		$topic_preview_manager = $this->topic_preview_manager();

		// Make sure setup has been run
		$topic_preview_manager->setup();

		// Set the expected avatar data here
		$expected['TOPIC_PREVIEW_FIRST_AVATAR'] = $expected['TOPIC_PREVIEW_LAST_AVATAR'] = $topic_preview_manager->tp_avatar_fallback();

		// Update the block array with topic preview data
		$block = $topic_preview_manager->display_topic_preview($data, $block);

		// Test that we get the expected result
		$this->assertEquals($expected, $block);
	}
}
