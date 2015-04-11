<?php
/**
*
* Topic Preview
*
* @copyright (c) 2014 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\core;

class trim_tools
{
	/** @var \phpbb\config\config */
	protected $config;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	/**
	 * Trim and clean text
	 *
	 * @param string $message Message text
	 * @param int    $length  The length to trim text to
	 * @return string Trimmed message text
	 * @access protected
	 */
	public function trim_text($message, $length)
	{
		$message = $this->remove_markup($message);

		if (utf8_strlen($message) <= $length)
		{
			return $this->tp_nl2br($message);
		}

		// trim the text to the last whitespace character before the cut-off
		$message = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($message, 0, $length));

		return $this->tp_nl2br($message) . '...';
	}

	/**
	 * Strip BBCodes, tags and links from text
	 *
	 * @param string $message Message text
	 * @return string Cleaned message text
	 * @access protected
	 */
	protected function remove_markup($message)
	{
		$message = smiley_text($message, true); // display smileys as text :)

		$message = $this->strip_bbcode_contents($message);

		static $patterns = array();

		if (empty($patterns))
		{
			// RegEx patterns based on Topic Text Hover Mod by RMcGirr83
			$patterns = array(
				'#<!-- [lmw] --><a class="postlink[^>]*>(.*<\/a[^>]*>)?<!-- [lmw] -->#Usi', // Magic URLs
				'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[/?[^\[\]]+\]#mi', // All BBCode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Remaining URLs
				'#"#', // Possible un-encoded quotes from older board conversions
				'#[ \t]{2,}#' // Multiple spaces #[\s]+#
			);
		}

		return trim(preg_replace($patterns, ' ', $message));
	}

	/**
	 * Strip special BBCodes and their contents
	 * Uses recursion to handle nested BBCodes
	 *
	 * @param string $message Message text
	 * @return string Stripped message text
	 * @access protected
	 */
	protected function strip_bbcode_contents($message)
	{
		static $regex;

		if (!isset($regex))
		{
			$strip_bbcodes = (!empty($this->config['topic_preview_strip_bbcodes'])) ? 'flash|' . trim($this->config['topic_preview_strip_bbcodes']) : 'flash';
			$regex = '#\[(' . $strip_bbcodes . ')[^\[\]]*\]((?:(?!\[\1[^\[\]]*\]).)*)\[\/\1[^\[\]]*\]#Usi';
		}

		if (preg_match($regex, $message))
		{
			return $this->strip_bbcode_contents(preg_replace($regex, '', $message));
		}

		return $message;
	}

	/**
	 * Convert and preserve line breaks
	 *
	 * @param string $message Message text
	 * @return string Message text with line breaks
	 * @access protected
	 */
	protected function tp_nl2br($message)
	{
		// http://stackoverflow.com/questions/816085/removing-redundant-line-breaks-with-regular-expressions
		return nl2br(preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $message));
	}
}
