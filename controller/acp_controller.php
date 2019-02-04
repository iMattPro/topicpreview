<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2013, 2016 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\controller;

use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use vse\topicpreview\core\settings;

class acp_controller implements acp_controller_interface
{
	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var settings */
	protected $settings;

	/** @var template */
	protected $template;

	/** @var string */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param language $language
	 * @param request  $request
	 * @param settings $settings
	 * @param template $template
	 */
	public function __construct(language $language, request $request, settings $settings, template $template)
	{
		$this->language = $language;
		$this->request = $request;
		$this->settings = $settings;
		$this->template = $template;
	}

	/**
	 * @inheritdoc
	 */
	public function handle()
	{
		$this->language->add_lang('topic_preview_acp', 'vse/topicpreview');

		$form_key = 'acp_topic_preview';
		add_form_key($form_key);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$this->settings->set_settings();

			trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$this->template->assign_vars($this->settings->display_settings($this->u_action));
	}

	/**
	 * @inheritdoc
	 */
	public function set_u_action($u_action)
	{
		$this->u_action = $u_action;
		return $this;
	}
}
