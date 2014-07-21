<?php
/**
*
* Topic Preview [Polski]
* Translated by Pico88
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
	'TOPIC_PREVIEW'					=> 'Podgląd tematu',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Wyświetli fragment tekstu z pierwszego postu w dymku, gdy kursor myszy pojawi się nad tytułem tematu.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Ustawienie podglądu tematu',
	'TOPIC_PREVIEW_LENGTH'			=> 'Długość tekstu w podglądzie tematu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Wpisz liczbę znaków do wyświetlenia w dymku (domyślnie 150). Ustawienie wartości 0 wyłącza tę funkcję.',
	'TOPIC_PREVIEW_STRIP'			=> 'Ukryj BBKody w podglądzie tematu',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista z zawartością BBKodów, które chcesz usunąć z podglądu (np. BBKody: spoiler, ukryty tekst, kod). Kilka BBKodów oddziel znakiem | , np.: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Wyświetl awatary w podglądzie tematu',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Wyświetl fragment ostatniego postu w podglądzie tematu',
	'CHARS'							=> 'Znaki',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Ustawienia stylu podglądu temat',
	'TOPIC_PREVIEW_WIDTH'			=> 'Szerokość podglądu temat (w pikselach)',
	'TOPIC_PREVIEW_DELAY'			=> 'Opóźnienie przed wyświetleniem podglądu tematu (w milisekundach)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animowany efekt przesunięcia (w pikselach)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Wartość pionowej animacji zanikania (użyj wartości ujemnych, aby zmienić kierunek animacji).',
	'TOPIC_PREVIEW_THEME'			=> 'Motyw %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Wybierz motyw podglądu temat na %s.',
	'THEME'							=> 'motyw',
	'MILLISECOND'					=> 'ms',
));
