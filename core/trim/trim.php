<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2014 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\core\trim;

class trim
{
	/** @var array An array of trim tools */
	protected $tools;

	/**
	 * Constructor
	 *
	 * @param manager $manager
	 * @access public
	 */
	public function __construct(manager $manager)
	{
		$this->tools = $manager->get_tools();
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
		// display smileys as text :)
		$message = smiley_text($message, true);

		// run the text through our trim tools (they filter out bbcodes and other markup)
		/** @var tools\tool_interface $tool */
		foreach ($this->tools as $tool)
		{
			$message = $tool->set_text($message)->run();
		}

		if (utf8_strlen($message) <= $length)
		{
			return $this->tp_nl2br($message);
		}

		// trim the text to the last whitespace character before the cut-off
		$message = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($message, 0, $length));

		return $this->tp_nl2br($message) . '...';
	}

	/**
	 * Convert and preserve line breaks
	 * http://stackoverflow.com/questions/816085/removing-redundant-line-breaks-with-regular-expressions
	 *
	 * @param string $message Message text
	 * @return string Message text with line breaks
	 * @access protected
	 */
	protected function tp_nl2br($message)
	{
		return nl2br(preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $message));
	}
}
