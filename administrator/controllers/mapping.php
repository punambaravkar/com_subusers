<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Mapping controller class.
 *
 * @since  1.6
 */
class SubusersControllerMapping extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'mappings';
		parent::__construct();
	}
}
