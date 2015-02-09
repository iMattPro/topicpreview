<?php
/**
*
* Topic Preview [Arabic]
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Translated By : Bassel Taha Alhitary - www.alhitary.net
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
	'TOPIC_PREVIEW'					=> 'مُعاينة الموضوع بالماوس ',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'عند وضع الماوس على عنوان الموضوع , يتم إظهار نافذة تحتوي على نص مُختصر لأول مشاركة في الموضوع.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'الإعدادات',
	'TOPIC_PREVIEW_LENGTH'			=> 'عدد حروف النص ',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'كم عدد حروف النص التي تريد عرضها في نافذة "مُعاينة الموضوع بالماوس" ( العدد الإفتراضي هو 150 ). القيمة صفر تعني تعطيل هذه الإضافة.',
	'TOPIC_PREVIEW_STRIP'			=> 'اخفاء أكواد البي بي ',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'تستطيع اخفاء النص الذي يحتوي على الـ BBCodes من الظهور في نافذة "مُعاينة الموضوع بالماوس" (مثل : أكواد النص spoiler و hidden ). تستطيع إضافة أكثر من كود للـ BBCodes بواسطة استخدام العلامة | بين الأكواد. مثال : spoiler|hide|code.',
	'TOPIC_PREVIEW_AVATARS'			=> 'إظهار الصور الشخصية ',
	'TOPIC_PREVIEW_LAST_POST'		=> 'إظهار “آخر مشاركة” ',
	'CHARS'							=> 'حروف',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'إعدادات التصميم',
	'TOPIC_PREVIEW_WIDTH'			=> 'عرض نافذة النص (بالبكسل) ',
	'TOPIC_PREVIEW_DELAY'			=> 'فترة التأخير قبل إظهار نافذة النص (بالملي ثانية) ',
	'TOPIC_PREVIEW_DRIFT'			=> 'مؤثرات مُتحركة (بالبكسل) ',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'كمية الحركة العمودية بإختفاء تدريجي ( استخدم القيم السالبة لتغيير الإتجاه - أقل من الصفر ).',
	'TOPIC_PREVIEW_THEME'			=> 'التصميم للاستايل %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'اختار تصميم نافذة "مُعاينة الموضوع بالماوس" للإستايل %s.',
	'THEME'							=> 'تصميم',
	'MILLISECOND'					=> 'ملي ثانية',
));
