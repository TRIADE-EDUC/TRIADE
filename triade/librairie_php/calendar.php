<?php
//print "cal18.setDisabledWeekDays(0,1,2,3,4,5);";

if (file_exists("../common/config2.inc.php")) { include_once("../common/config2.inc.php"); }
if (file_exists("./common/config2.inc.php")) { include_once("./common/config2.inc.php"); }

function calendarpopup($iddiv,$form,$langue,$format) {
        // $form  = document.formulaire.saisie_date_naissance
        // $iddiv nom de la div
        // format 1 == avec bouton select
        print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
        print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
        print "<!-- ================================================================================== -->\n";
        print "<script type='text/javascript' id='js18'>\n";
	print "var cal18 = new CalendarPopup();\n";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}

	if ($semainedim != "oui") { print "cal18.setDisabledWeekDays(0);"; }
	print "cal18.setCssPrefix('TEST');";
        if ($langue == "fr") {
                print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
                print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
                print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
        }
        if ($format == "1") {
                print "cal18.showNavigationDropdowns();\n";
                print "cal18.setYearSelectStartOffset(15);\n";
        }
        print "</script>\n";
        print "<A HREF='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
        print "<!-- ================================================================================== -->\n";
}

function calendarpopupDim($iddiv,$form,$langue,$format,$dim) {
        // $form  = document.formulaire.saisie_date_naissance
        // $iddiv nom de la div
        // format 1 == avec bouton select
        print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
        print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
        print "<!-- ================================================================================== -->\n";
        print "<script type='text/javascript' id='js18'>\n";
	print "var cal18 = new CalendarPopup();\n";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}

	if (($dim == 1) && ($semainedim != "oui")){
		print "cal18.setDisabledWeekDays(0);";
	}
	print "cal18.setCssPrefix('TEST');";
        if ($langue == "fr") {
                print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
                print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
                print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
        }
        if ($format == "1") {
                print "cal18.showYearNavigation();\n";
                print "cal18.showYearNavigationInput(25);\n";
        }
        print "</script>\n";
        print "<A HREF='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
        print "<!-- ================================================================================== -->\n";
}

function calendarpopupCalend($iddiv,$form,$langue,$format) {
        // $form  = document.formulaire.saisie_date_naissance
        // $iddiv nom de la div
        // format 1 == avec bouton select
        print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
        print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
        print "<!-- ================================================================================== -->\n";
        print "<script type='text/javascript' id='js18'>\n";
	print "var cal18 = new CalendarPopup();\n";
	print "cal18.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}

	if ($semainedim != "oui") { print "cal18.setDisabledWeekDays(0);"; }
        if ($langue == "fr") {
                print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
                print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
                print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
        }
        if ($format == "1") {
                print "cal18.showNavigationDropdowns();\n";
                print "cal18.setYearSelectStartOffset(15);\n";
        }
        print "</script>\n";
        print "<A HREF='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
        print "<!-- ================================================================================== -->\n";
}


function calendarpopupCalendParent($iddiv,$form,$langue,$format) {
        // $form  = document.formulaire.saisie_date_naissance
        // $iddiv nom de la div
        // format 1 == avec bouton select
        print "<script type='text/javascript' src='../librairie_js/CalendarPopup.js'></script>\n";
        print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
        print "<!-- ================================================================================== -->\n";
        print "<script type='text/javascript' id='js18'>\n";
        print "var cal18 = new CalendarPopup();\n";
        print "cal18.setCssPrefix('TEST');";
        if (defined("SEMAINEDIMANCHE")) {
                $semainedim=SEMAINEDIMANCHE;
        }else{
                $semainedim="non";
        }

        if ($semainedim != "oui") { print "cal18.setDisabledWeekDays(0);"; }
        if ($langue == "fr") {
                print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
                print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
                print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
        }
        if ($format == "1") {
                print "cal18.showNavigationDropdowns();\n";
                print "cal18.setYearSelectStartOffset(15);\n";
        }
        print "</script>\n";
        print "<A HREF='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='../image/commun/calendar.gif' border='0' align='center'></A>\n";
        print "<!-- ================================================================================== -->\n";
}



function calendar($iddiv,$form,$langue,$format) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal18 = new CalendarPopup('$iddiv');\n";
	print "cal18.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}
	if ($semainedim != "oui") { print "cal18.setDisabledWeekDays(0);"; }
	if ($langue == "fr") {
		print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
	}
	if ($format == "1") {
	//	print "cal18.showNavigationDropdowns();\n";
	//	print "cal18.setYearSelectStartOffset(50);\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;z-index:100'></div>\n";
}

