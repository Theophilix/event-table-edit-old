/*
 * $Id: $
 * @copyright (C) 2007 - 2017 Manuel Kaspar and Theophilix
 * @license GNU/GPL
 */

// Using as this-context, elseway context is lost at events
var self = null;

/**
 * Creates the elements for the popup window
 * @param editedCell the edited cell, so that the value can be changed
 */
function BuildPopupWindow(datatype, rowId) {
	self = this;
	
	// Dropdowns have the format dropdown.id
	var dataParts = datatype.split('.');
	this.datatype = dataParts[0];
	
	// For Dropdowns
	if (dataParts.length > 1) {
		this.dropdownId = dataParts[1];
	}
		
	this.rowId = rowId;
}

/**
 * A pseudo constructor for "normal" popups
 * @param editedCell holds an identifier for the edited cell
 */
BuildPopupWindow.prototype.constructNormalPopup = function(cellContent, cell, editedCell) {
	 this.cellContent = cellContent;
	 this.cell = cell;
	 this.editedCell = editedCell;
	 this.inputValue = null;
	 
	 this.addDefaultElements();
}
 
/**
 * A pseudo constructor for changing the status of boolean fields
 */
BuildPopupWindow.prototype.constructBoolean = function(status, cell, editedCell, datatype) {
	this.cell = cell;
	this.editedCell = editedCell;
	if(datatype == "four_state"){
		if (status == '') {
			status = 0;
		}else if (status == 0 || status == '&') {			
			status = 1;
		}else if (status == 1) {
			status = 2;		
		}else if (status == 2) {
			status = '';
		}
	}else{
		// Decide what value the field has
		if (status == 1) {
			//status = -1;
			status = 0;
		} /*else if (typeof(status) == "undefined" || status == null || status == "") {
			status = 0;	   
		} else if (status == -1 || status == 0) {
			status++;
		} */else {
			status = 1;
		}
	}
	this.inputValue = status;
	   
	this.sendData();
}
 
/**
 * A pseudo constructor for "delete" popups
 */
BuildPopupWindow.prototype.constructDeletePopup = function(rowIdentifier) {
 	this.rowIdentifier = rowIdentifier;
 	
 	this.addDefaultElements();
}

/**
 * The main method for a standard window
 */
BuildPopupWindow.prototype.addDefaultElements = function() {
	// Outer div
	var outerDiv = new Element ('div', {
		'id': 'etetable-outerPopupDiv'
	});
	 
	// Detect datatype
	var popupDivId = 'etetable-popupNormal';
	if (this.datatype == 'text') {
		popupDivId = 'etetable-popupText';
	}
	
	var popupDiv = new Element ('div', {
		'id': popupDivId,
		'class' : 'popupDiv'
	});
	
	popupDiv.inject(outerDiv);
	outerDiv.inject($('adminForm'));
	
	// Add drag
	addDrag(popupDiv);
	
	var popupForm = new Element ('form', {
		'id': 'popupForm',
		'name': 'popupForm',
		'onsubmit': 'return false'
	});
	popupForm.inject(popupDiv);
	
	if (!this.switchSpecifiedWindows()) {
		return false;
	}
	
	if (this.datatype != 'delete') {
		this.addToolTip();
	}
	this.addButtons(popupDivId);
	
	// At the delete popup there's no inputfield
	try {
		$('etetable-inputfield').focus();
	} catch(e) {}

	// Add Keyboard controls if type is not text
	if (this.datatype != "text") {
		this.addKeyboard();
	}
}

/**
 * Add the Drag Event, we have to set the position again,
 * because some Browsers have problems there
 */
