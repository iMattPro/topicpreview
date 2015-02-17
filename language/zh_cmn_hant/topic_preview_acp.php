<?php
/**
*
* Topic Preview [Traditional Chinese]
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
	'TOPIC_PREVIEW'					=> '主題預覽',
	'TOPIC_PREVIEW_EXPLAIN'			=> '在游標停於主題名稱上方時，主題預覽會顯示原貼的一段文字。',
	'TOPIC_PREVIEW_SETTINGS'		=> '主題預覽設定',
	'TOPIC_PREVIEW_LENGTH'			=> '預覽文字長度',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> '輸入顯示於預覽中的字元數（預設為150）。設為0會取消此功能。',
	'TOPIC_PREVIEW_STRIP'			=> '預覽中隱藏的BBCode',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> '列出不想在預覽中出現的BBCode（例如spoiler或是其他隱藏文字的代碼）。請以 | 符號分隔多個BBCode，例如：spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> '於預覽中顯示大頭貼',
	'TOPIC_PREVIEW_LAST_POST'		=> '於預覽中顯示最後發表貼文的文字',
	'CHARS'							=> '字元',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> '主題預覽樣式',
	'TOPIC_PREVIEW_WIDTH'			=> '預覽的寬度（像素）',
	'TOPIC_PREVIEW_DELAY'			=> '顯示預覽',
	'TOPIC_PREVIEW_DRIFT'			=> '飄移效果（像素）',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> '漸出效果移動的距離（負數為反向）',
	'TOPIC_PREVIEW_THEME'			=> '%s的樣式',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> '請為%s挑一個預覽樣式',
	'THEME'							=> '樣式',
	'MILLISECOND'					=> '毫秒',
));
