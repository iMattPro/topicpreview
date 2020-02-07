<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2015 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview;

/**
 * Extension class for custom enable/disable/purge actions
 */
class ext extends \phpbb\extension\base
{
	/** @var string Require 3.2.0 due to updated INCLUDECSS and ordered services */
	const PHPBB_MIN_VERSION = '3.2.0';

	/**
	 * Enable extension if phpBB minimum version requirement is met
	 * (check database and filesystem)
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return phpbb_version_compare($config['version'], self::PHPBB_MIN_VERSION, '>=') &&
			phpbb_version_compare(PHPBB_VERSION, self::PHPBB_MIN_VERSION, '>=');
	}
}
