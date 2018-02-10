<?php
/**
 * $Id:$
 * @copyright (C) 2007 - 2017 Manuel Kaspar and Theophilix
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

jimport('joomla.application.component.controller');

class EventtableeditControllerChangetable extends JControllerLegacy
{	
	public function save() {
		// Check for request forgeries and acl
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
		
		
		// Get Variables
		$main  				= JFactory::getApplication()->input;
		$id 				= $main->getInt('id', '');
		$cid 				= $main->post->get('cid', array(), 'array');		
		$name 				= $main->post->get('name', array(), 'array');		
		$datatype 			= $main->post->get('datatype', array(), 'array');		
		$defaultSorting 	= $main->post->get('defaultSorting', array(), 'array');		
				
		$model = $this->getModel('changetable');
		$model->save($cid, $name, $datatype, $defaultSorting);
		$normal = $model->getnormal_table($id);
		if($normal == 0){
			$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit&view=etetable&id=' . $id,false), JText::_('COM_EVENTTABLEEDIT_SETTINGS_SAVED'));
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit&view=appointments&id=' . $id,false), JText::_('COM_EVENTTABLEEDIT_SETTINGS_SAVED'));
			
		}
	}
	
	private function aclCheck() {
		$user   = JFactory::getUser();
		$main  	= JFactory::getApplication()->input;
		$id 	= $main->getInt('id', '-1');
		$asset	= 'com_eventtableedit.etetable.'.$id;
		
		if (!$user->authorise('core.create_admin', $asset)) {
			return false;
		}
		return true;
	}
}
?>
