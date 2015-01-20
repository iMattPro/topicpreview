<?php
/**
*
* Topic Preview
*
* @copyright (c) 2014 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\migrations\v2xx;

class release_2_1_1_data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return !isset($this->config['topic_preview_version']);
	}

	static public function depends_on()
	{
		return array('\vse\topicpreview\migrations\v2xx\release_2_1_0_data');
	}

	public function update_data()
	{
		return array(
			// Remove old configs
			array('config.remove', array('topic_preview_version')),
		);
	}
}
