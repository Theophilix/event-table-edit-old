<?php
/**
 * $Id: default.php 140 2011-01-11 08:11:30Z kapsl $
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
JHtml::_('bootstrap.popover');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'csvimport.upload' && checkTableName()) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_EVENTTABLEEDIT_ERROR_ENTER_NAME'));?>');
			jQuery('#tableName').focus();
		}
	}

	function checkTableName() {
		$val = jQuery('input[name=importaction]:checked').val();
		
		if($val == 'newTable')
		{
		   if (jQuery('#tableName').val() == '') {
			  return false;
		   }
		}
		
		if($val == 'overwriteTable')
		{
		   if (jQuery('#tableList').val() == '') {
			  return false;
		   }
		}
		
		if($val == 'appendTable')
		{
		   if (jQuery('#tableList1').val() == '') {
			  return false;
		   }
		}
		
		return true;
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
	<div class="">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_EVENTTABLEEDIT_UPLOAD_FILE') ?></legend>
		
		<p><?php echo JText::sprintf('COM_EVENTTABLEEDIT_CSVIMPORT_DESC', (int) $this->maxFileSize) ?></p>
		
		<ul class="adminformlist" style="float:left;">
			<li>
				
				<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_CHECKBOX_NORMAL_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_CHECKBOX_NORMAL_DESC'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_CHECKBOX_NORMAL'); ?>: </label>
				<select name="checkfun">
					<option value="0"><?php echo JText::_('JNO') ?></option>
					<option value="1"><?php echo JText::_('JYES') ?></option>
				</select>
			</li>
			<li>
				<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_CSVFILE_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_CSVFILE_DESC'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_CSVFILE'); ?>: </label>
				<input type="file" name="fupload" />
			</li>
			<li>
				<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR_DESC'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR'); ?>: </label>
				<select name="separator">
					<option selected="selected">;</option>
					<option>,</option>
					<option>:</option>
				</select>
			</li>
			<li>
				<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES_DESC'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES'); ?>: </label>
				<select name="doubleqt">
					<option selected="selected" value="1"><?php echo JText::_('JYES'); ?></option>
					<option value="0"><?php echo JText::_('JNO'); ?></option>
				</select>
			</li>
			<li>
				<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_CSVACTIONS_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_CSVACTIONS_DESC'); ?>"><b><?php echo JText::_('COM_EVENTTABLEEDIT_CSVACTIONS'); ?>: </b></label>
				<ul class="etetable-import-actions" style="list-style: none;margin-left: 0;">
					<li>
						<fieldset class="radio">
							<input type="radio" name="importaction" id="newTable" value="newTable" 
								   onclick="hideSelect();" checked /> 
								   <label class="hasPopover" for="newTable" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE_2'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE_2'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_NEW_TABLE_2'); ?></label>
								   <p id="tables2">
									<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT_DESC'); ?>"><?php echo JText::_("COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT"); ?>: </label>
									<input type="text" name="table_name" id="tableName" />
								</p>
						</fieldset>
					</li>
					<li>
						<fieldset class="radio">
						<input type="radio" name="importaction" id="overwriteTable" value="overwriteTable"
							   onclick="showSelect();" /> 
							   <label class="hasPopover" for="overwriteTable" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_OVERWRITE_TABLE'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_OVERWRITE_TABLE'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_OVERWRITE_TABLE'); ?></label>
							<p id="tables">
								<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT_DESC'); ?>"><?php echo JText::_("COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT"); ?>: </label><?php echo $this->tables; ?>
							</p>
						</fieldset>
					</li>
					<li>
						<fieldset class="radio">
						<input type="radio" name="importaction" id="appendTable" value="appendTable"
							   onclick="showSelect1();" /> 
							   <label class="hasPopover" for="appendTable" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_APPEND_TABLE'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_APPEND_TABLE'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_APPEND_TABLE'); ?></label>
							   <p id="tables1">
								<label class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT_DESC'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT_DESC'); ?>"><?php echo JText::_("COM_EVENTTABLEEDIT_IMPORT_TABLES_SELECT"); ?>: </label><?php echo $this->tables1; ?>
							</p>
						</fieldset>
					</li>
				</ul>
			</li>
			
		</ul>
	</fieldset>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<style>#tables1{display: none;}</style>
<script type="text/javascript">
<!--
	function showSelect() {
		document.getElementById('tables').style.display = 'inline';
		document.getElementById('tables1').style.display = 'none';
		document.getElementById('tables2').style.display = 'none';

	}
	function hideSelect() {
		document.getElementById('tables').style.display = 'none';
		document.getElementById('tables1').style.display = 'none';
		document.getElementById('tables2').style.display = 'inline';

	}
		function showSelect1() {
		document.getElementById('tables1').style.display = 'inline';
		document.getElementById('tables').style.display = 'none';
		document.getElementById('tables2').style.display = 'none';

	}
-->
</script>
