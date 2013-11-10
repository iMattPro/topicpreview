<?php
/**
*
* mods_info_acp_topic_preview.php [Persian]
*
* @package language
* @version $Id: $
* @copyright (c) 2013 phpBB Group
* @author 2013-11-10 - Asef, Hossein, Nima
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
	'TOPIC_PREVIEW'	=> 'پیشنمایش پست',
	'TOPIC_PREVIEW_EXPLAIN'	=> 'پیش نمایش موضوع و نمایش گزیده ای کوتاه از متن از اولین پست در یک tooltip در حالی که نشانه گر ماوس روی آن است.',
	'TOPIC_PREVIEW_SETTINGS'	=> 'تنظیمات پیشنمایش پستها',
	'TOPIC_PREVIEW_LENGTH'	=> 'طول موضوع متن پیشنمایش',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'تعداد حروف نمایش دهنده در toplip که به طور پیشفرض 150 حرف است.      برای بکار انداختن این ویژگی 0 را وارد کنید.',
	'DISPLAY_TOPIC_PREVIEW'	=> 'نمایش مود پیشنمایش پستها',
	'TOPIC_PREVIEW_STRIP'	=> 'مخفی کردن BBcode ها',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'لیست BBcode هایی که نمیخواهید در toplip نمایش داده شود.   برای مثال متن های پنهانی که برای میهمانان قابل مشاهده نیست.',
	'TOPIC_PREVIEW_JQUERY'	=> 'فعال کردن جی کوئری در پیشنمایش',
	'TOPIC_PREVIEW_AVATARS'	=> 'نمایش آواتار کاربر در جی کوئری',
	'TOPIC_PREVIEW_LAST_POST'	=> 'نمایش آخرین پست موضوع در جی کوئری',
	'CHARS'	=> 'کاراکتر',
	'FIRST_POST'	=> 'اولین پست',
));

?>