function addDrag(popupDiv) {
	if(window.innerWidth) {
		var winW = window.innerWidth;
		var winH = window.innerHeight;
	} else if(document.documentElement)	{
		var winW = document.documentElement.clientWidth;
		var winH = document.documentElement.clientHeight;
	} else if(document.body) {
		var winW = document.body.clientWidth;
		var winH = document.body.clientHeight;
	}
	$(popupDiv).setStyle('left', (winW / 2) + 'px');
	$(popupDiv).setStyle('top', (winH / 2) + 'px');
	
	var dragHandle = new Element ('div', {'id': 'dragHandle'});
	dragHandle.inject(popupDiv);
	
	new Drag.Move(popupDiv, {
		'handle': dragHandle
	});
}

/**
 * Create the classes dependent on the different datatypes
 */ 
BuildPopupWindow.prototype.switchSpecifiedWindows = function() {
	switch (this.datatype) {
		case "text":
			this.textWindow();
			break;
		case "date":
			this.dateWindow();
			break;
		case "dropdown":
			return this.dropDownWindow();
		// To open a popup window for deleting a row
		case "delete":
			this.deleteRow();
			break;
		default:
			this.standardWindow();
	}
	return true;
}

/**
 * A text window
 */
BuildPopupWindow.prototype.textWindow = function() {
	var textArea = new Element ('textarea', {
		'id': 'etetable-inputfield',
		'name': 'etetable-textArea',
		'rows': '5',
		'cols': '48'
	});	
	textArea.innerHTML = this.cellContent;
	$('popupForm').appendChild(textArea);
}

/**
 * Date window with calendar
 */
BuildPopupWindow.prototype.dateWindow = function() {
	
	/*var calSrc = others.rootUrl + '/templates/system/images/calendar.png';
	var calInput = new Element ('input', {
		'id': 'etetable-inputfield',
		'type': 'text',
		'value': this.cellContent,
		'name': 'ete-calendar',
		'readonly': 'readonly'
	});
	var calImg = new Element ('img', {
		'id': 'calImg',
		'class': 'calendar',
		'alt': 'Calendar',
		'src': calSrc	
	});
	
	
	$('popupForm').appendChild(calInput);
	$('popupForm').appendChild(calImg);
	$('popupForm').appendChild(clear);
	*/
	
	var calhtmls = '<div class="input-append"><input id="etetable-inputfield" name="ete-calendar" value="'+this.cellContent+'"  data-alt-value="" autocomplete="off" type="text"><button type="button" class="btn btn-secondary" id="etetable-inputfield_btn" data-inputfield="filterstring" data-dayformat="%Y-%m-%d" data-button="filterstring_btn" data-firstday="1" data-weekend="0,6" data-today-btn="1" data-week-numbers="1" data-show-time="0" data-show-others="1" data-time-24="24" data-only-months-nav="0"><span class="icon-calendar"></span></button></div>';
	var div = new Element ('div', {
		'class': 'field-calendar'
	});	
	div.innerHTML = calhtmls;
	var clear = new Element ('span', {
		'id'	: 'clear',
		'class'	: 'etetable-button',
		'text'	: lang.clear,
		'events': {
			'click': function () {
				$('etetable-inputfield').value = '';
			}
		}
	});
	$('popupForm').appendChild(div);
	$('popupForm').appendChild(clear);

	// Reinitalize the Joomla-Calendar for the dynamically added date-picker
	Calendar.setup({
		inputField     :    "etetable-inputfield",
		ifFormat       :    "%Y-%m-%d",
		showsTime      :    false,
		button         :    "etetable-inputfield_btn",
		align		   :    "BR"
	});
}
 
/**
 * Window for managing dropdown fields
 */
