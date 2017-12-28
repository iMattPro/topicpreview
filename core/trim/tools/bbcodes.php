<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\core\trim\tools;

use phpbb\config\config;
use phpbb\textformatter\s9e\utils;

class bbcodes extends base
{
	/** @var config */
	protected $config;

	/** @var bbcodes_legacy */
	protected $trim_bbcodes_legacy;

	/** @var utils|null */
	protected $text_formatter_utils;

	/** @var array Data array of BBCodes to remove */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param config         $config               Config object
	 * @param bbcodes_legacy $trim_bbcodes_legacy  Legacy BBCodes trim tool
	 * @param utils|null     $text_formatter_utils Text Formatter Utils
	 */
	public function __construct(config $config, bbcodes_legacy $trim_bbcodes_legacy, utils $text_formatter_utils = null)
	{
		$this->config = $config;
		$this->trim_bbcodes_legacy = $trim_bbcodes_legacy;
		$this->text_formatter_utils = $text_formatter_utils;
	}

	/**
	 * @inheritdoc
	 */
	public function is_available()
	{
		return ($this->text_formatter_utils !== null);
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		// If text is not formatted as expected, use legacy bbcode stripper
		if (!$this->s9e_format())
		{
			return $this->trim_bbcodes_legacy
				->set_text($this->text)
				->run();
		}

		return $this->set_data()->process();
	}

	/**
	 * @inheritdoc
	 */
	public function set_data()
	{
		if (!isset($this->data) || !is_array($this->data))
		{
			$this->data = !empty($this->config['topic_preview_strip_bbcodes']) ? explode('|', $this->config['topic_preview_strip_bbcodes']) : array();
			array_unshift($this->data, 'flash');
		}

		return $this;
	}

	/**
	 * Remove specified BBCodes and their contents
	 *
	 * @return string Stripped message text
	 */
	protected function process()
	{
		foreach ($this->data as $bbcode)
		{
			$this->text = $this->text_formatter_utils->remove_bbcode($this->text, $bbcode);
		}

		return $this->text_formatter_utils->unparse($this->text);
	}

	/**
	 * Is the message s9e formatted
	 *
	 * @return bool True if message is s9e formatted, false otherwise
	 */
	protected function s9e_format()
	{
		return (bool) preg_match('/^<[rt][ >]/', $this->text);
	}
}
