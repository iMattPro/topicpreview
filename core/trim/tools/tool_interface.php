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

interface tool_interface
{
	/**
	 * Is this tool available to run?
	 *
	 * @return bool True if available, false otherwise
	 */
	public function is_available();

	/**
	 * Run this tool
	 *
	 * @return string Stripped message text
	 */
	public function run();

	/**
	 * Set the text property
	 *
	 * @param string $text Text to process
	 *
	 * @return tool_interface
	 */
	public function set_text($text);

	/**
	 * Set the data array of data to remove
	 *
	 * @return tool_interface
	 */
	public function set_data();

	/**
	 * Get the tool name
	 *
	 * @return string Name of a trim tool service
	 */
	public function get_name();

	/**
	 * Set the tool name
	 *
	 * @param string $name Name of a trim tool service
	 */
	public function set_name($name);
}
