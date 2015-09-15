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

class data extends base
{
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
	 * @param string       $type     Type of SQL statement SELECT|JOIN
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
}
