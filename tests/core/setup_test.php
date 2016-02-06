<?php
/**
*
* Topic Preview
*
* @copyright (c) 2015 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\tests\core;

class setup_test extends base
{
	public function setup_data()
	{
		return array(
			array(
				array(
					'topic_preview_delay' => 1,
					'topic_preview_drift' => 1,
					'topic_preview_width' => 500,
					'topic_preview_limit' => 150,
				),
				array(
					'user_topic_preview' 	=> 1,
					'topic_preview_theme'	=> 'light',
				),
				array(
					'S_TOPICPREVIEW'		=> true,
					'TOPICPREVIEW_THEME'	=> 'light',
					'TOPICPREVIEW_DELAY'	=> 1,
					'TOPICPREVIEW_DRIFT'	=> 1,
					'TOPICPREVIEW_WIDTH'	=> 500,
				),
			),
			array(
				array(
					'topic_preview_delay' => 0,
					'topic_preview_drift' => 0,
					'topic_preview_width' => 0,
					'topic_preview_limit' => 150,
				),
				array(
					'user_topic_preview' 	=> 0,
					'topic_preview_theme'	=> '',
				),
				array(
					'S_TOPICPREVIEW'		=> false,
					'TOPICPREVIEW_THEME'	=> false,
					'TOPICPREVIEW_DELAY'	=> 0,
					'TOPICPREVIEW_DRIFT'	=> 0,
					'TOPICPREVIEW_WIDTH'	=> \vse\topicpreview\core\display::PREVIEW_SIZE,
				),
			),
			array(
				array(
					'topic_preview_delay' => 0,
					'topic_preview_drift' => 0,
					'topic_preview_width' => 0,
					'topic_preview_limit' => 0,
				),
				array(
					'user_topic_preview' 	=> 1,
					'topic_preview_theme'	=> 'null',
				),
				array(
					'S_TOPICPREVIEW'		=> false,
					'TOPICPREVIEW_THEME'	=> false,
					'TOPICPREVIEW_DELAY'	=> 0,
					'TOPICPREVIEW_DRIFT'	=> 0,
					'TOPICPREVIEW_WIDTH'	=> \vse\topicpreview\core\display::PREVIEW_SIZE,
				),
			),
		);
	}

	/**
	 * @dataProvider setup_data
	 */
	public function test_setup($config_map, $user_map, $expected)
	{
		$this->config = new \phpbb\config\config($config_map);
		$this->user->data['user_topic_preview'] = $user_map['user_topic_preview'];
		$this->user->style['topic_preview_theme'] = $user_map['topic_preview_theme'];

		$this->template->expects($this->once())
			->method('assign_vars')
			->with($expected);

		// Get an instance of topic preview display class
		// setup() is called in the class constructor
		$this->get_topic_preview_display();
	}

}
