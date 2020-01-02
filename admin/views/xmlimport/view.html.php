<?php
/**
 * $Id: view.html.php 140 2011-01-11 08:11:30Z kapsl $
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
defined( '_JEXEC' ) or die;
jimport( 'joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/ete.php';
require_once JPATH_SITE.'/components/com_eventtableedit/helpers/datatypes.php';

/**
 * This view can diesplay different stages of the import process
 */
class EventtableeditViewXmlimport extends JViewLegacy {
	function display($tpl = null) {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		if (!$user->authorise('core.csv', 'com_eventtableedit')) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		$input  =  JFactory::getApplication()->input;
		$layout = $input->get('com_eventtableedit.layout');
		$this->addDefaultToolbar();	
		$this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');
		
		$this->setLayout($layout);
	    parent::display($tpl);
	}
	
	/**
	 * Generates a select list, where all tables are listed
	 * This function is also used in the export module
	 */

	
	protected function addDefaultToolbar()	{
		$canDo		= eteHelper::getActions();

		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_MANAGER_XMLIMPORT'), 'import');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_MANAGER_XMLIMPORT' ), 'etetables' );
		// For uploading, check the create permission.
		if ($canDo->get('core.csv')) {
			JToolBarHelper::custom('xmlimport.upload', 'upload.png', '', 'COM_EVENTTABLEEDIT_UPLOAD', true);
		}
	}
}
?>
