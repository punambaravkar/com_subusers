<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';
jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use Joomla\Utilities\ArrayHelper;
/**
 * Subusers model.
 *
 * @since  1.6
 */
class SubusersModelOrganization extends JModelItem
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since    1.6
	 *
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('com_subusers');

		// Load state from the request userState on edit or from the passed variable on default
		if (JFactory::getApplication()->input->get('layout') == 'edit')
		{
			$id = JFactory::getApplication()->getUserState('com_subusers.edit.organization.id');
		}
		else
		{
			$id = JFactory::getApplication()->input->get('id');
			JFactory::getApplication()->setUserState('com_subusers.edit.organization.id', $id);
		}

		$this->setState('organization.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('organization.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('organization.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->_item = ArrayHelper::toObject($properties, 'JObject');
			}
		}

		return $this->_item;
	}

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string  $type    Name of the JTable class to get an instance of.
	 * @param   string  $prefix  Prefix for the table class name. Optional.
	 * @param   array   $config  Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = 'Organization', $prefix = 'SubusersTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_subusers/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string  $alias  Item alias
	 *
	 * @return  mixed
	 */
	public function getItemIdByAlias($alias)
	{
		$table = $this->getTable();
		$table->load(array('alias' => $alias));

		return $table->id;
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('organization.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('organization.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get the name of a category by id
	 *
	 * @param   int  $id  Category id
	 *
	 * @return  Object|null	Object if success, null in case of failure
	 */
	public function getCategoryName($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('title')
			->from('#__categories')
			->where('id = ' . $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Publish the element
	 *
	 * @param   int  $id     Item id
	 * @param   int  $state  Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{
		$table = $this->getTable();
		$table->load($id);
		$table->state = $state;

		return $table->store();
	}

	/**
	 * Method to delete an item
	 *
	 * @param   int  $id  Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();

		return $table->delete($id);
	}

	/**
	 * Check member
	 *
	 * @param   int  $user_id  Element id
	 *
	 * @return   check
	 */
	public function checkmember($user_id,$orgid)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__tjsu_users')
			->where('user_id = ' . $user_id)
			->where('client_id = ' . $orgid);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * check state
	 *
	 * @param   int  $user_id  Element id
	 *
	 * @return  bool
	 */
	public function checkmemberstate($user_id,$orgid)
	{
		$table = $this->getTable('user');
		$table->load(array('user_id' => $user_id,'client_id' => $orgid));
		$result = $table->state;

		return $result;
	}

	/**
	 * check state
	 *
	 * @param   int  $orgid      Element id
	 *
	 * @param   int  $oluser_id  Element id
	 *
	 * @return  bool
	 */
	public function applymembership($orgid,$oluser_id)
	{
		$table = $this->getTable('user');
		$table->load(array('user_id' => $oluser_id));
		$tjsuid = $table->id;
		$tjsuid = 0;

		if (!$tjsuid)
		{
			/*$table->client_id = $orgid;
			return $table->store();
			Create the array of new fields.*/

				$from = array('id' => '',
				'user_id' => $oluser_id,
				'client' => 'com_subusers.organisation',
				'client_id' => $orgid,
				'role_id' => '2',
				'state' => '0'
			);

			// Bind the array to the table object.
			$table->bind($from);

			$grpid = $this->getGrpId($orgid);
			$group	= FD::group($grpid);

			// Get the user's access as we want to limit the number of groups they can join
			$user = FD::user($oluser_id);
			$access = $user->getAccess();
			$total = $user->getTotalGroups();

			if ($access->exceeded('groups.join', $total))
			{
				$obj->success = 0;
				$obj->message = 'group joining limit exceeded';

				return $obj;
			}

			if (!$group->isMember($oluser_id))
			{
				// Create a member record for the group
				$members = $group->createMember($oluser_id);
			}

			return $table->store();

			return true;
		}

		return false;
	}

		/**
	  * Get $orgid
	  *
	  * @param   int  $orgid  Org id
	  *
	  * @return org id
	  */

	public function getGrpId($orgid)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create a new query object.
		$query
			->select('a.grp_id')
			->from('`#__es_group_xref` AS a');
		$query->where('a.client_id=' . $orgid);
		$db->setQuery($query);
		$grp_id = $db->loadResult();

		return $grp_id;
	}

	/**
	 * Get $orgid
	 *
	 * @param   int  $orgid      Org id
	 *
	 * @param   int  $oluser_id  oluser id
	 *
	 * @return org id
	 */

	public function checkIfOrgAdmin($orgid,$oluser_id)
	{
		$table = $this->getTable('user');
		$table->load(array('user_id' => $oluser_id,'client_id' => $orgid));

		return $table->role_id;
	}
}
