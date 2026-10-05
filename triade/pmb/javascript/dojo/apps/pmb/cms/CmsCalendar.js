// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsCalendar.js,v 1.2 2021/02/10 16:22:53 qvarin Exp $


define([
	"dojo/_base/declare",
	"dijit/Calendar",
	"dojo/date",
	"dojo/on",
	"dijit/Tooltip",
	"dojo/_base/lang",
	"dojo/query",
	"dojo/dom-attr",
], function(declare, Calendar, date, on, Tooltip, lang, query, domAttr) {
	return declare([Calendar], {
		style: 'width:100%;',
		events: [],
		tooltips: [],
		singleEventLink: "",
		eventsLink: "",
		
		postCreate: function () {
			this.inherited(arguments);
			this.initEvents();
			this.addToolTips();
		},
		
		initEvents: function () {
 			on(this.monthWidget.dropDown, "click", lang.hitch(this, this.realoadToolTips));
 			on(this.decrementMonth, "click", lang.hitch(this, this.realoadToolTips));
			on(this.incrementMonth, "click", lang.hitch(this, this.realoadToolTips));
			on(this.previousYearLabelNode, "click", lang.hitch(this, this.realoadToolTips));
			on(this.nextYearLabelNode, "click", lang.hitch(this, this.realoadToolTips));
		},
		
		realoadToolTips: function () {
			this.removeToolTips();
			this.addToolTips();
		},
		
		addToolTips : function () {
			for (let i = 0; i < this.dateCells.length; i++) {
				var TooltipLabel = "";
				var dateCell = this.dateCells[i];
				dateCell.id = this.id + "_" + dateCell.dijitDateValue.toString()
				dayDate = new Date(dateCell.dijitDateValue);
				for (let j = 0; j < this.events.length; j++) {
					var event = this.events[j];
					start_day = new Date(event['event_start']['time']*1000);
					start_day.setHours(1,0,0,0);
					if (event['event_end']) {
						end_day = new Date(event['event_end']['time']*1000);
						end_day.setHours(1,0,0,0);
					} else {
						end_day = false;
					}
					
					if ( (dayDate.valueOf()>=start_day.valueOf() && (end_day && dayDate.valueOf()<=end_day.valueOf())) || dayDate.valueOf()==start_day.valueOf() ) {
						if (TooltipLabel != "") {
							TooltipLabel += "<br>";
						}
						TooltipLabel += event.event_title;
					}
				}
				
				if (TooltipLabel) {
					this.tooltips.push(new Tooltip({
						showDelay: 500,
						hideDelay: 250,
						connectId: [dateCell.id],
						label: "<div class='tooltipCalendar'>"+ TooltipLabel +"</div>"
					}));
				}
			}
		},
		
		removeToolTips: function () {
			if (this.tooltips.length !== 0) {
				var tooltips = this.tooltips;
				this.tooltips = [];
				for (let i = 0; i < tooltips.length; i++) {
					tooltips[i].connectId = []
					tooltips[i].destroyDescendants();
					tooltips[i].destroy();
				}
			}
		},
		
		getClassForDate: function (date, locale) {
			var classname='';
			dojo.forEach(this.events, function (event){
				start_day = new Date(event['event_start']['time']*1000);
				start_day.setHours(1,0,0,0);
				if (event['event_end']) {
					end_day = new Date(event['event_end']['time']*1000);
					end_day.setHours(1,0,0,0);
				} else {
					end_day = false;
				}
				if ( (date.valueOf()>=start_day.valueOf() && (end_day && date.valueOf()<=end_day.valueOf())) || date.valueOf()==start_day.valueOf() ) {
					if (classname.indexOf('cms_module_agenda_event_'+event.id_type) === -1) {
						classname+='cms_module_agenda_event_'+event.id_type;	
					}
					if (classname) {
						classname+= ' ';
						if(classname.indexOf('cms_module_agenda_multiple_events') === -1) {
							classname+=' cms_module_agenda_multiple_events ';
						}
					}
				}
			});
			
			return classname;
		},
		
		onChange: function (value) {
			if(value) {
				var current_events = new Array();
				dojo.forEach(this.events, function (event) {
					start_day = new Date(event['event_start']['time']*1000);
					if (event['event_end']) {
						end_day = new Date(event['event_end']['time']*1000);
					} else {
						end_day = false;
					} 
				
					//juste une date ou dates debut et fin
					if (date.difference(value, start_day, 'day') == 0 || (start_day && end_day && date.difference(value, start_day, 'day') <= 0 && date.difference(value, end_day, 'day') >= 0 )) {
						current_events.push(event);
					}
					start_day = end_day = false;
				});
				
				if (current_events.length == 1) {
					//un seul evenement sur la journee, on l'affiche directement
					var link = this.singleEventLink;
					document.location = link.replace('!!id!!',current_events[0]['id_event']);
				} else if (current_events.length > 1) {
					//plusieurs evenements, on affiche la liste...
					var month = value.getMonth()+1;
					var day = value.getDate();
					var day = value.getFullYear()+'-'+(month >9 ? month : '0'+month)+'-'+(day > 9 ? day : '0'+day);
					var link = this.eventsLink;
					document.location = link.replace('!!date!!', day);
				}
			}
		}
	});
});