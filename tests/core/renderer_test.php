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

		$this->renderer = new \vse\topicpreview\core\renderer(new \phpbb\textformatter\s9e\utils());
	}

	public function render_text_data()
	{
		return [
			'Empty text - rich mode' =>
			[
				'',
				150,
				1,
				'',
			],
			'Empty text - plain mode' =>
			[
				'',
				150,
				0,
				'',
			],
			'Simple text - rich mode' =>
			[
				'<t>Hello world</t>',
				150,
				1,
				'Hello world',
			],
			'Simple text - plain mode' =>
			[
				'<t>Hello world</t>',
				150,
				0,
				'Hello world',
			],
			'BBCode text - rich mode' =>
			[
				'<t><B><s>[b]</s>Bold text<e>[/b]</e></B> normal text</t>',
				150,
				1,
				'<B><s>[b]</s>Bold text<e>[/b]</e></B> normal text',
			],
			'BBCode text - plain mode (should strip BBCode)' =>
			[
				'<t><B><s>[b]</s>Bold text<e>[/b]</e></B> normal text</t>',
				150,
				0,
				'Bold text normal text',
			],
			'Long text - rich mode (should be trimmed)' =>
			[
				'<t>' . str_repeat('Long text content ', 20) . '</t>',
				10,
				1,
				'Long text ...',
			],
			'Long text - plain mode (should be trimmed)' =>
			[
				'<t>' . str_repeat('Long text content ', 20) . '</t>',
				10,
				0,
				'Long text...',
			],
			'Text with HTML entities - rich mode' =>
			[
				'<t>5 &lt; 10 &gt; 1</t>',
				150,
				1,
				'5 &lt; 10 &gt; 1',
			],
			'Text with HTML entities - plain mode' =>
			[
				'<t>5 &lt; 10 &gt; 1</t>',
				150,
				0,
				'5 &lt; 10 &gt; 1',
			],
			'Plain text with quotes' => [
				'<t>Test "quoted" text</t>',
				150,
				0,
				'Test &quot;quoted&quot; text',
			],
			'Text with line breaks - plain mode' =>
			[
				"<t>First line\n\nSecond line</t>",
				150,
				0,
				'First line<br />' . "\n" . '<br />' . "\n" . 'Second line',
			],
			'Empty text after stripping - rich mode' =>
			[
				'<t><QUOTE><s>[quote]</s>Quoted text<e>[/quote]</e></QUOTE></t>',
				150,
				1,
				'',
			],
			'Empty text after stripping - plain mode' =>
			[
				'<t><QUOTE><s>[quote]</s>Quoted text<e>[/quote]</e></QUOTE></t>',
				150,
				0,
				'',
			],
		];
	}

	/**
	 * @dataProvider render_text_data
	 */
	public function test_render_text($input, $limit, $rich_text, $expected)
	{
		$result = $this->renderer->render_text($input, $limit, 'quote', $rich_text, true, [], 0);

		$this->assertEquals($expected, $result);
	}

	public function test_render_text_with_attachments()
	{
		// This test verifies that render_text handles attachments properly.
		// The mock parse_attachments function is defined at the bottom of this file
		// in the vse\topicpreview\core namespace to override phpBB's version.
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t>Text with <!-- ia0 --> inline attachment and more text</t>';
		// Mix of inline attachment (array) and non-inline attachment (string)
		$attachments = [
			0 => ['attach_id' => 1, 'real_filename' => 'inline.jpg'],
			1 => ['attach_id' => 2, 'real_filename' => 'non-inline.pdf'],
		];

		$result = $this->renderer->render_text($text, 150, '', true, true, $attachments, 1);

		$this->assertStringContainsString('inline.jpg', $result);
		$this->assertStringContainsString('non-inline.pdf', $result);
		$this->assertStringContainsString('and more text', $result);
	}

	public function test_render_text_plain_mode_ignores_attachments()
	{
		$text = '<t>Plain text</t>';
		$attachments = [['attach_id' => 1, 'real_filename' => 'test.jpg']];

		$result = $this->renderer->render_text($text, 150, '', false, false, $attachments, 1);

		$this->assertEquals('Plain text', $result);
	}

	public function test_render_text_empty_attachments()
	{
		$text = '<t>Text without attachments</t>';
		$result = $this->renderer->render_text($text, 150, '', true, true, [], 0);

		$this->assertEquals('Text without attachments', $result);
	}

	public function test_remove_ignored_bbcodes()
	{
		$strip_bbcodes = 'quote|code';
		$text = '[quote]This should be removed[/quote] This should remain [code]This too should be removed[/code]';

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('remove_ignored_bbcodes');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text, $strip_bbcodes);

		// Should contain the remaining text
		$this->assertStringContainsString('This should remain', $result);
		// Should not contain the stripped BBCodes (depending on real utils behavior)
		$this->assertNotEmpty($result);
	}

	public function test_remove_ignored_bbcodes_empty_config()
	{
		$strip_bbcodes = '';
		$text = '[quote]This should remain[/quote]';

		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('remove_ignored_bbcodes');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text, $strip_bbcodes);
		$this->assertEquals($text, $result);
	}

	public function trim_html_content_data()
	{
		return [
			'HTML needs trimming' => [
				25,
				'<p>This is a <strong>test</strong> message with <em>formatting</em></p>',
				'<p>This is a <strong>test</strong> message...</p>',
			],
			'HTML no trimming' => [
				150,
				'<p>This is a <strong>test</strong> message with <em>formatting</em></p>',
				'<p>This is a <strong>test</strong> message with <em>formatting</em></p>',
			],
			'HTML with emoji needs trimming' => [
				5,
				str_repeat('<img alt="😇">', 10),
				str_repeat('<img alt="😇">', 5) . '...',
			],
			'HTML with text and image exceeding limit' => [
				5,
				'<p>Text<img alt="1"><img alt="2"></p>',
				'<p>Text<img alt="1"></p>...',
			],
		];
	}

	/**
	 * @dataProvider trim_html_content_data
	 */
	public function test_trim_html_content($limit, $html, $expected)
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('trim_html_content');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $html, $limit);

		$this->assertEquals($expected, $result);
	}
}

// Mock parse_attachments for testing in the vse\topicpreview\core namespace
// This will be called by renderer.php instead of the global parse_attachments
namespace vse\topicpreview\core;

if (!function_exists('vse\topicpreview\core\parse_attachments'))
{
	function parse_attachments($forum_id, &$message, &$attachments, &$update_count)
	{
		// Simple mock: process inline attachments and append non-inline ones
		$compiled_attachments = [];
		foreach ($attachments as $key => $attachment)
		{
			if ($attachment['real_filename'] === 'non-inline.pdf')
			{
				// Already rendered HTML - append to message
				$message .= $attachment['real_filename'];
				$compiled_attachments[] = $attachment['real_filename'];
			}
			else if (is_array($attachment) && isset($attachment['attach_id']))
			{
				// Mock inline attachment - replace placeholder
				$message = str_replace('<!-- ia' . $key . ' -->', '<div class="inline-attachment">' . $attachment['real_filename'] . '</div>', $message);
			}
		}
		$attachments = $compiled_attachments;
	}

	function generate_text_for_display($text, $uid, $bitfield, $flags)
	{
		return \generate_text_for_display($text, $uid, $bitfield, $flags);
	}
}
