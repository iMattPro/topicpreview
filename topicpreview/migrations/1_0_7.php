<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_vse_topicpreview_migrations_1_0_7 extends phpbb_db_migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['topic_preview_version'], '1.0.7', '>=');
	}

	static public function depends_on()
	{
		return array('phpbb_ext_vse_topicpreview_migrations_1_0_6');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('topic_preview_version', '1.0.7')),
		);
	}
}
