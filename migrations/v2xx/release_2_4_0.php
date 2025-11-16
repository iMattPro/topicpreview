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
		return isset($this->config['topic_preview_version']);
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
			['custom', [[$this, 'update_none_theme_to_light']]],
		];
	}

	/**
	 * Update any styles using the deprecated "no" theme to use the "light" theme instead
	 */
	public function update_none_theme_to_light()
	{
		$sql = 'UPDATE ' . STYLES_TABLE . "
			SET topic_preview_theme = 'light'
			WHERE topic_preview_theme = 'no'";
		$this->db->sql_query($sql);
	}
}
