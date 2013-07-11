<?php
/**
*
* info_acp_topic_preview [Polski]
* 
* @package language
* @copyright (c) 2013 Matt Friedman (translated by Pico88)
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
	'TOPIC_PREVIEW'					=> 'Podgląd Tematu',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Pokaże fragment tekstu z pierwszego tematu w dymku, gdy kursor myszy pojawi się nad tytułem tematu.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Topic preview settings',
	'TOPIC_PREVIEW_DISPLAY'			=> 'Wyświetl Podgląd Tematu',
	'TOPIC_PREVIEW_LENGTH'			=> 'Długość tekstu w Podglądzie Tematu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Wpisz liczbę znaków do wyświetlenia w dymku (domyślnie jest 150). Ustawienie wartości 0 wyłącza tę funkcję.',
	'TOPIC_PREVIEW_STRIP'			=> 'Ukryj BBkody w Podglądzie Tematu',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista z zawartością BBKodów, które chcesz usunąć podglądu (na przykład BBKody: spoiler, ukryty tekst, kod). Kilka BBKodów oddziel znakiem | , na przykład: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Display avatars in topic previews',
	'CHARS'							=> 'Znaki',
));
