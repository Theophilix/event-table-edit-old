<?php
/**
 * $Id: default.php 140 2011-01-11 08:11:30Z kapsl $
 * @copyright (C) 2007 - 2017 Manuel Kaspar and Theophilix
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
?>

<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_EVENTTABLEEDIT_EXPORT_TABLE') ?></legend>
		
		
		<ul class="adminformlist">
			<li id="tableList">
				<label><?php echo JText::_('COM_EVENTTABLEEDIT_EXPORT_TABLES_DESC'); ?>: </label>
				<?php echo $this->tables; ?>
			</li>
			<li>
				<label><?php echo JText::_('COM_EVENTTABLEEDIT_XMLEXPORT_TIMESTAMP'); ?>: </label>
				<select name="xmlexporttimestamp">
					<option selected="selected" value="1"><?php JText::_('JYES'); ?></option>
					<option value="0"><?php echo JText::_('JNO'); ?></option>
				</select>
			</li>
			
			
			<!--<li>
				<label><?php //echo JText::_('COM_EVENTTABLEEDIT_SEPARATOR'); ?>: </label>
				<select name="separator">
					<option selected="selected">;</option>
					<option>,</option>
					<option>:</option>
				</select>
			</li>
			<li>
				<label><?php //echo JText::_('COM_EVENTTABLEEDIT_DOUBLEQUOTES'); ?>: </label>
				<select name="doubleqt">
					<option selected="selected" value="1"><?php //echo JText::_('JYES'); ?></option>
					<option value="0"><?php //echo JText::_('JNO'); ?></option>
				</select>
			</li>-->
		</ul>
	</fieldset>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>
