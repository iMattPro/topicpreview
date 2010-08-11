<?php
/**
*
* @package acp
* @version $Id: topic_preview_check_version.php 3 8/10/10 10:34 PM VSE $
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
			'file'		=> array('vse.heliohost.org', 'software', 'mods.xml'),
		);
	}
}

?>