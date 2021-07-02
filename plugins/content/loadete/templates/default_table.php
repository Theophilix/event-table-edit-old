<?php
/**
 * @version		$Id: $
 *
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<script>
TablesawConfig = {
	swipeHorizontalThreshold: 20, // default is 15
	swipeVerticalThreshold: 40 // default is 30
};
</script>
<?php

$main = JFactory::getApplication()->input;
$postget = $main->getArray();
$sorting_enable = 'data-tablesaw-minimap ';
$switcher_enable = 'columntoggle';
if (@$postget['currentmode']) {
    $tmodes = $postget['currentmode'];
} elseif (@$postget[$this->item->alias.'change_mode']) {
    $tmodes = $postget[$this->item->alias.'change_mode'];
} else {
    $tmodes = (isset($this->item->standardlayout) && $this->item->standardlayout) ? $this->item->standardlayout : $switcher_enable;
}

$sortdy = @$postget['sort'] ? @$postget['sort'] : '0_asc';
if (1 === (int)$this->item->sorting) {
    //$sorting_enable .= 'data-tablesaw-sortable data-tablesaw-sortable-switch ';
}
if (1 === (int)$this->item->switcher) {
    $sorting_enable .= 'data-tablesaw-mode-switch';
}
?>

<?php
if (1 === (int)$this->item->sorting) {
    $options = '';
    $default = JText::_('COM_EVENTTABLEEDIT_SELECT_SORT');
    $heads = $this->heads;

    $ordering = new stdclass();
    $ordering->datatype = '';
    $ordering->head = 'a.timestamp';
    $ordering->name = 'timestamp';
    $listOrder = $this->state->get($this->item->id.'list.ordering');
    $listDirn = $this->state->get($this->item->id.'list.direction');
    $heads = (object) array_merge((array) $heads, (array) [$ordering]);

    foreach ($heads as $head) {
        $types = ['text', 'link', 'email'];
        $name_asc = $head->name;
        $name_desc = $head->name;
        $asc = '&uarr;';
        $desc = '&darr;';

        if ('timestamp' === $head->name) {
            $name_asc = JText::_('COM_EVENTTABLEEDIT_NEWEST');
            $name_desc = JText::_('COM_EVENTTABLEEDIT_OLDEST');
        }

        if (in_array($head->datatype, $types)) {
            $asc = '(A-Z)';
            $desc = '(Z-A)';
        }

        $selected1 = $head->head === $listOrder && 'asc' === $listDirn ? ' selected' && $default = $name_asc.' '.$asc : '';
        $selected2 = $head->head === $listOrder && 'desc' === $listDirn ? ' selected' && $default = $name_desc.' '.$desc : '';
        $name_asc_asc = $name_asc.' '.$asc;
        $name_desc_desc = $name_desc.' '.$desc;
        $options .= "<option value='".$head->head."|asc' ".$selected1.' >'.$name_asc_asc.'</option>';
        $options .= "<option value='".$head->head."|desc' ".$selected2.' >'.$name_desc_desc.'</option>';
    }
    $select = '';
    $sort_select = '<div class=\'table-sorting tablesaw-toolbar\'><label>'.JText::_('COM_EVENTTABLEEDIT_SORT').':<span class=\'btn btn-small btn-select\'>'.$default.'<select class=\'sort-select\'>'.$options.'</select></span></label></div>'; ?>

<script >
jQuery(document).ready(function(){
	jQuery("#<?php echo $this->unique; ?> .sort").click(function(){
		$list = jQuery(this).data("col").split("|");
		jQuery("#<?php echo $this->unique; ?> input[name=filter_order]").val($list[0]);
		jQuery("#<?php echo $this->unique; ?> input[name=filter_order_Dir]").val($list[1]);
		jQuery("#<?php echo $this->unique; ?> input[name=<?php echo $this->item->alias; ?>change_mode]").val($('#<?php echo $this->unique; ?> #change_mode').val());
		jQuery(this).closest("form").submit();
	});
	jQuery('body').on('click',"#<?php echo $this->unique; ?> .sort",function(){
		$list = jQuery(this).data("col").split("|");
		jQuery("#<?php echo $this->unique; ?> input[name=filter_order]").val($list[0]);
		jQuery("#<?php echo $this->unique; ?> input[name=filter_order_Dir]").val($list[1]);
		jQuery("#<?php echo $this->unique; ?> input[name=<?php echo $this->item->alias; ?>change_mode]").val($('#<?php echo $this->unique; ?> #change_mode').val());
		jQuery(this).closest("form").submit();
	});
	setTimeout(function() {
		//console.log(jQuery(".tablesaw-bar"));
		jQuery("#<?php echo $this->unique; ?> .tablesaw-bar").prepend("<?php echo $sort_select; ?>");
	}, 1000);
   
   
   jQuery(document).on('change', '#<?php echo $this->unique; ?> .sort-select', function() {
	  $list = jQuery(this).val().split("|");
	  jQuery("input[name=filter_order]").val($list[0]);
	  jQuery("input[name=filter_order_Dir]").val($list[1]);
	  jQuery("#<?php echo $this->unique; ?> input[name=<?php echo $this->item->alias; ?>change_mode]").val($('#<?php echo $this->unique; ?> #change_mode').val());
	  jQuery(this).closest("form").submit();
   });
});
</script>
<?php
}
?>
<table class="tablesaw" id="etetable-table_<?php echo $this->unique; ?>"  data-tablesaw-mode="<?php echo $tmodes; ?>" <?php echo $sorting_enable; ?>>
	<thead class="etetable-thead">
		<tr>
			<?php include JPATH_SITE.'/plugins/content/loadete/templates/default_thead.php'; ?>
		</tr>
	</thead>

	<?php

    if (!$this->print) : /* ?>
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
     * The table body.
     */
    if ($this->rows) {
        for ($this->rowCount = 0; $this->rowCount < count($this->rows); ++$this->rowCount) { ?>
			
				<?php include JPATH_SITE.'/plugins/content/loadete/templates/default_row.php'; ?> 
			
			
			<?php
        }
    } ?>
	</tbody>
</table>
<div style="display: none;" id="num-of-col_<?php echo $this->unique; ?>" data-num-of-col="<?php echo isset($this->rows[0]) ? (count($this->rows[0]) - 2) : 0; ?>">
<script>
    jQuery(document).ready(function () {
        var numCol = jQuery('#timestamp-head').parent().children().index(jQuery('#timestamp-head'));
        jQuery('#etetable-table_<?php echo $this->unique; ?> td:nth-child('+(numCol+1)+')').hide();
    });
	
	jQuery(document).ready(function(){
		if(jQuery( window ).width()<=400){
			setTimeout(function() {
				$('select#change_mode').val('swipe');
				$('select#change_mode').trigger('change');
			}, 1000);			
		}
	});
	jQuery(document).ready(function(){
		/* <?php if (isset($_REQUEST[$this->item->alias.'change_mode']) && '' !== $_REQUEST[$this->item->alias.'change_mode']) {?>
			setTimeout(function() {
				$('#<?php echo $this->unique; ?> select#change_mode').val("<?php echo $_REQUEST[$this->item->alias.'change_mode']; ?>");
				$('#<?php echo $this->unique; ?> select#change_mode').trigger('change');
			}, 1000);			
		<?php }?> */
	});

</script>
</div>