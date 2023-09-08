<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

/*  Copyright Mihai Bazon, 2002, 2003  |  http://dynarch.com/mishoo/
 * ---------------------------------------------------------------------------
 *
 * The DHTML Calendar
 *
 * Details and latest version at:
 * http://dynarch.com/mishoo/calendar.epl
 *
 * This script is distributed under the GNU Lesser General Public License.
 * Read the entire license text here: http://www.gnu.org/licenses/lgpl.html
 *
 * This file defines helper functions for setting up the calendar.  They are
 * intended to help non-programmers get a working calendar on their site
 * quickly.  This script should not be seen as part of the calendar.  It just
 * shows you what one can do with the calendar, while in the same time
 * providing a quick and simple method for setting it up.  If you need
 * exhaustive customization of the calendar creation process feel free to
 * modify this code to suit your needs (this is recommended and much better
 * than modifying calendar.js itself).
 */

// $Id: calendar-setup.js,v 1.25 2005/03/07 09:51:33 mishoo Exp $

/**
 *  This function "patches" an input field (or other element) to use a calendar
 *  widget for date selection.
 *
 *  The "params" is a single object that can have the following properties:
 *
 *    prop. name   | description
 *  -------------------------------------------------------------------------------------------------
 *   inputField    | the ID of an input field to store the date
 *   displayArea   | the ID of a DIV or other element to show the date
 *   button        | ID of a button or other element that will trigger the calendar
 *   eventName     | event that will trigger the calendar, without the "on" prefix (default: "click")
 *   ifFormat      | date format that will be stored in the input field
 *   daFormat      | the date format that will be used to display the date in displayArea
 *   singleClick   | (true/false) wether the calendar is in single click mode or not (default: true)
 *   firstDay      | numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
 *   align         | alignment (default: "Br"); if you don't know what's this see the calendar documentation
 *   range         | array with 2 elements.  Default: [1900, 2999] -- the range of years available
 *   weekNumbers   | (true/false) if it's true (default) the calendar will display week numbers
 *   flat          | null or element ID; if not null the calendar will be a flat calendar having the parent with the given ID
 *   flatCallback  | function that receives a JS Date object and returns an URL to point the browser to (for flat calendar)
 *   disableFunc   | function that receives a JS Date object and should return true if that date has to be disabled in the calendar
 *   onSelect      | function that gets called when a date is selected.  You don't _have_ to supply this (the default is generally okay)
 *   onClose       | function that gets called when the calendar is closed.  [default]
 *   onUpdate      | function that gets called after the date is updated in the input field.  Receives a reference to the calendar.
 *   date          | the date that the calendar will be initially displayed to
 *   showsTime     | default: false; if true the calendar will include a time selector
 *   timeFormat    | the time format; can be "12" or "24", default is "12"
 *   electric      | if true (default) then given fields/date areas are updated for each move; otherwise they're updated only on close
 *   step          | configures the step of the years in drop-down boxes; default: 2
 *   position      | configures the calendar absolute position; default: null
 *   cache         | if "true" (but default: "false") it will reuse the same calendar object, where possible
 *   showOthers    | if "true" (but default: "false") it will show days from other months too
 *
 *  None of them is required, they all have default values.  However, if you
 *  pass none of "inputField", "displayArea" or "button" you'll get a warning
 *  saying "nothing to setup".
 */
