<?php
/**
*
* Topic Preview [Simplified Chinese]
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
	'TOPIC_PREVIEW'					=> '主题预览',
	'TOPIC_PREVIEW_EXPLAIN'			=> '在游标停于主题名称上方时，主题预览会显示原贴的一段文字。',
	'TOPIC_PREVIEW_SETTINGS'		=> '主题预览参数',
	'TOPIC_PREVIEW_LENGTH'			=> '预览文字长度',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> '输入显示于预览中的字元数（预设为150）。设为0会取消此功能。',
	'TOPIC_PREVIEW_STRIP'			=> '预览中隐藏的BBCode',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> '列出不想在预览中出现的BBCode（例如spoiler或是其他隐藏文字的代码）。请以 | 符号分隔多个BBCode，例如：spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> '于预览中显示大头贴',
	'TOPIC_PREVIEW_LAST_POST'		=> '于预览中显示最后发表贴文的文字',
	'CHARS'							=> '字元',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> '主题预览样式',
	'TOPIC_PREVIEW_WIDTH'			=> '预览的宽度（像素）',
	'TOPIC_PREVIEW_DELAY'			=> '显示预览',
	'TOPIC_PREVIEW_DRIFT'			=> '飘移效果（像素）',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> '渐出效果移动的距离（负数为反向）',
	'TOPIC_PREVIEW_THEME'			=> '%s的样式',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> '请为%s挑一个预览样式',
	'THEME'							=> '样式',
	'MILLISECOND'					=> '毫秒',
));
