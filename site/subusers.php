<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('SubusersFrontendHelper', JPATH_COMPONENT . '/helpers/subusers.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Subusers');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
