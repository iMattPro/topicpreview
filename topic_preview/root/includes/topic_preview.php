<?php
/**
*
* @package phpBB3
* @version $Id: topic_preview.php, 7 2010/2/09 13:22:42 VSE Exp $
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
* This function originally written by RMcGirr83 for his Topic Text Hover Mod
* Modified by Matt Friedman to display smileys as text, strip URLs and trim whitespace
*/
if(!function_exists('bbcode_strip'))
{
	function bbcode_strip($text)
	{
		static $RegEx = array();
		static $bbcode_strip = 'flash';
		// Save the smileys - show them as text :)
		$text = smiley_text($text, true);
		$text = str_replace(' ... ', '', $text); // remove ' ... ' from shortened urls so they can be stripped too 
		if (empty($RegEx))
		{
			$RegEx = array('#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[(' . $bbcode_strip . ')[^\[\]]+\].*\[/(' . $bbcode_strip . ')[^\[\]]+\]#Usi', // bbcode to strip
				'#\[/?[^\[\]]+\]#mi', // Strip all bbcode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Strip out URLs
				'#[\s]+#' // Multiple spaces
			);
		}
		return trim(preg_replace($RegEx, ' ', $text));
	}
}
?>