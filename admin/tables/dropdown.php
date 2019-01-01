<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class EventtableeditTableDropdown extends JTable {
	public function __construct(& $db) {
		parent::__construct('#__eventtableedit_dropdown', 'id', $db);
	}
	
	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		/** check for valid name */
		if (trim($this->name) == '') {
			$this->setError(JText::_('COM_EVENTTABLEEDIT_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}
		/** check for existing name */
		$query = 'SELECT id FROM #__eventtableedit_dropdown '.
				 ' WHERE name = '.$this->_db->Quote($this->name);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::_('COM_EVENTTABLEEDIT_ERROR_SAME_NAME'));
			return false;
		}
	}
}
