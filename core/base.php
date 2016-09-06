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
use phpbb\user;

class base
{
	/** @var config */
	protected $config;

	/** @var user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param config $config Config object
	 * @param user   $user   User object
	 */
	public function __construct(config $config, user $user)
	{
		$this->config = $config;
		$this->user = $user;
	}

	/**
	 * Show topic previews, given current board and user configurations
	 *
	 * @return bool
	 */
	public function is_enabled()
	{
		return (bool) !empty($this->config['topic_preview_limit']) && !empty($this->user->data['user_topic_preview']);
	}

	/**
	 * Show avatars, given current board and user configurations
	 *
	 * @return bool
	 */
	public function avatars_enabled()
	{
		return (bool) $this->config['topic_preview_avatars'] && $this->config['allow_avatar'] && $this->user->optionget('viewavatars');
	}

	/**
	 * Show last post text, given current board configuration
	 *
	 * @return bool
	 */
	public function last_post_enabled()
	{
		return (bool) $this->config['topic_preview_last_post'];
	}
}
