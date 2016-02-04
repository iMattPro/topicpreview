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

class manager
{
	/** @var array Array of tools from the service collection */
	protected $tools = array();

	/**
	 * Constructor
	 *
	 * @param array $tools Array of tools from the service collection
	 * @access public
	 */
	public function __construct($tools)
	{
		$this->tools = $tools;
	}

	/**
	 * Get all available tools
	 *
	 * @return array Array of available tools
	 */
	public function get_tools()
	{
		$tools = array();

		/** @var tool_interface $tool */
		foreach ($this->tools as $tool)
		{
			if ($tool->is_available())
			{
				$tools[$tool->get_name()] = $tool;
			}
		}

		if (isset($tools['remove_bbcodes']))
		{
			unset($tools['remove_bbcodes_legacy']);
		}

		return $tools;
	}

	/**
	 * Get tool by name
	 *
	 * @param string $name
	 * @return null|tool_interface
	 */
	public function get_tool($name)
	{
		/** @var tool_interface $tool */
		foreach ($this->tools as $tool)
		{
			if ($tool->get_name() == $name)
			{
				return $tool;
			}
		}

		return null;
	}
}
