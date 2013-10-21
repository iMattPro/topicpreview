<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\core;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class topic_preview
{
	public $is_active;

	protected $config;
	protected $db;
	protected $user;
	protected $phpbb_root_path;

	private $tp_avatars;
	private $tp_avatar_fallback;
	private $tp_last_post;
	private $preview_limit;
	private $tp_sql_select;
	private $tp_sql_join;

	/**
	* Topic Preview class constructor method
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\user $user, $root_path)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->root_path = $root_path;

		// config parameters
		$this->is_active     = (!empty($this->config['topic_preview_limit']) && !empty($this->user->data['user_topic_preview'])) ? true : false;
		$this->tp_avatars    = (!empty($this->config['topic_preview_avatars']) && $this->config['allow_avatar']) ? true : false;
		$this->tp_last_post  = (!empty($this->config['topic_preview_last_post'])) ? true : false;
		$this->preview_limit = (int) $this->config['topic_preview_limit'];

		// statement parameters
		$this->tp_sql_select = ', fp.post_text AS first_post_preview_text' . ($this->tp_last_post ? ', lp.post_text AS last_post_preview_text' : '');
		$this->tp_sql_join   = ' LEFT JOIN ' . POSTS_TABLE . ' fp ON (fp.post_id = t.topic_first_post_id)' . ($this->tp_last_post ? ' LEFT JOIN ' . POSTS_TABLE . ' lp ON (lp.post_id = t.topic_last_post_id)' : '');

		if ($this->tp_avatars)
		{
			$this->tp_sql_select .= ', fpu.user_avatar AS first_user_avatar, fpu.user_avatar_type AS first_user_avatar_type' . ($this->tp_last_post ? ', lpu.user_avatar AS last_user_avatar, lpu.user_avatar_type AS last_user_avatar_type' : '');
			$this->tp_sql_join   .= ' LEFT JOIN ' . USERS_TABLE . ' fpu ON (fpu.user_id = t.topic_poster)' . ($this->tp_last_post ? ' LEFT JOIN ' . USERS_TABLE . ' lpu ON (lpu.user_id = t.topic_last_poster_id)' : '');
			$this->tp_avatar_fallback = '<img src="' . $this->root_path . 'styles/' . rawurlencode($this->user->style['style_path']) . '/theme/images/no_avatar.gif" width="60" height="60" alt="" />';
		}

		// Load our language file (only needed if showing last post text)
		if ($this->tp_last_post)
		{
			$this->user->add_lang_ext('vse/topicpreview', 'topic_preview');
		}
	}

	/**
	* Modify SQL array to get post text for viewforum topics
	*
	* @param	array	$sql_array 	SQL statement array
	* @return	array	SQL statement array
	*/
	public function modify_sql_array($sql_array)
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
	public function modify_sql($sql)
	{
		if (!$this->is_active)
		{
			return $sql;
		}

		global $shadow_topic_list;

		$sql = 'SELECT t.*' . $this->tp_sql_select . '
			FROM ' . TOPICS_TABLE . ' t ' . $this->tp_sql_join . '
			WHERE ' . $this->db->sql_in_set('t.topic_id', array_keys($shadow_topic_list));

		return $sql;
	}

	/**
	* Modify SQL SELECT statement to get post text for searchresults
	*
	* @param	string	$sql_select 	SQL SELECT statement
	* @return	string	SQL SELECT statement
	*/
	public function modify_sql_select($sql_select)
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
	public function modify_sql_join($sql_join)
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
	* @return	array	template vars array
	*/
	public function display_topic_preview($row, $block)
	{
		if (!$this->is_active)
		{
			return $block;
		}

		if (!empty($row['first_post_preview_text']))
		{
			$first_post_preview_text = $this->trim_topic_preview($row['first_post_preview_text'], $this->preview_limit);
		}

		if (!empty($row['last_post_preview_text']) && $row['topic_first_post_id'] != $row['topic_last_post_id'])
		{
			$last_post_preview_text = $this->trim_topic_preview($row['last_post_preview_text'], $this->preview_limit);
		}

		if ($this->tp_avatars)
		{
			$first_post_avatar = (!empty($row['first_user_avatar'])) ? get_user_avatar($row['first_user_avatar'], $row['first_user_avatar_type'], 60, 60) : $this->tp_avatar_fallback;
			$last_post_avatar  = (!empty($row['last_user_avatar'])) ? get_user_avatar($row['last_user_avatar'], $row['last_user_avatar_type'], 60, 60) : $this->tp_avatar_fallback;
		}

		$block = array_merge(array(
			'TOPIC_PREVIEW_FP'	=> (isset($first_post_preview_text)) ? censor_text($first_post_preview_text) : '',
			'TOPIC_PREVIEW_LP'	=> (isset($last_post_preview_text))  ? censor_text($last_post_preview_text)  : '',
			'TOPIC_PREVIEW_AVATAR_FP'	=> (isset($first_post_avatar) && $this->user->optionget('viewavatars')) ? $first_post_avatar : '',
			'TOPIC_PREVIEW_AVATAR_LP'	=> (isset($last_post_avatar) && $this->user->optionget('viewavatars')) ? $last_post_avatar : '',
		), $block);

		return $block;
	}

	/**
	* Truncate and clean topic preview text
	*
	* @param	string	$text 	topic preview text
	* @param	int		$limit 	number of characters to allow
	* @return	string	topic preview text
	* @access private
	*/
	private function trim_topic_preview($text, $limit)
	{
		$text = $this->strip_bbcodes_tags($text);

		if (utf8_strlen($text) >= $limit)
		{
			$text = (utf8_strlen($text) > $limit) ? utf8_substr($text, 0, $limit) : $text;
			// use last space before the character limit as the break-point, if one exists
			$new_limit = utf8_strrpos($text, ' ') != false ? utf8_strrpos($text, ' ') : $limit;
			return utf8_substr($text, 0, $new_limit) . '...';
		}

		return $text;
	}

	/**
	* Strip bbcodes, tags and links for topic preview text
	*
	* @param	string	$text 	topic preview text
	* @return	string	topic preview text
	* @access private
	*/
	private function strip_bbcodes_tags($text)
	{
		static $patterns = array();

		$text = smiley_text($text, true); // display smileys as text :)

		$strip_bbcodes = (empty($this->config['topic_preview_strip_bbcodes']) ? 'flash' : 'flash|' . trim($this->config['topic_preview_strip_bbcodes']));

		if (empty($patterns))
		{
			// RegEx patterns based on Topic Text Hover Mod by RMcGirr83
			$patterns = array(
				'#<a class="postlink[^>]*>(.*<\/a[^>]*>)?#', // Magic URLs			
				'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[(' . $strip_bbcodes . ')[^\[\]]+\].*\[/(' . $strip_bbcodes . ')[^\[\]]+\]#Usi', // bbcode content to strip
				'#\[/?[^\[\]]+\]#mi', // All bbcode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Remaining URLs
				'#"#', // Possible quotes from older board conversions
				'#[\s]+#' // Multiple spaces
			);
		}

		return trim(preg_replace($patterns, ' ', $text));
	}
}
