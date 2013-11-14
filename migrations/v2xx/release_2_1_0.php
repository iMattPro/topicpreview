<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\migrations\v2xx;

class release_2_1_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['topic_preview_version'], '2.1.0', '>=');
	}

	static public function depends_on()
	{
		return array('\vse\topicpreview\migrations\v2xx\release_2_0_0');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'styles'	=> array(
					'topic_preview_theme'	=> array('VCHAR_UNI', 'light'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'styles'	=> array(
					'topic_preview_theme',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			// remove module if updating
			array('if', array(
				array('module.exists', array('acp', 'TOPIC_PREVIEW', 'TOPIC_PREVIEW_SETTINGS')),
				array('module.remove', array('acp', 'TOPIC_PREVIEW', 'TOPIC_PREVIEW_SETTINGS')),
			)),

			// re-add module if updating
			array('module.add', array(
				'acp',
				'TOPIC_PREVIEW',
				array(
					'module_basename'	=> '\vse\topicpreview\acp\topic_preview_module',
					'modes'				=> array('settings'),
				),
			)),

			array('if', array(
				($this->config['topic_preview_jquery']),
				array('config.remove', array('topic_preview_jquery')),
			)),

			array('config.add', array('topic_preview_delay', '1500')),
			array('config.add', array('topic_preview_drift', '15')),
			array('config.add', array('topic_preview_width', '360')),

			array('config.update', array('topic_preview_avatars', '1')),
			array('config.update', array('topic_preview_version', '2.1.0')),
		);
	}
}
