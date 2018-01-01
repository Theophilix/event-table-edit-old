<?php 
/**
 * $Id:$
 * @copyright (C) 2007 - 2018 Manuel Kaspar and Theophilix
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
?>

<script type="text/javascript">
<!--

var dropdowns = null;

window.addEvent('load', function() {
    dropdowns = new Dropdowns();
    dropdowns.loadDropdowns();

    addNewEvent();
});

Joomla.submitbutton = function(task) {
	if (task == 'dropdown.cancel' || dropdowns.validate()) {
		dropdowns.serializeToForm();
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

function addNewEvent() {
	$('addNew').addEvent('click', function() {
		var dropdown = new Dropdown('');
		dropdowns.dropdowns.push(dropdown);
		dropdowns.addRows(dropdowns.dropdowns.length - 1, -1);
		
		// Update ordering of last row if exists
		if(dropdowns.dropdowns.length > 1) {
			var lastRow = dropdowns.dropdowns[dropdowns.dropdowns.length - 2];
			var cell = lastRow.row.cells[1];
			lastRow.updateCell(cell, lastRow.addOrdering());
		}
			
		dropdown.row.cells[0].firstChild.fireEvent('click');
	});
}

/**
 * A list of all dropdowns
 */
function Dropdowns() {
	this.table = $('dropdown-table').tBodies[0];
	
	this.dropdowns = new Array();
}

Dropdowns.prototype.loadDropdowns = function() {
	this.generateObjects();
	this.addRows(0, -1);
}

Dropdowns.prototype.generateObjects = function() {
	var dropdown = null;
	
	<?php if (count($this->dropdowns) > 0) :
	
		foreach ($this->dropdowns as $dropdown) : ?>
			dropdown = new Dropdown(<?php echo "'" . $dropdown->name . "'" ?>);
			this.dropdowns.push(dropdown);
		<?php endforeach; 
	endif; ?>
}

/**
 * Adds the rows in the html table
 * If endRow is -1 all rows are added
 */
Dropdowns.prototype.addRows = function(startRow, endRow) {
	// If used for reordering
	if (endRow == -1) {
		endRow = this.dropdowns.length;
	}

	for (var a = startRow; a < endRow; a++) {
		var row = this.table.insertRow(a);
		
		this.dropdowns[a].row = row;
		this.dropdowns[a].rowNumber = a;
		
		this.dropdowns[a].addCell(this.dropdowns[a].addName());
		this.dropdowns[a].addCell(this.dropdowns[a].addOrdering());
		this.dropdowns[a].addCell(this.dropdowns[a].addDeleteIcon());
	}
}

/**
 * One dropdown object
 */
function Dropdown(name) {
	// A reference to the row
	this.row = null;
	this.rowNumber = null;
	
	this.name = name;
}

/**
 * For adding all the new cells
 */
Dropdown.prototype.addCell = function(func) {
	var cell = this.row.insertCell(-1);
	func.inject(cell);
}

/**
 * For updating a cell
 */
 Dropdown.prototype.updateCell = function(cell, func) {
	// Delete content if something there
	while (cell.childNodes.length >= 1) {
        cell.removeChild(cell.firstChild);       
    } 

	func.inject(cell);
}

/**
 * Adds the name cell
 */
Dropdown.prototype.addName = function() {
	var span = new Element('span', {
		'id'	: 'dropdowns-name',
		'text'  : this.name,
		'events': {
			'click': (function(dropdown) {
				return function () {
					dropdown.editName();
				}
			})(this)
		}
	});
	
	var editImg = new Element('img', {
		'src': '<?php echo JURI::root(); ?>media/system/images/edit.png'
	});
	
	editImg.inject(span);
	
	return span;
}

 /**
  * Adds the ordering cell
  */
  Dropdown.prototype.addOrdering = function() {
 	var span = new Element('span', {
 		'class'	: 'dropdown-ordering-span'
 	});
 	
 	if (this.rowNumber > 0) {
 		this.getOrderingImg("up").inject(span);
 	}
 	if (this.rowNumber < dropdowns.dropdowns.length - 1) {
 		this.getOrderingImg("down").inject(span);
 	}
 	
 	return span;
 }

 /**
  * Helper function for adding the ordering images
  */
Dropdown.prototype.getOrderingImg = function(direction) {
 	var imgUp = new Element('span', {
 		'id'	: 'dropdown-ordering',
 		'class'	: direction + 'arrow',
 		'events': {
 			'click': (function(dropdown, direction) {
 				return function () {
 					dropdowns.reorder(dropdown, direction);
 				}
 			})(this, direction)
 		}		
 	});
 	return imgUp;
}

