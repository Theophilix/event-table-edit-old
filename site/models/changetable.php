<?php
/**
 * $Id:$
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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT.'/helpers/datatypes.php';

class EventtableeditModelChangetable extends JModelList
{
	protected $_context = 'com_eventtableedit.etetable';
	protected $params;
	protected $db;
	protected $id;
	
	public function __construct() {
		parent::__construct();
		
		// Load the parameters
		$app = JFactory::getApplication('site');
		$params = $app->getParams();
		
		$this->setState('params', $params);
		$this->params = $params;
		$main     = $app->input;
		$this->id = $main->getInt('id', '');
		
		
	
		$this->db = $this->getDbo();
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$query = $this->db->getQuery(true);

		$query->select('a.*');
		$query->from('#__eventtableedit_heads AS a');
		$query->where('a.table_id = ' . $this->id);
		$query->order('a.ordering', 'asc');
		
		return $query;
	}
	
	/**
	 * Get the table name and check if it is published
	 */
	public function getInfo() {
		$query = $this->db->getQuery(true);

		$query->select('d.name as tablename');
		$query->from('#__eventtableedit_details AS d');
		$query->where('d.id = ' . $this->id);
		$query->where('d.published = 1');
		$this->db->setQuery($query);
	
		return $this->db->loadAssoc();
	}
	
	public function getAccess() {
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		$asset	= 'com_eventtableedit.etetable.'.$this->id;

		if ($user->authorise('core.create_admin', $asset)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Save the entered values
	 */
	public function save($cid, $name, $datatype, $defaultSorting) {
		// Update rows table


		$this->createRowsTable();
		
		$deleteColIds = $this->deleteNotUsedCols($cid);
		
		// Delete not used entries in the heads table
		if (count($deleteColIds) > 0) {
			$query = 'DELETE FROM #__eventtableedit_heads' .
					 ' WHERE id IN (' . implode(',', $deleteColIds) . ')';
			$this->db->setQuery($query);
			$this->db->query();
		}
		$db = JFactory::getDBO();
		$updatecol = "UPDATE `#__eventtableedit_details` SET col='".count($name)."' WHERE id='".$this->id."'";
		$db->setQuery($updatecol);
		$db->query();		
		// Add the new table heads
		
		for ($a = 0; $a < count($name); $a++) {
			$table = JTable::getInstance('Heads', 'EventtableeditTable');
			
			$data = array();
			$data['id'] = $cid[$a];
			$data['table_id'] = $this->id;
			$data['name'] = addslashes($name[$a]);
			$data['datatype'] = $datatype[$a];
			$data['defaultSorting'] = $defaultSorting[$a];
			$data['ordering'] = $a;
			
			if (!$table->bind($data)) {
        			echo $table->getError();
			}
			if (!$table->store()) {
        			echo $table->getError();
			}
			
			$newId = $table->id;
			
			// Create or adjust the db table to save the entries
			$this->updateRowsTable($cid[$a], $newId, $datatype[$a]);
		}
	}
	
	/**
	 * See if a new table has to be created
	 */
	private function createRowsTable() {
		// Need to use getPrefix because of a Joomla Bug
		// within quotes #__ is not replaced
		$query = 'SHOW TABLE STATUS LIKE \'' . $this->db->getPrefix() . 'eventtableedit_rows_' . $this->id . '\'';
		$this->db->setQuery($query);
		
		if (count($this->db->loadObjectList()) > 0) {
			return false;
		}
		
		// A new table has to be created
		$query = 'CREATE TABLE #__eventtableedit_rows_' . $this->id .
				 ' (id INT NOT NULL AUTO_INCREMENT,' .
				 ' ordering INT(11) NOT NULL default 0,' .
				 ' created_by INT(11) NOT NULL default 0,' .
				 ' PRIMARY KEY (id))' .
				 ' ENGINE=MyISAM CHARACTER SET \'utf8\' COLLATE \'utf8_general_ci\'';
		$this->db->setQuery($query);
		$this->db->query();
	}
	
	private function deleteNotUsedCols($cid) {
		// Get columns that has to be deleted
		$query = 'SELECT id FROM #__eventtableedit_heads' .
				 ' WHERE table_id = ' . $this->id;
		
		if (count($cid) > 0) {
			$query .=  ' AND id NOT IN (' . implode(',', $cid) . ')';
		}
		$this->db->setQuery($query);
		$rows = $this->db->loadColumn();
		
		for ($a = 0; $a < count($rows); $a++) {
			$query = 'ALTER TABLE #__eventtableedit_rows_' . $this->id .
					 ' DROP COLUMN head_' . $rows[$a];
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Delete all rows if all colums are deleted
		if (!count($cid)) {
			$query = 'TRUNCATE TABLE #__eventtableedit_rows_' . $this->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		return $rows;
	}
	
	/**
	 * replace string between start and end string
	 */
	 
	function delete_all_between($beginning, $end, $string) {
		$beginningPos = strpos($string, $beginning);
		$endPos = strpos($string, $end);
		if ($beginningPos === false || $endPos === false) {
			return $string;
		}

		$textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

		return str_replace($textToDelete, '', $string);
	}


	/**
	 * Alters the _rows_$id table that it fits to the table heads
	 */
	private function updateRowsTable($cid, $newId, $datatype) {
		
		$query = 'ALTER TABLE #__eventtableedit_rows_' . $this->id . ' ';
		
		// If it's a existing column
		if ($cid != 0) {
			
			$qx = 'SELECT DATA_TYPE as datatype FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . $this->db->getPrefix() . 'eventtableedit_rows_' . $this->id . '" AND COLUMN_NAME = "head_' . $newId . '"';
			$this->db->setQuery($qx);
			$this->db->execute();
			$new_datatype = $this->db->loadObject()->datatype;
			
			$map_datatype = $new_datatype = $this->delete_all_between('(',')',strtoupper(Datatypes::mapDatatypes($datatype)));
			if(strtoupper($new_datatype) != $map_datatype){
				
				$query .= 'CHANGE head_' . $newId . ' head_' . $newId . ' ' . Datatypes::mapDatatypes($datatype);
				$this->db->setQuery($query);
				$this->db->query();
				
				if($datatype == 'four_state'){
					$q2 = 'UPDATE #__eventtableedit_rows_' . $this->id . ' SET head_' . $newId . ' = "0"';
					$this->db->setQuery($q2);
					$this->db->query();
				}elseif($datatype == "boolean"){
					$q3 = 'UPDATE #__eventtableedit_rows_' . $this->id . ' SET head_' . $newId . ' = "&nbsp;"';
					$this->db->setQuery($q3);
					$this->db->query();
				}
			}			
			
		} else {
			$detailquery = "SELECT normalorappointment FROM #__eventtableedit_details WHERE id ='".$this->id."'";
			$this->db->setQuery($detailquery);
			$appointment = $this->db->loadResult();
			$query .= 'ADD head_' . $newId . ' ' . Datatypes::mapDatatypes($datatype);
			$this->db->setQuery($query);
			$this->db->query();
			if($appointment == 1){
				$update = "UPDATE `#__eventtableedit_rows_". $this->id . "` SET `head_" . $newId."`='free'";
				$this->db->setQuery($update);
				$this->db->query();
			}
		}
		
	}
	public function getnormal_table($id){
		$db = JFactory::GetDBO();
		$select = "SELECT normalorappointment FROM #__eventtableedit_details WHERE id='".$id."'";
		$db->setQuery($select);
		$table = $db->loadResult();
		return $table;
	}
}
