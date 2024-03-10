<?php
session_start();
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
error_reporting(0);
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="../librairie_js/info-bulle.js"></script>
<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Droit d'accès aux modules Triade</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left" ><font color="#000000">
<!-- // debut de la saisie -->
<br>
<form method='post' action='config_module2.php' >
<center><font class=T2>Autoriser ou non l'accès aux différents modules.</font></center>
<br>
<table width=100% border=0 >

<tr><td colspan=2 >&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Générale</b></font></td></tr>

<?php
include_once("../common/config-module.php");
$preinsciptionoui="";
$preinsciptionnon="";
$dispenseoui="";
$dispensenon="";
$disciplineoui="";
$disciplinenon="";
$cahierdetexteoui="";
$cahierdetextenon="";
$planclasseoui="";
$planclassenon="";
$DSToui="";
$DSTnon="";
$visudevoirprofoui="";
$visudevoirprofnon="";
$suppdevoirprofoui="";
$suppdevoirprofnon="";
$cahiertexteprofoui="";
$cahiertexteprofnon="";
$sanctionprofoui="";
$sanctionprofnon="";
$ficheeleveprofoui="";
$ficheeleveprofnon="";
$listeleveprofoui="";
$listeleveprofnon="";
$planprofoui="";
$planprofnon="";
$stageproprofoui="";
$stageproprofnon="";
$DSTProfoui="";
$DSTProfnon="";
$DOKEOSeleveoui="";
$DOKEOSelevenon="";
$DOKEOSProfoui="";
$DOKEOSProfnon="";
$parentdispenseoui="";
$parentdispensenon="";
$parentdisciplineoui="";
$parentdisciplinenon="";
$parentcahierdetexteoui="";
$parentcahierdetextenon="";
$parentplanclasseoui="";
$parentplanclassenon="";
$parentDSToui="";
$parentDSTnon="";
$parentTrombinoscopeoui="";
$parentTrombinoscopenon="";
$stageProparentoui="";
$stageProparentnon="";
$stageProeleveoui="";
$stageProelevenon="";
$stockageprofoui="";
$stockageprofnon="";
$intramsnprofoui="";
$intramsnprofnon="";
$agendaprofoui="";
$agendaprofnon="";
$fluxrssprofoui="";
$fluxrssprofnon="";
$notesprofoui="";
$notesprofnon="";
$bulletinprofoui="";
$bulletinprofnon="";
$resaprofoui="";
$resaprofnon="";
$visudevoirprofoui="";
$visudevoirprofnon="";
$informationprofoui="";
$informationprofnon="";
$calendrierprofoui="";
$calendrierprofnon="";
$stockageviescolaireoui="";
$stockageviescolairenon="";
$intramsnviescolaireoui="";
$intramsnviescolairenon="";
$agendaviescolaireoui="";
$agendaviescolairenon="";
$fluxrssviescolaireoui="";
$fluxrssviescolairenon="";
$etudeviescolaireoui="";
$etudeviescolairenon="";
$circulaireviescolaireoui="";
$circulaireviescolairenon="";
$dstviescolairenon="";
$dstviescolaireoui="";
$stageviescolaireoui="";
$stageviescolairenon="";
$visaviescolaireoui="";
$visaviescolairenon="";
$noteviescolaireoui="";
$noteviescolairenon="";
$imptableauviescolaireoui="";
$imptableauviescolairenon="";
$bulletinviescolaireoui="";
$bulletinviescolairenon="";
$periodeviescolaireoui="";
$periodeviescolairenon="";
$videoprojoviescolaireoui="";
$videoprojoviescolairenon="";
$planclasseviescolaireoui="";
$planclasseviescolairenon="";
$vacationviescolaireoui="";
$vacationviescolairenon="";
$historyviescolaireoui="";
$historyviescolairenon="";
$resaviescolaireoui="";
$resaviescolairenon="";
$exportviescolaireoui="";
$exportviescolairenon="";
$noteenseignantviascolaireoui="";
$noteenseignantviascolairenon="";

$cantineviascolaireoui="";
$cantineviascolairenon="";
$cantineprofoui="";
$cantineprofnon="";


$stockageadminoui="";
$stockageadminnon="";
$intramsnadminoui="";
$intramsnadminnon="";
$agendaadminoui="";
$agendaadminnon="";
$fluxrssadminoui="";
$fluxrssadminnon="";
$cantineadminoui="";
$cantineadminnon="";
$vacationadminoui="";
$vacationadminnon="";
$droitscolariteadminoui="";
$droitscolariteadminnon="";
$historyadminoui="";
$historyadminnon="";
$resaadminoui="";
$resaadminnon="";
$noteenseignantadminoui="";
$noteenseignantadminnon=""; 
$parentabsenceoui="";
$parentabsencenon="";
$parentretardoui="";
$parentretardnon="";

$modulechambresvateladminoui="";
$modulechambresvateladminnon="";
$chambreviescolaireoui="";
$chambreviescolairenon="";
$modulefinanciervateladminoui="";
$modulefinanciervateladminnon="";

$tuteurnoteoui="";
$tuteurnotenon="";
$tuteurdisciplineoui=""; 
$tuteurdisciplinenon=""; 
$tuteurabsoui=""; 
$tuteurabsnon=""; 
$tuteurdispenseoui=""; 
$tuteurdispensenon=""; 
$tuteurEDToui=""; 
$tuteurEDTnon=""; 
$tuteurcahierdetexteoui=""; 
$tuteurcahierdetextenon="";
$tuteurcirculaireoui="";
$tuteurcirculairenon=""; 
$tuteurcalendrieroui=""; 
$tuteurcalendriernon="";
$emargementprofoui="";
$emargementprofnon="";


$agendaparentoui="";
$stockageparentoui="";
$intramsnparentoui="";
$comptaparentoui="";
$rssparentoui="";
$cantineparentoui="";
$agendaparentnon="";
$stockageparentnon="";
$intramsnparentnon="";
$comptaparentnon="";
$rssparentnon="";
$cantineparentnon="";

$agendaeleveoui="";
$stockageeleveoui="";
$intramsneleveoui="";
$comptaeleveoui="";
$rsseleveoui="";
$cantineeleveoui="";
$agendaelevenon="";
$stockageelevenon="";
$intramsnelevenon="";
$comptaelevenon="";
$rsselevenon="";
$cantineelevenon="";

$newspageviescolaireoui="";
$newspageviescolairenon="";
$moduleboursieradminoui="";
$moduleboursieradminnon="";

$messagerieadminoui="";
$messagerieadminnon="";
$messagerieviescolaireoui="";
$messagerieviescolairenon="";
$messagerieProfoui="";
$messagerieProfnon="";
$messagerieEleveoui="";
$messagerieElevenon="";
$messagerieParentoui="";
$messagerieParentnon="";
$messagerietuteuroui="";
$messagerietuteurnon="";
$preinscriptionviescolaireoui="";
$preinscriptionviescolairenon="";



$newsviescolaireoui="";
$newsviescolairenon="";
$planningeleveoui="";
$planningelevenon="";
$planningparentoui="";
$planningparentnon="";
$planningprofoui="";
$planningprofnon="";

$moduleadmincdinon="";
$moduleadmincdioui="";
$moduleadminnotanetnon="";
$moduleadminnotanetoui="";
$moduleadmingestionsmsnon="";
$moduleadmingestionsmsoui="";
$moduleadminreservequipnon="";
$moduleadminreservequipoui="";
$moduleadminreservsallenon="";
$moduleadminreservsalleoui="";
$moduleadminfourniturenon="";
$moduleadminfournitureoui="";
$moduleadminexambrevetnon="";
$moduleadminexambrevetoui="";
$moduleadmingestionetudenon="";
$moduleadmingestionetudeoui="";
$moduleadmingestiondisciplinenon="";
$moduleadmingestiondisciplineoui="";
$moduleadminretenudjnon="";
$moduleadminretenudjoui="";
$moduleadminsanctiondujournon="";
$moduleadminsanctiondujouroui="";
$moduleadmingestiondispensenon="";
$moduleadmingestiondispenseoui="";
$moduleadmindosmedicalnon="";
$moduleadmindosmedicaloui="";
$moduleadminplanclassenon="";
$moduleadminplanclasseoui="";
$moduleadmingestiondeleguenon="";
$moduleadmingestiondelegueoui="";
$moduleadminsousmatierenon="";
$moduleadminsousmatiereoui="";
$moduleadminsuppleantnon="";
$moduleadminsuppleantoui="";
$moduleprofPoui="";
$moduleprofPnon="";
$moduleconfignoteusaoui="";
$moduleconfignoteusanon="";
$moduleentretienindividueloui="";
$moduleentretienindividuelnon="";
$modulecarnetsuivioui="";
$modulecarnetsuivinon="";
$moduleverifbulletinoui="";
$moduleverifbulletinnon="";
$modulenoteviescolaireoui="";
$modulenoteviescolairenon="";
$moduleadminimprperiodeoui="";
$moduleadminimprperiodenon="";
$moduleadminabsrtdoui="";
$moduleadminabsrtdnon="";
$moduleadminnouvelleanneeoui="";
$moduleadminnouvelleanneenon="";
$moduleadminarchivageoui="";
$moduleadminarchivagenon="";
$moduleadmindeleguesoui="";
$moduleadmindeleguesnon="";
$moduleadminnewsdefilantoui="";
$moduleadminnewsdefilantnon="";
$moduleadminpurgerinfooui="";
$moduleadminpurgerinfonon="";

