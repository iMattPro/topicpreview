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

	/** @var \vse\topicpreview\core\data|\PHPUnit\Framework\MockObject\MockObject */
	protected $topic_preview_data;

	/** @var \vse\topicpreview\core\display|\PHPUnit\Framework\MockObject\MockObject */
	protected $topic_preview_display;

	/**
	* Create our event listener
	*/
	protected function set_listener()
	{
		$this->topic_preview_data = $this->createMock('\vse\topicpreview\core\data');
		$this->topic_preview_display = $this->createMock('\vse\topicpreview\core\display');

		$this->listener = new \vse\topicpreview\event\listener($this->topic_preview_data, $this->topic_preview_display);
	}

	/**
	* Test the event listener is constructed correctly
	*/
	public function test_construct()
	{
		$this->set_listener();
		self::assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	/**
	* Test the event listener is subscribing events
	*/
	public function test_getSubscribedEvents()
	{
		self::assertEquals(array(
			'core.viewforum_get_topic_data',
			'core.viewforum_get_shadowtopic_data',
			'core.viewforum_modify_topics_data',
			'core.viewforum_modify_topicrow',
			'core.search_get_topic_data',
			'core.search_modify_rowset',
			'core.search_modify_tpl_ary',
			'vse.similartopics.get_topic_data',
			'vse.similartopics.modify_rowset',
			'vse.similartopics.modify_topicrow',
			'paybas.recenttopics.sql_pull_topics_data',
			'paybas.recenttopics.modify_topics_list',
			'paybas.recenttopics.modify_tpl_ary',
			'imcger.recenttopicsng.sql_pull_topics_data',
			'imcger.recenttopicsng.modify_topics_list',
			'imcger.recenttopicsng.modify_tpl_ary',
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
		$this->topic_preview_data->expects(self::once())
			->method('modify_sql')
			->with($data['sql_array'])
			->willReturn(array('BAR'));

		// Call the event
		$this->listener->modify_sql_array($data);

		// Assert that the event data has been modified
		self::assertEquals(array('BAR'), $data['sql_array']);
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
		$this->topic_preview_data->expects(self::exactly(2))
			->method('modify_sql')
			->withConsecutive([$data['sql_select'], 'SELECT'], [$data['sql_from'], 'JOIN'])
			->willReturnOnConsecutiveCalls('SELECT FOO BAR', 'FROM FOO BAR');

		// Call the event
		$this->listener->modify_sql_string($data);

		// Assert that the event data has been modified
		self::assertEquals('SELECT FOO BAR', $data['sql_select']);
		self::assertEquals('FROM FOO BAR', $data['sql_from']);
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
		$this->topic_preview_display->expects(self::once())
			->method('display_topic_preview')
			->with($data['row'], $data[$block])
			->willReturn(array('BAR'));

		// Call the event
		$this->listener->display_topic_previews($data);

		// Assert that the event data has been modified
		self::assertEquals(array('BAR'), $data[$block]);
	}

	public function test_load_attachments_disabled()
	{
		// Set up the listener
		$this->set_listener();

		// Create event data
		$data = new \phpbb\event\data([
			'rowset' => [],
		]);

		// Mock get_attachments_for_topics to return empty array (disabled case)
		$this->topic_preview_data->expects(self::once())
			->method('get_attachments_for_topics')
			->with($data['rowset'])
			->willReturn([]);

		// Should not call set_attachments_cache when empty
		$this->topic_preview_display->expects(self::never())
			->method('set_attachments_cache');

		$this->listener->load_attachments($data);
	}

	public function test_load_attachments_with_attachments()
	{
		// Set up the listener
		$this->set_listener();

		// Create event data with topics that have attachments
		$rowset = [
			[
				'topic_attachment' => 1,
				'topic_first_post_id' => 1,
				'topic_last_post_id' => 2,
			],
			[
				'topic_attachment' => 0, // No attachments
				'topic_first_post_id' => 3,
				'topic_last_post_id' => 3,
			],
		];

		$data = new \phpbb\event\data([
			'rowset' => $rowset,
		]);

		$expected_attachments = [1 => [], 2 => []];

		// Mock get_attachments_for_topics to return attachments
		$this->topic_preview_data->expects(self::once())
			->method('get_attachments_for_topics')
			->with($rowset)
			->willReturn($expected_attachments);

		// Should call set_attachments_cache with the attachments
		$this->topic_preview_display->expects(self::once())
			->method('set_attachments_cache')
			->with($expected_attachments);

		$this->listener->load_attachments($data);
	}
}
