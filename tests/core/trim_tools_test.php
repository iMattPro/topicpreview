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

class trim_tools_test extends \phpbb_test_case
{
	public static function trim_tools_data()
	{
		return array(
			array(
				'First message',
				'First message',
			),
			array(
				str_repeat ('a', 155),
				str_repeat ('a', 150) . '...',
			),
			array(
				str_repeat ('á', 155),
				str_repeat ('á', 150) . '...',
			),
			array(
				'Second message [b:3o8ohvlj]with bold text[/b:3o8ohvlj] <!-- s:) --><img src="{SMILIES_PATH}/icon_e_smile.gif" alt=":)" title="Smile" /><!-- s:) --> and smiley',
				'Second message with bold text :) and smiley',
			),
			array(
				'Third message with <!-- m --><a class="postlink" href="http://google.com">http://google.com</a><!-- m --> magic url and <!-- e --><a href="mailto:test@google.com">test@google.com</a><!-- e --> email',
				'Third message with magic url and test@google.com email',
			),
			array(
				'Fourth message [quote:3o8ohvlj]' . str_repeat('aaa ', 600) . '[/quote:3o8ohvlj]',
				'Fourth message',
			),
			array(
				'This is a fifth [b:3o8ohvlj]test topic[/b:3o8ohvlj] with [quote:3o8ohvlj]nested content inside of [quote:3o8ohvlj][i:3o8ohvlj][b:3o8ohvlj]nested[/b:3o8ohvlj] [u:3o8ohvlj]content[/u:3o8ohvlj][/i:3o8ohvlj][/quote:3o8ohvlj][/quote:3o8ohvlj] content [quote:3o8ohvlj]on top of more content[/quote:3o8ohvlj] posted by the testing framework.',
				'This is a fifth test topic with content posted by the testing framework.',
			),
			array(
				'',
				'',
			),
		);
	}

	/**
	* @dataProvider trim_tools_data
	*/
	public function test_trim_tools($message, $expected)
	{
		global $config;

		$config = new \phpbb\config\config(array(
			'topic_preview_limit'			=> 150,
			'topic_preview_strip_bbcodes'	=> 'quote',
		));

		$trim_tools = new \vse\topicpreview\core\trim_tools($config);

		$this->assertEquals($expected, $trim_tools->trim_text($message));
	}
}
