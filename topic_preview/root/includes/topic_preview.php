<?php
/**
*
* @package phpBB3
* @version $Id: topic_preview.php, 11 2010/4/03 23:13:42 VSE Exp $
* @copyright (c) Matt Friedman
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Include only once.
* This function's RegEx originally written by RMcGirr83 for his Topic Text Hover Mod
* Modified by Matt Friedman to display smileys as text, strip URLs, custom BBcodes and trim whitespace
*/

function bbcode_strip($text)
{
	global $config;

	static $RegEx = array();
	$bbcode_strip = empty($config['topic_preview_strip_bbcodes']) ? 'flash' : 'flash|' . trim($config['topic_preview_strip_bbcodes']);
	$text = smiley_text($text, true); // Save the smileys - show them as text :)
	if (empty($RegEx))
	{
		$RegEx = array(
			'#<a class="postlink[^>]*>(.*<\/a[^>]*>)?#', // Strip magic URLs			
			'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
			'#\[(' . $bbcode_strip . ')[^\[\]]+\].*\[/(' . $bbcode_strip . ')[^\[\]]+\]#Usi', // bbcode to strip
			'#\[/?[^\[\]]+\]#mi', // Strip all bbcode tags
			'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Strip remaining URLs
			'#"#', // Possible quotes from older board conversions
			'#[\s]+#' // Multiple spaces
		);
	}
	return trim(preg_replace($RegEx, ' ', $text));
}

function trim_topic_preview($string, $limit)
{
	$text = bbcode_strip($string);
	if (utf8_strlen($text) >= $limit)
	{
		$text = (utf8_strlen($text) > $limit) ? utf8_substr($text, 0, $limit) : $text;
		// use last space before the character limit as the break-point, if one exists
		$new_limit = utf8_strrpos($text, ' ') != false ? utf8_strrpos($text, ' ') : $limit;
		return utf8_substr($text, 0, $new_limit) . '...';
	}
	return $text;
}
?>