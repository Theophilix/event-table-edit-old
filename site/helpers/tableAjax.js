/*
 * $Id: $
 * @copyright (C) 2007 - 2019 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 */

var access = null;
tableProperties = null;
others = null;
lang = null;

window.addEvent('load', function() {
	// Initate objects
	access = new Access();
	tableProperties = new TableProperties();
	others = new Others();
	lang = new Language();

	// Add the linecolors
	if (tableProperties.nmbCells != 0) {
		BuildPopupWindow.prototype.updateAllLineColors();
		initEvents();
	}
});

/**
 * Add the neccessary Events to the new table 
 */ 
function initEvents() {
	if (tableProperties.nmbRows != 0) {
		for (q = 0; q < tableProperties.myTable.tBodies[0].rows.length; q++) {
			addClickEvent(q);
			addAnchorEvent(q);
		}
	
		addActionRow(0, null);
		addActionRow2(0, null);
	}
	
	addNewRowEvent();
}

/**
 * Add Edit Events on a single row
 */
function addClickEvent(row) {
	// Check ACL
	//Get ID of the row
	var rowId = $('rowId_' + row).value;
	if (!access.edit && !checkAclOwnRow(rowId)) return false;
	
	var mycells = tableProperties.myTable.tBodies[0].rows[row].cells;
	var endCell = tableProperties.nmbCells + tableProperties.show_first_row;
	if(endCell > 6){
		var constt = Math.round(endCell/12);
	}else if(endCell > 3 && endCell < 6){
		var constt = Math.round(endCell/6);
	}else{
		var constt = 1;	
	}
	//var constt = 2;

	var j=0;
	var z= 0;
	for (a = tableProperties.show_first_row, v = 0; a < endCell; a++, v++) {
		// Add Event
		$(mycells[a]).addEvent('click', 
			(function(rowId, v, editedCell) {
				return function () {
					openCell(rowId, v, editedCell);
				}
			})(rowId, v, mycells[a])
		);
		var aa = parseInt(a);
		var dd = '';
		if(aa == 0){
			dd = ' title';
		}else{
			dd = ' tablesaw-priority-'+z;
		}
		// Add CSS Class that cell is editable
		mycells[a].set('class', 'editable'+dd);
	if(j%constt==0){
		z++;
	}
	j++;
	}
}

/**
 * Adds a action row and the neccessary events
 * if a user has the rights for that
 *
 * @param row: The row from that the action should be started
 */
function addActionRow(row, singleOrdering) {
	// If the user has not engough rights
	if (!access.reorder && !access.ownRows) {
		if (!tableProperties.show_pagination) {
			return false;
		}
		else if (tableProperties.defaultSorting) {
			return false;
		}
	}
	showLoad();
	
	// Add table head for action row if it's the first time
	var ordering = new Array();
	if (singleOrdering == null) {
		ordering = addActionRowFirstTime();
	}
	// If there's a new row to be added
	else {
		ordering[row] = singleOrdering; 
	}
	
	// Add the column
	var tempTable = tableProperties.myTable.tBodies[0];
	for(var a = row; a < tempTable.rows.length; a++ ) {
		var cell = new Element('td', {
			'id': 'etetable-action',
			'class':"editable tablesaw-priority-50 sort_col",
			'data-tablesaw-priority':"50",
			'data-tablesaw-sortable-col':"col"

		});
		
		var elem = tempTable.rows[a].appendChild(cell);
		
		//addDeleteButton(a);
		addOrdering(a, elem, ordering[a]);
	}
	removeLoad();
}

/**
 * Adds a action row and the neccessary events
 * if a user has the rights for that
 *
 * @param row: The row from that the action should be started
 */
function addActionRow2(row, singleOrdering) {
	
	// If the user has not engough rights
	if (!access.deleteRow && !access.ownRows) {
		return false;
	}
	showLoad();
	
	// Add table head for action row if it's the first time
	//if(access.deleteRow){	
		var ordering = new Array();
		if (singleOrdering == null) {
			ordering = addActionDeleteRowFirstTime();
		}
		// If there's a new row to be added
		else {
			ordering[row] = singleOrdering; 
		}
		// Add the column
		var tempTable = tableProperties.myTable.tBodies[0];
		for(var a = row; a < tempTable.rows.length; a++ ) {
			var cell = new Element('td', {
				'id': 'etetable-action-delete',
				'class':"editable tablesaw-priority-50",
				'data-tablesaw-priority':"10",
				'data-tablesaw-sortable-col':"col"
			});
			
			var elem = tempTable.rows[a].appendChild(cell);
			addDeleteButton(a);
			//addOrdering(a, elem, ordering[a]);
		}				
	//}
	removeLoad();
}
/**
 * Executed when the whole action column has to be added the first time
 */
