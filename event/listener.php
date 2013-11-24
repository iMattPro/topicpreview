<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
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
			// viewform.php events
			'core.viewforum_get_topic_data'			=> 'modify_sql_array',
			'core.viewforum_get_shadowtopic_data'	=> 'modify_sql_statement',
			'core.viewforum_modify_topicrow'		=> 'display_topic_previews',

			// search.php events
			'core.search_get_topic_data'			=> 'modify_sql_events',
			'core.search_modify_tpl_ary'			=> 'display_topic_previews',

			// ucp_prefs.php events
			'core.ucp_prefs_view_data'				=> 'ucp_prefs_get_data',
			'core.ucp_prefs_view_update_data'		=> 'ucp_prefs_set_data',

			// These are custom events for integration with Precise Similar Topics
			'similartopics.similar_topic_data'		=> 'modify_sql_array',
			'similartopics.modify_topicrow'			=> 'display_topic_previews',
		);
	}

	/**
	* Set up the environment
	*/
	public function setup()
	{
		global $phpbb_container;

		$this->container = $phpbb_container;
		$this->manager = $this->container->get('vse.topicpreview.manager');

		$this->container->get('template')->assign_vars(array(
			'S_TOPICPREVIEW'		=> $this->manager->is_active,
			'TOPICPREVIEW_DELAY'	=> $this->manager->preview_delay,
			'TOPICPREVIEW_DRIFT'	=> $this->manager->preview_drift,
			'TOPICPREVIEW_WIDTH'	=> $this->manager->preview_width,
			'TOPICPREVIEW_THEME'	=> $this->manager->preview_theme,
		));
	}

	/**
	* Modify an SQL array to get post text for topic previews (viewforum)
	*
	* @param object $event The event object
	* @return null
	*/
	public function modify_sql_array($event)
	{
		if (!$this->manager)
		{
			$this->setup();
		}
		
		$event['sql_array'] = $this->manager->modify_sql_array($event['sql_array']);
	}

	/**
	* Modify an SQL statement to get post text for topic previews (shadow topics)
	*
	* @param object $event The event object
	* @return null
	*/
	public function modify_sql_statement($event)
	{
		if (!$this->manager)
		{
			$this->setup();
		}
		
		$event['sql'] = $this->manager->modify_sql($event['sql']);
	}

	/**
	* Modify SQL from and select to get post text for topic previews (search results )
	*
	* @param object $event The event object
	* @return null
	*/
	public function modify_sql_events($event)
	{
		if (!$this->manager)
		{
			$this->setup();
		}
		
		$event['sql_from'] = $this->manager->modify_sql_join($event['sql_from']);
		$event['sql_select'] = $this->manager->modify_sql_select($event['sql_select']);		
	}

	/**
	* Modify template vars to display topic previews
	*
	* @param object $event The event object
	* @return null
	*/
	public function display_topic_previews($event)
	{
		if (!$this->manager)
		{
			$this->setup();
		}
		
		$block = $event['topic_row'] ? 'topic_row' : 'tpl_ary';
		$event[$block] = $this->manager->display_topic_preview($event['row'], $event[$block]);
	}

	/**
	* Get user's Topic Preview option and display it in UCP Prefs View page
	*
	* @param object $event The event object
	* @return null
	*/
	public function ucp_prefs_get_data($event)
	{
		if (!$this->manager)
		{
			$this->setup();
		}
		
		$config = $this->container->get('config');
		$request = $this->container->get('request');
		$template = $this->container->get('template');
		$user = $this->container->get('user');
		
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'topic_preview'	=> $request->variable('topic_preview', (int) $user->data['user_topic_preview']),
		));

		// Output the data vars to the template (except on form submit)
		if (!$event['submit'])
		{
			$user->add_lang_ext('vse/topicpreview', 'acp/info_acp_topic_preview');
			$template->assign_vars(array(
				'S_TOPIC_PREVIEW'			=> $config['topic_preview_limit'],
				'S_DISPLAY_TOPIC_PREVIEW'	=> $event['data']['topic_preview'],
			));
		}
	}

	/**
	* Add user's Topic Preview option state into the sql_array
	*
	* @param object $event The event object
	* @return null
	*/
	public function ucp_prefs_set_data($event)
	{
		if (!$this->manager)
		{
			$this->setup();
		}
		
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_topic_preview' => $event['data']['topic_preview'],
		));
	}
}
