<?php
/**
*
* Topic Preview
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\acp;

/**
* @package module_install
*/
class topic_preview_info
{
	public function module()
	{
		return array(
			'filename'	=> '\vse\topicpreview\acp\topic_preview_module',
			'title'		=> 'TOPIC_PREVIEW',
			'modes'		=> array(
				'settings'	=> array('title' => 'TOPIC_PREVIEW_SETTINGS', 'auth' => 'ext_vse/topicpreview && acl_a_board', 'cat' => array('TOPIC_PREVIEW')),
			),
		);
	}
}
