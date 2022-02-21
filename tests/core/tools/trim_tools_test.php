<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\core\tools;

class trim_tools_test extends base
{
	public function trim_tools_data()
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
				'Second message [b]with bold text[/b] :) and smiley',
				'Second message with bold text :) and smiley',
			),
			array(
				'Third message with [url]http://google.com">http://google.com[/url] magic url and [email]test@google.com[/email] email',
				'Third message with magic url and test@google.com email',
			),
			array(
				'Fourth message [quote]' . str_repeat('aaa ', 600) . '[/quote]',
				'Fourth message',
			),
			array(
				'This is a fifth [b]test topic[/b] with [quote="u1"]nested content inside of [quote="u1"][i][b]nested[/b] [u]content[/u][/i][/quote][/quote] content [quote="u1"]on top of more content[/quote] posted by the testing framework.',
				'This is a fifth test topic with content posted by the testing framework.',
			),
			array(
				'This is a sixth [b]test topic[/b] with empty [quote="u1"]stuff[quote="u1"][/quote][/quote] content [quote="u1"][/quote] posted by the testing framework.',
				'This is a sixth test topic with empty content posted by the testing framework.',
			),
			array(
				'Fourth message [quote="u1"]' . str_repeat('aaa ', 600) . '[/quote]',
				'Fourth message',
			),
			array(
				'This is a fifth [b]test topic[/b] with [quote="u1"]nested content inside of [quote="u1"][i][b]nested[/b] [u]content[/u][/i][/quote][/quote] content [quote="u1"]on top of more content[/quote] posted by the testing framework.',
				'This is a fifth test topic with content posted by the testing framework.',
			),
			array(
				'This is a sixth [b]test topic[/b] with empty [quote="u1"]stuff[quote="u1"][/quote][/quote] content [quote="u1"][/quote] posted by the testing framework.',
				'This is a sixth test topic with empty content posted by the testing framework.',
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
		if (phpbb_version_compare(PHPBB_VERSION, '3.2.0-dev', '<'))
		{
			self::markTestSkipped('Testing trim/tools/bbcodes is for phpBB 3.2 or higher');
		}

		$container = $this->get_test_case_helpers()->set_s9e_services();
		$parser    = $container->get('text_formatter.parser');
		$utils     = $container->get('text_formatter.utils');

		$trim = helper::trimTools()
			->setTools($this->config, $utils)
			->getTrim();

		// parse it to emulate how text is stored in db
		$parsed = $parser->parse($message);
		self::assertEquals($expected, $trim->trim_text($parsed, $this->config['topic_preview_limit']));

		// Test data again, unparsed (falls back to legacy tool)
		self::assertEquals($expected, $trim->trim_text($message, $this->config['topic_preview_limit']));
	}
}
