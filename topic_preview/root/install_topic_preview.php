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
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

$mod_name = 'TOPIC_PREVIEW';
$version_config_name = 'topic_preview_version';
$language_file = 'mods/info_acp_topic_preview';

$versions = array(
	// Version 1.0.6
	'1.0.6'	=> array(
		// Set default configuration variables
		'config_add' => array(
			array('topic_preview_limit', '150'),
			array('topic_preview_strip_bbcodes', ''),
		),

		// Add the UCP option to the users table
		'table_column_add' => array(
			array(USERS_TABLE, 'user_topic_preview', array('BOOL', 1)),
		),
	),

	// Version 1.0.7
	'1.0.7' => array(),

	// Version 1.0.9
	'1.0.9' => array(),

	// Version 1.0.10
	'1.0.10' => array(),

	// Version 2.0.0
	'2.0.0' => array(
		'config_add' => array(
			array('topic_preview_last_post', '0'),
			array('topic_preview_avatars', '0'),
			array('topic_preview_jquery', '0'),
		),

		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', 'TOPIC_PREVIEW'),
			array('acp', 'TOPIC_PREVIEW', array(
					'module_basename'		=> 'topic_preview',
					'modes'					=> array('settings'),
				),
			),
		),

		'cache_purge' => '',	
	),

);

include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>