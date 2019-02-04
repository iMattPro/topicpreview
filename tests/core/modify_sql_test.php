<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2014 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\core;

class modify_sql_test extends base
{
	public static function topic_preview_data()
	{
		return array(
			array(
				array(
					// First post by user 1, no last post
					1 => array(
						'First message', 'avatar1.jpg', 0, 60, 60,
						'First message', 'avatar1.jpg', 0, 60, 60,
					),
					// First post by user 2, last post by user 3
					2 => array(
						'Second message', 'avatar2.jpg', 0, 100, 100,
						'Third message', '', 0, 0, 0,
					),
					// First post by user 3, no last post
					3 => array(
						'Fourth message', '', 0, 0, 0,
						'Fourth message', '', 0, 0, 0,
					),
				),
			),
		);
	}

	/**
	* @dataProvider topic_preview_data
	*/
	public function test_modify_sql_array($expected)
	{
		// We need a basic SQL query to begin with
		$sql_array = array(
			'SELECT'	=> 't.*',
			'FROM'		=> array(
				'phpbb_topics'		=> 't'
			),
			'LEFT_JOIN'	=> array(),
			'WHERE'		=> 't.forum_id = 2',
			'ORDER_BY'	=> 't.topic_time ASC',
		);

		// Get an instance of topic preview data class
		$preview_data = $this->get_topic_preview_data();

		// Modify the sql_array for topic previews
		$sql_array = $preview_data->modify_sql($sql_array, 'SELECT');

		// Build the SQL query
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		// Test that we get the expected result
		$this->assertEquals($expected, $this->run_query_helper($sql));
	}

	/**
	* @dataProvider topic_preview_data
	*/
	public function test_modify_sql_string($expected)
	{
		// We need a basic SQL query to begin with
		$sql_select = 't.*';
		$sql_from = 'phpbb_topics t';
		$sql_where = 't.forum_id = 2 ORDER BY t.topic_time ASC';

		// Get an instance of topic preview data class
		$preview_data = $this->get_topic_preview_data();

		// Modify the sql strings for topic previews
		$sql_select = $preview_data->modify_sql($sql_select, 'SELECT');
		$sql_from = $preview_data->modify_sql($sql_from, 'JOIN');

		// Build the SQL query
		$sql = "SELECT $sql_select
			FROM $sql_from
			WHERE $sql_where";

		// Test that we get the expected result
		$this->assertEquals($expected, $this->run_query_helper($sql));
	}

	public function run_query_helper($sql)
	{
		$rowset = array();
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[$row['topic_id']] = array(
				$row['first_post_text'],
				$row['fp_avatar'],
				$row['fp_avatar_type'],
				$row['fp_avatar_width'],
				$row['fp_avatar_height'],
				$row['last_post_text'],
				$row['lp_avatar'],
				$row['lp_avatar_type'],
				$row['lp_avatar_width'],
				$row['lp_avatar_height'],
			);
		}
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	public function test_modify_sql_disabled()
	{
		// Disable topic preview
		$this->config['topic_preview_limit'] = 0;

		// Set a generic test string
		$sql_stmt = 'FOOBAR';

		// Get an instance of topic preview data class
		$preview_data = $this->get_topic_preview_data();

		// Test that we get back the unmodified test string
		$this->assertEquals($sql_stmt, $preview_data->modify_sql($sql_stmt, 'SELECT'));
	}
}
