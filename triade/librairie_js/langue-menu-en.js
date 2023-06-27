/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
// ne pas dépaser 24 caractères
langtitre0="Helpdesk"; // Assistance
langtitre1="News";
langtitre2="Triade Service";
langtitre3="Forum";
langtitre4="Search";
langtitre5="Quit";
langtitre6="Helpdesk"; // Assistance
//-------
langmenuparent1="Message";
langmenuparent11="Send message";
//langmenuparent12="Consult message"; sam le 06/06/2014
langmenuparent12="Inbox";
langmenuparent13="Main Teacher Messages";
//-------
langmenuparent2="School life";
langmenuparent21="grades";
langmenuparent22="Delays"; // sam le 15/09/2014
langmenuparent23="Absences";
langmenuparent24="Exemption";
langmenuparent25="Discipline";
langmenuparent26="homework";
langmenuparent27="Agenda notebook";
//-------
langmenuparent3="Administration";
langmenuparent31="Schedule";
langmenuparent32="Administration memos";
langmenuparent33="representative";
langmenuparent34="Calendar";
langmenuparent35="In class test";
//------
// #########################
langmenuprof1="Message center";
langmenuprof11="Send message";
//langmenuprof12="Consult message"; sam le 06/06/2014
langmenuprof12="Inbox";
langmenuprof13="Delete Items";
//------
langmenuprof2="grades";
langmenuprof21="Add grades";
langmenuprof22="show grades";
langmenuprof23="Change grades";
langmenuprof24="show assignments";
langmenuprof25="Delete assignments";
langmenuprof26="Assignment";
langmenuprof27="Homework diary";
//------
langmenuprof3="Students";
//langmenuprof30="Students tardy"; sam 15/09/2014
langmenuprof30="Students late";
langmenuprof32="Students files";
//------
langmenuprof4="Administration";
langmenuprof40="Room reservation";
langmenuprof41="Schedule";
langmenuprof42="administrative memos";
langmenuprof43="Calendar";
langmenuprof44="class test";
//------
// #########################
langmenuadmin00="Agenda";
langmenuadmin0="Message center";
langmenuadmin01="Announcement";
langmenuadmin02="Send message";
//langmenuadmin03="Consult message"; sam le 06/06/2014
//langmenuadmin03="Read message";
langmenuadmin03="InBox";
langmenuadmin04="Sent Items";
langmenuadmin05="Audio Message";
//------ 
//langmenuadmin11="Direction"; 31/07/2014
langmenuadmin11="School Management";
langmenuadmin12="School life";
// langmenuadmin13="Teacher"; le 17/06/2014
langmenuadmin13="Professors";
langmenuadmin14="Substitute";
langmenuadmin15="Student";
langmenuadmin16="Group";
langmenuadmin17="Class";
langmenuadmin18="Course";
langmenuadmin19="sub course";
//------
langmenuadmin2="Delete";
//------
langmenuadmin3="Affectaction";
langmenuadmin31="Schedule";
langmenuadmin33="Setup";
langmenuadmin34="Show";
langmenuadmin35="Change";
langmenuadmin36="Delete";
langmenuadmin37="Edit";
//------
langmenuadmin4="School";
langmenuadmin40="Room reservation";
langmenuadmin41="Lunch";
langmenuadmin42="boarding school";
langmenuadmin43="Library";
//------
langmenuadmin5="Student";
langmenuadmin51="Search";
//langmenuadmin52="Consult"; Traduit par Sam le 03/06/2014
langmenuadmin52="Class List";
langmenuadmin53="Manage diplomas";
langmenuadmin54="Manage groups";
langmenuadmin55="Grades book";
langmenuadmin56="Medicals";
langmenuadmin57="Manage Class Test.";
langmenuadmin58="Manage planning";
langmenuadmin59="Today's Attendance";
langmenuadmin510="Manage Attendance";
langmenuadmin511="Manage class exemption";
langmenuadmin512="Retenues du jour";
langmenuadmin513="Manage discipline";
//langmenuadmin514="Manage Memos"; Traduit par Sam
langmenuadmin514="Memos MGT";
langmenuadmin515="Manage S.M.S.";
langmenuadmin516="Manage W.A.P.";
//langmenuadmin517="Manage training"; Traduit par Sam
langmenuadmin517="Training MGT";
langmenuadmin518="ID Picture";
//langmenuadmin519="Manage Barcode"; Traduit par Sam
langmenuadmin519="Barcode MGT";
// --------------
langmenuadmin6="Report card";
//langmenuadmin61="Configuration";
langmenuadmin61="Settings";
//langmenuadmin62="Define trimester periods"; sam le 090/6/2014
langmenuadmin62="Define Quarter periods";
langmenuadmin63="Print report card";
langmenuadmin64="Print periods";
langmenuadmin65="Member personal infos";
// --------------
langmenuadmin7="Manage banners";
langmenuadmin71="Add banner";
langmenuadmin72="Show";
// --------------
langmenuadmin81="Import";
//langmenuadmin81="Download";
langmenuadmin82="Refresh";
langmenuadmin83="No class student";
langmenuadmin84="Archive";
langmenuadmin85="Verify";
// --------------
langmenuadmin9="New Year";
langmenuadmin91="Class change";
langmenuadmin92="Purge database";
// --------------
// #########################
langmenuscolaire0="School life";
langmenuscolaire01="Today's Attendance";
langmenuscolaire02="Manage Attendance";
langmenuscolaire03="Manage exemptions";
langmenuscolaire04="Retenues du jour";
langmenuscolaire05="Manage discipline";
// --------------
langmenuscolaire1="Administration";
langmenuscolaire11="Students List";
langmenuscolaire12="Schedule";
langmenuscolaire13="Search student";
langmenuscolaire14="Manage calendar";
//langmenuscolaire15="Manage Memos"; Traduit par Sam
langmenuscolaire15="Memos MGT";
//langmenuscolaire16="Manage Class tests";
langmenuscolaire16="Test Planning";
// #########################
// pied de page
if (footer != "") { 
	if (footerlien != "") {
		langmenupied="<a href='"+footerlien+"' target='_blank'>"+footer+"</a>"; 
	}else{
		langmenupied=footer; 
	}
	langmenupied+="<br>Optimized for :  minimum resolution : 800x600  <br>"; 
}else{
	langmenupied="<br><p>La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>Optimized for :  minimum resolution : 800x600 <br> T.R.I.A.D.E. © - 2024 - Tous droits réservés";
}


