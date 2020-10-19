<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class EventtableeditTableAppointmenttable extends JTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__eventtableedit_details', 'id', $db);
    }

    /**
     * Overloaded bind function.
     *
     * @param	array		Named array
     *
     * @return string|null null is operation was satisfactory, otherwise returns an error
     *
     * @since	1.6
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }
        /*if (is_array($array['switcher_mode']))
        {
            $array['switcher_mode'] = json_encode($array['switcher_mode']);
        }*/

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Stores a etetable.
     *
     * @param	bool	true to update fields even if they are null
     *
     * @return bool true on success, false on failure
     *
     * @since	1.6
     */
    public function store($updateNulls = false)
    {
        // Transform the params field
        if (@is_array($this->params)) {
            $registry = new JRegistry();
            $registry->loadArray($this->params);
            $this->params = (string) $registry;
        }

        $date = JFactory::getDate();
        $user = JFactory::getUser();

        // Verify that the alias is unique
        $table = JTable::getInstance('Etetable', 'EventtableeditTable');
        if ($table->load(array('alias' => $this->alias)) && ($table->id !== $this->id || 0 === $this->id)) {
            $this->setError(JText::_('COM_EVENTTABLEEDIT_ERROR_UNIQUE_ALIAS'));
            return false;
        }

        // Delete # from tablecolor
        $this->tablecolor1 = str_replace('#', '', $this->tablecolor1);
        $this->tablecolor2 = str_replace('#', '', $this->tablecolor2);

        // Attempt to store the data.
        return parent::store($updateNulls);
    }

    /**
     * Overloaded check function.
     *
     * @return bool
     *
     * @see JTable::check
     * @since 1.5
     */
    public function check()
    {
        /* check for valid name */
        if ('' === trim($this->name)) {
            $this->setError(JText::_('COM_EVENTTABLEEDIT_WARNING_PROVIDE_VALID_NAME'));
            return false;
        }

        /** check for existing name */
        $query = 'SELECT id FROM #__eventtableedit_details WHERE name = '.$this->_db->Quote($this->name);
        $this->_db->setQuery($query);

        $xid = intval($this->_db->loadResult());
        if ($xid && $xid !== intval($this->id)) {
            $this->setError(JText::_('COM_EVENTTABLEEDIT_SAME_NAME'));
            return false;
        }

        if (empty($this->alias)) {
            $this->alias = $this->name;
        }
        $this->alias = JApplication::stringURLSafe($this->alias);
        if ('' === trim(str_replace('-', '', $this->alias))) {
            $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        // Check the linecolors
        if (strlen($this->tablecolor1) > 7 || strlen($this->tablecolor2) > 7) {
            $this->setError(JText::_('COM_EVENTTABLEEDIT_TABLECOLOR_WRONG_FORMAT'));
            return false;
        }

        return true;
    }

    /**
     * Overload the delete function to delete the _rows table and the heads.
     */
    public function delete($pk = null)
    {
        parent::delete($pk);

        // Delete heads
        $db = $this->getDbo();
        $query = 'DELETE FROM #__eventtableedit_heads'.
                 ' WHERE table_id = '.$pk;
        $db->setQuery($query);
        $db->query();

        // Delete _rows_$pk table
        $query = 'DROP TABLE IF EXISTS #__eventtableedit_rows_'.$pk;
        $db->setQuery($query);
        $db->query();

        return true;
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return string
     *
     * @since       1.6
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_eventtableedit.appointmenttable.'.(int) $this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @return string
     *
     * @since       1.6
     */
    protected function _getAssetTitle()
    {
        return $this->name;
    }

    /*
     * Get the parent asset id for the record
     *
     * @return      int
     * @since       1.6
     */
   /* protected function _getAssetParentId()
    {
            $asset = JTable::getInstance('Asset');
            $asset->loadByName('com_eventtableedit');
            return $asset->id;
    }*/
}
