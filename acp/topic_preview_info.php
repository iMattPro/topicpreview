<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\acp;

/**
* @package module_install
*/
class topic_preview_info
{
	function module()
	{
		return array(
			'filename'	=> '\vse\topicpreview\acp\topic_preview_module',
			'title'		=> 'TOPIC_PREVIEW',
			'version'	=> '2.1.0',
			'modes'		=> array(
				'settings'	=> array('title' => 'TOPIC_PREVIEW_SETTINGS', 'auth' => 'ext_vse/topicpreview && acl_a_board', 'cat' => array('TOPIC_PREVIEW')),
			),
		);
	}
}
