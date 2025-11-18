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
	/** @var string Require 3.3.0 due to the expectation that all posts are parsed by textformatter */
	public const PHPBB_MIN_VERSION = '3.3.0';

	/** @var string */
	public const PHPBB_MAX_VERSION = '4.0.0-dev';

	/**
	 * Enable extension if phpBB minimum version requirement is met
	 * (check database and filesystem)
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		$config_version = $this->container->get('config')['version'];

		return $this->is_version_compatible($config_version)
			&& $this->is_version_compatible(PHPBB_VERSION);
	}

	/**
	 * Check if a version is within the acceptable range
	 *
	 * @param string $version Version to check
	 * @return bool
	 */
	private function is_version_compatible($version)
	{
		return phpbb_version_compare($version, self::PHPBB_MIN_VERSION, '>=')
			&& phpbb_version_compare($version, self::PHPBB_MAX_VERSION, '<');
	}
}
