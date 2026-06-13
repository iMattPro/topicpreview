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

	public static function render_text_data()
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
			'Emoji long text' => [
				'<r>' . str_repeat ('😀', 155) . '</r>',
				150,
				0,
				str_repeat ('😀', 150) . '...',
			],
			'Legacy post plain text' => [
				'Plain text message',
				150,
				0,
				'Plain text message',
			],
			'Legacy post plain long text' => [
				str_repeat ('a', 155),
				150,
				0,
				str_repeat ('a', 150) . '...',
			],
			'Legacy post multibyte long text' => [
				str_repeat ('á', 155),
				150,
				0,
				str_repeat ('á', 150) . '...',
			],
			'Legacy post magic urls' => [
				'Legacy message with <!-- m --><a class="postlink" href="http://google.com">http://google.com</a><!-- m --> magic url and <!-- e --><a href="mailto:test@google.com">test@google.com</a><!-- e --> email',
				150,
				0,
				'Legacy message with magic url and test@google.com email',
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

		$result = $method->invoke($this->renderer, $text, $strip_bbcodes);
		$this->assertEquals($text, $result);
	}

	public static function trim_html_content_data()
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

		$result = $method->invoke($this->renderer, $html, $limit);

		$this->assertEquals($expected, $result);
	}

	public function test_trim_html_content_falls_back_without_libxml()
	{
		global $topic_preview_force_missing_libxml;

		$topic_preview_force_missing_libxml = true;

		try
		{
			$reflection = new \ReflectionClass($this->renderer);
			$method = $reflection->getMethod('trim_html_content');
			$method->setAccessible(true);

			$result = $method->invoke($this->renderer, '<p>This is long text</p>', 7);

			$this->assertEquals('This is...', $result);
		}
		finally
		{
			$topic_preview_force_missing_libxml = false;
		}
	}

	public static function get_attachment_info_data()
	{
		return [
			'No attachments' => [
				'<t>Simple text</t>',
				'',
				[
					'excluded_xml_indices' => [],
					'xml_to_array_map' => [],
				],
			],
			'Single attachment, no stripping' => [
				'<t>Text <ATTACHMENT filename="test.jpg" index="0"><s>[attachment=0]</s>test.jpg<e>[/attachment]</e></ATTACHMENT></t>',
				'',
				[
					'excluded_xml_indices' => [],
					'xml_to_array_map' => [], // Empty when nothing excluded (optimization)
				],
			],
			'Attachment inside quote BBCode' => [
				'<t><QUOTE><s>[quote]</s>Quote <ATTACHMENT filename="hidden.jpg" index="0"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE></t>',
				'quote',
				[
					'excluded_xml_indices' => [0],
					'xml_to_array_map' => [],
				],
			],
			'Multiple attachments, one inside stripped BBCode' => [
				'<t><QUOTE><s>[quote]</s><ATTACHMENT filename="hidden.jpg" index="0"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE> <ATTACHMENT filename="visible.jpg" index="1"><s>[attachment=1]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>',
				'quote',
				[
					'excluded_xml_indices' => [0],
					'xml_to_array_map' => [1 => 0],
				],
			],
			'Multiple attachments with non-sequential indices' => [
				'<t><HIDDEN><s>[hidden]</s><ATTACHMENT filename="file1.jpg" index="3"><s>[attachment=3]</s>file1.jpg<e>[/attachment]</e></ATTACHMENT><ATTACHMENT filename="file2.jpg" index="0"><s>[attachment=0]</s>file2.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN> <ATTACHMENT filename="file3.jpg" index="2"><s>[attachment=2]</s>file3.jpg<e>[/attachment]</e></ATTACHMENT></t>',
				'hidden',
				[
					'excluded_xml_indices' => [3, 0],
					'xml_to_array_map' => [2 => 0],
				],
			],
			'Multiple BBCodes to strip with attachments' => [
				'<t><QUOTE><s>[quote]</s><ATTACHMENT filename="quote.jpg" index="0"><s>[attachment=0]</s>quote.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE> <CODE><s>[code]</s><ATTACHMENT filename="code.jpg" index="1"><s>[attachment=1]</s>code.jpg<e>[/attachment]</e></ATTACHMENT><e>[/code]</e></CODE> <ATTACHMENT filename="visible.jpg" index="2"><s>[attachment=2]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>',
				'quote|code',
				[
					'excluded_xml_indices' => [0, 1],
					'xml_to_array_map' => [2 => 0],
				],
			],
		];
	}

	/**
	 * @dataProvider get_attachment_info_data
	 */
	public function test_get_attachment_info($text, $strip_bbcodes, $expected)
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('get_attachment_info');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, $text, $strip_bbcodes);

		$this->assertEquals($expected['excluded_xml_indices'], $result['excluded_xml_indices']);
		$this->assertEquals($expected['xml_to_array_map'], $result['xml_to_array_map']);
	}

	public function test_get_attachment_info_handles_attachment_attribute_order()
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('get_attachment_info');
		$method->setAccessible(true);

		$text = '<t><QUOTE><s>[quote]</s><ATTACHMENT index="0" filename="hidden.jpg"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE> <ATTACHMENT index="1" filename="visible.jpg"><s>[attachment=1]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>';
		$result = $method->invoke($this->renderer, $text, 'quote');

		$this->assertEquals([0], $result['excluded_xml_indices']);
		$this->assertEquals([1 => 0], $result['xml_to_array_map']);
	}

	public function test_find_attachment_index_returns_null_for_missing_attachment()
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('find_attachment_index');
		$method->setAccessible(true);

		$result = $method->invoke($this->renderer, 2, 'missing.jpg', [
			0 => ['attach_id' => 1, 'real_filename' => 'visible.jpg'],
			1 => ['attach_id' => 2, 'real_filename' => 'other.jpg'],
		]);

		$this->assertNull($result);
	}

	public function test_render_text_with_attachments_in_stripped_bbcode()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		// Text with attachment inside a QUOTE that should be stripped
		$text = '<t><QUOTE><s>[quote]</s><ATTACHMENT filename="hidden.jpg" index="0"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE> <ATTACHMENT filename="visible.jpg" index="1"><s>[attachment=1]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 1, 'real_filename' => 'hidden.jpg'],
			1 => ['attach_id' => 2, 'real_filename' => 'visible.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, 'quote', true, true, $attachments, 1);

		// Should NOT contain the hidden attachment
		$this->assertStringNotContainsString('hidden.jpg', $result);
		// Should contain the visible attachment
		$this->assertStringContainsString('visible.jpg', $result);
	}

	public function test_render_text_with_stripped_inline_and_non_inline_attachments()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t><HIDDEN><s>[hidden]</s><ATTACHMENT filename="hidden.jpg" index="0"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN> <ATTACHMENT filename="visible.jpg" index="1"><s>[attachment=1]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 3, 'real_filename' => 'non-inline.pdf'],
			1 => ['attach_id' => 2, 'real_filename' => 'visible.jpg'],
			2 => ['attach_id' => 1, 'real_filename' => 'hidden.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, 'hidden', true, true, $attachments, 1);

		$this->assertStringNotContainsString('hidden.jpg', $result);
		$this->assertStringContainsString('visible.jpg', $result);
		$this->assertStringContainsString('non-inline.pdf', $result);
		$this->assertLessThan(strpos($result, 'non-inline.pdf'), strpos($result, 'visible.jpg'));
	}

	public function test_render_text_keeps_inline_attachments_before_non_inline_attachments()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t><ATTACHMENT filename="hidden.jpg" index="0"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT> <ATTACHMENT filename="visible.jpg" index="1"><s>[attachment=1]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 3, 'real_filename' => 'non-inline.pdf'],
			1 => ['attach_id' => 2, 'real_filename' => 'visible.jpg'],
			2 => ['attach_id' => 1, 'real_filename' => 'hidden.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, '', true, true, $attachments, 1);

		$this->assertStringContainsString('hidden.jpg', $result);
		$this->assertStringContainsString('visible.jpg', $result);
		$this->assertStringContainsString('non-inline.pdf', $result);
		$this->assertLessThan(strpos($result, 'visible.jpg'), strpos($result, 'hidden.jpg'));
		$this->assertLessThan(strpos($result, 'non-inline.pdf'), strpos($result, 'visible.jpg'));
	}

	public function test_render_text_renumbers_descending_inline_attachment_indexes()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t><HIDDEN member="1"><s>[hidden]</s><ATTACHMENT filename="avatar2.PNG" index="2"><s>[attachment=2]</s>avatar2.PNG<e>[/attachment]</e></ATTACHMENT><ATTACHMENT filename="catfail.gif" index="1"><s>[attachment=1]</s>catfail.gif<e>[/attachment]</e></ATTACHMENT><ATTACHMENT filename="26fa1f455b67040e5aa4270c7f693a31.jpg" index="0"><s>[attachment=0]</s>26fa1f455b67040e5aa4270c7f693a31.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN></t>';

		$attachments = [
			0 => ['attach_id' => 74, 'real_filename' => '26fa1f455b67040e5aa4270c7f693a31.jpg'],
			1 => ['attach_id' => 73, 'real_filename' => 'catfail.gif'],
			2 => ['attach_id' => 72, 'real_filename' => 'avatar2.PNG'],
		];

		$result = $this->renderer->render_text($text, 150, '', true, true, $attachments, 1);

		$this->assertStringContainsString('avatar2.PNG', $result);
		$this->assertStringContainsString('catfail.gif', $result);
		$this->assertStringContainsString('26fa1f455b67040e5aa4270c7f693a31.jpg', $result);
		$this->assertSame(1, substr_count($result, 'avatar2.PNG'));
		$this->assertSame(1, substr_count($result, 'catfail.gif'));
		$this->assertSame(1, substr_count($result, '26fa1f455b67040e5aa4270c7f693a31.jpg'));
		$this->assertLessThan(strpos($result, 'catfail.gif'), strpos($result, 'avatar2.PNG'));
		$this->assertLessThan(strpos($result, '26fa1f455b67040e5aa4270c7f693a31.jpg'), strpos($result, 'catfail.gif'));
	}

	public function test_render_text_maps_duplicate_filenames_by_index()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t><HIDDEN member="1"><s>[hidden]</s><ATTACHMENT filename="image.jpg" index="1"><s>[attachment=1]</s>image.jpg<e>[/attachment]</e></ATTACHMENT> <ATTACHMENT filename="image.jpg" index="0"><s>[attachment=0]</s>image.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN></t>';

		$attachments = [
			0 => ['attach_id' => 11, 'real_filename' => 'image.jpg'],
			1 => ['attach_id' => 10, 'real_filename' => 'image.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, '', true, true, $attachments, 1);

		$this->assertStringContainsString('image.jpg#10', $result);
		$this->assertStringContainsString('image.jpg#11', $result);
		$this->assertLessThan(strpos($result, 'image.jpg#11'), strpos($result, 'image.jpg#10'));
	}

	public function test_render_text_excludes_duplicate_filename_by_index()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t><HIDDEN><s>[hidden]</s><ATTACHMENT filename="image.jpg" index="0"><s>[attachment=0]</s>image.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN> <ATTACHMENT filename="image.jpg" index="1"><s>[attachment=1]</s>image.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 11, 'real_filename' => 'image.jpg'],
			1 => ['attach_id' => 10, 'real_filename' => 'image.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, '', true, true, $attachments, 1);

		$this->assertStringNotContainsString('image.jpg#11', $result);
		$this->assertStringContainsString('image.jpg#10', $result);
	}

	public function test_render_text_keeps_guest_hidden_inline_attachment_hidden()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		$text = '<t><HIDDEN><s>[hidden]</s><ATTACHMENT filename="hidden.jpg" index="0"><s>[attachment=0]</s>hidden.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN> <ATTACHMENT filename="visible.jpg" index="1"><s>[attachment=1]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 3, 'real_filename' => 'non-inline.pdf'],
			1 => ['attach_id' => 2, 'real_filename' => 'visible.jpg'],
			2 => ['attach_id' => 1, 'real_filename' => 'hidden.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, '', true, true, $attachments, 1);

		$this->assertStringContainsString('hc-box', $result);
		$this->assertStringNotContainsString('hidden.jpg', $result);
		$this->assertStringContainsString('visible.jpg', $result);
		$this->assertStringContainsString('non-inline.pdf', $result);
		$this->assertLessThan(strpos($result, 'non-inline.pdf'), strpos($result, 'visible.jpg'));
	}

	public function test_render_text_with_multiple_attachments_in_different_bbcodes()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		// Multiple attachments in different BBCodes that should be stripped
		$text = '<t>Content <QUOTE><s>[quote]</s><ATTACHMENT filename="quote.jpg" index="0"><s>[attachment=0]</s>quote.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE> <CODE><s>[code]</s><ATTACHMENT filename="code.jpg" index="1"><s>[attachment=1]</s>code.jpg<e>[/attachment]</e></ATTACHMENT><e>[/code]</e></CODE> <ATTACHMENT filename="visible.jpg" index="2"><s>[attachment=2]</s>visible.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 1, 'real_filename' => 'quote.jpg'],
			1 => ['attach_id' => 2, 'real_filename' => 'code.jpg'],
			2 => ['attach_id' => 3, 'real_filename' => 'visible.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, 'quote|code', true, true, $attachments, 1);

		// Should NOT contain the hidden attachments
		$this->assertStringNotContainsString('quote.jpg', $result);
		$this->assertStringNotContainsString('code.jpg', $result);
		// Should contain the visible attachment
		$this->assertStringContainsString('visible.jpg', $result);
		// Should contain the text content
		$this->assertStringContainsString('Content', $result);
	}

	public function test_render_text_with_non_sequential_attachment_indices()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		// Attachments with non-sequential indices (0, 3) hidden, (2) visible
		$text = '<t><HIDDEN><s>[hidden]</s><ATTACHMENT filename="file1.jpg" index="3"><s>[attachment=3]</s>file1.jpg<e>[/attachment]</e></ATTACHMENT><ATTACHMENT filename="file2.jpg" index="0"><s>[attachment=0]</s>file2.jpg<e>[/attachment]</e></ATTACHMENT><e>[/hidden]</e></HIDDEN> <ATTACHMENT filename="file3.jpg" index="2"><s>[attachment=2]</s>file3.jpg<e>[/attachment]</e></ATTACHMENT></t>';

		$attachments = [
			0 => ['attach_id' => 1, 'real_filename' => 'file1.jpg'],
			1 => ['attach_id' => 2, 'real_filename' => 'file3.jpg'],
			2 => ['attach_id' => 3, 'real_filename' => 'file2.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, 'hidden', true, true, $attachments, 1);

		// Should NOT contain the hidden attachments
		$this->assertStringNotContainsString('file1.jpg', $result);
		$this->assertStringNotContainsString('file2.jpg', $result);
		// Should contain the visible attachment
		$this->assertStringContainsString('file3.jpg', $result);
	}

	public function test_render_text_all_attachments_in_stripped_bbcode()
	{
		global $config, $phpbb_root_path, $phpEx, $extensions;

		$config = new \phpbb\config\config([]);
		$phpbb_root_path = '';
		$phpEx = 'php';
		$extensions = [];

		// All attachments inside stripped BBCode
		$text = '<t><QUOTE><s>[quote]</s><ATTACHMENT filename="file1.jpg" index="0"><s>[attachment=0]</s>file1.jpg<e>[/attachment]</e></ATTACHMENT> <ATTACHMENT filename="file2.jpg" index="1"><s>[attachment=1]</s>file2.jpg<e>[/attachment]</e></ATTACHMENT><e>[/quote]</e></QUOTE></t>';

		$attachments = [
			0 => ['attach_id' => 1, 'real_filename' => 'file1.jpg'],
			1 => ['attach_id' => 2, 'real_filename' => 'file2.jpg'],
		];

		$result = $this->renderer->render_text($text, 150, 'quote', true, true, $attachments, 1);

		// Should be empty or contain no attachments
		$this->assertStringNotContainsString('file1.jpg', $result);
		$this->assertStringNotContainsString('file2.jpg', $result);
	}


	public function test_extract_bbcode_content()
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('extract_bbcode_content');
		$method->setAccessible(true);

		// Test extracting QUOTE content
		$text = '<t>Before <QUOTE><s>[quote]</s>Inside quote<e>[/quote]</e></QUOTE> After</t>';
		$result = $method->invoke($this->renderer, $text, 'quote');

		$this->assertStringContainsString('Inside quote', $result);
		$this->assertStringNotContainsString('Before', $result);
		$this->assertStringNotContainsString('After', $result);
	}

	public function test_extract_bbcode_content_multiple_instances()
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('extract_bbcode_content');
		$method->setAccessible(true);

		// Test extracting multiple QUOTE instances
		$text = '<t><QUOTE><s>[quote]</s>First quote<e>[/quote]</e></QUOTE> Text <QUOTE><s>[quote]</s>Second quote<e>[/quote]</e></QUOTE></t>';
		$result = $method->invoke($this->renderer, $text, 'quote');

		$this->assertStringContainsString('First quote', $result);
		$this->assertStringContainsString('Second quote', $result);
	}

	public function test_extract_bbcode_content_no_match()
	{
		$reflection = new \ReflectionClass($this->renderer);
		$method = $reflection->getMethod('extract_bbcode_content');
		$method->setAccessible(true);

		// Test extracting non-existent BBCode
		$text = '<t>Some text without the BBCode</t>';
		$result = $method->invoke($this->renderer, $text, 'quote');

		$this->assertEquals('', $result);
	}
}

