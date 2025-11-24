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

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\user;

class data extends base
{
	/** @var driver_interface */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param config $config Config object
	 * @param user   $user   User object
	 * @param driver_interface $db Database driver
	 */
	public function __construct(config $config, user $user, driver_interface $db)
	{
		$this->db = $db;
		parent::__construct($config, $user);
	}

	/**
	 * Update an SQL SELECT statement to get data needed for topic previews
	 *
	 * @return string SQL SELECT appendage
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
			$sql .= $this->build_avatar_select('fp');

			if ($this->last_post_enabled())
			{
				$sql .= $this->build_avatar_select('lp');
			}
		}

		return $sql;
	}

	/**
	 * Update an SQL JOIN statement to get data needed for topic previews
	 *
	 * @return array SQL JOIN params
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
	 * @param string       $type     Type of SQL statement SELECT|JOIN
	 *
	 * @return string|array SQL statement string or array
	 */
	public function modify_sql($sql_stmt, $type)
	{
		if (!$this->is_enabled())
		{
			return $sql_stmt;
		}

		if (is_array($sql_stmt))
		{
			$sql_stmt = $this->build_join_array($sql_stmt);
			$sql_stmt['SELECT'] .= $this->tp_sql_select();
			return $sql_stmt;
		}

		if ($type === 'SELECT')
		{
			$sql_stmt .= $this->tp_sql_select();
			return $sql_stmt;
		}

		return $this->build_join_statement($sql_stmt);
	}

	/**
	 * Build select statement for user avatar fields, e.g.:
	 * ', fpu.user_avatar AS fp_avatar
	 *  , fpu.user_avatar_type AS fp_avatar_type
	 *  , fpu.user_avatar_width AS fp_avatar_width
	 *  , fpu.user_avatar_height AS fp_avatar_height'
	 *
	 * @param string $prefix First or last post (fp|lp)
	 *
	 * @return string Partial sql statement
	 */
	protected function build_avatar_select($prefix)
	{
		$sql = '';

		$avatar_ary = array(
			'user_avatar'        => 'avatar',
			'user_avatar_type'   => 'avatar_type',
			'user_avatar_width'  => 'avatar_width',
			'user_avatar_height' => 'avatar_height',
		);

		foreach ($avatar_ary as $key => $var)
		{
			$sql .= ", {$prefix}u.$key AS {$prefix}_$var";
		}

		return $sql;
	}

	/**
	 * Add LEFT_JOIN statements to an sql array
	 *
	 * @param array $sql_stmt An sql array
	 *
	 * @return array Updated sql array
	 */
	protected function build_join_array($sql_stmt)
	{
		$array = $this->tp_sql_join();

		foreach ($array['LEFT_JOIN'] as $join)
		{
			$sql_stmt['LEFT_JOIN'][] = $join;
		}

		return $sql_stmt;
	}

	/**
	 * Add LEFT_JOIN statements to an sql statement
	 *
	 * @param string $sql_stmt An sql statement
	 *
	 * @return string Updated sql statement
	 */
	protected function build_join_statement($sql_stmt)
	{
		$array = $this->tp_sql_join();

		foreach ($array['LEFT_JOIN'] as $join)
		{
			$sql_stmt .= ' LEFT JOIN ' . key($join['FROM']) . ' ' . current($join['FROM']) . ' ON (' . $join['ON'] . ')';
		}

		return $sql_stmt;
	}

	/**
	 * Get attachments for topics from a rowset
	 *
	 * @param array $rowset Array of topic rows
	 * @return array Attachments grouped by post_id
	 */
	public function get_attachments_for_topics($rowset)
	{
		if (!$this->is_enabled() || !$this->attachments_enabled())
		{
			return [];
		}

		$post_ids = [];
		foreach ($rowset as $row)
		{
			if ($row['topic_attachment'])
			{
				$post_ids[] = $row['topic_first_post_id'];
				if ($this->last_post_enabled() && $row['topic_first_post_id'] !== $row['topic_last_post_id'])
				{
					$post_ids[] = $row['topic_last_post_id'];
				}
			}
		}

		if (empty($post_ids))
		{
			return [];
		}

		return $this->get_attachments(array_unique($post_ids));
	}

	/**
	 * Get attachments for multiple posts
	 *
	 * @param array $post_ids Array of post IDs
	 * @return array Attachments grouped by post_id
	 */
	protected function get_attachments($post_ids)
	{
		if (empty($post_ids))
		{
			return [];
		}

		// Limit to prevent excessive memory usage - process in chunks if needed
		$chunk_size = 100;
		$attachments = [];

		foreach (array_chunk($post_ids, $chunk_size) as $chunk)
		{
			$sql = 'SELECT *
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE ' . $this->db->sql_in_set('post_msg_id', $chunk) . '
					AND in_message = 0
				ORDER BY post_msg_id, filetime ASC';
			$result = $this->db->sql_query_limit($sql, 500);

			while ($row = $this->db->sql_fetchrow($result))
			{
				// Limit to 2 attachments per post
				if (!isset($attachments[$row['post_msg_id']]) || count($attachments[$row['post_msg_id']]) < 2)
				{
					$attachments[$row['post_msg_id']][] = $row;
				}
			}
			$this->db->sql_freeresult($result);
		}

		return $attachments;
	}
}
