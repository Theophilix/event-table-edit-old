<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2018 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<form method="post" name="filterform" action="<?php echo JRoute::_('index.php?option=com_eventtableedit&view=etetable&id='.$this->item->slug); ?>" class="filterform">
	<span class="filter-head">
		<?php echo JText::_('COM_EVENTTABLEEDIT_FILTER'); ?>
	</span>
	
	<?php 
	$main  = JFactory::getApplication()->input;
 $filterstring = 	$this->state->get('filterstring');
	 $filterstring1 = 	$this->state->get('filterstring1');
	//$filterstring = $this->params->get('filterstring');
	if ($this->additional['containsDate']) :
		
	else : ?>
		
		
	<?php endif; ?>
	<?php 
	echo JHTML::calendar($filterstring1, 'filterstring1', 'filterstring1', '%Y-%m-%d', array ('placeholder' => JTExt::_('COM_EVENTTABLEEDIT_CALANDER_PACHEHOLDER'),'class'=>'filterstring', 'size'=>'20', 'maxlength'=>'100'));?>
	&nbsp;
	<div class="input-append filterstext">
		<input type="text" class="filterstring" name="filterstring" value="<?php echo $filterstring ?>" size="20" maxlength="100" placeholder="<?php echo JTExt::_('COM_EVENTTABLEEDIT_FILTER_PACHEHOLDER'); ?>" />
	</div>
	<div class="etetable-button">
		<!-- <a href="javascript:document.filterform.submit();" >
			<?php //echo JText::_('COM_EVENTTABLEEDIT_SHOW'); ?>
		</a> -->
		<input class="filtersub" type="submit" value="<?php echo JText::_('COM_EVENTTABLEEDIT_SHOW'); ?>" onclick="document.filterform.submit();">
	</div>

	<div class="etetable-button">
		<a href="javascript:document.filterform.filterstring.value = '';document.filterform.filterstring1.value = ''; document.filterform.submit();">
			<?php echo JText::_('COM_EVENTTABLEEDIT_RESET'); ?>
		</a>
	</div>
	
	&nbsp;
	<?php echo JHTML::tooltip(JText::_('COM_EVENTTABLEEDIT_FILTER_TOOL_TIP'), JText::_('COM_EVENTTABLEEDIT_FILTER'), 'tooltip.png', '', '', false); ?>
</form>
