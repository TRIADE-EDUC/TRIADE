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
<br><br>
<center><font class=T2>Configuration enregistrée</font></center>
<br>
<?php 
$preinscription=$_POST['preinsciption'];
$dispense=$_POST['dispense'];
$discipline=$_POST['discipline'];
$cahierdetexte=$_POST['cahierdetexte'];
$planclasse=$_POST['planclasse'];
$DST=$_POST['DST'];
$parentdispense=$_POST['parentdispense'];
$parentdiscipline=$_POST['parentdiscipline'];
$parentcahierdetexte=$_POST['parentcahierdetexte'];
$parentplanclasse=$_POST['parentplanclasse'];
$parentDST=$_POST['parentDST'];
$visudevoirprof=$_POST['visudevoirprof'];
$suppdevoirprof=$_POST['suppdevoirprof'];
$cahiertexteprof=$_POST['cahiertexteprof'];
$sanctionprof=$_POST['sanctionprof'];
$ficheeleveprof=$_POST['ficheeleveprof'];
$listeleveprof=$_POST['listeleveprof'];
$planprof=$_POST['planprof'];
$stageproprof=$_POST['stageproprof'];
$DSTProf=$_POST['DSTProf'];
$DOKEOSEleve=$_POST['DOKEOSEleve'];
$DOKEOSProf=$_POST['DOKEOSProf'];
$comptaProf=$_POST['comptaProf'];
$parentTrombinoscope=$_POST['parentTrombinoscope'];
$stageProeleve=$_POST['stageProeleve'];
$stageProparent=$_POST['stageProparent'];
$stockageprof=$_POST['stockageprof'];
$intramsnprof=$_POST['intramsnprof'];
$agendaprof=$_POST['agendaprof'];
$fluxrssprof =$_POST['fluxrssprof'];
$notesprof=$_POST['notesprof']; 
$resaprof=$_POST['resaprof']; 
$bulletinprof=$_POST['bulletinprof'];
$resaprof=$_POST['resaprof'];
$circulaireprof=$_POST['circulaireprof'];
$informationprof=$_POST['informationprof'];
$calendrierprof=$_POST['calendrierprof'];
$stockageviescolaire=$_POST['stockageviescolaire'];
$intramsnviescolaire=$_POST['intramsnviescolaire'];
$agendaviescolaire=$_POST['agendaviescolaire'];
$fluxrssviescolaire=$_POST['fluxrssviescolaire'];
$etudeviescolaire=$_POST['etudeviescolaire'];
$circulaireviescolaire=$_POST['circulaireviescolaire'];
$dstviescolaire=$_POST['dstviescolaire'];
$visaviescolaire=$_POST['visaviescolaire'];
$noteviescolaire=$_POST['noteviescolaire'];
$imptableauviescolaire=$_POST['imptableauviescolaire'];
$bulletinviescolaire=$_POST['bulletinviescolaire'];
$periodeviescolaire=$_POST['periodeviescolaire'];
$videoprojoviescolaire=$_POST['videoprojoviescolaire'];
$planclasseviescolaire=$_POST['planclasseviescolaire'];
$historyviescolaire=$_POST['historyviescolaire'];
$resaviescolaire=$_POST['resaviescolaire'];
$exportviescolaire=$_POST['exportviescolaire'];
$stageviescolaire=$_POST['stageviescolaire'];
$vacationviescolaire=$_POST['vacationviescolaire'];
$noteenseignantviascolaire=$_POST['noteenseignantviascolaire'];
$cantineviescolaire=$_POST['cantineviescolaire'];
$cantineprof=$_POST['cantineprof'];
$stockageadmin=$_POST['stockageadmin'];
$intramsnadmin=$_POST['intramsnadmin'];
$agendaadmin=$_POST['agendaadmin'];
$fluxrssadmin=$_POST['fluxrssadmin'];
$resaadmin=$_POST['resaadmin'];
$vacationadmin=$_POST['vacationadmin'];
$historyadmin=$_POST['historyadmin'];
$cantineadmin=$_POST['cantineadmin'];
$droitscolariteadmin=$_POST['droitscolariteadmin'];
$noteenseignantadmin=$_POST['noteenseignantadmin'];
$parentabsence=$_POST['parentabsence'];
$parentretard=$_POST['parentretard'];
$modulechambresvateladmin=$_POST['modulechambresvateladmin'];
$modulefinanciervateladmin=$_POST['modulefinanciervateladmin'];
$tuteurnote=$_POST['tuteurnote'];
$tuteurdiscipline=$_POST['tuteurdiscipline'];
$tuteurabs=$_POST['tuteurabs'];
$tuteurdispense=$_POST['tuteurdispense'];
$tuteurEDT=$_POST['tuteurEDT'];
$tuteurcahierdetexte=$_POST['tuteurcahierdetexte'];
$tuteurcirculaire=$_POST['tuteurcirculaire'];
$tuteurcalendrier=$_POST['tuteurcalendrier'];
$emargementprof=$_POST['emargementprof'];
$agendaparent=$_POST['agendaparent'];
$stockageparent=$_POST['stockageparent'];
$intramsnparent=$_POST['intramsnparent'];
$comptaparent=$_POST['comptaparent'];
$rssparent=$_POST['rssparent'];
$cantineparent=$_POST['cantineparent'];
$agendaeleve=$_POST['agendaeleve'];
$stockageeleve=$_POST['stockageeleve'];
$intramsneleve=$_POST['intramsneleve'];
$comptaeleve=$_POST['comptaeleve'];
$rsseleve=$_POST['rsseleve'];
$cantineeleve=$_POST['cantineeleve'];
$newspageviescolaire=$_POST['newspageviescolaire'];
$newsviescolaire=$_POST['newsviescolaire'];
$moduleboursieradmin=$_POST['moduleboursieradmin'];
$messageriescolaire=$_POST['messagerieviescolaire'];
$messagerieProf=$_POST['messagerieProf'];
$messagerieEleve=$_POST['messagerieEleve'];
$messagerieParent=$_POST['messagerieParent'];
$messagerietuteur=$_POST['messagerietuteur'];
$messagerieadmin=$_POST['messagerieadmin'];
$planningeleve=$_POST['planningeleve'];
$planningparent=$_POST['planningparent'];
$planningprof=$_POST['planningprof'];
$rubriquebulletin=$_POST['rubriquebulletin'];
$rubriqueannexe=$_POST['rubriqueannexe'];
$rubriquegestion=$_POST['rubriquegestion']; 
$rubriqueaffectation=$_POST['rubriqueaffectation'];
$rubriqueviescolaire=$_POST['rubriqueviescolaire'];
$rubriqueetablissement=$_POST['rubriqueetablissement'];
$rubriqueactualite=$_POST['rubriqueactualite'];
$rubriqueetudiant=$_POST['rubriqueetudiant'];
$moduleadminpreinscription=$_POST['moduleadminpreinscription'];
$preinscriptionviescolaire=$_POST['preinscriptionviescolaire'];
$moduleadmingestionsavoiretre=$_POST['moduleadmingestionsavoiretre'];
$moduleprofgestionsavoiretre=$_POST['moduleprofgestionsavoiretre'];
$moduleelevegestionsavoiretre=$_POST['moduleelevegestionsavoiretre'];
$moduleparentgestionsavoiretre=$_POST['moduleparentgestionsavoiretre'];
$modulebulletinvisueleve=$_POST['modulebulletinvisueleve'];
$modulebulletinvisuparent=$_POST['modulebulletinvisuparent'];
$cahierdetexteviescolaire=$_POST['cahierdetexteviescolaire'];
$cahierdetexteviescolaire=$_POST['cahierdetexteviescolaire'];
$modulebulletinvisututeurstage=$_POST['modulebulletinvisututeurstage'];
$moduleadminevalens=$_POST['moduleadminevalens'];
$radio=$_POST['radio'];
$modulefourniturescolaire=$_POST['modulefourniturescolaire'];
$moduledelegueparent=$_POST['moduledelegueparent'];