$moduleadmingestionsavoiretreoui="";
$moduleadmingestionsavoiretrenon="";
$moduleprofgestionsavoiretreoui="";
$moduleprofgestionsavoiretrenon="";
$moduleparentgestionsavoiretreoui="";
$moduleparentgestionsavoiretrenon="";
$moduleelevegestionsavoiretreoui="";
$moduleelevegestionsavoiretrenon="";
$modulebulletinvisuparentoui="";
$modulebulletinvisuparentnon="";
$modulebulletinvisueleveoui="";
$modulebulletinvisuelevenon="";

$moduleviescolairesavoiretreoui="";
$moduleviescolairesavoiretrenon="";
$moduletuteursavoiretreoui="";
$moduletuteursavoiretrenon="";

$cahierdetexteviescolaireoui="";
$cahierdetexteviescolairenon="";
$modulebulletinvisututeurstageoui="";
$modulebulletinvisututeurstagenon="";
$moduleadminevalensoui="";
$moduleadminevalensnon="";


if (VIESCOLAIRECAHIERDETEXTE == "oui") { $cahierdetexteviescolaireoui="checked"; }
if (VIESCOLAIRECAHIERDETEXTE == "non") { $cahierdetexteviescolairenon="checked"; }

if (PREINSCRIPTION == "oui") { $preinsciptionoui="checked"; }
if (PREINSCRIPTION == "non") { $preinsciptionnon="checked"; }
if (DISPENSE == "oui") { $dispenseoui="checked"; }
if (DISPENSE == "non") { $dispensenon="checked"; }
if (DISCIPLINE == "oui") { $disciplineoui="checked"; }
if (DISCIPLINE == "non") { $disciplinenon="checked"; }
if (CAHIERDETEXTE == "oui") { $cahierdetexteoui="checked"; }
if (CAHIERDETEXTE == "non") { $cahierdetextenon="checked"; }
if (PLANCLASSE == "oui") { $planclasseoui="checked"; }
if (PLANCLASSE == "non") { $planclassenon="checked"; }
if (DST == "oui") { $DSToui="checked"; }
if (DST == "non") { $DSTnon="checked"; }

if (PARENTDISPENSE == "oui") { $parentdispenseoui="checked"; }
if (PARENTDISPENSE == "non") { $parentdispensenon="checked"; }
if (PARENTDISCIPLINE == "oui") { $parentdisciplineoui="checked"; }
if (PARENTDISCIPLINE == "non") { $parentdisciplinenon="checked"; }
if (PARENTCAHIERDETEXTE == "oui") { $parentcahierdetexteoui="checked"; }
if (PARENTCAHIERDETEXTE == "non") { $parentcahierdetextenon="checked"; }
if (PARENTPLANCLASSE == "oui") { $parentplanclasseoui="checked"; }
if (PARENTPLANCLASSE == "non") { $parentplanclassenon="checked"; }
if (PARENTDST == "oui") { $parentDSToui="checked"; }
if (PARENTDST == "non") { $parentDSTnon="checked"; }

if (VISUDEVOIRPROF == "oui") { $visudevoirprofoui="checked"; }
if (VISUDEVOIRPROF == "non") { $visudevoirprofnon="checked"; }
if (SUPPDEVOIRPROF == "oui") { $suppdevoirprofoui="checked"; }
if (SUPPDEVOIRPROF == "non") { $suppdevoirprofnon="checked"; }
if (CAHIERTEXTPROF == "oui") { $cahiertexteprofoui="checked"; }
if (CAHIERTEXTPROF == "non") { $cahiertexteprofnon="checked"; }
if (SANCTIONPROF == "oui") { $sanctionprofoui="checked"; }
if (SANCTIONPROF == "non") { $sanctionprofnon="checked"; }
if (FICHEELEVEPROF == "oui") { $ficheeleveprofoui="checked"; }
if (FICHEELEVEPROF == "non") { $ficheeleveprofnon="checked"; }
if (LISTEELEVEPROF == "oui") { $listeleveprofoui="checked"; }
if (LISTEELEVEPROF == "non") { $listeleveprofnon="checked"; }
if (PLANPROF == "oui") { $planprofoui="checked"; }
if (PLANPROF == "non") { $planprofnon="checked"; }
if (STAGEPROPROF == "oui") { $stageproprofoui="checked"; }
if (STAGEPROPROF == "non") { $stageproprofnon="checked"; }
if (DSTPROFACCES == "oui") { $DSTProfoui="checked"; }
if (DSTPROFACCES == "non") { $DSTProfnon="checked"; }
if (DOKEOSPROF == "oui") { $DOKEOSProfoui="checked"; }
if (DOKEOSPROF == "non") { $DOKEOSProfnon="checked"; }
if (DOKEOSELEVE == "oui") { $DOKEOSeleveoui="checked"; }
if (DOKEOSELEVE == "non") { $DOKEOSelevenon="checked"; }
if (COMPTAPROF == "oui") { $comptaProfoui="checked"; }
if (COMPTAPROF == "non") { $comptaProfnon="checked"; }
if (PARENTTROMBINOSCOPE == "oui") { $parentTrombinoscopeoui="checked"; }
if (PARENTTROMBINOSCOPE == "non") { $parentTrombinoscopenon="checked"; }
if (STAGEPROELEVE == "oui") { $stageProeleveoui="checked"; }
if (STAGEPROELEVE == "non") { $stageProelevenon="checked"; }
if (STAGEPROPARENT == "oui") { $stageProparentoui="checked"; }
if (STAGEPROPARENT == "non") { $stageProparentnon="checked"; }

if (STOCKAGEPROF == "oui") { $stockageprofoui="checked"; }
if (STOCKAGEPROF == "non") { $stockageprofnon="checked"; }
if (INTRAMSNPROF == "oui") { $intramsnprofoui="checked"; }
if (INTRAMSNPROF == "non") { $intramsnprofnon="checked"; }
if (AGENDAMSNPROF == "oui") { $agendaprofoui="checked"; }
if (AGENDAMSNPROF == "non") { $agendaprofnon="checked"; }
if (FLUXRSSPROF == "oui") { $fluxrssprofoui="checked"; }
if (FLUXRSSPROF == "non") { $fluxrssprofnon="checked"; }
if (NOTESPROF == "oui") { $notesprofoui="checked"; }
if (NOTESPROF == "non") { $notesprofnon="checked"; }
if (BULLETINPROF == "oui") { $bulletinprofoui="checked"; }
if (BULLETINPROF == "non") { $bulletinprofnon="checked"; }
if (RESAPROF == "oui") { $resaprofoui="checked"; }
if (RESAPROF == "non") { $resaprofnon="checked"; }
if (CIRCULAIREPROF == "oui") { $circulaireprofoui="checked"; }
if (CIRCULAIREPROF == "non") { $circulaireprofnon="checked"; }
if (INFORMATIONPROF == "oui") { $informationprofoui="checked"; }
if (INFORMATIONPROF == "non") { $informationprofnon="checked"; }
if (CALENDRIERPROF == "oui") { $calendrierprofoui="checked"; }
if (CALENDRIERPROF == "non") { $calendrierprofnon="checked"; }

