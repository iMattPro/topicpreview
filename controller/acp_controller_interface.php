<?php
/*
 * This file is part of myCloud.
 *
 * (c) 2016 Matt Friedman
 *
 * This work is licensed under a Creative Commons
 * Attribution-NonCommercial-NoDerivs 3.0 Unported License.
 * http://creativecommons.org/licenses/by-nc-nd/3.0/
 */
namespace vse\topicpreview\controller;


/**
 * Class acp_controller
 *
 * @package vse\topicpreview\controller
 */
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
	 * @return acp_controller $this
	 */
	public function set_u_action($u_action);
}
