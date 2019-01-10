<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\system;

use vse\topicpreview\ext;

class ext_test extends \phpbb_test_case
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\DependencyInjection\ContainerInterface */
	protected $container;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\finder */
	protected $extension_finder;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\db\migrator */
	protected $migrator;

	public function setUp()
	{
		parent::setUp();

		// Stub the container
		$this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
			->getMock();

		// Stub the ext finder and disable its constructor
		$this->extension_finder = $this->getMockBuilder('\phpbb\finder')
			->disableOriginalConstructor()
			->getMock();

		// Stub the migrator and disable its constructor
		$this->migrator = $this->getMockBuilder('\phpbb\db\migrator')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * Data set for test_ext
	 *
	 * @return array
	 */
	public function ext_test_data()
	{
		return array(
			array(ext::PHPBB_MIN_VERSION, true), // minimum version
			array(PHPBB_VERSION, true), // current version
			array('3.1.0', false), // old version
		);
	}

	/**
	 * Test the extension can only be enabled when the minimum
	 * phpBB version requirement is satisfied.
	 *
	 * @param $version
	 * @param $expected
	 *
	 * @dataProvider ext_test_data
	 */
	public function test_ext($version, $expected)
	{
		// Instantiate config object and set config version
		$config = new \phpbb\config\config(array(
			'version' => $version,
		));

		// Mocked container should return the config object
		// when encountering $this->container->get('config')
		$this->container->expects($this->once())
			->method('get')
			->with('config')
			->willReturn($config);

		/** @var \vse\topicpreview\ext */
		$ext = new \vse\topicpreview\ext($this->container, $this->extension_finder, $this->migrator, 'vse/topicpreview', '');

		$this->assertSame($expected, $ext->is_enableable());
	}
}
