<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\tests\core\tools;

require_once __DIR__ . '/../../../../../../includes/functions.php';
require_once __DIR__ . '/../../../../../../includes/functions_content.php';

class base extends \phpbb_test_case
{
	/** @var \phpbb\config\config */
	protected $config;

	protected function setUp(): void
	{
		parent::setUp();

		global $config;

		$this->config = $config = new \phpbb\config\config(array(
			'topic_preview_limit'			=> 150,
			'topic_preview_strip_bbcodes'	=> 'quote',
		));
	}
}
