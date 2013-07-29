<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
    exit;
}

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class phpbb_ext_vse_topicpreview_event_listener implements EventSubscriberInterface
{
	/**
	* Topic Preview manager object
	* @var phpbb_ext_vse_topicpreview_core_manager
	*/
	private $manager;

	/**
	* Get subscribed events
	*
	* @return array
	* @static
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'			=> 'setup',

			'core.viewforum_get_topic_data'			=> 'modify_sql_array',
			'core.viewforum_get_shadowtopic_data'	=> 'modify_sql_statement',
			'core.viewforum_modify_topicrow'		=> 'display_topic_previews',

			'core.search_get_topic_data'			=> 'modify_sql_events',
			'core.search_modify_tpl_ary'			=> 'display_topic_previews',
		);
	}

	/**
	* Set up the environment
	*
	* @param Event $event Event object
	* @return null
	*/
	public function setup($event)
	{
		global $phpbb_container, $template, $phpbb_root_path;

		$this->manager = $phpbb_container->get('topicpreview.manager');

		$template->assign_vars(array(
			'T_TOPICPREVIEW_ASSETS'	=> $phpbb_root_path . 'ext/vse/topicpreview/styles/all/template/assets',
		));
	}

	/**
	* Modify an SQL array to get post text for topic previews (viewforum)
	*
	* @param Event $event Event object
	* @return null
	*/
	public function modify_sql_array($event)
	{
		$event['sql_array'] = $this->manager->modify_sql_array($event['sql_array']);
	}

	/**
	* Modify an SQL statement to get post text for topic previews (shadow topics)
	*
	* @param Event $event Event object
	* @return null
	*/
	public function modify_sql_statement($event)
	{
		$event['sql'] = $this->manager->modify_sql($event['sql']);
	}

	/**
	* Modify SQL from and select to get post text for topic previews (search results )
	*
	* @param Event $event Event object
	* @return null
	*/
	public function modify_sql_events($event)
	{
		$event['sql_from'] = $this->manager->modify_sql_join($event['sql_from']);
		$event['sql_select'] = $this->manager->modify_sql_select($event['sql_select']);		
	}

	/**
	* Modify template vars to display topic previews
	*
	* @param Event $event Event object
	* @return null
	*/
	public function display_topic_previews($event)
	{
		$block = $event['topic_row'] ? 'topic_row' : 'tpl_ary';
		$event[$block] = $this->manager->display_topic_preview($event['row'], $event[$block]);
	}
}
