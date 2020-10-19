<?php
/**
 * $Id:$.
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
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$id = $app->input->get('tableList');
$xmlexporttimestamp = $app->input->get('xmlexporttimestamp');
$this->model = $this->getModel('xmlexport');
$name = $this->model->getTabledata($id);
$name = str_replace(' ', '_', $name->name);
$file = JPATH_ROOT.'/components/com_eventtableedit/template/tablexml/'.$name.'_'.$id.'.xml';

?>

<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_EVENTTABLEEDIT_XMLEXPORT_TITLE'); ?></legend>
		
		<textarea readonly="readonly" rows="20" cols="150" id="export-text"><?php echo $this->orderxml; //readfile($file); //echo $this->csvFile;?></textarea>
		<input type="hidden" name="tableList" value="<?php echo $id; ?>" >
		<input type="hidden" name="xmlexporttimestamp" value="<?php echo $xmlexporttimestamp; ?>" >
	</fieldset>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>
