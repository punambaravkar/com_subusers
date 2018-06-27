<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Subusers model.
 *
 * @since  1.6
 */
class SubusersModelRole extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_SUBUSERS';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Role', $prefix = 'SubusersTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_subusers.role', 'role',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_subusers.edit.role.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		// $item->actions = self::getActions($item->id);

		return $item;
	}

	public function getActions($role_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('action');
		$query->from($db->quoteName('#__tjsu_role_action_map'));
		$query->where($db->quoteName('role_id')." = ".$db->quote($role_id));

		$db->setQuery($query);
		$actions = $db->loadColumn();
		return $actions;
	}

	public function save($data)
	{
		// Initialise variables.
		$userId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('user.id');
		$user = JFactory::getUser();

		$table_one = $this->getTable('role', 'SubusersTable', array());

		// Bind the data.
		if (!$table_one->bind($data))
		{
			$this->setError(JText::sprintf('USERS PROFILE BIND FAILED', $user->getError()));
			return false;
		}

		// Store the data.
		if (!$table_one->save($data))
		{
			$this->setError($user->getError());
			return false;
		}

		//The fast way
		if (! $data['id'])
		{
			$db = $table_one->getDBO();
			$role_id = $db->insertid();
		}else
		{
			$role_id = $data['id'];
		}

		$jinput = JFactory::getApplication()->input;
		$role_actions = $jinput->get('role_actions', array(), 'ARRAY');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// delete all custom keys for role_id
		$conditions = array($db->quoteName('role_id') . ' = ' . $role_id);
		$query->delete($db->quoteName('#__tjsu_role_action_map'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();

		foreach ($role_actions as $role_action)
		{
			// Create and populate an object.
			$action_map = new stdClass();
			$action_map->user_id = null;
			$action_map->role_id = $role_id;
			$action_map->action = $role_action;

			// Insert the object into the user #__tjsu_role_action_map table.
			JFactory::getDbo()->insertObject('#__tjsu_role_action_map', $action_map);
		}


		$task = $jinput->get('task', '', 'STRING');

		if ($task === "apply")
		{
			$app = JFactory::getApplication();
			$url = JRoute::_('index.php?option=com_subusers&view=role&layout=edit&id=' . (int)$role_id, false);
			$app->redirect($url, JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
		}

		// Set the error to empty and return true.
		$this->setError('');
		return true;
	}

	/**
	 * Method to duplicate an Role
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = JFactory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_subusers'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new Exception($table->getError());
				}


				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tjsu_roles');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
