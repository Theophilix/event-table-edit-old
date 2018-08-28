<?php
/**
 * $Id:$
 * @copyright (C) 2007 - 2018 Manuel Kaspar and Matthias  Gruhn
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

$app = JFactory::Getapplication();
$checkfun = $app->input->get('checkfun');


?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'csvimport.cancel' || checkTableName()) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_EVENTTABLEEDIT_ERROR_ENTER_NAME'));?>');
		}
	}

	function checkTableName() {
		if (jQuery('#tableName').val() == '') {
			return false;
		}
		return true;
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="">
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_EVENTTABLEEDIT_SET_SETTINGS'); ?></legend>
			<ul>
			<li style="display:none;">
				<label for="tableName"><b><?php echo JText::_('COM_EVENTTABLEEDIT_TABLE_NAME'); ?>: </b></label>
				
			</li>
			</ul>
			<table id="datatypeTable" border="0" width="90%">
				<?php for($a = 0; $a < count($this->headLine); $a++) :
					if($this->headLine[$a]!='timestamp'):
					?>
					<tr>
						<td id="colText"><b><?php echo JText::_('COM_EVENTTABLEEDIT_DATATYPE_FOR') . ' ' . $this->headLine[$a]; ?></b></td>
						<td><?php echo $this->listDatatypes; ?></td>
					</tr>
				<?php 
					endif;
					endfor; ?>
			</table>
		</fieldset>
	</div>
	<input type="hidden" id="tableName" class="inputbox required" size="30" value="<?php echo $_REQUEST["tableName"]?>" name="tableName" />
	<input type="hidden" name="task" value="" />
		<input type="hidden" name="checkfun" value="<?php echo $checkfun?$checkfun:'0'; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>