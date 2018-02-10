<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2017 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_SITE.'/components/com_eventtableedit/helpers/etetable.php';

class EventtableeditModelappointmentform extends JModelList
{
	protected $_context = 'com_eventtableedit.etetable';
	protected $params;
	protected $heads;
	protected $db;
	protected $id;

	// Holds the filterstring standard ''
	protected $filter;
	protected $defaultSorting;
	
	public function __construct() {
		parent::__construct();
		
		// Load the parameters
		$app = JFactory::getApplication('site');
		$params = $app->getParams();
		
		$this->setState('params', $params);
		$this->params = $params;
		$main         = $app->input;
		 $this->id     = $main->getInt('id', '');
		
		$this->filter = '';
		
		$this->setState('is_module', 0);
		
		$this->db = $this->getDbo();
	}
	
	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Load state from the request.
		$app 		= JFactory::getApplication('site');
		$main       = $app->input;
		$pk         = $this->id;
		
		if ($pk == '') {
			$pk = $this->id;
		}
		$this->setState('appointments.id', $pk);
		
		// filter.order
		$this->setState('list.ordering', $app->getUserStateFromRequest($pk . '.filter_order', 'filter_order', 'a.ordering', 'string'));
		$this->setState('list.direction', $app->getUserStateFromRequest($pk . '.filter_order_Dir',	'filter_order_Dir', 'asc', 'cmd'));