if (STOCKAGEVIESCOLAIRE == "oui") { $stockageviescolaireoui="checked"; }
if (STOCKAGEVIESCOLAIRE == "non") { $stockageviescolairenon="checked"; }
if (INTRAMSNVIESCOLAIRE == "oui") { $intramsnviescolaireoui="checked"; }
if (INTRAMSNVIESCOLAIRE == "non") { $intramsnviescolairenon="checked"; }
if (AGENDAVIESCOLAIRE == "oui") { $agendaviescolaireoui="checked"; }
if (AGENDAVIESCOLAIRE == "non") { $agendaviescolairenon="checked"; }
if (FLUXRSSVIESCOLAIRE == "oui") { $fluxrssviescolaireoui="checked"; }
if (FLUXRSSVIESCOLAIRE == "non") { $fluxrssviescolairenon="checked"; }
if (ETUDEVIESCOLAIRE == "oui") { $etudeviescolaireoui="checked"; }
if (ETUDEVIESCOLAIRE == "non") { $etudeviescolairenon="checked"; }
if (CIRCULAIREVIESCOLAIRE == "oui") { $circulaireviescolaireoui="checked"; }
if (CIRCULAIREVIESCOLAIRE == "non") { $circulaireviescolairenon="checked"; }
if (STAGEVIESCOLAIRE == "oui") { $stageviescolaireoui="checked"; }
if (STAGEVIESCOLAIRE == "non") { $stageviescolairenon="checked"; }
if (DSTVIESCOLAIRE == "oui") { $dstviescolaireoui="checked"; }
if (DSTVIESCOLAIRE == "non") { $dstviescolairenon="checked"; }
if (VISAVIESCOLAIRE == "oui") { $visaviescolaireoui="checked"; }
if (VISAVIESCOLAIRE == "non") { $visaviescolairenon="checked"; }
if (NOTEVIESCOLAIRE == "oui") { $noteviescolaireoui="checked"; }
if (NOTEVIESCOLAIRE == "non") { $noteviescolairenon="checked"; }
if (IMPTABLEAUVIESCOLAIRE == "oui") { $imptableauviescolaireoui="checked"; }
if (IMPTABLEAUVIESCOLAIRE == "non") { $imptableauviescolairenon="checked"; }
if (BULLETINVIESCOLAIRE == "oui") { $bulletinviescolaireoui="checked"; }
if (BULLETINVIESCOLAIRE == "non") { $bulletinviescolairenon="checked"; }
if (PERIODEVIESCOLAIRE == "oui") { $periodeviescolaireoui="checked"; }
if (PERIODEVIESCOLAIRE == "non") { $periodeviescolairenon="checked"; }
if (VIDEOPROJOVIESCOLAIRE == "oui") { $videoprojoviescolaireoui="checked"; }
if (VIDEOPROJOVIESCOLAIRE == "non") { $videoprojoviescolairenon="checked"; }
if (PLANCLASSEVIESCOLAIRE == "oui") { $planclasseviescolaireoui="checked"; }
if (PLANCLASSEVIESCOLAIRE == "non") { $planclasseviescolairenon="checked"; }
if (HISTORYVIESCOLAIRE == "oui") { $historyviescolaireoui="checked"; }
if (HISTORYVIESCOLAIRE == "non") { $historyviescolairenon="checked"; }
if (RESAVIESCOLAIRE == "oui") { $resaviescolaireoui="checked"; }
if (RESAVIESCOLAIRE == "non") { $resaviescolairenon="checked"; }
if (EXPORTVIESCOLAIRE == "oui") { $exportviescolaireoui="checked"; }
if (EXPORTVIESCOLAIRE == "non") { $exportviescolairenon="checked"; }
if (VACATIONVIESCOLAIRE == "oui") { $vacationviescolaireoui="checked"; }
if (VACATIONVIESCOLAIRE == "non") { $vacationviescolairenon="checked"; }

if (NOTEENSEIGNANTVIASCOLAIRE == "oui") { $noteenseignantviascolaireoui="checked"; }
if (NOTEENSEIGNANTVIASCOLAIRE == "non") { $noteenseignantviascolairenon="checked"; }


if (MODULECANTINEPROF == "oui") { $cantineprofoui="checked"; }
if (MODULECANTINEPROF == "non") { $cantineprofnon="checked"; }
if (MODULECANTINEVIESCOLAIRE == "oui") { $cantineviascolaireoui="checked"; }
if (MODULECANTINEVIESCOLAIRE == "non") { $cantineviascolairenon="checked"; }


if (STOCKAGEADMIN == "oui") { $stockageadminoui="checked"; }
if (STOCKAGEADMIN == "non") { $stockageadminnon="checked"; }
if (INTRAMSNADMIN == "oui") { $intramsnadminoui="checked"; }
if (INTRAMSNADMIN == "non") { $intramsnadminnon="checked"; }
if (AGENDAADMIN == "oui") { $agendaadminoui="checked"; }
if (AGENDAADMIN == "non") { $agendaadminnon="checked"; }
if (FLUXRSSADMIN == "oui") { $fluxrssadminoui="checked"; }
if (FLUXRSSADMIN == "non") { $fluxrssadminnon="checked"; }
if (RESAADMIN == "oui") { $resaadminoui="checked"; }
if (RESAADMIN == "non") { $resaadminnon="checked"; }
if (VACATIONADMIN == "oui") { $vacationadminoui="checked"; }
if (VACATIONADMIN == "non") { $vacationadminnon="checked"; }
if (HISTORYADMIN == "oui") { $historyadminoui="checked"; }
if (HISTORYADMIN == "non") { $historyadminnon="checked"; }
if (MODULECANTINEADMIN == "oui") { $cantineadminoui="checked"; }
if (MODULECANTINEADMIN == "non") { $cantineadminnon="checked"; }
if (DROITSCOLARITEADMIN == "oui") { $droitscolariteadminoui="checked"; }
if (DROITSCOLARITEADMIN == "non") { $droitscolariteadminnon="checked"; }
if (NOTEPROFVIAADMIN == "oui") { $noteenseignantadminoui="checked"; }
if (NOTEPROFVIAADMIN == "non") { $noteenseignantadminnon="checked"; }

if (PARENTABSENCE == "oui") { $parentabsenceoui="checked"; }
if (PARENTABSENCE == "non") { $parentabsencenon="checked"; }
if (PARENTRETARD == "oui") { $parentretardoui="checked"; }
if (PARENTRETARD == "non") { $parentretardnon="checked"; }

if (MODULECHAMBRESADMIN == "oui") { $modulechambresvateladminoui="checked"; }
if (MODULECHAMBRESADMIN == "non") { $modulechambresvateladminnon="checked"; }
if (MODULEFINANCIERADMIN == "oui") { $modulefinanciervateladminoui="checked"; }
if (MODULEFINANCIERADMIN == "non") { $modulefinanciervateladminnon="checked"; }

if (MODULECHAMBRESVIESCOLAIRE == "oui") { $chambreviescolaireoui="checked"; }
if (MODULECHAMBRESVIESCOLAIRE == "non") { $chambreviescolairenon="checked"; }


if (MODULETUTEURNOTE == "oui") {$tuteurnoteoui="checked"; }
if (MODULETUTEURNOTE == "non") {$tuteurnotenon="checked"; }
if (MODULETUTEURDISCIPLINE == "oui") {$tuteurdisciplineoui="checked"; }
if (MODULETUTEURDISCIPLINE == "non") {$tuteurdisciplinenon="checked"; }
if (MODULETUTEURABS == "oui") {$tuteurabsoui="checked"; }
if (MODULETUTEURABS == "non") {$tuteurabsnon="checked"; }
if (MODULETUTEURDISPENSE == "oui") {$tuteurdispenseoui="checked"; }
if (MODULETUTEURDISPENSE == "non") {$tuteurdispensenon="checked"; }
if (MODULETUTEUREDT == "oui") {$tuteurEDToui="checked"; }
if (MODULETUTEUREDT == "non") {$tuteurEDTnon="checked"; }
if (MODULETUTEURCAHIERDETEXTE == "oui") {$tuteurcahierdetexteoui="checked"; }
if (MODULETUTEURCAHIERDETEXTE == "non") {$tuteurcahierdetextenon="checked"; }
if (MODULETUTEURCIRCULAIRE == "oui") {$tuteurcirculaireoui="checked"; }
if (MODULETUTEURCIRCULAIRE == "non") {$tuteurcirculairenon="checked"; }
if (MODULETUTEURCALENDRIER == "oui") {$tuteurcalendrieroui="checked"; }
if (MODULETUTEURCALENDRIER == "non") {$tuteurcalendriernon="checked"; }
if (MODULEPROFEMARGEMENT == "oui") {$emargementprofoui="checked"; }
if (MODULEPROFEMARGEMENT == "non") {$emargementprofnon="checked"; }