BuildPopupWindow.prototype.dropDownWindow = function() {
	// Get the dropdown
	var drop = dropdowns.getDropdownById(this.dropdownId);
	var option = [];
	var selectedIndex = 0;
	
	// If dropdown was deleted in backend
	if (drop == null) {
		alert(lang.err_dropdown_deleted);
		self.removePopup();
	  	return false;
	}
	
	var name = new Element('span', {
		'id'	: 'etetable-dropdownName',
		'text'	: drop.name 
	});
	name.inject($('popupForm'));
	
	var select = new Element('select', {
		'id'	: 'etetable-inputfield',
		'name'	: 'etetableDropdown'
	});
	
	//Insert empty first option
	option[0] = new Element('option');
	select.appendChild(option[0]);
	
	// Insert the options
	for (a = 0; a < drop.elements.length; a++) {
		// Select the right option
		if (drop.elements[a] == this.cellContent) {
			selectedIndex = a + 1;
		}
	  
	  option[a+1] = new Element('option', {
		  'text': drop.elements[a]
	  });
	  select.appendChild(option[a+1]);
	}
	
	$('popupForm').appendChild(select);
	$('popupForm').etetableDropdown.options[selectedIndex].selected = true;

	return true;
}
 
BuildPopupWindow.prototype.deleteRow = function() {
	var text = new Element ('div', {
		'id': 'etetable-popup-text',
		'text': lang.really_delete
	})
	
	$('etetable-popupNormal').appendChild(text);
}
 
BuildPopupWindow.prototype.standardWindow = function() {
	var inputfield = new Element ('input', {
		'id': 'etetable-inputfield',
		'type': 'text',
		'value': this.cellContent,
		'name': 'etetable-inputfield'
	});
	
	$('popupForm').appendChild(inputfield);
}

/**
 * Built a tooltip - because normal joomla tooltips doesn't work with dynamically added Elements
 */
BuildPopupWindow.prototype.addToolTip = function() {
	//Get title and text
	var langTip = lang.getToolTip(this.datatype);
	
	var srcImg = others.rootUrl + 'media/system/images/tooltip.png';
	var newTip = new Element ('img', {
		'id': 'etetable-tipImg',
		'src': srcImg,
		'events': {
			'mouseenter': function() {
				self.showTip();
			},
			'mouseleave': function() {
				self.hideTip();
			}
		}
	});
	var toolDiv = new Element ('div', {
		'id': 'etetable-tipDiv',
		'class': 'tip tool-tip',
		'style': 'visibility: hidden;' +
				 'right: -378px;'  +
				 'background: #E8E8E8;'  +
				 'padding: 10px;'  +
				 'top: 21px'
	});
	var innerDiv = new Element ('div');
	
	var toolTitle = new Element ('div', {
		'class': 'tip-title tool-title'
	});
	
	var headSpan = new Element ('span');
	headSpan.innerHTML = langTip["title"]; 
	
	var textDiv = new Element ('div', {
		'class': 'tip-text tool-text'
	});
	var textSpan = new Element ('span');
	textSpan.innerHTML = langTip["desc"];
	
	$('popupForm').appendChild(newTip);
	$('popupForm').appendChild(toolDiv).appendChild(innerDiv).
			appendChild(toolTitle).appendChild(headSpan).parentNode.
			parentNode.appendChild(textDiv).appendChild(textSpan);
}
 
BuildPopupWindow.prototype.showTip = function(myEvent) {
	$('etetable-tipDiv').style.visibility = 'visible'; 
}
							
BuildPopupWindow.prototype.hideTip = function(event) {
	$('etetable-tipDiv').style.visibility = 'hidden';
}
 
/**
 * Add the OK- and Cancel-Button
 
 * @param function method is a function what should be done (at the moment send or delete)
 */
 BuildPopupWindow.prototype.addButtons = function(cssId) {
	var containerDiv = new Element('div', {'id': 'etetable-buttons'}); 
	
	var okButton = new Element ('a', {
		'id': 'etetable-okbutton',
		'class': 'etetable-popup-button',
		'events': {
			'click': function() {
				// The button is used for edit and delete popup
				if (self.datatype == 'delete') {
					self.executeDeleteRow();
				} else {
					self.processData();
				}
			}
		}
	});
	var cancelButton = new Element ('a', {
		'id': 'etetable-cancelbutton',
		'class': 'etetable-popup-button',
		'events': {
			'click': function() {
				self.removePopup();
			}
		}
	});	
	
	cancelButton.inject(containerDiv);
	okButton.inject(containerDiv);
	containerDiv.inject(cssId);
}
 
