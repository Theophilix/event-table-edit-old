<?php
/**
 * $Id: $
 * @copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 * 
 * Translates PHP Values to JS Variables
 */
 
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

?>
<script type="text/javascript">
<!--

function Access() {
	this.edit 		= <?php if ($this->params->get('access-edit')) echo "1"; else echo "0"; ?>;
	this.add 		= <?php if ($this->params->get('access-add')) echo "1"; else echo "0"; ?>;
	this.deleteRowR = <?php if ($this->params->get('access-delete')) echo "1"; else echo "0"; ?>;
	this.deleteRow 	= <?php if ($this->item->rowdelete) echo "1"; else echo "0"; ?>;
	this.reorder	= <?php if ($this->params->get('access-reorder')) echo "1"; else echo "0"; ?>;
	
	this.ownRows 	= <?php if ($this->params->get('access-ownRows')) echo "1"; else echo "0"; ?>;
	
	// Saves what rows a user created himself
	var createdTemp  = '<?php echo $this->additional['createdRows']; ?>';
	this.createdRows = createdTemp.split('|');
}

function TableProperties() {
	this.id 		= <?php echo $this->item->id; ?>;
	this.nmbRows 	= <?php echo count($this->rows); ?>;
	this.nmbCells	= <?php echo count($this->heads); ?>;
	this.show_first_row = <?php echo $this->item->show_first_row; ?>;
	this.limitstart = <?php echo $this->pagination->get('limitstart', 0); ?>;
	this.show_pagination = <?php echo $this->item->show_pagination; ?>;
	
	this.defaultSorting	= <?php echo $this->additional['defaultSorting']; ?>;
	var orderTemp 	= '<?php echo $this->additional['ordering']; ?>';
	this.ordering	= orderTemp.split('|');
	
	this.myTable 	= $('etetable-table');
}

function Language() {
	// Infoboxes
	this.toolTip_title = new Object();
	this.toolTip_desc = new Object();
	
	this.toolTip_title["text"] 	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_TEXT_TITLE'); ?>';
	this.toolTip_desc["text"]	= '<?php 
					   echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_TEXT_DESC');
					   if ($this->item->bbcode) {
						echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_TEXT_BBCODE');
					   } ?>';

	this.toolTip_title["int"]  	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_INT_TITLE'); ?>';
	this.toolTip_desc["int"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_INT_DESC'); ?>';
	
	this.toolTip_title["float"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_FLOAT_TITLE'); ?>';
	this.toolTip_desc["float"] 	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_FLOAT_DESC'); ?>';
	
	this.toolTip_title["date"] 	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_DATE_TITLE'); ?>';
	this.toolTip_desc["date"] 		= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_DATE_DESC'); ?>';
	
	this.toolTip_title["time"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_TIME_TITLE'); ?>';
	this.toolTip_desc["time"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_TIME_DESC'); ?>';
	
	this.toolTip_title["dropdown"] = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_DROPDOWN_TITLE'); ?>';
	this.toolTip_desc["dropdown"]  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_DROPDOWN_DESC'); ?>';
	
	this.toolTip_title["mail"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_EMAIL_TITLE'); ?>';
	this.toolTip_desc["mail"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_EMAIL_DESC'); ?>';
	
	this.toolTip_title["link"] 	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_LINK_TITLE'); ?>';
	this.toolTip_desc["link"]	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_TIP_LINK_DESC'); ?>';
	
	// Errors
	this.err_no_int		= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ERROR_NO_INT'); ?>';
	this.err_no_float	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ERROR_NO_FLOAT'); ?>';
	this.err_no_time	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ERROR_NO_TIME'); ?>';
	this.err_no_mail	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ERROR_NO_MAIL'); ?>';
	this.err_no_Link	= '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ERROR_NO_LINK'); ?>';

	// Others
	this.really_delete		  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_REALLY_DELETE'); ?>';
	this.err_dropdown_deleted = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ERROR_DROPDOWN_DELETED'); ?>';
	this.actions 			  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ACTIONS'); ?>';
	this.deletetext			  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_DELETE'); ?>';
	this.clear				  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_CLEAR'); ?>';
	this.saveOrder			  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_SAVE_ORDER'); ?>';
	this.deleteRow			  = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_DELETE_ROW'); ?>';
}

Language.prototype.getToolTip = function(datatype) {
	var ret = new Object();
	ret["title"] = this.toolTip_title[datatype];
	ret["desc"] = this.toolTip_desc[datatype];
	
	return ret;
}

function Others() {
	this.rootUrl 			= '<?php echo JURI::root(); ?>';
	this.windowOpen			= 0;
	this.orderingLink		= '<?php echo str_replace('\'', '\\\'', JHTML::_( 'grid.sort', 'COM_EVENTTABLEEDIT_ORDERING', 'a.ordering', $listDirn, $listOrder)); ?>';
	this.listOrder			= '<?php echo $listOrder; ?>';
}

Others.prototype.doOpen = function() {
	//If a window is open
	if (this.windowOpen) {
		return false;
	} else {
		this.windowOpen = 1;
		return true;
	}
}

Others.prototype.doClose = function() {
	this.windowOpen = 0;
}

// Dropdowns
function Dropdowns() {
	this.dropdowns = new Array();
}

function Dropdown(id, name) {
	this.id = id;
	this.name = name;
	this.elements = new Array();
}

Dropdowns.prototype.getDropdownById = function(id) {
	for (var a = 0; a < this.dropdowns.length; a++) {
		if (this.dropdowns[a].id == id) {
			return this.dropdowns[a];
		}
	}
	return null;
}

dropdowns = new Dropdowns();

<?php
$dropdowns = $this->additional['dropdowns'];

for ($a = 0; $a < count($dropdowns); $a++) :?>
	var dropdown = new Dropdown(<?php echo $dropdowns[$a]['meta']['id'] ?>, '<?php echo $dropdowns[$a]['meta']['name'] ?>');
	
	<?php for ($b = 0; $b < count($dropdowns[$a]['items']); $b++) : ?>
		dropdown.elements.push('<?php echo $dropdowns[$a]['items'][$b] ?>');
	<?php endfor; ?>

	dropdowns.dropdowns.push(dropdown);
<?php endfor; ?>
		
-->
</script>
