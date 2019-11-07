<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2014 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\core;

class base extends \phpbb_database_test_case
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template|\PHPUnit_Framework_MockObject_MockObject */
	protected $template;

	/** @var \vse\topicpreview\core\trim\trim */
	protected $trim;

	/** @var \phpbb\user|\PHPUnit_Framework_MockObject_MockObject */
	protected $user;

	/** @var string */
	protected $root_path;

	protected static function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/topic_preview.xml');
	}

	public function setUp(): void
	{
		parent::setUp();

		global $cache, $config, $user, $phpbb_dispatcher, $phpbb_root_path, $phpEx;

		$this->root_path = $phpbb_root_path;

		$this->db = $this->new_dbal();
		$cache = new \phpbb_mock_cache;

		$config = $this->config = new \phpbb\config\config(array(
			'topic_preview_strip_bbcodes'	=> 'quote',
			'topic_preview_limit'			=> 150,
			'topic_preview_avatars'			=> 1,
			'topic_preview_last_post'		=> 1,
			'allow_avatar'					=> 1,
		));

		$phpbb_dispatcher = $this->dispatcher = new \phpbb\event\dispatcher(new \phpbb_mock_container_builder());

		$this->language = new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx));
		$user = $this->user = $this->getMockBuilder('\phpbb\user')
			->setConstructorArgs(array($this->language, '\phpbb\datetime'))
			->setMethods(array())
			->getMock();
		$this->user->method('optionget')
			->with($this->anything())
			->willReturnMap(array(array('viewavatars', false, true), array('viewcensors', false, false)));
		$this->user->style['style_path'] = 'prosilver';
		$this->user->data['user_topic_preview'] = 1;
		$this->trim = tools\helper::trimTools()
			->setTools($config)
			->getTrim();
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
	}

	protected function get_topic_preview_data()
	{
		return new \vse\topicpreview\core\data(
			$this->config,
			$this->user
		);
	}

	protected function get_topic_preview_display()
	{
		return new \vse\topicpreview\core\display(
			$this->config,
			$this->dispatcher,
			$this->language,
			$this->template,
			$this->trim,
			$this->user,
			$this->root_path
		);
	}
}
