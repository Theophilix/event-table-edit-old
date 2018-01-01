<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2018 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

class EventtableeditControllerXmlexport extends JControllerLegacy {
	protected $text_prefix = 'COM_EVENTTABLEEDIT_XMLEXPORT';
	protected $app;
	
	protected $id;
	protected $separator;
	protected $doubleqt;
	protected $model;
	
	function __construct() {
		parent::__construct();
		$this->app = JFactory::getApplication();
	}
	
	/**
	 * Task that is called when exporting a table
	 */
	public function export() {
		// ACL Check
		$user = JFactory::getUser();
		if (!$user->authorise('core.csv', 'com_eventtableedit')) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect(JRoute('index.php?option=com_eventtableedit'));
			return false;
		}
		$xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR .'/eventtableedit.xml');
		$version = (string)$xml->version;

		// Initialize Variables
		$this->model = $this->getModel('xmlexport');
		$app = JFactory::getApplication();
		$input  =  JFactory::getApplication()->input;
		$postget = $input->getArray($_POST);

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
				$orderxml .= '</linerow>';
				$b++;
}
$orderxml .= '</rowdata>';
  $orderxml .= '</Event_Table_Edit_XML_file>';

//echo '<pre>';
//echo htmlspecialchars($orderxml);
//echo '</pre>';

 $file = str_replace(' ','_',$table->name)."_".$this->id.".xml";

 $pf = fopen (JPATH_ROOT.'/components/com_eventtableedit/template/tablexml/'.$file, "w");
 if (!$pf)
 {
 	echo "Cannot create $file!" . NL;
 	return;
 }
 fwrite ($pf, $orderxml);
 fclose ($pf);

		 $input->set('com_eventtableedit.layout','summary');
		 $input->set('view','xmlexport');
		 $input->set('com_eventtableedit.id',$this->id);
 		 $this->model->setVariables($this->id, $this->separator, $this->doubleqt);
		parent::display();

	}

	public function cancel() {
		$this->setRedirect(JRoute::_('index.php?option=com_eventtableedit'));
		return false;
	}

	public function download(){

			$app = JFactory::getApplication();
			$id = $app->input->get('tableList');
			$this->model = $this->getModel('xmlexport');
			$name = $this->model->getTabledata($id);
			$name = str_replace(' ','_',$name->name);
			$file = JPATH_ROOT."/components/com_eventtableedit/template/tablexml/".$name."_".$id.".xml";
			
	header('Content-Description: File Transfer');
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
	}
}
