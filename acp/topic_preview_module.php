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
	/** @var string */
	public $u_action;

	/**
	 * Main ACP module
	 *
	 * @param int    $id
	 * @param string $mode
	 * @return null
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		$user = $phpbb_container->get('user');
		$this->tpl_name   = 'acp_topic_preview';
		$this->page_title = $user->lang('TOPIC_PREVIEW');

		$phpbb_container->get('vse.topicpreview.acp.controller')
			->set_u_action($this->u_action)
			->handle();
	}
}