function calendarVatel($iddiv,$form,$langue,$format) {
        // $form  = document.formulaire.saisie_date_naissance
        // $iddiv nom de la div
        // format 1 == avec bouton select
        print "<script type='text/javascript' src='../librairie_js/CalendarPopup.js'></script>\n";
        print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
        print "<!-- ================================================================================== -->\n";
        print "<script type='text/javascript' id='js18'>\n";
		print "var cal18 = new CalendarPopup();\n";
		print "cal18.setCssPrefix('TEST');";
		if (defined("SEMAINEDIMANCHE")) {
			$semainedim=SEMAINEDIMANCHE;
		}else{
			$semainedim="non";
		}

		if ($semainedim != "oui") { print "cal18.setDisabledWeekDays(0);"; }
		if ($langue == "fr") {
				print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
				print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
				print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
		}
		if ($format == "1") {
				print "cal18.showNavigationDropdowns();\n";
				print "cal18.setYearSelectStartOffset(15);\n";
		}
		print "</script>\n";
		print "<A HREF='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='../image/commun/calendar.gif' border='0' align='center'></A>\n";
		print "<!-- ================================================================================== -->\n";
}


function calendarDim($iddiv,$form,$langue,$format,$dim=0) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal18 = new CalendarPopup('$iddiv');\n";
	print "cal18.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}
	if (($dim == 1)  && ($semainedim != "oui"))  { print "cal18.setDisabledWeekDays(0);"; }

	if ($langue == "fr") {
		print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
	}
	if ($format == "1") {
	//	print "cal18.showNavigationDropdowns();\n";
	//	print "cal18.setYearSelectStartOffset(50);\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;  \"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}


function calendarEDT($iddiv,$form,$langue,$format,$dim) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal18 = new CalendarPopup('$iddiv');\n";
	print "cal18.setCssPrefix('TEST');";
	print "cal18.setWeekStartDay(1);";
	print "cal18.setDisabledWeekDays(1,2,3,4,5);";

	if ($langue == "fr") {
		print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
	}

	print "</script>\n";
	print "<a href='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}



function calendarSemaine($iddiv,$form,$langue,$format,$dim) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal18 = new CalendarPopup('$iddiv');\n";
	print "cal18.setCssPrefix('TEST');";
	print "cal18.setWeekStartDay(1);";
	print "cal18.setDisabledWeekDays(1,2,3,4,5,6);";

	if ($langue == "fr") {
		print "cal18.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal18.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal18.setTodayText(\"".LANGAGENDA39."\");\n";
	}

	print "</script>\n";
	print "<a href='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}



function calendarMoiAnnee($iddiv,$form,$langue,$dim) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal$iddiv = new CalendarPopup('$iddiv');\n";
//	print "cal$iddiv.showNavigationDropdowns();\n";
//print "cal$iddiv.setYearSelectStartOffset(50);\n";
	print "cal$iddiv.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}

	if (($dim == 1) && ($semainedim != "oui")){ 
		print "cal$iddiv.setDisabledWeekDays(0);"; 
	}
	if ($langue == "fr") {
		print "cal$iddiv.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal$iddiv.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal$iddiv.setTodayText(\"".LANGAGENDA39."\");\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal$iddiv.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}


function calendarMoiAnnee2($iddiv,$form,$langue,$dim,$nbannee) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal$iddiv = new CalendarPopup('$iddiv');\n";
	print "cal$iddiv.showNavigationDropdowns();\n";
	print "cal$iddiv.setYearSelectStartOffset($nbannee);\n";
	print "cal$iddiv.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}

	if (($dim == 1) && ($semainedim != "oui")){ 
		print "cal$iddiv.setDisabledWeekDays(0);"; 
	}
	if ($langue == "fr") {
		print "cal$iddiv.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal$iddiv.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal$iddiv.setTodayText(\"".LANGAGENDA39."\");\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal$iddiv.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}

function calendarMoiAnnee2NonIE($iddiv,$form,$langue,$dim,$nbannee) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal$iddiv = new CalendarPopup();\n";
	print "cal$iddiv.showNavigationDropdowns();\n";
	print "cal$iddiv.setYearSelectStartOffset($nbannee);\n";
	print "cal$iddiv.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}

	if (($dim == 1) && ($semainedim != "oui")){ 
		print "cal$iddiv.setDisabledWeekDays(0);"; 
	}
	if ($langue == "fr") {
		print "cal$iddiv.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal$iddiv.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal$iddiv.setTodayText(\"".LANGAGENDA39."\");\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal$iddiv.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}

function calendarSupp($iddiv,$form,$langue,$format,$dateSupp) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	print "<script type='text/javascript' src='./librairie_js/CalendarPopup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal$iddiv = new CalendarPopup('$iddiv');\n";
	print "cal$iddiv.setCssPrefix('TEST');";
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}
	if ($semainedim != "oui") { print "cal$iddiv.setDisabledWeekDays(0);"; }


	foreach($dateSupp as $key=>$value) {
		print "cal$iddiv.addDisabledDates(\"$key\",\"$value\");";
	}
	if ($langue == "fr") {
		print "cal$iddiv.setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal$iddiv.setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal$iddiv.setTodayText(\"".LANGAGENDA39."\");\n";
	}
	if ($format == "1") {
		print "cal$iddiv.showNavigationDropdowns();\n";
		print "cal$iddiv.setYearSelectStartOffset(50);\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal$iddiv.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\"  NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></div>\n";
}

?>