function addActionRowFirstTime() {
	var thead = new Element('th', {
		'text': lang.actions,
		'class':"evth50 tablesaw-priority-persist tablesaw-sortable-head sort_col",
			'data-tablesaw-priority':"persist",
			'data-tablesaw-sortable-col':"col"
	});

	tableProperties.myTable.tHead.rows[0].appendChild(thead);
	
	// Add the ordering link
	//if (tableProperties.show_pagination) {
		var orderingLink = new Element('span', {'id'	: 'etetable-orderingLink'});
		orderingLink.innerHTML = others.orderingLink;
		orderingLink.inject(thead);
	//}
	
	// Add order save icon if allowed
	if (access.reorder && !tableProperties.defaultSorting) {
		var saveIcon = new Element('div', {
			'id'	: 'etetable-saveicon',
			'class'	: 'etetable-saveicon',
			'title' : lang.saveOrder,
			'events': {
				'click': function() {
					document.adminForm.task.value = 'etetable.saveOrder';
					document.adminForm.submit();
				}
			}
		});
		saveIcon.inject(thead);
	}

	return tableProperties.ordering;
}

function addActionDeleteRowFirstTime() {
	
	var thead2 = new Element('th', {
		'text': lang.deletetext,
		'class':"evth50 tablesaw-priority-60 tablesaw-sortable-head",
			'data-tablesaw-priority':"60",
			'data-tablesaw-sortable-col':"col"
	});
	
	tableProperties.myTable.tHead.rows[0].appendChild(thead2);
	return tableProperties.ordering;
}

/**
 * Add the Delete Event on a single row
 */
function addDeleteButton(row) {
	// Check ACL
	// Get ID of the row
	var rowId = $('rowId_' + row).value;
	if (!access.deleteRow && !checkAclOwnRow(rowId)) return false;

	var insertRows = tableProperties.myTable.tBodies[0].rows[row];
	spanclass = "";
	if(!access.deleteRowR){
		spanclass = "disabled";
	}
	var span = new Element ('span', {
		'id': 'etetable-delete',
		'class': spanclass,
		'events': {
			'click': (function(rowId, rowIdentifier) {
				return function () {
					if(access.deleteRowR){
						deleteRow(rowId, rowIdentifier);
					}
				}
			})(rowId, insertRows)
		}
	});
	
	
	var img = new Element ('img', {
		'src'	: others.rootUrl + 'administrator/components/com_eventtableedit/template/images/cross.png',
		'id'	: 'etetable-delete-img',
		'title'	: lang.deleteRow
	});
	
	
	var insertCell = insertRows.cells[insertRows.cells.length - 1];
	if(!access.deleteRowR){
		$(insertCell).addClass("disabled")
	}
	img.inject(span);
	span.inject(insertCell);
}
 
/**
 * Add the Ordering Input fields
 */
function addOrdering(row, cell, ordering) {
	/** 
	 * Check ACL (edit rights are used for ordering)
	 * Check if ordering fields should be there, this is if
	 * there's no automatic ordering
	 */
	if ((!access.reorder || tableProperties.defaultSorting) && others.listOrder != 'a.ordering') return false;
	
	//Get ID of the row
	var rowId = $('rowId_' + row).value;
	
	var disabled = true;
	if (access.reorder && others.listOrder == 'a.ordering') {
		disabled = false;
	}
	
	var orderInput = new Element('input', {
		'type'		:	'text',
		'id'		: 	'etetable-ordering',
		'name'		: 'order[]',
		'value'		: ordering,
		'disabled'	: disabled
	});
	orderInput.inject(cell);
}

//Ads the click Event to the new row button
function addNewRowEvent() {
	// Check ACL
	if (!access.add) return;
	
	$('etetable-add').addEvent('click', function() {
		newRow();
	});

	
}

/**
 * Open a window to edit a cell
 */
function openCell(rowId, cell, editedCell) {
	//Check that only one instance of the window is opened
	if (!others.doOpen()) return;
	showLoad();
		
	var url = 'index.php?option=com_eventtableedit' +
			  '&task=etetable.ajaxGetCell' +
			  '&id=' + tableProperties.id +
			  '&cell=' + cell +
			  '&rowId=' + rowId;
	
	var myAjax = new Request({
		method: 'post',
        url: url,
		onSuccess: function (response) {
			var parsed = response.split('|');
			
			var cellContent = parsed[0];
			var datatype	= parsed[1];
		
			var popup = new BuildPopupWindow(datatype, rowId);
			if (datatype != "boolean" && datatype != "four_state") {
				popup.constructNormalPopup(cellContent, cell, editedCell);
			} else {
				popup.constructBoolean(cellContent, cell, editedCell, datatype);
			}
			
			removeLoad();
		}
	}).send();
}

/**
 * Show the AJAX-Loading Symbol
 */
