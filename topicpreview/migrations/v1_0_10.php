<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\migrations;

class v1_0_10 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['topic_preview_version'], '1.0.10', '>=');
	}

	static public function depends_on()
	{
		return array('\vse\topicpreview\migrations\v1_0_9');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('topic_preview_version', '1.0.10')),
		);
	}
}
