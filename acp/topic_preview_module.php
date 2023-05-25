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

class topic_preview_module
{
	/**
	 * @var string
	 */
	public $page_title;

	/**
	 * @var string
	 */
	public $tpl_name;

	/**
	 * @var string
	 */
	public $u_action;

	/**
	 * Main ACP module
	 *
	 * @throws \Exception
	 */
	public function main()
	{
		global $phpbb_container;

		$this->tpl_name = 'acp_topic_preview';
		$this->page_title = 'TOPIC_PREVIEW';

		$phpbb_container->get('vse.topicpreview.acp.controller')
			->set_u_action($this->u_action)
			->handle();
	}
}
