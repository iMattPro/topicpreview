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

	public static function depends_on()
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

	/**
	 * Explicit revert handler for phpBB 4.0+ compatibility
	 *
	 * This fixes uninstall failures in phpBB 4.0.0+ caused by a behavior change in
	 * module removal (commit 07b63fc6a8, ticket PHPBB-17507):
	 *
	 * - phpBB 3.x: Silently succeeded when removing non-existent modules
	 * - phpBB 4.0: Throws MODULE_NOT_EXIST exception when removing non-existent modules
	 *
	 * The problem: This migration uses 'if' conditions to conditionally remove modules
	 * during install. During automatic reversal (uninstall), the migration helper skips
	 * all 'if' statements, causing it to attempt removal of modules that may not exist,
	 * triggering the exception in phpBB 4.0+.
	 *
	 * The solution: Provide explicit revert_data() that removes the parent category
	 * TOPIC_PREVIEW instead of individual child modules. This works because:
	 * - The parent category always exists (added by release_2_0_0.php)
	 * - Child modules are already removed by prior migration reversals
	 * - Removing an empty parent category never throws exceptions
	 *
	 * @return array
	 */
	public function revert_data()
	{
		return array(
			array('config.remove', array('topic_preview_delay')),
			array('config.remove', array('topic_preview_drift')),
			array('config.remove', array('topic_preview_width')),

			array('module.remove', array('acp', 'TOPIC_PREVIEW')),
		);
	}
}
