<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$switcher_enable = 'columntoggle';
if (@$postget['currentmode']) {
    $tmodes = $postget['currentmode'];
} else {
    $tmodes = ($this->item->standardlayout) ? $this->item->standardlayout : $switcher_enable;
}
?>

<form method="post" name="filterform" action="<?php echo JRoute::_('index.php?option=com_eventtableedit&view=etetable&id='.$this->item->slug); ?>" class="filterform" onsubmit="return checkMethod();">
	<span class="filter-head">
		<?php echo JText::_('COM_EVENTTABLEEDIT_FILTER'); ?>
	</span>
	
	<?php
    $main = JFactory::getApplication()->input;
 $filterstring = $this->state->get('filterstring');
     $filterstring1 = $this->state->get('filterstring1');
    //$filterstring = $this->params->get('filterstring');
    if ($this->additional['containsDate']) :

    else : ?>
		
		
	<?php endif; ?>
	<?php
    /* echo JHTML::calendar($filterstring1, 'filterstring1', 'filterstring1', '%Y-%m-%d', array ('placeholder' => JTExt::_('COM_EVENTTABLEEDIT_CALANDER_PACHEHOLDER'),'class'=>'filterstring', 'size'=>'20', 'maxlength'=>'100')); */
    //echo JHTML::calendar($filterstring1, 'filterstring1', 'filterstring1', $this->item->dateformat, array ('placeholder' => JTExt::_('COM_EVENTTABLEEDIT_CALANDER_PACHEHOLDER'),'class'=>'filterstring', 'size'=>'20', 'maxlength'=>'100'));
    ?>
	&nbsp;
	<div class="input-append filterstext">
		<input type="text" class="filterstring_<?php echo $this->unique; ?>" name="filterstring" value="<?php echo $filterstring; ?>" size="20" maxlength="100" placeholder="<?php echo JTExt::_('COM_EVENTTABLEEDIT_FILTER_PACHEHOLDER'); ?>" />
	</div>
	<div class="etetable-button">
		<!-- <a href="javascript:document.filterform.submit();" >
			<?php //echo JText::_('COM_EVENTTABLEEDIT_SHOW');?>
		</a> -->
		<input class="filtersub" type="submit" value="<?php echo JText::_('COM_EVENTTABLEEDIT_SHOW'); ?>"> <!--onclick="document.filterform.submit();"-->
	</div>

	<div class="etetable-button">
		<a href="javascript:document.filterform.filterstring.value = '';document.filterform.filterstring1.value = ''; jQuery('#currentmode').val(jQuery('.tablesaw-modeswitch span.btn-select select').val()); document.filterform.submit();">
			<?php echo JText::_('COM_EVENTTABLEEDIT_RESET'); ?>
		</a>
	</div>
	<div class="tooltip_box" style="padding: 5px;position:relative;">
		<input type="hidden" name="currentmode" id="currentmode" value="<?php echo $tmodes; ?>"/>
		<img id="tooltip_img" alt="Tooltip" alt="Tooltip" src='<?php echo JURI::base(); ?>media/system/images/tooltip.png' />
		<div id="etetable_tooltip" class="tip tool-tip" style="display: none; background: rgb(232, 232, 232);padding: 10px;position: absolute;z-index: 999;left: 29px;top: 17px;width: 210px;"><div><div class="tip-title tool-title" style="width: 100%;font-weight: bold;margin-bottom: 10px;"><span><?php echo JText::_('COM_EVENTTABLEEDIT_FILTER'); ?><br></span></div><div class="tip-text tool-text"><span><?php echo JText::_('COM_EVENTTABLEEDIT_FILTER_TOOL_TIP'); ?></span></div></div></div>
	</div>
	<?php //echo JHTML::tooltip(JText::_('COM_EVENTTABLEEDIT_FILTER_TOOL_TIP'), JText::_('COM_EVENTTABLEEDIT_FILTER'), 'tooltip.png', '', '', false);
    //
    //
    ?>
</form>
<script>
function checkMethod(){
	jQuery("#currentmode").val(jQuery('.tablesaw-modeswitch span.btn-select select').val());
	//return false;
}

jQuery(document).ready(function(){
	jQuery('.tablesaw-modeswitch span.btn-select select').on("change",function(){ 
		jQuery("#currentmode").val(jQuery(this).val()) 
	})
	<?php if (isset($postget['currentmode']) && '' !== $postget['currentmode']) {
        ?>
		jQuery('.tablesaw-modeswitch span.btn-select select').val('<?php echo $postget['currentmode']; ?>');
		jQuery('.tablesaw-modeswitch span.btn-select select').change();
		<?php
    }?>
});
jQuery("#tooltip_img").mouseenter(function(){
	jQuery('#etetable_tooltip').show();
})
jQuery("#tooltip_img").mouseleave(function(){
	jQuery('#etetable_tooltip').hide();
})
</script>