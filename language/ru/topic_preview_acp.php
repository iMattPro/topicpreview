<?php
/**
*
* Topic Preview [Russian]
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
	'TOPIC_PREVIEW'					=> 'Topic Preview: предварительный просмотр тем',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Расширение Topic Preview позволяет отобразить небольшой фрагмент первого сообщения при наведении курсора мыши на название темы.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Общие параметры',
	'TOPIC_PREVIEW_LENGTH'			=> 'Длина текста',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Количество символов, которое будет отображено во всплывающей подсказке для предварительного просмотра темы (по умолчанию 150). Введите 0 для отключения функции предварительного просмотра.',
	'TOPIC_PREVIEW_STRIP'			=> 'BB-коды для скрытия',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Перечень BB-кодов, которые надо удалить из предварительного просмотра (например, spoiler или hidden). Для указания нескольких BB-кодов используйте символ | для разделения. Например: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Включить отображение аватаров',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Дополнительно отображать «Последнее сообщение»',
	'CHARS'							=> 'символов',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Параметры стиля',
	'TOPIC_PREVIEW_WIDTH'			=> 'Ширина всплывающей подсказки (в пикселях)',
	'TOPIC_PREVIEW_DELAY'			=> 'Задержка перед показом всплывающей подсказки (в миллисекундах)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Эффект смещения (в пикселях)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Величина смещения по вертикали при анимации исчезания всплывающей подсказки (отрицательные значения меняют направление анимации).',
	'TOPIC_PREVIEW_THEME'			=> 'Тема для стиля %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Тема оформления всплывающей подсказки для стиля %s.',
	'THEME'							=> '',
	'MILLISECOND'					=> 'мс',
));
