<?php
/**
 * $Id: default.php 157 2011-03-19 00:08:23Z kapsl $.
 *
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
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();

$params = $app->getParams();
$main = JFactory::getApplication()->input;
        $tablenumber = $main->getInt('id', '');

?>

<div class="eventtableedit<?php echo $this->params->get('pageclass_sfx'); ?>">

<h2 class="etetable-title">
	<?php echo JText::_('COM_EVENTTABLEEDIT_ETETABLE_ADMIN').' '.$this->info['tablename']; ?>
</h2>

<div id="changetable-toolbar">
	<ul>
		<li id="changetable-newrow" class="etetable-toolbutton">
			<span id="icon-32-new"></span>
			<?php echo JText::_('COM_EVENTTABLEEDIT_NEW'); ?>
		</li>

		<li id="changetable-save" class="etetable-toolbutton">
			<span id="icon-32-save"></span>
			<?php echo JText::_('COM_EVENTTABLEEDIT_SAVE'); ?>
		</li>

		<li id="changetable-cancel" class="etetable-toolbutton">
			<span id="icon-32-cancel"></span>
			<?php echo JText::_('COM_EVENTTABLEEDIT_CANCEL'); ?>
		</li>
	</ul>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist" id="changetable-table">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('COM_EVENTTABLEEDIT_NAME'); ?>
				</th>
				<th width="25%">
					<?php echo JText::_('COM_EVENTTABLEEDIT_DATATYPE'); ?>
				</th>
				<th width="9%">
					<?php echo JText::_('COM_EVENTTABLEEDIT_ORDERING'); ?>
				</th>
				<!--<th width="15%">
					<?php //echo JText::_('COM_EVENTTABLEEDIT_AUTOSORT');?>
				</th>-->
				<th width="5%">
					<?php echo JText::_('COM_EVENTTABLEEDIT_DELETE'); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
			
		</tbody>
	</table>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="changetable.save" />
	<input type="hidden" name="id" value="<?php echo $tablenumber; ?>" />
</form>

</div>
