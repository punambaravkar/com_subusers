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

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

use Joomla\Utilities\ArrayHelper;
/**
 * Subusers model.
 *
 * @since  1.6
 */
class SubusersModelOrganizationForm extends JModelForm
{
	private $item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since  1.6
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
	 * Method to get an ojbect.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return Object|boolean Object on success, false on failure.
	 *
	 * @throws Exception
	 */
	public function &getData($id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('organization.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table !== false && $table->load($id))
			{
				$user = JFactory::getUser();
				$id   = $table->id;
				$canEdit = $user->authorise('core.edit', 'com_subusers') || $user->authorise('core.create', 'com_subusers');

				if (!$canEdit && $user->authorise('core.edit.own', 'com_subusers'))
				{
					$canEdit = $user->id == $table->created_by;
				}

				if (!$canEdit)
				{
					throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
				}

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->item = ArrayHelper::toObject($properties, 'JObject');
			}
		}

		return $this->item;
	}

	/**
	 * Method to get the table
	 *
	 * @param   string  $type    Name of the JTable class
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $config  Optional configuration array for JTable object
	 *
	 * @return  JTable|boolean JTable if found, boolean false on failure
	 */
	public function getTable($type = 'Organization', $prefix = 'SubusersTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_subusers/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get an item by alias
	 *
	 * @param   string  $alias  Alias string
	 *
	 * @return int Element id
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
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_subusers.organization', 'organizationform', array(
			'control'   => 'jform',
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
	 * @return    mixed    The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_subusers.edit.organization.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function save($data)
	{
		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('organization.id');
		$state = (!empty($data['state'])) ? 1 : 0;
		$user  = JFactory::getUser();

		// Added to fetch data of organsiation form
		$input = JFactory::getApplication()->input;
		$formData = new JRegistry($input->get('jform', '', 'array'));

		if ($id)
		{
			// Check the user can edit this item
			$authorised = $user->authorise('core.edit', 'com_subusers') || $authorised = $user->authorise('core.edit.own', 'com_subusers');

			if ($user->authorise('core.edit.state', 'com_subusers') !== true && $state == 1)
			{
				// The user cannot edit the state of the item.
				$data['state'] = 0;
			}
		}
		else
		{
			// Check the user can create new items in this section
			$authorised = $user->authorise('core.create', 'com_subusers');

			if ($user->authorise('core.edit.state', 'com_subusers') !== true && $state == 1)
			{
				// The user cannot edit the state of the item.
				$data['state'] = 0;
			}
		}

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		// Insert daat in organisation table
		$table = $this->getTable();

		if ($id)
		{
			if ($table->save($data) === true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ($table->save($data) === true)
			{
				if ($table->id)
				{
					$userdata = new stdClass;
					$userdata->user_id = $formData->get('userid', 0);
					$userdata->client = 'com_subusers.organisation';
					$userdata->client_id = $table->id;
					$userdata->role_id = 1;
					$userdata->created_by = $user->id;
					$userdata->ordering = '2';
					$userdata->state = '1';

					$result = JFactory::getDBO()->insertObject('#__tjsu_users', $userdata);

					$params	 = JComponentHelper::getParams('com_subusers');
					$maincatid = $params->get('announementcategory', '');

					// Set the extension. For content categories, use 'com_content'
					$extension = 'com_content';

					// Set the title for the category
					$title     = $formData->get('name');

					// Type the description, this is also the 'body'. HTML allowed here.
					$desc      = '';

					// Set the parent category. 1 is the root item.
					$parent_id = $maincatid;

					// JTableCategory is autoloaded in J! 3.0, so...
					if (version_compare(JVERSION, '3.0', 'lt'))
					{
						JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
					}

					// Initialize a new category
					$category = JTable::getInstance('Category');
					$category->extension = $extension;
					$category->title = $title;
					$category->description = $desc;
					$category->published = 1;
					$category->access = 1;
					$category->params = '{"target":"","image":""}';
					$category->metadata = '{"page_title":"","author":"","robots":""}';
					$category->language = '*';

					// Set the location in the tree
					$category->setLocation($parent_id, 'last-child');

					// Check to make sure our data is valid
					if (!$category->check())
					{
						JError::raiseNotice(500, $category->getError());

						return false;
					}

					// Now store the category
					if (!$category->store(true))
					{
						JError::raiseNotice(500, $category->getError());

						return false;
					}

					// Build the path for our category
					$category->rebuildPath($category->id);

					// Check if data is inserted correctly in tjsu_users table
					if ($result)
					{
						// Creat easysocial group
						jimport('techjoomla.jsocial.jsocial');
						jimport('techjoomla.jsocial.easysocial');

						$esdata = array();
						$esdata['title'] = $formData->get('name', 'string');
						$esdata['description'] = $formData->get('description', 'string');
						$esdata['uid'] = $formData->get('userid', 0);

						/* Easysocial group type -
						 1. Open Group
						 2. Closed Group
						 3. Invite only groupInvite only */
						$esdata['type'] = '3';

						$catid = array();
						$params	 = JComponentHelper::getParams('com_subusers');
						$catid['catId'] = $params->get('escategory', '');

						// Create object
						$jsSocialEasysocialObj	=	new JSocialEasysocial;
						$groupid = $jsSocialEasysocialObj->createGroup($esdata, $catid);

						// @TODO with easy social
						$member = $formData->get('userid', 0);

						if ($member)
						{
							$memberdata = new stdClass;
							$memberdata->cluster_id = $groupid;
							$memberdata->uid = $member;
							$memberdata->type = 'user';
							$memberdata->created = 'user';
							$memberdata->state = '1';
							$memberdata->owner = '1';
							$memberdata->admin = '1';
							$memberdata->invited_by = '0';
							$memberdata->admin = '1';
							$memberdata->reminder_sent = $category->id;

							// Insert data in easysocial cluster table table
							$grpMember = JFactory::getDBO()->insertObject('#__social_clusters_nodes', $memberdata);
						}

						// Check if data is inserted correctly in tjsu_users table
						if ($groupid)
						{
							$groupdata = new stdClass;
							$groupdata->grp_id = $groupid;
							$groupdata->client_id = $table->id;
							$groupdata->category_id = $category->id;

							// Insert data in easysocial xref table
							$grpid = JFactory::getDBO()->insertObject('#__es_group_xref', $groupdata);

							if ($grpid)
							{
								return true;
							}
							else
							{
								return false;
							}
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Method to delete data
	 *
	 * @param   array  $data  Data to be deleted
	 *
	 * @return bool|int If success returns the id of the deleted item, if not false
	 *
	 * @throws Exception
	 */
	public function delete($data)
	{
		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('organization.id');

		if (JFactory::getUser()->authorise('core.delete', 'com_subusers') !== true)
		{
			throw new Exception(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$table = $this->getTable();

		if ($table->delete($data['id']) === true)
		{
			return $id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check if data can be saved
	 *
	 * @return bool
	 */
	public function getCanSave()
	{
		$table = $this->getTable();

		return $table !== false;
	}

	/**
	 * To check if entered org id is already exists or not
	 *
	 * @param   string  $orgId  orgId
	 *
	 * @return  boolean
	 *
	 * @since    1.6
	 */
	public function validateOrgId($orgId)
	{
		$query	= "SELECT id FROM #__tjsu_organizations WHERE id = '" . $orgId . "'";
		$this->_db->setQuery($query);
		$orgIdExists = $this->_db->loadResult();
		$nameid = "";

		if ($orgIdExists)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * To check if entered is orgemail
	 *
	 * @param   string  $orgEmail  Eamilid
	 *
	 * @return  boolean
	 *
	 * @since    1.6
	 */
	public function validateOrgEmail($orgEmail)
	{
		$query	= "SELECT id FROM #__tjsu_organizations WHERE email = '" . $orgEmail . "'";
		$this->_db->setQuery($query);
		$emailExists = $this->_db->loadResult();
		$nameid = "";

		if ($emailExists)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
