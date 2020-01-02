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
<tr id="rowId_<?php echo $this->rowCount; ?>"  data-id="<?php echo $this->rows[$this->rowCount]['id']; ?>">
<?php
/**
 * Optional first row
 */
			$lang = JFactory::getLanguage();

if ($this->item->show_first_row) :?>
	<td id="first_row" class="first_row_<?php echo $this->rowCount; ?> tablesaw-priority-50">
		<?php echo ((int) $this->state->get('list.start') + $this->rowCount + 1); ?>
	</td>
<?php endif; ?>

<?php 

for($colCount = 0; $colCount < count($this->rows[0]) - 1; $colCount++) { 
	$atemptime = '';
	if(@$this->heads[$colCount]->datatype == 'date'){

		
		if($this->rows[$this->rowCount][$colCount] == '&nbsp;' || $this->rows[$this->rowCount][$colCount] == '' || $this->rows[$this->rowCount][$colCount] == ' '){
			$atemptime = '<input type="hidden" value="0">';
		}else{

		//$atemptime = '<input type="hidden" value="'.strtotime($tempdates).'">';
		}
	}else if(@$this->heads[$colCount]->datatype == 'boolean'){
		$pos = strpos($this->rows[$this->rowCount][$colCount],'cross.png');
		$pos1 = strpos($this->rows[$this->rowCount][$colCount],'tick.png');
		if ($pos !== false) {
			$atemptime = '<input type="hidden" value="0">';
		} else if($pos1 !== false){
			$atemptime = '<input type="hidden" value="1">';
		}else{
			$atemptime = '<input type="hidden" value="2">';
		}
	}else if(@$this->heads[$colCount]->datatype == 'four_state'){
		$pos = strpos($this->rows[$this->rowCount][$colCount],'cross.png');
		$pos1 = strpos($this->rows[$this->rowCount][$colCount],'tick.png');
		$pos2 = strpos($this->rows[$this->rowCount][$colCount],'question-mark.png');
		if ($pos !== false) {
			$atemptime = '<input type="hidden" value="0">';
		} else if($pos1 !== false){
			$atemptime = '<input type="hidden" value="1">';
		}else if($pos2 !== false){
			$atemptime = '<input type="hidden" value="2">';
		}else{
			$atemptime = '<input type="hidden" value="">';
		}
	}else if(@$this->heads[$colCount]->datatype == 'float'){
			$float_val = str_replace(',','.',$this->rows[$this->rowCount][$colCount]);
			$atemptime = '<input type="hidden" value="'.$float_val.'">';
		
	}

	if($colCount == 0){
		$mydyanmiclass = 'title';
	}else{
		$colCount1 = $colCount + 1;
		$mydyanmiclass = 'tablesaw-priority-'.$colCount;	
	}
	
	/**
	 * The cell content
	 class="etetable-row_<?php echo $this->rowCount . '_' . $colCount.' '.$mydyanmiclass ; ?>" 
	 */ ?>
	 <?php
		// Add the hidden field in the last row
		if ($colCount == count($this->rows[0]) - 2) :?>
		<?php else:?>
	<td 
		id="etetable-row_<?php echo $this->rows[$this->rowCount]['id'] . '_' . $colCount; ?>"><?php if($atemptime != ''){  echo $atemptime; } ?><?php echo str_replace("&nbsp;&nbsp;&nbsp;","",trim($this->rows[$this->rowCount][$colCount])); ?><?php
		// Add the hidden field in the last row
		if ($colCount == count($this->rows[0]) - 2) :?>
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