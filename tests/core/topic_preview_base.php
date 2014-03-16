<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\tests\core;

require_once dirname(__FILE__) . '/../../../../../includes/functions_content.php';
require_once dirname(__FILE__) . '/../../../../../includes/utf/utf_tools.php';

class topic_preview_base extends \extension_database_test_case
{
	protected $config;
	protected $db;
	protected $request;
	protected $template;
	protected $user;
	protected $root_path;

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/topic_preview.xml');
	}

	public function setUp()
	{
		parent::setUp();

		global $config, $user, $phpbb_path_helper, $phpbb_root_path, $phpEx;

		$this->db = $this->new_dbal();

		$config = $this->config = new \phpbb\config\config(array(
			'topic_preview_limit'		=> 150,
			'topic_preview_avatars'		=> 1,
			'topic_preview_last_post'	=> 1,
			'allow_avatar'				=> 1,
			));
		set_config(null, null, null, $config);

		$this->request = $this->getMock('\phpbb\request\request');

		$user = $this->user = $this->getMock('\phpbb\user');
		$this->user->expects($this->any())
			->method('optionget')
			->with($this->anything())
			->will($this->returnValueMap(array(array('viewavatars', false, true), array('viewcensors', false, false))));
		$this->user->style['style_path'] = 'prosilver';
		$this->user->data['user_topic_preview'] = 1;

		$phpbb_path_helper = new \phpbb\path_helper(
			new \phpbb\symfony_request(
				new \phpbb_mock_request()
			),
			new \phpbb\filesystem(),
			$phpbb_root_path,
			$phpEx
		);

		$this->template = new \phpbb\template\twig\twig($phpbb_path_helper, $this->config, $this->user, new \phpbb\template\context());

		$this->root_path = $phpbb_root_path;
	}

	protected function topic_preview_manager()
	{
		return new \vse\topicpreview\core\topic_preview($this->config, $this->db, $this->request, $this->template, $this->user, $this->root_path);
	}
}
