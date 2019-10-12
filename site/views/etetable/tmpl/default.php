<?php
/**
 * $Id: default.php 144 2011-01-13 08:17:03Z kapsl $
 * @copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
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
$Itemid = 	$main->getInt('Itemid', '');

if($this->item->sorting == 1){
?>
<script>
		/*
 * Natural Sort algorithm for Javascript - Version 0.8.1 - Released under MIT license
 * Author: Jim Palmer (based on chunking idea from Dave Koelle)
 */
function naturalSort (a, b) {
			    var re = /(^([+\-]?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?(?=\D|\s|$))|^0x[\da-fA-F]+$|\d+)/g,
			        sre = /^\s+|\s+$/g,   // trim pre-post whitespace
			        snre = /\s+/g,        // normalize all whitespace to single ' ' character
			        dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
			        hre = /^0x[0-9a-f]+$/i,
			        ore = /^0/,
			        i = function(s) {
			            return (naturalSort.insensitive && ('' + s).toLowerCase() || '' + s).replace(sre, '');
			        },
			        // convert all to strings strip whitespace
			        x = i(a),
			        y = i(b),
			        // chunk/tokenize
			        xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
			        yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
			        // numeric, hex or date detection
			        xD = parseInt(x.match(hre), 16) || (xN.length !== 1 && Date.parse(x)),
			        yD = parseInt(y.match(hre), 16) || xD && y.match(dre) && Date.parse(y) || null,
			        normChunk = function(s, l) {
			            // normalize spaces; find floats not starting with '0', string or 0 if not defined (Clint Priest)
			            return (!s.match(ore) || l == 1) && parseFloat(s) || s.replace(snre, ' ').replace(sre, '') || 0;
			        },
			        oFxNcL, oFyNcL;
			    // first try and sort Hex codes or Dates
			    if (yD) {
			        if (xD < yD) { return -1; }
			        else if (xD > yD) { return 1; }
			    }
			    // natural sorting through split numeric strings and default strings
			    for(var cLoc = 0, xNl = xN.length, yNl = yN.length, numS = Math.max(xNl, yNl); cLoc < numS; cLoc++) {
			        oFxNcL = normChunk(xN[cLoc] || '', xNl);
			        oFyNcL = normChunk(yN[cLoc] || '', yNl);
			        // handle numeric vs string comparison - number < string - (Kyle Adams)
			        if (isNaN(oFxNcL) !== isNaN(oFyNcL)) {
			            return isNaN(oFxNcL) ? 1 : -1;
			        }
			        // if unicode use locale comparison
			        if (/[^\x00-\x80]/.test(oFxNcL + oFyNcL) && oFxNcL.localeCompare) {
			            var comp = oFxNcL.localeCompare(oFyNcL);
			            return comp / Math.abs(comp);
			        }
			        if (oFxNcL < oFyNcL) { return -1; }
			        else if (oFxNcL > oFyNcL) { return 1; }
			    }
			}
			    
    
	</script>
<style>
<?php if($this->item->show_pagination) { ?>
#etetable-table{display: none;}
<?php } ?>
span.filter-head{display: none;}
div.etetable-button{display: none;}
</style>
<?php 

