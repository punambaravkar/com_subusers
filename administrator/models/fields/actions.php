<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldActions extends JFormField {

	protected $type = 'Actions';

	public function getInput()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('Distinct client');
		$query->from($db->quoteName('#__tjsu_actions'));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$clients = $db->loadColumn();
		$jinput = JFactory::getApplication()->input;
		$role_id = $jinput->get('id', '', 'INT');

		if ($role_id)
		{
			$actions_of_role = self::getActionsByRole($role_id);
		}else
		{
			$actions_of_role = array();
		}
		//print_r($actions_of_role);

		$list_html = "<select multiple name='role_actions[]'>";
		$actions_of_role = array_map('trim', $actions_of_role);

		foreach ($clients as $client)
		{
			$list_html .= "<optgroup label='" . $client . "'>";

			$actions = self::getActions($client);

			foreach ($actions as $action)
			{
				$action->title = preg_replace('/\s+/', '',$action->title);

				if (in_array($action->title, $actions_of_role))
				{
					$list_html .= "<option value={$action->title} selected='selected'> {$action->title} </option>";
				}else
				{
					$list_html .= "<option value={$action->title}> {$action->title} </option>";
				}
			}

			$list_html .= "</optgroup>";
		}

		$list_html .= "</select>";

		return $list_html;
	}

	public function getActions($client)
	{
		if (!empty($client))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, CONCAT(client,".", name) AS title');
			$query->from($db->quoteName('#__tjsu_actions'));
			$query->where($db->quoteName('client')." = ".$db->quote($client));

			$db->setQuery($query);
			$actions = $db->loadObjectList();
			return $actions;
		}
	}

	public function getActionsByRole($role_id)
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
}
