<?php
/**
*
* Topic Preview [Čeština]
* Pro nahlášení chyb v překladu a podporu překladu použijte stránky http://www.skolaponetu.cz. Děkujeme
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
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
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Topic Preview displays a short excerpt of text from the first post in a tooltip while the mouse hovers over a topic’s title.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Topic preview settings',
	'TOPIC_PREVIEW_LENGTH'			=> 'Úryvek z tématu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Zde máte možnost zadat počet znaků zobrazených v úryvku z tématu.<br><strong>Zadáním 0 funkci vypnete.</strong>',
	'TOPIC_PREVIEW_STRIP'			=> 'Skryté BBkódy',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Zde máte možnost zadat BBkódy, které v úryvku nebudou zobrazeny. Pro oddělení využijte znak |.<br><i>Například: list|code|quote.</i>',
	'TOPIC_PREVIEW_AVATARS'			=> 'Display avatars in topic previews',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Include “Last post” text in topic previews',
	'CHARS'							=> 'Počet znaků',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Topic preview style settings',
	'TOPIC_PREVIEW_WIDTH'			=> 'Width of topic previews (in pixels)',
	'TOPIC_PREVIEW_DELAY'			=> 'Delay before showing topic previews (in milliseconds)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animated drift effect (in pixels)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Amount of vertical animation on fadeout (use negative values to change direction).',
	'TOPIC_PREVIEW_THEME'			=> 'Theme for %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Choose a topic preview theme for %s.',
	'THEME'							=> 'theme',
	'MILLISECOND'					=> 'ms',
));