// --------------
// #########################

langmenuscolaire04="Today's detention";
langmenuadmin38="Resp. Level";
langmenuprof28="trimester Report Card";
langtitre7="Administrator&nbsp;Triade"
//langmenuadmin1="Gestion"; 31/07/2014
langmenuadmin1="Administration";
langmenueleve1="Pedagogy";
langmenueleve2="Lesson";
langmenueleve3="Exercises";

langmenuadmin8="School";
langmenuadmin06="Storage";
langmenuadmin07="My space"
langmenuadmin66="Print table";

img_logo_pied="<img src='./image/commun/triade-xhtml.jpg' alt='XHTML'>  <img src='./image/commun/triade-w3C.jpg' alt='w3C'> <img src='./image/commun/triade-css.png' alt='css' > <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='./image/commun/triade_paypal.png' alt='Paypal' ></a><br /><br />";

langmenuadmin521="My RSS feeds";
langmenuadmin522="SMS Message";
langmenuadmin67="Valid. report card";
langmenuadmin68="Principal's supervision";

langmenuadmin69="Visa school life";
langmenuadmin523="Todays sanctions";
langmenuadmin520="Manage study Hall";
langmenuprof45="Sanctions students";
langmenuadmin44="History cmd";
langmenuadmin45="Diaporama";
langmenuadmin46="Settings";
langmenuadmin38="Config grade USA";

if (VATEL == "1") {
	langmenuadmin70="Diploma supplement";
}else{
	langmenuadmin70="Manage Test";
}

langtitre2bis="New window";
langmenuadmin73="School life note";
langmenuprof29="Educational Institutes";
langmenuadmin47="Other Modules";


langmenuadmin48="Student Report";
langmenuprof31="H.teacher / teacher";
langmenuadmin32="H.teacher / teacher";
langmenuadmin20="Internship mentor";
langmenuadmin40bis="Equip. Reservation";
// non traduit
langmenuadmin41bis="Gestion délégués";
langmenuparent36="Délégués";
langmenuprof46="Plan de classe";
langmenuadmin524="Notanet";
langmenuadmin39="Personal interview";
langmenuadmin525="Vacation profs.";
langmenuadmin102="Preliminary registration";
langmenuadmin101="House Rules";
langmenuprof47="Stage Pro.";
langmenuadmin90="Comptabilité";
langmenuadmin91bis="Fiche d'état";
langmenuadmin92bis="Versement";
langmenuadmin93="Rappel";
langmenuadmin94="Config. Echéancier";
langmenuadmin95="Quantification";
langmenuadmin96="Export"; // sam
langmenuadmin97="Signing Sheet";
langmenuadmin98="Course Units";
langmenuadmin99="New Year";
langmenuadmin100="Intra-MSN";
langmenuadmin103="E-Learning";
langmenuadmin525bis="Etat Versement";
langmenuadmin104="Staff Member";
langmenueleve517="Stage Pro."; 
langmenueleve518="Mailing";
langmenuadmin01A="News 1st Page";
langmenuadmin01B="School News";
langmenuadmin01C="News défilant";
langmenupersonnel1="Accès Modules";
langmenupersonnel2="Cantine";
langmenuadmin399="Entretien Enseignant";
langmenuadmin531="Centrale Stages";


//****** APRES_MAJ_TRIADE_AUTO - 20100826232122 - IGONE : CODE AJOUTE AUTOMATIQUEMENT PAR SCRIPT 'admin_apres_maj_triade' ******
// -------------- MODULE FINANCIER
langmenuadmin9000="Financial module";
langmenuadmin9001="Inscription";
langmenuadmin9002="Settings";
langmenuadmin9003="Payments";
langmenuadmin9004="Editions";

// -------------- MODULE CHAMBRES
langmenuadmin9100="Rooms module";
langmenuadmin9101="Planning";
langmenuadmin9102="Booking";
langmenuadmin9103="Settings";
//***************************************************************************
//langmenuadmin022="Brouillon"; traduit par Sam
langmenuadmin022="Drafts";
//langmenuadmin055="News Vidéo"; sam le 06/06/2014
langmenuadmin055="Video News";
langmenuadmin1011="Boursier";
langmenuadmin526="Fourniture scolaire";
langmenuadmin527="Gestion ressources";
//langmenugeneral01="Votre compte"; Traduit par Sam
langmenugeneral01="My Account";
langmenuparent34bis="Planning";
langmenuadmin528="Organigramme";
langmenuadmin529="Année Scolaire";
langmenuadmin911="Classe antérieure";

langmenuadmin912="Savoir-être";
