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
		global $phpbb_container;

		// Test basic module instantiation
		$module = new \vse\topicpreview\acp\topic_preview_module();
		self::assertInstanceOf('\vse\topicpreview\acp\topic_preview_module', $module);

		$mock_acp_controller = $this->getMockBuilder('\vse\topicpreview\controller\acp_controller')
			->disableOriginalConstructor()
			->onlyMethods(['handle'])
			->getMock();

		$phpbb_container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$phpbb_container
			->expects(self::once())
			->method('get')
			->with('vse.topicpreview.acp.controller')
			->willReturn($mock_acp_controller);

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
