<?php
/**
 * $Id:$
 * @copyright (C) 2007 - 2018 Manuel Kaspar and Theophilix
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class com_eventtableeditInstallerScript
{
        function install($parent) 
        {
        		echo '<p>' . JText::_('COM_EVENTTABLEEDIT_POSTFLIGHT_INSTALL_TEXT') . '</p>';
                $parent->getParent()->setRedirectURL('index.php?option=com_eventtableedit');
        }
 
        function uninstall($parent) 
        {
			// Uninstall the _rows tables
			$db = JFactory::getDBO();
			$query = 'SELECT id FROM #__eventtableedit_details';
			$db->setQuery($query);
			$rows = $db->loadColumn();
	
			for ($a = 0; $a < count($rows); $a++) {
				$query = 'DROP TABLE IF EXISTS #__eventtableedit_rows_' . $rows[$a];
				$db->setQuery($query);
				$db->query();
			}

            echo '<p>' . JText::_('COM_EVENTTABLEEDIT_UNINSTALL_TEXT') . '</p>';
        }
 
        function update($parent) 
        {
                echo '<p>' . JText::_('COM_EVENTTABLEEDIT_UPDATE_TEXT') . '</p>';
        }
 
        /**
         * method to run before an install/update/uninstall method
         */
        function preflight($type, $parent) 
        {
                // $type is the type of change (install, update or discover_install)
                echo '<p>' . JText::_('COM_EVENTTABLEEDIT_PREFLIGHT_' . $type . '_TEXT') . '</p>';
        }
 
        /**
         * method to run after an install/update/uninstall method
         */
        function postflight($type, $parent) 
        {
                // $type is the type of change (install, update or discover_install)
                echo '<p>' . JText::_('COM_EVENTTABLEEDIT_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
        }
}


