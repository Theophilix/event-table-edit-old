<?php
/**
 * $Id: default.php 144 2011-01-13 08:17:03Z kapsl $.
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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$main = JFactory::getApplication()->input;
$postget = $main->getArray();
?>
<?php
if (isset($requests['print'])) {
	$document = JFactory::getDocument();
	$style = '.appointmentsbtn{display: none;}';
	$document->addStyleDeclaration( $style );
}
 ?>

<?php if (!$this->option_id && $this->item->add_option_list) {
	$document = JFactory::getDocument();
	$style = '.etetable-outtable, input.btn.btn-primary.appointmentsbtn{display:none;}';
	$document->addStyleDeclaration( $style );
    
 }?>

<div class="eventtableedit<?php echo $this->params->get('pageclass_sfx'); ?>">

<ul class="actions">
	<?php if ($this->item->show_print_view) :?>
	<li class="print-icon">
		<?php if (!$this->print) : ?>
			<?php echo str_replace('view=etetable', 'view=appointments', JHtml::_('icon.print_popup', $this->item, $this->params)); ?>
			<?php //echo JHtml::_('icon.print_popup',  $this->item, $this->params);?>
		<?php else : ?>
			<?php echo JHtml::_('icon.print_screen', $this->item, $this->params); ?>
		<?php endif; ?>
	</li>
	<?php endif; ?>

	<?php if ($this->params->get('access-create_admin')) :?>
	<li class="admin-icon">
		<?php if ($this->heads) :?>
			<?php echo JHtml::_('icon.adminTable', $this->item, JText::_('COM_EVENTTABLEEDIT_ETETABLE_ADMIN')); ?>
		<?php else: ?>
			<?php echo JHtml::_('icon.adminTable', $this->item, JText::_('COM_EVENTTABLEEDIT_ETETABLE_CREATE')); ?>
		<?php endif; ?>
	</li>
	<?php endif; ?>
</ul>


<?php
if (1 === (int)$this->item->addtitle) { ?>
<h2 class="etetable-title">
	<?php echo $this->item->name; ?>
</h2>
<?php } ?>

<?php if ('' !== $this->item->pretext) :?>
	<div class="etetable-pretext">
		<?php echo $this->item->pretext; ?>
	</div>
<?php endif; ?>

<div style="clear:both"></div>
<!-- etetable-tform -->
<form name="adminForm" id="adminForm" method="post">
	<?php if ($this->item->add_option_list) :
        //$session = JFactory::getSession();
        //$corresponding_table = $session->get('corresponding_table');
    ?>
		<div class="etetable-options" style="position: absolute;top: 10px;left: 0;">
			<?php
            $corresptables = json_decode($this->item->corresptable, true);
            if (!empty($corresptables)) {
                ?>
				<select name="corresponding_table" id="corresponding_table">
					<option value=""><?php echo JText::_('COM_EVENTTABLEEDIT_CHOOSE_YOUR_OPTION'); ?></option>
					<?php
                foreach ($corresptables as $global_option => $corresptable) {
                    ?><option <?php if ($this->option_id === $corresptable) {
                        echo 'selected=selected';
                    } ?> value="<?php echo $corresptable; ?>"><?php echo $global_option; ?></option><?php
                } ?>
				</select>
				<?php
            }
            ?>
		</div>
	<?php endif;  //etetable-tform?>
	<?php
    //If there is already a table set up
    if ($this->heads) :?>
  		<input type="button" name="appointments" value="<?php echo JText::_('COM_EVENTTABLEEDIT_BOOK_BUTTON'); ?>" style="float:right;" onclick="subappointments();" class="btn btn-primary appointmentsbtn" />
		<div class="etetable-outtable">


			<?php echo $this->loadTemplate('table'); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="option" value="com_eventtableedit" />
	<input type="hidden" name="view" value="appointmentform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="rowcolmix" id="rowcolmix" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
/**
 * Adding a new row.
 */
?>
<?php if ($this->params->get('access-add') && $this->heads) : ?>
	<!--<div id="etetable-add" title="<?php echo JText::_('COM_EVENTTABLEEDIT_NEW_ROW'); ?>"></div>
-->
<?php endif; ?>

<?php if ('' !== $this->item->aftertext) :?>
	<div class="etetable-aftertext">
		<?php echo $this->item->aftertext; ?>
	</div>
<?php endif; ?>

</div>
<div style="clear:both"></div>

<script >
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

jQuery(document).ready(function(){
	jQuery("#corresponding_table").change(function(){
		var val = jQuery(this).val();
		jQuery.post( "<?php echo JURI::root(); ?>/index.php?option=com_eventtableedit&task=etetable.setSessionOption", {'corresponding_table':val} , function( data ) {
			window.location.reload();
		});
	})
})

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
