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

class display extends base
{
	/** @var int default width of topic preview */
	const PREVIEW_SIZE = 360;

	/** @var int default height and width of topic preview avatars */
	const AVATAR_SIZE = 60;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var \vse\topicpreview\core\trim_tools */
	protected $trim_tools;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config              $config
	 * @param \phpbb\event\dispatcher_interface $dispatcher
	 * @param \phpbb\template\template          $template
	 * @param \phpbb\user                       $user
	 * @param string                            $root_path
	 * @param \vse\topicpreview\core\trim_tools $trim_tools
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\event\dispatcher_interface $dispatcher, \phpbb\template\template $template, \phpbb\user $user, $root_path, \vse\topicpreview\core\trim_tools $trim_tools)
	{
		$this->dispatcher = $dispatcher;
		$this->template = $template;
		$this->root_path = $root_path;
		$this->trim_tools = $trim_tools;
		parent::__construct($config, $user);

		$this->setup();
	}
	/**
	 * Set up some common components
	 *
	 * @return null
	 * @access public
	 */
	public function setup()
	{
		// Load our language file (only needed if showing last post text)
		if ($this->last_post_enabled())
		{
			$this->user->add_lang_ext('vse/topicpreview', 'topic_preview');
		}

		$this->template->assign_vars(array(
			'S_TOPICPREVIEW'		=> $this->is_enabled(),
			'TOPICPREVIEW_THEME'	=> $this->get_theme(),
			'TOPICPREVIEW_DELAY'	=> $this->config['topic_preview_delay'],
			'TOPICPREVIEW_DRIFT'	=> $this->config['topic_preview_drift'],
			'TOPICPREVIEW_WIDTH'	=> (!empty($this->config['topic_preview_width'])) ? $this->config['topic_preview_width'] : self::PREVIEW_SIZE,
		));
	}

	/**
	 * Inject topic preview text into the template
	 *
	 * @param array $row   Row data
	 * @param array $block Template vars array
	 * @return array Template vars array
	 * @access public
	 */
	public function display_topic_preview($row, $block)
	{
		if (!$this->is_enabled())
		{
			return $block;
		}

		$block = array_merge($block, array(
			'TOPIC_PREVIEW_FIRST_POST'		=> (!empty($row['first_post_text'])) ? censor_text($this->trim_tools->trim_text($row['first_post_text'], $this->config['topic_preview_limit'])) : '',
			'TOPIC_PREVIEW_LAST_POST'		=> (!empty($row['last_post_text']) && ($row['topic_first_post_id'] != $row['topic_last_post_id'])) ? censor_text($this->trim_tools->trim_text($row['last_post_text'], $this->config['topic_preview_limit'])) : '',
			'TOPIC_PREVIEW_FIRST_AVATAR'	=> $this->get_user_avatar_helper($row, 'fp'),
			'TOPIC_PREVIEW_LAST_AVATAR'		=> $this->get_user_avatar_helper($row, 'lp'),
		));

		$tp_avatars = $this->avatars_enabled();

		/**
		 * EVENT to modify the topic preview display output before it gets inserted in the template block
		 *
		 * @event vse.topicpreview.display_topic_preview
		 * @var array row Row data
		 * @var array block Template vars array
		 * @var bool tp_avatars Display avatars setting
		 * @since 2.1.0
		 */
		$vars = array('row', 'block', 'tp_avatars');
		extract($this->dispatcher->trigger_event('vse.topicpreview.display_topic_preview', compact($vars)));

		return $block;
	}

	/**
	 * Get user avatar helper function
	 *
	 * @param array  $row    User row data
	 * @param string $poster Type of poster, fp or lp
	 * @return string Avatar image
	 * @access protected
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
				'avatar'		=> $row[$poster . '_avatar'],
				'avatar_type'	=> $row[$poster . '_avatar_type'],
				'avatar_width'	=> $row[$poster . '_avatar_width'],
				'avatar_height'	=> $row[$poster . '_avatar_height'],
			);

			$avatar = phpbb_get_user_avatar($map, 'USER_AVATAR', false, true);
		}

		// If avatar string is empty, fall back to no_avatar.gif
		return ($avatar) ?: '<img class="avatar" src="' . $this->root_path . 'styles/' . rawurlencode($this->user->style['style_path']) . '/theme/images/no_avatar.gif' . '" width="' . self::AVATAR_SIZE . '" height="' . self::AVATAR_SIZE . '" />';
	}

	/**
	 * Get user's style topic preview theme
	 * Fall back to no theme if expected theme not found
	 *
	 * @return mixed Theme name if theme file found, false otherwise
	 * @access protected
	 */
	protected function get_theme()
	{
		if (!empty($this->user->style['topic_preview_theme']))
		{
			if (file_exists($this->root_path . 'ext/vse/topicpreview/styles/all/theme/' . $this->user->style['topic_preview_theme'] . '.css'))
			{
				return $this->user->style['topic_preview_theme'];
			}
		}

		return false;
	}
}
