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

class release_2_1_0_data extends \phpbb\db\migration\container_aware_migration
{
	public function effectively_installed()
	{
		return isset($this->config['topic_preview_delay']);
	}

	static public function depends_on()
	{
		return array('\vse\topicpreview\migrations\v2xx\release_2_1_0_schema');
	}

	public function update_data()
	{
		// use module tool explicitly since module.exists does not work in 'if'
		$module_tool = $this->container->get('migrator.tool.module');

		return array(
			// Remove old ACP module if it exists
			array('if', array(
				$module_tool->exists('acp', 'TOPIC_PREVIEW', 'TOPIC_PREVIEW_SETTINGS', true),
				array('module.remove', array('acp', 'TOPIC_PREVIEW', 'TOPIC_PREVIEW_SETTINGS')),
			)),

			// Add new ACP module
			array('module.add', array('acp', 'TOPIC_PREVIEW', array(
				'module_basename'	=> '\vse\topicpreview\acp\topic_preview_module',
				'modes'				=> array('settings'),
			))),

			// Remove old config if it exists
			array('if', array(
				isset($this->config['topic_preview_jquery']),
				array('config.remove', array('topic_preview_jquery')),
			)),

			// Add new configs
			array('config.add', array('topic_preview_delay', '1000')),
			array('config.add', array('topic_preview_drift', '15')),
			array('config.add', array('topic_preview_width', '360')),

			// Update existing configs
			array('config.update', array('topic_preview_avatars', '1')),
			array('config.update', array('topic_preview_version', '2.1.0')),
		);
	}
}
