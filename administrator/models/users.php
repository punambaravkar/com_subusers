<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
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
				'id', 'a.`id`',
				'user_id', 'a.`user_id`',
				'client', 'a.`client`',
				'client_content_id', 'a.`client_content_id`',
				'role_id', 'a.`role_id`',
				'created_by', 'a.`created_by`',
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the filter state.
		$curriculum = $app->getUserStateFromRequest($this->context . '.filter.curriculum', 'filter_curriculum', 1, 'string');
		$this->setState('filter.curriculum', $curriculum);
		
		// Load the filter role.
		$roles = $app->getUserStateFromRequest($this->context . '.filter.roles', 'filter_roles');
		$this->setState('filter.roles', $roles);
        
		// Filtering user_id
		$this->setState('filter.user_id', $app->getUserStateFromRequest($this->context.'.filter.user_id', 'filter_user_id', '', 'string'));

		// Load the parameters.
		$params = JComponentHelper::getParams('com_subusers');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__tjsu_users` AS a');

		// Join over the user field 'user_id'
		$query->select('`a`.user_id AS `user_original_id`,`user_id`.name AS `user_id`');
		$query->join('LEFT', '#__users AS `user_id` ON `user_id`.id = a.`user_id`');
        
        //Get the roles name
        $query->select('`tjr`.name AS `role_name`');
		$query->join('INNER', '#__tjsu_roles AS `tjr` ON `a`.role_id = tjr.`id`');
		
		$query->select('`ceu`.state AS `ceu_state`');
		$query->join('INNER', $db->quoteName('#__tjlms_curriculum_enrolled_users', 'ceu') . ' ON (' .
		$db->quoteName('ceu.user_id') . ' = ' . $db->quoteName('user_id.id') . ') AND '.$db->quoteName('ceu.state') . '= 1 AND '.$db->quoteName('ceu.curriculum_id') .'= '.$this->getState('filter.curriculum'));
		
		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');
        
        $query->where($db->quoteName('a.client') . " = " . $db->quote("com_tjlms.curriculum"));
        
        // Filter by search in title
		$search = $this->getState('filter.search');
		$curriculum_id = $this->getState('filter.curriculum');
		$role_id = $this->getState('filter.roles');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.`id` LIKE ' . $search . '  OR  a.`user_id` LIKE ' . $search . '  OR  a.`client_content_id` LIKE ' . $search . ' )');
			}
		}
		if (!empty($curriculum_id))
		{
			$query->where('a.client_content_id = ' . (int) $curriculum_id);
		}
		if (!empty($role_id))
		{
			$query->where('a.role_id = ' . (int) $role_id);
		}


		//Filtering user_id
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
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
}
