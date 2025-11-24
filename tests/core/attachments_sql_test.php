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

class attachments_sql_test extends base
{
	public static function get_attachments_empty_data()
	{
		return [
			'disabled' => [
				['topic_preview_limit' => 0],
				[[
					'topic_attachment' => 1,
					'topic_first_post_id' => 1,
					'topic_last_post_id' => 1,
				]],
			],
			'attachments_disabled' => [
				['allow_attachments' => 0],
				[[
					'topic_attachment' => 1,
					'topic_first_post_id' => 1,
					'topic_last_post_id' => 1,
				]],
			],
			'empty_rowset' => [
				[],
				[],
			],
			'no_attachments_flag' => [
				[],
				[[
					'topic_attachment' => 0,
					'topic_first_post_id' => 1,
					'topic_last_post_id' => 1,
				]],
			],
		];
	}

	/**
	 * @dataProvider get_attachments_empty_data
	 */
	public function test_get_attachments_for_topics_returns_empty($config_overrides, $rowset)
	{
		$preview_data = $this->get_topic_preview_data();

		// Apply config overrides
		foreach ($config_overrides as $key => $value)
		{
			$this->config[$key] = $value;
		}

		$result = $preview_data->get_attachments_for_topics($rowset);

		self::assertEquals([], $result);
	}

	public function test_get_attachments_for_topics_with_first_post()
	{
		$preview_data = $this->get_topic_preview_data();

		// Disable last post
		$this->config['topic_preview_last_post'] = 0;

		$rowset = [
			[
				'topic_attachment' => 1,
				'topic_first_post_id' => 1,
				'topic_last_post_id' => 1,
			],
		];

		$result = $preview_data->get_attachments_for_topics($rowset);

		self::assertArrayHasKey(1, $result);
		self::assertCount(2, $result[1]); // Post 1 has 2 attachments
	}

	public function test_get_attachments_for_topics_with_first_and_last_post()
	{
		$preview_data = $this->get_topic_preview_data();

		$rowset = [
			[
				'topic_attachment' => 1,
				'topic_first_post_id' => 1,
				'topic_last_post_id' => 2,
			],
		];

		$result = $preview_data->get_attachments_for_topics($rowset);

		self::assertArrayHasKey(1, $result);
		self::assertArrayHasKey(2, $result);
		self::assertCount(2, $result[1]); // Post 1 has 2 attachments
		self::assertCount(1, $result[2]); // Post 2 has 1 attachment
		// Verify actual attachment data is returned correctly
		self::assertEquals('test1.jpg', $result[1][0]['real_filename']);
		self::assertEquals('test2.pdf', $result[1][1]['real_filename']);
		self::assertEquals('test3.png', $result[2][0]['real_filename']);
	}

	public function test_get_attachments_for_topics_multiple_topics()
	{
		$preview_data = $this->get_topic_preview_data();

		$rowset = [
			[
				'topic_attachment' => 1,
				'topic_first_post_id' => 1,
				'topic_last_post_id' => 2,
			],
			[
				'topic_attachment' => 0, // No attachments
				'topic_first_post_id' => 3,
				'topic_last_post_id' => 3,
			],
			[
				'topic_attachment' => 1,
				'topic_first_post_id' => 4,
				'topic_last_post_id' => 4,
			],
		];

		$result = $preview_data->get_attachments_for_topics($rowset);

		// Should only get attachments for posts 1, 2, and 4
		self::assertArrayHasKey(1, $result);
		self::assertArrayHasKey(2, $result);
		self::assertArrayNotHasKey(3, $result);
		self::assertArrayNotHasKey(4, $result); // Post 4 has no attachments in fixture
	}

	public static function get_attachments_data()
	{
		return [
			'empty' => [
				[],
				[],
			],
			'with_data' => [
				[1, 2],
				[
					1 => [
						['real_filename' => 'test1.jpg'],
						['real_filename' => 'test2.pdf'],
					],
					2 => [
						['real_filename' => 'test3.png'],
					],
				],
			],
		];
	}

	/**
	 * @dataProvider get_attachments_data
	 */
	public function test_get_attachments($post_ids, $expected)
	{
		$preview_data = $this->get_topic_preview_data();

		// Use reflection to access protected method
		$reflection = new \ReflectionClass($preview_data);
		$method = $reflection->getMethod('get_attachments');

		$result = $method->invokeArgs($preview_data, [$post_ids]);

		if (empty($expected))
		{
			self::assertEquals([], $result);
		}
		else
		{
			self::assertArrayHasKey(1, $result);
			self::assertArrayHasKey(2, $result);
			self::assertCount(2, $result[1]); // Post 1 has 2 attachments
			self::assertCount(1, $result[2]); // Post 2 has 1 attachment
			self::assertEquals('test1.jpg', $result[1][0]['real_filename']);
			self::assertEquals('test2.pdf', $result[1][1]['real_filename']);
			self::assertEquals('test3.png', $result[2][0]['real_filename']);
		}
	}
}
