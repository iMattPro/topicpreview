<?php
/**
*
* Topic Preview [Bulgarian]
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
	'TOPIC_PREVIEW'					=> 'Topic Preview: предварителен преглед на теми',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Расширението Topic Preview позволява показването на малък фрагмент от първото съобщение при насочване на курсора на мишката върху заглавието на темта.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Общи параметри',
	'TOPIC_PREVIEW_LENGTH'			=> 'Дължина на текста',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Количество символи, което ще бъде показано в изплуващата подсказка, за предварителен преглед на темта (по подразбиране 150). Въведете 0 за изключване на функцията на предварителен преглед.',
	'TOPIC_PREVIEW_STRIP'			=> 'BB-кодове за забраняване',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Списък на BB-кодове, които трябва да бъдат премахнати от предварителният преглед (например, spoiler или hidden). За указаване на няколко BB-кода исполвайте символ | за разделяне. Например: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Включване на показването на аватари',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Допълнително показване на «Последно съобщение»',
	'CHARS'							=> 'символи',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Параметри на стила',
	'TOPIC_PREVIEW_WIDTH'			=> 'Широчина на изплуващата подсказка (в пиксели)',
	'TOPIC_PREVIEW_DELAY'			=> 'Забавяне преди показването на изплуващата подсказка(в миллисекунди)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Эффект на преместване (в пиксели)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Големина на  преместване по вертикал при анимацията на изчезване на изплуващата подсказка (отрицатените значения променят направлението на анимацията).',
	'TOPIC_PREVIEW_THEME'			=> 'Тема за стила %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Тема на формата на зплуващата подсказка за стила %s.',
	'THEME'							=> 'Тема',
	'MILLISECOND'					=> 'мс',
));
