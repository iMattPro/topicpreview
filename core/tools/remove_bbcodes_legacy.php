<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\core\tools;

use phpbb\config\config;

class remove_bbcodes_legacy extends base
{
	/** @var config */
	protected $config;

	/** @var string Regex data of BBCodes to remove */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param config $config
	 * @access public
	 */
	public function __construct(config $config)
	{
		$this->config = $config;
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		return $this->set_data()->remove_bbcodes();
	}

	/**
	 * @inheritdoc
	 */
	public function set_data()
	{
		if (!isset($this->data) || is_array($this->data))
		{
			$strip_bbcodes = (!empty($this->config['topic_preview_strip_bbcodes'])) ? 'flash|' . trim($this->config['topic_preview_strip_bbcodes']) : 'flash';
			$this->data = '#\[(' . $strip_bbcodes . ')[^\[\]]*\]((?:(?!\[\1[^\[\]]*\]).)*)\[\/\1[^\[\]]*\]#Usi';
		}

		return $this;
	}

	/**
	 * Remove specified BBCodes and their contents
	 * Uses recursion to handle nested BBCodes
	 *
	 * @return string Stripped message text
	 * @access protected
	 */
	protected function remove_bbcodes()
	{
		if (preg_match($this->data, $this->text))
		{
			$this->text = preg_replace($this->data, '', $this->text);
			return $this->remove_bbcodes();
		}

		return $this->text;
	}
}