if (MODULEPARENTAGENDA == "oui") { $agendaparentoui="checked"; }
if (MODULEPARENTAGENDA == "non") { $agendaparentnon="checked"; }
if (MODULEPARENTSTOCKAGE == "oui") { $stockageparentoui="checked"; }
if (MODULEPARENTSTOCKAGE == "non") { $stockageparentnon="checked"; }
if (MODULEPARENTMSN == "oui") { $intramsnparentoui="checked"; }
if (MODULEPARENTMSN == "non") { $intramsnparentnon="checked"; }
if (MODULEPARENTCOMPTA == "oui") {$comptaparentoui="checked"; }
if (MODULEPARENTCOMPTA == "non") {$comptaparentnon="checked"; }
if (MODULEPARENTRSS == "oui") {$rssparentoui="checked"; }
if (MODULEPARENTRSS == "non") {$rssparentnon="checked"; }
if (MODULEPARENTCANTINE == "oui") {$cantineparentoui="checked"; }
if (MODULEPARENTCANTINE == "non") {$cantineparentnon="checked"; }

if (MODULEELEVEAGENDA == "oui") { $agendaeleveoui="checked"; }
if (MODULEELEVEAGENDA == "non") { $agendaelevenon="checked"; }
if (MODULEELEVESTOCKAGE == "oui") { $stockageeleveoui="checked"; }
if (MODULEELEVESTOCKAGE == "non") { $stockageelevenon="checked"; }
if (MODULEELEVEMSN == "oui") { $intramsneleveoui="checked"; }
if (MODULEELEVEMSN == "non") { $intramsnelevenon="checked"; }
if (MODULEELEVECOMPTA == "oui") {$comptaeleveoui="checked"; }
if (MODULEELEVECOMPTA == "non") {$comptaelevenon="checked"; }
if (MODULEELEVERSS == "oui") {$rsseleveoui="checked"; }
if (MODULEELEVERSS == "non") {$rsselevenon="checked"; }
if (MODULEELEVECANTINE == "oui") {$cantineeleveoui="checked"; }
if (MODULEELEVECANTINE == "non") {$cantineelevenon="checked"; }

if (MODULENEWSPAGEVIESCOLAIRE == "oui") {$newspageviescolaireoui="checked"; }
if (MODULENEWSPAGEVIESCOLAIRE == "non") {$newspageviescolairenon="checked"; }
if (MODULENEWSVIESCOLAIRE == "oui") {$newsviescolaireoui="checked"; }
if (MODULENEWSVIESCOLAIRE == "non") {$newsviescolairenon="checked"; }
if (MODULEBOURSIERADMIN == "oui") {$moduleboursieradminoui="checked"; }
if (MODULEBOURSIERADMIN == "non") {$moduleboursieradminnon="checked"; }

if (MODULEMESSAGERIEADMIN == "oui") {$messagerieadminoui="checked"; }
if (MODULEMESSAGERIEADMIN == "non") {$messagerieadminnon="checked"; }
if (MODULEMESSAGERIESCOLAIRE == "oui") {$messagerieviescolaireoui="checked"; }
if (MODULEMESSAGERIESCOLAIRE == "non") {$messagerieviescolairenon="checked"; }
if (MODULEMESSAGERIEPROF == "oui") {$messagerieProfoui="checked"; }
if (MODULEMESSAGERIEPROF == "non") {$messagerieProfnon="checked"; }
if (MODULEMESSAGERIEELEVE == "oui") {$messagerieEleveoui="checked"; }
if (MODULEMESSAGERIEELEVE == "non") {$messagerieElevenon="checked"; }
if (MODULEMESSAGERIEPARENT == "oui") {$messagerieParentoui="checked"; }
if (MODULEMESSAGERIEPARENT == "non") {$messagerieParentnon="checked"; }
if (MODULEMESSAGERIETUTEUR == "oui") {$messagerietuteuroui="checked"; }
if (MODULEMESSAGERIETUTEUR == "non") {$messagerietuteurnon="checked"; }

if (MODULEPREINSCRIPTIONVIESCOLAIRE == "oui") {$preinscriptionviescolaireoui="checked"; }
if (MODULEPREINSCRIPTIONVIESCOLAIRE == "non") {$preinscriptionviescolairenon="checked"; }

if (MODULEPLANNINGELEVE == "oui") {$planningeleveoui="checked"; }
if (MODULEPLANNINGELEVE == "non") {$planningelevenon="checked"; }
if (MODULEPLANNINGPARENT == "oui") {$planningparentoui="checked"; }
if (MODULEPLANNINGPARENT == "non") {$planningparentnon="checked"; }
if (MODULEPLANNINGPROF == "oui") {$planningprofoui="checked"; }
if (MODULEPLANNINGPROF == "non") {$planningprofnon="checked"; }

if (RUBRIQUEBULLETIN == "oui") {$rubriquebulletinoui="checked"; }
if (RUBRIQUEBULLETIN == "non") {$rubriquebulletinnon="checked"; }
if (RUBRIQUEANNEXE == "oui") {$rubriqueannexeoui="checked"; }
if (RUBRIQUEANNEXE == "non") {$rubriqueannexenon="checked"; }
if (RUBRIQUEGESTION == "oui") {$rubriquegestionoui="checked"; }
if (RUBRIQUEGESTION == "non") {$rubriquegestionnon="checked"; }

if (RUBRIQUEAFFECTATION == "oui") {$rubriqueaffectationoui="checked"; }
if (RUBRIQUEAFFECTATION == "non") {$rubriqueaffectationnon="checked"; }

if (RUBRIQUEETABLISSEMENT == "oui") {$rubriqueetablissementoui="checked"; }
if (RUBRIQUEETABLISSEMENT == "non") {$rubriqueetablissementnon="checked"; }

if (RUBRIQUEVIESCOLAIRE == "oui") {$rubriqueviescolaireoui="checked"; }
if (RUBRIQUEVIESCOLAIRE == "non") {$rubriqueviescolairenon="checked"; }

if (RUBRIQUEETUDIANT == "oui") {$rubriqueetudiantoui="checked"; }
if (RUBRIQUEETUDIANT == "non") {$rubriqueetudiantnon="checked"; }

if (RUBRIQUEACTUALITE == "oui") {$rubriqueactualiteoui="checked"; }
if (RUBRIQUEACTUALITE == "non") {$rubriqueactualitenon="checked"; }

// -------------------------------------------------------------------------------

