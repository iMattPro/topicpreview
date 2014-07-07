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

	/** @var bool Topic Preview config enabled */
	protected $tp_enabled;

	/** @var bool Show avatars */
	protected $tp_avatars;

	/** @var bool Show last post */
	protected $tp_last_post;

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
	* Set up the environment
	*
	* @return null
	* @access public
	*/
	public function setup()
	{
		// Make sure we only run setup once
		if (isset($this->tp_enabled))
		{
			return;
		}

		// environment parameters
		$this->tp_enabled = (!empty($this->config['topic_preview_limit']) && !empty($this->user->data['user_topic_preview'])) ? true : false;
		$this->tp_avatars = (!empty($this->config['topic_preview_avatars']) && $this->config['allow_avatar'] && $this->user->optionget('viewavatars')) ? true : false;
		$this->tp_last_post = (!empty($this->config['topic_preview_last_post'])) ? true : false;

		// Load our language file (only needed if showing last post text)
		if ($this->tp_last_post)
		{
			$this->user->add_lang_ext('vse/topicpreview', 'topic_preview');
		}

		// Assign our template vars
		$this->template->assign_vars(array(
			'S_TOPICPREVIEW'		=> $this->tp_enabled,
			'TOPICPREVIEW_DELAY'	=> (isset($this->config['topic_preview_delay'])) ? $this->config['topic_preview_delay'] : 1000,
			'TOPICPREVIEW_DRIFT'	=> (isset($this->config['topic_preview_drift'])) ? $this->config['topic_preview_drift'] : 15,
			'TOPICPREVIEW_WIDTH'	=> (!empty($this->config['topic_preview_width'])) ? $this->config['topic_preview_width'] : 360,
			'TOPICPREVIEW_THEME'	=> (!empty($this->user->style['topic_preview_theme'])) ? $this->user->style['topic_preview_theme'] : 'light',
		));
	}

	/**
	* Return additional params for an SQL SELECT statement to get data needed
	* for topic previews
	*
	* @return string SQL SELECT appendage
	* @access public
	*/
	public function tp_sql_select()
	{
		return ', fp.post_text AS first_post_text' .
			($this->tp_last_post ? ', lp.post_text AS last_post_text' : '') .
			($this->tp_avatars ? ', fpu.user_avatar AS fp_avatar, fpu.user_avatar_type AS fp_avatar_type, fpu.user_avatar_width AS fp_avatar_width, fpu.user_avatar_height AS fp_avatar_height' .
			($this->tp_last_post ? ', lpu.user_avatar AS lp_avatar, lpu.user_avatar_type AS lp_avatar_type, lpu.user_avatar_width AS lp_avatar_width, lpu.user_avatar_height AS lp_avatar_height' : '') : '');
	}

	/**
	* Return additional params for an SQL JOIN statement to get data needed
	* for topic previews
	*
	* @return array SQL JOIN params
	* @access public
	*/
	public function tp_sql_join()
	{
		$sql_array['LEFT_JOIN'][] = array(
			'FROM'	=> array(POSTS_TABLE => 'fp'),
			'ON'	=> 'fp.post_id = t.topic_first_post_id'
		);

		if ($this->tp_avatars)
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(USERS_TABLE => 'fpu'),
				'ON'	=> 'fpu.user_id = t.topic_poster'
			);
		}

		if ($this->tp_last_post)
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(POSTS_TABLE => 'lp'),
				'ON'	=> 'lp.post_id = t.topic_last_post_id'
			);

			if ($this->tp_avatars)
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
	* Return an img link to the board's default no avatar image
	*
	* @return string img link to the board's default no avatar image
	* @access public
	*/
	public function tp_avatar_fallback()
	{
		static $no_avatar = '';
		if (empty($no_avatar))
		{
			$no_avatar = $this->get_user_avatar_helper($this->root_path . 'styles/' . rawurlencode($this->user->style['style_path']) . '/theme/images/no_avatar.gif', 'avatar.driver.remote');
		}
		return $no_avatar;
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

		if (!$this->tp_enabled)
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
		if (!$this->tp_enabled)
		{
			return $block;
		}

		if (!empty($row['first_post_text']))
		{
			$first_post_preview_text = $this->trim_topic_preview($row['first_post_text']);
		}

		if (!empty($row['last_post_text']) && $row['topic_first_post_id'] != $row['topic_last_post_id'])
		{
			$last_post_preview_text = $this->trim_topic_preview($row['last_post_text']);
		}

		if ($this->tp_avatars)
		{
			$first_poster_avatar = (!empty($row['fp_avatar'])) ? $this->get_user_avatar_helper($row['fp_avatar'], $row['fp_avatar_type'], $row['fp_avatar_width'], $row['fp_avatar_height']) : $this->tp_avatar_fallback();
			$last_poster_avatar = (!empty($row['lp_avatar'])) ? $this->get_user_avatar_helper($row['lp_avatar'], $row['lp_avatar_type'], $row['lp_avatar_width'], $row['lp_avatar_height']) : $this->tp_avatar_fallback();
		}

		$block = array_merge($block, array(
			'TOPIC_PREVIEW_FIRST_POST'		=> (isset($first_post_preview_text)) ? censor_text($first_post_preview_text) : '',
			'TOPIC_PREVIEW_FIRST_AVATAR'	=> (isset($first_poster_avatar)) ? $first_poster_avatar : '',
			'TOPIC_PREVIEW_LAST_POST'		=> (isset($last_post_preview_text)) ? censor_text($last_post_preview_text) : '',
			'TOPIC_PREVIEW_LAST_AVATAR'		=> (isset($last_poster_avatar)) ? $last_poster_avatar : '',
		));

		$tp_avatars = $this->tp_avatars;

		/**
		* Modify the topic preview display output before it gets inserted in the template block
		*
		* @event vse.topicpreview.display_topic_preview
		* @var array $row Row data
		* @var array $block Template vars array
		* @var int $tp_avatars Display avatars setting
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

		static $patterns = array();

		if (empty($patterns))
		{
			$strip_bbcodes = (!empty($this->config['topic_preview_strip_bbcodes'])) ? 'flash|' . trim($this->config['topic_preview_strip_bbcodes']) : 'flash';
			// RegEx patterns based on Topic Text Hover Mod by RMcGirr83
			$patterns = array(
				'#<!-- [lmw] --><a class="postlink[^>]*>(.*<\/a[^>]*>)?<!-- [lmw] -->#Usi', // Magic URLs
				'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[(' . $strip_bbcodes . ')[^\[\]]+\]((?:[^[]|\[(?!/?\1[^\[\]]+\])|(?R))+)\[/\1[^\[\]]+\]#Usi', // BBCode content to strip
				'#\[/?[^\[\]]+\]#mi', // All BBCode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Remaining URLs
				'#"#', // Possible un-encoded quotes from older board conversions
				'#[ \t]{2,}#' // Multiple spaces #[\s]+#
			);
		}

		return trim(preg_replace($patterns, ' ', $text));
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
	* @param string $avatar Users assigned avatar name
	* @param int $avatar_type Type of avatar
	* @param int $avatar_width Width of avatar
	* @param int $avatar_height Height of avatar
	* @return string Avatar image
	* @access protected
	*/
	protected function get_user_avatar_helper($avatar, $avatar_type, $avatar_width = 60, $avatar_height = 60)
	{
		// map arguments to new function phpbb_get_avatar()
		$row = array(
			'avatar'		=> $avatar,
			'avatar_type'	=> $avatar_type,
			'avatar_width'	=> $avatar_width,
			'avatar_height'	=> $avatar_height,
		);

		return phpbb_get_user_avatar($row);
	}
}