/**
 * Adds keyboard controls
 */
BuildPopupWindow.prototype.addKeyboard = function() {
	keydown = new Keyboard({
	    defaultEventType: 'keydown',
	    events: {
	        'enter': function () {
				self.processData();
			},
			'esc': function () {
				self.removePopup();
			}
	    }
	});
	return;
}
 
/**
 * Takes the actions, that are neccessary
 * for executing the "Send"-Action
 */
BuildPopupWindow.prototype.processData = function() {
	if (self.checkData()) {
		try {
			keydown.deactivate();
		} catch(e) {}
		self.sendData();
	}
}

BuildPopupWindow.prototype.removePopup = function() {
	try {
		keydown.deactivate();
	} catch(e) {}
	try {
		$('etetable-outerPopupDiv').dispose();
	} catch(e) {}
	
	others.doClose();
}

/**
 * Checks if the data is valid
 */
BuildPopupWindow.prototype.checkData = function() {
	this.inputValue = $('etetable-inputfield').value;
	
	// If there's nothing in it's ok
	if (this.inputValue == "") return true;
	
	switch (this.datatype) {
		case 'int':
			return this.checkInt();
		case 'float':
			return this.checkFloat();
		case 'time':
			return this.checkTime();
		case 'mail':
			return this.checkMail();
		case 'link':
			return this.checkLink();
	}
	
	return true;
}

/**
 * Check single datatypes
 */
BuildPopupWindow.prototype.checkInt = function() {
	var parsed = this.inputValue;
	
	if (isNaN(parsed)) {
		$('etetable-inputfield').value = lang.err_no_int;
		return false;
	}
	this.inputValue = parsed;
	return true;
}

BuildPopupWindow.prototype.checkFloat = function() {
	var parsed = this.inputValue.replace(',', '.');
	parsed = parseFloat(parsed);
	
	if (isNaN(parsed)) {
		$('etetable-inputfield').value = lang.err_no_float;
		return false;
	}
	this.inputValue = parsed;
	return true;
}

BuildPopupWindow.prototype.checkTime = function() {
	var isTime = true;
	var parr = this.inputValue.split(':');
	
	if (parr.length < 2 || parr.length > 3) {
		isTime = false;
	}
	for (g = 0; g < parr.length; g++) {
		var pcheck = parseInt(parr[g]);
		if (isNaN(pcheck) || (g==0 && parr[g] > 24)) {
			isTime = false;
		} else if (g > 0) {
			if (parr[g] < 0 || parr[g] > 60) {
				isTime = false;
			}
		}
	}			
								
	if (!isTime) {
		$('etetable-inputfield').value = lang.err_no_time;
		return false;
	}
	return true;
}

BuildPopupWindow.prototype.checkMail = function() {
	var email = this.inputValue;
	//var filter  = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
	var filter = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
	if (!filter.test(email)) {
		$('etetable-inputfield').value = lang.err_no_mail;
		return false;
	}
	return true;
}



BuildPopupWindow.prototype.checkLink = function() {
	var parsed = this.inputValue;
	var validurl = isURL(parsed);
	if (!validurl) {
		$('etetable-inputfield').value = lang.err_no_Link;
		return false;
	}
	this.inputValue = parsed;
	return true;
}



function isURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return pattern.test(str);
}


/**
 * Send the new content to the database
 */