?>
<SCRIPT type="text/javascript">
<!--
Calendar.setup = function (params) {
  function param_default(pname, def) { if (typeof params[pname] == "undefined") { params[pname] = def; } };

  param_default("inputField",     null);
  param_default("displayArea",    null);
  param_default("button",         null);
  param_default("eventName",      "click");
  param_default("ifFormat",       "<?php echo trad("CALENDAR_DEF_DATE_FORMAT"); ?>");
  param_default("daFormat",       "<?php echo trad("CALENDAR_TT_DATE_FORMAT"); ?>");
  param_default("singleClick",    true);
  param_default("disableFunc",    null);
  param_default("dateStatusFunc", params["disableFunc"]);  // takes precedence if both are defined
  param_default("dateText",       null);
  param_default("firstDay",       "<?php echo trad("CALENDAR_FIRST_DAY"); ?>");
  param_default("align",          "Br");
  param_default("range",          [1900, 2050]);
  param_default("weekNumbers",    true);
  param_default("flat",           null);
  param_default("flatCallback",   null);
  param_default("onSelect",       null);
  param_default("onClose",        null);
  param_default("onUpdate",       null);
  param_default("date",           null);
  param_default("showsTime",      false);
  param_default("timeFormat",     "<?php echo trad("CALENDAR_TIME_FORMAT"); ?>");
  param_default("electric",       true);
  param_default("step",           1);
  param_default("position",       null);
  param_default("cache",          false);
  param_default("showOthers",     true);
  param_default("multiple",       null);

  var tmp = ["inputField", "displayArea", "button"];
  for (var i in tmp) {
    if (typeof params[tmp[i]] == "string") {
      params[tmp[i]] = document.getElementById(params[tmp[i]]);
    }
  }
  if (!(params.flat || params.multiple || params.inputField || params.displayArea || params.button)) {
    alert("Calendar.setup:\n  Nothing to setup (no fields found).  Please check your code");
    return false;
  }

  function onSelect(cal) {
    var p = cal.params;
    var update = (cal.dateClicked || p.electric);
    if (update && p.inputField) {
      p.inputField.value = cal.date.print(p.ifFormat);
      if (typeof p.inputField.onchange == "function")
        p.inputField.onchange();
    }
    if (update && p.displayArea)
      p.displayArea.innerHTML = cal.date.print(p.daFormat);
    if (update && typeof p.onUpdate == "function")
      p.onUpdate(cal);
    if (update && p.flat) {
      if (typeof p.flatCallback == "function")
        p.flatCallback(cal);
    }
    if (update && p.singleClick && cal.dateClicked)
      cal.callCloseHandler();
  };

  if (params.flat != null) {
    if (typeof params.flat == "string")
      params.flat = document.getElementById(params.flat);
    if (!params.flat) {
      alert("Calendar.setup:\n  Flat specified but can't find parent.");
      return false;
    }
    var cal = new Calendar(params.firstDay, params.date, params.onSelect || onSelect);
    cal.showsOtherMonths = params.showOthers;
    cal.showsTime = params.showsTime;
    cal.time24 = (params.timeFormat == "24");
    cal.params = params;
    cal.weekNumbers = params.weekNumbers;
    cal.setRange(params.range[0], params.range[1]);
    cal.setDateStatusHandler(params.dateStatusFunc);
    cal.getDateText = params.dateText;
    if (params.ifFormat) {
      cal.setDateFormat(params.ifFormat);
    }
    if (params.inputField && typeof params.inputField.value == "string") {
      cal.parseDate(params.inputField.value);
    }
    cal.create(params.flat);
    cal.show();
    return false;
  }

  var triggerEl = params.button || params.displayArea || params.inputField;
  triggerEl["on" + params.eventName] = function() {
    var dateEl = params.inputField || params.displayArea;
    var dateFmt = params.inputField ? params.ifFormat : params.daFormat;
    var mustCreate = false;
    var cal = window.calendar;
    if (dateEl)
      params.date = Date.parseDate(dateEl.value || dateEl.innerHTML, dateFmt);
    if (!(cal && params.cache)) {
      window.calendar = cal = new Calendar(params.firstDay,
                   params.date,
                   params.onSelect || onSelect,
                   params.onClose || function(cal) { cal.hide(); });
      cal.showsTime = params.showsTime;
      cal.time24 = (params.timeFormat == "24");
      cal.weekNumbers = params.weekNumbers;
      mustCreate = true;
    } else {
      if (params.date)
        cal.setDate(params.date);
      cal.hide();
    }
    if (params.multiple) {
      cal.multiple = {};
      for (var i = params.multiple.length; --i >= 0;) {
        var d = params.multiple[i];
        var ds = d.print("%Y%m%d");
        cal.multiple[ds] = d;
      }
    }
    cal.showsOtherMonths = params.showOthers;
    cal.yearStep = params.step;
    cal.setRange(params.range[0], params.range[1]);
    cal.params = params;
    cal.setDateStatusHandler(params.dateStatusFunc);
    cal.getDateText = params.dateText;
    cal.setDateFormat(dateFmt);
    if (mustCreate)
      cal.create();
    cal.refresh();
    if (!params.position)
      cal.showAtElement(params.button || params.displayArea || params.inputField, params.align);
    else
      cal.showAt(params.position[0], params.position[1]);
    return false;
  };

  return cal;
};

// full day names
Calendar._DN = new Array
("<?php echo trad("COMMUN_DIMANCHE"); ?>",
 "<?php echo trad("COMMUN_LUNDI"); ?>",
 "<?php echo trad("COMMUN_MARDI"); ?>",
 "<?php echo trad("COMMUN_MERCREDI"); ?>",
 "<?php echo trad("COMMUN_JEUDI"); ?>",
 "<?php echo trad("COMMUN_VENDREDI"); ?>",
 "<?php echo trad("COMMUN_SAMEDI"); ?>",
 "<?php echo trad("COMMUN_DIMANCHE"); ?>");
// short day names
Calendar._SDN = new Array
("<?php echo trad("COMMUN_DIM"); ?>",
 "<?php echo trad("COMMUN_LUN"); ?>",
 "<?php echo trad("COMMUN_MAR"); ?>",
 "<?php echo trad("COMMUN_MER"); ?>",
 "<?php echo trad("COMMUN_JEU"); ?>",
 "<?php echo trad("COMMUN_VEN"); ?>",
 "<?php echo trad("COMMUN_SAM"); ?>",
 "<?php echo trad("COMMUN_DIM"); ?>");
