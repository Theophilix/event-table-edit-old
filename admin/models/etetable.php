<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

class EventtableeditModelEtetable extends JModelAdmin {
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
		return parent::canDelete($record);
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
		return parent::canEditState($record);
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
	public function getTable($type = 'Etetable', $prefix = 'EventtableeditTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
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
		$form = $this->loadForm('com_eventtableedit.etetable', 'etetable', array('control' => 'jform', 'load_data' => $loadData));
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
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();
		}

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	public function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_eventtableedit.edit.etetable.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			//$data->switcher_mode = json_decode($data->switcher_mode);
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param	JTable	$table
	 *
	 * @return	void
	 * @since	1.6
	 */
	/*protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name		= htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->name);
		}
	}*/
	
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
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
		$isNew		= true;
		
		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Load the row if saving an existing category.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}

		// Alter the title for save as copy
		if (!$isNew && $data['id'] == 0) {
			$m = null;
			$data['alias'] = '';
			if (preg_match('#\((\d+)\)$#', $table->name, $m)) {
				$data['name'] = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $table->name);
			}
			else {
				$data['name'] .= ' (2)';
			}
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Bind the rules.
		if (isset($data['rules'])) {
			$rules = new JRules($data['rules']);
			$table->setRules($rules);
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

		

		if($data['id'] == 0 && @$data['temps'] == 1){
			$this->createRowsTable($table->id);
			if($data['col'] > 0){
				$db = JFactory::getDBO();
				for ($i=1; $i <= $data['col']; $i++) { 
					$nameofhead = 'Head'.$i;
					$ins = "INSERT INTO #__eventtableedit_heads (`id`,`table_id`,`name`,`datatype`,`ordering`) VALUES ('',".$table->id.",'".$nameofhead."','text',".$i.")";	
					$db->setQuery($ins);
					$db->query();
					$newId = $db->insertid();
					$this->updateRowsTable('0',$newId,$table->id);
				}
			}
			$this->Insertemptyrow($table->id,$data['row']);
		}
		
		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, $table, $isNew));

		$this->setState($this->getName().'.id', $table->id);
		
		// Store id in session
		$app = JFactory::getApplication();
		$app->setUserState("etetable.id", $table->id);

		return true;
	}
	public function saveXml($data)
	{
		
		$data['adminemailsubject']  = html_entity_decode($data['adminemailsubject']);
		$data['useremailsubject']   = html_entity_decode($data['useremailsubject']);
		$data['useremailtext'] 		= html_entity_decode($data['useremailtext']);
		$data['adminemailtext']     = html_entity_decode($data['adminemailtext']);

		$data['pretext'] 		= html_entity_decode($data['pretext']);
		$data['aftertext']     = html_entity_decode($data['aftertext']);

		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
		$isNew		= true;
		$db = JFactory::getDBO();
		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Load the row if saving an existing category.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}
		
		
		
	
		// Alter the title for save as copy
		if (!$isNew && $data['id'] == 0) {
			$m = null;
			$data['alias'] = '';
			if (preg_match('#\((\d+)\)$#', $table->name, $m)) {
				$data['name'] = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $table->name);
			}
			else {
				$data['name'] .= ' (2)';
			}
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		$data['rules'] = json_decode($data['rules'],true);
		// Bind the rules.
		if (isset($data['rules'])) {
			$rules = new JRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		/*if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}*/
		
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

		

		if($data['id'] == 0 && @$data['temps'] == 1){
			$this->createRowsTable($table->id);
			if($data['col'] > 0){
				
				for ($i=1; $i <= $data['col']; $i++) { 
					$nameofhead = 'Head'.$i;
					$ins = "INSERT INTO #__eventtableedit_heads (`id`,`table_id`,`name`,`datatype`,`ordering`) VALUES ('',".$table->id.",'".$nameofhead."','text',".$i.")";	
					$db->setQuery($ins);
					$db->query();
					$newId = $db->insertid();
					$this->updateRowsTable('0',$newId,$table->id);
				}
			}
			$this->Insertemptyrow($table->id,$data['row']);
		}else{
			$this->createRowsTable($table->id);
			if(count($data['headdata']['linehead']) > 0){
				
				for ($i=0; $i <= count($data['headdata']['linehead'])-1; $i++) {
					$temp = $data['headdata']['linehead'][$i]; 
					$nameofhead = $temp['name'];
					if (strtolower($nameofhead) === 'timestamp' || strtolower($temp['headtable']) === 'timestamp')
						continue;
					$datatype = $temp['datatype'];
					$ins = "INSERT INTO #__eventtableedit_heads (`id`,`table_id`,`name`,`datatype`,`ordering`) VALUES ('',".$table->id.",'".$nameofhead."','".$datatype."',".$i.")";	
					$db->setQuery($ins);
					$db->query();
					$newId = $db->insertid();
					$this->updateRowsTablefromxml('0',$newId,$table->id,$datatype);
				}
			}
			$this->Insertrowfromxml($table->id,$data['rowdata']['linerow'],$data['checkfun']);

		}
	
		return $table->id;
	}
	public function Insertemptyrow($id,$emptyrow){
		$db = JFactory::getDBO();
		$select = 'SELECT id FROM #__eventtableedit_heads WHERE table_id="'.$id.'"';

		$db->setQuery($select);
		$filedsname = $db->loadColumn();

		$headdefine = '';
		for ($x=0; $x < count($filedsname); $x++) { 
			$headdefine .= '`head_'.$filedsname[$x].'`,';
		}
	
		$headdefine .= '`timestamp`,';
		
		$headdefine = rtrim($headdefine,',');
		
		$aemptyda = count($filedsname);
		
		$nbspstring = '';
		for ($j=0; $j < $aemptyda; $j++) { 
			$nbspstring .= "'&nbsp',";
		}

		$nbspstring .= "'obj_timestamp_obj'";
		$currentTime = new DateTime();
		
		for ($z=0; $z < $emptyrow; $z++) { 
			$nbspstring = rtrim($nbspstring,',');
			$currentTime->modify("+1 second"); 
			$timestamp = $currentTime->format("Y-m-d H:i:s");
			$valueString = str_replace('obj_timestamp_obj', $timestamp, $nbspstring);

			$insert = "INSERT INTO `#__eventtableedit_rows_" . $id . "` ($headdefine) VALUES ($valueString)";

			$db->setQuery($insert);
			$db->query();
		}
		

	}

	public function Insertrowfromxml($id,$prerow,$checkfun){

		
		$db = JFactory::getDBO();
		$select = 'SELECT id FROM #__eventtableedit_heads WHERE table_id="'.$id.'" ORDER BY id ASC';
		$db->setQuery($select);
		$filedsname = $db->loadColumn();
		
		$headdefine = '`ordering`,`created_by`,';

		$beginCol = 2;
		$haveTimestamp = false;
		if (isset($prerow[0]) && $prerow[0]['timestamp']) {
			$beginCol = 2;
			$haveTimestamp = true;
		}

		for ($x=0; $x < count($filedsname); $x++) {

			$headdefine .= '`head_'.$filedsname[$x].'`,';
		}
		$headdefine .= '`timestamp`,';
		$headdefine = rtrim($headdefine,',');
		$aemptyda = count($filedsname);
		$currentTime = new DateTime();



		for ($z=0; $z < count($prerow); $z++) { 
			$reocrddata = array_values($prerow[$z]);
			$nbspstring = '';
				
			$final_record = count($reocrddata);
			if($haveTimestamp){
				$final_record = count($reocrddata)-1;
			}
			for ($p=$beginCol; $p < $final_record; $p++) {

				$checkstring = str_replace("'", "\'",$reocrddata[$p]);
				if(is_array($checkstring)){
					if($checkfun == 1){
					$checkstring = 'free';
					}else{
					$checkstring = '';
					}
				}
				$nbspstring .= '"'.$checkstring.'",';
			}
			$nbspstring .= "'obj_timestamp_obj'";
			$nbspstring = rtrim($nbspstring,',');
			
			if ($haveTimestamp === true) {
				if (isset($reocrddata[count($reocrddata)-1]) && $reocrddata[count($reocrddata)-1] != '') {
					$reocrddata[count($reocrddata)-1] = str_replace("'", '', $reocrddata[count($reocrddata)-1]);
					$date = str_replace('.', '-', $reocrddata[count($reocrddata)-1]);
					$timestamp = date('Y-m-d H:i:s', strtotime($date));
				} else {
					$currentTime->modify("+1 second");
					$timestamp = $currentTime->format("Y-m-d H:i:s");
				}

			} else {
				$currentTime->modify("+1 second");
				$timestamp = $currentTime->format("Y-m-d H:i:s");
			}
			if ($timestamp == '1970-01-01 00:00:00') {
				$currentTime->modify("+1 second");
				$timestamp = $currentTime->format("Y-m-d H:i:s");
			}
			$valueString = str_replace('obj_timestamp_obj', $timestamp, $nbspstring);
			//$nbspstring = rtrim($nbspstring,',');

			 $insert = "INSERT INTO `#__eventtableedit_rows_" . $id . "` ($headdefine) VALUES ($valueString)";
			
			$db->setQuery($insert);
			$db->query();
		}
		
	}

	private function createRowsTable($id) {
		$db = JFactory::getDBO();
		// Need to use getPrefix because of a Joomla Bug
		// within quotes #__ is not replaced
		$query = 'SHOW TABLE STATUS LIKE \' #__eventtableedit_rows_' . $id . '\'';
		$db->setQuery($query);
		
		if (count($db->loadObjectList()) > 0) {
			return false;
		}
		
		// A new table has to be created
		$query = 'CREATE TABLE #__eventtableedit_rows_' . $id .
				 ' (id INT NOT NULL AUTO_INCREMENT,' .
				 ' ordering INT(11) NOT NULL default 0,' .
				 ' created_by INT(11) NOT NULL default 0,' .

				 ' timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,' .

				 ' PRIMARY KEY (id))' .
				 ' ENGINE=MyISAM CHARACTER SET \'utf8\' COLLATE \'utf8_general_ci\'';
		$db->setQuery($query);
		$db->query();
	}

	private function updateRowsTable($cid, $newId,$id) {
		$db = JFactory::getDBO();
		$query = 'ALTER TABLE #__eventtableedit_rows_' . $id . ' ';
		
		// If it's a existing column
		if ($cid != 0) {
			$query .= 'CHANGE head_' . $newId . ' head_' . $newId . ' text';
		} else {
			$query .= 'ADD head_' . $newId . ' text';
		}
		
		$db->setQuery($query);
		$db->query();
	}
	private function updateRowsTablefromxml($cid, $newId,$id,$datatype) {
		$db = JFactory::getDBO();
		if($datatype == 'boolean' || $datatype == 'link' || $datatype == 'mail' || $datatype == 'four_state'){
			$datatype = 'text';
		}
		$query = 'ALTER TABLE #__eventtableedit_rows_' . $id . ' ';
		
		// If it's a existing column
		if ($cid != 0) {
			$query .= 'CHANGE head_' . $newId . ' head_' . $newId . ' '.$datatype;
		} else {
			$query .= 'ADD head_' . $newId . ' '.$datatype;
		}
		
		$db->setQuery($query);
		$db->query();
	}
	
	/**
	 * Method to perform batch operations on a set of tables.
	 *
	 * @param	array	$commands	An array of commands to perform.
	 * @param	array	$pks		An array of category ids.
	 *
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.6
	 */
