<?php
/**
 * $Id: view.html.php 140 2011-01-11 08:11:30Z kapsl $.
 *
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

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');
require_once JPATH_COMPONENT.'/views/csvimport/view.html.php';
require_once JPATH_COMPONENT.'/models/csvimport.php';

class EventtableeditViewCsvexport extends JViewLegacy
{
    public function display($tpl = null)
    {
		$input = JFactory::getApplication()->input;
		$id = $input->get('id');
		
		if (!$id) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            return false;
        }
		
        $user = JFactory::getUser();
        $app = JFactory::getApplication();

        if (!$user->authorise('core.csv', 'com_eventtableedit')) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            return false;
        }
        $input = JFactory::getApplication()->input;
        $layout = $input->get('com_eventtableedit.layout');
        // Switch the differnet datatypes

        switch ($layout) {
            case 'summary':
                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                    foreach ($errors as $error) {
                        JFactory::getApplication()->enqueueMessage($error, 'error');
                    }
                    return false;
                }
                $postget = $input->getArray();

                $this->assignRef('csvFile', $postget['csvFile']);

                break;
            default:
                /* $importView = new EventtableeditViewCsvimport();
                $tableList = $importView->createTableSelectList();

                $this->assignRef('tables', $tableList); */
				$this->assignRef('id', $id);

               
        }

        $this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');

        $this->setLayout($layout);
        parent::display($tpl);
    }
}