// full month names
Calendar._MN = new Array
("<?php echo trad("COMMUN_JANVIER"); ?>",
 "<?php echo trad("COMMUN_FEVRIER"); ?>",
 "<?php echo trad("COMMUN_MARS"); ?>",
 "<?php echo trad("COMMUN_AVRIL"); ?>",
 "<?php echo trad("COMMUN_MAI"); ?>",
 "<?php echo trad("COMMUN_JUIN"); ?>",
 "<?php echo trad("COMMUN_JUILLET"); ?>",
 "<?php echo trad("COMMUN_AOUT"); ?>",
 "<?php echo trad("COMMUN_SEPTEMBRE"); ?>",
 "<?php echo trad("COMMUN_OCTOBRE"); ?>",
 "<?php echo trad("COMMUN_NOVEMBRE"); ?>",
 "<?php echo trad("COMMUN_DECEMBRE"); ?>");
// short month names
Calendar._SMN = new Array
("<?php echo trad("COMMUN_JANVIER2"); ?>",
 "<?php echo trad("COMMUN_FEVRIER2"); ?>",
 "<?php echo trad("COMMUN_MARS2"); ?>",
 "<?php echo trad("COMMUN_AVRIL2"); ?>",
 "<?php echo trad("COMMUN_MAI2"); ?>",
 "<?php echo trad("COMMUN_JUIN2"); ?>",
 "<?php echo trad("COMMUN_JUILLET2"); ?>",
 "<?php echo trad("COMMUN_AOUT2"); ?>",
 "<?php echo trad("COMMUN_SEPTEMBRE2"); ?>",
 "<?php echo trad("COMMUN_OCTOBRE2"); ?>",
 "<?php echo trad("COMMUN_NOVEMBRE2"); ?>",
 "<?php echo trad("COMMUN_DECEMBRE2"); ?>");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = <?php echo trad("CALENDAR_FIRST_DAY"); ?>;

// tooltips
Calendar._TT = {};

Calendar._TT["INFO"] = "<?php echo trad("CALENDAR_INFO"); ?>";

Calendar._TT["ABOUT"] =
"<?php echo trad("CALENDAR_ABOUT_1"); ?>\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this ;-)
"<?php echo trad("CALENDAR_ABOUT_2"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_3"); ?>" +
"\n\n" +
"<?php echo trad("CALENDAR_ABOUT_4"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_5"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_6"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_7"); ?>";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"<?php echo trad("CALENDAR_ABOUT_TIME_1"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_TIME_2"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_TIME_3"); ?>\n" +
"<?php echo trad("CALENDAR_ABOUT_TIME_4"); ?>";

Calendar._TT["PREV_YEAR"] = "<?php echo trad("CALENDAR_PREV_YEAR"); ?>";
Calendar._TT["PREV_MONTH"] = "<?php echo trad("CALENDAR_PREV_MONTH"); ?>";
Calendar._TT["GO_TODAY"] = "<?php echo trad("CALENDAR_GO_TODAY"); ?>";
Calendar._TT["NEXT_MONTH"] = "<?php echo trad("CALENDAR_NEXT_MONTH"); ?>";
Calendar._TT["NEXT_YEAR"] = "<?php echo trad("CALENDAR_NEXT_YEAR"); ?>";
Calendar._TT["SEL_DATE"] = "<?php echo trad("CALENDAR_SEL_DATE"); ?>";
Calendar._TT["DRAG_TO_MOVE"] = "<?php echo trad("CALENDAR_DRAG_TO_MOVE"); ?>";
Calendar._TT["PART_TODAY"] = "<?php echo trad("CALENDAR_PART_TODAY"); ?>";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "<?php echo trad("CALENDAR_DAY_FIRST"); ?>";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
<?php
  //Recherche de la semaine type de l'utilisateur
  $found = false;
  $WeekEnd = "";
  for ($i=0; $i<7 && !empty($SEMAINE_TYPE); $i++)
    if ($SEMAINE_TYPE[$i] == 0) {
      $found = true;
      $WeekEnd .= ((!empty($WeekEnd) ? "," : "")).(($i!=6) ? $i+1 : 0);
    }
  if (!$found)
    $WeekEnd = trad("CALENDAR_WEEKEND");
?>
Calendar._TT["WEEKEND"] = "<?php echo $WeekEnd; ?>";

Calendar._TT["CLOSE"] = "<?php echo trad("CALENDAR_CLOSE"); ?>";
Calendar._TT["TODAY"] = "<?php echo trad("CALENDAR_TODAY"); ?>";
Calendar._TT["TIME_PART"] = "<?php echo trad("CALENDAR_TIME_PART"); ?>";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "<?php echo trad("CALENDAR_DEF_DATE_FORMAT"); ?>";
Calendar._TT["TT_DATE_FORMAT"] = "<?php echo trad("CALENDAR_TT_DATE_FORMAT"); ?>";

Calendar._TT["WK"] = "<?php echo trad("CALENDAR_WK"); ?>";
Calendar._TT["TIME"] = "<?php echo trad("CALENDAR_TIME"); ?>";
//-->
</SCRIPT>
