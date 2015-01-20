<?php
/**
*
* Topic Preview
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\core;

class topic_preview
{
	/** @var int default width of topic preview */
	const PREVIEW_SIZE = 360;

	/** @var int default height and width of topic preview avatars */
	const AVATAR_SIZE = 60;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var \vse\topicpreview\core\trim_tools */
	protected $trim_tools;

	/**
	* Constructor
	*
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param string $root_path
	* @param \vse\topicpreview\core\trim_tools $trim_tools
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\event\dispatcher_interface $dispatcher, \phpbb\template\template $template, \phpbb\user $user, $root_path, \vse\topicpreview\core\trim_tools $trim_tools)
	{
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->trim_tools = $trim_tools;

		$this->setup();
	}

	/**
	* Show topic previews, given current board and user configurations
	*
	* @return bool
	* @access public
	*/
	public function is_enabled()
	{
		return (bool) !empty($this->config['topic_preview_limit']) && !empty($this->user->data['user_topic_preview']);
	}

	/**
	* Show avatars, given current board and user configurations
	*
	* @return bool
	* @access public
	*/
	public function avatars_enabled()
	{
		return (bool) $this->config['topic_preview_avatars'] && $this->config['allow_avatar'] && $this->user->optionget('viewavatars');
	}

	/**
	* Show last post text, given current board configuration
	*
	* @return bool
	* @access public
	*/
	public function last_post_enabled()
	{
		return (bool) $this->config['topic_preview_last_post'];
	}

	/**
	* Set up some common components
	*
	* @return null
	* @access public
	*/
	public function setup()
	{
		// Load our language file (only needed if showing last post text)
		if ($this->last_post_enabled())
		{
			$this->user->add_lang_ext('vse/topicpreview', 'topic_preview');
		}

		$this->template->assign_vars(array(
			'S_TOPICPREVIEW'		=> $this->is_enabled(),
			'TOPICPREVIEW_THEME'	=> $this->get_theme(),
			'TOPICPREVIEW_DELAY'	=> $this->config['topic_preview_delay'],
			'TOPICPREVIEW_DRIFT'	=> $this->config['topic_preview_drift'],
			'TOPICPREVIEW_WIDTH'	=> (!empty($this->config['topic_preview_width'])) ? $this->config['topic_preview_width'] : self::PREVIEW_SIZE,
		));
	}

	/**
	* Update an SQL SELECT statement to get data needed for topic previews
	*
	* @return string SQL SELECT appendage
	* @access public
	*/
	public function tp_sql_select()
	{
		$sql = ', fp.post_text AS first_post_text';

		if ($this->last_post_enabled())
		{
			$sql .= ', lp.post_text AS last_post_text';
		}

		if ($this->avatars_enabled())
		{
			$sql .= ', fpu.user_avatar AS fp_avatar,
				fpu.user_avatar_type AS fp_avatar_type,
				fpu.user_avatar_width AS fp_avatar_width,
				fpu.user_avatar_height AS fp_avatar_height';

			if ($this->last_post_enabled())
			{
				$sql .= ', lpu.user_avatar AS lp_avatar,
					lpu.user_avatar_type AS lp_avatar_type,
					lpu.user_avatar_width AS lp_avatar_width,
					lpu.user_avatar_height AS lp_avatar_height';
			}
		}

		return $sql;
	}

	/**
	* Update an SQL JOIN statement to get data needed for topic previews
	*
	* @return array SQL JOIN params
	* @access public
	*/
	public function tp_sql_join()
	{
		$sql_array = array();

		$sql_array['LEFT_JOIN'][] = array(
			'FROM'	=> array(POSTS_TABLE => 'fp'),
			'ON'	=> 'fp.post_id = t.topic_first_post_id'
		);

		if ($this->avatars_enabled())
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(USERS_TABLE => 'fpu'),
				'ON'	=> 'fpu.user_id = t.topic_poster'
			);
		}

		if ($this->last_post_enabled())
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(POSTS_TABLE => 'lp'),
				'ON'	=> 'lp.post_id = t.topic_last_post_id'
			);

			if ($this->avatars_enabled())
			{
				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(USERS_TABLE => 'lpu'),
					'ON'	=> 'lpu.user_id = t.topic_last_poster_id'
				);
			}
		}

		return $sql_array;
	}

	/**
	* Modify SQL string|array to get post text
	*
	* @param string|array $sql_stmt SQL string or array to be modified
	* @param string $type Type of SQL statement SELECT|JOIN
	* @return string|array SQL statement string or array
	* @access public
	*/
	public function modify_sql($sql_stmt, $type = 'SELECT')
	{
		if (!$this->is_enabled())
		{
			return $sql_stmt;
		}

		if (is_array($sql_stmt))
		{
			$array = $this->tp_sql_join();
			foreach ($array['LEFT_JOIN'] as $join)
			{
				$sql_stmt['LEFT_JOIN'][] = $join;
			}

			$sql_stmt['SELECT'] .= $this->tp_sql_select();
		}
		else
		{
			if ($type == 'SELECT')
			{
				$sql_stmt .= $this->tp_sql_select();
			}
			else
			{
				$array = $this->tp_sql_join();
				foreach ($array['LEFT_JOIN'] as $join)
				{
					$sql_stmt .= ' LEFT JOIN ' . key($join['FROM']) . ' ' . current($join['FROM']) . ' ON (' . $join['ON'] . ')';
				}
			}
		}

		return $sql_stmt;
	}

	/**
	* Inject topic preview text into the template
	*
	* @param array $row Row data
	* @param array $block Template vars array
	* @return array Template vars array
	* @access public
	*/
	public function display_topic_preview($row, $block)
	{
		if (!$this->is_enabled())
		{
			return $block;
		}

		$block = array_merge($block, array(
			'TOPIC_PREVIEW_FIRST_POST'		=> (!empty($row['first_post_text'])) ? censor_text($this->trim_tools->trim_text($row['first_post_text'], $this->config['topic_preview_limit'])) : '',
			'TOPIC_PREVIEW_LAST_POST'		=> (!empty($row['last_post_text']) && ($row['topic_first_post_id'] != $row['topic_last_post_id'])) ? censor_text($this->trim_tools->trim_text($row['last_post_text'], $this->config['topic_preview_limit'])) : '',
			'TOPIC_PREVIEW_FIRST_AVATAR'	=> $this->get_user_avatar_helper($row, 'fp'),
			'TOPIC_PREVIEW_LAST_AVATAR'		=> $this->get_user_avatar_helper($row, 'lp'),
		));

		$tp_avatars = $this->avatars_enabled();

		/**
		* EVENT to modify the topic preview display output before it gets inserted in the template block
		*
		* @event vse.topicpreview.display_topic_preview
		* @var array row Row data
		* @var array block Template vars array
		* @var bool tp_avatars Display avatars setting
		* @since 2.1.0
		*/
		$vars = array('row', 'block', 'tp_avatars');
		extract($this->dispatcher->trigger_event('vse.topicpreview.display_topic_preview', compact($vars)));

		return $block;
	}

	/**
	* Get user avatar helper function
	*
	* @param array $row User row data
	* @param string $poster Type of poster, fp or lp
	* @return string Avatar image
	* @access protected
	*/
	protected function get_user_avatar_helper($row, $poster)
	{
		if (!$this->avatars_enabled())
		{
			return '';
		}

		// If user has no avatar, lets use a fallback
		if (empty($row[$poster . '_avatar']))
		{
			$row[$poster . '_avatar'] = $this->root_path . 'styles/' . rawurlencode($this->user->style['style_path']) . '/theme/images/no_avatar.gif';
			$row[$poster . '_avatar_type'] = 'avatar.driver.remote';
			$row[$poster . '_avatar_width'] = self::AVATAR_SIZE;
			$row[$poster . '_avatar_height'] = self::AVATAR_SIZE;
		}

		// map arguments to new function phpbb_get_avatar()
		$map = array(
			'avatar'		=> $row[$poster . '_avatar'],
			'avatar_type'	=> $row[$poster . '_avatar_type'],
			'avatar_width'	=> $row[$poster . '_avatar_width'],
			'avatar_height'	=> $row[$poster . '_avatar_height'],
		);

		return phpbb_get_user_avatar($map);
	}

	/**
	* Get user's style topic preview theme
	* Fall back to no theme if expected theme not found
	*
	* @return mixed Theme name if theme file found, false otherwise
	* @access protected
	*/
	protected function get_theme()
	{
		if (!empty($this->user->style['topic_preview_theme']))
		{
			if (file_exists($this->root_path . 'ext/vse/topicpreview/styles/all/theme/' . $this->user->style['topic_preview_theme'] . '.css'))
			{
				return $this->user->style['topic_preview_theme'];
			}
		}

		return false;
	}
}
