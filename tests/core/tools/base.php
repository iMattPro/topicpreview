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

require_once dirname(__FILE__) . '/../../../../../../includes/functions.php';
require_once dirname(__FILE__) . '/../../../../../../includes/functions_content.php';

class base extends \phpbb_test_case
{
	/** @var \phpbb\config\config */
	protected $config;

	public function setUp()
	{
		parent::setUp();

		global $config;

		$this->config = $config = new \phpbb\config\config(array(
			'topic_preview_limit'			=> 150,
			'topic_preview_strip_bbcodes'	=> 'quote',
		));
	}

	public function get_tools_manager(array $tools)
	{
		foreach ($tools as $tool)
		{
			$tool->set_name((new \ReflectionClass($tool))->getShortName());
		}
		return new \vse\topicpreview\core\tools\manager($tools);
	}
}
