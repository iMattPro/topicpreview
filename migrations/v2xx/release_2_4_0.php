<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2025 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\migrations\v2xx;

class release_2_4_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['topic_preview_rich_text']);
	}

	public static function depends_on()
	{
		return [
			'\vse\topicpreview\migrations\v2xx\release_2_1_0_schema',
			'\vse\topicpreview\migrations\v2xx\release_2_1_1_data'
		];
	}

	public function update_data()
	{
		return [
			['config.add', ['topic_preview_rich_text', '0']],
			['config.add', ['topic_preview_rich_attachments', '0']],
		];
	}
}
