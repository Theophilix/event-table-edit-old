<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<?php

 ?>
 <style type="text/css">
 .active.btn-success {
    background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: medium none;
    color: #000;
}
 #timestamp-head {
     display: none;
 }
.tablesaw-sortable th.tablesaw-sortable-head button {
    font-weight: bold;
    padding-bottom: 0.7em !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    padding-top: 0.9em !important;
    text-align: center;
}

/* Customized Demo CSS for our Demo Tables */
.tablesaw-columntoggle td.title a,
.tablesaw-swipe td.title a {
	display: inline;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	width: 10em;
}
.tablesaw-swipe td a,.tablesaw-columntoggle td a {
		display: inline;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 10em;
		max-width: 10em;
	}

.tablesaw-stack td a{
		display: inline;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 10em;
		max-width: 10em;

}


.tablesaw-stack td{padding:0.3em 0.5em !important;}

td.tablesaw-priority-50 a {
    color: #888;
    text-decoration: none;
}
.show_always{display:table-cell!important;}
@media (min-width: 40em) {
	td.title {
		/* max-width: 12em; */
	}
	.tablesaw-stack td a {
		display: inline;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 10em;
		max-width: 10em;
	}
}
 </style>
<script>
TablesawConfig = {
	swipeHorizontalThreshold: 20, // default is 15
	swipeVerticalThreshold: 40 // default is 30
};
</script>
<?php

$main  = JFactory::getApplication()->input;
$postget = $main->getArray($_REQUEST);
$sorting_enable = 'data-tablesaw-minimap ';
$switcher_enable = 'columntoggle';
if(@$postget['currentmode']){
	$tmodes = $postget['currentmode'];
}else{
	$tmodes = ($this->item->standardlayout) ? $this->item->standardlayout : $switcher_enable;
}

$sortdy = @$postget['sort']?@$postget['sort']:'0_asc';
if($this->item->sorting == 1){
	//$sorting_enable .= 'data-tablesaw-sortable data-tablesaw-sortable-switch ';
}
if($this->item->switcher == 1){
	$sorting_enable .= 'data-tablesaw-mode-switch';
}
?>

<?php
if($this->item->sorting == 1)
{
$options = '';
$default = JText::_('COM_EVENTTABLEEDIT_SELECT_SORT');
$heads = $this->heads;

$ordering           = new stdclass;
$ordering->datatype = '';
$ordering->head     = 'a.timestamp';
$ordering->name     = 'timestamp';
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$heads = (object) array_merge((array)$heads, (array)array($ordering));

foreach($heads as $head)
{
   $types     = array('text','link','email');
   $name_asc  = $head->name;
   $name_desc = $head->name;
   $asc       = '&uarr;';
   $desc      = '&darr;';

   if($head->name == 'timestamp')
   {
	  $name_asc  = JText::_('COM_EVENTTABLEEDIT_NEWEST');
      $name_desc = JText::_('COM_EVENTTABLEEDIT_OLDEST');
   }
   
   if(in_array($head->datatype, $types))
   {
	  $asc  = '(A-Z)';
	  $desc = '(Z-A)';
   }
   
   
   
   $selected1 = $head->head == $listOrder && $listDirn == 'asc' ? ' selected' && $default = $name_asc.' '.$asc : '';
   $selected2 = $head->head == $listOrder && $listDirn == 'desc' ? ' selected' && $default = $name_desc. ' '.$desc : '';
   $options   .= '<option value="'.$head->head.'|asc"'.$selected1.'>'.$name_asc.' '.$asc.'</option>';
   $options   .= '<option value="'.$head->head.'|desc"'.$selected2.'>'.$name_desc.' '.$desc.'</option>';
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
	setTimeout(function() {
		//console.log(jQuery(".tablesaw-bar"));
		jQuery(".tablesaw-bar").prepend('<?php echo $sort_select; ?>');
	}, 1000);
   
   
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
<table class="tablesaw" id="etetable-table"  data-tablesaw-mode="<?php echo $tmodes; ?>" <?php echo $sorting_enable; ?>>
	<thead class="etetable-thead">
		<tr>
			<?php echo $this->loadTemplate('thead'); ?>
		</tr>
	</thead>

	<?php 

	if(!$this->print) : /* ?>
	<tfoot class="limit">
		<tr>
			<td colspan="100%" class="show_always">
				<div id="container">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="100%" class="show_always">
				<div id="container">
					<?php echo $this->pagination->getListFooter() ?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="100%" class="show_always">
				<div id="container">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</div>
			</td>
		</tr>

		

	</tfoot> 
	<?php */ endif; ?>	

	<tbody>
	<?php
	/**
	 * The table body
	 */
	 
	if ($this->rows) {
		for($this->rowCount = 0; $this->rowCount < count($this->rows); $this->rowCount++) { ?>
			
				<?php echo $this->loadTemplate('row'); ?> 
			
			
			<?php
		}
	} ?>
	</tbody>
</table>
<div style="display: none;" id="num-of-col" data-num-of-col="<?php echo isset($this->rows[0]) ? (count($this->rows[0]) - 2) : 0?>">
<script>
    jQuery(document).ready(function () {
        var numCol = jQuery('#timestamp-head').parent().children().index(jQuery('#timestamp-head'));
        jQuery('#etetable-table td:nth-child('+(numCol+1)+')').hide();
    });
	
	jQuery(document).ready(function(){
		if(jQuery( window ).width()<=400){
			setTimeout(function() {
				$('select#change_mode').val('swipe');
				$('select#change_mode').trigger('change');
			}, 1000);			
		}
	});

</script>
</div>