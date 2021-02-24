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
require_once JPATH_COMPONENT.'/helpers/ete.php';
require_once JPATH_SITE.'/components/com_eventtableedit/helpers/datatypes.php';

/**
 * This view can diesplay different stages of the import process.
 */
class EventtableeditViewCsvimport extends JViewLegacy
{
    public function display($tpl = null)
    {
        
		$input = JFactory::getApplication()->input;
		$this->id = $input->get('id');
		
		$jinput = JFactory::getApplication()->input;
		$checkfun = $jinput->get('checkfun', 0);


		if (!$this->id) {
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
            case 'newTable':
                $headLine = $this->get('HeadLine');

                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                    foreach ($errors as $error) {
                        JFactory::getApplication()->enqueueMessage($error, 'error');
                    }
                    return false;
                }

                // Create the select list of datatypes
                $datatypes = new Datatypes();
                $listDatatypes = $datatypes->createSelectList();

                $this->assignRef('headLine', $headLine);
                $this->assignRef('listDatatypes', $listDatatypes);

                //$this->addNewTableToolbar();
                break;
            case 'summary':
                // Check for errors.
                if (count($errors = $this->get('Errors'))) {
                    foreach ($errors as $error) {
                        JFactory::getApplication()->enqueueMessage($error, 'error');
                    }
                    return false;
                }

                $this->assignRef('headLine', $headLine);
               
                break;
            default:
                // Get max upload size
                $max_upload = (int) (ini_get('upload_max_filesize'));
                $max_post = (int) (ini_get('post_max_size'));
                $memory_limit = (int) (ini_get('memory_limit'));
                $upload_mb = min($max_upload, $max_post, $memory_limit);

                $tableList = EventtableeditViewCsvimport::createTableSelectList();
				

                $this->assignRef('tables', $tableList);
                $this->assignRef('maxFileSize', $upload_mb);

                //$this->addDefaultToolbar();
        }

        $this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');
		$this->assignRef('id', $this->id);
		$this->assignRef('checkfun', $checkfun);
        $this->setLayout($layout);
        parent::display($tpl);
    }

    /**
     * Generates a select list, where all tables are listed
     * This function is also used in the export module.
     */
    public function createTableSelectList()
    {
        $tables = EventtableeditModelCsvimport::getTables();

        if (0 === (int)count($tables)) {
            return null;
        }

        $elem = [];
        //$elem[] = JHTML::_('select.option', '', JText::_('COM_EVENTTABLEEDIT_PLEASE_SELECT_TABLE'));

        foreach ($tables as $table) {
			if($this->id == $table->id)
				$elem[] = JHTML::_('select.option', $table->id, $table->id.' '.$table->name);
        }
        return JHTML::_('select.genericlist', $elem, 'tableList', ' required="true"', 'value', 'text', 0);
    }

    protected function addDefaultToolbar()
    {
        $canDo = eteHelper::getActions();

        //JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_MANAGER_CSVIMPORT'), 'import');
        $xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/eventtableedit.xml');
        $currentversion = (string) $xml->version;
        JToolBarHelper::title(JText::_('Event Table Edit '.$currentversion).' - '.JText::_('COM_EVENTTABLEEDIT_MANAGER_CSVIMPORT'), 'etetables');
        // For uploading, check the create permission.
        if ($canDo->get('core.csv')) {
            JToolBarHelper::custom('csvimport.upload', 'upload.png', '', 'COM_EVENTTABLEEDIT_UPLOAD', true);
        }
    }

    /**
     * The Toolbar for importing a new table and selecting the datatypes.
     */
    protected function addNewTableToolbar()
    {
        $canDo = eteHelper::getActions();

        //JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE'), 'import');
        $xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/eventtableedit.xml');
        $currentversion = (string) $xml->version;
        JToolBarHelper::title(JText::_('Event Table Edit '.$currentversion).' - '.JText::_('COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE'), 'etetables');

        // For uploading, check the create permission.
        if ($canDo->get('core.csv')) {
            JToolBarHelper::custom('csvimport.newTable', 'apply.png', '', 'JTOOLBAR_APPLY', false);
        }
        JToolBarHelper::cancel('csvimport.cancel', 'JTOOLBAR_CLOSE');
    }
}
