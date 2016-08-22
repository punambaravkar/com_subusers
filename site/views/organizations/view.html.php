<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    Subusers
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

JPATH_SITE . 'components/com_subusers/helpers/subusers.php';

/**
 * View class for a list of Subusers.
 *
 * @since  1.6
 */
class SubusersViewOrganizations extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$user   = JFactory::getUser();
		$layout = $app->input->get('layout', 'default');

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params     = $app->getParams('com_subusers');

		$user = JFactory::getUser();
		$uid = $user->id;

		// Import helper file
		require_once JPATH_COMPONENT . '/helpers/acl.php';

		// Check logged in user is admin of organization
		$this->isAdmin = SubusersAclHelper::isOrganisationAdmin($uid);

		// Check logged in user is superuser or not
		$this->isSuperAdmin = SubusersAclHelper::isSuperAdmin($uid);

		if ($layout == 'default')
		{
			if (!$user->id)
			{
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_subusers&view=organizations&layout=pin&Itemid=");
			}
			elseif(!$this->isSuperAdmin)
			{
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_subusers&view=organizations&layout=pin");
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SUBUSERS_DEFAULT_PAGE_TITLE'));
		}

		if (empty($title))
		{
			$title =  JText::_('COM_SUBUSERS_ORGANIZATION_LIST_PAGE_TITLE');
		}

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
