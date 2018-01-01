<?php
/**
 * $Id: default.php 144 2011-01-13 08:17:03Z kapsl $
 * @copyright (C) 2007 - 2018 Manuel Kaspar and Theophilix
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

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
$main  = JFactory::getApplication()->input;
		$requests  = $main->getArray($_REQUEST);
?>
<?php
if(isset($requests['print'])){
?>
<style type="text/css">

.appointmentsbtn{display: none;}

</style>
<?php 
}
 ?>
<style type="text/css">
table#etetable-table tr td.highlighted {
  background-color:#c4c1c1;
  border: 1px solid #dddddd;
}
td.tdblue{
	cursor: pointer;
}
</style>
<div class="eventtableedit<?php echo $this->params->get('pageclass_sfx')?>">

<ul class="actions">
	<?php if($this->item->show_print_view) :?>
	<li class="print-icon">
		<?php if (!$this->print) : ?>
			<?php echo str_replace('view=etetable', 'view=appointments',JHtml::_('icon.print_popup',  $this->item, $this->params)); ?>
			<?php //echo JHtml::_('icon.print_popup',  $this->item, $this->params); ?>
		<?php else : ?>
			<?php echo JHtml::_('icon.print_screen',  $this->item, $this->params); ?>
		<?php endif; ?>
	</li>
	<?php endif; ?>

	<?php if($this->params->get('access-create_admin')) :?>
	<li class="admin-icon">
		<?php if ($this->heads) :?>
			<?php echo JHtml::_('icon.adminTable',  $this->item, JText::_('COM_EVENTTABLEEDIT_ETETABLE_ADMIN')); ?>
		<?php else: ?>
			<?php echo JHtml::_('icon.adminTable',  $this->item, JText::_('COM_EVENTTABLEEDIT_ETETABLE_CREATE')); ?>
		<?php endif; ?>
	</li>
	<?php endif; ?>
</ul>


<?php 
if($this->item->addtitle == 1){ ?>
<h2 class="etetable-title">
	<?php echo $this->item->name; ?>
</h2>
<?php } ?>

<?php if($this->item->pretext != '') :?>
	<div class="etetable-pretext">
		<?php echo $this->item->pretext; ?>
	</div>
<?php endif; ?>

<?php if($this->item->show_filter && count($this->heads) > 0) :?>
	<div class="etetable-filter">
		<?php echo $this->loadTemplate('filter'); ?>
	</div>
<?php endif;  //etetable-tform ?>
<div style="clear:both"></div>
<!-- etetable-tform -->
<form action="<?php //echo JRoute::_('index.php?option=com_eventtableedit'); ?>" name="adminForm" id="adminForm" method="post">
	<?php // echo '<pre>';print_r($this->item);

	//If there is already a table set up
	if ($this->heads) :?>
  		<input type="button" name="appointments" value="<?php echo JText::_('COM_EVENTTABLEEDIT_BOOK_BUTTON') ?>" style="float:right;" onclick="subappointments();" class="btn btn-primary appointmentsbtn" />
		<div class="etetable-outtable">


			<?php echo $this->loadTemplate('table'); ?>
		</div>
	<?php endif; ?>
	
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction') ?>" />
	<input type="hidden" name="filterstring" value="<?php echo $this->params->get('filterstring') ?>" />
	<input type="hidden" name="option" value="com_eventtableedit" />
	<input type="hidden" name="view" value="appointmentform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="rowcolmix" id="rowcolmix" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
/**
 * Adding a new row
 */
?>
<?php if($this->params->get('access-add') && $this->heads) : ?>
	<!--<div id="etetable-add" title="<?php echo JText::_('COM_EVENTTABLEEDIT_NEW_ROW'); ?>"></div>
-->
<?php endif; ?>

<?php if($this->item->aftertext != '') :?>
	<div class="etetable-aftertext">
		<?php echo $this->item->aftertext; ?>
	</div>
<?php endif; ?>

</div>
<div style="clear:both"></div>

<script type="text/javascript">
jQuery(document).ready(function() {
    
  	var isMouseDown = false,
    isHighlighted;
    var array = [];
  	jQuery(document).on('mousedown', '#etetable-table td.tdblue', function() {
      isMouseDown = true;
      jQuery(this).toggleClass("highlighted");
      isHighlighted =jQuery(this).hasClass("highlighted");
      return false; // prevent text selection
    })
  	.on('mouseover', '#etetable-table td.tdblue', function () {
      if (isMouseDown) {
        jQuery(this).toggleClass("highlighted", isHighlighted);
      }
    })
  	.bind("selectstart", function () {
      return false;
    })
	jQuery(document)
    .mouseup(function () {
      isMouseDown = false;
    });
    
});
function subappointments(){
	var array = [];
	jQuery('.highlighted').each(function(){
	  	var rowcolmixs = jQuery(this).attr('id').split('row_');
	  	array.push(rowcolmixs[1]);
	  	
	});
	jQuery('#rowcolmix').val(array.toString());
	if(jQuery('#rowcolmix').val() !=''){
		document.adminForm.submit();
	}
}
</script>
