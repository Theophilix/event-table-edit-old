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
     * The table body.
     */
    if ($this->rows) {
        for ($this->rowCount = 0; $this->rowCount < count($this->rows); ++$this->rowCount) { ?>
			<tr>
				<?php echo $this->loadTemplate('row'); ?> 
			</tr>
			
			<?php
        }
    } ?>
	</tbody>
</table>
