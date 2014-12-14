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

	/**
	* Constructor
	*
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param string $root_path
	* @return \vse\topicpreview\core\topic_preview
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\event\dispatcher_interface $dispatcher, \phpbb\template\template $template, \phpbb\user $user, $root_path)
	{
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
	}

	/**
	* Returns whether topic preview is enabled, given current board and user configurations
	*
	* @return bool
	* @access public
	*/
	public function is_enabled()
	{
		return (bool) !empty($this->config['topic_preview_limit']) && !empty($this->user->data['user_topic_preview']);
	}

	/**
	* Returns whether avatars should enabled, given current board and user configurations
	*
	* @return bool
	* @access public
	*/
	public function avatars_enabled()
	{
		return (bool) $this->config['topic_preview_avatars'] && $this->config['allow_avatar'] && $this->user->optionget('viewavatars');
	}

	/**
	* Returns whether last post text should be enabled, given current board configuration
	*
	* @return bool
	* @access public
	*/
	public function last_post_enabled()
	{
		return (bool) $this->config['topic_preview_last_post'];
	}

	/**
	* Set up the environment
	*
	* @return null
	* @access public
	*/
	public function setup()
	{
		static $is_setup = false;

		// Make sure we only run setup once
		if ($is_setup)
		{
			return;
		}

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
			'TOPICPREVIEW_WIDTH'	=> (!empty($this->config['topic_preview_width'])) ? $this->config['topic_preview_width'] : 360,
		));

		// So the setup is only loaded once
		$is_setup = true;
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
		$this->setup();

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
			'TOPIC_PREVIEW_FIRST_POST'		=> (!empty($row['first_post_text'])) ? censor_text($this->trim_topic_preview($row['first_post_text'])) : '',
			'TOPIC_PREVIEW_LAST_POST'		=> (!empty($row['last_post_text']) && ($row['topic_first_post_id'] != $row['topic_last_post_id'])) ? censor_text($this->trim_topic_preview($row['last_post_text'])) : '',
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
	* Trim and clean topic preview text
	*
	* @param string $text Topic preview text
	* @return string Trimmed topic preview text
	* @access protected
	*/
	protected function trim_topic_preview($text)
	{
		$text = $this->remove_markup($text);

		if (utf8_strlen($text) <= $this->config['topic_preview_limit'])
		{
			return $this->tp_nl2br($text);
		}

		// trim the text to the last whitespace character before the cut-off
		$text = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($text, 0, $this->config['topic_preview_limit']));

		return $this->tp_nl2br($text) . '...';
	}

	/**
	* Strip BBCodes, tags and links for topic preview text
	*
	* @param string $text Topic preview text
	* @return string Stripped topic preview text
	* @access protected
	*/
	protected function remove_markup($text)
	{
		$text = smiley_text($text, true); // display smileys as text :)

		$text = $this->strip_bbcode_contents($text);

		static $patterns = array();

		if (empty($patterns))
		{
			// RegEx patterns based on Topic Text Hover Mod by RMcGirr83
			$patterns = array(
				'#<!-- [lmw] --><a class="postlink[^>]*>(.*<\/a[^>]*>)?<!-- [lmw] -->#Usi', // Magic URLs
				'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[/?[^\[\]]+\]#mi', // All BBCode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Remaining URLs
				'#"#', // Possible un-encoded quotes from older board conversions
				'#[ \t]{2,}#' // Multiple spaces #[\s]+#
			);
		}

		return trim(preg_replace($patterns, ' ', $text));
	}

	/**
	* Strip special BBCodes and their contents
	* Uses recursion to handle nested BBCodes
	*
	* @param string $text Topic preview text
	* @return string Topic preview text stripped
	* @access protected
	*/
	protected function strip_bbcode_contents($text)
	{
		static $regex;

		if (!isset($regex))
		{
			$strip_bbcodes = (!empty($this->config['topic_preview_strip_bbcodes'])) ? 'flash|' . trim($this->config['topic_preview_strip_bbcodes']) : 'flash';
			$regex = '#\[(' . $strip_bbcodes . ')[^\[\]]+\]((?:(?!\[\1[^\[\]]+\]).)+)\[\/\1[^\[\]]+\]#Usi';
		}

		if (preg_match($regex, $text))
		{
			return $this->strip_bbcode_contents(preg_replace($regex, '', $text));
		}

		return $text;
	}

	/**
	* Convert and preserve line breaks
	*
	* @param string $text Topic preview text
	* @return string Topic preview text with line breaks
	* @access protected
	*/
	protected function tp_nl2br($text)
	{
		// http://stackoverflow.com/questions/816085/removing-redundant-line-breaks-with-regular-expressions
		return nl2br(preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $text));
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
			$row[$poster . '_avatar_width'] = 60;
			$row[$poster . '_avatar_height'] = 60;
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
	* Fall back to default theme if expected theme not found
	*
	* @return string Theme name
	* @access protected
	*/
	protected function get_theme()
	{
		if (!empty($this->user->style['topic_preview_theme']))
		{
			if (file_exists($this->root_path . 'ext/vse/topicpreview/styles/all/theme/' . $this->user->style['topic_preview_theme'] . '.css') || ($this->user->style['topic_preview_theme'] == 'no'))
			{
				return $this->user->style['topic_preview_theme'];
			}
		}

		return 'light';
	}
}