BuildPopupWindow.prototype.sendData = function() {
	showLoad();
	
	// Handle dropdowns
	if (self.datatype == "dropdown") {
		self.inputValue = $('etetable-inputfield').options[$('etetable-inputfield').selectedIndex].text;
	}
	
	var url = 'index.php?option=com_eventtableedit' +
			  '&task=etetable.ajaxSaveCell';
	var post = "content=" + encodeURIComponent(self.inputValue) +
				"&cell=" + self.cell +
				"&rowId=" + self.rowId +
				"&id=" + tableProperties.id;
				
    var myAjax = new Request({
	method: 'post',
        url: url,
	data: post,
	onComplete: function (response) {
		var data = response.split("|");
		var numCol = jQuery('#num-of-col').data('num-of-col');
		var rowTh = self.rowId-1;
		self.editedCell.innerHTML = data[0];
		jQuery('#etetable-row_'+(rowTh)+'_'+(numCol)).html(data[1]);

		addAnchorEvent(null, self.editedCell);
		self.removePopup();
		removeLoad();
		jQuery('.tablesaw-modeswitch select').trigger('change');
		
	}
	}).send();
}
 
 /**
  * Now really delete the row
  */
BuildPopupWindow.prototype.executeDeleteRow = function() {
	showLoad();
	
	var url = 'index.php?option=com_eventtableedit' +
			  '&task=etetable.ajaxDeleteRow' +
			  '&id=' + tableProperties.id +
			  '&rowId=' + self.rowId;
	
	var myAjax = new Request({
		method: 'get',
        	url: url,
		onComplete: function () {
			// Delete the row
			var row = self.detectRow(self.rowIdentifier);
			$(self.rowIdentifier).dispose();

			// Update fields
			self.updateRows(row);

			self.removePopup();
			removeLoad();
		}
	}).send();
}

BuildPopupWindow.prototype.updateRows = function(row) {
	var tRows = tableProperties.myTable.tBodies[0].rows;

	for (var a = row; a < tRows.length; a++) {
		var tempTable = tRows[a];
		self.updateHiddenField(tempTable, a);
		self.updateFirstRow(tempTable, a);
		self.updateLineColors(tempTable, a);
		self.updateCellId(tempTable,a);
	}
}
  
/**
 * Update the first row
 * 
 * @param row after this row the fields have to be updated
 */
BuildPopupWindow.prototype.updateFirstRow = function(tempTable, row) {
	 // Do this only, if the first row is activated in the configuration
	 if (!tableProperties.show_first_row) return;

	 tempTable.cells[0].className = 'first_row_' + row;
	 tempTable.cells[0].innerHTML = (tableProperties.limitstart + row + 1);
}
 
/**
 * Update the linecolors
 */
BuildPopupWindow.prototype.updateLineColors = function(tempTable, row) {
	tempTable.className = 'etetable-linecolor' + (row % 2);
}

/**
 * Update all linecolors, when initializing the table
 */
BuildPopupWindow.prototype.updateAllLineColors = function() {
	var tRows = tableProperties.myTable.tBodies[0].rows;

	for (var b = 0; b < tRows.length; b++) {
		var tempTable = tRows[b];
		BuildPopupWindow.prototype.updateLineColors(tempTable, b);
	}
}

/**
 * Update id of the hidden field
 */
BuildPopupWindow.prototype.updateHiddenField = function(tempTable, row) {
	$(tempTable).getElement('input').id = "rowId_" + row;
}

/**
 * Update id of the cell id
 */
BuildPopupWindow.prototype.updateCellId = function(tempTable, row) {
	for (var b = tableProperties.show_first_row; b <= tableProperties.nmbCells; b++) {
		tempTable.cells[b].id = "etetable-row_" + row + "_" + b;
	}
}

/**
 * Get the row number if a identifier for a row is given
 */
BuildPopupWindow.prototype.detectRow = function(rowIdentifier) {
	for (var a = 0; a < tableProperties.myTable.tBodies[0].rows.length; a++) {
		if (rowIdentifier == tableProperties.myTable.tBodies[0].rows[a]) {
			return a;
		}
	}

	return false;
}
