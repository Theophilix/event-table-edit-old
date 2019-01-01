<?php
/**
 * $Id: default.php 118 2010-10-04 08:43:45Z kapsl $
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
JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_EVENTTABLEEDIT_NEW_DROPDOWN') : JText::sprintf('COM_EVENTTABLEEDIT_EDIT_DROPDOWN', $this->item->id); ?></legend>
				<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
				
				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				</ul>
				
				<table class="adminlist" id="dropdown-table">
					<thead>
						<tr>
							<th width="70%">
								<?php echo JText::_('JGLOBAL_TITLE'); ?>
							</th>
							<th>
								<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
							</th>
							<th>
								<?php echo JText::_('COM_EVENTTABLEEDIT_DELETE'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div id="addNew"></div>
		</fieldset>
		
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="clr"></div>
