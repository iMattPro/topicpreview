<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2013 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\migrations\v2xx;

class release_2_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['topic_preview_avatars']);
	}

	static public function depends_on()
	{
		return array('\vse\topicpreview\migrations\v1xx\release_1_0_6_data');
	}

	public function update_data()
	{
		return array(
			// Add ACP module
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'TOPIC_PREVIEW')),
			array('module.add', array('acp', 'TOPIC_PREVIEW', array(
				'module_basename'	=> '\vse\topicpreview\acp\topic_preview_module',
				'modes'				=> array('settings'),
			))),

			// Add new configs
			array('config.add', array('topic_preview_last_post', '0')),
			array('config.add', array('topic_preview_avatars', '0')),
			array('config.add', array('topic_preview_jquery', '0')),

			// Update existing configs
			array('config.update', array('topic_preview_version', '2.0.0')),
		);
	}
}
