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

interface acp_controller_interface
{
	/**
	 * Controller handler. Call this method from the ACP module.
	 */
	public function handle();

	/**
	 * Set the u_action variable from the form/module
	 *
	 * @param string $u_action
	 *
	 * @return acp_controller $this
	 */
	public function set_u_action($u_action);
}
