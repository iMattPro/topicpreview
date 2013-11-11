<?php
/**
*
* @package Topic Preview
* @copyright (c) 2013 Matt Friedman
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vse\topicpreview\acp;

/**
* @package acp
*/
class topic_preview_module
{
	public $u_action;

	protected $db;
	protected $cache;
	protected $user;
	protected $template;
	protected $request;
	protected $config;
	protected $phpbb_root_path;

	public function main($id, $mode)
	{
		global $db, $cache, $user, $template, $request, $config, $phpbb_root_path;

		$this->db = $db;
		$this->cache = $cache;
		$this->user = $user;
		$this->template = $template;
		$this->request = $request;
		$this->config = $config;
		$this->phpbb_root_path = $phpbb_root_path;

		$this->user->add_lang('acp/common');
		$this->tpl_name = 'acp_topic_preview';
		$this->page_title = $this->user->lang['TOPIC_PREVIEW'];

		$form_key = 'acp_topic_preview';
		add_form_key($form_key);

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error('FORM_INVALID');
			}

			$topic_preview_limit = $this->request->variable('topic_preview_limit', 0);
			$this->config->set('topic_preview_limit', abs($topic_preview_limit));

			$topic_preview_strip_bbcodes = $this->request->variable('topic_preview_strip_bbcodes', '');
			$this->config->set('topic_preview_strip_bbcodes', $topic_preview_strip_bbcodes);

			$topic_preview_avatars = $this->request->variable('topic_preview_avatars', 0);
			$this->config->set('topic_preview_avatars', $topic_preview_avatars);

			$topic_preview_last_post = $this->request->variable('topic_preview_last_post', 0);
			$this->config->set('topic_preview_last_post', $topic_preview_last_post);

			$topic_preview_width = $this->request->variable('topic_preview_width', 0);
			$this->config->set('topic_preview_width', abs($topic_preview_width));

			$topic_preview_delay = $this->request->variable('topic_preview_delay', 0);
			$this->config->set('topic_preview_delay', abs($topic_preview_delay));

			$topic_preview_drift = $this->request->variable('topic_preview_drift', 0);
			$this->config->set('topic_preview_drift', $topic_preview_drift);

			$styles = $this->get_styles();
			foreach ($styles as $row)
			{
				$topic_preview_theme = $this->request->variable($row['style_name'], '');
				$this->set_style_theme($row['style_name'], $topic_preview_theme);
			}

			trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$styles = $this->get_styles();
		foreach ($styles as $row)
		{
			$this->template->assign_block_vars('styles', array(
				'STYLE_NAME'			=> $row['style_name'],
				'THEME_OPTIONS'			=> $this->theme_options((!empty($row['topic_preview_theme'])) ? $row['topic_preview_theme'] : 'light'),
				'THEME_STYLE'			=> sprintf($this->user->lang['TOPIC_PREVIEW_THEME'], $row['style_name']),
				'THEME_STYLE_EXPLAIN'	=> sprintf($this->user->lang['TOPIC_PREVIEW_THEME_EXPLAIN'], $row['style_name']),
			));
		}
		
		$this->template->assign_vars(array(
			'TOPIC_PREVIEW_LIMIT'		=> isset($this->config['topic_preview_limit']) ? $this->config['topic_preview_limit'] : '',
			'TOPIC_PREVIEW_STRIP'		=> isset($this->config['topic_preview_strip_bbcodes']) ? $this->config['topic_preview_strip_bbcodes'] : '',
			'S_TOPIC_PREVIEW_AVATARS'	=> isset($this->config['topic_preview_avatars']) ? $this->config['topic_preview_avatars'] : false,
			'S_TOPIC_PREVIEW_LAST_POST'	=> isset($this->config['topic_preview_last_post']) ? $this->config['topic_preview_last_post'] : false,
			'TOPIC_PREVIEW_WIDTH'		=> isset($this->config['topic_preview_width']) ? $this->config['topic_preview_width'] : '',
			'TOPIC_PREVIEW_DELAY'		=> isset($this->config['topic_preview_delay']) ? $this->config['topic_preview_delay'] : '',
			'TOPIC_PREVIEW_DRIFT'		=> isset($this->config['topic_preview_drift']) ? $this->config['topic_preview_drift'] : '',
			'TOPIC_PREVIEW_VERSION'		=> isset($this->config['topic_preview_version']) ? 'v' . $this->config['topic_preview_version'] : '',
			'U_ACTION'					=> $this->u_action,
		));
	}

	/*
	* Update topic_preview_theme setting in the styles table
	*
	* @param	string	$style 	name of the board style
	* @param	string	$theme	name of the selected theme
	* @access	protected
	*/
	protected function set_style_theme($style, $theme)
	{
		$sql = 'UPDATE ' . STYLES_TABLE . "
			SET topic_preview_theme = '" . $this->db->sql_escape($theme) . "'
			WHERE style_name = '" . $this->db->sql_escape($style) . "'";

		$result = $this->db->sql_query($sql);

		$this->cache->destroy('sql', STYLES_TABLE);
	}

	/*
	* Get style data from the styles table
	*
	* @return	Array of style data
	* @access	protected
	*/
	protected function get_styles()
	{
		$sql = 'SELECT style_id, style_name, topic_preview_theme
			FROM ' . STYLES_TABLE;
		$result = $this->db->sql_query($sql);

		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/*
	* Get file paths/names from Topic Preview's CSS files
	*
	* @return	Array of file data from ext/vse/topicpreview/styles/all/theme/
	* @access	protected
	*/
	protected function get_themes()
	{
		global $phpbb_extension_manager;

		$finder = $phpbb_extension_manager->get_finder();

		return $finder
			->extension_suffix('.css')
			->extension_directory('/styles/all/theme')
			->find_from_extension('topicpreview', $this->phpbb_root_path . 'ext/vse/topicpreview');
	}

	/*
	* Create <option> tags for each Topic Preview theme
	*
	* @param	string	$theme	name of the Topic Preview theme stored in the db
	* @return	string	html <option> tags for Topic Preview themes
	* @access	protected
	*/
	protected function theme_options($theme)
	{
		static $themes = array();
		
		if (empty($themes))
		{
			$themes = $this->get_themes();
		}

		$theme_options = '';
		foreach($themes as $name => $ext)
		{
			$name = basename($name, '.css');
			$selected = ($theme == $name) ? ' selected="selected"' : '';
			$theme_options .= '<option value="' . $name . '"' . $selected . '>' . ucwords($name) . ' ' . $this->user->lang['THEME'] . '</option>';
		}
		
		return $theme_options;
	}
}
