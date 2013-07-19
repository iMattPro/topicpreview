<?php
/**
*
* topic_preview [Polski]
*
* @package Topic Preview
* @version $Id$
* @copyright (c) 2010 Matt Friedman (translated by Pico88)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Podgląd tematu wyświetla fragment pierwszego postu w "dymku" podczas najechania kursorem myszy na tytuł tematu.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Ustawienia podglądu tematu',
	'TOPIC_PREVIEW_LENGTH'			=> 'Długość tekstu w podglądzie tematu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Podaj liczbę znaków do wyświetlenia w "dymku" podglądu tematu(domyślnie 150). Wartość zero (0) wyłącza tę funkcję.',
	'DISPLAY_TOPIC_PREVIEW'			=> 'Wyświetl podgląd tematu',
	'TOPIC_PREVIEW_STRIP'			=> 'Ukryj znacznki BBCode w podglądzie tematu',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista znaczników BBCode, które chcesz usunąć z podglądu (np.: spoiler, ukryty tekst, kod). Kilka znaczników BBCode oddziel znakiem | , np.: spoiler|hide|code',
	'TOPIC_PREVIEW_JQUERY'			=> 'Włącz Podgląd Tematu jQuery',
	'TOPIC_PREVIEW_AVATARS'			=> 'Wyświetl awatary (w Poglądzie Tematów jQuery)',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Wyświetl fragment ostatniego postu (w Poglądzie Tematów jQuery)',
	'CHARS'							=> 'Znaki',
	'FIRST_POST'					=> 'Pierwszy post',
));

?>