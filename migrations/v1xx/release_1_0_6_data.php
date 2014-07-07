<?php
/**
*
* Topic Preview
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\migrations\v1xx;

class release_1_0_6_data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['topic_preview_version']) && version_compare($this->config['topic_preview_version'], '1.0.6', '>=');
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
