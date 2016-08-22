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
class SubusersHelper
{
	/**
	 * Check if logged in user is Organsiation admin
	 *
	 * @param   string  $clientId  client id
	 *
	 * @return role
	 */

	public static function getGroupId($clientId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('grp_id'));
		$query->from($db->quoteName('#__es_group_xref'));
		$query->where($db->quoteName('client_id') . " = " . $db->quote($clientId));

		$db->setQuery($query);

		$groupId = $db->loadObject();

		// Get groupId
		if ($groupId)
		{
			return $groupId;
		}
		else
		{
			return false;
		}
	}
}
