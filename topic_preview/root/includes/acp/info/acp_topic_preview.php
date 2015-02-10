<?php
/**
*
* @package Topic Preview
* @version $Id$
* @copyright (c) 2010 Matt Friedman
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_topic_preview_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_topic_preview',
			'title'		=> 'TOPIC_PREVIEW',
			'version'	=> '2.0.2',
			'modes'		=> array(
				'settings'	=> array('title' => 'TOPIC_PREVIEW_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('TOPIC_PREVIEW')),
			),
		);
	}
}