if (MODULEADMINCDI == "oui") {$moduleadmincdioui="checked"; }
if (MODULEADMINCDI == "non") {$moduleadmincdinon="checked"; }
if (MODULEADMINNOTANET == "oui") {$moduleadminnotanetoui="checked"; }
if (MODULEADMINNOTANET == "non") {$moduleadminnotanetnon="checked"; }
if (MODULEADMINGESTIONSMS == "oui") {$moduleadmingestionsmsoui="checked"; }
if (MODULEADMINGESTIONSMS == "non") {$moduleadmingestionsmsnon="checked"; }
if (MODULEADMINRESERVEQUIP == "oui") {$moduleadminreservequipoui="checked"; }
if (MODULEADMINRESERVEQUIP == "non") {$moduleadminreservequipnon="checked"; }
if (MODULEADMINRESERVSALLE == "oui") {$moduleadminreservsalleoui="checked"; }
if (MODULEADMINRESERVSALLE == "non") {$moduleadminreservsallenon="checked"; }
if (MODULEADMINFOURNITURE == "oui") {$moduleadminfournitureoui="checked"; }
if (MODULEADMINFOURNITURE == "non") {$moduleadminfourniturenon="checked"; }
if (MODULEADMINEXAMBREVET == "oui") {$moduleadminexambrevetoui="checked"; }
if (MODULEADMINEXAMBREVET == "non") {$moduleadminexambrevetnon="checked"; }
if (MODULEADMINGESTIONETUDE == "oui") {$moduleadmingestionetudeoui="checked"; }
if (MODULEADMINGESTIONETUDE == "non") {$moduleadmingestionetudenon="checked"; }
if (MODULEADMINGESTIONDISCIPLINE == "oui") {$moduleadmingestiondisciplineoui="checked"; }
if (MODULEADMINGESTIONDISCIPLINE == "non") {$moduleadmingestiondisciplinenon="checked"; }
if (MODULEADMINRETENUDJ == "oui") {$moduleadminretenudjoui="checked"; }
if (MODULEADMINRETENUDJ == "non") {$moduleadminretenudjnon="checked"; }
if (MODULEADMINSANCTIONDUJOUR == "oui") {$moduleadminsanctiondujouroui="checked"; }
if (MODULEADMINSANCTIONDUJOUR == "non") {$moduleadminsanctiondujournon="checked"; }
if (MODULEADMINGESTIONDISPENSE == "oui") {$moduleadmingestiondispenseoui="checked"; }
if (MODULEADMINGESTIONDISPENSE == "non") {$moduleadmingestiondispensenon="checked"; }
if (MODULEADMINDOSMEDICAL == "oui") {$moduleadmindosmedicaloui="checked"; }
if (MODULEADMINDOSMEDICAL == "non") {$moduleadmindosmedicalnon="checked"; }
if (MODULEADMINPLANCLASSE == "oui") {$moduleadminplanclasseoui="checked"; }
if (MODULEADMINPLANCLASSE == "non") {$moduleadminplanclassenon="checked"; }
if (MODULEADMINGESTIONDELEGUE == "oui") {$moduleadmingestiondelegueoui="checked"; }
if (MODULEADMINGESTIONDELEGUE == "non") {$moduleadmingestiondeleguenon="checked"; }
if (MODULEADMINSOUSMATIERE == "oui") {$moduleadminsousmatiereoui="checked"; }
if (MODULEADMINSOUSMATIERE == "non") {$moduleadminsousmatierenon="checked"; }
if (MODULEADMINPROFP == "oui") {$moduleprofPoui="checked"; }
if (MODULEADMINPROFP == "non") {$moduleprofPnon="checked"; }
if (MODULEADMINCONFIGNOTEUSA == "oui") {$moduleconfignoteusaoui="checked"; }
if (MODULEADMINCONFIGNOTEUSA == "non") {$moduleconfignoteusanon="checked"; }
if (MODULEADMINENTRETIENINDIVIDUEL == "oui") {$moduleentretienindividueloui="checked"; }
if (MODULEADMINENTRETIENINDIVIDUEL == "non") {$moduleentretienindividuelnon="checked"; }
if (MODULEADMINCARNETSUIVI == "oui") {$modulecarnetsuivioui="checked"; }
if (MODULEADMINCARNETSUIVI == "non") {$modulecarnetsuivinon="checked"; }
if (MODULEADMINVERIFBULLETIN == "oui") {$moduleverifbulletinoui="checked"; }
if (MODULEADMINVERIFBULLETIN == "non") {$moduleverifbulletinnon="checked"; }
if (MODULEADMINNOTEVIESCOLAIRE == "oui") {$modulenoteviescolaireoui="checked"; }
if (MODULEADMINNOTEVIESCOLAIRE == "non") {$modulenoteviescolairenon="checked"; }
if (MODULEADMINIMPRPERIODE == "oui") {$moduleadminimprperiodeoui="checked"; }
if (MODULEADMINIMPRPERIODE == "non") {$moduleadminimprperiodenon="checked"; }
if (MODULEADMINSUPPLEANT == "oui") {$moduleadminsuppleantoui="checked"; }
if (MODULEADMINSUPPLEANT == "non") {$moduleadminsuppleantnon="checked"; }
if (MODULEADMINABSRTD == "oui") {$moduleadminabsrtdoui="checked"; }
if (MODULEADMINABSRTD == "non") {$moduleadminabsrtdnon="checked"; }
if (MODULEADMINPREINSCRIPTION == "oui") {$moduleadminpreinscriptionoui="checked"; }
if (MODULEADMINPREINSCRIPTION == "non") {$moduleadminpreinscriptionnon="checked"; }
if (MODULEADMINNOUVELLEANNEE == "oui") {$moduleadminnouvelleanneeoui="checked"; }
if (MODULEADMINNOUVELLEANNEE == "non") {$moduleadminnouvelleanneenon="checked"; }
if (MODULEADMINARCHIVAGE == "oui") {$moduleadminarchivageoui="checked"; }
if (MODULEADMINARCHIVAGE == "non") {$moduleadminarchivagenon="checked"; }
if (MODULEADMINNEWSDEFILANT == "oui") {$moduleadminnewsdefilantoui="checked"; }
if (MODULEADMINNEWSDEFILANT == "non") {$moduleadminnewsdefilantnon="checked"; }
if (MODULEADMINPURGERINFO == "oui") {$moduleadminpurgerinfooui="checked"; }
if (MODULEADMINPURGERINFO == "non") {$moduleadminpurgerinfonon="checked"; }
$CDIEleveoui="";
$CDIElevenon="";
if (MODULEELEVECDI == "oui") {$CDIEleveoui="checked"; }
if (MODULEELEVECDI == "non") {$CDIElevenon="checked"; }
if (MODULEADMINGESTIONSAVOIRETRE == "oui") {$moduleadmingestionsavoiretreoui="checked"; }
if (MODULEADMINGESTIONSAVOIRETRE == "non") {$moduleadmingestionsavoiretrenon="checked"; }
if (MODULEPROFGESTIONSAVOIRETRE == "oui") {$moduleprofgestionsavoiretreoui="checked"; }
if (MODULEPROFGESTIONSAVOIRETRE == "non") {$moduleprofgestionsavoiretrenon="checked"; }
if (MODULEELEVEGESTIONSAVOIRETRE == "non") {$moduleelevegestionsavoiretrenon="checked"; }
if (MODULEELEVEGESTIONSAVOIRETRE == "oui") {$moduleelevegestionsavoiretreoui="checked"; }
if (MODULEPARENTGESTIONSAVOIRETRE == "oui") {$moduleparentgestionsavoiretreoui="checked"; }
if (MODULEPARENTGESTIONSAVOIRETRE == "non") {$moduleparentgestionsavoiretrenon="checked"; }
if (MODULETUTEURGESTIONSAVOIRETRE == "oui") {$moduletuteursavoiretreoui="checked"; }
if (MODULETUTEURGESTIONSAVOIRETRE == "non") {$moduletuteursavoiretrenon="checked"; }
if (MODULEVIESCOLAIREGESTIONSAVOIRETRE == "oui") {$moduleviescolairesavoiretreoui="checked"; }
if (MODULEVIESCOLAIREGESTIONSAVOIRETRE == "non") {$moduleviescolairesavoiretrenon="checked"; }

if (MODULEBULLETINVISUELEVE == "oui") {$modulebulletinvisueleveoui="checked"; }
if (MODULEBULLETINVISUELEVE == "non") {$modulebulletinvisuelevenon="checked"; }
if (MODULEBULLETINVISUPARENT == "oui") {$modulebulletinvisuparentoui="checked"; }
if (MODULEBULLETINVISUPARENT == "non") {$modulebulletinvisuparentnon="checked"; }

if (MODULEBULLETINVISUTUTEUR == "oui") {$modulebulletinvisututeurstageoui="checked"; }
if (MODULEBULLETINVISUTUTEUR == "non") {$modulebulletinvisututeurstagenon="checked"; }

if (MODULEADMINEVALENS == "oui") {$moduleadminevalensoui="checked"; }
if (MODULEADMINEVALENS == "non") {$moduleadminevalensnon="checked"; }

$radiooui="";
$radionon="";
if (MODULERADIO == "oui") {$radiooui="checked"; }
if (MODULERADIO == "non") {$radionon="checked"; }

$modulefourniturescolaireoui="";
$modulefourniturescolairenon="";
if (MODULEFOURNITURESCOLAIRE == "oui") {$modulefourniturescolaireoui="checked"; }
if (MODULEFOURNITURESCOLAIRE == "non") {$modulefourniturescolairenon="checked"; }
 
$moduledelegueparentoui="";
$moduledelegueparentnon="";
if (MODULEDELEGUEPARENT == "oui") {$moduledelegueparentoui="checked"; }
if (MODULEDELEGUEPARENT == "non") {$moduledelegueparentnon="checked"; }

?>

<tr><td align=right >Module Pré-inscription : </td>
<td align=left><input type=radio <?php print $preinsciptionoui ?> name=preinsciption value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $preinsciptionnon ?> name=preinsciption value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Radio : </td>
<td align=left><input type=radio <?php print $radiooui ?> name=radio value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $radionon ?> name=radio value="non" class=btradio1  > non </td>
</tr>