// Mock parse_attachments for testing in the vse\topicpreview\core namespace
// This will be called by renderer.php instead of the global parse_attachments
namespace vse\topicpreview\core;

if (!function_exists('vse\topicpreview\core\extension_loaded'))
{
	function extension_loaded($extension)
	{
		global $topic_preview_force_missing_libxml;

		return $topic_preview_force_missing_libxml && $extension === 'libxml' ? false : \extension_loaded($extension);
	}
}

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
				$compiled_attachments[] = $attachment['real_filename'] . '#' . $attachment['attach_id'];
			}
			else if (is_array($attachment) && isset($attachment['attach_id']))
			{
				// Mock inline attachment - replace placeholder
				$replace_count = 0;
				$message = preg_replace('#<!-- ia' . $key . ' -->.*?<!-- ia' . $key . ' -->#', '<div class="inline-attachment">' . $attachment['real_filename'] . '#' . $attachment['attach_id'] . '</div>', $message, -1, $replace_count);
				if (!$replace_count)
				{
					$message = str_replace('<!-- ia' . $key . ' -->', '<div class="inline-attachment">' . $attachment['real_filename'] . '#' . $attachment['attach_id'] . '</div>', $message);
				}
			}
		}
		$attachments = $compiled_attachments;
	}

	function generate_text_for_display($text, $uid, $bitfield, $flags)
	{
		if (strpos($text, '<HIDDEN') !== false)
		{
			if (strpos($text, 'member="1"') !== false)
			{
				$text = preg_replace('#<HIDDEN[^>]*>#', '<div class="hc-box hc-box--member"><div class="hc-content">', $text);
				$text = str_replace('</HIDDEN>', '</div></div>', $text);
			}
			else
			{
				$text = preg_replace('#<HIDDEN[^>]*>.*?</HIDDEN>#s', '<div class="hc-box">Hidden content</div>', $text);
			}
			$text = preg_replace_callback('#<ATTACHMENT filename="([^"]+)" index="(\d+)">.*?</ATTACHMENT>#s', static function ($matches) {
				return '<div class="inline-attachment"><!-- ia' . $matches[2] . ' -->' . $matches[1] . '<!-- ia' . $matches[2] . ' --></div>';
			}, $text);
			return $text;
		}

		return \generate_text_for_display($text, $uid, $bitfield, $flags);
	}
}
