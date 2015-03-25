<?php
/**
*
* Topic Preview
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace vse\topicpreview\acp;

/**
* @package acp
*/
class topic_preview_module
{
	const NO_THEME = 'no';
	const DEFAULT_THEME = 'light';

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $phpbb_root_path;

	/** @var array */
	protected $themes;

	/** @var string */
	public $u_action;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $cache, $config, $db, $phpbb_extension_manager, $request, $template, $user, $phpbb_root_path;

		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->ext_manager = $phpbb_extension_manager;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;

		$this->user->add_lang('acp/common');
		$this->user->add_lang_ext('vse/topicpreview', 'topic_preview_acp');
	}

	/**
	 * Main ACP module
	 *
	 * @param int    $id
	 * @param string $mode
	 * @return null
	 * @access public
	 */
	public function main($id, $mode)
	{
		$this->tpl_name = 'acp_topic_preview';
		$this->page_title = $this->user->lang('TOPIC_PREVIEW');

		$form_key = 'acp_topic_preview';
		add_form_key($form_key);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($this->user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

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

			trigger_error($this->user->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$styles = $this->get_styles();
		foreach ($styles as $row)
		{
			$this->template->assign_block_vars('styles', array(
				'STYLE_ID'				=> $row['style_id'],
				'STYLE_THEME'			=> $this->user->lang('TOPIC_PREVIEW_THEME', $row['style_name']),
				'STYLE_THEME_EXPLAIN'	=> $this->user->lang('TOPIC_PREVIEW_THEME_EXPLAIN', $row['style_name']),
				'THEME_OPTIONS'			=> $this->theme_options($row['topic_preview_theme']),
			));
		}

		$this->template->assign_vars(array(
			'TOPIC_PREVIEW_LIMIT'		=> $this->config['topic_preview_limit'],
			'TOPIC_PREVIEW_WIDTH'		=> $this->config['topic_preview_width'],
			'TOPIC_PREVIEW_DELAY'		=> $this->config['topic_preview_delay'],
			'TOPIC_PREVIEW_DRIFT'		=> $this->config['topic_preview_drift'],
			'S_TOPIC_PREVIEW_AVATARS'	=> $this->config['topic_preview_avatars'],
			'S_TOPIC_PREVIEW_LAST_POST'	=> $this->config['topic_preview_last_post'],
			'TOPIC_PREVIEW_STRIP'		=> $this->config['topic_preview_strip_bbcodes'],
			'U_ACTION'					=> $this->u_action,
		));
	}

	/**
	 * Update topic_preview_theme setting in the styles table
	 *
	 * @param int    $style_id Identifier of the board style
	 * @param string $theme    Name of the selected theme
	 * @return null
	 * @access protected
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
	 * @return array Style data array
	 * @access protected
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
	 * @return array File name data array
	 * @access protected
	 */
	protected function get_themes()
	{
		$finder = $this->ext_manager->get_finder();

		// Find css files in ext/vse/topicpreview/styles/all/theme/
		$files = $finder
			->extension_suffix('.css')
			->extension_directory('/styles/all/theme')
			->find_from_extension('topicpreview', $this->phpbb_root_path . 'ext/vse/topicpreview/');

		// Get just basenames of array keys
		$files = array_map(function ($value) {
			return basename($value, '.css');
		}, array_keys($files));

		return $files;
	}

	/**
	 * Set themes data array
	 *
	 * @return null
	 * @access protected
	 */
	protected function set_themes()
	{
		if (!isset($this->themes))
		{
			// Get an array of available theme names
			$this->themes = $this->get_themes();

			// Add option for native browser tooltip (aka no theme)
			$this->themes[] = self::NO_THEME;
		}
	}

	/**
	 * Create <option> tags for each Topic Preview theme
	 *
	 * @param string $current Name of the Topic Preview theme stored in the db
	 * @return string HTML <option> tags for Topic Preview themes
	 * @access protected
	 */
	protected function theme_options($current)
	{
		$this->set_themes();

		// If current theme name not available, fallback to default theme
		if (!in_array($current, $this->themes))
		{
			$current = self::DEFAULT_THEME;
		}

		$theme_options = '';
		foreach ($this->themes as $theme)
		{
			$display_name = ($theme == self::NO_THEME) ? $this->user->lang('NO') : ucwords($theme);
			$selected = ($theme == $current) ? ' selected="selected"' : '';
			$theme_options .= '<option value="' . $theme . '"' . $selected . '>' . $display_name . ' ' . $this->user->lang('THEME') . '</option>';
		}

		return $theme_options;
	}
}
