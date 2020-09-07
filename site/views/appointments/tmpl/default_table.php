<?php
/**
 * @version		$Id: $
 * @package		eventtableedit
 * @copyright	Copyright (C) 2007 - 2020 Manuel Kaspar and Theophilix
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
 </style>

<!--<table data-tablesaw-mode-switch="" data-tablesaw-minimap="" data-tablesaw-sortable-switch="" data-tablesaw-sortable=""
 data-tablesaw-mode="swipe" class="tablesaw tablesaw-swipe tablesaw-sortable" id="etetable-table" style="">
-->
<table class="tablesaw" data-tablesaw-mode="columntoggle" data-tablesaw-minimap  id="etetable-table">
	<thead class="etetable-thead">
		<tr>
			<?php echo $this->loadTemplate('thead'); ?>
		</tr>
	</thead>

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
