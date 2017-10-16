<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2017 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
require_once JPATH_COMPONENT.'/models/csvimport.php';
require_once JPATH_SITE.'/components/com_eventtableedit/helpers/datatypes.php';

/**
 * A subclass of Csvimport to handle importing a new table
 * so that the main class doesn't get too crowded
 */
class EventtableeditModelCsvimportnewtable extends EventtableeditModelCsvimport {
	private $detailsModel;
	private $tableName;
	
	public function __construct() {
		parent::__construct();
		
		$this->separator = $this->app->getUserState('com_eventtableedit.separator', ';');
		$this->doubleqt = $this->app->getUserState('com_eventtableedit.doubleqt', 1);
	}
	
	/**
	 * Import all the csv data and create a new table
	 */
	public function importCsvNew($detailsModel, $name, $datatype) {
		$this->detailsModel = $detailsModel;
		$this->tableName = $name;
		$this->heads['datatype'] = $datatype;
		
		if (!$this->createDetailsTable()) {
			return false;
		}
		
		$this->createRowsTable();
		$this->createHeadsTable();

		$this->readCsvFile();
		$this->app->setUserState("com_eventtableedit.csvError", false);
	}
	
	/**
	 * Save the configuration
	 */
	private function createDetailsTable() {
		$data = array();
		$data['id'] = 0;
		$data['name'] = $this->tableName;
		$data['published'] = 1;
		if (!$this->detailsModel->save($data)) {
			JError::raiseError( 500, JText::_('COM_EVENTTABLEEDIT_SAME_NAME'));
			return false;
		}
		
		// Get the table id
		$this->id = $this->app->getUserState('etetable.id', 0);
		
		return true;
	}
	
	private function createHeadsTable() {
		$this->getHeadLine();
			$db = JFactory::getDBO();
		$updatecol = "UPDATE `#__eventtableedit_details` SET col='".count($this->csvHeadLine)."' WHERE id='".$this->id."'";
		$db->setQuery($updatecol);
		$db->query();
		$len = count($this->csvHeadLine);
		if (isset($this->csvHeadLine[0]) && $this->csvHeadLine[0] === 'timestamp')
			$len = $len - 1;
		
		for ($a = 0; $a < $len; $a++) {
			$table = JTable::getInstance('Heads', 'EventtableeditTable');
			
			$data = array();
			$data['table_id'] = $this->id;
			$data['name'] = $this->csvHeadLine[$a];
			$data['datatype'] = $this->heads['datatype'][$a];
			$data['ordering'] = $a;
			
			if (!$table->bind($data)) {
        		echo $table->getError();
			}
			if (!$table->store()) {
        		echo $table->getError();
			}
			
			$newId = $table->id;
			
			// Adjust the db rows table to save the entries
			$this->updateRowsTable($newId, $this->heads['datatype'][$a]);
		}
	}
	
	/**
	 * A new table has to be created, insert into rows table
	 */
	private function createRowsTable() {
		// A new table has to be created
		$query = 'CREATE TABLE #__eventtableedit_rows_' . $this->id .
				 ' (id INT NOT NULL AUTO_INCREMENT,' .
				 ' ordering INT(11) NOT NULL default 0,' .
				 ' created_by INT(11) NOT NULL default 0,' .
				 ' timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,' .
				 ' PRIMARY KEY (id)
				 )' .
				 ' ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
		$this->db->setQuery($query);
		$this->db->query();
	}
	
	/**
	 * Alters the _rows_$id table that it fits to the table heads
	 */
	private function updateRowsTable($newId, $datatype) {
		$query = 'ALTER TABLE #__eventtableedit_rows_' . $this->id;
		$query .= ' ADD head_' . $newId . ' ' . Datatypes::mapDatatypes($datatype);
		
		$this->db->setQuery($query);
		$this->db->query();
	}
}