$texte="<?php\n";
$texte.="define(\"PREINSCRIPTION\",\"$preinscription\");\n";
$texte.="define(\"DISPENSE\",\"$dispense\");\n";
$texte.="define(\"DISCIPLINE\",\"$discipline\");\n";
$texte.="define(\"CAHIERDETEXTE\",\"$cahierdetexte\");\n";
$texte.="define(\"PLANCLASSE\",\"$planclasse\");\n";
$texte.="define(\"DST\",\"$DST\");\n";
$texte.="define(\"PARENTDISPENSE\",\"$parentdispense\");\n";
$texte.="define(\"PARENTDISCIPLINE\",\"$parentdiscipline\");\n";
$texte.="define(\"PARENTCAHIERDETEXTE\",\"$parentcahierdetexte\");\n";
$texte.="define(\"PARENTPLANCLASSE\",\"$parentplanclasse\");\n";
$texte.="define(\"PARENTDST\",\"$parentDST\");\n";
$texte.="define(\"VISUDEVOIRPROF\",\"$visudevoirprof\");\n";
$texte.="define(\"SUPPDEVOIRPROF\",\"$suppdevoirprof\");\n";
$texte.="define(\"CAHIERTEXTPROF\",\"$cahiertexteprof\");\n";
$texte.="define(\"SANCTIONPROF\",\"$sanctionprof\");\n";
$texte.="define(\"FICHEELEVEPROF\",\"$ficheeleveprof\");\n";
$texte.="define(\"LISTEELEVEPROF\",\"$listeleveprof\");\n";
$texte.="define(\"PLANPROF\",\"$planprof\");\n";
$texte.="define(\"STAGEPROPROF\",\"$stageproprof\");\n";
$texte.="define(\"DSTPROFACCES\",\"$DSTProf\");\n";
$texte.="define(\"DOKEOSELEVE\",\"$DOKEOSEleve\");\n";
$texte.="define(\"DOKEOSPROF\",\"$DOKEOSProf\");\n";
$texte.="define(\"COMPTAPROF\",\"$comptaProf\");\n";
$texte.="define(\"PARENTTROMBINOSCOPE\",\"$parentTrombinoscope\");\n";
$texte.="define(\"STAGEPROELEVE\",\"$stageProeleve\");\n";
$texte.="define(\"STAGEPROPARENT\",\"$stageProparent\");\n";
$texte.="define(\"STOCKAGEPROF\",\"$stockageprof\");\n";
$texte.="define(\"INTRAMSNPROF\",\"$intramsnprof\");\n";
$texte.="define(\"AGENDAMSNPROF\",\"$agendaprof\");\n";
$texte.="define(\"FLUXRSSPROF\",\"$fluxrssprof\");\n";
$texte.="define(\"NOTESPROF\",\"$notesprof\");\n";
$texte.="define(\"BULLETINPROF\",\"$bulletinprof\");\n";
$texte.="define(\"RESAPROF\",\"$resaprof\");\n";
$texte.="define(\"CIRCULAIREPROF\",\"$circulaireprof\");\n";
$texte.="define(\"INFORMATIONPROF\",\"$informationprof\");\n";
$texte.="define(\"CALENDRIERPROF\",\"$calendrierprof\");\n";
$texte.="define(\"STOCKAGEVIESCOLAIRE\",\"$stockageviescolaire\");\n";
$texte.="define(\"INTRAMSNVIESCOLAIRE\",\"$intramsnviescolaire\");\n";
$texte.="define(\"AGENDAVIESCOLAIRE\",\"$agendaviescolaire\");\n";
$texte.="define(\"FLUXRSSVIESCOLAIRE\",\"$fluxrssviescolaire\");\n";
$texte.="define(\"ETUDEVIESCOLAIRE\",\"$etudeviescolaire\");\n";
$texte.="define(\"CIRCULAIREVIESCOLAIRE\",\"$circulaireviescolaire\");\n";
$texte.="define(\"DSTVIESCOLAIRE\",\"$dstviescolaire\");\n";
$texte.="define(\"VISAVIESCOLAIRE\",\"$visaviescolaire\");\n";
$texte.="define(\"NOTEVIESCOLAIRE\",\"$noteviescolaire\");\n";
$texte.="define(\"IMPTABLEAUVIESCOLAIRE\",\"$imptableauviescolaire\");\n";
$texte.="define(\"BULLETINVIESCOLAIRE\",\"$bulletinviescolaire\");\n";
$texte.="define(\"PERIODEVIESCOLAIRE\",\"$periodeviescolaire\");\n";
$texte.="define(\"VIDEOPROJOVIESCOLAIRE\",\"$videoprojoviescolaire\");\n";
$texte.="define(\"PLANCLASSEVIESCOLAIRE\",\"$planclasseviescolaire\");\n";
$texte.="define(\"HISTORYVIESCOLAIRE\",\"$historyviescolaire\");\n";
$texte.="define(\"RESAVIESCOLAIRE\",\"$resaviescolaire\");\n";
$texte.="define(\"EXPORTVIESCOLAIRE\",\"$exportviescolaire\");\n";
$texte.="define(\"STAGEVIESCOLAIRE\",\"$stageviescolaire\");\n";
$texte.="define(\"VACATIONVIESCOLAIRE\",\"$vacationviescolaire\");\n";
$texte.="define(\"NOTEENSEIGNANTVIASCOLAIRE\",\"$noteenseignantviascolaire\");\n";
$texte.="define(\"MODULECANTINEVIESCOLAIRE\",\"$cantineviescolaire\");\n";
$texte.="define(\"MODULECANTINEPROF\",\"$cantineprof\");\n";
$texte.="define(\"STOCKAGEADMIN\",\"$stockageadmin\");\n";
$texte.="define(\"INTRAMSNADMIN\",\"$intramsnadmin\");\n";
$texte.="define(\"AGENDAADMIN\",\"$agendaadmin\");\n";
$texte.="define(\"FLUXRSSADMIN\",\"$fluxrssadmin\");\n";
$texte.="define(\"RESAADMIN\",\"$resaadmin\");\n";
$texte.="define(\"VACATIONADMIN\",\"$vacationadmin\");\n";
$texte.="define(\"HISTORYADMIN\",\"$historyadmin\");\n";
$texte.="define(\"MODULECANTINEADMIN\",\"$cantineadmin\");\n";
$texte.="define(\"DROITSCOLARITEADMIN\",\"$droitscolariteadmin\");\n";
$texte.="define(\"NOTEPROFVIAADMIN\",\"$noteenseignantadmin\");\n";
$texte.="define(\"PARENTABSENCE\",\"$parentabsence\");\n";
$texte.="define(\"PARENTRETARD\",\"$parentretard\");\n";
$texte.="define(\"MODULECHAMBRESADMIN\",\"$modulechambresvateladmin\");\n";
$texte.="define(\"MODULEFINANCIERADMIN\",\"$modulefinanciervateladmin\");\n";
$texte.="define(\"MODULETUTEURNOTE\",\"$tuteurnote\");\n";
$texte.="define(\"MODULETUTEURDISCIPLINE\",\"$tuteurdiscipline\");\n";
$texte.="define(\"MODULETUTEURABS\",\"$tuteurabs\");\n";
$texte.="define(\"MODULETUTEURDISPENSE\",\"$tuteurdispense\");\n";
$texte.="define(\"MODULETUTEUREDT\",\"$tuteurEDT\");\n";
$texte.="define(\"MODULETUTEURCAHIERDETEXTE\",\"$tuteurcahierdetexte\");\n";
$texte.="define(\"MODULETUTEURCIRCULAIRE\",\"$tuteurcirculaire\");\n";
$texte.="define(\"MODULETUTEURCALENDRIER\",\"$tuteurcalendrier\");\n";
$texte.="define(\"MODULEPROFEMARGEMENT\",\"$emargementprof\");\n";
$texte.="define(\"MODULEPARENTAGENDA\",\"$agendaparent\");\n";
$texte.="define(\"MODULEPARENTSTOCKAGE\",\"$stockageparent\");\n";
$texte.="define(\"MODULEPARENTMSN\",\"$intramsnparent\");\n";
$texte.="define(\"MODULEPARENTCOMPTA\",\"$comptaparent\");\n";
$texte.="define(\"MODULEPARENTRSS\",\"$rssparent\");\n";
$texte.="define(\"MODULEPARENTCANTINE\",\"$cantineparent\");\n";
$texte.="define(\"MODULEELEVEAGENDA\",\"$agendaeleve\");\n";
$texte.="define(\"MODULEELEVESTOCKAGE\",\"$stockageeleve\");\n";
$texte.="define(\"MODULEELEVEMSN\",\"$intramsneleve\");\n";
$texte.="define(\"MODULEELEVECOMPTA\",\"$comptaeleve\");\n";
$texte.="define(\"MODULEELEVERSS\",\"$rsseleve\");\n";
$texte.="define(\"MODULEELEVECANTINE\",\"$cantineeleve\");\n";
$texte.="define(\"MODULENEWSPAGEVIESCOLAIRE\",\"$newspageviescolaire\");\n";
$texte.="define(\"MODULENEWSVIESCOLAIRE\",\"$newsviescolaire\");\n";
$texte.="define(\"MODULEBOURSIERADMIN\",\"$moduleboursieradmin\");\n";
$texte.="define(\"MODULEMESSAGERIEADMIN\",\"$messagerieadmin\");\n";
$texte.="define(\"MODULEMESSAGERIEPROF\",\"$messagerieProf\");\n";
$texte.="define(\"MODULEMESSAGERIEELEVE\",\"$messagerieEleve\");\n";
$texte.="define(\"MODULEMESSAGERIEPARENT\",\"$messagerieParent\");\n";
$texte.="define(\"MODULEMESSAGERIETUTEUR\",\"$messagerietuteur\");\n";
$texte.="define(\"MODULEMESSAGERIESCOLAIRE\",\"$messageriescolaire\");\n";
$texte.="define(\"MODULEPREINSCRIPTIONVIESCOLAIRE\",\"$preinscriptionviescolaire\");\n";
$texte.="define(\"MODULEPLANNINGELEVE\",\"$planningeleve\");\n";
$texte.="define(\"MODULEPLANNINGPARENT\",\"$planningparent\");\n";
$texte.="define(\"MODULEPLANNINGPROF\",\"$planningprof\");\n";
$texte.="define(\"RUBRIQUEBULLETIN\",\"$rubriquebulletin\");\n";
$texte.="define(\"RUBRIQUEANNEXE\",\"$rubriqueannexe\");\n";
$texte.="define(\"RUBRIQUEGESTION\",\"$rubriquegestion\");\n";
$texte.="define(\"RUBRIQUEAFFECTATION\",\"$rubriqueaffectation\");\n";
$texte.="define(\"RUBRIQUEETABLISSEMENT\",\"$rubriqueetablissement\");\n";
$texte.="define(\"RUBRIQUEVIESCOLAIRE\",\"$rubriqueviescolaire\");\n";
$texte.="define(\"RUBRIQUEETUDIANT\",\"$rubriqueetudiant\");\n";
$texte.="define(\"RUBRIQUEACTUALITE\",\"$rubriqueactualite\");\n";
$moduleadmincdi=$_POST["moduleadmincdi"];
$moduleadminnotanet=$_POST["moduleadminnotanet"];
$moduleadmingestionsms=$_POST["moduleadmingestionsms"];
$moduleadminreservequip=$_POST["moduleadminreservequip"];
$moduleadminreservsalle=$_POST["moduleadminreservsalle"];
$moduleadminfourniture=$_POST["moduleadminfourniture"];
$moduleadminexambrevet=$_POST["moduleadminexambrevet"];
$moduleadmingestionetude=$_POST["moduleadmingestionetude"];
$moduleadmingestiondiscipline=$_POST["moduleadmingestiondiscipline"];
$moduleadminretenudj=$_POST["moduleadminretenudj"];
$moduleadminsanctiondujour=$_POST["moduleadminsanctiondujour"];
$moduleadmingestiondispense=$_POST["moduleadmingestiondispense"];
$moduleadmindosmedical=$_POST["moduleadmindosmedical"];
$moduleadminplanclasse=$_POST["moduleadminplanclasse"];
$moduleadmingestiondelegue=$_POST["moduleadmingestiondelegue"];
$moduleadminsousmatiere=$_POST["moduleadminsousmatiere"];
$moduleadminsuppleant=$_POST["moduleadminsuppleant"];
$moduleadminabsrtd=$_POST["moduleadminabsrtd"];
$texte.="define(\"MODULEADMINCDI\",\"$moduleadmincdi\");\n";
$texte.="define(\"MODULEADMINNOTANET\",\"$moduleadminnotanet\");\n";
$texte.="define(\"MODULEADMINGESTIONSMS\",\"$moduleadmingestionsms\");\n";
$texte.="define(\"MODULEADMINRESERVEQUIP\",\"$moduleadminreservequip\");\n";
$texte.="define(\"MODULEADMINRESERVSALLE\",\"$moduleadminreservsalle\");\n";
$texte.="define(\"MODULEADMINFOURNITURE\",\"$moduleadminfourniture\");\n";
$texte.="define(\"MODULEADMINEXAMBREVET\",\"$moduleadminexambrevet\");\n";
$texte.="define(\"MODULEADMINGESTIONETUDE\",\"$moduleadmingestionetude\");\n";
$texte.="define(\"MODULEADMINGESTIONDISCIPLINE\",\"$moduleadmingestiondiscipline\");\n";
$texte.="define(\"MODULEADMINRETENUDJ\",\"$moduleadminretenudj\");\n";
$texte.="define(\"MODULEADMINSANCTIONDUJOUR\",\"$moduleadminsanctiondujour\");\n";
$texte.="define(\"MODULEADMINGESTIONDISPENSE\",\"$moduleadmingestiondispense\");\n";
$texte.="define(\"MODULEADMINDOSMEDICAL\",\"$moduleadmindosmedical\");\n";
$texte.="define(\"MODULEADMINPLANCLASSE\",\"$moduleadminplanclasse\");\n";
$texte.="define(\"MODULEADMINGESTIONDELEGUE\",\"$moduleadmingestiondelegue\");\n";
$texte.="define(\"MODULEADMINSOUSMATIERE\",\"$moduleadminsousmatiere\");\n";
$texte.="define(\"MODULEADMINSUPPLEANT\",\"$moduleadminsuppleant\");\n";
$moduleadminprofP=$_POST["moduleadminprofP"];
$moduleadminconfignoteusa=$_POST["moduleadminconfignoteusa"];
$moduleadminentretienindividuel=$_POST["moduleadminentretienindividuel"];
$moduleadmincarnetsuivi=$_POST["moduleadmincarnetsuivi"];
$moduleadminverifbulletin=$_POST["moduleadminverifbulletin"];
$moduleadminnoteviescolaire=$_POST["moduleadminnoteviescolaire"];
$moduleadminimprperiode=$_POST["moduleadminimprperiode"];
$texte.="define(\"MODULEADMINPROFP\",\"$moduleadminprofP\");\n";
$texte.="define(\"MODULEADMINCONFIGNOTEUSA\",\"$moduleadminconfignoteusa\");\n";
$texte.="define(\"MODULEADMINENTRETIENINDIVIDUEL\",\"$moduleadminentretienindividuel\");\n";
$texte.="define(\"MODULEADMINCARNETSUIVI\",\"$moduleadmincarnetsuivi\");\n";
$texte.="define(\"MODULEADMINVERIFBULLETIN\",\"$moduleadminverifbulletin\");\n";
$texte.="define(\"MODULEADMINNOTEVIESCOLAIRE\",\"$moduleadminnoteviescolaire\");\n";
$texte.="define(\"MODULEADMINIMPRPERIODE\",\"$moduleadminimprperiode\");\n";
$texte.="define(\"MODULEADMINABSRTD\",\"$moduleadminabsrtd\");\n";
$texte.="define(\"MODULEADMINPREINSCRIPTION\",\"$moduleadminpreinscription\");\n";
$moduleadminnouvelleannee=$_POST["moduleadminnouvelleannee"];
$moduleadminarchivage=$_POST["moduleadminarchivage"];
$moduleadminnewsdefilant=$_POST["moduleadminnewsdefilant"];
$moduleadminpurgerinfo=$_POST["moduleadminpurgerinfo"];
$texte.="define(\"MODULEADMINNOUVELLEANNEE\",\"$moduleadminnouvelleannee\");\n";
$texte.="define(\"MODULEADMINARCHIVAGE\",\"$moduleadminarchivage\");\n";
$texte.="define(\"MODULEADMINNEWSDEFILANT\",\"$moduleadminnewsdefilant\");\n";
$texte.="define(\"MODULEADMINPURGERINFO\",\"$moduleadminpurgerinfo\");\n";
$CDIEleve=$_POST["CDIEleve"];
$texte.="define(\"MODULEELEVECDI\",\"$CDIEleve\");\n";
$texte.="define(\"MODULEADMINGESTIONSAVOIRETRE\",\"$moduleadmingestionsavoiretre\");\n";
$texte.="define(\"MODULEPROFGESTIONSAVOIRETRE\",\"$moduleprofgestionsavoiretre\");\n";
$texte.="define(\"MODULEELEVEGESTIONSAVOIRETRE\",\"$moduleelevegestionsavoiretre\");\n";
$texte.="define(\"MODULEPARENTGESTIONSAVOIRETRE\",\"$moduleparentgestionsavoiretre\");\n";
$texte.="define(\"MODULEBULLETINVISUELEVE\",\"$modulebulletinvisueleve\");\n";
$texte.="define(\"MODULEBULLETINVISUPARENT\",\"$modulebulletinvisuparent\");\n";
$moduletuteursavoiretre=$_POST["moduletuteursavoiretre"];
$moduleviescolairesavoiretre=$_POST["moduleviescolairesavoiretre"];
$texte.="define(\"MODULETUTEURGESTIONSAVOIRETRE\",\"$moduletuteursavoiretre\");\n";
$texte.="define(\"MODULEVIESCOLAIREGESTIONSAVOIRETRE\",\"$moduleviescolairesavoiretre\");\n";
$texte.="define(\"VIESCOLAIRECAHIERDETEXTE\",\"$cahierdetexteviescolaire\");\n";
$chambreviescolaire=$_POST["chambreviescolaire"];
$texte.="define(\"MODULECHAMBRESVIESCOLAIRE\",\"$chambreviescolaire\");\n";
$texte.="define(\"MODULEBULLETINVISUTUTEUR\",\"$modulebulletinvisututeurstage\");\n";
$texte.="define(\"MODULEADMINEVALENS\",\"$moduleadminevalens\");\n";
$texte.="define(\"MODULERADIO\",\"$radio\");\n";
$texte.="define(\"MODULEFOURNITURESCOLAIRE\",\"$modulefourniturescolaire\");\n";
$texte.="define(\"MODULEDELEGUEPARENT\",\"$moduledelegueparent\");\n";


$texte.="?>\n";

$fp=fopen("../common/config-module.php","wb");
fwrite($fp,"$texte");
fclose($fp);

?>

</td></tr></table>


<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
