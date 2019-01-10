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

require_once __DIR__ . '/../../../../../includes/functions_acp.php';

class controller_test extends \phpbb_database_test_case
{
	protected static function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/topic_preview.xml');
	}

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \vse\topicpreview\controller\acp_controller */
	protected $controller;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\request\request */
	protected $request;

	/** @var \vse\topicpreview\core\settings */
	protected $settings;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	public function setUp()
	{
		parent::setUp();

		global $config, $phpbb_extension_manager, $phpbb_dispatcher, $request, $template, $phpbb_root_path, $phpEx;

		$cache = new \phpbb_mock_cache;
		$config = $this->config = new \phpbb\config\config(array());
		$db = $this->new_dbal();
		$phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
		$request = $this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$template = $this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$this->language = new \phpbb\language\language($lang_loader);
		$this->user = new \phpbb\user($this->language, '\phpbb\datetime');

		$this->settings = new \vse\topicpreview\core\settings(
			$cache,
			$this->config,
			$db,
			$phpbb_extension_manager,
			$this->request,
			$phpbb_root_path
		);

		$this->controller = new \vse\topicpreview\controller\acp_controller(
			$this->language,
			$this->request,
			$this->settings,
			$this->template
		);
	}

	/**
	 * Test the main module displays expected data
	 */
	public function test_main_display()
	{
		$this->template->expects($this->at(1))
			->method('assign_vars')
			->with(array(
				'TOPIC_PREVIEW_LIMIT'		=> $this->config['topic_preview_limit'],
				'TOPIC_PREVIEW_WIDTH'		=> $this->config['topic_preview_width'],
				'TOPIC_PREVIEW_DELAY'		=> $this->config['topic_preview_delay'],
				'TOPIC_PREVIEW_DRIFT'		=> $this->config['topic_preview_drift'],
				'S_TOPIC_PREVIEW_AVATARS'	=> $this->config['topic_preview_avatars'],
				'S_TOPIC_PREVIEW_LAST_POST'	=> $this->config['topic_preview_last_post'],
				'TOPIC_PREVIEW_STRIP'		=> $this->config['topic_preview_strip_bbcodes'],
				'TOPIC_PREVIEW_STYLES'		=> $this->invokeMethod($this->settings, 'get_styles'),
				'TOPIC_PREVIEW_THEMES'		=> $this->invokeMethod($this->settings, 'get_themes'),
				'TOPIC_PREVIEW_DEFAULT'		=> \vse\topicpreview\core\settings::DEFAULT_THEME,
				'TOPIC_PREVIEW_NO_THEME'	=> \vse\topicpreview\core\settings::NO_THEME,
				'U_ACTION'					=> 'u_action',
			));

		$this->controller->set_u_action('u_action')->handle();
	}

	/**
	 * Test the main module accepts submitted data
	 *
	 * @param $data_map
	 * @param $error
	 * @param $expected
	 * @dataProvider main_submit_test_data
	 */
	public function test_main_submit($data_map, $error, $expected)
	{
		// Set up some test configurations
		$this->user->data['user_id'] = 2;
		$this->config['form_token_lifetime'] = -1;

		$this->request->expects($this->atLeastOnce())
			->method('is_set_post')
			->will($this->returnValueMap(array(
				array('submit', true),
				array('form_token', true),
				array('creation_time', true),
			)));

		$this->request->expects($this->any())
			->method('variable')
			->will($this->returnValueMap($data_map));

		$this->setExpectedTriggerError($error, $this->language->lang($expected));

		$this->controller->handle();
	}

	public function main_submit_test_data()
	{
		return array(
			array(
				array(
					array('topic_preview_limit', 0, false, \phpbb\request\request_interface::REQUEST, 500),
					array('topic_preview_width', 0, false, \phpbb\request\request_interface::REQUEST, 400),
					array('topic_preview_delay', 0, false, \phpbb\request\request_interface::REQUEST, 300),
					array('topic_preview_drift', 0, false, \phpbb\request\request_interface::REQUEST, 200),
					array('topic_preview_avatars', 0, false, \phpbb\request\request_interface::REQUEST, 1),
					array('topic_preview_last_post', 0, false, \phpbb\request\request_interface::REQUEST, 1),
					array('topic_preview_strip_bbcodes', '', false, \phpbb\request\request_interface::REQUEST, 'foo'),
					// hidden fields used for check_form_key
					array('form_token', '', false, \phpbb\request\request_interface::REQUEST, sha1(0 . 'acp_topic_preview')),
					array('creation_time', 0, false, \phpbb\request\request_interface::REQUEST, 0),
				),
				E_USER_NOTICE,
				'CONFIG_UPDATED',
			),
			array(
				array(),
				E_USER_WARNING,
				'FORM_INVALID',
			),
		);
	}

	/**
	 * Call protected/private method of a class.
	 *
	 * @param object &$object     Instantiated object that we will run method on.
	 * @param string  $methodName Method name to call
	 * @param array   $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 * @throws \ReflectionException
	 */
	public function invokeMethod(&$object, $methodName, array $parameters = array())
	{
		$reflection = new \ReflectionClass(get_class($object));
		$method = $reflection->getMethod($methodName);
		$method->setAccessible(true);

		return $method->invokeArgs($object, $parameters);
	}
}
