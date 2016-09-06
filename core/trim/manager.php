<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\core\trim;

class manager
{
	/** @var array Array of tools from the service collection */
	protected $tools = array();

	/**
	 * Constructor
	 *
	 * @param array $tools Array of tools from the service collection
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

		/** @var tools\tool_interface $tool */
		foreach ($this->tools as $tool)
		{
			if ($tool->is_available())
			{
				$tools[$tool->get_name()] = $tool;
			}
		}

		$tools = $this->order_tools($tools);

		return $tools;
	}

	/**
	 * Get tool by name
	 *
	 * @param string $name Name of a trim tool service
	 *
	 * @return null|tools\tool_interface A trim tool object
	 */
	public function get_tool($name)
	{
		/** @var tools\tool_interface $tool */
		foreach ($this->tools as $tool)
		{
			if ($tool->get_name() === $name)
			{
				return $tool;
			}
		}

		return null;
	}

	/**
	 * Configure tools in the proper order and remove
	 * any conflicting tools
	 *
	 * @param $tools array Array of available tools
	 *
	 * @return array Array of available tools
	 */
	protected function order_tools(array $tools)
	{
		if (isset($tools['bbcodes']))
		{
			unset($tools['bbcodes_legacy']);
		}

		ksort($tools, SORT_STRING);

		return $tools;
	}
}
