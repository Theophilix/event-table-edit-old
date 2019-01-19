<?php
/**
 * $Id:$
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
defined( '_JEXEC' ) or die;

class Datatypes {
	private $dropdowns;
	
	function __construct() {
		$this->dropdowns = $this->getDropdowns();
	}
	
	/**
	 * Get the possible dropdowns
	 */
	private function getDropdowns() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('a.id, a.name');
		$query->from('#__eventtableedit_dropdowns AS a');
		$query->where('a.published = 1');
		$query->order('a.ordering', 'asc');
		$db->setQuery($query);
		
		$ret = $db->loadObjectList();
		return $ret;
	}
	
	public function getDatatypes() {
		$ret = array();
		$ret[] = 'text';
		$ret[] = 'date';
		$ret[] = 'time';
		$ret[] = 'int';
		$ret[] = 'float';
		$ret[] = 'boolean';
		$ret[] = 'link';
		$ret[] = 'mail';
		$ret[] = 'four_state';
		
		// Add the dropdowns
		foreach($this->dropdowns as $dropdown) {
			$ret[] = 'dropdown.' . $dropdown->id;
		}
		
		return $ret;
	}
	
	public function getDatatypesDesc() {
		$ret = array();
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_TEXT');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_DATE');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_TIME');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_INT');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_FLOAT');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_BOOLEAN');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_LINK');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_MAIL');
		$ret[] = JTEXT::_('COM_EVENTTABLEEDIT_DATATYPE_FOUR_STATE');
		
		// Add the dropdowns
		foreach($this->dropdowns as $dropdown) {
			$ret[] = $dropdown->name;
		}
		
		return $ret;
	}
	
	public function createSelectList() {
		$values = $this->getDatatypes();
		$text = $this->getDatatypesDesc();
		$elem = array();
		
		for($a = 0; $a < count($values); $a++) {
			$elem[] = JHTML::_('select.option', $values[$a], $text[$a]);
		}
		
		return JHTML::_('select.genericlist', $elem, 'datatypesList[]', '', 'value', 'text', 0);
	}
	
	/**
	 * Maps the datatypes
	 */
	public static function mapDatatypes($datatype) {
		switch ($datatype) {
			case 'int': return 'INT(11)';
			case 'float': return 'FLOAT';
			case 'date': return 'DATE';
			case 'time': return 'TIME';
			case 'four_state': return 'varchar(1)';
			
			default: return 'TEXT';
		}
	}
}

?>