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
	/** @var \vse\topicpreview\core\topic_preview */
	protected $topicpreview;

	/**
	* Constructor
	*
	* @param \vse\topicpreview\core\topic_preview    $topicpreview  Topic Preview object
	* @return \vse\topicpreview\event\listener
	* @access public
	*/
	public function __construct(\vse\topicpreview\core\topic_preview $topicpreview)
	{
		$this->topicpreview = $topicpreview;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			// viewform.php events
			'core.viewforum_get_topic_data'			=> 'modify_sql_array',
			'core.viewforum_get_shadowtopic_data'	=> 'modify_sql_array',
			'core.viewforum_modify_topicrow'		=> 'display_topic_previews',

			// search.php events
			'core.search_get_topic_data'			=> 'modify_sql_string',
			'core.search_modify_tpl_ary'			=> 'display_topic_previews',

			// ucp_prefs.php events
			'core.ucp_prefs_view_data'				=> 'ucp_prefs_get_data',
			'core.ucp_prefs_view_update_data'		=> 'ucp_prefs_set_data',

			// These are custom events for integration with Precise Similar Topics
			'similartopics.get_topic_data'			=> 'modify_sql_array',
			'similartopics.modify_topicrow'			=> 'display_topic_previews',
		);
	}

	/**
	* Modify an SQL array to get post text for topic previews
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_sql_array($event)
	{
		$event['sql_array'] = $this->topicpreview->modify_sql($event['sql_array']);
	}

	/**
	* Modify SQL strings to get post text for topic previews (search results)
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_sql_string($event)
	{
		$event['sql_select'] = $this->topicpreview->modify_sql($event['sql_select'], 'SELECT');
		$event['sql_from'] = $this->topicpreview->modify_sql($event['sql_from'], 'JOIN');
	}

	/**
	* Modify template vars to display topic previews
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function display_topic_previews($event)
	{
		$block = $event['topic_row'] ? 'topic_row' : 'tpl_ary';
		$event[$block] = $this->topicpreview->display_topic_preview($event['row'], $event[$block]);
	}

	/**
	* Get user's Topic Preview option and display it in UCP Prefs View page
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function ucp_prefs_get_data($event)
	{
		// Request the user option vars and add them to the data array
		$event['data'] = array_merge($event['data'], array(
			'topic_preview'	=> $this->topicpreview->request_ucp_setting(),
		));

		// Output the data vars to the template (except on form submit)
		if (!$event['submit'])
		{
			$this->topicpreview->display_ucp_setting($event['data']);
		}
	}

	/**
	* Add user's Topic Preview option state into the sql_array
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function ucp_prefs_set_data($event)
	{
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_topic_preview' => $event['data']['topic_preview'],
		));
	}
}
