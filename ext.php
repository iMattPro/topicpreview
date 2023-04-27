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
		return $this->version_check($config['version']) && $this->version_check(PHPBB_VERSION);
	}

	/**
	 * Enable version check
	 *
	 * @param string|int $version The version to check
	 * @return bool
	 */
	protected function version_check($version)
	{
		return phpbb_version_compare($version, self::PHPBB_MIN_VERSION, '>=');
	}
}
