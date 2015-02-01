<?php
/**
*
* Topic Preview [Turkish]
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
	'TOPIC_PREVIEW'					=> 'Konu Önizleme',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Konu Önizleme fare imleci konu başlığına getirildiğinde ilk mesajdan kısa bir metin gösterir.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Konu Önizleme ayarları',
	'TOPIC_PREVIEW_LENGTH'			=> 'Konu önizleme metin uzunluğu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Konu önizlemede gösterilecek karakter sayısı (varsayılan 150). 0 yazmak bu özelliği kapatır.',
	'TOPIC_PREVIEW_STRIP'			=> 'Konu önizlemede gizlenecek BBCodelar',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Önzilemeden kaldırlmasını istediğiniz BBCode listesi (spoiler ve hidden text BBCodes, örnek olarak). Birden fazla BBCode için aralarına | karakterini kullanın, örneğin: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Konu önizlemelerde avatarları göster',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Konu önizlemelerde “Son mesaj” metnini göster',
	'CHARS'							=> 'Karakter',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Konu özizleme stil ayarları',
	'TOPIC_PREVIEW_WIDTH'			=> 'Konu önizleme genişliği(piksel olaraks)',
	'TOPIC_PREVIEW_DELAY'			=> 'Konu önizlemeyi gösterme öncesi geçen süre (milisaniye olarak)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animasyonlu sürüklenme efekti (piksel olarak)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Karartmada dikey animasyon miktarı (yönü değiştirmek için negatif değerler kullanın).',
	'TOPIC_PREVIEW_THEME'			=> '%s için tema',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> '%s için bir konu önizleme teması seç.',
	'THEME'							=> 'tema',
	'MILLISECOND'					=> 'ms',
));
