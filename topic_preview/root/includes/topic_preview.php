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
	* Add-On: Preserve line breaks?
	*/
	var $preserve_lb	= false;

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

		$this->is_active		= (!empty($config['topic_preview_limit']) && !empty($user->data['user_topic_preview'])) ? true : false;
		$this->preview_limit	= (int) $config['topic_preview_limit'];
		$this->strip_bbcodes	= (string) $config['topic_preview_strip_bbcodes'];
		$this->tp_sql_select	= ', fp.post_text AS first_post_preview_text' . (($this->tp_last_post) ? ', lp.post_text AS last_post_preview_text' : '');
		$this->tp_sql_join		= ' LEFT JOIN ' . POSTS_TABLE . ' fp ON (fp.post_id = t.topic_first_post_id)' . (($this->tp_last_post) ? ' LEFT JOIN ' . POSTS_TABLE . ' lp ON (lp.post_id = t.topic_last_post_id)' : '');
	}

	/**
	* Extend a query to get post text from the POSTS_TABLE
	* @access public
	*/
	function inject_sql_array($sql_array)
	{
		if (!$this->is_active)
		{
			return $sql_array;
		}

		$sql_array['LEFT_JOIN'][] = array(
			'FROM'	=> array(POSTS_TABLE => 'fp'),
			'ON'	=> "fp.post_id = t.topic_first_post_id"
		);

		if ($this->tp_last_post)
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(POSTS_TABLE => 'lp'),
				'ON'	=> "lp.post_id = t.topic_last_post_id"
			);
		}

		$sql_array['SELECT'] .= $this->tp_sql_select;

		return $sql_array;
	}

	/**
	* Rewrite the query to get more values from the POSTS_TABLE
	* @access public
	*/
	function inject_sql($sql)
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
	* Extend an $sql_select statement
	* @access public
	*/
	function inject_sql_select($sql_select)
	{
		if (!$this->is_active)
		{
			return $sql_select;
		}

		$sql_select .= $this->tp_sql_select;

		return $sql_select;
	}

	/**
	* Extend an $sql_join statement
	* @access public
	*/
	function inject_sql_join($sql_join)
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
	* @access public
	*/
	function display_topic_preview($row, $block)
	{
		if (!$this->is_active)
		{
			return false;
		}

		global $template;

		if (!empty($row['first_post_preview_text']))
		{
			$first_post_preview_text = $this->_trim_topic_preview($row['first_post_preview_text'], $this->preview_limit);
		}

		if (!empty($row['last_post_preview_text']) && $row['topic_first_post_id'] != $row['topic_last_post_id'])
		{
			$last_post_preview_text = $this->_trim_topic_preview($row['last_post_preview_text'], $this->preview_limit);
		}

		$template->alter_block_array($block, array(
			'TOPIC_PREVIEW_TEXT'	=> (isset($first_post_preview_text)) ? censor_text($first_post_preview_text) : '',
			'TOPIC_PREVIEW_TEXT2'	=> (isset($last_post_preview_text))  ? censor_text($last_post_preview_text)  : '',
		), true, 'change');
	}

	/**
	* Truncate and clean topic preview text
	* @access private
	*/
	function _trim_topic_preview($string, $limit)
	{
		$text = $this->_bbcode_strip($string);

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
	* This function's $RegEx originally written by RMcGirr83 for his Topic Text Hover Mod
	* Modified by Matt Friedman to display smileys as text, strip URLs, custom BBcodes and trim whitespace
	* @access private
	*/
	function _bbcode_strip($text)
	{
		static $RegEx = array();

		if ($this->tp_bbcode)
		{
			// use text inside [topicpreview] bbcode as the topic preview
			if (preg_match('#\[(topicpreview[^\[\]]+)\].*\[/\1\]#Usi', $text, $matches))
			{
				$text = $matches[0];
			}
		}

		$text = smiley_text($text, true); // display smileys as text :)
		$text = ($this->preserve_lb ? str_replace("\n", '&#13;&#10;', $text) : $text); // preserve line breaks

		$bbcode_strip = (empty($this->strip_bbcodes) ? 'flash' : 'flash|' . trim($this->strip_bbcodes));

		if (empty($RegEx))
		{
			$RegEx = array(
				'#<a class="postlink[^>]*>(.*<\/a[^>]*>)?#', // Strip magic URLs			
				'#<[^>]*>(.*<[^>]*>)?#Usi', // HTML code
				'#\[(' . $bbcode_strip . ')[^\[\]]+\].*\[/(' . $bbcode_strip . ')[^\[\]]+\]#Usi', // bbcode to strip
				'#\[/?[^\[\]]+\]#mi', // Strip all bbcode tags
				'#(http|https|ftp|mailto)(:|\&\#58;)\/\/[^\s]+#i', // Strip remaining URLs
				'#"#', // Possible quotes from older board conversions
				'#[\s]+#' // Multiple spaces
			);
		}

		return trim(preg_replace($RegEx, ' ', $text));
	}
}