<!--
<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Parent</b></font></td></tr>
-->

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Elève</b></font></td></tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaeleveoui ?> name="agendaeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaelevenon ?> name="agendaeleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageeleveoui ?> name="stockageeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageelevenon ?> name="stockageeleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsneleveoui ?> name="intramsneleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnelevenon ?> name="intramsneleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Comptabilité : </td>
<td align=left><input type=radio <?php print $comptaeleveoui ?> name="comptaeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $comptaelevenon ?> name="comptaeleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Flux RSS : </td>
<td align=left><input type=radio <?php print $rsseleveoui ?> name="rsseleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rsselevenon ?> name="rsseleve" value="non" class=btradio1  > non </td>
</tr>
</tr>
<tr><td align=right >Module Cantine : </td>
<td align=left><input type=radio <?php print $cantineeleveoui ?> name="cantineeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineelevenon ?> name="cantineeleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Planning : </td>
<td align=left><input type=radio <?php print $planningeleveoui ?> name="planningeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planningelevenon ?> name="planningeleve" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Dispense : </td>
<td align=left><input type=radio <?php print $dispenseoui ?> name="dispense" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $dispensenon ?> name="dispense" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Discipline : </td>
<td align=left><input type=radio <?php print $disciplineoui ?> name="discipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $disciplinenon ?> name="discipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de texte : </td>
<td align=left><input type=radio <?php print $cahierdetexteoui ?> name=cahierdetexte value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cahierdetextenon ?> name=cahierdetexte value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $planclasseoui ?> name=planclasse value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planclassenon ?> name=planclasse value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $DSToui ?> name=DST value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DSTnon ?> name=DST value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module E-Learning : </td>
<td align=left><input type=radio <?php print $DOKEOSeleveoui ?> name="DOKEOSEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DOKEOSelevenon ?> name="DOKEOSEleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageProeleveoui ?> name="stageProeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageProelevenon ?> name="stageProeleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieEleveoui ?> name="messagerieEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieElevenon ?> name="messagerieEleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module CDI : </td>
<td align=left><input type=radio <?php print $CDIEleveoui ?> name="CDIEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $CDIElevenon ?> name="CDIEleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleelevegestionsavoiretreoui ?> name="moduleelevegestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleelevegestionsavoiretrenon ?> name="moduleelevegestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

</tr>
<tr><td align=right >Module Visualisation du bulletin : </td>
<td align=left><input type=radio <?php print $modulebulletinvisueleveoui ?> name="modulebulletinvisueleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulebulletinvisuelevenon ?> name="modulebulletinvisueleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Parent</b></font></td></tr>



<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaparentoui ?> name="agendaparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaparentnon ?> name="agendaparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageparentoui ?> name="stockageparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageparentnon ?> name="stockageparent" value="non" class=btradio1  > non </td>
</tr>
<!-- <tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnparentoui ?> name="intramsnparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnparentnon ?> name="intramsnparent" value="non" class=btradio1  > non </td>
</tr>
--> 
<tr><td align=right >Module Comptabilité : </td>
<td align=left><input type=radio <?php print $comptaparentoui ?> name="comptaparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $comptaparentnon ?> name="comptaparent" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Flux RSS : </td>
<td align=left><input type=radio <?php print $rssparentoui ?> name="rssparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rssparentnon ?> name="rssparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Planning : </td>
<td align=left><input type=radio <?php print $planningparentoui ?> name="planningparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planningparentnon ?> name="planningparent" value="non" class=btradio1  > non </td>
</tr>

</tr>
<tr><td align=right >Module Cantine : </td>
<td align=left><input type=radio <?php print $cantineparentoui ?> name="cantineparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineparentnon ?> name="cantineparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Dispense : </td>
<td align=left><input type=radio <?php print $parentdispenseoui ?> name="parentdispense" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentdispensenon ?> name="parentdispense" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Absence : </td>
<td align=left><input type=radio <?php print $parentabsenceoui ?> name="parentabsence" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentabsencenon ?> name="parentabsence" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Retard : </td>
<td align=left><input type=radio <?php print $parentretardoui ?> name="parentretard" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentretardnon ?> name="parentretard" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Discipline : </td>
<td align=left><input type=radio <?php print $parentdisciplineoui ?> name="parentdiscipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentdisciplinenon ?> name="parentdiscipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de texte : </td>
<td align=left><input type=radio <?php print $parentcahierdetexteoui ?> name=parentcahierdetexte value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentcahierdetextenon ?> name=parentcahierdetexte value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $parentplanclasseoui ?> name=parentplanclasse value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentplanclassenon ?> name=parentplanclasse value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Trombinoscopes : </td>
<td align=left><input type=radio <?php print $parentTrombinoscopeoui ?> name='parentTrombinoscope' value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentTrombinoscopenon ?> name='parentTrombinoscope' value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $parentDSToui ?> name=parentDST value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentDSTnon ?> name=parentDST value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageProparentoui ?> name="stageProparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageProparentnon ?> name="stageProparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieParentoui ?> name="messagerieParent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieParentnon ?> name="messagerieParent" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleparentgestionsavoiretreoui ?> name="moduleparentgestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleparentgestionsavoiretrenon ?> name="moduleparentgestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

</tr>

<tr><td align=right >Module Visualisation du bulletin : </td>
<td align=left><input type=radio <?php print $modulebulletinvisuparentoui ?> name="modulebulletinvisuparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulebulletinvisuparentnon ?> name="modulebulletinvisuparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module fourniture scolaire : </td>
<td align=left><input type=radio <?php print $modulefourniturescolaireoui ?> name="modulefourniturescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $modulefourniturescolairenon ?> name="modulefourniturescolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module d&eacute;l&eacute;gu&eacute; : </td>
<td align=left><input type=radio <?php print $moduledelegueparentoui ?> name="moduledelegueparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $moduledelegueparentnon ?> name="moduledelegueparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Tuteur de stage</b></font></td></tr>

<tr><td align=right >Module Note : </td>
<td align=left><input type=radio <?php print $tuteurnoteoui ?> name="tuteurnote" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurnotenon ?> name="tuteurnote" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Discipline : </td>
<td align=left><input type=radio <?php print $tuteurdisciplineoui ?> name="tuteurdiscipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurdisciplinenon ?> name="tuteurdiscipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Absence : </td>
<td align=left><input type=radio <?php print $tuteurabsoui ?> name=tuteurabs value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurabsnon ?> name=tuteurabs value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Dispense : </td>
<td align=left><input type=radio <?php print $tuteurdispenseoui ?> name=tuteurdispense value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurdispensenon ?> name=tuteurdispense value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Emploi du temps : </td>
<td align=left><input type=radio <?php print $tuteurEDToui ?> name=tuteurEDT value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurEDTnon ?> name=tuteurEDT value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de textes : </td>
<td align=left><input type=radio <?php print $tuteurcahierdetexteoui ?> name="tuteurcahierdetexte" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurcahierdetextenon ?> name="tuteurcahierdetexte" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Circulaires : </td>
<td align=left><input type=radio <?php print $tuteurcirculaireoui ?> name="tuteurcirculaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurcirculairenon ?> name="tuteurcirculaire" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Calendrier : </td>
<td align=left><input type=radio <?php print $tuteurcalendrieroui ?> name="tuteurcalendrier" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurcalendriernon ?> name="tuteurcalendrier" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerietuteuroui ?> name="messagerietuteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerietuteurnon ?> name="messagerietuteur" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduletuteursavoiretreoui ?> name="moduletuteursavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduletuteursavoiretrenon ?> name="moduletuteursavoiretre" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Visualisation du bulletin : </td>
<td align=left><input type=radio <?php print $modulebulletinvisututeurstageoui ?> name="modulebulletinvisututeurstage" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulebulletinvisututeurstagenon ?> name="modulebulletinvisututeurstage" value="non" class=btradio1  > non </td>
</tr>

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Enseignant</b></font></td></tr>

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageprofoui ?> name=stockageprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageprofnon ?> name=stockageprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnprofoui ?> name=intramsnprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnprofnon ?> name=intramsnprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaprofoui ?> name=agendaprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaprofnon ?> name=agendaprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Planning : </td>
<td align=left><input type=radio <?php print $planningprofoui ?> name="planningprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planningprofnon ?> name="planningprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Emargement : </td>
<td align=left><input type=radio <?php print $emargementprofoui ?> name=emargementprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $emargementprofnon ?> name=emargementprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Mes Flux RSS : </td>
<td align=left><input type=radio <?php print $fluxrssprofoui ?> name=fluxrssprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fluxrssprofnon ?> name=fluxrssprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module cantine : </td>
<td align=left><input type=radio <?php print $cantineprofoui ?> name=cantineprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineprofnon ?> name=cantineprof value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Notes : </td>
<td align=left><input type=radio <?php print $notesprofoui ?> name=notesprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $notesprofnon ?> name=notesprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Bulletin Trimestriel : </td>
<td align=left><input type=radio <?php print $bulletinprofoui ?> name=bulletinprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $bulletinprofnon ?> name=bulletinprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Réservation salle : </td>
<td align=left><input type=radio <?php print $resaprofoui ?> name=resaprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resaprofnon ?> name=resaprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Circulaires adm. : </td>
<td align=left><input type=radio <?php print $circulaireprofoui ?> name=circulaireprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $circulaireprofnon ?> name=circulaireprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Informations : </td>
<td align=left><input type=radio <?php print $informationprofoui ?> name=informationprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $informationprofnon ?> name=informationprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Visualiser un devoir : </td>
<td align=left><input type=radio <?php print $visudevoirprofoui ?> name=visudevoirprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $visudevoirprofnon ?> name=visudevoirprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Supprimer un devoir : </td>
<td align=left><input type=radio <?php print $suppdevoirprofoui ?> name=suppdevoirprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $suppdevoirprofnon ?> name=suppdevoirprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de textes : </td>
<td align=left><input type=radio <?php print $cahiertexteprofoui ?> name=cahiertexteprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cahiertexteprofnon ?> name=cahiertexteprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Sanction élèves : </td>
<td align=left><input type=radio <?php print $sanctionprofoui ?> name=sanctionprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $sanctionprofnon ?> name=sanctionprof value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Fiche élèves : </td>
<td align=left><input type=radio <?php print $ficheeleveprofoui ?> name=ficheeleveprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $ficheeleveprofnon ?> name=ficheeleveprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Liste élèves : </td>
<td align=left><input type=radio <?php print $listeleveprofoui ?> name=listeleveprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $listeleveprofnon ?> name=listeleveprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $planprofoui ?> name=planprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planprofnon ?> name=planprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageproprofoui ?> name=stageproprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageproprofnon ?> name=stageproprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $DSTProfoui ?> name="DSTProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DSTProfnon ?> name="DSTProf" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module E-Learning : </td>
<td align=left><input type=radio <?php print $DOKEOSProfoui ?> name="DOKEOSProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DOKEOSProfnon ?> name="DOKEOSProf" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Comptabilité (Vacation) : </td>
<td align=left><input type=radio <?php print $comptaProfoui ?> name="comptaProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $comptaProfnon ?> name="comptaProf" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieProfoui ?> name="messagerieProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieProfnon ?> name="messagerieProf" value="non" class=btradio1  > non </td>
</tr>

