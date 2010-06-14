<?php
/**
*
* @package phpBB3
* @version $Id: topic_title_hover_text.php, 3 2010/2/04 15:14:42 VSE Exp $
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
* Modified by Matt Friedman to display smileys as text
*/
if(!function_exists('bbcode_strip'))
{
	function bbcode_strip($text)
	{
			static $RegEx = array();
			static $bbcode_strip = 'flash';
			// Save the smileys - show them as text
			$text = smiley_text($text, true);
			// html is pretty but it may break the layout of the tooltip...let's
			// remove some common ones from the tip
//			$text_html = array('&quot;','&amp;','&#039;','&lt;','&gt;');
//			$text = str_replace($text_html,'',$text);
			if (empty($RegEx))
			{
				$RegEx = array('#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
					'#\[(' . $bbcode_strip . ')[^\[\]]+\].*\[/(' . $bbcode_strip . ')[^\[\]]+\]#Usi', // bbcode to strip
					'#\[/?[^\[\]]+\]#mi', // Strip all bbcode tags
					'#[\s]+#' // Multiple spaces
				);
			}
		return preg_replace($RegEx, ' ', $text );
	}
}
?>