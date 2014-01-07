<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\migrations\v1xx;

class release_1_0_6_data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['topic_preview_version']) && version_compare($this->config['topic_preview_version'], '1.0.6', '>=');
	}

	static public function depends_on()
	{
		return array('\vse\topicpreview\migrations\v1xx\release_1_0_6_schema');
	}

	public function update_data()
	{
		return array(
			// Add new configs
			array('config.add', array('topic_preview_limit', '150')),
			array('config.add', array('topic_preview_strip_bbcodes', '')),
			array('config.add', array('topic_preview_version', '1.0.6')),
		);
	}
}
