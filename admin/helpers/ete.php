<?php
/**
 * $Id: $
 * @copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license GNU/GPL, see LICENSE.php in the installation package
 * This file is part of Event Table Edit
 *
 * Event Table Edit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Event Table Edit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Event Table Edit. If not, see <http://www.gnu.org/licenses/>.
 */

// No direct access
defined('_JEXEC') or die;

class eteHelper {
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName) {
		JSubMenuHelper::addEntry(
			JText::_('COM_EVENTTABLEEDIT_SUBMENU_ETETABLES'),
			'index.php?option=com_eventtableedit&view=etetables',
			$vName == 'etetables'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_EVENTTABLEEDIT_SUBMENU_APPOINTMENTTABLES'),
			'index.php?option=com_eventtableedit&view=appointmenttables',
			$vName == 'appointmenttables'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_EVENTTABLEEDIT_SUBMENU_DROPDOWN'),
			'index.php?option=com_eventtableedit&view=dropdowns',
			$vName == 'dropdowns'
		);

		// Add only if user has sufficient rights
		$user = JFactory::getUser();
		if ($user->authorise('core.csv', 'com_eventtableedit')) {
			JSubMenuHelper::addEntry(
				JText::_('COM_EVENTTABLEEDIT_SUBMENU_CSVIMPORT'),
				'index.php?option=com_eventtableedit&view=csvimport',
				$vName == 'csvimport'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_EVENTTABLEEDIT_SUBMENU_CSVEXPORT'),
				'index.php?option=com_eventtableedit&view=csvexport',
				$vName == 'csvexport'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_EVENTTABLEEDIT_SUBMENU_XMLIMPORT'),
				'index.php?option=com_eventtableedit&view=xmlimport',
				$vName == 'xmlimport'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_EVENTTABLEEDIT_SUBMENU_XMLEXPORT'),
				'index.php?option=com_eventtableedit&view=xmlexport',
				$vName == 'xmlexport'
			);
		
		}
	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_eventtableedit';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete', 'core.csv'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
}
