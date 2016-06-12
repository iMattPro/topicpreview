<?php
/**
*
* Topic Preview
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\controller;

use phpbb\cache\driver\driver_interface as cache_driver;
use phpbb\config\config;
use phpbb\db\driver\driver_interface as db_driver;
use phpbb\extension\manager;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

/**
 * Class acp_controller
 *
 * @package vse\topicpreview\controller
 */
class acp_controller implements acp_controller_interface
{
	const NO_THEME = 'no';
	const DEFAULT_THEME = 'light';

	/** @var cache_driver */
	protected $cache;

	/** @var config */
	protected $config;

	/** @var db_driver */
	protected $db;

	/** @var manager */
	protected $ext_manager;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param cache_driver $cache
	 * @param config       $config
	 * @param db_driver    $db
	 * @param manager      $phpbb_ext_manager
	 * @param request      $request
	 * @param template     $template
	 * @param user         $user
	 * @param              $phpbb_root_path
	 */
	public function __construct(cache_driver $cache, config $config, db_driver $db, manager $phpbb_ext_manager, request $request, template $template, user $user, $phpbb_root_path)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->ext_manager = $phpbb_ext_manager;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
	}

	/**
	 * @inheritdoc
	 */
	public function handle()
	{
		$this->user->add_lang_ext('vse/topicpreview', 'topic_preview_acp');

		$form_key = 'acp_topic_preview';
		add_form_key($form_key);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($this->user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$this->submit_settings();

			trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$this->display_settings();
	}

	/**
	 * @inheritdoc
	 */
	public function set_u_action($u_action)
	{
		$this->u_action = $u_action;
		return $this;
	}

	/**
	 * Display the settings with the current config values
	 */
	protected function display_settings()
	{
		$this->template->assign_vars(array(
			'TOPIC_PREVIEW_LIMIT'		=> $this->config['topic_preview_limit'],
			'TOPIC_PREVIEW_WIDTH'		=> $this->config['topic_preview_width'],
			'TOPIC_PREVIEW_DELAY'		=> $this->config['topic_preview_delay'],
			'TOPIC_PREVIEW_DRIFT'		=> $this->config['topic_preview_drift'],
			'S_TOPIC_PREVIEW_AVATARS'	=> $this->config['topic_preview_avatars'],
			'S_TOPIC_PREVIEW_LAST_POST'	=> $this->config['topic_preview_last_post'],
			'TOPIC_PREVIEW_STRIP'		=> $this->config['topic_preview_strip_bbcodes'],
			'TOPIC_PREVIEW_STYLES'		=> $this->get_styles(),
			'TOPIC_PREVIEW_THEMES'		=> $this->get_themes(),
			'TOPIC_PREVIEW_DEFAULT'		=> self::DEFAULT_THEME,
			'TOPIC_PREVIEW_NO_THEME'	=> self::NO_THEME,
			'U_ACTION'					=> $this->u_action,
		));
	}

	/**
	 * Submit the settings from the form to the database
	 */
	protected function submit_settings()
	{
		$this->config->set('topic_preview_limit', abs($this->request->variable('topic_preview_limit', 0))); // abs() no negative values
		$this->config->set('topic_preview_width', abs($this->request->variable('topic_preview_width', 0))); // abs() no negative values
		$this->config->set('topic_preview_delay', abs($this->request->variable('topic_preview_delay', 0))); // abs() no negative values
		$this->config->set('topic_preview_drift', $this->request->variable('topic_preview_drift', 0));
		$this->config->set('topic_preview_avatars', $this->request->variable('topic_preview_avatars', 0));
		$this->config->set('topic_preview_last_post', $this->request->variable('topic_preview_last_post', 0));
		$this->config->set('topic_preview_strip_bbcodes', $this->request->variable('topic_preview_strip_bbcodes', ''));

		$styles = $this->get_styles();
		foreach ($styles as $row)
		{
			$this->set_style_theme($row['style_id'], $this->request->variable('style_' . $row['style_id'], ''));
		}
	}

	/**
	 * Update topic_preview_theme setting in the styles table
	 *
	 * @param int    $style_id Identifier of the board style
	 * @param string $theme    Name of the selected theme
	 */
	protected function set_style_theme($style_id, $theme)
	{
		$sql = 'UPDATE ' . STYLES_TABLE . "
			SET topic_preview_theme = '" . $this->db->sql_escape($theme) . "'
			WHERE style_id = " . (int) $style_id;

		$this->db->sql_query($sql);

		$this->cache->destroy('sql', STYLES_TABLE);
	}

	/**
	 * Get style data from the styles table
	 *
	 * @return array Style data
	 */
	protected function get_styles()
	{
		$sql = 'SELECT style_id, style_name, topic_preview_theme
			FROM ' . STYLES_TABLE . '
			WHERE style_active = 1';
		$result = $this->db->sql_query($sql);

		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Get file names from Topic Preview's CSS files
	 *
	 * @return array File name data
	 */
	protected function get_themes()
	{
		$finder = $this->ext_manager->get_finder();

		// Find css files in ext/vse/topicpreview/styles/all/theme/
		$themes = $finder
			->extension_suffix('.css')
			->extension_directory('/styles/all/theme')
			->find_from_extension('topicpreview', $this->phpbb_root_path . 'ext/vse/topicpreview/');

		// Get just basenames of array keys
		$themes = array_map(function ($value) {
			return basename($value, '.css');
		}, array_keys($themes));

		// Add option for native browser tooltip (aka no theme)
		$themes[] = self::NO_THEME;

		return $themes;
	}
}