</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleprofgestionsavoiretreoui ?> name="moduleprofgestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleprofgestionsavoiretrenon ?> name="moduleprofgestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Vie Scolaire</b></font></td></tr>

<tr><td align=right >Module News 1er page : </td>
<td align=left><input type=radio <?php print $newspageviescolaireoui ?> name=newspageviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $newspageviescolairenon ?> name=newspageviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module News établissement : </td>
<td align=left><input type=radio <?php print $newsviescolaireoui ?> name=newsviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $newsviescolairenon ?> name=newsviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageviescolaireoui ?> name=stockageviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageviescolairenon ?> name=stockageviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnviescolaireoui ?> name=intramsnviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnviescolairenon ?> name=intramsnviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaviescolaireoui ?> name=agendaviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaviescolairenon ?> name=agendaviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Mes Flux RSS : </td>
<td align=left><input type=radio <?php print $fluxrssviescolaireoui ?> name=fluxrssviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fluxrssviescolairenon ?> name=fluxrssviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module cantine : </td>
<td align=left><input type=radio <?php print $cantineviascolaireoui ?> name=cantineviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineviascolairenon ?> name=cantineviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Etude : </td>
<td align=left><input type=radio <?php print $etudeviescolaireoui ?> name=etudeviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $etudeviescolairenon ?> name=etudeviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Circulaire : </td>
<td align=left><input type=radio <?php print $circulaireviescolaireoui ?> name=circulaireviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $circulaireviescolairenon ?> name=circulaireviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $dstviescolaireoui ?> name=dstviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $dstviescolairenon ?> name=dstviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageviescolaireoui ?> name=stageviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageviescolairenon ?> name=stageviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Visa vie scolaire : </td>
<td align=left><input type=radio <?php print $visaviescolaireoui ?> name=visaviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $visaviescolairenon ?> name=visaviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Note vie scolaire : </td>
<td align=left><input type=radio <?php print $noteviescolaireoui ?> name=noteviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteviescolairenon ?> name=noteviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Imprimer tableaux : </td>
<td align=left><input type=radio <?php print $imptableauviescolaireoui ?> name=imptableauviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $imptableauviescolairenon ?> name=imptableauviescolaire value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Imprimer bulletins : </td>
<td align=left><input type=radio <?php print $bulletinviescolaireoui ?> name=bulletinviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $bulletinviescolairenon ?> name=bulletinviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Imprimer période : </td>
<td align=left><input type=radio <?php print $periodeviescolaireoui ?> name=periodeviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $periodeviescolairenon ?> name=periodeviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vidéo-Projecteur : </td>
<td align=left><input type=radio <?php print $videoprojoviescolaireoui ?> name=videoprojoviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $videoprojoviescolairenon ?> name=videoprojoviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $planclasseviescolaireoui ?> name=planclasseviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planclasseviescolairenon ?> name=planclasseviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vacation ens. : </td>
<td align=left><input type=radio <?php print $vacationviescolaireoui ?> name=vacationviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $vacationviescolairenon ?> name=vacationviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module History cmd : </td>
<td align=left><input type=radio <?php print $historyviescolaireoui ?> name=historyviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $historyviescolairenon ?> name=historyviescolaire value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Réservation salle : </td>
<td align=left><input type=radio <?php print $resaviescolaireoui ?> name=resaviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resaviescolairenon ?> name=resaviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Exporter : </td>
<td align=left><input type=radio <?php print $exportviescolaireoui ?> name=exportviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $exportviescolairenon ?> name=exportviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Notation enseignants : </td>
<td align=left><input type=radio <?php print $noteenseignantviascolaireoui ?> name="noteenseignantviascolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteenseignantviascolairenon ?> name="noteenseignantviascolaire" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieviescolaireoui ?> name="messagerieviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieviescolairenon ?> name="messagerieviescolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module pré-inscription : </td>
<td align=left><input type=radio <?php print $preinscriptionviescolaireoui ?> name="preinscriptionviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $preinscriptionviescolairenon ?> name="preinscriptionviescolaire" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleviescolairesavoiretreoui ?> name="moduleviescolairesavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleviescolairesavoiretrenon ?> name="moduleviescolairesavoiretre" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de texte : </td>
<td align=left><input type=radio <?php print $cahierdetexteviescolaireoui ?> name=cahierdetexteviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cahierdetexteviescolairenon ?> name=cahierdetexteviescolaire value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Chambre : </td>
<td align=left><input type=radio <?php print $chambreviescolaireoui ?> name=chambreviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $chambreviescolairenon ?> name=chambreviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Direction</b></font></td></tr>

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageadminoui ?> name=stockageadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageadminnon ?> name=stockageadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnadminoui ?> name=intramsnadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnadminnon ?> name=intramsnadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaadminoui ?> name=agendaadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaadminnon ?> name=agendaadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Mes Flux RSS : </td>
<td align=left><input type=radio <?php print $fluxrssadminoui ?> name=fluxrssadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fluxrssadminnon ?> name=fluxrssadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module cantine : </td>
<td align=left><input type=radio <?php print $cantineadminoui ?> name=cantineadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineadminnon ?> name=cantineadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vacation ens. : </td>
<td align=left><input type=radio <?php print $vacationadminoui ?> name=vacationadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $vacationadminnon ?> name=vacationadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module droit de scolaire : </td>
<td align=left><input type=radio <?php print $droitscolariteadminoui ?> name=droitscolariteadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $droitscolariteadminnon ?> name=droitscolariteadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module History cmd : </td>
<td align=left><input type=radio <?php print $historyadminoui ?> name=historyadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $historyadminnon ?> name=historyadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Réservation salle : </td>
<td align=left><input type=radio <?php print $resaadminoui ?> name=resaadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resaadminnon ?> name=resaadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Notation enseignants : </td>
<td align=left><input type=radio <?php print $noteenseignantadminoui ?> name="noteenseignantadmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteenseignantadminnon ?> name="noteenseignantadmin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module boursier : </td>
<td align=left><input type=radio <?php print $moduleboursieradminoui ?> name="moduleboursieradmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleboursieradminnon ?> name="moduleboursieradmin" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Financier (Vatel) : </td>
<td align=left><input type=radio <?php print $modulefinanciervateladminoui ?> name="modulefinanciervateladmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulefinanciervateladminnon ?> name="modulefinanciervateladmin" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Chambres (Vatel) : </td>
<td align=left><input type=radio <?php print $modulechambresvateladminoui ?> name="modulechambresvateladmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulechambresvateladminnon ?> name="modulechambresvateladmin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieadminoui ?> name="messagerieadmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieadminnon ?> name="messagerieadmin" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Affectation : </td>
<td align=left><input type=radio <?php print $rubriqueaffectationoui ?> name="rubriqueaffectation" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueaffectationnon ?> name="rubriqueaffectation" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Gestion : </td>
<td align=left><input type=radio <?php print $rubriquegestionoui ?> name="rubriquegestion" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriquegestionnon ?> name="rubriquegestion" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Annexe : </td>
<td align=left><input type=radio <?php print $rubriqueannexeoui ?> name="rubriqueannexe" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueannexenon ?> name="rubriqueannexe" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Bulletin : </td>
<td align=left><input type=radio <?php print $rubriquebulletinoui ?> name="rubriquebulletin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriquebulletinnon ?> name="rubriquebulletin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Etablissement : </td>
<td align=left><input type=radio <?php print $rubriqueetablissementoui ?> name="rubriqueetablissement" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueetablissementnon ?> name="rubriqueetablissement" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Vie Scolaire : </td>
<td align=left><input type=radio <?php print $rubriqueviescolaireoui ?> name="rubriqueviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueviescolairenon ?> name="rubriqueviescolaire" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Actualit&eacute; : </td>
<td align=left><input type=radio <?php print $rubriqueactualiteoui ?> name="rubriqueactualite" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueactualitenon ?> name="rubriqueactualite" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Etudiant : </td>
<td align=left><input type=radio <?php print $rubriqueetudiantoui ?> name="rubriqueetudiant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueetudiantnon ?> name="rubriqueetudiant" value="non" class=btradio1  > non </td>
</tr>

