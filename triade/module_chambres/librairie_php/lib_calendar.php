<?php
//print "cal18.setDisabledWeekDays(0,1,2,3,4,5);";

if (file_exists("../common/config2.inc.php")) { include_once("../common/config2.inc.php"); }
if (file_exists("./common/config2.inc.php")) { include_once("./common/config2.inc.php"); }

function calendarpopup($iddiv,$form,$langue,$format) {
        // $form  = document.formulaire.saisie_date_naissance
        // $iddiv nom de la div
        // format 1 == avec bouton select
	global $g_chemin_relatif_module;
		print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
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
 	global $g_chemin_relatif_module;
		print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
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
		global $g_chemin_relatif_module;
		print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
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


function calendar($iddiv,$form,$langue,$format,$conteneur='') {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	global $g_chemin_relatif_module;
	print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal18 = new CalendarPopup('$iddiv','$conteneur');\n";
	print "cal18.offsetX = 0;\n";
	print "cal18.offsetY = 0;\n";
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
	print "<a href='#' onClick=\"cal18.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white; z-index: 1000;'></div>\n";
}







function calendarDim($iddiv,$form,$langue,$format,$dim,$conteneur='',$dates_interdites_debut='', $dates_interdites_fin='') {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	global $g_chemin_relatif_module;
	print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal18_" . $iddiv . " = new CalendarPopup('$iddiv','$conteneur');\n";
	//print "alert('');\n";
	//print "var cal18_" . $iddiv . " = new CalendarPopup('$iddiv','$conteneur');\n";
	//print "alert(cal18_" . $iddiv . ".c.offsetX);\n";
	print "cal18_" . $iddiv . ".offsetX = 0;\n";
	print "cal18_" . $iddiv . ".offsetY = 0;\n";
	print "cal18_" . $iddiv . ".setCssPrefix('TEST');";
	
	if($dates_interdites_debut != '' && $dates_interdites_fin != '') {
		if($dates_interdites_debut == 'null') {
			$date_debut = "null";
		} else {
			$date_debut = "'" . $dates_interdites_debut . "'";
		}
		if($dates_interdites_fin == 'null') {
			$date_fin = "null";
		} else {
			$date_fin = "'" . $dates_interdites_fin . "'";
		}
		print "cal18_" . $iddiv . ".addDisabledDates(" . $date_debut . "," . $date_fin . ");";
	}
	
	if (defined("SEMAINEDIMANCHE")) {
		$semainedim=SEMAINEDIMANCHE;
	}else{
		$semainedim="non";
	}
	if (($dim == 1)  && ($semainedim != "oui"))  { print "cal18.setDisabledWeekDays(0);"; }

	if ($langue == "fr") {
		print "cal18_" . $iddiv . ".setDayHeaders('".LANGLETTREDIMANCHE."','".LANGLETTRELUNDI."','".LANGLETTREMARDI."','".LANGLETTREMERCREDI."','".LANGLETTREJEUDI."','".LANGLETTREVENDREDI."','".LANGLETTRESAMEDI."');\n";
		print "cal18_" . $iddiv . ".setMonthNames(\"".LANGMOIS1."\",\"".LANGMOIS2."\",\"".LANGMOIS3."\",\"".LANGMOIS4."\",\"".LANGMOIS5."\",\"".LANGMOIS6."\",\"".LANGMOIS7."\",\"".LANGMOIS8."\",\"".LANGMOIS9."\",\"".LANGMOIS10."\",\"".LANGMOIS11."\",\"".LANGMOIS12."\");\n";
		print "cal18_" . $iddiv . ".setTodayText(\"".LANGAGENDA39."\");\n";
	}
	if ($format == "1") {
	//	print "cal18.showNavigationDropdowns();\n";
	//	print "cal18.setYearSelectStartOffset(50);\n";
	}
	print "</script>\n";
	print "<a href='#' onClick=\"cal18_" . $iddiv . ".select($form,'anchor18$iddiv','dd/MM/yyyy');return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:;background-color:white;layer-background-color:white;z-index:1000;'></div>\n";
}

function calendarMoiAnnee($iddiv,$form,$langue,$dim) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	// dim 1 == dimanche non click
	global $g_chemin_relatif_module;
	print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal$iddiv = new CalendarPopup('$iddiv');\n";
	print "cal18.offsetX = 0;\n";
	print "cal18.offsetY = 0;\n";
//	print "cal$iddiv.showNavigationDropdowns();\n";
//	print "cal$iddiv.setYearSelectStartOffset(50);\n";
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
	print "<a href='#' onClick=\"cal$iddiv.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;z-index: 1000;'></div>\n";
}


function calendarSupp($iddiv,$form,$langue,$format,$dateSupp) {
	// $form  = document.formulaire.saisie_date_naissance
	// $iddiv nom de la div
	// format 1 == avec bouton select
	global $g_chemin_relatif_module;
	print "<script type='text/javascript' src='./" . $g_chemin_relatif_module . "librairie_js/calendar_popup.js'></script>\n";
	print "<script type='text/javascript'>document.write(getCalendarStyles());</script>\n";
	print "<!-- ================================================================================== -->\n";
	print "<script type='text/javascript' ID='js18'>\n";
	print "var cal$iddiv = new CalendarPopup('$iddiv');\n";
	print "cal18.offsetX = 0;\n";
	print "cal18.offsetY = 0;\n";
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
	print "<a href='#' onClick=\"cal$iddiv.select($form,'anchor18$iddiv','dd/MM/yyyy'); return false;\" return false;\" NAME='anchor18$iddiv' ID='anchor18$iddiv'><img src='image/commun/calendar.gif' border='0' align='center'></A>\n";
	print "<!-- ================================================================================== -->\n";
	print "<div ID='$iddiv' STYLE='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;z-index: 1000;'></div>\n";
}

?>
