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
class SubusersAclHelper
{
	/**
	 * Check if logged in user is Organsiation admin
	 *
	 * @param   string  $uid  user id
	 *
	 * @return role
	 */

	public static function isOrganisationAdmin($uid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('client_id'));
		$query->select($db->quoteName('role_id'));
		$query->from($db->quoteName('#__tjsu_users'));
		$query->where($db->quoteName('role_id') . " = " . $db->quote('1'));
		$query->where($db->quoteName('user_id') . " = " . $db->quote($uid));

		$db->setQuery($query);

		$client = $db->loadObject();

		// Get organisation name if logged user is admin
		if ($client)
		{
			$query->select($db->quoteName('org.name'));
			$query->from($db->quoteName('#__tjsu_organizations', 'org'));
			$query->where($db->quoteName('org.id') . " = " . $db->quote($client->client_id));

			$db->setQuery($query);

			$clientname = $db->loadObject();

			if ($clientname)
			{
				return $clientname;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check if logged in user is Super admin
	 *
	 * @return user
	 */

	public static function isSuperAdmin()
	{
		$user = JFactory::getUser();
		$groups = $user->groups;

		if (in_array(8, $groups) || in_array(21, $groups))
		{
			return true;
		}
	}

	/**
	 * Check if logged in user is org member
	 *
	 * @param   string  $uid  user id
	 *
	 * @return userid
	 */

	public static function isOrgMember($uid)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('client_id'));
		$query->from($db->quoteName('#__tjsu_users'));
		$query->where($db->quoteName('user_id') . " = " . $uid);
		$db->setQuery($query);
		$user_id = $db->loadResult();

		return $user_id;
	}

	/**
	 * Get list of all organisation
	 *
	 * @return users
	 */
	public static function getAllOrganizations()
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('id as value,name as text');
		$query->from('#__tjsu_organizations');
		$query->order('name ASC');
		$db->setQuery($query);
		$orglist = $db->loadAssocList();
		$options = array();

		$options[''] = JText::_('COM_SUBUSERS_ALL_USERS');

		foreach ($orglist as $k => $val)
		{
			$options[$val['value']] = $val['text'];
		}

		return $options;
	}

	/**
	 * Get Easy social Group id and title
	 *
	 * @param   string  $orgId  org id
	 *
	 * @return esIdAndName
	 */
	public static function getEasySocialGroup($orgId)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('a.id,a.title');
		$query->from('`#__social_clusters` AS a');
		$query->join('', $db->quoteName('#__es_group_xref', 'b') . 'ON' . $db->quoteName('b.grp_id') . '=' . $db->quoteName('a.id'));
		$query->where($db->quoteName('client_id') . " = " . $orgId);

		$db->setQuery($query);
		$esIdAndName = $db->loadObject();

		return $esIdAndName;
	}

	/**
	 * Get partner admin user id
	 *
	 * @param   string  $uid    uid
	 *
	 * @param   string  $orgId  orgid
	 *
	 * @return userId
	 */
	public static function isThisPartnerAdmin($uid, $orgId)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('user_id'));
		$query->from($db->quoteName('#__tjsu_users'));
		$query->where($db->quoteName('user_id') . " = " . $uid);
		$query->where($db->quoteName('client_id') . " = " . $orgId);
		$query->where($db->quoteName('role_id') . " = " . $db->quote('1'));
		$db->setQuery($query);
		$userId = $db->loadResult();

		return $userId;
	}

	/**
	 * Get partner admin name
	 *
	 * @param   string  $orgId  orgId
	 *
	 * @return userId
	 */
	public static function partnerAdminName($orgId)
	{
		// Check org id is not null or not empty
		if ($orgId !== null || $orgId !== "")
		{
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);
			$subQuery = $db->getQuery(true);

			if ($subQuery)
			{
				$subQuery->select($db->quoteName('userid'))
					->from($db->quoteName('#__tjsu_organizations'))
					->where($db->quoteName('id') . " = " . $db->quote($orgId));
				$db->setQuery($subQuery);
				$userId = $db->loadResult();
			}

			$query->select('id,name');
			$query->from($db->quoteName('#__users'));
			$query->where($db->quoteName('id') . " = " . $db->quote($userId));
			$db->setQuery($query);
			$userName = $db->loadObject();

			return $userName;
		}
		else
		{
			return false;
		}
	}
}
