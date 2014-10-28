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

	/**
	* Create our event listener
	*
	* @access protected
	*/
	protected function set_listener()
	{
		$this->listener = new \vse\topicpreview\event\listener(
			new \vse\topicpreview\tests\mock\topic_preview()
		);
	}

	/**
	* Test the event listener is constructed correctly
	*
	* @access public
	*/
	public function test_construct()
	{
		$this->set_listener();
		$this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	/**
	* Test the event listener is subscribing events
	*
	* @access public
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
}
