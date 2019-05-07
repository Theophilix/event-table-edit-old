<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Optional first row
 */
if ($this->item->show_first_row) :?>
	<th class="etetable-first_row tablesaw-priority-50">#</th>
<?php endif; ?>

<?php
/**
 * The table heads
 */
$thcount = 0;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
if(count($this->heads) > 6){
$cont = round(count($this->heads)/12);
}else if(count($this->heads) > 3 && count($this->heads) < 6){
$cont = round(count($this->heads)/6);
}else{
	$cont = 1;
}
//$cont = round((count($this->heads)+ $this->item->show_first_row)/6);
//$cont = 1;
$main  = JFactory::getApplication()->input;
$postget = $main->getArray($_REQUEST);
if(@$postget['sort']){
$sortdynamic = explode('_', $postget['sort']);
$sortdynamic = $sortdynamic[0];
}else{
	$sortdynamic = 0;
}
$j=0;
$doc = JFactory::getDocument();
foreach ($this->heads as $head) { 
	$sortcalss = '';
	if($head->datatype == 'text'){
		$sortcalss = 'custom-sort'.$head->id;	
	}
	/*if($head->head == 'link' || $head->head == 'mail'){
		$priority = "persist";
		$classofdynamic = "";
	}else */
	if($thcount == 0){
		$priority = "persist";
		$classofdynamic = "";
	}else{
		$priority = $thcount;
		$classofdynamic = 'tablesaw-priority-'.$priority;
	}
		
			if($classofdynamic==""){
				$myclass =  $thcount;
			}else{
				$myclass = $thcount.' '.$classofdynamic;
				}
			
			$icon = '';
			$dir = '';
			if($this->item->sorting == 1)
			{
			// Added Sort
			$icon = $head->head == $listOrder ? ($listDirn == 'desc' ? '&darr;' : '&uarr;') : '';
            $dir = $head->head == $listOrder ? ($listDirn == 'desc' ? 'asc' : 'desc') : 'asc';
?>
	<th class="evth<?php echo $myclass; ?>" id="<?php echo $sortcalss; ?>" <?php //if($j==$sortdynamic){ echo 'data-tablesaw-sortable-default-col="true"'; }  ?> data-tablesaw-priority="<?php echo $priority; ?>" scope="col"><a data-col="<?php echo $head->head; ?>|<?php echo $dir; ?>" class="sort" href="javascript:void(0);"><?php 	echo trim($head->name). ' '. $icon;?> </a></th>
    <?php
			}
			else
			{
			?>
            <th class="evth<?php echo $myclass; ?>" id="<?php echo $sortcalss; ?>" data-tablesaw-priority="<?php echo $priority; ?>" scope="col"><?php 	echo trim($head->name). ' '. $icon;?></th>
            <?php
			}
			?>
	<?php
	
	if($j%$cont == 0){
	$thcount++;
	}
$j++;
}	
?>
<th class="evth<?php echo $myclass; ?>" id="timestamp-head" <?php //if($j==$sortdynamic){ echo 'data-tablesaw-sortable-default-col="true"'; }  ?> data-tablesaw-priority="<?php echo $priority; ?>" scope="col">Timestamp</th>

<?php
if($this->item->sorting == 1)
{
$options = '';
$default = JText::_('COM_EVENTTABLEEDIT_SELECT_SORT');
foreach($this->heads as $head)
{
   $selected1 = $head->head == $listOrder && $listDirn == 'asc' ? ' selected' && $default = $head->name.' &uarr;' : '';
   $selected2 = $head->head == $listOrder && $listDirn == 'desc' ? ' selected' && $default = $head->name. ' &darr;' : '';
   $options   .= '<option value="'.$head->head.'|asc"'.$selected1.'>'.$head->name.' &uarr;</option>';
   $options   .= '<option value="'.$head->head.'|desc"'.$selected2.'>'.$head->name.' &darr;</option>';
}
$select = '';
$sort_select = '<div class="table-sorting tablesaw-toolbar"><label>'.JText::_('COM_EVENTTABLEEDIT_SORT').':<span class="btn btn-small btn-select">'.$default.'<select class="sort-select">'.$options.'</select></span></label></div>';
?>

<script type="text/javascript">
jQuery(document).ready(function(){
   jQuery(".sort").click(function(){
      $list = jQuery(this).data("col").split("|");
	  jQuery("input[name=filter_order]").val($list[0]);
	  jQuery("input[name=filter_order_Dir]").val($list[1]);
	  jQuery(this).closest("form").submit();
  });
   
   jQuery(".tablesaw-modeswitch").parent().prepend('<?php echo $sort_select; ?>');
   
   jQuery(document).on('change', '.sort-select', function() {
	  $list = jQuery(this).val().split("|");
	  jQuery("input[name=filter_order]").val($list[0]);
	  jQuery("input[name=filter_order_Dir]").val($list[1]);
	  jQuery(this).closest("form").submit();
   });
});
</script>
<?php
}
?>