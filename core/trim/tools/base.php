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

abstract class base implements tool_interface
{
	/** @var string Name of tool */
	protected $name;

	/** @var string The text to parse */
	protected $text;

	/**
	 * @inheritdoc
	 */
	public function is_available()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function set_text($text)
	{
		$this->text = $text;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}
}
