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

		$submit = (isset($_POST['submit'])) ? true : false;

		if ($submit)
		{
			if (!check_form_key($form_key))
			{
				trigger_error('FORM_INVALID');
			}

			$topic_preview_limit = $this->request->variable('topic_preview_limit', 0);
			set_config('topic_preview_limit', abs($topic_preview_limit));

			$topic_preview_strip_bbcodes = $this->request->variable('topic_preview_strip_bbcodes', '');
			set_config('topic_preview_strip_bbcodes', $topic_preview_strip_bbcodes);

			$topic_preview_avatars = $this->request->variable('topic_preview_avatars', 0);
			set_config('topic_preview_avatars', $topic_preview_avatars);

			$topic_preview_last_post = $this->request->variable('topic_preview_last_post', 0);
			set_config('topic_preview_last_post', $topic_preview_last_post);

			trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'TOPIC_PREVIEW_LIMIT'		=> isset($this->config['topic_preview_limit']) ? $this->config['topic_preview_limit'] : '',
			'TOPIC_PREVIEW_STRIP'		=> isset($this->config['topic_preview_strip_bbcodes']) ? $this->config['topic_preview_strip_bbcodes'] : '',
			'S_TOPIC_PREVIEW_AVATARS'	=> isset($this->config['topic_preview_avatars']) ? $this->config['topic_preview_avatars'] : false,
			'S_TOPIC_PREVIEW_LAST_POST'	=> isset($this->config['topic_preview_last_post']) ? $this->config['topic_preview_last_post'] : false,
			'TOPIC_PREVIEW_VERSION'		=> isset($this->config['topic_preview_version']) ? 'v' . $this->config['topic_preview_version'] : '',
			'U_ACTION'					=> $this->u_action,
		));
	}
}
