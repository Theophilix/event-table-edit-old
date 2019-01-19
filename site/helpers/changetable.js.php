<?php
/**
 * $Id: $
 * @copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 */
 
// no direct access
defined( '_JEXEC' ) or die;
?>

<script type="text/javascript">
<!--

theads = null;

window.addEvent('load', function() {
	theads = new Theads();
	theads.loadTheads();
	
	addNewEvent();
	addSaveEvent();
	addCancelEvent();
});

function Thead(id, name, datatype) {
	// A reference to the row
	this.row = null;
	this.rowNumber = null;

	this.id = id;
	this.name = name;
	this.datatype = datatype;
	
	// Contains defaultSorting['number'] and defaultSorting['direction']
	this.defaultSorting = new Array();
	
}

function Theads() {
	this.table = $('changetable-table').tBodies[0];
	
	this.theads = new Array();
	
	// Counts how many default sortings are added
	this.sortingNumber = 0;
}

function addNewEvent() {
	$('changetable-newrow').addEvent('click', function() {
		var thead = new Thead(0, '', 'text');
		theads.theads.push(thead);
		thead.prepareDefaultSorting('');
		theads.addRows(theads.theads.length - 1, -1);
		
		// Update ordering of last row if exists
		if(theads.theads.length > 1) {
			var lastRow = theads.theads[theads.theads.length - 2];
			var cell = lastRow.row.cells[2];
			lastRow.updateCell(cell, lastRow.addOrdering());
		}
			
		thead.row.cells[0].firstChild.fireEvent('click');
	});
}

function addSaveEvent() {
	$('changetable-save').addEvent('click', function() {
		theads.serializeToForm();
		$('adminForm').submit(); 
	});
}

function addCancelEvent() {
	$('changetable-cancel').addEvent('click', function() {
		window.history.back();
	});
}

/**
 * Loads the available table heads
 */
Theads.prototype.loadTheads = function() {
	this.generateObjects();
	this.addRows(0, -1);
}

/**
 * Load the thead data from the php variables to javascript objects
 */
Theads.prototype.generateObjects = function() {
	var thead = null;
	
	<?php foreach ($this->item as $item) : ?>
		thead = new Thead(<?php echo $item->id . ", '" . $item->name . "', '" . $item->datatype . "'"; ?>);
		thead.prepareDefaultSorting('<?php echo $item->defaultSorting; ?>');
		this.theads.push(thead);
	<?php endforeach; ?>
}

/**
 * Prepare DefaultSorting (table_id:asc or desc, 2nd sorting)
 */
Thead.prototype.prepareDefaultSorting = function(data) {
	// If no sorting is set for the row
	if (data == '' || data == ':') {
		this.defaultSorting['number'] = '';
		this.defaultSorting['direction'] = '';
		return;
	}
	
	var sp = data.split(':');
	this.defaultSorting['number'] = sp[0];
	this.defaultSorting['direction'] = sp[1];
	theads.sortingNumber++;
}

/**
 * Adds the rows in the html table
 * If endRow is -1 all rows are added
 */
Theads.prototype.addRows = function(startRow, endRow) {
	// If used for reordering
	if (endRow == -1) {
		endRow = this.theads.length;
	}

	for (var a = startRow; a < endRow; a++) {
		var row = this.table.insertRow(a);
		this.theads[a].row = row;
		this.theads[a].rowNumber = a;
		
		this.theads[a].addCell(this.theads[a].addName());
		this.theads[a].addCell(this.theads[a].addDatatype());
		this.theads[a].addCell(this.theads[a].addOrdering());
		//this.theads[a].addCell(this.theads[a].addDefaultSorting());
		this.theads[a].addCell(this.theads[a].addDeleteIcon());
	}
}

/**
 * For adding all the new cells
 */
Thead.prototype.addCell = function(func) {
	var cell = this.row.insertCell(-1);
	func.inject(cell);
}

/**
 * For updating a cell
 */
Thead.prototype.updateCell = function(cell, func) {
	// Delete content if something there
	while (cell.childNodes.length >= 1) {
        cell.removeChild(cell.firstChild);       
    } 

	func.inject(cell);
}

/**
 * Adds the name cell
 */
