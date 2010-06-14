<?php
/**
*
* @author VSE (Matt Friedman) maf675@gmail.com
* @package umil
* @version $Id install_topic_preview.php 1.0.6 2010-03-03 23:40:11GMT VSE $
* @copyright (c) 2010 VSE
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/topic_preview');

if (!file_exists($phpbb_root_path . 'umil/umil.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// We only allow a founder to install this MOD
if ($user->data['user_type'] != USER_FOUNDER)
{
	if ($user->data['user_id'] == ANONYMOUS)
	{
		login_box('', 'LOGIN');
	}
	trigger_error('NOT_AUTHORISED');
}

if (!class_exists('umil'))
{
	include($phpbb_root_path . 'umil/umil.' . $phpEx);
}

$umil = new umil(true);

$mod = array(
	'name'		=> 'Topic Preview',
	'version'	=> '1.0.6',
	'config'	=> 'topic_preview_version',
);

if (confirm_box(true))
{
	// Install the base 1.0.6 version
	if (!$umil->config_exists($mod['config']))
	{
		// We must handle the version number ourselves.
		$umil->config_add($mod['config'], $mod['version']);

		// Set other configuration variables
		$umil->config_add('topic_preview_limit', '150');
		$umil->config_add('topic_preview_strip_bbcodes', '');

		// Add the UCP option to the users table
		$umil->table_column_add(USERS_TABLE, 'user_topic_preview', array('BOOL', 1));

		// Our final action, we purge the board cache
		$umil->cache_purge();
	}

	// We are done
	trigger_error($user->lang['INSTALL_TP_SUCCESS']);
}
else
{
	confirm_box(false, $user->lang['INSTALL_TP_MOD']);
}

// Shouldn't get here.
redirect($phpbb_root_path . $user->page['page_name']);

?>