foreach ($this->heads as $headSort) {
   $sortcalssscript = '';
	if($headSort->datatype == 'text'){
		$sortcalssscript = 'custom-sort'.$headSort->id;	
	
		?>
		<script type="text/javascript">
			jQuery(function() {
				jQuery( "#<?php echo $sortcalssscript; ?>" ).data( "tablesaw-sort", function( ascending ) {
					return  function( a, b ) {
						// a.cell
						// a.element
						// a.rowNum
						
						var a = a.cell;
						var b = b.cell;
						console.log(a);
						var re = /(^([+\-]?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?(?=\D|\s|$))|^0x[\da-fA-F]+$|\d+)/g,
					        sre = /^\s+|\s+$/g,   // trim pre-post whitespace
					        snre = /\s+/g,        // normalize all whitespace to single ' ' character
					        dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
					        hre = /^0x[0-9a-f]+$/i,
					        ore = /^0/,
					        i = function(s) {
					            return (naturalSort.insensitive && ('' + s).toLowerCase() || '' + s).replace(sre, '');
					        },
					        x = i(a),
					        y = i(b),
					        xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
					        yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
					        xD = parseInt(x.match(hre), 16) || (xN.length !== 1 && Date.parse(x)),
					        yD = parseInt(y.match(hre), 16) || xD && y.match(dre) && Date.parse(y) || null,
					        normChunk = function(s, l) {
					            return (!s.match(ore) || l == 1) && parseFloat(s) || s.replace(snre, ' ').replace(sre, '') || 0;
					        },
					        oFxNcL, oFyNcL;
					    if (yD) {
					        if (xD < yD) { 
					        	return -1;
					        }
					        else if (xD > yD) { 
					        	return 1;
					        }
					    }
					    for(var cLoc = 0, xNl = xN.length, yNl = yN.length, numS = Math.max(xNl, yNl); cLoc < numS; cLoc++) {
					        oFxNcL = normChunk(xN[cLoc] || '', xNl);
					        oFyNcL = normChunk(yN[cLoc] || '', yNl);
					        if (isNaN(oFxNcL) !== isNaN(oFyNcL)) {
					            return isNaN(oFxNcL) ? 1 : -1;
					        }
					        if (/[^\x00-\x80]/.test(oFxNcL + oFyNcL) && oFxNcL.localeCompare) {
					            var comp = oFxNcL.localeCompare(oFyNcL);
					            return comp / Math.abs(comp);
					        }
					        if (oFxNcL < oFyNcL) { 
					        	if( ascending ) {
					        	return -1;
					        	}else{
					        	return 1;	
					        	}
					        }
					        else if (oFxNcL > oFyNcL) {
					        	if( ascending ) {
					        	return 1;
					        	}else{
					        	return -1;
					        	}
					        	
					        }
					    }
					    


					};
				});
			});

		</script>

<?php } } ?>

<?php } ?>
<div class="eventtableedit<?php echo $this->params->get('pageclass_sfx')?>">

<ul class="actions">
	<?php if($this->item->show_print_view) :?>
	<li class="print-icon">
		<?php if (!$this->print) : ?>
			<?php echo JHtml::_('icon.print_popup',  $this->item, $this->params); ?>
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
<form action="<?php echo JRoute::_('index.php?option=com_eventtableedit'); ?>" name="adminForm" id="adminForm" method="post">
	<?php // echo '<pre>';print_r($this->item);

	//If there is already a table set up
	if ($this->heads) :?>
  
		<div class="etetable-outtable">
			<?php echo $this->loadTemplate('table'); ?>
		</div>
	<?php endif; ?>
	
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction') ?>" />
	<input type="hidden" name="filterstring" value="<?php echo $this->params->get('filterstring') ?>" />
	<input type="hidden" name="option" value="com_eventtableedit" />
	<input type="hidden" name="view" value="etetable" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
/**
 * Adding a new row
 */
?>
<?php if($this->params->get('access-add') && $this->heads) : ?>
	<div id="etetable-add" title="<?php echo JText::_('COM_EVENTTABLEEDIT_NEW_ROW'); ?>"></div>

<?php endif; ?>

<?php if($this->item->aftertext != '') :?>
	<div class="etetable-aftertext">
		<?php echo $this->item->aftertext; ?>
	</div>
<?php endif; ?>

</div>
<div style="clear:both"></div>
<?php
if($this->item->scroll_table){ 
	$scroll_table_height = ($this->item->scroll_table_height)?$this->item->scroll_table_height.'px':"200px";
?>
<style>
div.scroller {
  display: inline-block;
  height: <?php echo $scroll_table_height;?>;
  overflow: auto;
  float:left;
  width:100%;
  border-top: solid 1px #e5e5e4;
}

table#etetable-table th {
  position: -webkit-sticky;
  position: sticky;
  top: -1px;
  height: 34px;
}

