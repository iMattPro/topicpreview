<?php
/**
*
* Topic Preview
*
* @copyright (c) 2016 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\tests\core\tools;

class helper
{
	static protected $_instance = null;

	static protected $tools = array();

	public static function trimTools()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	public function setTools($config, $utils = null)
	{
		$remove_bbcodes_legacy = new \vse\topicpreview\core\trim\tools\remove_bbcodes_legacy($config);
		$remove_bbcodes = new \vse\topicpreview\core\trim\tools\remove_bbcodes($config, $remove_bbcodes_legacy, $utils);
		$remove_markup = new \vse\topicpreview\core\trim\tools\remove_markup();
		self::$tools = array(
			$remove_bbcodes,
			$remove_bbcodes_legacy,
			$remove_markup,
		);
		return $this;
	}

	public function getManager($tools = null)
	{
		if ($tools === null)
		{
			$tools = self::$tools;
		}

		foreach ($tools as $tool)
		{
			$reflection = new \ReflectionClass($tool);
			$tool->set_name($reflection->getShortName());
		}

		return new \vse\topicpreview\core\trim\manager($tools);
	}

	public function getTrim()
	{
		return new \vse\topicpreview\core\trim\trim($this->getManager(self::$tools));
	}
}
