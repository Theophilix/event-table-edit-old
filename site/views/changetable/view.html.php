<?php
/**
 * $Id: view.html.php 157 2011-03-19 00:08:23Z kapsl $
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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
require_once JPATH_COMPONENT.'/models/changetable.php';
require_once JPATH_COMPONENT.'/helpers/datatypes.php';

class EventtableeditViewChangetable extends JViewLegacy {
	protected $item;
	protected $tableInfo;
	protected $params;
	
	function display($tpl = null) {
		// Initialise variables.

		
		$app				= JFactory::getApplication();
		$this->item			= $this->get('Items');
		$this->tableInfo 	= $this->get('Info');
		
		if ($this->tableInfo == null) {
			JError::raiseWarning(500, JText::_('COM_EVENTTABLEEDIT_ERROR_ETETABLE_NOT_FOUND'));
			return false;
		}
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		// Get the parameters of the active menu item
		$this->params	= $app->getParams();
		$main  			= $app->input;
		$id				= $main->getInt('id');
		

		$additional = array();
		
		// Get Datatypes
		$datatypes = new Datatypes();
		$additional['datatypes'] = $datatypes->getDatatypes();
		$additional['datatypes_desc'] = $datatypes->getDatatypesDesc();
		
		$this->assignRef('item', $this->item);
		$this->assignRef('info', $this->tableInfo);
		$this->assignRef('params', $this->params);
		$this->assignRef('additional', $additional);
		$this->assignRef('id', $id);
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		JHtml::_('behavior.framework');
		
		// Add Scripts and Stylesheets
		require_once JPATH_COMPONENT.'/helpers/changetable.js.php';
		$this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtablecss.css');
		$this->document->addCustomTag($this->getBrowserStyles());
	}
	
	/**
	 * Especially for IE that the calendar is on the right position
	 */
	private function getBrowserStyles() {
		$ie  = '<!--[if lte IE 7]>' ."\n";
		$ie .= '<link rel="stylesheet" href="' . $this->baseurl.'/components/com_eventtableedit/template/css/ie7.css" />' ."\n";
		$ie .= '<![endif]-->' ."\n";
		
		return $ie;
	}
}
?>