#etetable-table thead th{
	border: 1px solid #e5e5e4;
    background: #e2dfdc;
    background-image: -webkit-linear-gradient(top, #fff, #e2dfdc);
    background-image: linear-gradient(to bottom, #fff, #e2dfdc);
}

</style>
<?php  } ?>
<?php if($this->item->show_pagination) { ?>
	<div>
		<a href="#" class="paginate" id="previous">&laquo;</a> <a href="#" class="paginate" id="next">&raquo;</a>
	</div>
<?php } ?>
<style>

.paginate{
	width: 20px;
	height: 20px;
	text-align: center;
	border: solid 1px #ccc;
	margin: 10px 5px 0 0;
	float: left;
	color: #000;
	text-decoration: none;
}
.paginate:hover, .paginate:active, .paginate:focus{
	text-decoration: none;
}
</style>
<script type='text/javascript'>
 

    // Selectors for future use
    var myTable = "#etetable-table";
    var myTableBody = myTable + " tbody";
    var myTableRows = myTableBody + " tr";
    var myTableColumn = myTable + " th";
 
    // Starting table state
    function initTable(size) {
		if(size == ""){
			size = 4;
		}
        jQuery(myTableBody).attr("data-pageSize", size);
        jQuery(myTableBody).attr("data-firstRecord", 0);
        jQuery('#previous').hide();
        jQuery('#next').show();
 
        // Increment the table width for sort icon support
    
 
        // Start the pagination
        paginate(parseInt(jQuery(myTableBody).attr("data-firstRecord"), 10),
                 parseInt(jQuery(myTableBody).attr("data-pageSize"), 10));
    }
 
    // Heading click
    jQuery(myTableColumn).click(function () {
        
 
        // Start the pagination
        paginate(parseInt(jQuery(myTableBody).attr("data-firstRecord"), 10),
                 parseInt(jQuery(myTableBody).attr("data-pageSize"), 10));
    });
 
    // Pager click
    jQuery("a.paginate").click(function (e) {
        e.preventDefault();
        var tableRows = jQuery(myTableRows);
        var tmpRec = parseInt(jQuery(myTableBody).attr("data-firstRecord"), 10);
        var pageSize = parseInt(jQuery(myTableBody).attr("data-pageSize"), 10);
 
        // Define the new first record
        if (jQuery(this).attr("id") == "next") {
            tmpRec += pageSize;
        } else {
            tmpRec -= pageSize;
        }
        // The first record is < of 0 or > of total rows
        if (tmpRec < 0 || tmpRec > tableRows.length) return
 
        jQuery(myTableBody).attr("data-firstRecord", tmpRec);
        paginate(tmpRec, pageSize);
    });
 
    // Paging function
    var paginate = function (start, size) {
        var tableRows = jQuery(myTableRows).not('.musthide');
        var end = start + size;
        // Hide all the rows
        tableRows.hide();
		
        // Show a reduced set of rows using a range of indices.
        tableRows.slice(start, end).show();
		jQuery(myTable).show();
        // Show the pager
        jQuery(".paginate").show();
		
		jQuery('.paginate').removeAttr('disabled');
        // If the first row is visible hide prev
        if (tableRows.eq(0).is(":visible")) jQuery('#previous').hide();
        // If the last row is visible hide next 
        if (tableRows.eq(tableRows.length - 1).is(":visible")) jQuery('#next').hide();
    }
	
	
jQuery(function () { 
   <?php if($this->item->show_pagination) { ?>
    initTable('<?php echo $this->item->pagebreak;?>');
   <?php } ?> 
});
</script>

<script>
var $rows = jQuery('#etetable-table tbody tr');
jQuery(document).ready(function() {
	jQuery('.filterstring').keyup(function() {
		var val = jQuery.trim(jQuery(this).val()).replace(/ +/g, ' ').toLowerCase();
		$rows.show().removeClass('musthide').filter(function() {
			var text = jQuery(this).find('td:visible').text().replace(/\s+/g, ' ').toLowerCase();
			//console.log(jQuery(this).find('td:visible').text());
			return !~text.indexOf(val);
		}).hide().addClass('musthide');
		//initTable(5);
		<?php if($this->item->show_pagination) { ?>
		initTable('<?php echo $this->item->pagebreak;?>');
	   <?php } ?> 
	});
});
</script>