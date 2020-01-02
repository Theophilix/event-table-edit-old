<?php
/**
 * $Id: view.html.php 140 2011-01-11 08:11:30Z kapsl $
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

// no direct access
defined( '_JEXEC' ) or die;
jimport( 'joomla.application.component.view');
require_once JPATH_COMPONENT.'/views/csvimport/view.html.php';
require_once JPATH_COMPONENT.'/models/csvimport.php';

class EventtableeditViewxmlexport extends JViewLegacy {
	function display($tpl = null) {
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		if (!$user->authorise('core.csv', 'com_eventtableedit')) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		$input  =  JFactory::getApplication()->input;
		$layout = $input->get('com_eventtableedit.layout');
		// Switch the differnet datatypes
		
		switch ($layout) {
			case 'summary':
				// Check for errors.
				if (count($errors = $this->get('Errors'))) {
					JError::raiseError(500, implode("\n", $errors));
					return false;
				}
				$postget = $input->getArray($_REQUEST);
				
				$orderxml = $this->getXML();
				$this->assignRef('orderxml',$orderxml);
				
				
				$this->addSummaryToolbar();
				break;
			default:
				$importView = new EventtableeditViewCsvimport();
				$tableList = $importView->createTableSelectList();
				
				$this->assignRef('tables', $tableList);
				
				$this->addDefaultToolbar();
		}
		
		$this->document->addStyleSheet($this->baseurl.'/components/com_eventtableedit/template/css/eventtableedit.css');
		
		$this->setLayout($layout);
	    parent::display($tpl);
	}
	
	protected function addDefaultToolbar()	{
		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_MANAGER_XMLEXPORT'), 'export');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_MANAGER_XMLEXPORT' ), 'etetables' );
		JToolBarHelper::custom('xmlexport.export', 'apply.png', '', 'COM_EVENTTABLEEDIT_EXPORT', false);
	}
	
	/**
	 * The Toolbar for showing the summary of the export
	 */
	protected function addSummaryToolbar()	{
		//JToolBarHelper::title(JText::_('COM_EVENTTABLEEDIT_EXPORT_SUMMARY'), 'export');
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$currentversion = (string)$xml->version;
		JToolBarHelper::title( JText::_( 'Event Table Edit '.$currentversion ) . ' - ' . JText::_( 'COM_EVENTTABLEEDIT_EXPORT_SUMMARY' ), 'etetables' );
		JToolBarHelper::custom('xmlexport.cancel', 'apply.png', '', 'COM_EVENTTABLEEDIT_OK', false);
		JToolBarHelper::custom('xmlexport.download', 'apply.png', '', 'COM_EVENTTABLEEDIT_DOWNLOAD_FILE', false);
	}
	
	function getXML(){
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$version = (string)$xml->version;
		
		$this->model = $this->getModel('xmlexport');
		$app = JFactory::getApplication();
		$input  =  JFactory::getApplication()->input;
		$postget = $input->getArray($_POST);
		$this->xmlexporttimestamp  = $postget['xmlexporttimestamp'];
		$this->id 		 = $postget['tableList'];
		if(empty($this->id)){
			$msg = JTEXT::_('COM_EVENTTABLEEDIT_PLEASE_SELECT_TABLE');
			$app->redirect('index.php?option=com_eventtableedit&view=xmlexport',$msg);
				
		}	
		$table = $this->model->getTabledata($this->id);
			

		$db = JFactory::GetDBO();
		$query = 'SELECT CONCAT(\'head_\', a.id) AS head, a.name,a.datatype, a.defaultSorting FROM #__eventtableedit_heads AS a' .
					' WHERE a.table_id = ' . $this->id .
					' ORDER BY a.ordering ASC';
		$db->setQuery($query);
		$heads = $db->loadObjectList();
	
		$query = 'SELECT * FROM #__eventtableedit_rows_' . $this->id;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$orderxml = '<?xml version="1.0" encoding="utf-8"?> 
		<Event_Table_Edit_XML_file>
		<ETE_version>'.$version.'</ETE_version>
		<id>'.$table->id.'</id>
		<name>'.$table->name.'</name>
		<alias>'.$table->alias.'</alias>
		<user_id>'.$table->user_id.'</user_id>
		<access>'.$table->access.'</access>
		<checked_out>'.$table->checked_out.'</checked_out>
		<checked_out_time>'.$table->checked_out_time.'</checked_out_time>

		<language>'.$table->language.'</language>
		<show_filter>'.$table->show_filter.'</show_filter>
		<show_first_row>'.$table->show_first_row.'</show_first_row>
		<show_print_view>'.$table->show_print_view.'</show_print_view>
		<rowsort>'.$table->rowsort.'</rowsort>
		<show_pagination>'.$table->show_pagination.'</show_pagination>
		<bbcode>'.$table->bbcode.'</bbcode>
		<bbcode_img>'.$table->bbcode_img.'</bbcode_img>
		<pretext>'.str_replace('&','&amp;',htmlentities($table->pretext)).'</pretext>
		<aftertext>'.str_replace('&','&amp;',htmlentities($table->aftertext)).'</aftertext>
		<metakey>'.$table->metakey.'</metakey>
		<metadesc>'.$table->metadesc.'</metadesc>
		<metadata>'.$table->metadata.'</metadata>
		<edit_own_rows>'.$table->edit_own_rows.'</edit_own_rows>
		<dateformat>'.$table->dateformat.'</dateformat>
		<timeformat>'.$table->timeformat.'</timeformat>
		<cellspacing>'.$table->cellspacing.'</cellspacing>
		<cellpadding>'.$table->cellpadding.'</cellpadding>
		<tablecolor1>'.$table->tablecolor1.'</tablecolor1>
		<tablecolor2>'.$table->tablecolor2.'</tablecolor2>
		<float_separator>'.$table->float_separator.'</float_separator>
		<link_target>'.$table->link_target.'</link_target>
		<cellbreak>'.$table->cellbreak.'</cellbreak>
		<pagebreak>'.$table->pagebreak.'</pagebreak>
		<asset_id>'.$table->asset_id.'</asset_id>
		<lft>'.$table->lft.'</lft>
		<rgt>'.$table->rgt.'</rgt>
		<published>'.$table->published.'</published>
		<normalorappointment>'.$table->normalorappointment.'</normalorappointment>
		<addtitle>'.$table->addtitle.'</addtitle>
		<location>'.$table->location.'</location>
		<summary>'.$table->summary.'</summary>
		<email>'.$table->email.'</email>
		<adminemailsubject>'.str_replace('&','&amp;',htmlentities($table->adminemailsubject)).'</adminemailsubject>
		<useremailsubject>'.str_replace('&','&amp;',htmlentities($table->useremailsubject)).'</useremailsubject>
		<useremailtext>'.str_replace('&','&amp;',htmlentities($table->useremailtext)).'</useremailtext>
		<adminemailtext>'.str_replace('&','&amp;',htmlentities($table->adminemailtext)).'</adminemailtext>
		<displayname>'.$table->displayname.'</displayname>
		<icsfilename>'.$table->icsfilename.'</icsfilename>
		<sorting>'.$table->sorting.'</sorting>
		<switcher>'.$table->switcher.'</switcher>
		<standardlayout>'.$table->standardlayout.'</standardlayout>
		<row>'.$table->row.'</row>
		<col>'.$table->col.'</col>
		<hours>'.$table->hours.'</hours>
		<showdayname>'.$table->showdayname.'</showdayname>
		<showusernametoadmin>'.$table->showusernametoadmin.'</showusernametoadmin>
		<showusernametouser>'.$table->showusernametouser.'</showusernametouser>
		<rules>'.$table->rules.'</rules>';

		$orderxml .= '<headdata>';
		$a=1;
		foreach ($heads as $value) {
			$orderxml .= '<linehead>
							<no>'.$a.'</no>
							<headtable>'.$value->head.'</headtable>
							<name>'.$value->name.'</name>
							<datatype>'.$value->datatype.'</datatype>
						</linehead>';
						$a++;
		}
		if($this->xmlexporttimestamp){
			$orderxml .= '<linehead>
							<no>'.$a.'</no>
							<headtable>timestamp</headtable>
							<name>timestamp</name>
							<datatype>timestamp</datatype>
						</linehead>';
		}
		$orderxml .= '</headdata>';



		$orderxml .= '<rowdata>';
		$b=1;

		foreach ($rows as $row) {
			$orderxml .= '<linerow>
							<no>'.$b.'</no>
							<id>'.$row->id.'</id>
							<ordering>'.$row->ordering.'</ordering>
							<created_by>'.$row->created_by.'</created_by>';
							for ($h=0; $h < count($heads); $h++) { 
								$findrowval = $heads[$h]->head;
								$orderxml .= '<'.$findrowval.'>'.htmlspecialchars($row->$findrowval).'</'.$findrowval.'>';	
							}
							if($this->xmlexporttimestamp){
								$orderxml .= '<timestamp>'.htmlspecialchars($row->timestamp).'</timestamp>';	
							}
						$orderxml .= '</linerow>';
						$b++;
		}
		
		$orderxml .= '</rowdata>';
		$orderxml .= '</Event_Table_Edit_XML_file>';
		return $orderxml;
	}
}
?>
