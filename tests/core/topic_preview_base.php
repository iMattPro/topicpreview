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

require_once dirname(__FILE__) . '/../../../../../includes/functions.php';
require_once dirname(__FILE__) . '/../../../../../includes/functions_content.php';
require_once dirname(__FILE__) . '/../../../../../includes/utf/utf_tools.php';

class topic_preview_base extends \phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	protected $config;
	protected $db;
	protected $dispatcher;
	protected $template;
	protected $user;
	protected $root_path;
	protected $trim_tools;

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/topic_preview.xml');
	}

	public function setUp()
	{
		parent::setUp();

		global $cache, $config, $user, $phpbb_root_path, $phpEx;

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

		$this->dispatcher = new \phpbb\event\dispatcher(new \phpbb_mock_container_builder());

		$user = $this->user = $this->getMock('\phpbb\user', array(), array(
			new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
			'\phpbb\datetime'
		));
		$this->user->expects($this->any())
			->method('optionget')
			->with($this->anything())
			->will($this->returnValueMap(array(array('viewavatars', false, true), array('viewcensors', false, false))));
		$this->user->style['style_path'] = 'prosilver';
		$this->user->data['user_topic_preview'] = 1;

		$this->trim_tools = new \vse\topicpreview\core\trim_tools($this->config);

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
	}

	protected function topic_preview_manager()
	{
		return new \vse\topicpreview\core\topic_preview(
			$this->config,
			$this->db,
			$this->dispatcher,
			$this->template,
			$this->user,
			$this->root_path,
			$this->trim_tools
		);
	}
}
