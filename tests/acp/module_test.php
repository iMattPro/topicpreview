<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\acp;

class module_test extends \phpbb_test_case
{
	/**
	 * Test the acp module instance
	 */
	public function test_module()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		// Test basic module instantiation
		$module = new \vse\topicpreview\acp\topic_preview_module();
		self::assertInstanceOf('\vse\topicpreview\acp\topic_preview_module', $module);

		// Test calling module->main()
		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$lang = new \phpbb\language\language($lang_loader);

		$mock_acp_controller = $this->getMockBuilder('\vse\topicpreview\controller\acp_controller')
			->disableOriginalConstructor()
			->setMethods(array('handle'))
			->getMock();

		$phpbb_container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
			->getMock();
		$phpbb_container
			->expects(self::exactly(2))
			->method('get')
			->withConsecutive(
				['language'], ['vse.topicpreview.acp.controller']
			)
			->willReturnOnConsecutiveCalls(
				$lang, $mock_acp_controller
			);

		$module->main();
	}

	public function test_info()
	{
		$info_class = new \vse\topicpreview\acp\topic_preview_info();
		$info_array = $info_class->module();
		self::assertArrayHasKey('filename', $info_array);
		self::assertEquals('\vse\topicpreview\acp\topic_preview_module', $info_array['filename']);
	}
}
