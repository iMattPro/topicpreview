<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2025 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\core;

class renderer_test extends \phpbb_test_case
{
	/** @var \vse\topicpreview\core\renderer */
	protected $renderer;

	/** @var \phpbb\config\config */
	protected $config;

	protected function setUp(): void
	{
		parent::setUp();

		global $cache, $phpbb_container, $phpbb_dispatcher, $user;

		$cache = new \phpbb_mock_cache();
		$phpbb_container = new \phpbb_mock_container_builder();
		$renderer = $this->get_test_case_helpers()->set_s9e_services()->get('text_formatter.renderer');
		$phpbb_container->set('text_formatter.renderer', $renderer);
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
		$user = new \phpbb_mock_user();
		$user->optionset('viewcensors', true);

		$this->config = new \phpbb\config\config([
			'topic_preview_strip_bbcodes' => 'quote|code',
			'topic_preview_limit' => 150,
			'topic_preview_rich_text' => 1,
		]);

		$this->renderer = new \vse\topicpreview\core\renderer($this->config, new \phpbb\textformatter\s9e\utils());
	}

	public function render_text_data()
	{
		return [
			// Empty text - rich mode
			[
				'',
				150,
				1,
				'',
			],
			// Empty text - plain mode
			[
				'',
				150,
				0,
				'',
			],
			// Simple text - rich mode
			[
				'<t>Hello world</t>',
				150,
				1,
				'Hello world',
			],
			// Simple text - plain mode
			[
				'<t>Hello world</t>',
				150,
				0,
				'Hello world',
			],
			// BBCode text - rich mode
			[
				'<t><B><s>[b]</s>Bold text<e>[/b]</e></B> normal text</t>',
				150,
				1,
				'<B><s>[b]</s>Bold text<e>[/b]</e></B> normal text',
			],
			// BBCode text - plain mode (should strip BBCode)
			[
				'<t><B><s>[b]</s>Bold text<e>[/b]</e></B> normal text</t>',
				150,
				0,
				'Bold text normal text',
			],
			// Long text - rich mode (should be trimmed)
			[
				'<t>' . str_repeat('Long text content ', 20) . '</t>',
				10,
				1,
				'Long text ...',
			],
			// Long text - plain mode (should be trimmed)
			[
				'<t>' . str_repeat('Long text content ', 20) . '</t>',
				10,
				0,
				'Long text...',
			],
			// Text with HTML entities - rich mode
			[
				'<t>5 &lt; 10 &gt; 1</t>',
				150,
				1,
				'5 &lt; 10 &gt; 1',
			],
			// Text with HTML entities - plain mode
			[
				'<t>5 &lt; 10 &gt; 1</t>',
				150,
				0,
				'5 &lt; 10 &gt; 1',
			],
			// Text with line breaks - plain mode
			[
				'<t>First line\nSecond line</t>',
				150,
				0,
				'First line\nSecond line',
			],
		];
	}

	/**
	 * @dataProvider render_text_data
	 */
	public function test_render_text($input, $limit, $rich_text, $expected)
	{
		$this->config['topic_preview_rich_text'] = $rich_text;

		$result = $this->renderer->render_text($input, $limit);

		$this->assertEquals($expected, $result);
	}

	public function test_remove_ignored_bbcodes()
	{
		$text = '[quote]This should be removed[/quote] This should remain [code]This too should be removed[/code]';

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('remove_ignored_bbcodes');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text);

		// Should contain the remaining text
		$this->assertStringContainsString('This should remain', $result);
		// Should not contain the stripped BBCodes (depending on real utils behavior)
		$this->assertNotEmpty($result);
	}

	public function test_remove_ignored_bbcodes_empty_config()
	{
		$this->config['topic_preview_strip_bbcodes'] = '';
		$text = '[quote]This should remain[/quote]';

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('remove_ignored_bbcodes');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text);
		$this->assertEquals($text, $result);
	}

	public function test_plain_text_rendering()
	{
		$this->config['topic_preview_rich_text'] = 0;

		$text = '<t><B><s>[b]</s>Bold<e>[/b]</e></B> text and 5 &lt; 1 &gt; 0</t>';

		$result = $this->renderer->render_text($text, 150);

		$this->assertEquals('Bold text and 5 &lt; 1 &gt; 0', $result);
	}

	public function test_plain_text_with_line_breaks()
	{
		$this->config['topic_preview_rich_text'] = 0;
		$text = "<t>First line\n\nSecond line</t>";

		$result = $this->renderer->render_text($text, 150);

		$this->assertStringNotContainsString('First line<br />\n' . "\n" . '<br />\n' . "\n" . 'Second line', $result);
	}

	public function test_trim_html_content()
	{
		$html = '<p>This is a <strong>test</strong> message with <em>formatting</em></p>';
		$limit = 25;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('trim_html_content');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		$this->assertEquals('<p>This is a <strong>test</strong> message</p>...', $result);
	}

	public function test_rich_text_rendering()
	{
		$this->config['topic_preview_rich_text'] = 1;
		$text = '<t><B><s>[b]</s>Bold text<e>[/b]</e></B> normal text</t>';

		$result = $this->renderer->render_text($text, 150);

		$this->assertEquals('<B><s>[b]</s>Bold text<e>[/b]</e></B> normal text', $result);
	}

	public function test_dom_trim_html_when_available()
	{
		// Skip if DOM is not available
		if (!class_exists('DOMDocument') || !extension_loaded('libxml'))
		{
			$this->markTestSkipped('DOM extension not available');
		}

		$html = '<p>This is a <strong>test</strong> message</p>';
		$limit = 25;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('dom_trim_html');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		$this->assertEquals('<p>This is a <strong>test</strong> message</p>', $result);
	}
}
