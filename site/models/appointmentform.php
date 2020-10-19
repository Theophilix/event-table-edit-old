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

class EventtableeditModelappointmentform extends JModelList
{
    protected $_context = 'com_eventtableedit.etetable';
    protected $params;
    protected $heads;
    protected $db;
    protected $id;

    public function __construct()
    {
        parent::__construct();

        // Load the parameters
        $app = JFactory::getApplication('site');
        $params = $app->getParams();

        $this->setState('params', $params);
        $this->params = $params;
        $main = $app->input;
        $this->id = $main->getInt('id', '');
        $this->option_id = $main->getInt('id', '');
        $session = JFactory::getSession();
        $corresponding_table = $session->get('corresponding_table');
        if ($corresponding_table) {
            $this->option_id = $corresponding_table;
        }

        $this->setState('is_module', 0);

        $this->db = $this->getDbo();
    }

    public function getCorrespondingTableName($corresponding_table)
    {
        $db = JFactory::getDbo();
        $db->setQuery("SELECT * FROM #__eventtableedit_details WHERE id = '".$corresponding_table."'");
        $result = $db->loadObject();
        return $result->name;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Load state from the request.
        $app = JFactory::getApplication('site');
        $main = $app->input;
        $pk = $this->id;

        if ('' === $pk) {
            $pk = $this->id;
        }
        $this->setState('appointments.id', $pk);

        $this->setState('list.start', $main->getInt('limitstart', '0'));
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
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('appointments.id');

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
                $query->where('a.table_id = '.$this->option_id);
                $query->order('a.ordering asc');

                $this->db->setQuery($query);
                $this->heads = $this->db->loadObjectList();

                if (empty($this->heads)) {
                    return null;
                }

                return $this->heads;
            } catch (JException $e) {
                $this->setError($e);
                return false;
            }
        }
    }

    /**
     * Get the table rows.
     */
    public function getRows()
    {
        try {
            $data = [];

            $query = $this->getRowsQuery();
            $data['rows'] = $this->_getList($query, $this->getState('list.start'), $this->getState('list.limit'));

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
        //$tid = $this->state->get('appointments.id');
        $tid = $this->option_id;

        $query = $this->db->getQuery(true);
        $query->select($this->getState('item.select', 'a.*'));
        $query->from('#__eventtableedit_rows_'.$tid.' AS a');

        return $query;
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
                    $ret[$rowCount][$colCount] = '&nbsp;';
                }

                ++$colCount;
            }

            ++$rowCount;
        }

        return $ret;
    }

    private function parseCell($cell, $colCount)
    {
        $this->getItem();
        $this->getHeads();
        $dt = $this->heads[$colCount]->datatype;

        // Translating mySQL Date
        if ('date' === $dt) {
            $cell = eteHelper::date_mysql_to_german($cell, $this->_item->dateformat);
        }
        // Translate Time
        elseif ('time' === $dt) {
            $cell = eteHelper::format_time($cell, $this->_item->timeformat);
        }
        //Handle Booleans
        elseif ('boolean' === $dt) {
            $cell = eteHelper::parseBoolean($cell);
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
    public function newRow()
    {
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

        return $this->db->insertid().'|'.$newOrdering;
    }

    /**
     * Get the content of a single cell to edit it
     * through an ajax request.
     *
     * @param int $id    The table id
     * @param int $rowId The id of the row
     * @param int $cell  The number of the edited cell
     */
    public function getCell($rowId, $cell)
    {
        $ret = [];

        $colName = $this->getColumnInfo($cell);

        $query = 'SELECT '.$colName['head'].' AS content FROM #__eventtableedit_rows_'.$this->id.
                 ' WHERE id = '.$rowId;
        //echo $query;
        $this->db->setQuery($query);
        $cell = $this->db->loadResult();

        // Handle Float separator
        $this->getItem();
        if ('float' === $colName['datatype']) {
            $cell = eteHelper::parseFloat($cell, $this->_item->float_separator);
        }

        $ret[] = $cell;
        $ret[] = $colName['datatype'];

        return implode('|', $ret);
    }

    public function saveCell($rowId, $cell, $content)
    {
        // Get datatype and column name
        $colInfo = $this->getColumnInfo($cell);
        $datatype = $colInfo['datatype'];
        $headName = $colInfo['head'];

        $content = $this->prepareContentForDb($content, $datatype);

        $query = 'UPDATE #__eventtableedit_rows_'.$this->id.
                 ' SET '.$headName.' = '.$content.' WHERE id = '.$rowId;

        $this->db->setQuery($query);
        $this->db->query();

        // Get the saved cell
        // To see if bbcode is used, the table params has to be loaded
        $this->getItem($this->id);
        $ret = explode('|', $this->getCell($rowId, $cell));
        $ret = $this->parseCell($ret[0], $cell);

        return $ret;
    }

    /**
     * Prepare content before saving it in the database.
     */
    private function prepareContentForDb($content, $datatype)
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
    public function deleteRow($rowId)
    {
        $query = 'DELETE FROM #__eventtableedit_rows_'.$this->id.
                 ' WHERE id = '.$rowId;
        $this->db->setQuery($query);
        $this->db->query();

        return true;
    }

    /**
     * Get information about a column.
     */
    private function getColumnInfo($cell)
    {
        $colQuery = 'SELECT CONCAT(\'head_\', a.id) AS head, datatype FROM #__eventtableedit_heads AS a'.
                    ' WHERE a.table_id = '.$this->id.
                    ' ORDER BY a.ordering ASC'.
                    ' LIMIT '.$cell.', 1';
        //echo $colQuery;
        $this->db->setQuery($colQuery);

        return $this->db->loadAssoc();
    }

    public function saveOrder($rowIds, $order)
    {
        for ($a = 0; $a < count($rowIds); ++$a) {
            $query = 'UPDATE #__eventtableedit_rows_'.$this->id.
                     ' SET ordering = '.$order[$a].
                     ' WHERE id = '.$rowIds[$a];
            //echo $query;
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

    public function getDetails()
    {
        $db = JFactory::getDBO();
        $main = JFactory::getApplication()->input;
        $tableid = $main->getInt('id', '');

        $select = "SELECT * FROM #__eventtableedit_heads WHERE table_id='".$tableid."'";
        $db->setQuery($select);
        $heads = $db->loadobjectList();
    }
}
