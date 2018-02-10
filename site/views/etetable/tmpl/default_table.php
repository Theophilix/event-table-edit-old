<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2010 Manuel Kaspar
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
		max-width: 12em;
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
<?php
$main  = JFactory::getApplication()->input;
$postget = $main->getArray($_REQUEST);
$sorting_enable = 'data-tablesaw-minimap ';
$switcher_enable = 'columntoggle';
if(@$postget['mode']){
	$tmodes = $postget['mode'];
}else{
	$tmodes = $switcher_enable;
}
$sortdy = @$postget['sort']?@$postget['sort']:'0_asc';
if($this->item->sorting == 1){
	$sorting_enable .= 'data-tablesaw-sortable data-tablesaw-sortable-switch ';
}
if($this->item->switcher == 1){
	$sorting_enable .= 'data-tablesaw-mode-switch';
}
?>
<table class="tablesaw" id="etetable-table" data-tablesaw-mode="<?php echo $tmodes; ?>"  <?php echo $sorting_enable; ?>>
	<thead class="etetable-thead">
		<tr>
			<?php echo $this->loadTemplate('thead'); ?>
		</tr>
	</thead>

	<?php 

	if(!$this->print) : ?>
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
	<?php endif; ?>	

	<tbody>
	<?php
	/**
	 * The table body
	 */
	if ($this->rows) {
		for($this->rowCount = 0; $this->rowCount < count($this->rows); $this->rowCount++) { ?>
			<tr>
				<?php echo $this->loadTemplate('row'); ?> 
			</tr>
			
			<?php
		}
	} ?>
	</tbody>
</table>
<div style="display: none;" id="num-of-col" data-num-of-col="<?=isset($this->rows[0]) ? (count($this->rows[0]) - 2) : 0?>">
<script>
    jQuery(document).ready(function () {
        var numCol = jQuery('#timestamp-head').parent().children().index(jQuery('#timestamp-head'));
        jQuery('#etetable-table td:nth-child('+(numCol+1)+')').hide();
    });
	
	jQuery(document).ready(function(){
		if(jQuery( window ).width()<=400){
			jQuery('.tablesaw-modeswitch span.btn-select select').val('swipe');
			jQuery('.tablesaw-modeswitch span.btn-select select').change();
		}
	});
	
	jQuery(window).resize(function(){
		if(jQuery( window ).width()<=400){
			jQuery('.tablesaw-modeswitch span.btn-select select').val('swipe');
			jQuery('.tablesaw-modeswitch span.btn-select select').change();
		}else{
			jQuery('.tablesaw-modeswitch span.btn-select select').val('columntoggle');
			jQuery('.tablesaw-modeswitch span.btn-select select').change();
			
		}
		
	})
</script>