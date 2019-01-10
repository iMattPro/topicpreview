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

class listener_test extends \phpbb_test_case
{
	/** @var \vse\topicpreview\event\listener */
	protected $listener;

	/** @var \vse\topicpreview\core\data|\PHPUnit_Framework_MockObject_MockObject */
	protected $topic_preview_data;

	/** @var \vse\topicpreview\core\display|\PHPUnit_Framework_MockObject_MockObject */
	protected $topic_preview_display;

	/**
	* Create our event listener
	*/
	protected function set_listener()
	{
		$this->topic_preview_data = $this->getMockBuilder('\vse\topicpreview\core\data')
			->disableOriginalConstructor()
			->getMock();
		$this->topic_preview_display = $this->getMockBuilder('\vse\topicpreview\core\display')
			->disableOriginalConstructor()
			->getMock();

		$this->listener = new \vse\topicpreview\event\listener($this->topic_preview_data, $this->topic_preview_display);
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
			'core.viewforum_get_topic_data',
			'core.viewforum_get_shadowtopic_data',
			'core.viewforum_modify_topicrow',
			'core.search_get_topic_data',
			'core.search_modify_tpl_ary',
			'vse.similartopics.get_topic_data',
			'vse.similartopics.modify_topicrow',
			'paybas.recenttopics.sql_pull_topics_data',
			'paybas.recenttopics.modify_tpl_ary',
			'rmcgirr83.topfive.sql_pull_topics_data',
			'rmcgirr83.topfive.modify_tpl_ary',
		), array_keys(\vse\topicpreview\event\listener::getSubscribedEvents()));
	}

	public function test_modify_sql_array()
	{
		// Set up the listener
		$this->set_listener();

		// Create an event data array
		$data = new \phpbb\event\data(array(
			'sql_array'	=> array('FOO'),
		));

		// Check that expected method is called with
		// the correct arguments. Returns a new value.
		$this->topic_preview_data->expects($this->once())
			->method('modify_sql')
			->with($data['sql_array'])
			->willReturn(array('BAR'));

		// Call the event
		$this->listener->modify_sql_array($data);

		// Assert that the event data has been modified
		$this->assertEquals(array('BAR'), $data['sql_array']);
	}

	public function test_modify_sql_string()
	{
		// Set up the listener
		$this->set_listener();

		// Create an event data array
		$data = new \phpbb\event\data(array(
			'sql_select'	=> 'SELECT FOO',
			'sql_from'		=> 'FROM FOO',
		));

		// Check that expected method is called with
		// the correct arguments. Returns a new value.
		$this->topic_preview_data->expects($this->at(0))
			->method('modify_sql')
			->with($data['sql_select'], 'SELECT')
			->willReturn('SELECT FOO BAR');
		$this->topic_preview_data->expects($this->at(1))
			->method('modify_sql')
			->with($data['sql_from'], 'JOIN')
			->willReturn('FROM FOO BAR');

		// Call the event
		$this->listener->modify_sql_string($data);

		// Assert that the event data has been modified
		$this->assertEquals('SELECT FOO BAR', $data['sql_select']);
		$this->assertEquals('FROM FOO BAR', $data['sql_from']);
	}

	public function display_topic_previews_data()
	{
		return array(
			array('topic_row'),
			array('tpl_ary'),
		);
	}

	/**
	 * @dataProvider display_topic_previews_data
	 */
	public function test_display_topic_previews($block)
	{
		// Set up the listener
		$this->set_listener();

		// Create an event data array
		$data = new \phpbb\event\data(array(
			$block	=> array('FOO'),
			'row'	=> array(),
		));

		// Check that expected method is called with
		// the correct arguments. Returns a new value.
		$this->topic_preview_display->expects($this->once())
			->method('display_topic_preview')
			->with($data['row'], $data[$block])
			->willReturn(array('BAR'));

		// Call the event
		$this->listener->display_topic_previews($data);

		// Assert that the event data has been modified
		$this->assertEquals(array('BAR'), $data[$block]);
	}
}
