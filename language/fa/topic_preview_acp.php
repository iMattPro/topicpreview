<?php
/**
*
* Topic Preview [Persian]
* Translated by Meisam Noubari from IRAN in php-bb.ir
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
	'TOPIC_PREVIEW'					=> 'پیشنمایش پست',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'پیش نمایش موضوع و نمایش گزیده ای کوتاه از متن از اولین پست در یک tooltip در حالی که نشانه گر ماوس روی آن است.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'تنظیمات پیشنمایش پستها',
	'TOPIC_PREVIEW_LENGTH'			=> 'طول موضوع متن پیشنمایش',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'تعداد حروف نمایش دهنده در toplip که به طور پیشفرض 150 حرف است.      برای بکار انداختن این ویژگی 0 را وارد کنید.',
	'TOPIC_PREVIEW_STRIP'			=> 'مخفی کردن BBcode ها',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'لیست BBcode هایی که نمیخواهید در toplip نمایش داده شود.   برای مثال متن های پنهانی که برای میهمانان قابل مشاهده نیست.',
	'TOPIC_PREVIEW_AVATARS'			=> 'نمایش آواتار کاربر در جی کوئری',
	'TOPIC_PREVIEW_LAST_POST'		=> 'نمایش آخرین پست موضوع در جی کوئری',
	'CHARS'							=> 'کاراکتر',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'تنظیمات قالب افزونه"پیش نمایش پست"',
	'TOPIC_PREVIEW_WIDTH'			=> 'عرض جدول ( بر حسب پیکسل )',
	'TOPIC_PREVIEW_DELAY'			=> 'تاخیر در نمایش ( بر حسب میلی ثانیه )',
	'TOPIC_PREVIEW_DRIFT'			=> 'جلوه انیمیشن ( بر حسب پیکسل )',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> ' مقدار انیمیشن عمودی(استفاده از مقادیر منفی برای تغییر جهت).',
	'TOPIC_PREVIEW_THEME'			=> 'قالب برای %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'انتخاب قالب برای %s',
	'THEME'							=> 'قالب',
	'MILLISECOND'					=> 'میلی ثانیه',
));