/**
 * Adds the icon to delete a row
 */
Dropdown.prototype.addDeleteIcon = function() {
	var deleteIcon = new Element('img', {
		'src': '<?php echo JURI::root(); ?>administrator/components/com_eventtableedit/template/images/cross.png',
		'id': 'dropdown-deleteIcon',
		'events': {
			'click': (function(dropdown) {
				return function () {
					dropdowns.deleteRow(dropdown);
				}
			})(this)
		}		
	});
	
	return deleteIcon;
}

/**
 * Edit the name of a dropdown
 */
Dropdown.prototype.editName = function() {
	// Remove cell content
	var cell = this.row.cells[0];
	$(cell.firstChild).dispose();
	
	// Add input field
	var inputField = new Element('input', {
		'type'	: 'text',
		'id'	: 'dropdown-inputfield-active',
		'class'	: 'dropdown-inputfield',
		'value'	: this.name,
		'events': {
			'blur': (function(dropdown, cell) {
				return function () {
					// Save value in dropdown Object an delete inputfield
					dropdown.name = $('dropdown-inputfield-active').value;
					
					// Add normal name text again
					dropdown.updateCell(cell, dropdown.addName());			
				}
			})(this, cell)
		}
	});
	inputField.inject(cell);
	
	// Only works this way in the internet explorer
	setTimeout("doFocus()", 100);
}

function doFocus() {
	$('dropdown-inputfield-active').focus();
}

/**
 * Reorder a row
 */
Dropdowns.prototype.reorder = function(dropdown, direction) {
	// Reorder rows in dropdown array
	var newRow = -1;
	if (direction == 'up') {
		newRow = dropdown.rowNumber - 1;
	} else {
		newRow = dropdown.rowNumber + 1;
	}
	
	var temp = this.dropdowns[dropdown.rowNumber];
	this.dropdowns[dropdown.rowNumber] = this.dropdowns[newRow];
	this.dropdowns[newRow] = temp;
	
	// Reorder dom rows
	// Delete old row
	$(this.dropdowns[dropdown.rowNumber].row).dispose();
	$(this.dropdowns[newRow].row).dispose();
	
	// Add it again
	if (direction == 'up') {
		this.addRows(newRow, newRow + 2); 
	} else {
		this.addRows(dropdown.rowNumber, dropdown.rowNumber + 2);
	}
}

/**
 * Delete a row
 */
Dropdowns.prototype.deleteRow = function(dropdown) {
	// Delete row
	$(dropdown.row).dispose();
	
	// Update the table numbers
	var tempRowNumber = dropdown.rowNumber;
	for (var a = dropdown.rowNumber; a < this.dropdowns.length; a++) {
		this.dropdowns[a].rowNumber--;
	}
	
	// Delete from dropdown array
	this.dropdowns.splice(tempRowNumber, 1);
	
	// Update ordering of first and last row if exists
	if (this.dropdowns.length > 0) {
		var lastRow = this.dropdowns[this.dropdowns.length - 1];
		var cell = lastRow.row.cells[1];
		lastRow.updateCell(cell, lastRow.addOrdering());
		
		var firstRow = this.dropdowns[0];
		cell = firstRow.row.cells[1];
		firstRow.updateCell(cell, firstRow.addOrdering());
	}
}

/**
 * Creates hidden input fields, that the form can be sent to the server
 */
Dropdowns.prototype.serializeToForm = function() {
	for (var a = 0; a < this.dropdowns.length; a++) {
		var dropdown = this.dropdowns[a];
		this.addHiddenField('dropdownName[]', dropdown.name);
	}
}

Dropdowns.prototype.addHiddenField = function(name, value) {
	var hidden = new Element('input', {
		'type'	: 'hidden',
		'name'	: name,
		'value'	: value
	});
	hidden.inject($('adminForm'));
}

/**
 * See if there are no similiar names and the dropdown has a title
 */
Dropdowns.prototype.validate = function() {
	// Check name
	if ($('jform_name').value == '') {
		alert('<?php echo JText::_('COM_EVENTTABLEEDIT_ERROR_ENTER_NAME') ?>');
		return false;
	} 

	// Check for similar dropdownnames
	var flag = 1;
	for (var a = 0; a < this.dropdowns.length - 1; a++) {
		for (var b = a + 1; b < this.dropdowns.length; b++) {
			if (this.dropdowns[a].name == this.dropdowns[b].name) {
				alert('<?php echo JText::_('COM_EVENTTABLEEDIT_ERROR_SAME_NAME') ?>');
				return false;
			}
		}
	}
	return true;
}

// TODO new Point by keyboard

-->
</script>
