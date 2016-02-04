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

	/** @var \phpbb\textformatter\s9e\utils|null */
	protected $text_formatter_utils;

	/** @var string|array BBcodes to be removed */
	protected $strip_bbcodes;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config
	 * @param \phpbb\textformatter\s9e\utils|null $text_formatter_utils
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\textformatter\s9e\utils $text_formatter_utils = null)
	{
		$this->config = $config;
		$this->text_formatter_utils = $text_formatter_utils;
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

		$message = $this->remove_bbcode_contents($message);

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
	 * Remove specified BBCodes and their contents
	 *
	 * @param string $message Message text
	 * @return string Stripped message text
	 * @access protected
	 */
	protected function remove_bbcode_contents($message)
	{
		// If text formatter is not available, use legacy bbcode stripper
		if ($this->text_formatter_utils === null)
		{
			return $this->remove_bbcode_contents_legacy($message);
		}

		// Create the data array of bbcodes to strip
		if (!isset($this->strip_bbcodes) || !is_array($this->strip_bbcodes))
		{
			$this->strip_bbcodes = (!empty($this->config['topic_preview_strip_bbcodes'])) ? explode('|', $this->config['topic_preview_strip_bbcodes']) : array();
			array_unshift($this->strip_bbcodes, 'flash');
		}

		// Strip the bbcodes from the message
		foreach ($this->strip_bbcodes as $bbcode)
		{
			$message = $this->text_formatter_utils->remove_bbcode($message, $bbcode);
		}

		return $this->text_formatter_utils->unparse($message);
	}

	/**
	 * Remove specified BBCodes and their contents
	 * Uses recursion to handle nested BBCodes
	 * This method for b.c. with phpBB 3.1.x
	 *
	 * @param string $message Message text
	 * @return string Stripped message text
	 * @access protected
	 */
	protected function remove_bbcode_contents_legacy($message)
	{
		// Create the data string of bbcodes to strip
		if (!isset($this->strip_bbcodes) || is_array($this->strip_bbcodes))
		{
			$strip_bbcodes = (!empty($this->config['topic_preview_strip_bbcodes'])) ? 'flash|' . trim($this->config['topic_preview_strip_bbcodes']) : 'flash';
			$this->strip_bbcodes = '#\[(' . $strip_bbcodes . ')[^\[\]]*\]((?:(?!\[\1[^\[\]]*\]).)*)\[\/\1[^\[\]]*\]#Usi';
		}

		// Strip the bbcodes from the message
		if (preg_match($this->strip_bbcodes, $message))
		{
			return $this->remove_bbcode_contents_legacy(preg_replace($this->strip_bbcodes, '', $message));
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
