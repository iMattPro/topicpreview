<?php
/**
*
* @author VSE (Matt Friedman) maf675@gmail.com
* @package umil
* @version $Id install_topic_preview.php 4 7/10/10 11:10 AM VSE $
* @copyright (c) 2010 VSE
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

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'TP_MOD';

/*
* The name of the config variable which will hold the currently installed version
* UMIL will handle checking, setting, and updating the version itself.
*/
$version_config_name = 'topic_preview_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
*/
$language_file = 'mods/topic_preview';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
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
	'1.0.7' => array(
		// Nothing changed in this version.
	),

	// Version 1.0.9
	'1.0.9' => array(
		// Nothing changed in this version.
	),

);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>