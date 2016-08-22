<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class SubusersFrontendHelper
 *
 * @since  1.6
 */
class SubusersFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_subusers/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_subusers/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'SubusersModel');
		}

		return $model;
	}

	/**
	 * Add menu
	 *
	 * @param   string  $vName  Model name
	 *
	 * @return null|object
	 */

	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			'test',
			'index.php?option=com_subusers&view=users',
			$vName == 'users'
		);
	}

	/**
	 * Get language constant
	 *
	 * @return String
	 */

	public static function getLanguageConstant()
	{
		// JS file upload
		JText::script('COM_SUBUSERS_EMAIL_ALREADY_EXISTS');
		JText::script('COM_SUBUSERS_PASSWORD_DOES_NOT_MATCH');
		JText::script('COM_SUBUSERS_ERR_MSG_JS_FILE_TYPES');
		JText::script('COM_SUBUSERS_ERR_MSG_JS_FILE_SIZE');
		JText::script('COM_SUBUSERS_USER_NAME_ALREADY_EXISTS');

		// Organization form
		JText::script('COM_SUBUSERS_ORGANIZATION_ID_ALREADY_EXISTS');
		JText::script('COM_SUBUSERS_ORGANIZATION_EMAIL_ALREADY_EXISTS');
		JText::script('COM_SUBUSER_MSG_ERR');
	}
}
