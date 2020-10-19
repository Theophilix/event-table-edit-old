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
<tr id="rowId_<?php echo $this->rowCount; ?>"  data-id="<?php echo $this->rows[$this->rowCount]['id']; ?>">
<?php
/**
 * Optional first row.
 */
            $lang = JFactory::getLanguage();

if ($this->item->show_first_row) :?>
	<td id="first_row_<?php echo $this->rowCount; ?>" class="first_row_<?php echo $this->rowCount; ?> tablesaw-priority-50">
		<?php echo (int) $this->state->get('list.start') + $this->rowCount + 1; ?>
	</td>
<?php endif; ?>

<?php

for ($colCount = 0; $colCount < count($this->rows[0]) - 1; ++$colCount) {
    $atemptime = '';
    if ('date' === @$this->heads[$colCount]->datatype) {
        if ('&nbsp;' === $this->rows[$this->rowCount][$colCount] || '' === $this->rows[$this->rowCount][$colCount] || ' ' === $this->rows[$this->rowCount][$colCount]) {
            $atemptime = '<input type="hidden" value="0">';
        } else {
            //$atemptime = '<input type="hidden" value="'.strtotime($tempdates).'">';
        }
    } elseif ('boolean' === @$this->heads[$colCount]->datatype) {
        $pos = strpos($this->rows[$this->rowCount][$colCount], 'cross.png');
        $pos1 = strpos($this->rows[$this->rowCount][$colCount], 'tick.png');
        if (false !== $pos) {
            $atemptime = '<input type="hidden" value="0">';
        } elseif (false !== $pos1) {
            $atemptime = '<input type="hidden" value="1">';
        } else {
            $atemptime = '<input type="hidden" value="2">';
        }
    } elseif ('four_state' === @$this->heads[$colCount]->datatype) {
        $pos = strpos($this->rows[$this->rowCount][$colCount], 'cross.png');
        $pos1 = strpos($this->rows[$this->rowCount][$colCount], 'tick.png');
        $pos2 = strpos($this->rows[$this->rowCount][$colCount], 'question-mark.png');
        if (false !== $pos) {
            $atemptime = '<input type="hidden" value="0">';
        } elseif (false !== $pos1) {
            $atemptime = '<input type="hidden" value="1">';
        } elseif (false !== $pos2) {
            $atemptime = '<input type="hidden" value="2">';
        } else {
            $atemptime = '<input type="hidden" value="">';
        }
    } elseif ('float' === @$this->heads[$colCount]->datatype) {
        $float_val = str_replace(',', '.', $this->rows[$this->rowCount][$colCount]);
        $atemptime = '<input type="hidden" value="'.$float_val.'">';
    }

    if (0 === $colCount) {
        $mydyanmiclass = 'title';
    } else {
        $colCount1 = $colCount + 1;
        $mydyanmiclass = 'tablesaw-priority-'.$colCount;
    }

    /*
     * The cell content
     class="etetable-row_<?php echo $this->rowCount . '_' . $colCount.' '.$mydyanmiclass ; ?>"
     */ ?>
	 <?php
        // Add the hidden field in the last row
        if ($colCount === count($this->rows[0]) - 2) :?>
		<?php else:?>
	<td 
		id="etetable-row_<?php echo $this->rows[$this->rowCount]['id'].'_'.$colCount; ?>"><?php if ('' !== $atemptime) {
            echo $atemptime;
        } ?><?php echo str_replace('&nbsp;&nbsp;&nbsp;', '', trim($this->rows[$this->rowCount][$colCount])); ?><?php
        // Add the hidden field in the last row
        if ($colCount === count($this->rows[0]) - 2) :?>
			<input type="hidden" 
				   id="rowId_<?php echo $this->rowCount; ?>" 
				   name="rowId[]"
				   value="<?php echo $this->rows[$this->rowCount]['id']; ?>" />
		<?php endif; ?></td>
	<?php endif; ?>
<?php
}
?>
</tr>