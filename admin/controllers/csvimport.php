<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2020 Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

class EventtableeditControllerCsvimport extends JControllerLegacy {
	protected $app;
	
	protected $id;
	protected $file;
	protected $importaction;
	protected $separator;
	protected $doubleqt;
	protected $checkfun;
	protected $model;
	
	function __construct() {
		parent::__construct();
		$this->app = JFactory::getApplication();
	}
	
	/**
	 * Task that is called when uploading a csv file
	 */
	public function upload() {
		// ACL Check
		$user = JFactory::getUser();
		if (!$user->authorise('core.csv', 'com_eventtableedit')) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect(JRoute('index.php?option=com_eventtableedit'));
			return false;
		}
		$input  =  JFactory::getApplication()->input;
		$postget = $input->getArray($_REQUEST);
		
		// Initialize Variables
		$this->model = $this->getModel('csvimport');
		$this->file = $input->files->get('fupload');
		
		$this->separator    = $postget['separator']; 
		$this->doubleqt     = $postget['doubleqt'];  
		$this->importaction = $postget['importaction']; 
		$this->checkfun = $postget['checkfun']?$postget['checkfun']:0; 
			
		$input->set('view','csvimport');
		

		if ($this->importaction == 'overwriteTable') {
			$this->id = $input->get('tableList');

		}else if ($this->importaction == 'appendTable') {
			$this->id = $input->get('tableList1');
		} else {
			$this->id = 0;
		}
	
		
		$this->checkForErrors();
		$this->moveFile();
		$this->model->setVariables($this->id, $this->separator, $this->doubleqt,$this->checkfun);
		if($this->checkfun){
			$return = $this->switchUploadTypes();
			if($return == "newTable"){
				$this->newTable();
			}
			
			
			switch ($this->importaction) {
				case 'overwriteTable':
					if (!$this->app->getUserState('com_eventtableedit.csvError', true)) :
						$msg = JText::_('COM_EVENTTABLEEDIT_IMPORT_REPORT_OVERWRITE');
					else:
						$msg = JText::_('COM_EVENTTABLEEDIT_IMPORT_REPORT_OVERWRITE_FAILED');
					endif;
					break;
				case 'appendTable':
					if (!$this->app->getUserState('com_eventtableedit.csvError', true)) :
						$msg = JText::_('COM_EVENTTABLEEDIT_IMPORT_REPORT_APPEND');
					else:
						$msg = JText::_('COM_EVENTTABLEEDIT_IMPORT_REPORT_APPEND_FAILED');
					endif;
					break;
				case 'newTable':
					$msg = JText::_('COM_EVENTTABLEEDIT_IMPORT_REPORT_NEW');
					break;
			}
			
			
			
			
			if($this->checkfun){
				$this->app->redirect("index.php?option=com_eventtableedit&view=appointmenttables",$msg);
			}else{
				$this->app->redirect("index.php?option=com_eventtableedit&view=etetables",$msg);
			}
		}else{
			$input->set('tableName',$input->get("table_name"));
			$this->switchUploadTypes();
			parent::display();
		}
		
		
		
		//parent::display();
	}
	
	/**
	 * Task that is called when saving a new table
	 */
	public function newTable() {
		// ACL Check
		$user = JFactory::getUser();
		$input  =  JFactory::getApplication()->input;
		$checkfun = $input->get('checkfun');
		$input->set('checkfun',$checkfun);
		if (!$user->authorise('core.csv', 'com_eventtableedit')) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit'));
			return false;
		}
		$postget = $input->getArray();
		
		// Get Variables

		if($checkfun){
			$name = $input->get('table_name');
			$datatype = array();
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
			$datatype[] = 'text';
		}else{
			$name = $postget['tableName'];
			$datatype = $postget['datatypesList'];
		}
		
		
	
		
		$this->model = $this->getModel('csvimportnewtable');
		$detailsModel = $this->getModel('etetable');
		if(!$this->model->importCsvNew($detailsModel, $name, $datatype)){
			$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit&view=csvimport'));
		}

		return;
	}
	
	public function cancel() {
		$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit'));
		return false;
	}
	
	/**
	 * Checks the uploaded file
	 */
	private function checkForErrors() {
		$redirectUrl = 'index.php?option=com_eventtableedit&view=csvimport';

		// No file specified
		if ($this->file['name'] == NULL) {
			JError::raiseWarning( 1000, JText::_('COM_EVENTTABLEEDIT_NO_FILE_SPECIFIED'));
			$this->setRedirect($redirectUrl);
			$this->redirect();
		}
		// Check file type
		$ending = substr($this->file['name'], -3);
		$ending = strtolower($ending);
		if ($ending != 'txt' && $ending != 'csv') {
			JError::raiseWarning( 1000, JText::_('COM_EVENTTABLEEDIT_WRONG_FILE_TYPE'));
			$this->setRedirect($redirectUrl);
			$this->redirect();
		}
		// If a table is choosen
		if ($this->importaction == 'overwriteTable' || $this->importaction == 'appendTable') {
			if ($this->id == NULL || $this->id == '') {
				JError::raiseWarning( 1000, JText::_('COM_EVENTTABLEEDIT_NO_TABLE'));
				$this->setRedirect($redirectUrl);
				$this->redirect();
			}
		}
	}
	
	/**
	 * Move the uploaded file to the server
	 */ 
	private function moveFile() {
		$res = move_uploaded_file($this->file['tmp_name'], JPATH_BASE.DS.'components'.DS.'com_eventtableedit'.DS.'tmpUpload.csv');

		if (!$res) {
			JError::raiseWarning( 1000, JText::_('COM_EVENTTABLEEDIT_ERROR_MOVING_FILE'));
			$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit&view=csvimport'));
		}
	}
	
	private function switchUploadTypes() {
		// Save vars to session
		$this->storeVarsInSession();
		$input  =  JFactory::getApplication()->input;
		switch ($this->importaction) {
			case 'overwriteTable':
				$this->model->importCsvOverwrite();
				$input->set('com_eventtableedit.layout','summary');
				return 'summary';
				break;
			case 'appendTable':
				$this->model->importCsvAppend();
				$input->set('com_eventtableedit.layout','summary');
				return 'summary';
				break;
			case 'newTable':

				$input->set('com_eventtableedit.layout','newTable');
				return 'newTable';
				break;
		}
	}
	
	private function storeVarsInSession() {
		$this->app->setUserState("com_eventtableedit.id", $this->id);
		$this->app->setUserState("com_eventtableedit.importAction", $this->importaction);
		$this->app->setUserState("com_eventtableedit.separator", $this->separator);
		$this->app->setUserState("com_eventtableedit.doubleqt", $this->doubleqt);
		$this->app->setUserState("com_eventtableedit.checkfun", $this->checkfun);
	}
}
