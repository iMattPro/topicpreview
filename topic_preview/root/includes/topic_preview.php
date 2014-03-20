<?php
/**
*
* @package Topic Preview
* @version $Id$
* @copyright (c) 2010 Matt Friedman
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
* Class to get the first post text and add it to the title attribute of topic titles
*
* @package Topic Preview
*/
class phpbb_topic_preview
{
	/**
	* Are topic previews enabled?
	*/
	var $is_active		= true;

	/**
	* The max number of characters in the topic preview text
	*/
	var $preview_limit	= 150;

	/**
	* List of BBcodes whose text is to be stripped from topic previews
	*/
	var $strip_bbcodes	= '';

	/**
	* The default SQL SELECT statement injection
	*/
	var $tp_sql_select	= '';

	/**
	* The default SQL LEFT JOIN statement injection
	*/
	var $tp_sql_join	= '';

	/**
	* Get the last post's text for topic previews?
	*/
	var $tp_last_post	= false;

	/**
	* Get avatars for topic previews?
	*/
	var $tp_avatars		= false;

	/**
	* Display jQuery topic preview tooltip theme
	*/
	var $tp_jquery_mode	= false;

	/**
	* Add-On: Preserve line breaks?
	*/
	var $tp_line_breaks	= false;

	/**
	* Add-On: [topicpreview] bbcode support?
	*/
	var $tp_bbcode		= false;

	/**
	* Topic Preview MOD constructor
	*/
	function phpbb_topic_preview()
	{
		global $config, $user;

		// Set-up basic config parameters
		$this->is_active     = (!empty($config['topic_preview_limit']) && !empty($user->data['user_topic_preview'])) ? true : false;
		$this->preview_limit = (int) $config['topic_preview_limit'];
		$this->strip_bbcodes = (string) $config['topic_preview_strip_bbcodes'];

		// Set-up jQuery theme config parameters
		$this->tp_jquery_mode = (!empty($config['topic_preview_jquery'])) ? true : false;
		$this->tp_last_post   = (!empty($config['topic_preview_last_post']) && $this->tp_jquery_mode) ? true : false;
		$this->tp_avatars     = (!empty($config['topic_preview_avatars']) && $config['allow_avatar'] && $this->tp_jquery_mode) ? true : false;

		// Set-up some common SQL statements we'll be using
		$this->tp_sql_select = ', fp.post_text AS first_post_preview_text' . (($this->tp_last_post) ? ', lp.post_text AS last_post_preview_text' : '');
		$this->tp_sql_join   = ' LEFT JOIN ' . POSTS_TABLE . ' fp ON (fp.post_id = t.topic_first_post_id)' . (($this->tp_last_post) ? ' LEFT JOIN ' . POSTS_TABLE . ' lp ON (lp.post_id = t.topic_last_post_id)' : '');

		if ($this->tp_avatars)
		{
			$this->tp_sql_select .= ', fpu.user_avatar AS first_user_avatar, fpu.user_avatar_type AS first_user_avatar_type' . (($this->tp_last_post) ? ', lpu.user_avatar AS last_user_avatar, lpu.user_avatar_type AS last_user_avatar_type' : '');
			$this->tp_sql_join   .= ' LEFT JOIN ' . USERS_TABLE . ' fpu ON (fpu.user_id = t.topic_poster)' . (($this->tp_last_post) ? ' LEFT JOIN ' . USERS_TABLE . ' lpu ON (lpu.user_id = t.topic_last_poster_id)' : '');
		}

		// Load our language file if needed
		if ($this->tp_last_post)
		{
			$user->add_lang('mods/info_acp_topic_preview');
		}
	}