function showLoad() {
	var loadDiv = new Element ('div', {
		'id': 'loadDiv'
	});
	var loadImg = new Element ('img', {
		'src': others.rootUrl + '/components/com_eventtableedit/template/images/ajax-loader.gif'
	});
	
	document.body.appendChild(loadDiv).appendChild(loadImg);
}

function removeLoad() {
	$('loadDiv').dispose();
}

/**
 * Deletes a row
 */
function deleteRow(rowId, rowIdentifier) {
	if (!others.doOpen()) return false;
	showLoad();
	
	// Build the popup
	var popup = new BuildPopupWindow("delete", rowId);
	popup.constructDeletePopup(rowIdentifier);
	
	removeLoad();
}

function newRow() {
	if (!others.doOpen()) return false;
	showLoad();
	
	var myUrl = 'index.php?option=com_eventtableedit' +
				'&task=etetable.ajaxNewRow' +
				'&id=' + tableProperties.id;
	var myAjax = new Request({
		method: 'post',
			url: myUrl,
			onComplete: function (response) {
				var parsed = response.split('|');
				
				var rowId 		= parsed[0];
				var rowOrder	= parsed[1];
				var nmbPageRows	= parseInt(tableProperties.myTable.tBodies[0].rows.length);
				
				addCells(nmbPageRows, rowId);
				
				/* RowId has to be added, so the user can edit his own
				 * row if this function is activated
				 */ 
				access.createdRows.push(rowId);  
				
				addActionRow(nmbPageRows, rowOrder);
				addActionRow2(nmbPageRows, rowOrder);
				addClickEvent(nmbPageRows);
				
				removeLoad();
				others.doClose();
			}
	}).send();
}

function addCells(nmbPageRows, rowId) {
	// Insert Row at the end and define linecolor
	var tempTable = tableProperties.myTable.tBodies[0];
	tempTable.appendChild(document.createElement('tr'));
	tableProperties.nmbRows++;
	tempTable.rows[nmbPageRows].className = 'etetable-linecolor' + (nmbPageRows % 2);		
	
	// Insert Cells
	var totNmbCells = tableProperties.nmbCells + tableProperties.show_first_row 
	
	for (a = 0; a < totNmbCells; a++) {
		tempTable.rows[nmbPageRows].insertCell(-1);
	}
	
	// Optional first row
	if (tableProperties.show_first_row) {
		var firstRow = tempTable.rows[nmbPageRows].cells[0];
		
		firstRow.setAttribute('class', 'first_row' + nmbPageRows, true);
		firstRow.setAttribute('id', 'first_row', true);
		
		/**
		 * The searched number is just the number from the cell above + 1
		 * If it is the first row use list start
		 */
		if (nmbPageRows > 0) {
			firstRow.innerHTML = parseInt(tempTable.rows[nmbPageRows - 1].cells[0].innerHTML) + 1;
		} else {
			firstRow.innerHTML = tableProperties.limitstart + 1;
		}
	}
	
	// Normal cells
	for (a = tableProperties.show_first_row, b = 0; a < tableProperties.nmbCells; a++, b++) {
		var cell = tempTable.rows[nmbPageRows].cells[a];
		cell.setAttribute('id', 'etetable-row_' + nmbPageRows + '_' + b);
		cell.setAttribute('class', 'etetable-row_' + nmbPageRows + '_' + b);
		cell.innerHTML = '&nbsp;';			
	}
	
	// Hidden field
	var hiddenField = new Element('input', {
		'type'	: 'hidden',
		'id'	: 'rowId_' + nmbPageRows,
		'name'	: 'rowId[]',
		'value'	: rowId
	});
	var lastCell = tempTable.rows[nmbPageRows].cells;
	hiddenField.inject(lastCell[lastCell.length - 1]);
}

/**
 * Checks, if a user has the right to edit or delete a row
 * that he created himself
 */
function checkAclOwnRow(rowId) {
	if (access.ownRows && access.createdRows.indexOf(rowId) != -1) {
		return true;
	}
	return false;
}

/**
 * Add an event to every anchor element
 * in order to stop event bubbling, and if
 * someone clicks on a link not the popup window opens.
 */
function addAnchorEvent(row, cell) {
	if (row != null) {
		var mycells = tableProperties.myTable.tBodies[0].rows[row].cells;
		var endCell = tableProperties.nmbCells + tableProperties.show_first_row;
	
		for (var a = tableProperties.show_first_row, v = 0; a < endCell; a++, v++) {
			addAnchorEventsExe(mycells[a]);
		}
	}
	else {
		addAnchorEventsExe(cell);
	}
}

function addAnchorEventsExe(elem) {
	var anchors = $(elem).getElements('a');

	for (var b = 0; b < anchors.length; b++) {
		// Add Event
		anchors[b].addEvent('click', function(event) {
			event.stopPropagation();
		});
	}
}
