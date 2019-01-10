<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2014 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\event;

class ucp_listener_test extends \phpbb_test_case
{
	/** @var \vse\topicpreview\event\listener */
	protected $listener;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request|\PHPUnit_Framework_MockObject_MockObject */
	protected $request;

	/** @var \phpbb\template\template|\PHPUnit_Framework_MockObject_MockObject */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Setup test environment
	 */
	public function setUp()
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		// Load/Mock classes required by the event listener class
		$this->config = new \phpbb\config\config(array('topic_preview_limit' => 1));
		$this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$this->language = new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx));
		$this->user = new \phpbb\user($this->language, '\phpbb\datetime');
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
	}

	/**
	 * Create our event listener
	 */
	protected function set_listener()
	{
		$this->listener = new \vse\topicpreview\event\ucp_listener(
			$this->config,
			$this->language,
			$this->request,
			$this->template,
			$this->user
		);
	}

	/**
	 * Test the event listener is constructed correctly
	 */
	public function test_construct()
	{
		$this->set_listener();
		$this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	/**
	 * Test the event listener is subscribing events
	 */
	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.ucp_prefs_view_data',
			'core.ucp_prefs_view_update_data',
		), array_keys(\vse\topicpreview\event\ucp_listener::getSubscribedEvents()));
	}

	/**
	 * Data set for test_ucp_prefs_set_data
	 *
	 * @return array Array of test data
	 */
	public function ucp_prefs_set_data_data()
	{
		return array(
			array(
				array(),
				array(),
				array('user_topic_preview' => 0),
			),
			array(
				array('topic_preview' => 1),
				array(),
				array('user_topic_preview' => 1),
			),
			array(
				array(
					'user_options'	=> 0,
					'topic_preview'	=> 1,
				),
				array(
					'user_options'				=> 0,
					'user_topic_sortby_type'	=> 0,
					'user_post_sortby_type'		=> 0,
					'user_topic_sortby_dir'		=> 0,
					'user_post_sortby_dir'		=> 0,
				),
				array(
					'user_options'				=> 0,
					'user_topic_sortby_type'	=> 0,
					'user_post_sortby_type'		=> 0,
					'user_topic_sortby_dir'		=> 0,
					'user_post_sortby_dir'		=> 0,
					'user_topic_preview'		=> 1,
				),
			),
		);
	}

	/**
	 * Test the ucp_prefs_set_data event
	 *
	 * @dataProvider ucp_prefs_set_data_data
	 */
	public function test_ucp_prefs_set_data($data, $sql_ary, $expected)
	{
		$this->set_listener();

		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.ucp_prefs_view_update_data', array($this->listener, 'ucp_prefs_set_data'));

		$event_data = array('data', 'sql_ary');
		$event = new \phpbb\event\data(compact($event_data));
		$dispatcher->dispatch('core.ucp_prefs_view_update_data', $event);

		$event_data_after = $event->get_data_filtered($event_data);
		$sql_ary = $event_data_after['sql_ary'];

		$this->assertEquals($expected, $sql_ary);
	}

	/**
	 * Data set for test_ucp_prefs_set_data
	 *
	 * @return array Array of test data
	 */
	public function ucp_prefs_get_data_data()
	{
		return array(
			array(
				1,
				true,
				array(),
				array('topic_preview' => 1),
			),
			array(
				1,
				false,
				array(),
				array('topic_preview' => 1),
			),
			array(
				1,
				true,
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
				),
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
					'topic_preview'	=> 1,
				),
			),
			array(
				1,
				false,
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
				),
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
					'topic_preview'	=> 1,
				),
			),
			array(
				0,
				false,
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
				),
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
					'topic_preview'	=> 0,
				),
			),
			array(
				0,
				true,
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
				),
				array(
					'images'		=> 0,
					'flash'			=> 0,
					'smilies'		=> 0,
					'sigs'			=> 0,
					'avatars'		=> 0,
					'wordcensor'	=> 0,
					'topic_preview'	=> 0,
				),
			),
		);
	}

	/**
	 * Test the ucp_prefs_get_data event
	 *
	 * @dataProvider ucp_prefs_get_data_data
	 */
	public function test_ucp_prefs_get_data($topic_preview, $submit, $data, $expected)
	{
		$this->set_listener();

		$this->user->data['user_topic_preview'] = 0;
		$this->request->expects($this->once())
			->method('variable')
			->willReturn($topic_preview);

		if (!$submit)
		{
			$this->template->expects($this->once())
				->method('assign_vars')
				->with(array(
					'S_TOPIC_PREVIEW'			=> 1,
					'S_DISPLAY_TOPIC_PREVIEW'	=> $topic_preview,
				));
		}

		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.ucp_prefs_view_data', array($this->listener, 'ucp_prefs_get_data'));

		$event_data = array('submit', 'data');
		$event = new \phpbb\event\data(compact($event_data));
		$dispatcher->dispatch('core.ucp_prefs_view_data', $event);

		$data = $event->get_data_filtered($event_data);
		$data = $data['data'];

		$this->assertEquals($expected, $data);
	}
}
