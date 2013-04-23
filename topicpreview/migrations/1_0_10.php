<?php
/**
 *
 * @package Precise Similar Topics II
 * @copyright (c) 2010 Matt Friedman
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

class phpbb_ext_vse_topicpreview_migrations_1_0_10 extends phpbb_db_migration
{

	public function effectively_installed()
	{
		return version_compare($this->config['topic_preview_version'], '1.0.10', '>=');
	}

	static public function depends_on()
	{
		return array('phpbb_ext_vse_topicpreview_migrations_1_0_9');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('topic_preview_version', '1.0.10')),
		);
	}
}
