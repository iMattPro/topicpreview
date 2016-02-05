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

class manager_test extends base
{
	public function get_tools_test_data()
	{
		return array(
			array(true, array('remove_bbcodes', 'remove_markup')),
			array(false, array('remove_bbcodes_legacy', 'remove_markup')),
		);
	}

	/**
	 * @dataProvider get_tools_test_data
	 */
	public function test_get_tools($utils, $expected)
	{
		if ($utils && phpbb_version_compare(PHPBB_VERSION, '3.2.0-dev', '<'))
		{
			$this->markTestSkipped('Testing remove_bbcodes is for phpBB 3.2 or higher');
		}
		else if ($utils)
		{
			$container = $this->get_test_case_helpers()->set_s9e_services();
			$utils     = $container->get('text_formatter.utils');
		}
		else
		{
			$utils = null;
		}

		$manager = helper::trimTools()
			->setTools($this->config, $utils)
			->getManager();

		$tools = $manager->get_tools();

		foreach ($expected as $name)
		{
			$this->assertArrayHasKey($name, $tools);
			$this->assertInstanceOf("\\vse\\topicpreview\\core\\trim\\tools\\$name", $tools[$name]);
		}
	}

	public function get_tool_test_data()
	{
		return array(
			array('remove_markup', '\vse\topicpreview\core\trim\tools\remove_markup'),
			array('foo_bar', null),
			array('', null),
			array(array(), null),
			array(null, null),
		);
	}

	/**
	 * @dataProvider get_tool_test_data
	 */
	public function test_get_tool($name, $expected)
	{
		$manager = helper::trimTools()->getManager(array(
			new \vse\topicpreview\core\trim\tools\remove_markup()
		));

		if (is_null($expected))
		{
			$this->assertNull($manager->get_tool($name));
		}
		else
		{
			$this->assertInstanceOf($expected, $manager->get_tool($name));
		}
	}
}
