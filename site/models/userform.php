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

class SubusersModelUserForm extends JModelForm
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
			$id = JFactory::getApplication()->getUserState('com_subusers.edit.user.id');
		}
		else
		{
			$id = JFactory::getApplication()->input->get('id');
			JFactory::getApplication()->setUserState('com_subusers.edit.user.id', $id);
		}

		$this->setState('user.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('user.id', $params_array['item_id']);
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
				$id = $this->getState('user.id');
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
	public function getTable($type = 'User', $prefix = 'SubusersTable', $config = array())
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
		$id = (!empty($id)) ? $id : (int) $this->getState('user.id');

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
		$id = (!empty($id)) ? $id : (int) $this->getState('user.id');

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
		$form = $this->loadForm('com_subusers.user', 'userform', array(
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
		$data = JFactory::getApplication()->getUserState('com_subusers.edit.user.data', array());

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
		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');
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

		// Joomla user entry

		$db = JFactory::getDBO();
		$query = "SELECT id FROM #__users WHERE email = '" . $formData->get('email', '', 'STRING') . "'
		or username = '" . $formData->get('username', '', 'STRING') . "'";
		$this->_db->setQuery($query);
		$userexist = $this->_db->loadResult();
		$userid = "";
		$randomPassword = "";

		if (!$userexist)
		{
			// $pass = $formData->get('pass', null, null);
			$password = $formData->get('password', null, null);

			// Generate the random password & create a new user
			if (!$password)
			{
				$randomPassword = $this->rand_str(6);
				$userid  = $this->createnewuser($data, $randomPassword);
			}
			else
			{
				$randomPassword = $password;
				$userid  = $this->createnewuser($data, $randomPassword);
			}
		}
		else
		{
			$application = JFactory::getApplication();
			$application->enqueueMessage(JText::_('COM_SUBUSERS_REGISTRATION_USER_EXIST'), 'error');

			$mainframe = JFactory::getApplication();
			$mainframe->redirect('index.php?option=com_subusers&view=userform&layout=edit&Itemid=207');

			return false;
		}

		// Enter data into the tjsu_user table
		$table = $this->getTable();

		if ($userid)
		{
			$userdata = new stdClass;
			$userdata->user_id = $userid;
			$userdata->client = 'com_subusers.organisation';
			$userdata->client_id = $formData->get('client_id', '', 'INT');
			$userdata->role_id = $formData->get('role_id', '', 'INT');
			$userdata->created_by = $user->id;
			$userdata->ordering = '2';
			$userdata->state = '1';

			// Insert the object into the user profile table.
			$result = JFactory::getDBO()->insertObject('#__tjsu_users', $userdata);

			if ($result)
			{
				// Add user easysocial organisation group
				jimport('techjoomla.jsocial.jsocial');
				jimport('techjoomla.jsocial.easysocial');

				$clientId = $formData->get('client_id', 'string');

				// Users helper call - To get group id of organisation
				$usershelperPath = JPATH_SITE . '/components/com_subusers/helpers/users.php';

				if (!class_exists('SubusersHelper'))
				{
					// Require_once $path;
					JLoader::register('SubusersHelper', $usershelperPath);
					JLoader::load('SubusersHelper');
				}

				$usershelper = new SubusersHelper;
				$grpId = $usershelper->getGroupId($clientId);

				$groupId = $grpId->grp_id;
				$memberID = JFactory::getUser($userid);

				// Create object
				$jsSocialEasysocialObj	=	new JSocialEasysocial;
				$addmember  = $jsSocialEasysocialObj->addMemberToGroup($groupId, $memberID);

				if ($addmember)
				{
					$this->SendMailNewUser($data, $randomPassword);
					/**
					 * One partner have multiple admin's but one user cannot be admin of multiple partners
					 * if existing partner admin add new user as admin in his partner group, then newly added
					 * user should be admin of easy social group.
					 */

					if ($userdata->role_id == '1')
					{
						// Get a db connection.
						$db = JFactory::getDbo();

						// Create object to update record
						$query 		= $db->getQuery(true);
						$admin = $userdata->role_id;
						$newUser = $userdata->user_id;
						$fields = ($db->quoteName('admin') . ' = ' . $db->quote($admin));
						$conditions = array($db->quoteName('uid') . ' = ' . $db->quote($newUser),
											$db->quoteName('cluster_id') . ' = ' . $db->quote($groupId)
									);
						$query->update($db->quoteName('#__social_clusters_nodes'))->set($fields)->where($conditions);
						$db->setQuery($query);
						$result = $db->execute();
					}
					else
					{
						return false;
					}

					return $result;
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
		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('user_id'));
		$query->from('#__tjsu_users');
		$query->where($db->quoteName('id') . " = " . $id);
		$db->setQuery($query);
		$user_id = $db->loadResult();

		if ($user_id)
		{
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__social_clusters_nodes'));
			$query->where($db->quoteName('uid') . " = " . $user_id);
			$db->setQuery($query);
			$cluster = $db->execute();
		}

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
	 * Method to create the new user.
	 *
	 * @param   array   $data            The form data
	 *
	 * @param   vachar  $randomPassword  randam password
	 *
	 * @return userid
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function createnewuser($data, $randomPassword)
	{
		jimport('joomla.user.helper');
		jimport('joomla.application.component.helper');

		$authorize = JFactory::getACL();
		$user = clone JFactory::getUser();

		// Added to fetch data of organsiation form
		$input = JFactory::getApplication()->input;
		$formData = new JRegistry($input->get('jform', '', 'array'));

		$user->set('id', '');
		$user->set('usertype', 'Registered');
		$params = JComponentHelper::getParams('com_subusers');

		// Set user group
		$default_usergroup = $params->get('guest_usergroup', 2);
		$user->set('groups', array($default_usergroup));
		$user->set('username', $formData->get('username', '', 'STRING'));
		$password = $formData->get('password', '', 'STRING');

		// Check if password is entered
		if ($password)
		{
			$user->set('password', md5($password));
		}
		else
		{
			$user->set('password', md5($randomPassword));
		}

		$user->set('name', $formData->get('name', '', 'STRING'));
		$user->set('email', $formData->get('email', '', 'STRING'));

		$message = '';

		$date = JFactory::getDate();
		$user->set('registerDate', $date->toSql());

		// True on success, false otherwise
		if (!$user->save())
		{
			echo $message = JText::_('COM_SUBUSERS_REGISTRATION_USER_EXIST') . $user->getError();

			return false;
		}
		else
		{
			$message = JText::sprintf('COM_SUBUSERS_REGISTRATION_MESSAGE1', $user->username);
		}

		return $user->id;
	}

	/**
	 * Function to send mail to registered user
	 *
	 * @param   array   $data            The form data
	 *
	 * @param   vachar  $randomPassword  randam password
	 *
	 * @return userid
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function SendMailNewUser($data, $randomPassword)
	{
		$app = JFactory::getApplication();

		// Added to fetch data of organsiation form
		$input = JFactory::getApplication()->input;
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$sitename = $app->getCfg('sitename');
		$email = $formData->get('email', '', 'STRING');
		$subject = JText::_('COM_SUBUSERS_REGISTRATION_SUBJECT');
		$find1 = array('{sitename}');
		$replace1 = array($sitename);
		$subject = str_replace($find1, $replace1, $subject);
		$message = '';
		$message = JText::_('COM_SUBUSERS_REGISTRATION_USER');
		$find = array('{firstname}', '{sitename}', '{register_url}', '{username}', '{password}');
		$replace = array($formData->get('name', '', 'STRING'), $sitename, JUri::root(), $formData->get('username', '', 'STRING'), $randomPassword);
		$message = str_replace($find, $replace, $message);

		JFactory::getMailer()->sendMail($mailfrom, $fromname, $email, $subject, $message);

		return true;
	}

	/**
	 * Create a random character generator for password
	 *
	 * @param   string  $length  Default length
	 *
	 * @param   string  $chars   Default character string
	 *
	 * @return  string
	 *
	 * @since    1.6
	 */
	public function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		// Length of character list
		$chars_length = (strlen($chars) - 1);

		// Start our string
		$string = $chars{rand(0, $chars_length)};

		// Generate random string
		for ($i = 1; $i < $length; $i = strlen($string))
		{
			// Grab a random character from our list
			$r = $chars{rand(0, $chars_length)};

			// Make sure the same two characters don't appear next to each other
			if ($r != $string{$i - 1})
			{
				$string .= $r;
			}
		}
		// Return the string
		return $string;
	}

	/**
	 * To check if entered is valid email address
	 *
	 * @param   string  $emailId  Eamilid
	 *
	 * @return  boolean
	 *
	 * @since    1.6
	 */
	public function validateEmail($emailId)
	{
		$query = "SELECT id FROM #__users WHERE email = '" . $emailId . "'";
		$this->_db->setQuery($query);
		$userexist = $this->_db->loadResult();
		$userid = "";

		if ($userexist)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * To check if entered is valid user name
	 *
	 * @param   string  $userName  Eamilid
	 *
	 * @return  boolean
	 *
	 * @since    1.6
	 */
	public function validateUserName($userName)
	{
		$query	= "SELECT id FROM #__users WHERE username = '" . $userName . "'";
		$this->_db->setQuery($query);
		$userexists = $this->_db->loadResult();
		$userid = "";

		if ($userexists)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