/*	function batch($commands, $pks)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			$this->setError(JText::_('COM_EVENTTABLEEDIT_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['assetgroup_id'])) {
			if (!$this->_batchAccess($commands['assetgroup_id'], $pks)) {
				return false;
			}
			$done = true;
		}

		if (!empty($commands['menu_id'])) {
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c' && !$this->_batchCopy($commands['menu_id'], $pks)) {
				return false;
			}
			else if ($cmd == 'm' && !$this->_batchMove($commands['menu_id'], $pks)) {
				return false;
			}
			$done = true;
		}

		if (!$done) {
			$this->setError('COM_MENUS_ERROR_INSUFFICIENT_BATCH_INFORMATION');
			return false;
		}
		
		return true;
	}
*/
	/**
	 * Batch access level changes for a group of rows.
	 *
	 * @param	int		$value	The new value matching an Asset Group ID.
	 * @param	array	$pks	An array of row IDs.
	 *
	 * @return	booelan	True if successful, false otherwise and internal error is set.
	 * @since	1.6
	 */
	protected function _batchAccess($value, $pks)
	{
		$table = $this->getTable();
		foreach ($pks as $pk)
		{
			$table->reset();
			$table->load($pk);
			$table->access = (int) $value;

			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
	
	function getFields(){
		$jinput = JFactory::getApplication()->input;
		$id = $jinput->get('id');
		if(!$id){
			return array();
		}else{
			$db = JFactory::getDBO();
			$query = $db->setQuery("select * from #__eventtableedit_heads where table_id = $id");
			return $db->loadObjectList();
		}
	}
}
