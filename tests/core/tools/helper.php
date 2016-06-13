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

use vse\topicpreview\core\trim\tools;

class helper
{
	static protected $_instance;

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
		$trim_bbcodes_legacy = new tools\bbcodes_legacy($config);
		$trim_bbcodes = new tools\bbcodes($config, $trim_bbcodes_legacy, $utils);
		$trim_markup = new tools\markup();
		self::$tools = array(
			$trim_bbcodes,
			$trim_bbcodes_legacy,
			$trim_markup,
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
