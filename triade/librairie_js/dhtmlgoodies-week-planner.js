/************************************************************************************************************
(C) www.dhtmlgoodies.com, January 2006

This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	

Version: 1.0	: February 13th - 2006
	 1.1	: March 22nd - 2006	
: Made it possible to disable inline textaarea edit(inlineTextAreaEnabled) and instead open the window from external file.
			
Terms of use:
You are free to use this script as long as the copyright message is kept intact. However, you may not
redistribute, sell or repost it without our permission.

Thank you!

www.dhtmlgoodies.com
Alf Magne Kalleland

************************************************************************************************************/	
	

/* User variables */
var headerDateFormat = 'd';	// Format of day, month in header, i.e. at the right of days (avant d.m)
var instantSave = true;	// Save items to server every time something has been changed (i.e. moved, resized or text changed) -
                       // NB! New items are not saved until a description has been written?
var externalSourceFile_items = 'week_schedule_getItems.php';	// File called by ajax when changes are loaded from the server(by Ajax).
var externalSourceFile_save = 'week_schedule_save.php';	// File called by ajax when changes are made to an element
var externalSourceFile_delete = 'week_schedule_delete.php';	// File called by ajax when an element is deleted. Input to this file is the variable "eventToDeleteId=<id>"
var popupWindowUrl = 'edit_event.php';	// Called when double clicking on an event. use false if this option is disabled.

var txt_deleteEvent = 'Confirmer la suppression ?';	// Text in dialog box - confirm before deleting an event

var appointmentMarginSize = 5;	// Margin at the left and right of appointments;
var initTopHour = 8;	// Initially auto scroll scheduler to the position of this hour
var initMinutes = 15;	// Used to auto set start time. Example: 15 = auto set start time to 0,15,30 or 45. It all depends on where the mouse is located ondragstart
var snapToMinutes = 5;	        // Snap to minutes, example: 5 = allow minute 0,5,10,15,20,25,30,35,40,45,50,55
var weekplannerStartHour=8;	// If you don't want to display all hours from 0, but start later, example: 8am


var inlineTextAreaEnabled = false;	// Edit events from inline textarea?

/* End user variables */

var weekScheduler_container = false;
var weekScheduler_appointments = false;

var newAppointmentCounter = -1;
var moveAppointmentCounter = -1;
var resizeAppointmentCounter = -1;
var resizeAppointmentInitHeight = false;

var el_x;	// x position of element
var el_y;	// y position of element
var mouse_x;
var mouse_y;
var elWidth;

var currentAppointmentDiv = false;
var currentAppointmentContentDiv = false;
var currentTimeDiv = false;

var appointmentsOffsetTop = false;
var appointmentsOffsetLeft = false;

var currentZIndex = 20000;

var dayPositionArray = new Array();
var dayDateArray = new Array();

var weekSchedule_ajaxObjects = new Array();

var dateStartOfWeek = false;
var newAppointmentWidth = false;

var startIdOfNewItems = 500000000;
var contentEditInProgress = false;
var toggleViewCounter = -1;
var objectToToggle = false;
var currentEditableTextArea = false;

var appointmentProperties = new Array();	// Array holding properties of appointments/events.
var opera = navigator.userAgent.toLowerCase().indexOf('opera')>=0?true:false;

var activeEventObj;	// Reference to element currently active, i.e. with blue header;

var idProf="";
var idClasse="";
var idRessource="";

var po=1;

function trimString(sInString) {
  sInString = sInString.replace( /^\s+/g, "" );
  return sInString.replace( /\s+$/g, "" );
}

function editEventWindow(e,inputDiv)
{
	if(!inputDiv)inputDiv = this;
	if(!popupWindowUrl)return;
	if(inputDiv.id.indexOf('new_')>=0)return;
	var editEvent = window.open(popupWindowUrl + '?id=' + inputDiv.id,'editEvent','width=650,height=650,status=no');
	editEvent.focus();
}


function setElementActive(e,inputDiv)
{
	if(!inputDiv)inputDiv = this;
	var subDivs = inputDiv.getElementsByTagName('DIV');
	for(var no=0;no<subDivs.length;no++){
		if(subDivs[no].className=='weekScheduler_appointment_header'){
			subDivs[no].className = 'weekScheduler_appointment_headerActive';
		}	
	}
	
	if(activeEventObj && activeEventObj!=inputDiv){
		setElementInactive(activeEventObj);
	}
	activeEventObj = inputDiv;
}
/* updating content - this function is called from popup window */
function setElement_txt(id,text)
{
	var ta = document.getElementById(id).getElementsByTagName('TEXTAREA')[0]
	ta.value = text;
	transferTextAreaContent(false,ta);
}
// update bg color - this function is called from popup window */
function setElement_color(id,color)
{
	document.getElementById(id).style.backgroundColor=color;	
	appointmentProperties[id]['bgColorCode'] = color;
}

function setElementInactive(inputDiv)
{
	var subDivs = inputDiv.getElementsByTagName('DIV');
	for(var no=0;no<subDivs.length;no++){
		if(subDivs[no].className=='weekScheduler_appointment_headerActive'){
			subDivs[no].className = 'weekScheduler_appointment_header';
		}	
	}	
	
	
}

