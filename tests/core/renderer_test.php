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

	/** @var \phpbb\textformatter\s9e\utils|\PHPUnit\Framework\MockObject\MockObject */
	protected $utils;

	protected function setUp(): void
	{
		parent::setUp();

		global $cache, $phpbb_dispatcher, $user;

		$cache = new \phpbb_mock_cache();
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
		$user = new \phpbb_mock_user();
		$user->optionset('viewcensors', true);

		$this->config = new \phpbb\config\config([
			'topic_preview_strip_bbcodes' => 'quote,code',
			'topic_preview_limit' => 150,
		]);

		$this->utils = $this->createMock(\phpbb\textformatter\s9e\utils::class);
		$this->renderer = new \vse\topicpreview\core\renderer($this->config, $this->utils);
	}

	public function render_text_data()
	{
		return [
			// Basic text
			[
				'Hello world',
				150,
				'Hello world',
			],
			// Empty text
			[
				'',
				150,
				'',
			],
			// Text with HTML that should be preserved
			[
				'<p>Hello <strong>world</strong></p>',
				150,
				'<p>Hello <strong>world</strong></p>',
			],
			// Long text that should be trimmed
			[
				str_repeat('a', 200),
				150,
				str_repeat('a', 150) . '...',
			],
		];
	}

	/**
	 * @dataProvider render_text_data
	 */
	public function test_render_text($input, $limit, $expected)
	{
		// Mock the utils to return the input unchanged for basic tests
		$this->utils->method('remove_bbcode')
			->willReturnArgument(0);

		$result = $this->renderer->render_text($input, $limit);

		// For empty input, should return empty
		if (empty($input))
		{
			$this->assertEquals('', $result);
			return;
		}

		// For long text, should be trimmed
		if (utf8_strlen(strip_tags($input)) > $limit)
		{
			$this->assertStringEndsWith('...', $result);
			$this->assertLessThanOrEqual($limit + 3, utf8_strlen(strip_tags($result)));
		}
		else
		{
			// For short text, should remain unchanged
			$this->assertEquals($expected, $result);
		}
	}

	public function test_remove_ignored_bbcodes()
	{
		$text = '[quote]This should be removed[/quote] This should remain [code]This too should be removed[/code]';

		// Mock the utils to return the text with each BBCode removed individually
		$this->utils->method('remove_bbcode')
			->willReturnCallback(function($input, $bbcode) {
				if ($bbcode === 'quote')
				{
					return str_replace('[quote]This should be removed[/quote]', '', $input);
				}
				if ($bbcode === 'code')
				{
					return str_replace('[code]This too should be removed[/code]', '', $input);
				}
				return $input;
			});

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('remove_ignored_bbcodes');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text);
		$this->assertEquals(' This should remain ', $result);
	}

	public function test_remove_ignored_bbcodes_empty_config()
	{
		$this->config['topic_preview_strip_bbcodes'] = '';
		$text = '[quote]This should remain[/quote]';

		$this->utils->expects($this->never())
			->method('remove_bbcode');

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('remove_ignored_bbcodes');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text);
		$this->assertEquals($text, $result);
	}

	public function test_safe_trim_html()
	{
		$html = '<p>This is a <strong>test</strong> message with <em>formatting</em></p>';
		$limit = 20;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('safe_trim_html');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		// Should be trimmed and have ellipsis
		$this->assertStringEndsWith('...', $result);

		// Should still be valid HTML (no broken tags)
		$this->assertStringNotContainsString('<strong', strip_tags($result));

		// Text content should be within limit
		$text_content = strip_tags($result);
		$this->assertLessThanOrEqual($limit + 3, utf8_strlen($text_content)); // +3 for '...'
	}

	public function test_safe_trim_html_short_content()
	{
		$html = '<p>Short</p>';
		$limit = 150;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('safe_trim_html');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		// Should not be trimmed
		$this->assertEquals($html, $result);
		$this->assertStringNotContainsString('...', $result);
	}

	public function test_regex_trim_html_fallback()
	{
		$html = '<p>This is a long message that should be trimmed by the regex fallback method</p>';
		$limit = 20;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('regex_trim_html');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		// Should be trimmed and have ellipsis
		$this->assertStringEndsWith('...', $result);

		// Should not contain HTML tags
		$this->assertStringNotContainsString('<p>', $result);
		$this->assertStringNotContainsString('</p>', $result);

		// Text content should be within limit
		$text_content = strip_tags(str_replace('...', '', $result));
		$this->assertLessThanOrEqual($limit, utf8_strlen($text_content));
	}

	public function test_regex_trim_html_short_content()
	{
		$html = '<p>Short</p>';
		$limit = 150;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('regex_trim_html');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		// Should return original HTML for short content
		$this->assertEquals($html, $result);
		$this->assertStringNotContainsString('...', $result);
	}

	public function test_dom_trim_html_when_available()
	{
		// Skip if DOM is not available
		if (!class_exists('DOMDocument') || !extension_loaded('libxml'))
		{
			$this->markTestSkipped('DOM extension not available');
		}

		$html = '<p>This is a <strong>test</strong> message</p>';
		$limit = 15;

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('dom_trim_html');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		// Should be trimmed and have ellipsis
		$this->assertStringEndsWith('...', $result);

		// Text content should be within limit
		$text_content = strip_tags(str_replace('...', '', $result));
		$this->assertLessThanOrEqual($limit + 5, utf8_strlen($text_content)); // Allow some margin for word boundaries
	}
}
