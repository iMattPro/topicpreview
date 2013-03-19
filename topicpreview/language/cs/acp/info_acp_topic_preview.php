<?php
/**
 *
 * info_acp_topic_preview [Čeština]
 * 
 * @package language
 * @copyright (c) 2013 Matt Friedman (Pro nahlášení chyb v překladu a podporu překladu použijte stránky http://www.skolaponetu.cz. Děkujeme)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'TOPIC_PREVIEW'					=> 'Úryvek z tématu',
	'TOPIC_PREVIEW_DISPLAY'			=> 'Zobrazit úryvek z tématu',	
	'TOPIC_PREVIEW_LENGTH'			=> 'Úryvek z tématu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Zde máte možnost zadat počet znaků zobrazených v úryvku z tématu.<br /><strong>Zadáním 0 funkci vypnete.</strong>',
	'TOPIC_PREVIEW_STRIP'			=> 'Skryté BBkódy',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Zde máte možnost zadat BBkódy, které v úryvku nebudou zobrazeny. Pro oddělení využijte znak |.<br /><i>Například: list|code|quote.</i>',
	'CHARS'							=> 'Počet znaků',
));
