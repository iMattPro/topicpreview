<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2013 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\event;

use phpbb\config\config;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener for UCP related actions
 */
class ucp_listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param config   $config   Config object
	 * @param request  $request  Request object
	 * @param template $template Template object
	 * @param user     $user     User object
	 */
	public function __construct(config $config, request $request, template $template, user $user)
	{
		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.ucp_prefs_view_data'				=> 'ucp_prefs_get_data',
			'core.ucp_prefs_view_update_data'		=> 'ucp_prefs_set_data',
		);
	}

	/**
	 * Get user's Topic Preview option and display it in UCP Prefs View page
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function ucp_prefs_get_data($event)
	{
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'topic_preview'	=> $this->request->variable('topic_preview', (int) $this->user->data['user_topic_preview']),
		));

		// Output the data vars to the template (except on form submit)
		if (!$event['submit'])
		{
			$this->user->add_lang_ext('vse/topicpreview', 'topic_preview_ucp');
			$this->template->assign_vars(array(
				'S_TOPIC_PREVIEW'			=> $this->config['topic_preview_limit'],
				'S_DISPLAY_TOPIC_PREVIEW'	=> $event['data']['topic_preview'],
			));
		}
	}

	/**
	 * Add user's Topic Preview option state into the sql_array
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function ucp_prefs_set_data($event)
	{
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_topic_preview' => $event['data']['topic_preview'],
		));
	}
}
