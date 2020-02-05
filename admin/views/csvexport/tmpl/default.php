<?php
/**
 * $Id: default.php 140 2011-01-11 08:11:30Z kapsl $
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

// no direct access adminForm
defined( '_JEXEC' ) or die;
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.popover');
?>
<script>
Joomla.submitbutton = function(task)
{
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = jQuery('form.form-validate');
			for (var i = 0; i < forms.length; i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					break;
				}
			}
		}
	
		if (isValid)
		{
			Joomla.submitform(task);
			return true;
		}
		else
		{
			jQuery("#system-message-container h4.alert-heading").html("Error");
			jQuery("#system-message-container .alert").append("<p>Invalid field: Name</p>");
			return false;
		}
	}
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" class="form-validate" method="post" name="adminForm" id="adminForm">
	<div class="">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_EVENTTABLEEDIT_CSVEXPORT_TITLE') ?></legend>
		
		<p><?php echo JText::_('COM_EVENTTABLEEDIT_CSVEXPORT_TITLE_INFO') ?></p>
		
		<ul class="adminformlist" style="float: left;">
			<li id="tableList">
				<label id="file-lbl" for="file" class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_EXPORT_TABLE_CHOICE'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_EXPORT_TABLE_CHOICE'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_EXPORT_TABLE_CHOICE'); ?>: </label>
				<?php echo $this->tables; ?>
			</li>
			<li>
				<label id="file-lbl" for="file" class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR'); ?>: </label>
				<select name="separator">
					<option selected="selected">;</option>
					<option>,</option>
					<option>:</option>
				</select>
			</li>
			<li>
				<label id="file-lbl" for="file" class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES'); ?>: </label>
				<select name="doubleqt">
					<option selected="selected" value="1"><?php echo JText::_('JYES'); ?></option>
					<option value="0"><?php echo JText::_('JNO'); ?></option>
				</select>
			</li>
			
			<li>
				<label id="file-lbl" for="file" class="hasPopover" title="" data-content="<?php echo JText::_('COM_EVENTTABLEEDIT_CSVEXPORT_TIMESTAMP'); ?>" data-original-title="<?php echo JText::_('COM_EVENTTABLEEDIT_CSVEXPORT_TIMESTAMP'); ?>"><?php echo JText::_('COM_EVENTTABLEEDIT_CSVEXPORT_TIMESTAMP'); ?>: </label>
				<select name="csvexporttimestamp">
					<option selected="selected" value="1"><?php echo JText::_('JYES'); ?></option>
					<option value="0"><?php echo JText::_('JNO'); ?></option>
				</select>
			</li>
		</ul>
	</fieldset>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>