Thead.prototype.addName = function() {
	var span = new Element('span', {
		'id'	: 'changetable-name',
		'text'  : this.name,
		'events': {
			'click': (function(thead) {
				return function () {
					thead.editName();
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
 * Adds the datatype cell
 */
Thead.prototype.addDatatype = function() {
	var select = new Element('select', {
		'id'	: 'changetable-datatype',
		'events': {
			'change': (function(thead) {
				return function () {
					var sel = thead.row.cells[1].firstChild;
					thead.datatype = sel.options[sel.selectedIndex].value;
				}
			})(this)
		}
	});
	var option = null;
	var selextedIndex = 0;
	
	<?php 
	for($a = 0; $a < count($this->additional['datatypes']); $a++ ) :?>
		option = new Element('option', {
			'value'		: '<?php echo $this->additional['datatypes'][$a] ?>',
			'text'		: '<?php echo $this->additional['datatypes_desc'][$a] ?>'
		});
		option.inject(select);	
		
		if ('<?php echo $this->additional['datatypes'][$a] ?>' == this.datatype) {
			selectedIndex = <?php echo $a; ?>;
		} 
	<?php endfor; ?>
	
	select.selectedIndex = selectedIndex;
	
	return select;
}

/**
 * Adds the ordering cell
 */
Thead.prototype.addOrdering = function() {
	var span = new Element('span', {
		'class'	: 'changetable-ordering-span'
	});
	
	if (this.rowNumber > 0) {
		this.getOrderingImg("up").inject(span);
	}
	if (this.rowNumber < theads.theads.length - 1) {
		this.getOrderingImg("down").inject(span);
	}
	
	return span;
}

/**
 * Helper function for adding the ordering images
 */
Thead.prototype.getOrderingImg = function(direction) {
	var imgUp = new Element('span', {
		'id'	: 'changetable-ordering',
		'class'	: direction + 'arrow',
		'events': {
			'click': (function(thead, direction) {
				return function () {
					theads.reorder(thead, direction);
				}
			})(this, direction)
		}		
	});
	return imgUp;
}

/**
 * Adds the default sorting cell
 */
Thead.prototype.addDefaultSorting = function() {
	var span = new Element('span', {'id'	: 'changetable-defaultSorting'});
	
	var checkBox = new Element('div', {
		'id'	: 'changetable-checkBox',
		'text'	: this.defaultSorting['number']
	});
	checkBox.inject(span);
	
	var dirText = '';
	if (this.defaultSorting['direction'] == 'asc') {
		dirText = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ASCENDING'); ?>';
	}
	else if (this.defaultSorting['direction'] == 'desc') {
		dirText = '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_DESCENDING'); ?>';
	}
	else {
		dirText = '';
	}
	
	var direction = new Element('span', {
		'id'	: 'changetable-direction',
		'text'	: dirText
	});
	direction.inject(span);
	
	span.addEvent('click', 
		(function(thead, elem) {
			return function () {
				thead.switchDefaultOrdering(elem);
			}
		})(this, span)
	);
	
	return span;
}

/**
 * Adds the icon to delete a row
 */
Thead.prototype.addDeleteIcon = function() {
	var deleteIcon = new Element('img', {
		'src': '<?php echo JURI::root(); ?>administrator/components/com_eventtableedit/template/images/cross.png',
		'id': 'changetable-deleteIcon',
		'events': {
			'click': (function(thead) {
				return function () {
					theads.deleteRow(thead);
				}
			})(this)
		}		
	});
	
	return deleteIcon;
}

/**
 * Edit the name of a table head
 */
Thead.prototype.editName = function() {
	// Remove cell content
	var cell = this.row.cells[0];
	cell.innerHTML = '';
	
	// Add input field
	var inputField = new Element('input', {
		'type'	: 'text',
		'id'	: 'etetable-inputfield-active',
		'class'	: 'etetable-inputfield',
		'value'	: this.name,
		'events': {
			'blur': (function(thead, cell) {
				return function () {
					// Save value in Thead Object an delete inputfield
					thead.name = $('etetable-inputfield-active').value;
					
					// Add normal name text again
					thead.updateCell(cell, thead.addName());			
				}
			})(this, cell)
		}
	});
	inputField.inject(cell);
	
	// Only works this way in the internet explorer
	setTimeout("doFocus()", 100);
	
}
jQuery( "#etetable-inputfield-active" ).live( "keypress", function(event) {
    if (event.keyCode == 13) {
        event.preventDefault();
    }
});
function doFocus() {
	$('etetable-inputfield-active').focus();
}

/**
 * Reorder a row
 */
Theads.prototype.reorder = function(thead, direction) {
	// Reorder rows in Thead array
	var newRow = -1;
	if (direction == 'up') {
		newRow = thead.rowNumber - 1;
	} else {
		newRow = thead.rowNumber + 1;
	}
	
	var temp = this.theads[thead.rowNumber];
	this.theads[thead.rowNumber] = this.theads[newRow];
	this.theads[newRow] = temp;
	
	// Reorder dom rows
	// Delete old row
	$(this.theads[thead.rowNumber].row).dispose();
	$(this.theads[newRow].row).dispose();
	
	// Add it again
	if (direction == 'up') {
		this.addRows(newRow, newRow + 2); 
	} else {
		this.addRows(thead.rowNumber, thead.rowNumber + 2);
	}
}

/**
 * Set a default ordering for a table head
 */
Thead.prototype.switchDefaultOrdering = function(elem) {
	// Switch by state 
	switch(this.defaultSorting['direction']) {
		// No ordering set, yet
		case '':
			this.defaultSorting['number'] = ++theads.sortingNumber;
			this.defaultSorting['direction'] = 'asc';
			elem.getElements('div')[0].set('text', theads.sortingNumber);
			elem.getElements('span')[0].set('text', '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_ASCENDING'); ?>');
			break;
		case 'asc':
			this.defaultSorting['direction'] = 'desc';
			elem.getElements('span')[0].set('text', '<?php echo JTEXT::_('COM_EVENTTABLEEDIT_DESCENDING'); ?>');
			break;
		case 'desc':
			elem.getElements('div')[0].set('text', '');
			elem.getElements('span')[0].set('text', '');
			
			this.updateSortingNumber();
			
			this.defaultSorting['number'] = '';
			this.defaultSorting['direction'] = '';
			break;
	}
}

/**
 * Updates the default sorting numbers
 */
Thead.prototype.updateSortingNumber = function() {
	if (this.defaultSorting['number'] == '') return;

	// Reorder other elements
	for (var a = 0; a < theads.theads.length; a++) {
		if (theads.theads[a].defaultSorting['number'] == '' ||
		    theads.theads[a].defaultSorting['number'] <= this.defaultSorting['number']) {
			continue;
		}
		theads.theads[a].defaultSorting['number']--;
		theads.theads[a].row.cells[3].getElements('div')[0].set('text', theads.theads[a].defaultSorting['number']);
	}
	theads.sortingNumber--;
}

/**
 * Delete a row
 */
Theads.prototype.deleteRow = function(thead) {
	// Delete row
	$(thead.row).dispose();
	
	// Update the table numbers
	var tempRowNumber = thead.rowNumber;
	for (var a = thead.rowNumber; a < this.theads.length; a++) {
		this.theads[a].rowNumber--;
	}
	
	// Maybe the default sorting has to be updated
	thead.updateSortingNumber();
	
	// Delete from thead array
	this.theads.splice(tempRowNumber, 1);
	
	// Update ordering of first and last row if exists
	if (this.theads.length > 0) {
		var lastRow = this.theads[this.theads.length - 1];
		var cell = lastRow.row.cells[2];
		lastRow.updateCell(cell, lastRow.addOrdering());
		
		var firstRow = this.theads[0];
		cell = firstRow.row.cells[2];
		firstRow.updateCell(cell, firstRow.addOrdering());
	}
}

/**
 * Creates hidden input fields, that the form can be sent to the server
 */
Theads.prototype.serializeToForm = function() {
	for (var a = 0; a < this.theads.length; a++) {
		var thead = this.theads[a];
		this.addHiddenField('cid[]', thead.id);
		this.addHiddenField('name[]', thead.name);
		this.addHiddenField('datatype[]', thead.datatype);
		this.addHiddenField('defaultSorting[]', thead.defaultSorting['number'] + ':' + thead.defaultSorting['direction']);
	}
}

Theads.prototype.addHiddenField = function(name, value) {
	var hidden = new Element('input', {
		'type'	: 'hidden',
		'name'	: name,
		'value'	: value
	});
	hidden.inject($('adminForm'));
}

-->
</script>
