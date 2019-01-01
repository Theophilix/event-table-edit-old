<?php
/**
 * $Id: view.html.php 85 2009-09-22 16:07:12Z kapsl $
 * @copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
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
defined( '_JEXEC' ) or die;
jimport( 'joomla.application.component.view');

class EventtableeditViewDropdown extends JViewLegacy {
	protected $item;
	protected $form;
	protected $state;
	protected $dropdowns;
	
	public function display($tpl = null) {
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		 = $this->get('Item');
		$this->state	 = $this->get('State');
		$this->dropdowns = $this->get('Dropdowns');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		
		// Add CSS and Script
		$this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');
		require_once JPATH_COMPONENT.'/helpers/dropdown.js.php';
		
		$this->assignRef('dropdowns', $this->dropdowns);
		
		parent::display($tpl);
	}
	
	protected function addToolbar()	{
		$input  =  JFactory::getApplication()->input;
		$input->set('hidemainmenu',true);;

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= eteHelper::getActions($this->state->get('filter'));

		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_MANAGER_DROPDOWN'), 'dropdown.png');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_MANAGER_DROPDOWN' ), 'etetables' );
		// Built the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('dropdown.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('dropdown.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('dropdown.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}

			JToolBarHelper::cancel('dropdown.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit')) {
					JToolBarHelper::apply('dropdown.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('dropdown.save', 'JTOOLBAR_SAVE');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::custom('dropdown.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::custom('dropdown.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}

			JToolBarHelper::cancel('dropdown.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
?>
