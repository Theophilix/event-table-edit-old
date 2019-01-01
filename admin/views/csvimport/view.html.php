<?php
/**
 * $Id: view.html.php 140 2011-01-11 08:11:30Z kapsl $
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
require_once JPATH_COMPONENT.'/helpers/ete.php';
require_once JPATH_SITE.'/components/com_eventtableedit/helpers/datatypes.php';

/**
 * This view can diesplay different stages of the import process
 */
class EventtableeditViewCsvimport extends JViewLegacy {
	function display($tpl = null) {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		if (!$user->authorise('core.csv', 'com_eventtableedit')) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		$input  =  JFactory::getApplication()->input;
		$layout = $input->get('com_eventtableedit.layout');
		// Switch the differnet datatypes
		switch ($layout) {
			case 'newTable':
				$headLine = $this->get('HeadLine');

				// Check for errors.
				if (count($errors = $this->get('Errors'))) {
					JError::raiseError(500, implode("\n", $errors));
					return false;
				}
				
				// Create the select list of datatypes
				$datatypes = new Datatypes();
				$listDatatypes = $datatypes->createSelectList();

				$this->assignRef('headLine', $headLine);
				$this->assignRef('listDatatypes', $listDatatypes);
				
				$this->addNewTableToolbar();
				break;
			case 'summary':
				// Check for errors.
				if (count($errors = $this->get('Errors'))) {
					JError::raiseError(500, implode("\n", $errors));
					return false;
				}
				
				$this->assignRef('headLine', $headLine);
				$this->addSummaryToolbar();
				break;
			default:
				// Get max upload size
				$max_upload = (int) (ini_get('upload_max_filesize'));
				$max_post = (int) (ini_get('post_max_size'));
				$memory_limit = (int) (ini_get('memory_limit'));
				$upload_mb = min($max_upload, $max_post, $memory_limit);

				$tableList = EventtableeditViewCsvimport::createTableSelectList();
				$tableList1 = EventtableeditViewCsvimport::createTableSelectList1();

				$this->assignRef('tables', $tableList);
				$this->assignRef('tables1', $tableList1);
				$this->assignRef('maxFileSize', $upload_mb);
				
				$this->addDefaultToolbar();
		}
		
		$this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');
		
		$this->setLayout($layout);
	    parent::display($tpl);
	}
	
	/**
	 * Generates a select list, where all tables are listed
	 * This function is also used in the export module
	 */
	public function createTableSelectList() {
		$tables = EventtableeditModelCsvimport::getTables();
		
		if (count($tables) == 0) return null;
		
		$elem = array();
		$elem[] = JHTML::_('select.option', '', 'Please select table');
		
		foreach($tables as $table) {
			$elem[] = JHTML::_('select.option', $table->id, $table->id . ' ' . $table->name);
		}
		return JHTML::_('select.genericlist', $elem, 'tableList', ' required="true"', 'value', 'text', 0);
	}
	public function createTableSelectList1() {
		$tables = EventtableeditModelCsvimport::getTables();
		
		if (count($tables) == 0) return null;
		
		$elem = array();
		$elem[] = JHTML::_('select.option', '', '');
		
		foreach($tables as $table) {
			$elem[] = JHTML::_('select.option', $table->id, $table->id . ' ' . $table->name);
		}
		return JHTML::_('select.genericlist', $elem, 'tableList1', '', 'value', 'text', 0);
	}
	
	protected function addDefaultToolbar()	{
		$canDo		= eteHelper::getActions();

		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_MANAGER_CSVIMPORT'), 'import');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_MANAGER_CSVIMPORT' ), 'etetables' );
		// For uploading, check the create permission.
		if ($canDo->get('core.csv')) {
			JToolBarHelper::custom('csvimport.upload', 'upload.png', '', 'COM_EVENTTABLEEDIT_UPLOAD', true);
		}
	}
	
	/**
	 * The Toolbar for importing a new table and selecting the datatypes
	 */
	protected function addNewTableToolbar()	{
		$canDo		= eteHelper::getActions();

		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE'), 'import');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE' ), 'etetables' );
		
		
		// For uploading, check the create permission.
		if ($canDo->get('core.csv')) {
			JToolBarHelper::custom('csvimport.newTable', 'apply.png', '', 'JTOOLBAR_APPLY', false);
		}
		JToolBarHelper::cancel('csvimport.cancel', 'JTOOLBAR_CLOSE');
	}
	
	/**
	 * The Toolbar for showing the summary of the import
	 */
	protected function addSummaryToolbar()	{
		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_IMPORT_SUMMARY'), 'import');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_IMPORT_SUMMARY' ), 'etetables' );
		JToolBarHelper::custom('csvimport.cancel', 'apply.png', '', 'COM_EVENTTABLEEDIT_OK', false);
	}
}
?>
