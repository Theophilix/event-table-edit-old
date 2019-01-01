<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class EventtableeditModelDropdown extends JModelAdmin {
	protected $item;
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_eventtableedit');
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_eventtableedit');
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Dropdowns', $prefix = 'EventtableeditTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Dummy Method to get the row form, because it's abstract
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Get the form.
		$form = $this->loadForm('com_eventtableedit.dropdowns', 'dropdowns', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	$pk	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		$this->item = parent::getItem($pk);

		return $this->item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_eventtableedit.edit.dropdown.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	/**
	 * Build an SQL query to load the single dropdowns
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getDropdowns() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState('list.select', 'a.*')
		);
		$query->from('#__eventtableedit_dropdown AS a');
		
		if ($this->item->id == null) {
				$this->item-> id = 0;
		}
		$query->where('dropdown_id = ' . $this->item->id);
		$query->order('a.id', 'asc');
		//echo $query;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		return $rows;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param	JTable	$table
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name		= htmlspecialchars_decode($table->name, ENT_QUOTES);
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable('dropdowns');
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
		$isNew		= true;
		
		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Load the row if saving an existing category.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}

		// Bind the data to the dropdowns table
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, $table, $isNew));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, $table, $isNew));

		$this->setState($this->getName().'.id', $table->id);
		
		$this->saveSingleDropdowns();

		return true;
	}
	
	/**
	 * With the save function only the dropdown name and meta-information is saved
	 * This function saves the real dropdown points
	 */
	private function saveSingleDropdowns() {
		// Initialise variables;
		$db = $this->getDbo();
		$input  =  JFactory::getApplication()->input;
		$postget = $input->getArray($_REQUEST);
		$name = $postget['dropdownName'];
		$dropdown_id = (int) $this->getState($this->getName().'.id');
		
		// Delete old dropdowns
		$query = 'DELETE FROM #__eventtableedit_dropdown' .
				 ' WHERE dropdown_id = ' . $dropdown_id;
		$db->setQuery($query);
		$db->query();
		
		for ($a = 0; $a < count($name); $a++) {
			// Bind the data to the dropdowns table
			$table		= $this->getTable('dropdown');
			
			$data = array();
			$data['dropdown_id'] 	= $dropdown_id;
			$data['name'] 		= addslashes($name[$a]);
			
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}
	}
}