function parseItemsFromServer(ajaxIndex)
{
	var itemsToBeCreated = new Array();
	var items = weekSchedule_ajaxObjects[ajaxIndex].response.split(/<item>/g);
	weekSchedule_ajaxObjects[ajaxIndex] = false;
	for(var no=1;no<items.length;no++){
		var lines = items[no].split(/\n/g);
		itemsToBeCreated[no] = new Array();
		for(var no2=0;no2<lines.length;no2++){
			var key = lines[no2].replace(/<([^>]+)>.*/g,'$1');
			if(key)key = trimString(key);
			var pattern = new RegExp("<\/?" + key + ">","g");
			var value = lines[no2].replace(pattern,'');
			value = trimString(value);
			if(key=='eventStartDate' || key=='eventEndDate'){
				var d = new Date(value);
				value = d;
			}		

			itemsToBeCreated[no][key] = value;
		}	
		
		if(itemsToBeCreated[no]['id']){
			var dayDiff = itemsToBeCreated[no]['eventStartDate'].getTime() - dateStartOfWeek.getTime();
			dayDiff = Math.floor(dayDiff / (1000*60*60*24));
			el_x = dayPositionArray[dayDiff];
			topPos = getYPositionFromTime(itemsToBeCreated[no]['eventStartDate'].getHours(),itemsToBeCreated[no]['eventStartDate'].getMinutes());
			
			var elHeight = (itemsToBeCreated[no]['eventEndDate'].getTime() - itemsToBeCreated[no]['eventStartDate'].getTime()) / (60 * 60*1000);
			elHeight = Math.round((elHeight * (itemRowHeight + 1)) - 2);
			
			if (itemsToBeCreated[no]['idgroupe'] > 0) {
				po=2;
				decal=0;
				if (itemsToBeCreated[no]['position'] == 2) {
					decal=70;
					po=2;
				}
				if (itemsToBeCreated[no]['position'] == 3) {
					decal=50;
					po=3;
				}
				currentAppointmentDiv = createNewAppointmentDiv((el_x - appointmentsOffsetLeft)+decal,topPos,(newAppointmentWidth-(appointmentMarginSize*2))/po,itemsToBeCreated[no]['description'],elHeight);	
			}else{
				currentAppointmentDiv = createNewAppointmentDiv((el_x - appointmentsOffsetLeft),topPos,(newAppointmentWidth-(appointmentMarginSize*2)),itemsToBeCreated[no]['description'],elHeight);	
			}
				
			currentAppointmentDiv.id = itemsToBeCreated[no]['id'];
			currentZIndex = currentZIndex + 1;
			currentAppointmentDiv.style.zIndex = currentZIndex;
			currentTimeDiv = getCurrentTimeDiv(currentAppointmentDiv);
			currentTimeDiv.style.display='block';
			
			if(itemsToBeCreated[no]['bgColorCode'] && itemsToBeCreated[no]['bgColorCode'].match(/^#[0-9A-F]{6}$/)){
				currentAppointmentDiv.style.backgroundColor = itemsToBeCreated[no]['bgColorCode'];
			}

			currentAppointmentContentDiv  = getCurrentAppointmentContentDiv(currentAppointmentDiv);
			currentAppointmentContentDiv.style.height = (elHeight-20) + 'px';
			
			currentTimeDiv.innerHTML = '<span>' + getTime(currentAppointmentDiv) + '</span>';	
			autoResizeAppointment();
			
			currentAppointmentDiv = false;	
			currentTimeDiv = false;		
			
			var newIndex = itemsToBeCreated[no]['id'];
			appointmentProperties[newIndex] = new Array();
			appointmentProperties[newIndex]['id'] = itemsToBeCreated[no]['id'];
			appointmentProperties[newIndex]['description'] = itemsToBeCreated[no]['description'];			
			appointmentProperties[newIndex]['bgColorCode'] = itemsToBeCreated[no]['bgColorCode'];			
			appointmentProperties[newIndex]['eventStartDate'] = itemsToBeCreated[no]['eventStartDate'];			
			appointmentProperties[newIndex]['eventEndDate'] = itemsToBeCreated[no]['eventEndDate'];		
			appointmentProperties[newIndex]['object'] = currentAppointmentDiv;	
		}
	}
}

/* Update date and hour properties for an appointment after move or drag */

function updateAppointmentProperties(id)
{
	var obj = document.getElementById(id);
	var timeArray = getTimeAsArray(obj); 
	var startDate = getAppointmentDate(obj);
	var endDate = new Date();
	endDate.setTime(startDate.getTime());
	
	startDate.setHours(timeArray[0]);
	startDate.setMinutes(timeArray[1]);
	
	endDate.setHours(timeArray[2]);
	endDate.setMinutes(timeArray[3]);
	
	/*
	var startDateString = startDate.toGMTString().replace('UTC','GMT');
	var endDateString = endDate.toGMTString().replace('UTC','GMT');
	*/
	appointmentProperties[obj.id]['eventStartDate'] = startDate;
	appointmentProperties[obj.id]['eventEndDate'] = endDate;
	
	if(instantSave && appointmentProperties[obj.id]['description'].length>0){
		saveAnItemToServer(obj.id);
	}
		
	
}

function getYPositionFromTime(hour,minute){
	return Math.floor((hour - weekplannerStartHour) * (itemRowHeight+1) + (minute/60 * (itemRowHeight+1)));
}

function getItemsFromServer()
{
	var ajaxIndex = weekSchedule_ajaxObjects.length;
	weekSchedule_ajaxObjects[ajaxIndex] = new sack();	
	weekSchedule_ajaxObjects[ajaxIndex].requestFile = externalSourceFile_items  + '?year=' + dateStartOfWeek.getFullYear() + '&month=' + (dateStartOfWeek.getMonth()/1+1) + '&day=' + dateStartOfWeek.getDate() + '&idclasse='+  idClasse  + '&idprof='+  idProf + '&idRessource=' + idRessource ;	// Specifying which file to get
	weekSchedule_ajaxObjects[ajaxIndex].onCompletion = function(){ parseItemsFromServer(ajaxIndex); };	// Specify function that will be executed after file has been found
	weekSchedule_ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function		
}

function getCurrentTimeDiv(inputObj)
{
	var subDivs = inputObj.getElementsByTagName('DIV');
	for(var no=0;no<subDivs.length;no++){
		if(subDivs[no].className=='weekScheduler_appointment_time'){
			return subDivs[no];
		}
	}
}

function getCurrentAppointmentContentDiv(inputDiv)
{
	var divs = inputDiv.getElementsByTagName('DIV');
	for(var no=0;no<divs.length;no++){
		if(divs[no].className=='weekScheduler_appointment_txt')return divs[no];
	}
}

function getAppointmentDate(inputObj)
{
	var leftPos = getLeftPos(inputObj);
	//var d = new Date('<?php print dateY() ?>','<?php print dateM() ?>','<?php print datej() ?>','<?php print dateH() ?>','<?php print dateI() ?>',0);
	var d = new Date();
	var tmpTime = dateStartOfWeek.getTime();
	tmpTime = tmpTime + (1000*60*60*24 * Math.floor((leftPos-appointmentsOffsetLeft) / (dayPositionArray[1] - dayPositionArray[0])));
	d.setTime(tmpTime);
	return d;
	
	
}

function getTimeAsArray(inputObj)
{
	var startTime = (getTopPos(inputObj) - appointmentsOffsetTop) / (itemRowHeight+1) + weekplannerStartHour;
	if(startTime>23)startTime = startTime - 24;
	var startHour = Math.floor(startTime);
	var startMinute = Math.floor((startTime - startHour) *60);
	var endTime = (getTopPos(inputObj) + inputObj.offsetHeight - appointmentsOffsetTop) / (itemRowHeight+1) + weekplannerStartHour;
	if(endTime>23)endTime = endTime - 24;
	var endHour = Math.floor(endTime);
	var endMinute = Math.floor((endTime - endHour) *60);
	return Array(startHour,startMinute,endHour,endMinute);	
}


function getTime(inputObj)
{
	var startTime = (getTopPos(inputObj) - appointmentsOffsetTop) / (itemRowHeight+1) + weekplannerStartHour;

	if(startTime>23)startTime = startTime - 24;
	var startHour = Math.floor(startTime);
	var hourPrefix = "";
	if(startHour<10)hourPrefix = "0";
	var startMinute = Math.floor((startTime - startHour) *60);
	var startMinutePrefix = "";
	if(startMinute<10)startMinutePrefix="0";	
	
	var endTime = (getTopPos(inputObj) + inputObj.offsetHeight - appointmentsOffsetTop) / (itemRowHeight+1) + weekplannerStartHour;
	if(endTime>23)endTime = endTime - 24;
	var endHour = Math.floor(endTime);
	
	var endHourPrefix = "";
	if(endHour<10)endHourPrefix = "0";	
	var endMinute = Math.floor((endTime - endHour) *60);
	var endMinutePrefix = "";
	if(endMinute<10)endMinutePrefix="0";
	
	
	if (startMinute == "29") { startMinute = "30"; }
	if (endMinute == "29") { endMinute = "30"; }
	return hourPrefix + startHour + ':' + startMinutePrefix + "" + startMinute + '-' + endHourPrefix + endHour + ':' + endMinutePrefix + "" +  endMinute;	
	
}

function initNewAppointment(e,inputObj)
{
	if(document.all)e = event;
	if(!inputObj)inputObj = this;
	newAppointmentCounter = 0;
	el_x = getLeftPos(inputObj);	
	el_y = getTopPos(inputObj);
	elWidth = inputObj.offsetWidth;
	
	mouse_x = e.clientX;
	mouse_y = e.clientY;
	timerNewAppointment();

	return false;
}

function timerNewAppointment()
{
	if(newAppointmentCounter>=0 && newAppointmentCounter<10){
		newAppointmentCounter = newAppointmentCounter + 1;
		setTimeout('timerNewAppointment()',30);
		return;
	}	
	if(newAppointmentCounter==10){
		
		if(initMinutes){
			var topPos = mouse_y - appointmentsOffsetTop + document.documentElement.scrollTop + document.getElementById('weekScheduler_content').scrollTop;
			topPos = topPos - (getMinute(topPos) % initMinutes);
			var rest = (getMinute(topPos) % initMinutes);
			if(rest!=0){
				topPos = topPos - (getMinute(topPos) % initMinutes);
			}
		}else{
			var topPos = (el_y - appointmentsOffsetTop);
		}
		
		currentAppointmentDiv = createNewAppointmentDiv((el_x - appointmentsOffsetLeft),topPos,(elWidth-(appointmentMarginSize*2)),'');	
		currentAppointmentDiv.id = 'new_' + startIdOfNewItems;
		appointmentProperties[currentAppointmentDiv.id] = new Array();
		appointmentProperties[currentAppointmentDiv.id]['description'] = "";
		appointmentProperties[currentAppointmentDiv.id]['object'] = currentAppointmentDiv;	
		appointmentProperties[currentAppointmentDiv.id]['id'] = currentAppointmentDiv.id;
		startIdOfNewItems++;
		currentAppointmentContentDiv  = getCurrentAppointmentContentDiv(currentAppointmentDiv);
		currentZIndex = currentZIndex + 1;
		currentAppointmentDiv.style.zIndex = currentZIndex;
		currentAppointmentDiv.style.height='20px';
		currentTimeDiv = getCurrentTimeDiv(currentAppointmentDiv);
		currentTimeDiv.style.display='block';
		
	}
}

function initResizeAppointment(e)
{
	if(document.all)e = event;
	currentAppointmentDiv = this.parentNode;
	currentAppointmentContentDiv  = getCurrentAppointmentContentDiv(currentAppointmentDiv);
	currentZIndex = currentZIndex + 1;
	currentAppointmentDiv.style.zIndex = currentZIndex;	
	resizeAppointmentCounter = 0;
	el_x = getLeftPos(currentAppointmentDiv);	
	el_y = getTopPos(currentAppointmentDiv);	
	mouse_x = e.clientX;
	mouse_y = e.clientY;
	
	resizeAppointmentInitHeight = currentAppointmentDiv.style.height.replace('px','')/1;
	
	timerResizeAppointment();	
	return false;
}

function timerResizeAppointment()
{
	if(resizeAppointmentCounter>=0 && resizeAppointmentCounter<10){
		resizeAppointmentCounter = resizeAppointmentCounter + 1;
		setTimeout('timerResizeAppointment()',10);
		return;
	}	
	if(resizeAppointmentCounter==10){
		currentTimeDiv = getCurrentTimeDiv(currentAppointmentDiv);
		currentTimeDiv.style.display='block';
	}
}

function initMoveAppointment(e,inputObj)
{
	if(document.all)e = event;
	if(!inputObj)inputObj = this.parentNode;
	currentAppointmentDiv = inputObj;
	currentAppointmentContentDiv  = getCurrentAppointmentContentDiv(currentAppointmentDiv);
	currentZIndex = currentZIndex + 1;
	currentAppointmentDiv.style.zIndex = currentZIndex;	
	moveAppointmentCounter = 0;
	el_x = getLeftPos(inputObj);	
	el_y = getTopPos(inputObj);
	elWidth = inputObj.offsetWidth;
	
	mouse_x = e.clientX;
	mouse_y = e.clientY;
	
	

	timerMoveAppointment();
	return false;
	
}

function timerMoveAppointment()
{
	if(moveAppointmentCounter>=0 && moveAppointmentCounter<10){
		moveAppointmentCounter = moveAppointmentCounter + 1;
		setTimeout('timerMoveAppointment()',10);
		return;
	}
	if(moveAppointmentCounter==10){
		currentTimeDiv = getCurrentTimeDiv(currentAppointmentDiv);
		currentTimeDiv.style.display='block';
	}	
}

function getMinute(topPos)
{
	var time = (topPos) / (itemRowHeight+1);

	var hour = Math.floor(time);
	var minute = Math.floor((time - hour) *60);
	return minute;
}


function schedulerMouseMove(e)
{
	if(document.all)e = event;
	if(newAppointmentCounter==10){
		if(!currentAppointmentDiv)return;
		var tmpHeight = e.clientY - mouse_y;
		currentAppointmentDiv.style.height = Math.max(20,tmpHeight) + 'px';
		currentTimeDiv.innerHTML = '<span>' + getTime(currentAppointmentDiv) + '</span>';	
	}

	if(moveAppointmentCounter==10){
		var topPos = (e.clientY - mouse_y + el_y - appointmentsOffsetTop);

		currentAppointmentDiv.style.top = topPos + 'px';
		
		var destinationLeftPos = false;
		for(var no=0;no<dayPositionArray.length;no++){
			if(e.clientX>dayPositionArray[no])destinationLeftPos = dayPositionArray[no];			
		}
		
		currentAppointmentDiv.style.left = (destinationLeftPos + appointmentMarginSize -2) + 'px';
		
		currentTimeDiv.innerHTML = '<span>' + getTime(currentAppointmentDiv) + '</span>';	
	}
	
	if(resizeAppointmentCounter==10){
		currentAppointmentContentDiv.style.height = (Math.max((resizeAppointmentInitHeight + e.clientY - mouse_y),10)-8) + 'px';
		currentAppointmentDiv.style.height = Math.max((resizeAppointmentInitHeight + e.clientY - mouse_y),10) + 'px';
		currentTimeDiv.innerHTML = '<span>' + getTime(currentAppointmentDiv) + '</span>';		
	}
	
}

function repositionFooter(inputDiv)
{
	var subDivs = inputDiv.getElementsByTagName('DIV');
	for(var no=0;no<subDivs.length;no++){
		if(subDivs[no].className=='weekScheduler_appointment_footer'){
			subDivs[no].style.bottom = '-1px';
		}
	}	
}


/* This function copies content from ta to the span element */

function transferTextAreaContent(e,inputObj,discardContentUpdate)
{
	if(!inputObj)inputObj = this;
	inputObj.style.display='none';
	var spans = inputObj.parentNode.getElementsByTagName('DIV');
	for(var no=0;no<spans.length;no++){
		if(spans[no].className=='weekScheduler_appointment_txt'){
			if(!discardContentUpdate){
				appointmentProperties[inputObj.parentNode.id]['description'] = inputObj.value;
				spans[no].innerHTML = '<span style="float:right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;</span>' + inputObj.value.replace(/\n/g,'<br>');
			}
			spans[no].style.display='block';

		}	
		if(spans[no].className=='weekScheduler_appointment_footer'){
			spans[no].style.display='block';
		}	
		if(spans[no].className=='weekScheduler_appointment_time'){
			spans[no].style.display='block';
		}			
	}
	contentEditInProgress = false;
	currentEditableTextArea = false;
	repositionFooter(inputObj.parentNode);
	
	if(instantSave && appointmentProperties[inputObj.parentNode.id]['description'].length>0){
		saveAnItemToServer(inputObj.parentNode.id);
	}
		
}

function saveAnItemToServer_complete(index,oldId)
{
	if(oldId.indexOf('new_')>=0){
		appointmentProperties[oldId]['id'] = weekSchedule_ajaxObjects[index].response;
		appointmentProperties[oldId]['object'].id = weekSchedule_ajaxObjects[index].response.replace(/\s/g,'');
		appointmentProperties[weekSchedule_ajaxObjects[index].response] = appointmentProperties[oldId];
		
		weekSchedule_ajaxObjects[index] = false;
		
		if(!inlineTextAreaEnabled){
			editEventWindow(false,appointmentProperties[oldId]['object']);	
		}
	}
}

/* 
This function clears all appointments from the screen - Used when switching from one week to another 
*/
function clearAppointments()
{
	for(var prop in appointmentProperties){
		if(appointmentProperties[prop]['id']){
			if(document.getElementById(appointmentProperties[prop]['id'])){
				var obj = document.getElementById(appointmentProperties[prop]['id']);
				obj.parentNode.removeChild(obj);
			}
			appointmentProperties[prop]['id'] = false;	
		}
		
	}	
}

function saveAnItemToServer(inputId)
{

	if(!appointmentProperties[inputId]['description'])appointmentProperties[inputId]['description']='';
	if(!appointmentProperties[inputId]['bgColorCode'])appointmentProperties[inputId]['bgColorCode']='';
	if(!appointmentProperties[inputId]['eventStartDate'])updateAppointmentProperties(inputId);
	
	var saveString = "?saveAnItem=true&id=" + appointmentProperties[inputId]['id']
	+ '&description=' + escape(appointmentProperties[inputId]['description'])
	+ '&bgColorCode=' + escape(appointmentProperties[inputId]['bgColorCode'])
	+ '&eventStartDate=' + appointmentProperties[inputId]['eventStartDate'].toGMTString().replace('UTC','GMT')
	+ '&eventEndDate=' + appointmentProperties[inputId]['eventEndDate'].toGMTString().replace('UTC','GMT');
	
	if(appointmentProperties[inputId]['id'].indexOf('new_')>=0){
		saveString = saveString + '&newItem=1';	
	}
	
			
	var ajaxIndex = weekSchedule_ajaxObjects.length;
	weekSchedule_ajaxObjects[ajaxIndex] = new sack();	
	weekSchedule_ajaxObjects[ajaxIndex].requestFile = externalSourceFile_save  + saveString;
	weekSchedule_ajaxObjects[ajaxIndex].onCompletion = function(){ saveAnItemToServer_complete(ajaxIndex,appointmentProperties[inputId]['id']); };	
       // Specify function that will be executed after file has been found
	weekSchedule_ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function

}

function ffEndEdit(e)
{
	if(!currentEditableTextArea)return;
	if (e.target) source = e.target;
		else if (e.srcElement) source = e.srcElement;
		if (source.nodeType == 3) // defeat Safari bug
			source = source.parentNode;	
	if(source.tagName.toLowerCase()!='textarea')currentEditableTextArea.blur();			
}

function initToggleView(e)
{
	if(document.all)e = event;
	if (e.target) source = e.target;
		else if (e.srcElement) source = e.srcElement;
		if (source.nodeType == 3) // defeat Safari bug
			source = source.parentNode;	

	if(source.className && source.className!='weekScheduler_appointment_txt' && source.className!='weekScheduler_anAppointment'){		
		return;
	}		
	toggleViewCounter = 0;
	objectToToggle = this;
	timerToggleView();
	
}
function timerToggleView()
{
	if(toggleViewCounter>=0 && toggleViewCounter<10){
		toggleViewCounter = toggleViewCounter + 1;
		setTimeout('timerToggleView()',50);
	}
	if(toggleViewCounter==10){
		toggleViewCounter = -1;
		toggleAppointmentView(false,objectToToggle);	
		
	}
}


function toggleAppointmentView(e,inputObj)
{

	if(document.all)e = event;
	
	if(!inlineTextAreaEnabled)return;
	
	if(!inputObj){
		inputObj = this;		
	}
	if(e){
		if (e.target) source = e.target;
			else if (e.srcElement) source = e.srcElement;
			if (source.nodeType == 3) // defeat Safari bug
				source = source.parentNode;	
		if(source.tagName.toLowerCase()=='textarea')return;
		if(contentEditInProgress && source.tagName=='DIV'){
			transferTextAreaContent(false,currentAppointmentDiv.getElementsByTagName('TEXTAREA')[0]);
			return;	
		}
		if(source.className && source.className!='weekScheduler_anAppointment' && source.className!='weekScheduler_appointment_txt')return;
	
	}

	
	currentAppointmentDiv = inputObj;
	var spans = inputObj.getElementsByTagName('DIV');	
	
	var tmpValue = "";
	for(var no=0;no<spans.length;no++){
		if(spans[no].className=='weekScheduler_appointment_txt'){
			spans[no].style.display='none';	
			tmpValue = appointmentProperties[inputObj.id]['description'];
		}
		if(spans[no].className=='weekScheduler_appointment_footer'){
			spans[no].style.display='none';					
		}
		if(spans[no].className=='weekScheduler_appointment_time'){
			spans[no].style.display='none';					
		}				
	}

	var ta = currentAppointmentDiv.getElementsByTagName('TEXTAREA')[0];
	ta.style.width = (currentAppointmentDiv.clientWidth - 6) + 'px';
	ta.style.height = (currentAppointmentDiv.offsetHeight-14) + 'px';
	ta.style.display='inline';
	ta.value = tmpValue;
	contentEditInProgress = true;
	currentEditableTextArea = ta;
	ta.focus();			
		
	
	
	
}

/* Checking keyboard event for the textarea */

function keyboardEventTextarea(e)
{
	if(document.all)e = event;
	if(e.keyCode==27){	// Escape key
		transferTextAreaContent(false,this,true);
	}
}

/*
Creating new appointment DIV
*/

function createNewAppointmentDiv(leftPos,topPos,width,contentHTML,height)
{
	var div = document.createElement('DIV');
	div.onclick = setElementActive;
	div.ondblclick = editEventWindow;
	div.className='weekScheduler_anAppointment';
	div.style.left = leftPos + 'px';
	div.style.top = topPos + 'px';
	div.style.width = width + 'px';
	div.onmousedown = initToggleView;
	if(height)div.style.height = height + 'px';
	var timeDiv = document.createElement('DIV');
	timeDiv.className='weekScheduler_appointment_time';
	timeDiv.innerHTML = '<span></span>';
	div.appendChild(timeDiv);

	
	var header = document.createElement('DIV');
	header.className= 'weekScheduler_appointment_header';
	header.innerHTML = '<span></span>';
	header.onmousedown = initMoveAppointment;
	header.style.cursor = 'move';
	div.appendChild(header);
	
	var span = document.createElement('DIV');	

	var innerSpan = document.createElement('SPAN');
	
	innerSpan.innerHTML = '<span style="float:right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;</span>' + contentHTML;
	span.appendChild(innerSpan);
	span.className = 'weekScheduler_appointment_txt';
	div.appendChild(span);
	
	var textarea = document.createElement('TEXTAREA');
	textarea.className='weekScheduler_appointment_textarea';
	textarea.style.display='none';
	textarea.onblur = transferTextAreaContent;
	textarea.onkeyup = keyboardEventTextarea;
	div.appendChild(textarea);
	
	var colorCodeDiv = document.createElement('DIV');
	colorCodeDiv.className='weekScheduler_appointment_colorCodes';
	div.appendChild(colorCodeDiv);
	
	var footerDiv = document.createElement('DIV');
	footerDiv.className='weekScheduler_appointment_footer';
	footerDiv.style.cursor = 'n-resize';
	footerDiv.innerHTML = '<span></span>';
	footerDiv.onmousedown = initResizeAppointment;
	div.appendChild(footerDiv);
	

	
	
	weekScheduler_appointments.appendChild(div);		
	return div;
}



function schedulerMouseUp()
{
	if(newAppointmentCounter>=0){
		
		
		if(newAppointmentCounter==10){
			if(!currentAppointmentDiv)return;
			if(inlineTextAreaEnabled){		
				var spans = currentAppointmentDiv.getElementsByTagName('DIV');
				for(var no=0;no<spans.length;no++){
					if(spans[no].className=='weekScheduler_appointment_txt'){
						spans[no].style.display='none';					
					}
					if(spans[no].className=='weekScheduler_appointment_footer'){
						spans[no].style.display='none';					
					}
					if(spans[no].className=='weekScheduler_appointment_time'){
						spans[no].style.display='none';					
					}				
				}
				
				
				var ta = currentAppointmentDiv.getElementsByTagName('TEXTAREA')[0];
				ta.style.width = (currentAppointmentDiv.clientWidth - 6) + 'px';
				ta.style.height = (currentAppointmentDiv.offsetHeight-14) + 'px';
				ta.style.display='inline';
				ta.focus();
			}
			else{
				
				saveAnItemToServer(currentAppointmentDiv.id);
			}
			
		}
	}
	if(snapToMinutes && currentAppointmentDiv && moveAppointmentCounter==10){
		topPos = getTopPos(currentAppointmentDiv) - appointmentsOffsetTop;
		
		var minute = getMinute(topPos);
		var rest = (minute % snapToMinutes);
		if(rest>(snapToMinutes/2)){
			topPos = topPos + (snapToMinutes/60*(itemRowHeight+1)) - ((rest/60)*(itemRowHeight+1));
		}else{
			topPos = topPos - ((rest/60)*(itemRowHeight+1));
		}
		var minute = getMinute(topPos);	
		var rest = (minute % snapToMinutes);
		if(rest!=0){
			topPos = topPos - ((rest/60)*(itemRowHeight+1));
		}
		
		var minute = getMinute(topPos);
		var rest = (minute % snapToMinutes);
		if(rest!=0){
			topPos = topPos - ((rest/60)*(itemRowHeight+1));
		}
		currentAppointmentDiv.style.top = topPos + 'px';
		currentTimeDiv.innerHTML = '<span>' + getTime(currentAppointmentDiv) + '</span>';
	}
		
	if(currentAppointmentDiv && snapToMinutes && (resizeAppointmentCounter==10 || newAppointmentCounter)){
		autoResizeAppointment();
	}
	
	
	if(currentAppointmentDiv && !contentEditInProgress){
		repositionFooter(currentAppointmentDiv);
		updateAppointmentProperties(currentAppointmentDiv.id);
	}
	//if(currentTimeDiv)currentTimeDiv.style.display='none';
	displayPreviousClasse

	
	currentAppointmentDiv = false;
	currentTimeDiv = false;
	moveAppointmentCounter = -1;
	resizeAppointmentCounter = -1;
	newAppointmentCounter = -1;
	toggleViewCounter = -1;
}

function autoResizeAppointment()
{
	var tmpPos = getTopPos(currentAppointmentDiv) - appointmentsOffsetTop + currentAppointmentDiv.offsetHeight;
	var startPos = tmpPos;
	
	var minute = getMinute(tmpPos);
	
	var rest = (minute % snapToMinutes);
	var height = currentAppointmentDiv.style.height.replace('px','')/1;
	
	if(rest>(snapToMinutes/2)){
		tmpPos = tmpPos + snapToMinutes - (minute % snapToMinutes);
	}else{
		tmpPos = tmpPos - (minute % snapToMinutes);
	}		
	
	var minute = getMinute(tmpPos);
	if((minute % snapToMinutes)!=0){
		tmpPos = tmpPos - (minute % snapToMinutes);
	}
	var minute = getMinute(tmpPos);
	if((minute % snapToMinutes)!=0){
		tmpPos = tmpPos - (minute % snapToMinutes);
	}		
	
	currentAppointmentDiv.style.height = (height + tmpPos - startPos) + 'px';
	currentTimeDiv.innerHTML = '<span id="stime" >' + getTime(currentAppointmentDiv) + '</span>';	
	
}

function deleteEventFromView(index) {
	if(weekSchedule_ajaxObjects[index].response=='OK'){
		activeEventObj.parentNode.removeChild(activeEventObj);
		activeEventObj = false;	
	}else{
		// history.go(0);
		// Error handling - event not deleted
		clearAppointments();
		getItemsFromServer();
	}
}


function schedulerKeyboardEvent(e){
	if(document.all)e = event;
	if(e.keyCode==46 && activeEventObj){
		if(confirm(txt_deleteEvent)){
			
			
			var ajaxIndex = weekSchedule_ajaxObjects.length;
			weekSchedule_ajaxObjects[ajaxIndex] = new sack();	
			weekSchedule_ajaxObjects[ajaxIndex].requestFile = externalSourceFile_delete  + '?eventToDeleteId=' + activeEventObj.id;
			
			weekSchedule_ajaxObjects[ajaxIndex].onCompletion = function(){ deleteEventFromView(ajaxIndex); };	// Specify function that will be executed after file has been found
			weekSchedule_ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function	
		}		
	}	
}


function getTopPos(inputObj)
{		
  var returnValue = inputObj.offsetTop;
  while((inputObj = inputObj.offsetParent) != null){
  	if(inputObj.tagName!='HTML')returnValue += inputObj.offsetTop;
  }
  return returnValue;
}

function getLeftPos(inputObj)
{
  var returnValue = inputObj.offsetLeft;
  while((inputObj = inputObj.offsetParent) != null){
  	if(inputObj.tagName!='HTML')returnValue += inputObj.offsetLeft;
  }
  return returnValue;
}
	
	
function cancelSelectionEvent(e)
{
	if(document.all)e = event;
	
	if (e.target) source = e.target;
		else if (e.srcElement) source = e.srcElement;
		if (source.nodeType == 3) // defeat Safari bug
			source = source.parentNode;
	if(source.tagName.toLowerCase()=='input' || source.tagName.toLowerCase()=='textarea')return true;
					
	return false;
	
}
function initWeekScheduler()
{
	weekScheduler_container = document.getElementById('weekScheduler_container');
	if(!document.all)weekScheduler_container.onclick = ffEndEdit;
	weekScheduler_appointments = document.getElementById('weekScheduler_appointments');
	var subDivs = weekScheduler_appointments.getElementsByTagName('DIV');
	for(var no=0;no<subDivs.length;no++){
		if(subDivs[no].className=='weekScheduler_appointmentHour'){
			subDivs[no].onmousedown = initNewAppointment;
			
			if(!newAppointmentWidth)newAppointmentWidth = subDivs[no].offsetWidth;
		}
		if(subDivs[no].className=='weekScheduler_appointments_day'){
			dayPositionArray[dayPositionArray.length] = getLeftPos(subDivs[no]);
		}
		
	}
	if(initTopHour > weekplannerStartHour)document.getElementById('weekScheduler_content').scrollTop = ((initTopHour - weekplannerStartHour)*(itemRowHeight+1));
	
	//	initTopHour
	appointmentsOffsetTop = getTopPos(weekScheduler_appointments);
	appointmentsOffsetLeft = 2 - appointmentMarginSize;
	
	document.documentElement.onmousemove = schedulerMouseMove;
	document.documentElement.onselectstart = cancelSelectionEvent;
	document.documentElement.onmouseup = schedulerMouseUp;
	document.documentElement.onkeydown = schedulerKeyboardEvent;
/*
	if (document.getElementById("saisiedate").value != "") {
		var tmpInfo=document.getElementById("saisiedate").value;
		var reg=new RegExp("/","g");
		var tabInfo=tmpInfo.split(reg);	
		var j=tabInfo[0];
		var m=tabInfo[1];
		var a=tabInfo[2];
		var tmpDate = new Date(a,m,j);
	}
	*/
	var tmpDate = new Date();
	var dateItems = initDateToShow.split(/\-/g);
	tmpDate.setFullYear(dateItems[0]);
	tmpDate.setDate(dateItems[2]/1);
	tmpDate.setMonth(dateItems[1]/1-1);
	tmpDate.setHours(1);
	tmpDate.setMinutes(0);
	tmpDate.setSeconds(0);
	
	var day = tmpDate.getDay();
	if(day==0)day=7;
	if(day>1){
		var time = tmpDate.getTime();
		time = time - (1000*60*60*24) * (day-1);
		tmpDate.setTime(time);	
	}
	dateStartOfWeek = new Date(tmpDate);
	
	updateHeaderDates();
	
	if(externalSourceFile_items){
		getItemsFromServer();		
	}
	
	
	
}

function displayPreviousWeek()
{
	var tmpTime = dateStartOfWeek.getTime();
	tmpTime = tmpTime - (1000*60*60*24*7);
	dateStartOfWeek.setTime(tmpTime);
	
	updateHeaderDates();	
	clearAppointments();
	getItemsFromServer();
	
}


function displayPreviousWeek2(chaine) {
	var tab=chaine.split("/");
	var jour = tab[0];
	var mois = tab[1];
	var annee = tab[2];	
	var home=new Date();
        home.setFullYear(annee);
        home.setDate(jour/1);
        home.setMonth(mois/1-1);
        home.setHours(1);
        home.setMinutes(0);
        home.setSeconds(0);
	var day = home.getDay();
	if(day==0)day=7;
	if(day>1){
		var time = home.getTime();
		time = time - (1000*60*60*24) * (day-1);
		home.setTime(time);	
	}
	dateStartOfWeek = new Date(home);
	updateHeaderDates();	
	clearAppointments();
	getItemsFromServer();
}

function displayPreviousProf()
{
	var val=document.form.profID.options.selectedIndex
	idProf=document.form.profID.options[val].value;
	clearAppointments();
	getItemsFromServer();
}



function displayPreviousClasse()
{
	var val=document.form.classeID.options.selectedIndex
	idClasse=document.form.classeID.options[val].value;
	clearAppointments();
	getItemsFromServer();	
}

function displayPreviousRessource()
{
	var val=document.form.ressourceID.options.selectedIndex
	idRessource=document.form.ressourceID.options[val].value;
	clearAppointments();
	getItemsFromServer();	
}


function displayNextWeek()
{
	var tmpTime = dateStartOfWeek.getTime();
	tmpTime = tmpTime + (1000*60*60*24*7);
	dateStartOfWeek.setTime(tmpTime);
	updateHeaderDates();
	clearAppointments();
	getItemsFromServer();
}

function updateHeaderDates()
{
	var weekScheduler_dayRow = document.getElementById('weekScheduler_dayRow');
	var subDivs = weekScheduler_dayRow.getElementsByTagName('DIV');		
	var tmpDate2 = new Date(dateStartOfWeek);
	
	
	for(var no=0;no<subDivs.length;no++){
		var month = tmpDate2.getMonth()/1 + 1;
		var date = tmpDate2.getDate();
		var tmpHeaderFormat = " " + headerDateFormat + " :m: ";
		tmpHeaderFormat = tmpHeaderFormat.replace('d',date);
		tmpHeaderFormat = tmpHeaderFormat.replace('m',month);

		tmpHeaderFormat = tmpHeaderFormat.replace(':1:',"Janvier");
		tmpHeaderFormat = tmpHeaderFormat.replace(':2:',"Février");
		tmpHeaderFormat = tmpHeaderFormat.replace(':3:',"Mars");
		tmpHeaderFormat = tmpHeaderFormat.replace(':4:',"Avril");
		tmpHeaderFormat = tmpHeaderFormat.replace(':5:',"Mai");
		tmpHeaderFormat = tmpHeaderFormat.replace(':6:',"Juin");
		tmpHeaderFormat = tmpHeaderFormat.replace(':7:',"Juillet");
		tmpHeaderFormat = tmpHeaderFormat.replace(':8:',"Aout");
		tmpHeaderFormat = tmpHeaderFormat.replace(':9:',"Septembre");
		tmpHeaderFormat = tmpHeaderFormat.replace(':10:',"Octobre");
		tmpHeaderFormat = tmpHeaderFormat.replace(':11:',"Novembre");
		tmpHeaderFormat = tmpHeaderFormat.replace(':12:',"Décembre");

		subDivs[no].getElementsByTagName('SPAN')[0].innerHTML = tmpHeaderFormat;
		
		dayDateArray[no] = month + '|' + date;
		
		var time = tmpDate2.getTime();
		time = time + (1000*60*60*24);
		tmpDate2.setTime(time);
	}	
}

window.onload = initWeekScheduler;
