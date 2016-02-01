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

require_once dirname(__FILE__) . '/../../../../../includes/functions.php';
require_once dirname(__FILE__) . '/../../../../../includes/functions_acp.php';

class acp_main_test extends \phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('vse/topicpreview');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/topic_preview.xml');
	}

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \vse\topicpreview\acp\topic_preview_module */
	protected $module;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\request\request */
	protected $request;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	public function setUp()
	{
		parent::setUp();

		global $cache, $config, $db, $phpbb_extension_manager, $phpbb_dispatcher, $request, $template, $user, $phpbb_root_path, $phpEx;

		$cache = new \phpbb_mock_cache;;
		$config = $this->config = new \phpbb\config\config(array());
		$db = $this->new_dbal();
		$phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
		$request = $this->request = $this->getMock('\phpbb\request\request');
		$template = $this->template = $this->getMock('\phpbb\template\template');
		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$this->lang = new \phpbb\language\language($lang_loader);
		$user = $this->user = new \phpbb\user($this->lang, '\phpbb\datetime');

		$this->module = new \vse\topicpreview\acp\topic_preview_module();
	}

	/**
	 * Test the acp module instance
	 */
	public function test_module()
	{
		$this->assertInstanceOf('\vse\topicpreview\acp\topic_preview_module', $this->module);
	}

	/**
	 * Test the main module displays expected data
	 */
	public function test_main_display()
	{
		$this->template->expects($this->at(2))
			->method('assign_vars')
			->with(array(
				'TOPIC_PREVIEW_LIMIT'		=> $this->config['topic_preview_limit'],
				'TOPIC_PREVIEW_WIDTH'		=> $this->config['topic_preview_width'],
				'TOPIC_PREVIEW_DELAY'		=> $this->config['topic_preview_delay'],
				'TOPIC_PREVIEW_DRIFT'		=> $this->config['topic_preview_drift'],
				'S_TOPIC_PREVIEW_AVATARS'	=> $this->config['topic_preview_avatars'],
				'S_TOPIC_PREVIEW_LAST_POST'	=> $this->config['topic_preview_last_post'],
				'TOPIC_PREVIEW_STRIP'		=> $this->config['topic_preview_strip_bbcodes'],
				'U_ACTION'					=> null,
			));

		$this->module->main(null, null);
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

		$this->request->expects($this->any())
			->method('is_set_post')
			->will($this->returnValueMap(array(
				array('submit', true),
				array('form_token', true),
				array('creation_time', true),
			)));

		$this->request->expects($this->any())
			->method('variable')
			->will($this->returnValueMap($data_map));

		$this->setExpectedTriggerError($error, $this->lang->lang($expected));

		$this->module->main(null, null);
	}

	public function main_submit_test_data()
	{
		$now = time();

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
					array('form_token', '', false, \phpbb\request\request_interface::REQUEST, sha1($now . 'acp_topic_preview')),
					array('creation_time', 0, false, \phpbb\request\request_interface::REQUEST, $now),
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
}
