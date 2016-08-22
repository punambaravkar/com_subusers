<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Subusers
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2015 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Organization controller class.
 *
 * @since  1.6
 */
class SubusersControllerOrganization extends SubusersController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	public function edit()
	{
		$app = JFactory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_subusers.edit.organization.id');
		$editId     = $app->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_subusers.edit.organization.id', $editId);

		// Get the model.
		$model = $this->getModel('Organization', 'SubusersModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId && $previousId !== $editId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_subusers&view=organizationform&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return    void
	 *
	 * @throws Exception
	 * @since    1.6
	 */
	public function publish()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Checking if the user can remove object
		$user = JFactory::getUser();

		if ($user->authorise('core.edit', 'com_subusers') || $user->authorise('core.edit.state', 'com_subusers'))
		{
			$model = $this->getModel('Organization', 'SubusersModel');

			// Get the user data.
			$id    = $app->input->getInt('id');
			$state = $app->input->getInt('state');

			// Attempt to save the data.
			$return = $model->publish($id, $state);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(JText::sprintf('Save failed: %s', $model->getError()), 'warning');
			}

			// Clear the profile id from the session.
			$app->setUserState('com_subusers.edit.organization.id', null);

			// Flush the data from the session.
			$app->setUserState('com_subusers.edit.organization.data', null);

			// Redirect to the list screen.
			$this->setMessage(JText::_('COM_SUBUSERS_ITEM_SAVED_SUCCESSFULLY'));
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getActive();

			if (!$item)
			{
				// If there isn't any menu item active, redirect to list view
				$this->setRedirect(JRoute::_('index.php?option=com_subusers&view=organizations', false));
			}
			else
			{
				$this->setRedirect(JRoute::_($item->link . $menuitemid, false));
			}
		}
		else
		{
			throw new Exception(500);
		}
	}

	/**
	 * Remove data
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function remove()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Checking if the user can remove object
		$user = JFactory::getUser();

		if ($user->authorise('core.delete', 'com_subusers'))
		{
			$model = $this->getModel('Organization', 'SubusersModel');

			// Get the user data.
			$id = $app->input->getInt('id', 0);

			// Attempt to save the data.
			$return = $model->delete($id);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
			}
			else
			{
				// Check in the profile.
				if ($return)
				{
					$model->checkin($return);
				}

				// Clear the profile id from the session.
				$app->setUserState('com_subusers.edit.organization.id', null);

				// Flush the data from the session.
				$app->setUserState('com_subusers.edit.organization.data', null);

				$this->setMessage(JText::_('COM_SUBUSERS_ITEM_DELETED_SUCCESSFULLY'));
			}

			// Redirect to the list screen.
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getActive();
			$this->setRedirect(JRoute::_($item->link, false));
		}
		else
		{
			throw new Exception(500);
		}
	}

	/**
	 * Apply for membership
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function applymembership()
	{
			$input = JFactory::getApplication()->input;
			$post = $input->post;
			$orgid = $post->get('orgid', '', 'INT');

			if ($orgid)
			{
				$oluser_id = JFactory::getUser()->id;
				$model = $this->getModel('organization');
				$check = $model->applymembership($orgid, $oluser_id);

				if ($check)
				{
					$msg = JText::_('COM_SUBUSERS_APPLY_SUCCESSFULY');
				}
				else
				{
					$msg = JText::_('COM_SUBUSERS_APPLY_FAILED');
				}

				$this->setRedirect(JRoute::_('index.php?option=com_subusers&view=organization&layout=details&id=' . $orgid, false), $msg);
			}
	}
}