<?php // -------------------------------------------------------------------------------- ?>

<tr><td align=right >Module Suppléant : </td>
<td align=left><input type=radio <?php print $moduleadminsuppleantoui ?> name="moduleadminsuppleant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminsuppleantnon ?> name="moduleadminsuppleant" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module sous-matière : </td>
<td align=left><input type=radio <?php print $moduleadminsousmatiereoui ?> name="moduleadminsousmatiere" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminsousmatierenon ?> name="moduleadminsousmatiere" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion délégués : </td>
<td align=left><input type=radio <?php print $moduleadmingestiondelegueoui ?> name="moduleadmingestiondelegue" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestiondeleguenon ?> name="moduleadmingestiondelegue" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $moduleadminplanclasseoui ?> name="moduleadminplanclasse" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminplanclassenon ?> name="moduleadminplanclasse" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Dossier médical : </td>
<td align=left><input type=radio <?php print $moduleadmindosmedicaloui ?> name="moduleadmindosmedical" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmindosmedicalnon ?> name="moduleadmindosmedical" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion dispenses : </td>
<td align=left><input type=radio <?php print $moduleadmingestiondispenseoui ?> name="moduleadmingestiondispense" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestiondispensenon ?> name="moduleadmingestiondispense" value="non" class=btradio1  > non </td>
</tr>


</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleadmingestionsavoiretreoui ?> name="moduleadmingestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestionsavoiretrenon ?> name="moduleadmingestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Sanctions du jour : </td>
<td align=left><input type=radio <?php print $moduleadminsanctiondujouroui ?> name="moduleadminsanctiondujour" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminsanctiondujournon ?> name="moduleadminsanctiondujour" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Retenues du jour : </td>
<td align=left><input type=radio <?php print $moduleadminretenudjoui ?> name="moduleadminretenudj" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminretenudjnon ?> name="moduleadminretenudj" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion discipline : </td>
<td align=left><input type=radio <?php print $moduleadmingestiondisciplineoui ?> name="moduleadmingestiondiscipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestiondisciplinenon ?> name="moduleadmingestiondiscipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Gestion étude : </td>
<td align=left><input type=radio <?php print $moduleadmingestionetudeoui ?> name="moduleadmingestionetude" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestionetudenon ?> name="moduleadmingestionetude" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Examens / Brevets : </td>
<td align=left><input type=radio <?php print $moduleadminexambrevetoui ?> name="moduleadminexambrevet" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminexambrevetnon ?> name="moduleadminexambrevet" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Fourniture scolaire : </td>
<td align=left><input type=radio <?php print $moduleadminfournitureoui ?> name="moduleadminfourniture" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminfourniturenon ?> name="moduleadminfourniture" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion S.M.S. : </td>
<td align=left><input type=radio <?php print $moduleadmingestionsmsoui ?> name="moduleadmingestionsms" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestionsmsnon ?> name="moduleadmingestionsms" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Notanet : </td>
<td align=left><input type=radio <?php print $moduleadminnotanetoui ?> name="moduleadminnotanet" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminnotanetnon ?> name="moduleadminnotanet" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module C.D.I. : </td>
<td align=left><input type=radio <?php print $moduleadmincdioui ?> name="moduleadmincdi" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmincdinon ?> name="moduleadmincdi" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Prof P. / Instituteur : </td>
<td align=left><input type=radio <?php print $moduleprofPoui ?> name="moduleadminprofP" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleprofPnon ?> name="moduleadminprofP" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Config Note USA : </td>
<td align=left><input type=radio <?php print $moduleconfignoteusaoui ?> name="moduleadminconfignoteusa" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleconfignoteusanon ?> name="moduleadminconfignoteusa" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Entretien Individuel : </td>
<td align=left><input type=radio <?php print $moduleentretienindividueloui ?> name="moduleadminentretienindividuel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleentretienindividuelnon ?> name="moduleadminentretienindividuel" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Carnet de suivi : </td>
<td align=left><input type=radio <?php print $modulecarnetsuivioui ?> name="moduleadmincarnetsuivi" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulecarnetsuivinon ?> name="moduleadmincarnetsuivi" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vérifier bulletins : </td>
<td align=left><input type=radio <?php print $moduleverifbulletinoui ?> name="moduleadminverifbulletin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleverifbulletinnon ?> name="moduleadminverifbulletin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module notes vie scolaire : </td>
<td align=left><input type=radio <?php print $modulenoteviescolaireoui ?> name="moduleadminnoteviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulenoteviescolairenon ?> name="moduleadminnoteviescolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module imprimer période : </td>
<td align=left><input type=radio <?php print $moduleadminimprperiodeoui ?> name="moduleadminimprperiode" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminimprperiodenon ?> name="moduleadminimprperiode" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Abs/Retard : </td>
<td align=left><input type=radio <?php print $moduleadminabsrtdoui ?> name="moduleadminabsrtd" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminabsrtdnon ?> name="moduleadminabsrtd" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module pré-inscription : </td>
<td align=left><input type=radio <?php print $moduleadminpreinscriptionoui ?> name="moduleadminpreinscription" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminpreinscriptionnon ?> name="moduleadminpreinscription" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module nouvelle année : </td>
<td align=left><input type=radio <?php print $moduleadminnouvelleanneeoui ?> name="moduleadminnouvelleannee" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminnouvelleanneenon ?> name="moduleadminnouvelleannee" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Archivage : </td>
<td align=left><input type=radio <?php print $moduleadminarchivageoui ?> name="moduleadminarchivage" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminarchivagenon ?> name="moduleadminarchivage" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module News défilant : </td>
<td align=left><input type=radio <?php print $moduleadminnewsdefilantoui ?> name="moduleadminnewsdefilant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminnewsdefilantnon ?> name="moduleadminnewsdefilant" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Purger infos : </td>
<td align=left><input type=radio <?php print $moduleadminpurgerinfooui ?> name="moduleadminpurgerinfo" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminpurgerinfonon ?> name="moduleadminpurgerinfo" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Eval. Enseignant : </td>
<td align=left><input type=radio <?php print $moduleadminevalensoui ?> name="moduleadminevalens" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminevalensnon ?>  name="moduleadminevalens" value="non" class=btradio1  > non </td>
</tr>




<!-------------------------------- -- -->
<tr><td colspan=2><br>
<br><br><br>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("Valeur par défaut","config_module-default.php","_parent","","") //text,nomInput</script>
<br><br>
</td></tr></table>

</td></tr></table>

</form>

<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
