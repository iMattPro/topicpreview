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
			'version'	=> '1.1.0',
			'file'		=> array('orcamx.vlexofree.com', 'software', 'mods.xml'),
		);
	}
}

?>