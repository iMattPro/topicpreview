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

use phpbb\avatar\helper as avatar_helper;
use phpbb\config\config;
use phpbb\event\dispatcher_interface;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;

class display extends base
{
	/** @var int default width of topic preview */
	public const PREVIEW_SIZE = 360;

	/** @var string */
	public const NO_AVATAR = 'no-avatar';

	/** @var avatar_helper|null */
	protected $avatar_helper;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var renderer */
	protected $renderer;

	/** @var string|false */
	protected $topic_preview_theme;

	/** @var array */
	protected $attachments_cache = [];

	/**
	 * Constructor
	 *
	 * @param config               $config     Config object
	 * @param dispatcher_interface $dispatcher Event dispatcher object
	 * @param language             $language   Language object
	 * @param template             $template   Template object
	 * @param renderer             $renderer   Text renderer object
	 * @param user                 $user       User object
	 * @param string               $root_path
	 * @param avatar_helper|null   $avatar_helper Avatar helper object (phpBB 4.0.0)
	 */
	public function __construct(config $config, dispatcher_interface $dispatcher, language $language, template $template, renderer $renderer, user $user, $root_path, avatar_helper $avatar_helper = null)
	{
		$this->avatar_helper = $avatar_helper;
		$this->dispatcher = $dispatcher;
		$this->language = $language;
		$this->template = $template;
		$this->renderer = $renderer;
		$this->root_path = $root_path;
		parent::__construct($config, $user);

		$this->setup();
	}

	/**
	 * Set up some common components
	 */
	public function setup()
	{
		// Load language file (needed since we're rendering post-content)
		if ($this->last_post_enabled())
		{
			$this->language->add_lang('viewtopic');
			$this->language->add_lang('topic_preview', 'vse/topicpreview');
		}

		$this->topic_preview_theme = $this->get_theme();

		$this->template->assign_vars(array(
			'S_TOPICPREVIEW'		=> $this->is_enabled(),
			'TOPICPREVIEW_THEME'	=> $this->topic_preview_theme,
			'TOPICPREVIEW_DELAY'	=> $this->config['topic_preview_delay'],
			'TOPICPREVIEW_DRIFT'	=> $this->config['topic_preview_drift'],
			'TOPICPREVIEW_WIDTH'	=> !empty($this->config['topic_preview_width']) ? $this->config['topic_preview_width'] : self::PREVIEW_SIZE,
		));
	}

	/**
	 * Inject topic preview text into the template
	 *
	 * @param array $row   Row data
	 * @param array $block Template vars array
	 *
	 * @return array Template vars array
	 */
	public function display_topic_preview($row, $block)
	{
		if (!$this->is_enabled())
		{
			return $block;
		}

		$block = array_merge($block, array(
			'TOPIC_PREVIEW_FIRST_POST'		=> $this->get_text_helper($row, 'first_post_text'),
			'TOPIC_PREVIEW_LAST_POST'		=> $this->get_text_helper($row, 'last_post_text'),
			'TOPIC_PREVIEW_FIRST_AVATAR'	=> $this->get_user_avatar_helper($row, 'fp'),
			'TOPIC_PREVIEW_LAST_AVATAR'		=> $this->get_user_avatar_helper($row, 'lp'),
		));

		$tp_avatars = $this->avatars_enabled();

		/**
		 * EVENT to modify the topic preview display output before it gets inserted in the template block
		 *
		 * @event vse.topicpreview.display_topic_preview
		 * @var array row        Topic row data
		 * @var array block      Template vars array
		 * @var bool  tp_avatars Display avatars setting
		 * @since 2.1.0
		 */
		$vars = array('row', 'block', 'tp_avatars');
		extract($this->dispatcher->trigger_event('vse.topicpreview.display_topic_preview', compact($vars)));

		return $block;
	}

	/**
	 * Get topic preview text helper function
	 * This handles the trimming and censoring
	 *
	 * @param array  $row  User row data
	 * @param string $post The first or last post-text column key
	 *
	 * @return string The trimmed and censored topic preview text
	 */
	protected function get_text_helper($row, $post)
	{
		// Ignore empty/unset text or when the last post is also the first (and only) post
		if (empty($row[$post]) || ($post === 'last_post_text' && $row['topic_first_post_id'] === $row['topic_last_post_id']))
		{
			return '';
		}

		$attachments = [];
		if ($this->attachments_cache)
		{
			$post_id = $post === 'first_post_text' ? $row['topic_first_post_id'] : $row['topic_last_post_id'];
			$attachments = $this->attachments_cache[$post_id] ?? [];
		}

		return censor_text(
			$this->renderer->render_text(
				$row[$post],
				(int) $this->config['topic_preview_limit'],
				$this->config['topic_preview_strip_bbcodes'],
				(bool) $this->config['topic_preview_rich_text'],
				(bool) $this->topic_preview_theme,
				$attachments,
				$row['forum_id']
			)
		);
	}

	/**
	 * Get user avatar helper function
	 *
	 * @param array  $row    User row data
	 * @param string $poster Type of poster, fp or lp
	 *
	 * @return string Avatar image
	 */
	protected function get_user_avatar_helper($row, $poster)
	{
		if (!$this->avatars_enabled())
		{
			return '';
		}

		$avatar = '';
		if (!empty($row[$poster . '_avatar']))
		{
			$map = array(
				'user_avatar'		=> $row[$poster . '_avatar'],
				'user_avatar_type'	=> $row[$poster . '_avatar_type'],
				'user_avatar_width'	=> $row[$poster . '_avatar_width'],
				'user_avatar_height'=> $row[$poster . '_avatar_height'],
			);

			if ($this->avatar_helper !== null)
			{
				$avatar = $this->avatar_helper->get_user_avatar($map, 'USER_AVATAR', false, true)['html'];
			}
			else if (function_exists('phpbb_get_user_avatar'))
			{
				$avatar = phpbb_get_user_avatar($map, 'USER_AVATAR', false, true);
			}
		}

		// If the avatar string is empty, fall back to no_avatar.gif
		return $avatar ?: self::NO_AVATAR;
	}

	/**
	 * Get a user's style topic preview theme
	 * Fall back to no theme if expected theme not found
	 *
	 * @return mixed Theme name if a theme file found, false otherwise
	 */
	protected function get_theme()
	{
		if (!empty($this->user->style['topic_preview_theme']) && file_exists($this->root_path . 'ext/vse/topicpreview/styles/all/theme/' . $this->user->style['topic_preview_theme'] . '.css'))
		{
			return $this->user->style['topic_preview_theme'];
		}

		return false;
	}

	/**
	 * Set attachments cache
	 *
	 * @param array $attachments Attachments grouped by post_id
	 */
	public function set_attachments_cache($attachments)
	{
		$this->attachments_cache = $attachments;
	}
}
