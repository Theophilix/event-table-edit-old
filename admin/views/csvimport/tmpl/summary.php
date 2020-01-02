<?php
/**
 * $Id:$
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
$app = JFactory::getApplication();
?>

<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="">
		<fieldset class="adminform">
			<?php 
			switch ($app->getUserState('com_eventtableedit.importAction', 'newTable')) {
				case 'newTable':
					echo $this->loadTemplate('newtable');
					break;
				case 'overwriteTable':
					echo $this->loadTemplate('overwritetable');
					break;
				case 'appendTable':
					echo $this->loadTemplate('appendtable');
					break;
			}
			
			if (!$app->getUserState('com_eventtableedit.csvError', true)) : ?>
			
			<p id="gotoTable">
				<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_GOTO_CONFIG_DESC'); ?>
				<a href="<?php echo JRoute::_('index.php?option=com_eventtableedit&view=etetables'); ?>">
					<?php echo JText::_('COM_EVENTTABLEEDIT_IMPORT_GOTO_CONFIG'); ?>
				</a>
			</p>
			<?php endif; ?>
		</fieldset>
	</div>
	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
