<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_SITE.'/components/com_eventtableedit/helpers/etetable.php';

class EventtableeditModelEtetable extends JModelList
{
    protected $_context = 'com_eventtableedit.etetable';
    protected $params;
    protected $heads;
    protected $db;
    protected $id;

    // Holds the filterstring standard ''
    protected $filter;
    protected $defaultSorting;

    public function __construct()
    {
        parent::__construct();

        // Load the parameters
        $app = JFactory::getApplication('site');
        $params = $app->getParams();

        $this->setState('params', $params);
        $this->params = $params;
        $main = $app->input;
        if ($main->getInt('table_id', '')) {
            $pk = $main->getInt('table_id', '');
        } else {
            $pk = $main->getInt('id', '');
        }

        $this->filter = '';

        $this->setState('is_module', 0);

        $this->db = $this->getDbo();
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Load state from the request.

        $app = JFactory::getApplication('site');
        $main = $app->input;
        if ($main->getInt('table_id', '')) {
            $pk = $main->getInt('table_id', '');
        } else {
            $pk = $main->getInt('id', '');
        }

        if ('' === $pk) {
            $pk = $this->id;
        }
        $this->setState('etetable.id', $pk);
		
	

        // filter.order
        $this->setState($pk.'list.ordering', $app->getUserStateFromRequest($pk.'.filter_order', 'filter_order', $this->getDefaultOrdering($pk), 'string'));
        $this->setState($pk.'list.direction', $app->getUserStateFromRequest($pk.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd'));

        $this->setState($pk.'filterstring', $app->getUserStateFromRequest($pk.'.filterstring', 'filterstring', '', 'string'));
        $this->setState($pk.'filterstring1', $app->getUserStateFromRequest($pk.'.filterstring1', 'filterstring1', '', 'string'));

        //$this->setState('list.start',$main->getInt('limitstart', '0'));
        $this->setState($pk.'list.start', $app->getUserStateFromRequest($pk.'.limitstart', 'limitstart', '', 'string'));
    }
	
	function getDefaultOrdering($pk){
		$query = $this->db->getQuery(true);

		$query->select('a.*,'
		.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug ');
		$query->from('#__eventtableedit_details AS a');
		$query->where('a.id = '.(int) $pk);

		// Filter by published state.
		$query->where('a.published = 1');

		$this->db->setQuery($query);

		$data = $this->db->loadObject();
		if(empty($data)){
			return;
		}
		$automate_sort = explode(',',$data->automate_sort_column);
		return $automate_sort[0];
	}

    /**
     * Build the orderby for the query.
     *
     * @return string $orderby portion of query
     *
     * @since	1.5
     */
    protected function _buildContentOrderBy()
    {
        $app = JFactory::getApplication('site');
        $params = $this->state->params;
        $itemid = $this->getState('etetable.id');
        $filter_order = $app->getUserStateFromRequest('com_eventtableedit.etetable.list.'.$itemid.'.filter_order', 'filter_order', '', 'string');
        $filter_order_Dir = $app->getUserStateFromRequest('com_eventtableedit.etetable.list.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        $orderby = ' ';

        if ($filter_order && $filter_order_Dir) {
            $orderby .= $filter_order.' '.$filter_order_Dir.', ';
        }

        return $orderby;
    }

    /**
     * Get total number of rows.
     */
    public function getTotal()
    {
        // Lets load the total nr if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->getRowsQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    /**
     * Method to get a pagination object.
     *
     * @return int
     */
    public function getPagination()
    {
        jimport('joomla.html.pagination');

        // Load only if there are heads
        if (!count($this->heads)) {
            return new JPagination(0, 0, 0);
        }

        // Lets load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('list.start'), $this->getState('list.limit'));
        }

        return $this->_pagination;
    }

    /**
     * Gets a list of contacts.
     *
     * @param array
     *
     * @return mixed Object or null
     */
    public function &getItem($pk = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('site');
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('etetable.id');

        if (null === @$this->_item) {
            $this->_item = [];
        }

        try {
            $query = $this->db->getQuery(true);

            $query->select($this->getState('item.select', 'a.*').','
            .' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug ');
            $query->from('#__eventtableedit_details AS a');
            $query->where('a.id = '.(int) $pk);

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

            $registry = new JRegistry();
            $registry->loadString($data->metadata);
            $data->metadata = $registry;

            // Settings for pagination
            // Default Pagebreak if not set
            /* $limit = $data->pagebreak;
            if($data->show_pagination==1){
            if ($limit == '') {
                $limit = 100;
            }
            $limit = $app->getUserStateFromRequest('com_eventtableedit.etetable.list.' . $pk . '.limit', 'limit', $limit);
            $this->setState('list.limit',$limit);
            }else{
            //$limit = $app->getUserStateFromRequest('com_eventtableedit.etetable.list.' . $pk . '.limit', 'limit', $limit);
            $this->setState('list.limit', '');
            } */

            $this->getACL($data);

            $this->_item = $data;
        } catch (JException $e) {
            $this->setError($e);
            $this->_item = false;
        }

        return $this->_item;
    }

    /**
     * Handle the acl for the table.
     */
    private function getACL(&$data)
    {
        $user = JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();

        $asset = 'com_eventtableedit.etetable.'.$data->id;

        $data->params->set('access-view', in_array($data->access, $groups));

        $data->params->set('access-edit', false);
        $data->params->set('access-add', false);
        $data->params->set('access-delete', false);
        $data->params->set('access-reorder', false);
        $data->params->set('access-create_admin', false);
        $data->params->set('access-ownRows', false);
        $data->params->set('access-csv', false);

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
        if ($user->authorise('core.csv', $asset)) {
            $data->params->set('access-csv', true);
        }

        // See if edit_own_rows is set to yes and if a user is logged in
        if ($data->edit_own_rows && 0 !== (int)$user->get('id')) {
            $data->params->set('access-ownRows', true);
        }
    }

    /**
     * Get the table heads.
     */
    public function getHeads()
    {
        if (null === $this->heads) {
            try {
                $query = $this->db->getQuery(true);

                $query->select($this->getState('item.select', 'a.*, CONCAT(\'head_\', a.id) AS head'));
                $query->from('#__eventtableedit_heads AS a');
                $query->where('a.table_id = '.(int) $this->state->get('etetable.id'));
                $query->order('a.ordering asc');

                $this->db->setQuery($query);
                $this->heads = $this->db->loadObjectList();

                if (empty($this->heads)) {
                    return null;
                }

                // Prepare Default Sorting
                $defSort = [];
                foreach ($this->heads as $row) {
                    // Prepare Default Sorting
                    if ('' !== $row->defaultSorting && ':' !== $row->defaultSorting) {
                        $split = explode(':', $row->defaultSorting);
                        $defSort[((int) ($split[0]) - 1)] = 'a.'.$row->head.' '.$split[1];
                    }
                }

                if (count($defSort)) {
                    $this->defaultSorting = implode(', ', $defSort);
                }

                return $this->heads;
            } catch (JException $e) {
                $this->setError($e);
                return false;
            }
        }
    }

    /**
     * Get the dropdown fields used in the table.
     */
    public function getDropdowns()
    {
        if (null === $this->heads) {
            $this->getHeads();
        }

        if (0 === (int)count($this->heads)) {
            return null;
        }

        $ret = [];
        $a = 0;
        foreach ($this->heads as $head) {
            $temp = explode('.', $head->datatype);

            if ('dropdown' === $temp[0]) {
                // Load Dropdown
                $ret[$a]['name'] = $this->loadDropdownName($temp[1]);

                // If the dropdown was deleted
                if (!count($ret[$a]['name'])) {
                    continue;
                }

                $ret[$a]['items'] = $this->loadDropdown($temp[1]);
                ++$a;
            }
        }

        return $ret;
    }

    private function loadDropdownName($id)
    {
        $query = $this->db->getQuery(true);
        $query->select('a.id, a.name');
        $query->from('#__eventtableedit_dropdowns AS a');
        $query->where('a.id = '.$id);

        $this->db->setQuery($query);
        return $this->db->loadAssoc();
    }

    private function loadDropdown($id)
    {
        $query = $this->db->getQuery(true);
        $query->select('a.*');
        $query->from('#__eventtableedit_dropdown AS a');
        $query->where('a.dropdown_id = '.$id);
        $query->order('a.id asc');

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get the table rows.
     */
    public function getRows()
    {
        try {
            $data = [];
            $tid = (int) $this->state->get('etetable.id');
            $query = $this->getRowsQuery();

            $data['rows'] = $this->_getList($query, $this->getState($tid.'list.start'), $this->getState($tid.'list.limit'));

            if (empty($data['rows'])) {
                $data['rows'] = null;
                $data['additional']['createdRows'] = null;
                $data['additional']['ordering'] = null;

                return $data;
            }
			
            $data['additional'] = $this->prepareData($data['rows']);
			
            $data['rows'] = $this->parseRows($data['rows']);
			
            return $data;
        } catch (JException $e) {
            $this->setError($e);
            return false;
        }
    }

    /**
     * Create the query for getting the Rows.
     */
    protected function getRowsQuery()
    {
        // Add the list ordering clause.
        $tid = (int) $this->state->get('etetable.id');
        $orderCol = $this->state->get($tid.'list.ordering');
        $orderDirn = $this->state->get($tid.'list.direction');

        $query = $this->db->getQuery(true);
        $query->select($this->getState('item.select', 'a.*'));
        $query->from('#__eventtableedit_rows_'.$tid.' AS a');

        // Use default sorting, if no manual sorting is used
        if ('a.ordering' === $orderCol && null !== $this->defaultSorting) {
            //$orderCol = $this->defaultSorting;
            //$orderDirn = 'ASC';
        }

        if ($this->_item->automate_sort) {
			$order_dir = explode(",",$this->_item->automate_sort_column);
            $orderCol = $order_dir[0]; $orderDirn = $order_dir[1];
        }
        if ($orderCol && $orderDirn) {
			$query->order($orderCol.' '.$orderDirn);
        }

        // Filter
        $filter = $this->filterRows();

        if (false !== $filter) {
            $ex = explode('~', $filter);

            $query->where($ex[0]);
            $query->where($ex[1]);
        }
        //echo $query."<br>";die;
        return $query;
    }

    /**
     * Filters the rows if there is a filter set in the frontend
     * Thanks to unimx who mostly coded the filter.
     */
    private function filterRows()
    {
        $main = JFactory::getApplication()->input;
        $filter1 = $this->getState('filterstring1', '');
        $this->filter = $this->getState('filterstring', '');
        $identifier = false;

        if ('' === $this->filter && '' === $filter1) {
            return false;
        }
        if ('' === $filter1) {
            $identifier = true;
        }

        $this->filter = str_replace('*', '%', $this->filter);
        $filter1 = str_replace('*', '%', $filter1);

        $filter1 = date('Y-m-d', strtotime($filter1));

        $queryAr = [];
        $queryAr1 = [];
        $likeQuery = 'LIKE "'.'%'.$this->filter.'%'.'"';
        $likeQuery1 = 'LIKE "'.'%'.$filter1.'%'.'"';

        // Get Heads
        if (!isset($this->heads)) {
            $this->getHeads();
        }
        if (0 === (int)count($this->heads)) {
            return false;
        }

        foreach ($this->heads as $head) {
            $queryAr[] = 'head_'.$head->id.' '.$likeQuery;
            $queryAr1[] = 'head_'.$head->id.' '.$likeQuery1;
        }

        $query = implode(' OR ', $queryAr);
        $query1 = implode(' OR ', $queryAr1);

        if ('' === $this->filter) {
            $query = '1=1';
        }

        if ($identifier) {
            $query1 = '1=1';
        }

        $query2 = $query.'~'.$query1;
        return $query2;
    }

    /**
     * Get Ordering and Creator.
     */
    private function prepareData($rows)
    {
        $user = JFactory::getUser();
        $ret = [];
        foreach ($rows as $row) {
            $ret['ordering'][] = $row->ordering;

            // See if the user created the row
            $uid = $user->get('id');

            if ($uid === $row->created_by) {
                $ret['createdRows'][] = $row->id;
            } else {
                $ret['createdRows'][] = null;
            }
        }
        $ret['ordering'] = implode('|', $ret['ordering']);

        if (0 === (int)count($ret['createdRows'])) {
            $ret['createdRows'] = '';
        } else {
            $ret['createdRows'] = implode('|', $ret['createdRows']);
        }

        return $ret;
    }

    private function parseRows($rows)
    {
        $rowCount = 0;
        $ret = [];

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
                if ('' === $ret[$rowCount][$colCount]) {
                    if ('four_state' !== $head->datatype) {
                        $ret[$rowCount][$colCount] = '&nbsp;';
                    }
                }

                ++$colCount;
            }
            //get timestamp
            $colName = 'timestamp';
            $ret[$rowCount][$colCount] = trim($row->$colName);
            $ret[$rowCount][$colCount] = strtotime($this->parseCell($ret[$rowCount][$colCount], $colCount));

            ++$rowCount;
        }

        return $ret;
    }

    private function parseCell($cell, $colCount)
    {
        $this->getItem();
        $this->getHeads();
        @$dt = $this->heads[$colCount]->datatype;

        // Translating mySQL Date
        if ('date' === $dt) {
			if($cell){
				$cell = eteHelper::date_mysql_to_german($cell, $this->_item->dateformat);
			}
            if ('' === $cell) {
                $cell = '<input value="0" type="hidden">';
            }
        }
        // Translate Time
        elseif ('time' === $dt) {
			if($cell){
				$cell = eteHelper::format_time($cell, $this->_item->timeformat);
			}
        }
        //Handle Booleans
        elseif ('boolean' === $dt) {
            $cell = eteHelper::parseBoolean($cell);
        }
        //Handle Four State
        elseif ('four_state' === $dt) {
            $cell = eteHelper::parseFourState($cell);
        }
        // Handle Links
        elseif ('link' === $dt) {
            $cell = eteHelper::parseLink($cell, $this->_item->link_target, $this->_item->cellbreak);
        }
        // Handle Mails
        elseif ('mail' === $dt) {
            $cell = eteHelper::parseMail($cell, $this->_item->cellbreak);
        }
        // Handle Floats
        elseif ('float' === $dt) {
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
     * Creates a new row through an Ajax-Request.
     */
    public function newRow($tableId = 0)
    {
        $this->id = $tableId;
        //Get userid to store, who saved the row
        $user = JFactory::getUser();
        $uid = $user->get('id');

        //Add new row to the database
        $queryGetBiggestOrdering = 'SELECT (MAX(s.ordering) + 1) FROM #__eventtableedit_rows_'.$this->id.' AS s';
        $this->db->setQuery($queryGetBiggestOrdering);
        $newOrdering = $this->db->loadResult();

        // If no row is inserted, yet
        if (!$newOrdering) {
            $newOrdering = 0;
        }

        $query = 'INSERT INTO #__eventtableedit_rows_'.$this->id.
                 ' (ordering, created_by) VALUES ('.$newOrdering.', '.$uid.')';
        //echo $query;
        $this->db->setQuery($query);
        $this->db->query();
        $inserttempid = $this->db->insertid();

        $selectallrecords = 'SELECT COUNT(id) AS row FROM #__eventtableedit_rows_'.$this->id;
        $this->db->setQuery($selectallrecords);
        $rwo = $this->db->loadResult();

        $updatecol = "UPDATE `#__eventtableedit_details` SET row='".$rwo."' WHERE id='".$this->id."'";
        $this->db->setQuery($updatecol);
        $this->db->query();

        return  $inserttempid.'|'.$newOrdering;
    }

    /**
     * Get the content of a single cell to edit it
     * through an ajax request.
     *
     * @param int $id    The table id
     * @param int $rowId The id of the row
     * @param int $cell  The number of the edited cell
     */
    public function getCell($tableId = 0, $rowId, $cell)
    {
        $ret = [];
        $this->id = $tableId;
        $colName = $this->getColumnInfo($cell);
        if ($table = $this->checkAppointmentAndSession()) {
            $query = 'SELECT '.$colName['head'].' AS content FROM #__eventtableedit_rows_'.$table.
                 ' WHERE id = '.$rowId;
        } else {
            $query = 'SELECT '.$colName['head'].' AS content FROM #__eventtableedit_rows_'.$this->id.
                 ' WHERE id = '.$rowId;
        }

        //echo $query;die;

        $this->db->setQuery($query);
        $cell = $this->db->loadResult();

        if ('text' === $colName['datatype']) {
            $breaks = ['<br />', '<br>', '<br/>', '<br /> ', '<br> ', '<br/> '];
            $cell = str_ireplace($breaks, "\n", $cell);
        }
        // Handle Float separator
        $this->getItem();
        if ('float' === $colName['datatype']) {
            $cell = eteHelper::parseFloat($cell, $this->_item->float_separator);
        }
        if ('date' === $colName['datatype']) {
            $cell = eteHelper::date_mysql_to_german_to($cell, $this->_item->dateformat);
        }
        $ret[] = $cell;
        $ret[] = $colName['datatype'];

        return implode('|', $ret);
    }

    public function getCell_save($rowId, $cell)
    {
        $ret = [];

        $colName = $this->getColumnInfo($cell);
        if ($table = $this->checkAppointmentAndSession()) {
            $query = 'SELECT '.$colName['head'].' AS content FROM #__eventtableedit_rows_'.$table.
                     ' WHERE id = '.$rowId;
        } else {
            $query = 'SELECT '.$colName['head'].' AS content FROM #__eventtableedit_rows_'.$this->id.
                 ' WHERE id = '.$rowId;
        }
        //echo $query;die;
        $this->db->setQuery($query);
        $cell = $this->db->loadResult();
        //$breaks = array("<br />","<br>","<br/>");
        //$cell = str_ireplace($breaks, "\r\n", $cell);

        // Handle Float separator
        $this->getItem();
        if ('float' === $colName['datatype']) {
            $cell = eteHelper::parseFloat($cell, $this->_item->float_separator);
        }

        $ret[] = $cell;
        $ret[] = $colName['datatype'];

        return implode('|', $ret);
    }

    public function saveCell($rowId, $cell, $content, $tableId = 0)
    {
        $this->id = $tableId;
        // Get datatype and column name
        $colInfo = $this->getColumnInfo($cell);
        $datatype = $colInfo['datatype'];
        $headName = $colInfo['head'];
        $currentTime = new DateTime();
        $timestamp = $currentTime->format('Y-m-d H:i:s');

        if ('date' === $datatype) {
            $content = eteHelper::date_german_to_mysql($content);
        }

        $content = $this->prepareContentForDb($content, $datatype);
		
        if ('text' === $colInfo['datatype']) {
            $breaks = ['<br />', '<br>', '<br/>', '<br /> ', '<br> ', '<br/> '];
            $content = str_ireplace($breaks, '<br />', $content);
        }
        if ($table = $this->checkAppointmentAndSession()) {
            $breaks = ['<br />', '<br>', '<br/>', '<br /> ', '<br> ', '<br/> '];
            $content = str_ireplace($breaks, '', $content);
            $query = 'UPDATE #__eventtableedit_rows_'.$table.
                     ' SET '.$headName.' = '.$content.", timestamp = '".$timestamp."' WHERE id = ".$rowId;
        } else {
            $query = 'UPDATE #__eventtableedit_rows_'.$this->id.
                     ' SET '.$headName.' = '.$content.", timestamp = '".$timestamp."' WHERE id = ".$rowId;
        }
		
        $this->db->setQuery($query);
        $this->db->query();
	
        // Get the saved cell
        // To see if bbcode is used, the table params has to be loaded
        $this->getItem($this->id);
        $ret = explode('|', $this->getCell_save($rowId, $cell));
        $ret = $this->parseCell($ret[0], $cell);

        $result[0] = $ret;
        $result[1] = '<input type="hidden" value="'.strtotime($timestamp).'">';

        return $result;
    }

    /**
     * Prepare content before saving it in the database.
     */
    public function prepareContentForDb($content, $datatype)
    {
        $content = str_replace("\n", ' ', $content);
        $content = str_replace("\r", ' ', $content);
        $content = str_replace("\t", '', $content);
        $content = trim($content);
        $content = urldecode($content);

        // If content is empty write a NULL
        if ('' !== $content) {
            $content = "'".$content."'";
        } else {
            $content = 'NULL';
        }

        return $content;
    }

    /**
     *  Delete a row from the database.
     */
    public function deleteRow($rowId, $tableId = 0)
    {
        $this->id = $tableId;
        $query = 'DELETE FROM #__eventtableedit_rows_'.$this->id.
                 ' WHERE id = '.$rowId;
        $this->db->setQuery($query);
        $this->db->query();

        $selectallrecords = 'SELECT COUNT(id) AS row FROM #__eventtableedit_rows_'.$this->id;
        $this->db->setQuery($selectallrecords);
        $rwo = $this->db->loadResult();

        $updatecol = "UPDATE `#__eventtableedit_details` SET row='".$rwo."' WHERE id='".$this->id."'";
        $this->db->setQuery($updatecol);
        $this->db->query();

        return true;
    }

    /**
     * Get information about a column.
     */
    private function getColumnInfo($cell)
    {
        if ($table = $this->checkAppointmentAndSession()) {
            $colQuery = 'SELECT CONCAT(\'head_\', a.id) AS head, datatype FROM #__eventtableedit_heads AS a'.
                        ' WHERE a.table_id = '.$table.
                        ' ORDER BY a.ordering ASC'.
                        ' LIMIT '.$cell.', 1';
        } else {
            $colQuery = 'SELECT CONCAT(\'head_\', a.id) AS head, datatype FROM #__eventtableedit_heads AS a'.
                    ' WHERE a.table_id = '.$this->id.
                    ' ORDER BY a.ordering ASC'.
                    ' LIMIT '.$cell.', 1';
        }
        //echo $colQuery;die;
        $this->db->setQuery($colQuery);

        return $this->db->loadAssoc();
    }

    public function saveOrder($rowIds, $order, $id = null)
    {
        if ($id) {
            $this->id = $id;
        }
        for ($a = 0; $a < count($rowIds); ++$a) {
            $query = 'UPDATE #__eventtableedit_rows_'.$this->id.
                     ' SET ordering = '.$order[$a].
                     ' WHERE id = '.$rowIds[$a];

            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Check if a user created a row himself and
     * has the right to edit it.
     */
    public function checkAclOwnRow($rowId, $uid)
    {
        $query = 'SELECT IF(created_by = '.$uid.', 1, 0)'.
                 ' FROM #__eventtableedit_rows_'.$this->id.
                 ' WHERE id = '.$rowId;
        $this->db->setQuery($query);
        return (int) $this->db->loadResult();
    }

    public function getColumnInfoPublic($cell)
    {
        $app = JFactory::getApplication('site');
        $main = $app->input;
        $id = $main->getInt('id', '');
        $colQuery = 'SELECT CONCAT(\'head_\', a.id) AS head, datatype FROM #__eventtableedit_heads AS a'.
                    ' WHERE a.table_id = '.$id.
                    ' ORDER BY a.ordering ASC'.
                    ' LIMIT '.$cell.', 1';
        //echo $colQuery;
        $this->db->setQuery($colQuery);

        return $this->db->loadAssoc();
    }

    public function checkAppointmentAndSession()
    {
        $query = 'SELECT * '.
                 ' FROM #__eventtableedit_details'.
                 ' WHERE id = '.$this->id;

        $this->db->setQuery($query);
        $table = $this->db->loadObject();
        if ($table->normalorappointment) {
            $session = JFactory::getSession();
            $corresponding_table = $session->get('corresponding_table');
            if ($corresponding_table) {
                return $corresponding_table;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
	
	function checkTable($tableId){
		$query = 'SELECT * '.
                 ' FROM #__eventtableedit_details'.
                 ' WHERE id = '.$tableId;

        $this->db->setQuery($query);
        $table = $this->db->loadObject();
		if(!empty($table)){
			$this->setState('etetable.id', $table->id);
			return $table;
		}else{
			return false;
		}
	}
	
}
