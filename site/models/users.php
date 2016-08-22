<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Subusers records.
 *
 * @since  1.6
 */
class SubusersModelUsers extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'user_id', 'a.user_id',
				'client', 'a.client',
				'client_id', 'a.client_id',
				'role_id', 'a.role_id',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'state', 'a.state',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0);
		$this->setState('list.start', $limitstart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');

		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');
			$list['ordering'] = $app->input->get('filter_order');
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');

		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');
			$list['direction'] = $app->input->get('filter_order_Dir');
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');

		if (empty($list['ordering']))
		{
			$list['ordering'] = 'ordering';
		}

		if (empty($list['direction']))
		{
			$list['direction'] = 'asc';
		}

		if (isset($list['ordering']))
		{
			$this->setState('list.ordering', $list['ordering']);
		}

		if (isset($list['direction']))
		{
			$this->setState('list.direction', $list['direction']);
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// If Super user is logged then show organization wise user
		$jinput = JFactory::getApplication()->input;
		$org_id = $jinput->get('orgid', '', 'INT');

		require_once JPATH_COMPONENT . '/helpers/acl.php';

		// If logged user is Super User or Admin
		$user = JFactory::getUser();
		$uid = $user->id;

		$this->isAdmin		= SubusersAclHelper::isOrganisationAdmin($uid);
		$this->isOrgMember	= SubusersAclHelper::isOrgMember($uid);
		$this->isSuperAdmin	= SubusersAclHelper::isSuperAdmin($uid);

		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__tjsu_users` AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS name,uc.email,role.name,org.name as orgname');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS uc ON uc.id = a.user_id');

		// Join over the organization table to fetch organization table.
		$query->join('LEFT', '#__tjsu_organizations AS org ON org.id = a.client_id');

		// Join over the created by field 'role id'
		$query->join('LEFT', '#__tjsu_roles AS role ON role.id = a.role_id');

		if (!JFactory::getUser()->authorise('core.edit.state', 'com_subusers'))
		{
			$query->where('a.state = 1');
		}

		if ($org_id)
		{
			$query->where('a.client_id = ' . $org_id);
		}
		// If logged user is super user
		elseif (isset($this->isSuperAdmin))
		{
			// Filter for super user to view all user partner wise
			$mainframe      = JFactory::getApplication();
			$orgsearch 		= $mainframe->getUserStateFromRequest('.filter.search', 'filter_org_search');

			// If filter set
			if ($orgsearch)
			{
				// Fetch users from tjsu_users table by client_id (filter) wise
				$query->where('a.client_id = ' . $orgsearch);
			}
			// Fetch all publish/unpublish users
			$query->where('a.state IN (1,0) ');
		}
		// If logged in user is admin of any partner
		elseif (isset($this->isAdmin->client_id) && empty($this->isSuperAdmin))
		{
			// Find client_id of admin user
			$client_id = $this->isAdmin->client_id;

			// Fetch only respected client_id/partner users
			$query->where('a.client_id = ' . $client_id);
		}
		else
		{
			// If logged in user is member of any partner
			if ($this->isOrgMember)
			{
				// Fetch their partner users only
				$query->where('a.client_id = ' . $this->isOrgMember);
			}
			else
			{
				// If logged in user none of above then redirect to
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_easysocial&view=groups");
			}
		}

		// Filter by search in organisation name
		$mainframe   	= JFactory::getApplication();
		$search 		= $mainframe->getUserStateFromRequest('.filter.search', 'filter_user_search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( uc. name OR uc. email LIKE ' . $search . ' )');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_SUBUSERS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);

		return (date_create($date)) ? JFactory::getDate($date)->format("Y-m-d") : null;
	}
}
