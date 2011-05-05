<?php
/**
*
* @package acp
* @version $Id: topic_preview_check_version.php 4 5/5/11 1:02 AM VSE $
* @copyright (c) 2007 StarTrekGuide
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package mod_version_check
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class topic_preview_check_version
{
	function version()
	{
		return array(
			'author'	=> 'VSE',
			'title'		=> 'Topic Preview',
			'tag'		=> 'topic_preview',
			'version'	=> '1.0.10',
			'file'		=> array('www.orca-music.com', 'software', 'mods.xml'),
		);
	}
}

?>