		$this->setState('list.start', $main->getInt('limitstart', '0'));
	}
	
	/**
	 * Build the orderby for the query
	 *
	 * @return	string	$orderby portion of query
	 * @since	1.5
	 */
	protected function _buildContentOrderBy()
	{
		$app	= JFactory::getApplication('site');
		$params	= $this->state->params;
		$itemid	= $this->getState('appointments.id');
		$filter_order = $app->getUserStateFromRequest('com_eventtableedit.appointments.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$filter_order_Dir = $app->getUserStateFromRequest('com_eventtableedit.appointments.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$orderby = ' ';

		if ($filter_order && $filter_order_Dir) {
			$orderby .= $filter_order . ' ' . $filter_order_Dir . ', ';
		}

		return $orderby;
	}
	
	/**
	 * Get total number of rows
	 */
	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->getRowsQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		jimport('joomla.html.pagination');
		
		// Load only if there are heads
		if (!count($this->heads)) {
			return new JPagination(0, 0, 0);
		}
		
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('list.start'), $this->getState('list.limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Gets a list of contacts
	 * @param array
	 * @return mixed Object or null
	 */
	public function &getItem($pk = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication('site');
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('appointments.id');

		if (@$this->_item === null) {
			$this->_item = array();
		}

		try
		{
			$query = $this->db->getQuery(true);

			$query->select($this->getState('item.select', 'a.*') . ','
			. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug ');
			$query->from('#__eventtableedit_details AS a');
			$query->where('a.id = ' . (int) $pk);

			
			// Filter by published state.
			$query->where('a.published = 1');
			
			$this->db->setQuery($query);

			$data = $this->db->loadObject();

			if ($error = $this->db->getErrorMsg()) {
				throw new JException($error);
			}

			if (empty($data)) {
				//throw new JException(JText::_('COM_EVENTTABLEEDIT_ERROR_ETETABLE_NOT_FOUND'), 404);
			}

			// Convert parameter fields to objects.
			$data->params = clone $this->getState('params');

			$registry = new JRegistry;
			$registry->loadString($data->metadata);
			$data->metadata = $registry;
			
			// Settings for pagination
			// Default Pagebreak if not set
			$limit = $data->pagebreak;
			
			if ($limit == '') {
				$limit = 100;
			}
			
			$limit = $app->getUserStateFromRequest('com_eventtableedit.appointments.list.' . $pk . '.limit', 'limit', $limit);
			$this->setState('list.limit', $limit);
			
			$this->getACL($data);

			$this->_item = $data;
		}
		catch (JException $e)
		{
			$this->setError($e);
			$this->_item = false;
		}
		
  		return $this->_item;
	}
	
	/**
	 * Handle the acl for the table
	 */
	private function getACL(&$data) {
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		$asset	= 'com_eventtableedit.etetable.'.$data->id;

		$data->params->set('access-view', in_array($data->access, $groups));
		
		$data->params->set('access-edit', false);
		$data->params->set('access-add', false);
		$data->params->set('access-delete', false);
		$data->params->set('access-reorder', false);
		$data->params->set('access-create_admin', false);
		$data->params->set('access-ownRows', false);
		
		if ($user->authorise('core.edit', $asset)) {
			$data->params->set('access-edit', true);
		}
		if ($user->authorise('core.add', $asset)) {
			$data->params->set('access-add', true);
		}
		if ($user->authorise('core.delete', $asset)) {
			$data->params->set('access-delete', true);
		}
		if ($user->authorise('core.reorder', $asset)) {
			$data->params->set('access-reorder', true);
		}
		if ($user->authorise('core.create_admin', $asset)) {
			$data->params->set('access-create_admin', true);
		}
		
		// See if edit_own_rows is set to yes and if a user is logged in
		if ($data->edit_own_rows && $user->get('id') != 0) {
			$data->params->set('access-ownRows', true);
		}
	}
	
	/**
	 * Get the table heads
	 */
	public function getHeads() {
		
		if ($this->heads === null) {
			try {
				$query = $this->db->getQuery(true);
		
				$query->select($this->getState('item.select', 'a.*, CONCAT(\'head_\', a.id) AS head'));
				$query->from('#__eventtableedit_heads AS a');
				$query->where('a.table_id = ' . $this->state->get('appointments.id'));
				$query->order('a.ordering asc');
				
				$this->db->setQuery($query);
				$this->heads = $this->db->loadObjectList();
				
				if (empty($this->heads)) {
					return null;
				}

				// Prepare Default Sorting
				$defSort = array();
				foreach ($this->heads as $row) {
					// Prepare Default Sorting
					if ($row->defaultSorting != '' && $row->defaultSorting != ':') {
						$split = explode(':', $row->defaultSorting);
						$defSort[((int) ($split[0]) - 1)] = "a." . $row->head . " " . $split[1];
					}
				}

				if (count($defSort)) {
					$this->defaultSorting = implode(', ', $defSort);
				}

				return $this->heads;
			}
			catch (JException $e) {
				$this->setError($e);
				return false;
			}
		}
	}
	
	/**
	 * Get the dropdown fields used in the table
	 */
	public function getDropdowns() {
		if ($this->heads === null) {
			$this->getHeads();
		}
		
		if (count($this->heads) == 0) {
			return null;
		}
		
		$ret = array();
		$a = 0;
		foreach($this->heads as $head) {
			$temp = explode('.', $head->datatype);
			
			if ($temp[0] == 'dropdown') {
				// Load Dropdown
				$ret[$a]['name'] = $this->loadDropdownName($temp[1]);

				// If the dropdown was deleted
				if (!count($ret[$a]['name'])) continue;

				$ret[$a]['items'] = $this->loadDropdown($temp[1]);
				$a++;
			}
		}
		
		return $ret;
	}
	
	private function loadDropdownName($id) {
		$query = $this->db->getQuery(true);
		$query->select('a.id, a.name');
		$query->from('#__eventtableedit_dropdowns AS a');
		$query->where('a.id = ' . $id);
		
		$this->db->setQuery($query);
		return $this->db->loadAssoc();
	}
	
	private function loadDropdown($id) {
		$query = $this->db->getQuery(true);
		$query->select('a.*');
		$query->from('#__eventtableedit_dropdown AS a');
		$query->where('a.dropdown_id = ' . $id);
		$query->order('a.id asc');
		
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
	
	/**
	 * Get the table rows
	 */
	 public function getRows() {
	 	try {
			$data = array();
			
	 		$query = $this->getRowsQuery();
	 		$data['rows'] = $this->_getList( $query, $this->getState('list.start'), $this->getState('list.limit') );
			
			if (empty($data['rows'])) {
				$data['rows'] = null;
				$data['additional']['createdRows'] = null;
				$data['additional']['ordering'] = null;
				
				return $data;
			}
			
			$data['additional'] = $this->prepareData($data['rows']);;
			$data['rows'] = $this->parseRows($data['rows']);
			
			return $data;
		}
		catch (JException $e) {
			$this->setError($e);
			return false;
		}
	 }
	 
	 /**
	  * Create the query for getting the Rows
	  */
	 protected function getRowsQuery() {
	 	// Add the list ordering clause.
		  $orderCol	= $this->state->get('list.ordering');
	 	$orderDirn	= $this->state->get('list.direction');	
 		 $tid = $this->state->get('appointments.id');
 		
		$query = $this->db->getQuery(true);
		$query->select($this->getState('item.select', 'a.*'));
		$query->from('#__eventtableedit_rows_' . $tid . ' AS a');

		// Use default sorting, if no manual sorting is used
		if ($orderCol == 'a.ordering' && $this->defaultSorting != null) {
			//$orderCol = $this->defaultSorting;
			//$orderDirn = 'ASC';
		}
		$query->order($orderCol.' '.$orderDirn);
			
		// Filter
		$filter = $this->filterRows();
		if ($filter != false) {
			$query->where($filter);
		}
		return $query;
	 }
	 
	 /**
	  * Filters the rows if there is a filter set in the frontend
	  * Thanks to unimx who mostly coded the filter
	  */
	 private function filterRows() {

		$main  		  = JFactory::getApplication()->input;
		$this->filter = $main->get('filterstring');

		if ($this->filter == '') {
			return false;
		}

		$this->filter = str_replace('*', '%', $this->filter);
	
		$queryAr = array();
		$likeQuery = 'LIKE "'. "%". htmlentities($this->filter, ENT_QUOTES, 'utf-8') . "%". '"';
	
		// Get Heads
		if (!isset($this->heads)) {
			$this->getHeads();
		}
		if (count($this->heads) == 0) {
			return false;
		}
		
		foreach($this->heads as $head) {
			$queryAr[] = 'head_' . $head->id . ' ' . $likeQuery;
		}
	
		$query = implode(' OR ', $queryAr);
		//echo $query;

		return $query;  
	 }

	 /**
	  * Get Ordering and Creator
	  */
	 private function prepareData($rows) {
		$user   = JFactory::getUser();
		$ret = array();
		foreach ($rows as $row) {
			$ret['ordering'][] = $row->ordering;
			
			// See if the user created the row
			$uid = $user->get('id');
			
			if ($uid == $row->created_by) {
				$ret['createdRows'][] = $row->id;
			} else {
				$ret['createdRows'][] = null;
			}
		}
		$ret['ordering'] = implode('|', $ret['ordering']);
		
		if (count($ret['createdRows']) == 0) {
			$ret['createdRows'] = '';
		} else {
			$ret['createdRows'] = implode('|', $ret['createdRows']);
		}
		
		return $ret;
	 }
	 
	 private function parseRows($rows) {
	 	$rowCount = 0;
	 	$ret = array();
	 	
	 	foreach ($rows as $row) {
	 		// Iterate over the columns
	 		$colCount = 0;
	 		$ret[$rowCount]['id'] = $row->id;
	 		 
			foreach ($this->heads as $head) {
				//Get the column name
				$colName = 'head_'.$head->id;
				
				//Get the content of a cell
				$ret[$rowCount][$colCount] = trim($row->$colName);
				$ret[$rowCount][$colCount] = $this->parseCell($ret[$rowCount][$colCount], $colCount);
				
				//Insert a space character that the table doesn't collapse
				if ($ret[$rowCount][$colCount] == '') {
					$ret[$rowCount][$colCount] = '&nbsp;';
				}
				
				$colCount++;
			}
			
			$rowCount++;
	   }
	   
	   return $ret;
	}
	
	private function parseCell($cell, $colCount) {
		$this->getItem();
		$this->getHeads();
		$dt = $this->heads[$colCount]->datatype;		

		// Translating mySQL Date
		if ($dt == "date") {
			$cell = eteHelper::date_mysql_to_german($cell, $this->_item->dateformat);
		}
		// Translate Time
		else if ($dt == "time") {
			$cell = eteHelper::format_time($cell, $this->_item->timeformat);
		}
		//Handle Booleans
		else if ($dt == "boolean") {
			$cell = eteHelper::parseBoolean($cell);
		}
		// Handle Links
		else if ($dt == "link") {
			$cell = eteHelper::parseLink($cell, $this->_item->link_target, $this->_item->cellbreak);
		}
		// Handle Mails
		else if ($dt == "mail") {
			$cell = eteHelper::parseMail($cell, $this->_item->cellbreak);
		} 
		// Handle Floats
		else if ($dt == 'float') {
			$cell = eteHelper::parseFloat($cell, $this->_item->float_separator);
		}
		// Text and BBCODE Parsing
		else {
			// Don't show images in the module
			if ($this->getState('is_module', 0)) {
				$this->_item->bbcode_img = 0;
			}
			
			$cell = eteHelper::parseText($cell, $this->_item->bbcode, $this->_item->bbcode_img,
										 $this->_item->link_target, $this->_item->cellbreak);
		}
		
		// Highlighting search strings
		// Not used, because it destroys bb and html codes
		
		
		$cell = htmlspecialchars_decode($cell, ENT_NOQUOTES);
		
		return $cell;
	}

	/**
	 * Creates a new row through an Ajax-Request
	 */
	public function newRow() {
		//Get userid to store, who saved the row
		$user   = JFactory::getUser();
		$uid    = $user->get('id');
		
		//Add new row to the database
		$queryGetBiggestOrdering = 'SELECT (MAX(s.ordering) + 1) FROM #__eventtableedit_rows_' . $this->id .' AS s';
		$this->db->setQuery($queryGetBiggestOrdering);
		$newOrdering = $this->db->loadResult();

		// If no row is inserted, yet
		if (!$newOrdering) {
			$newOrdering = 0;
		}
		
		$query = 'INSERT INTO #__eventtableedit_rows_' . $this->id .
				 ' (ordering, created_by) VALUES (' . $newOrdering . ', ' . $uid . ')';
		//echo $query;
		$this->db->setQuery($query);
		$this->db->query();
				
		return $this->db->insertid() . '|' . $newOrdering;
	}
	
	/**
	 * Get the content of a single cell to edit it
	 * through an ajax request
	 * 
	 * @param int $id The table id
	 * @param int $rowId The id of the row
	 * @param int $cell The number of the edited cell 
	 */
	public function getCell($rowId, $cell) {
		$ret = array();
		
		$colName = $this->getColumnInfo($cell);
				
		$query = 'SELECT ' . $colName['head'] . ' AS content FROM #__eventtableedit_rows_' . $this->id .
				 ' WHERE id = ' . $rowId;
		//echo $query;
		$this->db->setQuery($query);
		$cell = $this->db->loadResult();

		// Handle Float separator
		$this->getItem();
		if ($colName['datatype'] == 'float') {
			$cell = eteHelper::parseFloat($cell, $this->_item->float_separator);
		}
		
		$ret[] = $cell;
		$ret[] = $colName['datatype'];
		
		return implode('|', $ret);
	}
	
	public function saveCell($rowId, $cell, $content) {
		// Get datatype and column name
		$colInfo = $this->getColumnInfo($cell);
		$datatype = $colInfo['datatype'];
		$headName = $colInfo['head'];
						
		$content = $this->prepareContentForDb($content, $datatype);
			
		$query = 'UPDATE #__eventtableedit_rows_' . $this->id .
				 ' SET ' . $headName . ' = ' . $content . ' WHERE id = ' . $rowId;
		
		$this->db->setQuery($query);
		$this->db->query();
		
		// Get the saved cell
		// To see if bbcode is used, the table params has to be loaded
		$this->getItem($this->id);
		$ret = explode("|", $this->getCell($rowId, $cell));
		$ret = $this->parseCell($ret[0], $cell);
		
		return $ret;
	}
	
	/**
	 * Prepare content before saving it in the database
	 */
	private function prepareContentForDb($content, $datatype) {
		$content = str_replace("\n", " ", $content);
		$content = str_replace("\r", " ", $content);
		$content = str_replace("\t", "", $content);
		$content = trim($content);
		$content = urldecode($content);
		//$content = mysql_escape_string($content);
		
		// If content is empty write a NULL
		if ($content != '') {
			$content = "'" . $content . "'";
		} else {
			$content = 'NULL';
		}
				
		return $content;
	}
	
	/**
	 *  Delete a row from the database
	 */
	public function deleteRow($rowId) {
		$query = 'DELETE FROM #__eventtableedit_rows_' . $this->id .
				 ' WHERE id = ' . $rowId;
		$this->db->setQuery($query);
		$this->db->query();
		
		return true;
	}
	
	/**
	 * Get information about a column
	 */
	private function getColumnInfo($cell) {
		$colQuery = 'SELECT CONCAT(\'head_\', a.id) AS head, datatype FROM #__eventtableedit_heads AS a' .
					' WHERE a.table_id = ' . $this->id .
					' ORDER BY a.ordering ASC' .
					' LIMIT ' . $cell . ', 1';
		//echo $colQuery;
		$this->db->setQuery($colQuery);
		
		return $this->db->loadAssoc();
	}
	
	public function saveOrder($rowIds, $order) {
		for ($a = 0; $a < count($rowIds); $a++) {
			$query = 'UPDATE #__eventtableedit_rows_' . $this->id .
					 ' SET ordering = ' . $order[$a] .
					 ' WHERE id = ' . $rowIds[$a];
			//echo $query;
			$this->db->setQuery($query);
			$this->db->query();		 
		}
	}
	
	/**
	 * Check if a user created a row himself and
	 * has the right to edit it
	 */
	public function checkAclOwnRow($rowId, $uid) {
		$query = 'SELECT IF(created_by = ' . $uid . ', 1, 0)' .
				 ' FROM #__eventtableedit_rows_' . $this->id .
				 ' WHERE id = ' . $rowId;
		$this->db->setQuery($query);
		return (int) $this->db->loadResult();
	}

	public function getDetails(){
		$db = JFactory::getDBO();
		$main  = JFactory::getApplication()->input;
		$tableid = 	$main->getInt('id', '');
		
		$select = "SELECT * FROM #__eventtableedit_heads WHERE table_id='".$tableid."'";
		$db->setQuery($select);
		$heads = $db->loadobjectList();


	}
}
