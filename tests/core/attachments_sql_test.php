<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2025 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace core;

use vse\topicpreview\tests\core\base;

class attachments_sql_test extends base
{
	public function test_get_attachments_bulk_empty()
	{
		$preview_data = $this->get_topic_preview_data();
		$result = $preview_data->get_attachments_bulk([]);

		self::assertEquals([], $result);
	}

	public function test_get_attachments_bulk_with_data()
	{
		$preview_data = $this->get_topic_preview_data();
		$result = $preview_data->get_attachments_bulk([1, 2]);

		self::assertArrayHasKey(1, $result);
		self::assertArrayHasKey(2, $result);
		self::assertCount(2, $result[1]); // Post 1 has 2 attachments
		self::assertCount(1, $result[2]); // Post 2 has 1 attachment
		self::assertEquals('test1.jpg', $result[1][0]['real_filename']);
		self::assertEquals('test2.pdf', $result[1][1]['real_filename']);
		self::assertEquals('test3.png', $result[2][0]['real_filename']);
	}
}
