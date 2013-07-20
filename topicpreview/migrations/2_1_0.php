<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_vse_topicpreview_migrations_2_1_0 extends phpbb_db_migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['topic_preview_version'], '2.1.0', '>=');
	}

	static public function depends_on()
	{
		return array('phpbb_ext_vse_topicpreview_migrations_2_0_0');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'update_module_basename'))),

			array('if', array(
				($this->config['topic_preview_jquery']),
				array('config.remove', array('topic_preview_jquery')),
			)),

			array('config.update', array('topic_preview_avatars', '1')),

			array('config.update', array('topic_preview_version', '2.1.0')),
		);
	}

	public function update_module_basename()
	{
		$old_module_basename = 'acp_topic_preview';
		$new_module_basename = 'phpbb_ext_vse_topicpreview_acp_topic_preview_module';
		
		$sql = 'UPDATE ' . $this->table_prefix . "modules
			SET module_basename = '$new_module_basename'
			WHERE module_basename = '$old_module_basename'";
		$this->db->sql_query($sql);
	}
}
