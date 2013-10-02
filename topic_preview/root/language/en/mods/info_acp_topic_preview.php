<?php
/**
*
* topic_preview [English]
*
* @package Topic Preview
* @version $Id$
* @copyright (c) 2010 Matt Friedman
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
	'TOPIC_PREVIEW'					=> 'Topic Preview',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Topic Preview displays a short excerpt of text from the first post in a tooltip while the mouse hovers over a topic’s title.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Topic preview settings',
	'TOPIC_PREVIEW_LENGTH'			=> 'Length of topic preview text',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Enter the number of characters to display in the topic preview tooltip (default is 150). Setting the value to 0 disables this feature.',
	'DISPLAY_TOPIC_PREVIEW'			=> 'Display topic previews',
	'TOPIC_PREVIEW_STRIP'			=> 'BBcodes to hide in topic previews',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'List BBcodes with content you want removed from the preview (spoiler and hidden text BBcodes, for example). Separate multiple BBcodes using the | character, for example: spoiler|hide|code',
	'TOPIC_PREVIEW_JQUERY'			=> 'Enable jQuery Topic Previews',
	'TOPIC_PREVIEW_AVATARS'			=> 'Display user avatars (in jQuery Topic Previews)',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Display “Last post” text (in jQuery Topic Previews)',
	'CHARS'							=> 'Characters',
	'FIRST_POST'					=> 'First post',
));

?>