<?php
/**
*
* topic_preview [English]
*
* @package language
* @version $Id: topic_preview.php, 7 2010-06-13 23:47:11GMT VSE $
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
	'TOPIC_PREVIEW'			=> 'Length of Topic Preview Text',
	'TOPIC_PREVIEW_EXPLAIN'	=> 'Will show some text from the first topic in a tooltip while the mouse hovers over the topic title. Enter the number of characters to display in the tooltip (default is 150). Setting the value to 0 disables this feature.',
	'CHARS'   				=> 'Characters',
	'DISPLAY_TOPIC_PREVIEW' => 'Display topic previews',	
	'TOPIC_PREVIEW_STRIP'			=> 'BBcodes to hide in Topic Preview',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'List BBcodes with content you want removed from the preview (spoiler and hidden text BBcodes, for example). Separate multiple BBcodes using the | character, for example: spoiler|hide|code',

	// Installation language vars (UMIL)
	'TP_MOD'	=> 'Topic Preview',
));

?>