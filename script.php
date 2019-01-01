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
			$db = JFactory::getDBO();
			$query = 'SELECT id FROM #__eventtableedit_details';
			$db->setQuery($query);
			$rows = $db->loadColumn();

			if(!empty($rows)){
				for ($a = 0; $a < count($rows); $a++) {
					$query = 'SELECT * FROM #__eventtableedit_rows_' . $rows[$a];
					
					$db->setQuery($query);
					$data = $db->loadObject();
					
					if(!isset($data->timestamp)){ 
						$query = 'ALTER TABLE `#__eventtableedit_rows_' . $rows[$a] . '` ADD `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`, COMMENT=""';
						$db->setQuery($query);
						$db->query();
					}
				}
			}

			$query = "SHOW COLUMNS FROM `#__eventtableedit_details` LIKE 'rowdelete'";
			$db->setQuery($query);
			$data = $db->loadObject();
			if(empty($data)){
				$query = 'ALTER TABLE `#__eventtableedit_details` ADD `rowdelete` tinyint(4) NOT NULL AFTER `rowsort`, COMMENT=""';
				$db->setQuery($query);
				$db->query(); 
			}
			
			$app = JFactory::getApplication();
			$prefix = $app->getCfg('dbprefix');
			
			$query = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$prefix."eventtableedit_details' AND COLUMN_NAME = 'scroll_table' ";
			$db->setQuery($query);
			$data = $db->loadObject();
			if(empty($data)){
				$query = 'ALTER TABLE `#__eventtableedit_details`
				ADD `scroll_table` varchar(255) COLLATE "utf8_general_ci" NOT NULL,
				COMMENT=""';
				$db->setQuery($query);
				$db->query(); 
			}
			
			$query = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$prefix."eventtableedit_details' AND COLUMN_NAME = 'scroll_table_height'";
			$db->setQuery($query);
			$data = $db->loadObject();
			if(empty($data)){
				$query = 'ALTER TABLE `#__eventtableedit_details`
				ADD `scroll_table_height` varchar(255) COLLATE "utf8_general_ci" NOT NULL AFTER `scroll_table`,
				COMMENT=""';
				$db->setQuery($query);
				$db->query(); 
			}
			
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