	/**
	* Modify SQL array to get post text for viewforum topics
	*
	* @param	array	$sql_array 	SQL statement array
	* @return	array	SQL statement array
	*/
	function modify_sql_array($sql_array)
	{
		if (!$this->is_active)
		{
			return $sql_array;
		}

		$sql_array['LEFT_JOIN'][] = array(
			'FROM'	=> array(POSTS_TABLE => 'fp'),
			'ON'	=> "fp.post_id = t.topic_first_post_id"
		);

		if ($this->tp_avatars)
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(USERS_TABLE => 'fpu'),
				'ON'	=> "fpu.user_id = t.topic_poster"
			);
		}

		if ($this->tp_last_post)
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(POSTS_TABLE => 'lp'),
				'ON'	=> "lp.post_id = t.topic_last_post_id"
			);

			if ($this->tp_avatars)
			{
				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(USERS_TABLE => 'lpu'),
					'ON'	=> "lpu.user_id = t.topic_last_poster_id"
				);
			}
		}

		$sql_array['SELECT'] .= $this->tp_sql_select;

		return $sql_array;
	}

	/**
	* Modify SQL statement to get post text for viewforum shadowtopics
	*
	* @param	string	$sql 	SQL statement
	* @return	string	SQL statement
	*/
	function modify_sql($sql)
	{
		if (!$this->is_active)
		{
			return $sql;
		}

		global $db, $shadow_topic_list;

		$sql = 'SELECT t.*' . $this->tp_sql_select . '
			FROM ' . TOPICS_TABLE . ' t ' . $this->tp_sql_join . '
			WHERE ' . $db->sql_in_set('t.topic_id', array_keys($shadow_topic_list));

		return $sql;
	}

	/**
	* Modify SQL SELECT statement to get post text for searchresults
	*
	* @param	string	$sql_select 	SQL SELECT statement
	* @return	string	SQL SELECT statement
	*/
	function modify_sql_select($sql_select)
	{
		if (!$this->is_active)
		{
			return $sql_select;
		}

		$sql_select .= $this->tp_sql_select;

		return $sql_select;
	}

	/**
	* Modify SQL JOIN statement to get post text for searchresults
	*
	* @param	string	$sql_join 	SQL JOIN statement
	* @return	string	SQL JOIN statement
	*/
	function modify_sql_join($sql_join)
	{
		if (!$this->is_active)
		{
			return $sql_join;
		}

		$sql_join .= $this->tp_sql_join;

		return $sql_join;
	}

	/**
	* Inject topic preview text into the template
	*
	* @param	array	$row 	row data
	* @param	array	$block 	template vars array
	* @return	null
	*/
	function display_topic_preview($row, $block)
	{
		if (!$this->is_active)
		{
			return false;
		}

		global $template, $user, $phpbb_root_path;

		if (!empty($row['first_post_preview_text']))
		{
			$first_post_preview_text = $this->_trim_topic_preview($row['first_post_preview_text'], $this->preview_limit);
		}

		if (!empty($row['last_post_preview_text']) && $row['topic_first_post_id'] != $row['topic_last_post_id'])
		{
			$last_post_preview_text = $this->_trim_topic_preview($row['last_post_preview_text'], $this->preview_limit);
		}

		if ($this->tp_avatars)
		{
			$no_avatar = '<img src="' . $phpbb_root_path . 'styles/topic_preview_assets/no_avatar.png" width="60" height="60" alt="" />';
			$first_post_avatar = (!empty($row['first_user_avatar'])) ? get_user_avatar($row['first_user_avatar'], $row['first_user_avatar_type'], 60, 60) : $no_avatar;
			$last_post_avatar  = (!empty($row['last_user_avatar'])) ? get_user_avatar($row['last_user_avatar'], $row['last_user_avatar_type'], 60, 60) : $no_avatar;
		}

		$template->alter_block_array($block, array(
			'TOPIC_PREVIEW_TEXT'	=> (isset($first_post_preview_text)) ? censor_text($first_post_preview_text) : '',
			'TOPIC_PREVIEW_TEXT2'	=> (isset($last_post_preview_text)) ? censor_text($last_post_preview_text) : '',
			'TOPIC_PREVIEW_AVATAR'	=> (isset($first_post_avatar) && $user->optionget('viewavatars')) ? $first_post_avatar : '',
			'TOPIC_PREVIEW_AVATAR2'	=> (isset($last_post_avatar) && $user->optionget('viewavatars')) ? $last_post_avatar : '',
		), true, 'change');

		// Set this template var only once
		if (!isset($template->_tpldata['.'][0]['S_JQUERY_TOPIC_PREVIEW']))
		{
			$template->assign_vars(array(
				'S_JQUERY_TOPIC_PREVIEW'	=> $this->tp_jquery_mode,
			));
		}
	}

	/**
	* Truncate and clean topic preview text
	*
	* @param	string	$text 	topic preview text
	* @param	int		$limit 	number of characters to allow
	* @return	string	topic preview text
	* @access private
	*/
	function _trim_topic_preview($text, $limit)
	{
		$text = $this->_bbcode_strip($text);

		if (utf8_strlen($text) <= $limit)
		{
			return $text;
		}

		// trim the text to the last whitespace character before the cut-off
		$text = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($text, 0, $limit));

		return $text . '...';

	}

	/**
	* Strip bbcodes and links for topic preview text
	*
	* NOTE: These RegEx patterns were originally written by RMcGirr83 for
	* his Topic Text Hover Mod, and Modified by Matt Friedman to display
	* smileys as text, strip URLs, custom BBcodes and trim whitespace.
	*
	* @param	string	$text 	topic preview text
	* @return	string	topic preview text
	* @access private
	*/
	function _bbcode_strip($text)
	{
		static $patterns = array();

		if ($this->tp_bbcode)
		{
			// use text inside [topicpreview] bbcode as the topic preview
			if (preg_match('#\[(topicpreview[^\[\]]+)\].*\[/\1\]#Usi', $text, $matches))
			{
				$text = $matches[0];
			}
		}

		$text = smiley_text($text, true); // display smileys as text :)
		$text = ($this->tp_line_breaks ? str_replace("\n", '&#13;&#10;', $text) : $text); // preserve line breaks

		if (empty($patterns))
		{
			$strip_bbcodes = (!empty($this->strip_bbcodes)) ? 'flash|' . trim($this->strip_bbcodes) : 'flash';
			$patterns = array(
				'#<!-- [lmw] --><a class="postlink[^>]*>(.*<\/a[^>]*>)?<!-- [lmw] -->#Usi', // Magic URLs
				'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[(' . $strip_bbcodes . ')[^\[\]]+\]((?:[^[]|\[(?!/?\1[^\[\]]+\])|(?R))+)\[/\1[^\[\]]+\]#Usi', // BBCode content to strip
				'#\[/?[^\[\]]+\]#mi', // Strip all bbcode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Strip remaining URLs
				'#"#', // Possible quotes from older board conversions
				'#[\s]+#' // Multiple spaces
			);
		}

		return trim(preg_replace($patterns, ' ', $text));
	}
}
