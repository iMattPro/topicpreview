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
	/** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\DependencyInjection\ContainerInterface */
	protected $container;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\finder\finder */
	protected $extension_finder;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\db\migrator */
	protected $migrator;

	protected function setUp(): void
	{
		parent::setUp();

		// Stub the container
		$this->container = $this->createMock('\Symfony\Component\DependencyInjection\ContainerInterface');

		// Stub the ext finder and disable its constructor
		$this->extension_finder = $this->createMock('\phpbb\finder\finder');

		// Stub the migrator and disable its constructor
		$this->migrator = $this->createMock('\phpbb\db\migrator');
	}

	/**
	 * Data set for test_ext
	 *
	 * @return array
	 */
	public static function ext_test_data()
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
		$this->container->expects(self::once())
			->method('get')
			->with('config')
			->willReturn($config);

		$ext = new ext($this->container, $this->extension_finder, $this->migrator, 'vse/topicpreview', '');

		self::assertSame($expected, $ext->is_enableable());
	}
}
