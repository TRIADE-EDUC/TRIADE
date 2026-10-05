// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsAnimationsCalendar.js,v 1.2 2022/10/06 07:32:19 gneveu Exp $

define([
	"dojo/_base/declare",
	"apps/pmb/cms/CmsCalendar",
	"dojo/date",
	"dojo/on",
	"dijit/Tooltip",
	"dojo/_base/lang",
	"dojo/query",
	"dojo/dom-attr",
], function(declare, CmsCalendar, date, on, Tooltip, lang, query, domAttr) {
	return declare([CmsCalendar], {
		style : "width:100%;",
		animations : [],
		tooltips : [],
		singleAnimationLink : "",
		animationsLink : "",
		
		addToolTips : function () {
			for (let i = 0; i < this.dateCells.length; i++) {
				var TooltipLabel = "";
				var dateCell = this.dateCells[i];
				dateCell.id = this.id + "_" + dateCell.dijitDateValue.toString();
				dayDate = new Date(dateCell.dijitDateValue);
				for (let j = 0; j < this.animations.length; j++) {
					var animation = this.animations[j];
					start_day = new Date(animation['event']['startDateTime'] * 1000);
					start_day.setHours(1, 0, 0, 0);
					
					end_day = false;
					if (animation['event']['startDate'] != animation['event']['endDate']) {
						end_day = new Date(animation['event']['endDateTime'] * 1000);
						end_day.setHours(1, 0, 0, 0);
					}
					
					if ((dayDate.valueOf() >= start_day.valueOf() && (end_day && dayDate.valueOf() <= end_day.valueOf())) || dayDate.valueOf() == start_day.valueOf()) {
						if (TooltipLabel != "") {
							TooltipLabel += "<br>";
						}
						TooltipLabel += animation['name'];
					}
				}
				
				if (TooltipLabel) {
					this.tooltips.push(new Tooltip({
						showDelay : 500,
						hideDelay : 250,
						connectId : [dateCell.id],
						label : "<div class='tooltipCalendar'>" + TooltipLabel + "</div>"
					}));
				}
			}
		},
		
		getClassForDate : function (date, locale) {
			var classname = "";
			dojo.forEach(this.animations, function (animation) {
				start_day = new Date(animation['event']['startDateTime'] * 1000);
				start_day.setHours(1, 0, 0, 0);
				
				end_day = false;
				if (animation['event']['startDate'] != animation['event']['endDate']) {
					end_day = new Date(animation['event']['endDateTime'] * 1000);
					end_day.setHours(1, 0, 0, 0);
				}
				
				if ((date.valueOf() >= start_day.valueOf() && (end_day && date.valueOf() <= end_day.valueOf())) || date.valueOf() == start_day.valueOf()) {
					if (classname.indexOf('cms_module_animationslist_animation_' + animation['color_key']) === -1) {
						classname += 'cms_module_animationslist_animation_' + animation['color_key'];	
					}
					if (classname) {
						classname += ' ';
						if (classname.indexOf('cms_module_animationlist_multiple_animations') === -1) {
							classname += ' cms_module_animationlist_multiple_animations ';
						}
					}
				}
			});
			
			return classname;
		},
		
		onChange : function (value) {
			if (value) {
				var current_animations = new Array();
				dojo.forEach(this.animations, function (animation) {
					start_day = new Date(animation['event']['startDateTime'] * 1000);
					
					end_day = false;
					if (animation['event']['startDate'] != animation['event']['endDate']) {
						end_day = new Date(animation['event']['endDateTime'] * 1000);
					} 
				
					if (date.difference(value, start_day, 'day') == 0 || (start_day && end_day && date.difference(value, start_day, 'day') <= 0 && date.difference(value, end_day, 'day') >= 0)) {
						current_animations.push(animation);
					}
					start_day = false;
				});
				
				if (current_animations.length == 1) {
					var link = this.singleAnimationLink;
					document.location = link.replace('!!id!!', current_animations[0]['id']);
				} else if (current_animations.length > 1) {
					var month = value.getMonth() + 1;
					var day = value.getDate();
					var day = value.getFullYear() + '-' + (month > 9 ? month : '0' + month) + '-' + (day > 9 ? day : '0' + day);
					var link = this.animationsLink;
					document.location = link.replace('!!date!!', day);
				}
			}
		}
	});
});