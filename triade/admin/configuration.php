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
if (@file_exists("../../../../common/config-all-site.php")) {
	include_once('../../../../common/config-all-site.php'); 
}
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Configuration de Triade</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
include_once("../librairie_php/lib_get_init.php");
$sendmail=1;
//$sendmail=php_module_load("sendmail");
$checkoui="";
$checknon="";
$selected="";
$checkmessoui="";
$checkmessnon="";
$checkpubhautoui="";
$checkpubhautnon="";
$uploadimgoui="";
$uploadimgnon="";
$grpmailparentoui="";
$grpmailparentnon="";
$meteooui="";
$meteonon="";
$lanoui="";
$lannon="";
$proxyoui="";
$proxynon="";
$dstprofoui="";
$dstprofnon="";
$calprofoui="";
$calprofnon="";
$ferie="";
$forum="";
$noteusaoui="";
$noteusanon="";
$mailreply="";
$traceoui="";
$tracenon="";
$resvprofoui="";
$resvprofnon="";
$retenuprofoui="";
$retenuprofnon="";
$absprofoui="";
$absprofnon="";
$noteprof1="";
$noteprof2="";
$noteprof3="";
$pwdoui="";
$pwdnon="";
$audiooui="";
$audionon="";
$feteoui="";
$fetenon="";
$infomedicoui="";
$infomedicnon="";
$infomedicoui2="";
$infomedicnon2="";
$accesstockageoui="";
$accesstockagenon="";
$accesstockageprofoui="";
$accesstockageprofnon="";
$accesstockageparentoui="";
$accesstockageparentnon="";
$accesstockagecpeoui="";
$accesstockagecpenon="";
$accesstockageeleveoui="";
$accesstockageelevenon="";
$securite1="";
$securite2="";
$securite3="";
$tailleupload2="";
$tailleupload8="";
$mailmessoui="";
$mailmessnon="";
$verifpassnon="";
$verifpassoui="";
$supppassmailoui="";
$supppassmailnon="";
$mailexterneoui="";
$mailexternenon="";
$noteeleveoui="";
$noteelevenon="";
$accesmesseleveoui="";
$accesmesselevenon="";
$accesmessparentoui="";
$accesmessparentnon="";
$noteexamenoui="";
$noteexamennon="";
$cmpsocialoui="";
$cmpsocialnon="";
$aniversaireoui="";
$aniversairenon="";
$dstvisuaccueiloui="";
$dstvisuaccueilnon="";
$accesforumprofnon="";
$accesforumprofoui="";
$accesforumelevenon="";
$accesforumeleveoui="";
$accesforumparentnon="";
$accesforumparentoui="";
$pwdparentnon="";
$pwdparentoui="";
$pwdprofnon="";
$pwdprofoui="";
$pwdelevenon="";
$pwdeleveoui="";
$accesnoteeleveoui="";
$accesnoteelevenon="";
$accesnoteparentoui="";
$accesnoteparentnon="";
$accesmessenvoieleveoui="";
$accesmessenvoielevenon="";
$accesmessenvoiparentoui="";
$accesmessenvoiparentnon="";
$calsamediapoui="";
$calsamediapnon="";
$calsamedimatinoui="";
$calsamedimatinnon="";
$calmercrediapoui="";
$calmercrediapnon="";
$calmercredimatinoui="";
$calmercredimatinnon="";
$trombinoParentoui="";
$trombinoParentnon="";
$trombinoEleveoui="";
$trombinoElevenon="";
$messagedelegueparentoui="";
$messagedelegueparentnon="";
$messagedelegueeleveoui="";
$messagedelegueelevenon="";
$profpbulletinoui="";
$profpbulletinnon="";
$profpreleveoui="";
$profprelevenon="";
$profpbulletinverifoui="";
$profpbulletinverifnon="";
$accesabsrtdprofoui="";
$accesabsrtdprofnon="";
$httpsnon="";
$httpsoui="";
$hd_edt="";
$hf_edt="";
$mf_edt="";
$absvisuprofoui="";
$absvisuprofnon="";
$visutriautooui="";
$visutriautonon="";
$emailchangeeleveoui="";
$emailchangeelevenon="";
$agendadirectoui="";
$agendadirectnon="";
$groupeclasseprofpoui="";
$groupeclasseprofpnon="";
$agentweboui="";
$agentwebnon="";
$bit32="";
$bit64="";
$eleveenvoieleveoui="";
$eleveenvoielevenon="";
$eleveenvoiprofoui="";
$eleveenvoiprofnon="";
$eleveenvoiparentoui="";
$eleveenvoiparentnon="";
$eleveenvoituteuroui="";
$eleveenvoituteurnon="";
$eleveenvoidirecoui="";
$eleveenvoidirecnon="";
$eleveenvoiscolaireoui="";
$eleveenvoiscolairenon="";
$parentenvoieleveoui="";
$parentenvoielevenon="";
$parentenvoiprofoui="";
$parentenvoiprofnon="";
$parentenvoiparentoui="";
$parentenvoiparentnon="";
$parentenvoituteuroui="";
$parentenvoituteurnon="";
$parentenvoidirecoui="";
$parentenvoidirecnon="";
$parentenvoiscolaireoui="";
$parentenvoiscolairenon="";
$profenvoiprofoui="";
$profenvoiprofnon="";
$profenvoituteuroui="";
$profenvoituteurnon="";
$profenvoigroupeoui="";
$profenvoigroupenon="";
$profenvoiparentoui="";
$profenvoiparentnon="";
$profenvoieleveoui="";
$profenvoielevenon="";
$profenvoidirecoui="";
$profenvoidirecnon="";
$profenvoiscolaireoui="";
$profenvoiscolairenon="";
$edtvisuoui="";
$edtvisunon="";
$examenblancnon="";
$examenblancoui="";
$examendsnon="";
$examendsoui="";
$examennamurnon="";
$examennamuroui="";
$examenkinshasanon="";
$examenkinshasaoui="";
$examenismapnon="";
$examenismapoui="";
$examencieformationoui="";
$examencieformationnon="";
$exameneeppoui="";
$exameneeppnon="";
$examennon="";
$examenoui="";
$profenvoiextoui="";
$profenvoiextnon="";
$parentenvoiextoui="";
$parentenvoiextnon="";
$eleveenvoiextoui="";
$eleveenvoiextnon="";
$verifsujetnoteoui="";
$verifsujetnotenon="";
$nomaniversaireoui="";
$nomaniversairenon="";
$profenvoigrpelevoui="";
$profenvoigrpelevnon="";
$profenvoidelegueoui="";
$profenvoideleguenon="";
$parentenvoigrpelevoui="";
$parentenvoigrpelevnon="";
$parentenvoidelegueoui="";
$parentenvoideleguenon="";
$eleveenvoigrpelevoui="";
$eleveenvoigrpelevnon="";
$eleveenvoidelegueoui="";
$eleveenvoideleguenon="";
$phpconfigmagicon="";
$phpconfigmagicoff="";
$phpconfigmagicauto="";
$combulltypeoui="";
$combulltypenon="";
$semainedimancheoui="";
$semainedimanchenon="";
$tvavatationoui="";
$tvavatationnon="";
$autocompletionloginoui="";
$autocompletionloginnon="";
$civarmeeoui="";
$civarmeenon="";
$edtdirectoui="";
$edtdirectnon="";
$viescolairehistocmdoui="";
$viescolairehistocmdnon="";
$agendapdaoui="";
$agendapdanon="";
$passmoduleindividueloui="";
$passmoduleindividuelnon="";
$passmodulemedicaloui="";
$passmodulemedicalnon="";
$parentenvoipersonneloui="";
$parentenvoipersonnelnon="";
$profenvoipersonneloui="";
$profenvoipersonnelnon="";
$eleveenvoipersonneloui="";
$eleveenvoipersonnelnon="";
$personnelenvoiprofoui="";
$personnelenvoiprofnon="";
$personnelenvoiparentoui="";
$personnelenvoiparentnon="";
$personnelenvoieleveoui="";
$personnelenvoielevenon="";
$personnelenvoiextoui="";
$personnelenvoiextnon="";
$personnelenvoigrpelevoui="";
$personnelenvoigrpelevnon="";
$planclasseParentoui="";
$planclasseParentnon="";
$gestionentrepriseprofpoui="";
$gestionentrepriseprofpnon="";
$viescolairestagedateoui="";
$viescolairestagedatenon="";
$viescolairestageentoui="";
$viescolairestageentnon="";
$viescolairestageetudiantoui="";
$viescolairestageetudiantnon="";
$profentroui="";
$profentrnon="";
$profstageetudiantnon="";
$profstageetudiantoui="";
$createntrelevenon="";
$createntreleveoui="";
$createntrparentnon="";
$createntrparentoui="";
$viescolairenoteenseignantoui="";
$viescolairenoteenseignantnon="";
$examenpigiernimesnon="";
$examenpigiernimesoui="";
$examenispacademiesnon="";
$examenispacademiesoui="";
$modulefinanciervateloui="";
$modulefinanciervatelnon="";
$profentrconventionoui="";
$profentrconventionnon="";
$profpentrconventionoui="";
$profpentrconventionnon="";
$profpcreatetuteuroui="";
$profpcreatetuteurnon="";
$presentprofoui="";
$presentprofnon="";
$modulenote20oui="";
$modulenote20non="";
$modulenote15oui="";
$modulenote15non="";
$modulenote10oui="";
$modulenote10non="";
$modulenote5oui="";
$modulenote5non="";
$modulenote6oui="";
$modulenote6non="";
$modulenote30oui="";
$modulenote30non="";
$modulenote40oui="";
$modulenote40non="";
$semainevendredioui="";
$semainevendredinon="";
$absprofmotifoui="";
$absprofmotifnon="";
$accesmesstuteuroui="";
$accesmesstuteurnon="";
$accesmessenvoituteuroui="";
$accesmessenvoituteurnon="";
$tuteurenvoiprofoui="";
$tuteurenvoiprofnon="";
$tuteurenvoituteuroui="";
$tuteurenvoituteurnon="";
$tuteurenvoiparentoui="";
$tuteurenvoiparentnon="";
$tuteurenvoieleveoui="";
$tuteurenvoielevenon="";
$tuteurenvoidirecoui="";
$tuteurenvoidirecnon="";
$tuteurenvoiscolaireoui="";
$tuteurenvoiscolairenon="";
$tuteurenvoiextoui="";
$tuteurenvoiextnon="";
$tuteurenvoigrpelevoui="";
$tuteurenvoigrpelevnon="";
$tuteurenvoidelegueoui="";
$tuteurenvoideleguenon="";
$tuteurenvoipersonneloui="";
$tuteurenvoipersonnelnon="";
$choixmatiereprofoui="";
$choixmatiereprofnon="";
$carnetsuiviprofoui="";
$carnetsuiviprofnon="";
$tombiallclasseprofoui="";
$tombiallclasseprofnon="";
$passmodulebilanfinancieroui="";
$passmodulebilanfinanciernon="";
$moduleeLearningdoekeos="";
$moduleeLearningmoodle="";
$intitule_direction="";
$passoublieoui="";
$passoublienon="";
$dircahiertextenon="";
$dircahiertexteoui="";
$viescolairemodifetudiantoui="";
$viescolairemodifetudiantnon="";
$modifnoteapresarretoui="";
$modifnoteapresarretoui="";
$titrebanniere="";
$profpVideoProjooui="";
$profpVideoProjonon="";
$profpmodifaffectoui="";
$profpmodifaffectnon="";
$profpaccesnoteoui="";
$profpaccesnotenon="";
$checkmesshorinon="";
$checkmesshorioui="";
$autoINEoui="";
$autoINEnon="";

$examenjtcoui="";
$examenjtcnon="";

$entretienprofoui="";
$entretienprofnon="";

$autodateabsrtdoui="";
$autodateabsrtdnon="";

$profpaccesvisadirectionnon="";
$profpaccesvisadirectionoui="";
$profpaccesabsrtdnon="";
$profpaccesabsrtdoui="";

$affichageVatelnon="";
$affichageVateloui="";
$examenvatelreunionnon="";
$examenvatelreunionoui="";

$affichageIAOui="";
$affichageIANon="";

$fichier="../common/config2.inc.php";
if ( file_exists($fichier)) {
	if ( file_exists("../common/config6.inc.php")) {
		include_once("../common/config6.inc.php");
		if (defined("MAXUPLOAD")) {
			if (MAXUPLOAD == "oui") {
				$tailleupload8="checked='checked'";
			}else{
				$tailleupload2="checked='checked'";
			}
		}
	}else {
		$tailleupload2="checked";
	}
	if ( file_exists("../common/config5.inc.php")) {
		include_once("../common/config5.inc.php");
		$charset=CHARSET;
	}else{
		$charset="iso-8859-1";
	}
	include_once("../common/config2.inc.php");
	if (defined("FERIE")){$ferie=FERIE;}
	if (defined("FORUM")){$forum=FORUM;}
	if (defined("MAILREPLY")){$mailreply=MAILREPLY;}
	if (defined("MAILADMIN")){$mailadmin=MAILADMIN;}
	if (defined("MAILADMIN2")){$mailadmin2=MAILADMIN2;}
	if (defined("MAILCONTACT")){$mailcontact=MAILCONTACT;}
	if (defined("MAILNOMREPLY")){$mailnomreply=MAILNOMREPLY;}
	if (defined("URLNOMCONTACT")){$nomdulien=URLNOMCONTACT;}
	if ((defined("URLCONTACT")) && (URLCONTACT != "")) {
		$addressedulien=URLCONTACT;
	}else{
		$addressedulien="http://";
	}
	if (defined("URLNOMCONTACT2")){$nomdulien2=URLNOMCONTACT2;}
	if ((defined("URLCONTACT2")) && (URLCONTACT2 != "")) {
		$addressedulien2=URLCONTACT2;
	}else{
		$addressedulien2="http://";
	}
	if (defined("URLNOMCONTACT3")){$nomdulien3=URLNOMCONTACT3;}
	if ((defined("URLCONTACT3")) && (URLCONTACT3 != "")) {
		$addressedulien3=URLCONTACT3;
	}else{
		$addressedulien3="http://";
	}
	if (defined("URLNOMCONTACT4")){$nomdulien4=URLNOMCONTACT4;}
	if ((defined("URLCONTACT4")) && (URLCONTACT4 != "")) {
		$addressedulien4=URLCONTACT4;
	}else{
		$addressedulien4="http://";
	}
	if (LAN == "non") {$lannon="checked='checked'";}
	if (LAN == "oui") {$lanoui="checked='checked'";}
	if (PROXY == "non") {$proxynon="checked='checked'";}
	if (PROXY == "oui") {$proxyoui="checked='checked'";}
	if (FORWARDMAIL == "oui") {$checkoui="checked='checked'";}
	if (FORWARDMAIL == "non") {$checknon="checked='checked'";}
	if (MESSDEFIL == "oui") {$checkmessoui="checked='checked'";}
	if (MESSDEFIL == "non") {$checkmessnon="checked='checked'";}
	if (PUBHAUT == "oui") {$checkpubhautoui="checked='checked'";}
	if (PUBHAUT == "non") {$checkpubhautnon="checked='checked'";}
	if (UPLOADIMG == "oui") {$uploadimgoui="checked='checked'";}
	if (UPLOADIMG == "non") {$uploadimgnon="checked='checked'";}
	if (GRPMAILPARENT == "oui") {$grpmailparentoui="checked='checked'";}
	if (GRPMAILPARENT == "non") {$grpmailparentnon="checked='checked'";}
	if (METEOVALIDE == "oui") {$meteooui="checked='checked'";}
	if (METEOVALIDE == "non") {$meteonon="checked='checked'";}
	if (DSTPROF == "oui") {$dstprofoui="checked='checked'";}
	if (DSTPROF == "non") {$dstprofnon="checked='checked'";}
	if (CALPROF == "oui") {$calprofoui="checked='checked'";}
	if (CALPROF == "non") {$calprofnon="checked='checked'";}
	if (NOTEUSA == "non") {$noteusanon="checked='checked'";}
	if (NOTEUSA == "oui") {$noteusaoui="checked='checked'";}
	if (TRACE == "non") {$tracenon="checked='checked'";}
	if (TRACE == "oui") {$traceoui="checked='checked'";}
    	if (RESERV == "oui") {$resvprofoui="checked='checked'";}
	if (RESERV == "non") {$resvprofnon="checked='checked'";}
	if (RETENUPROF == "oui") {$retenuprofoui="checked='checked'";}
	if (RETENUPROF == "non") {$retenuprofnon="checked='checked'";}
	if (ABSPROF == "oui") {$absprofoui="checked='checked'";}
	if (ABSPROF == "non") {$absprofnon="checked='checked'";}
	if (NOTEPROF == "1") { $noteprof1="checked='checked'";}
	if (NOTEPROF == "2") { $noteprof2="checked='checked'";}
	if (NOTEPROF == "3") { $noteprof3="checked='checked'";}
	if (VALIDPWD == "oui") {$pwdoui="checked='checked'";}
	if (VALIDPWD == "non") {$pwdnon="checked='checked'";}
	if (AUDIO == "oui") {$audiooui="checked='checked'";}
	if (AUDIO == "non") {$audionon="checked='checked'";}
	if (FETE == "oui") {$feteoui="checked='checked'";}
	if (FETE == "non") {$fetenon="checked='checked'";}
	if (INFOMEDIC == "oui") { $infomedicoui="checked='checked'"; }
	if (INFOMEDIC == "non") { $infomedicnon="checked='checked'"; }
	if (INFOMEDIC2 == "oui") { $infomedic2oui="checked='checked'"; }
	if (INFOMEDIC2 == "non") { $infomedic2non="checked='checked'"; }
	if (ACCESSTOCKAGE == "oui") { $accesstockageoui="checked='checked'"; }
	if (ACCESSTOCKAGE == "non") { $accesstockagenon="checked='checked'"; }
	if (VERIFPASS == "oui") { $verifpassoui="checked='checked'"; }
	if (VERIFPASS == "non") { $verifpassnon="checked='checked'"; }
	if (SUPPPASSMAIL == "oui") { $supppassmailoui="checked='checked'"; }
	if (SUPPPASSMAIL == "non") { $supppassmailnon="checked='checked'"; }
	if (MAILEXTERNE == "non") { $mailexternenon="checked='checked'"; }
	if (MAILEXTERNE == "oui") { $mailexterneoui="checked='checked'"; }
	if (NOTEELEVEVISU == "non") { $infonoteelevenon="checked='checked'"; }
	if (NOTEELEVEVISU == "oui") { $infonoteeleveoui="checked='checked'"; }
	if (NOTEEXAMEN == "non") { $noteexamennon="checked='checked'"; }
	if (NOTEEXAMEN == "oui") { $noteexamenoui="checked='checked'"; }
	if (DSTVISUACCUEIL == "non") { $dstvisuaccueilnon="checked='checked'"; }
	if (DSTVISUACCUEIL == "oui") { $dstvisuaccueiloui="checked='checked'"; }
	if (ACCESSTOCKAGEELEVE == "oui") { $accesstockageeleveoui="checked='checked'"; }
	if (ACCESSTOCKAGEELEVE == "non") { $accesstockageelevenon="checked='checked'"; }
	if (ACCESSTOCKAGEPARENT == "oui") { $accesstockageparentoui="checked='checked'"; }
	if (ACCESSTOCKAGEPARENT == "non") { $accesstockageparentnon="checked='checked'"; }
	if (ACCESSTOCKAGEPROF == "oui") { $accesstockageprofoui="checked='checked'"; }
	if (ACCESSTOCKAGEPROF == "non") { $accesstockageprofnon="checked='checked'"; }
	if (ACCESSTOCKAGECPE == "oui") { $accesstockagecpeoui="checked='checked'"; }
	if (ACCESSTOCKAGECPE == "non") { $accesstockagecpenon="checked='checked'"; }
	if (ACCESFORUMPARENT == "non") { $accesforumparentnon="checked='checked'"; }
	if (ACCESFORUMPARENT == "oui") { $accesforumparentoui="checked='checked'"; }
	if (ACCESFORUMPROF == "non") { $accesforumprofnon="checked='checked'"; }
	if (ACCESFORUMPROF == "oui") { $accesforumprofoui="checked='checked'"; }
	if (ACCESFORUMELEVE == "non") { $accesforumelevenon="checked='checked'"; }
	if (ACCESFORUMELEVE == "oui") { $accesforumeleveoui="checked='checked'"; }
	if (PWDELEVE == "oui") { $pwdeleveoui="checked='checked'"; }
	if (PWDELEVE == "non") { $pwdelevenon="checked='checked'"; }
	if (PWDPROF == "oui") { $pwdprofoui="checked='checked'"; }
	if (PWDPROF == "non") { $pwdprofnon="checked='checked'"; }
	if (PWDPARENT == "oui") { $pwdparentoui="checked='checked'"; }
	if (PWDPARENT == "non") { $pwdparentnon="checked='checked'"; }
	if (ACCESMESSPARENT == "non") { $accesmessparentnon="checked='checked'"; }
	if (ACCESMESSPARENT == "oui") { $accesmessparentoui="checked='checked'"; }
	if (ACCESMESSELEVE == "non") { $accesmesselevenon="checked='checked'"; }
	if (ACCESMESSELEVE == "oui") { $accesmesseleveoui="checked='checked'"; }
	if (ACCESNOTEPARENT == "non") { $accesnoteparentnon="checked='checked'"; }
	if (ACCESNOTEPARENT == "oui") { $accesnoteparentoui="checked='checked'"; }
	if (ACCESNOTEELEVE == "non") { $accesnoteelevenon="checked='checked'"; }
	if (ACCESNOTEELEVE == "oui") { $accesnoteeleveoui="checked='checked'"; }
	if (MODNAMUR0 == "non") { $cmpsocialnon="checked='checked'"; }
	if (MODNAMUR0 == "oui") { $cmpsocialoui="checked='checked'"; }
	if (ANI == "non") { $aniversairenon="checked='checked'"; }
	if (ANI == "oui") { $aniversaireoui="checked='checked'"; }
	if (MAILMESS == "non") { $mailmessnon="checked='checked'"; }
	if (MAILMESS == "oui") { $mailmessoui="checked='checked'"; }
	if (SECURITE == "1") { $securite1="checked='checked'"; }
	if (SECURITE == "2") { $securite2="checked='checked'"; }
	if (SECURITE == "3") { $securite3="checked='checked'"; }
	if (ACCESMESSENVOIPARENT == "non") { $accesmessenvoiparentnon="checked='checked'"; }
	if (ACCESMESSENVOIPARENT == "oui") { $accesmessenvoiparentoui="checked='checked'"; }
	if (ACCESMESSENVOIELEVE == "non") { $accesmessenvoielevenon="checked='checked'"; }
	if (ACCESMESSENVOIELEVE == "oui") { $accesmessenvoieleveoui="checked='checked'"; }
	if (CALSAMEDIMATIN == "non") { $calsamedimatinnon="checked='checked'"; }
	if (CALSAMEDIMATIN == "oui") { $calsamedimatinoui="checked='checked'"; }
	if (CALSAMEDIAP == "non") { $calsamediapnon="checked='checked'"; }
	if (CALSAMEDIAP == "oui") { $calsamediapoui="checked='checked'"; }
	if (CALMERCREDIMATIN == "non") { $calmercredimatinnon="checked='checked'"; }
	if (CALMERCREDIMATIN == "oui") { $calmercredimatinoui="checked='checked'"; }
	if (CALMERCREDIAP == "non") { $calmercrediapnon="checked='checked'"; }
	if (CALMERCREDIAP == "oui") { $calmercrediapoui="checked='checked'"; }
	if (TROMBIPARENT == "oui") { $trombinoParentoui="checked='checked'"; }
	if (TROMBIPARENT == "non") { $trombinoParentnon="checked='checked'"; }
	if (TROMBIELEVE == "oui") { $trombinoEleveoui="checked='checked'"; }
	if (TROMBIELEVE == "non") { $trombinoElevenon="checked='checked'"; }
	if (MESSDELEGUEPARENT == "oui") { $messagedelegueparentoui="checked='checked'"; }
	if (MESSDELEGUEPARENT == "non") { $messagedelegueparentnon="checked='checked'"; }
	if (MESSDELEGUEELEVE == "oui") { $messagedelegueeleveoui="checked='checked'"; }
	if (MESSDELEGUEELEVE == "non") { $messagedelegueelevenon="checked='checked'"; }
	if (HTTPS == "oui") { $httpsoui="checked='checked'"; }
	if (HTTPS == "non") { $httpsnon="checked='checked'"; }
	if (PROFPBULLETIN == "oui") { $profpbulletinoui="checked='checked'"; }
	if (PROFPBULLETIN == "non") { $profpbulletinnon="checked='checked'"; }
	if (PROFPBULLETINVERIF == "oui") { $profpbulletinverifoui="checked='checked'"; }
	if (PROFPBULLETINVERIF == "non") { $profpbulletinverifnon="checked='checked'"; }
	if (PROFPRELEVE == "oui") { $profpreleveoui="checked='checked'"; }
	if (PROFPRELEVE == "non") { $profprelevenon="checked='checked'"; }
	if (ACCESPROFABSRTD == "oui") { $accesabsrtdprofoui="checked='checked'"; }
	if (ACCESPROFABSRTD == "non") { $accesabsrtdprofnon="checked='checked'"; }
	if (ACCESPROFVISUABSRTD == "oui") { $absvisuprofoui="checked='checked'"; }
	if (ACCESPROFVISUABSRTD == "non") { $absvisuprofnon="checked='checked'"; }
	if (VISUTRIAUTO == "oui") { $visutriautooui="checked='checked'"; }
	if (VISUTRIAUTO == "non") { $visutriautonon="checked='checked'"; }
	if (EMAILCHANGEELEVE == "oui") { $emailchangeeleveoui="checked='checked'"; }
	if (EMAILCHANGEELEVE == "non") { $emailchangeelevenon="checked='checked'"; }
	if (AGENDADIRECT == "oui") { $agendadirectoui="checked='checked'"; }
	if (AGENDADIRECT == "non") { $agendadirectnon="checked='checked'"; }
	if (GROUPEGESTIONPROF == "oui") { $groupeclasseprofpoui="checked='checked'"; }
	if (GROUPEGESTIONPROF == "non") { $groupeclasseprofpnon="checked='checked'"; }
	if (ARCHBIT == "32") { $bit32="checked='checked'"; }
	if (ARCHBIT == "64") { $bit64="checked='checked'"; }
	if (AGENTWEB == "oui") { $agentweboui="checked='checked'"; }
	if (AGENTWEB == "non") { $agentwebnon="checked='checked'"; }
	if (ELEVEENVOIELEVE == "oui")    { $eleveenvoieleveoui="checked='checked'"; }
	if (ELEVEENVOIELEVE == "non")    { $eleveenvoielevenon="checked='checked'"; }
	if (ELEVEENVOIPROF == "oui")     { $eleveenvoiprofoui="checked='checked'"; }
	if (ELEVEENVOIPROF == "non")     { $eleveenvoiprofnon="checked='checked'"; }
	if (ELEVEENVOIPARENT == "oui")   { $eleveenvoiparentoui="checked='checked'"; }
	if (ELEVEENVOIPARENT == "non")   { $eleveenvoiparentnon="checked='checked'"; }
	if (ELEVEENVOITUTEUR == "oui")   { $eleveenvoituteuroui="checked='checked'"; }
	if (ELEVEENVOITUTEUR == "non")   { $eleveenvoituteurnon="checked='checked'"; }
	if (ELEVEENVOIDIREC == "oui")    { $eleveenvoidirecoui="checked='checked'"; }
	if (ELEVEENVOIDIREC == "non")    { $eleveenvoidirecnon="checked='checked'"; }
	if (ELEVEENVOISCOLAIRE == "non") { $eleveenvoiscolairenon="checked='checked'"; }
	if (ELEVEENVOISCOLAIRE == "oui") { $eleveenvoiscolaireoui="checked='checked'"; }
	if (ELEVEENVOIEXT == "non") 	 { $eleveenvoiextnon="checked='checked'"; }
	if (ELEVEENVOIEXT == "oui") 	 { $eleveenvoiextoui="checked='checked'"; }
	if (PROFENVOIPROF == "oui")   { $profenvoiprofoui="checked='checked'"; }
	if (PROFENVOIPROF == "non")   { $profenvoiprofnon="checked='checked'"; }
	if (PROFENVOITUTEUR == "oui") { $profenvoituteuroui="checked='checked'"; }
	if (PROFENVOITUTEUR == "non") { $profenvoituteurnon="checked='checked'"; }
	if (PROFENVOIGROUPE == "oui") { $profenvoigroupeoui="checked='checked'"; }
	if (PROFENVOIGROUPE == "non") { $profenvoigroupenon="checked='checked'"; }
	if (PROFENVOIPARENT == "oui") { $profenvoiparentoui="checked='checked'"; }
	if (PROFENVOIPARENT == "non") { $profenvoiparentnon="checked='checked'"; }
	if (PROFENVOIELEVE == "oui")  { $profenvoieleveoui="checked='checked'"; }
	if (PROFENVOIELEVE == "non")  { $profenvoielevenon="checked='checked'"; }
	if (PROFENVOIDIREC == "oui")  { $profenvoidirecoui="checked='checked'"; }
	if (PROFENVOIDIREC == "non")  { $profenvoidirecnon="checked='checked'"; }
	if (PROFENVOISCOLAIRE == "non") { $profenvoiscolairenon="checked='checked'"; }
	if (PROFENVOISCOLAIRE == "oui") { $profenvoiscolaireoui="checked='checked'"; }
	if (PROFENVOIEXT == "non") { $profenvoiextnon="checked='checked'"; }
	if (PROFENVOIEXT == "oui") { $profenvoiextoui="checked='checked'"; }
	if (PARENTENVOIPROF == "oui")   { $parentenvoiprofoui="checked='checked'"; }
	if (PARENTENVOIPROF == "non")   { $parentenvoiprofnon="checked='checked'"; }
	if (PARENTENVOITUTEUR == "oui") { $parentenvoituteuroui="checked='checked'"; }
	if (PARENTENVOITUTEUR == "non") { $parentenvoituteurnon="checked='checked'"; }
	if (PARENTENVOIGROUPE == "oui") { $parentenvoigroupeoui="checked='checked'"; }
	if (PARENTENVOIGROUPE == "non") { $parentenvoigroupenon="checked='checked'"; }
	if (PARENTENVOIPARENT == "oui") { $parentenvoiparentoui="checked='checked'"; }
	if (PARENTENVOIPARENT == "non") { $parentenvoiparentnon="checked='checked'"; }
	if (PARENTENVOIELEVE == "oui")  { $parentenvoieleveoui="checked='checked'"; }
	if (PARENTENVOIELEVE == "non")  { $parentenvoielevenon="checked='checked'"; }
	if (PARENTENVOIDIREC == "oui")  { $parentenvoidirecoui="checked='checked'"; }
	if (PARENTENVOIDIREC == "non")  { $parentenvoidirecnon="checked='checked'"; }
	if (PARENTENVOISCOLAIRE == "non") { $parentenvoiscolairenon="checked='checked'"; }
	if (PARENTENVOISCOLAIRE == "oui") { $parentenvoiscolaireoui="checked='checked'"; }
	if (PARENTENVOIEXT == "non") { $parentenvoiextnon="checked='checked'"; }
	if (PARENTENVOIEXT == "oui") { $parentenvoiextoui="checked='checked'"; }
	if (EDTVISUPROF == "oui") { $edtvisuoui="checked='checked'"; }
	if (EDTVISUPROF == "non") { $edtvisunon="checked='checked'"; }
	if (EXAMENBLANC == "non") { $examenblancnon="checked='checked'"; }
	if (EXAMENBLANC == "oui") { $examenblancoui="checked='checked'"; }
	if (EXAMENDS == "non") 	  { $examendsnon="checked='checked'"; }
	if (EXAMENDS == "oui")    { $examendsoui="checked='checked'"; }
	if (EXAMENNAMUR == "non") { $examennamurnon="checked='checked'"; }
	if (EXAMENNAMUR == "oui") { $examennamuroui="checked='checked'"; }
	if (EXAMENKINSHASA == "non") { $examenkinshasanon="checked='checked'"; }
        if (EXAMENKINSHASA == "oui") { $examenkinshasaoui="checked='checked'"; }
	if (EXAMENISMAP == "non") { $examenismapnon="checked='checked'"; }
	if (EXAMENISMAP == "oui") { $examenismapoui="checked='checked'"; }
	if (EXAMEN == "non")      { $examennon="checked='checked'"; }
	if (EXAMEN == "oui")      { $examenoui="checked='checked'"; }
	if (EXAMENCIEFORMATION == "non") { $examencieformationnon="checked='checked'"; }
	if (EXAMENCIEFORMATION == "oui") { $examencieformationoui="checked='checked'"; }
	if (EXAMENEEPP == "non") { $exameneeppnon="checked='checked'"; }
	if (EXAMENEEPP == "oui") { $exameneeppoui="checked='checked'"; }
	if (EXAMENBREVETCOLLEGE == "non") { $examenbrevetcollegenon="checked='checked'"; }
	if (EXAMENBREVETCOLLEGE == "oui") { $examenbrevetcollegeoui="checked='checked'"; }
	if (VERIFSUJETNOTE == "oui") { $verifsujetnoteoui="checked='checked'"; }
	if (VERIFSUJETNOTE == "non") { $verifsujetnotenon="checked='checked'"; }
	if (NOMANI == "non") { $nomaniversairenon="checked='checked'"; }
	if (NOMANI == "oui") { $nomaniversaireoui="checked='checked'"; }
	if (PROFENVOIGRPELE == "oui") { $profenvoigrpelevoui="checked='checked'"; }
	if (PROFENVOIGRPELE == "non") { $profenvoigrpelevnon="checked='checked'";  }
	if (PROFENVOIDELEGUE == "oui") { $profenvoidelegueoui="checked='checked'";  }
	if (PROFENVOIDELEGUE == "non") { $profenvoideleguenon="checked='checked'"; }
	if (PARENTENVOIGRPELE == "oui") { $parentenvoigrpelevoui="checked='checked'"; }
	if (PARENTENVOIGRPELE == "non") { $parentenvoigrpelevnon="checked='checked'";  }
	if (PARENTENVOIDELEGUE == "oui") { $parentenvoidelegueoui="checked='checked'";  }
	if (PARENTENVOIDELEGUE == "non") { $parentenvoideleguenon="checked='checked'"; }
	if (ELEVEENVOIGRPELE == "oui") { $eleveenvoigrpelevoui="checked='checked'"; }
	if (ELEVEENVOIGRPELE == "non") { $eleveenvoigrpelevnon="checked='checked'";  }
	if (ELEVEENVOIDELEGUE == "oui") { $eleveenvoidelegueoui="checked='checked'";  }
	if (ELEVEENVOIDELEGUE == "non") { $eleveenvoideleguenon="checked='checked'"; }
	if (COMBULTINTYPE == "oui") { $combulltypeoui="checked='checked'"; }
	if (COMBULTINTYPE == "non") { $combulltypenon="checked='checked'"; }
	if (SEMAINEDIMANCHE == "oui") { $semainedimancheoui="checked='checked'"; }
	if (SEMAINEDIMANCHE == "non") { $semainedimanchenon="checked='checked'"; }
	if (defined("TVAVACATIONTAUX")) { $tvavatationtaux=TVAVACATIONTAUX; }else{ $tvavatationtaux=""; }
	if (TVAVACATION == "oui") { $tvavatationoui="checked='checked'"; }
	if (TVAVACATION == "non") { $tvavatationnon="checked='checked'"; }
	if (AUTOCOMPLETIONLOGIN == "oui") { $autocompletionloginoui="checked='checked'"; }
	if (AUTOCOMPLETIONLOGIN == "non") { $autocompletionloginnon="checked='checked'"; }
	if (CIVARMEE == "oui") { $civarmeeoui="checked='checked'"; }
	if (CIVARMEE == "non") { $civarmeenon="checked='checked'"; }
	if (EDTDIRECT == "oui") { $edtdirectoui="checked='checked'"; }
	if (EDTDIRECT == "non") { $edtdirectnon="checked='checked'"; }
	if (VIESCOLAIREHISTORYCMD == "oui") { $viescolairehistocmdoui="checked='checked'"; }
	if (VIESCOLAIREHISTORYCMD == "non") { $viescolairehistocmdnon="checked='checked'"; }
	if (AGENDAPDA == "oui") { $agendapdaoui="checked='checked'"; }
	if (AGENDAPDA == "non") { $agendapdanon="checked='checked'"; }
	if (PASSMODULEMEDICAL == "non") { $passmodulemedicalnon="checked='checked'"; }
	if (PASSMODULEMEDICAL == "oui") { $passmodulemedicaloui="checked='checked'"; }
	if (PASSMODULEINDIVIDUEL == "non") { $passmoduleindividuelnon="checked='checked'"; }
	if (PASSMODULEINDIVIDUEL == "oui") { $passmoduleindividueloui="checked='checked'"; }
	if (PARENTENVOIPERSONNEL == "oui") { $parentenvoipersonneloui="checked='checked'"; }
	if (PARENTENVOIPERSONNEL == "non") { $parentenvoipersonnelnon="checked='checked'"; }
	if (ELEVEENVOIPERSONNEL == "oui")  { $eleveenvoipersonneloui="checked='checked'"; }
	if (ELEVEENVOIPERSONNEL == "non")  { $eleveenvoipersonnelnon="checked='checked'"; }
	if (PROFENVOIPERSONNEL == "oui")   { $profenvoipersonneloui="checked='checked'"; }
	if (PROFENVOIPERSONNEL == "non")   { $profenvoipersonnelnon="checked='checked'"; }
	if (PERSONNELENVOIPROF == "oui")   { $personnelenvoiprofoui="checked='checked'"; }
	if (PERSONNELENVOIPROF == "non")   { $personnelenvoiprofnon="checked='checked'"; }
	if (PERSONNELENVOIGRPELE == "oui") { $personnelenvoigrpelevoui="checked='checked'"; }
	if (PERSONNELENVOIGRPELE == "non") { $personnelenvoigrpelevnon="checked='checked'"; }
	if (PERSONNELENVOIPARENT == "oui") { $personnelenvoiparentoui="checked='checked'"; }
	if (PERSONNELENVOIPARENT == "non") { $personnelenvoiparentnon="checked='checked'"; }
	if (PERSONNELENVOIELEVE == "oui")  { $personnelenvoieleveoui="checked='checked'"; }
	if (PERSONNELENVOIELEVE == "non")  { $personnelenvoielevenon="checked='checked'"; }
	if (PERSONNELENVOIEXT == "non")    { $personnelenvoiextnon="checked='checked'"; }
	if (PERSONNELENVOIEXT == "oui")    { $personnelenvoiextoui="checked='checked'"; }
	if (PLANCLASSEPARENT == "non")    { $planclasseParentnon="checked='checked'"; }
	if (PLANCLASSEPARENT == "oui")    { $planclasseParentoui="checked='checked'"; }
	if (PROFPGESTIONENTREPRISE == "non")    { $gestionentrepriseprofpnon="checked='checked'"; }
	if (PROFPGESTIONENTREPRISE == "oui")    { $gestionentrepriseprofpoui="checked='checked'"; }
	if (VIESCOLAIRESTAGEDATE == "non")    { $viescolairestagedatenon="checked='checked'"; }
	if (VIESCOLAIRESTAGEDATE == "oui")    { $viescolairestagedateoui="checked='checked'"; }
	if (VIESCOLAIRESTAGEENT == "non")    { $viescolairestageentnon="checked='checked'"; }
	if (VIESCOLAIRESTAGEENT == "oui")    { $viescolairestageentoui="checked='checked'"; }
	if (VIESCOLAIRESTAGEETUDIANT == "non")    { $viescolairestageetudiantnon="checked='checked'"; }
	if (VIESCOLAIRESTAGEETUDIANT == "oui")    { $viescolairestageetudiantoui="checked='checked'"; }
	if (PROFSTAGEENTR == "non")    { $profentrnon="checked='checked'"; }
	if (PROFSTAGEENTR == "oui")    { $profentroui="checked='checked'"; }
	if (PROFSTAGEETUDIANT == "non")    { $profstageetudiantnon="checked='checked'"; }
	if (PROFSTAGEETUDIANT == "oui")    { $profstageetudiantoui="checked='checked'"; }
	if (CREATENTRPARENT == "non")    { $createntrparentnon="checked='checked'"; }
	if (CREATENTRPARENT == "oui")    { $createntrparentoui="checked='checked'"; }
	if (CREATENTRELEVE == "non")    { $createntrelevenon="checked='checked'"; }
	if (CREATENTRELEVE == "oui")    { $createntreleveoui="checked='checked'"; }
	if (VIESCOLAIRENOTEENSEIGNANT == "non")    { $viescolairenoteenseignantnon="checked='checked'"; }
	if (VIESCOLAIRENOTEENSEIGNANT == "oui")    { $viescolairenoteenseignantoui="checked='checked'"; }
	if (EXAMENPIGIERNIMES == "non")    { $examenpigiernimesnon="checked='checked'"; }
	if (EXAMENPIGIERNIMES == "oui")    { $examenpigiernimesoui="checked='checked'"; }
	if (EXAMENISPACADEMIES == "non")    { $examenispacademiesnon="checked='checked'"; }
	if (EXAMENISPACADEMIES == "oui")    { $examenispacademiesoui="checked='checked'"; }
	if (FINANCIERVATEL == "oui")    { $modulefinanciervateloui="checked='checked'"; }
	if (FINANCIERVATEL == "non")    { $modulefinanciervatelnon="checked='checked'"; }
	if (PROFENTRCONVENTION == "oui")    { $profentrconventionoui="checked='checked'"; }
	if (PROFENTRCONVENTION == "non")    { $profentrconventionnon="checked='checked'"; }
	if (PROFPENTRCONVENTION == "oui")    { $profpentrconventionoui="checked='checked'"; }
	if (PROFPENTRCONVENTION == "non")    { $profpentrconventionnon="checked='checked'"; }
	if (PROFPCREATETUTEUR == "oui")    { $profpcreatetuteuroui="checked='checked'"; }
	if (PROFPCREATETUTEUR == "non")    { $profpcreatetuteurnon="checked='checked'"; }
	if (PRESENTPROF == "oui")    { $presentprofoui="checked='checked'"; }
	if (PRESENTPROF == "non")    { $presentprofnon="checked='checked'"; }
	if (NOTATION20 == "oui")    { $modulenote20oui="checked='checked'"; }
	if (NOTATION20 == "non")    { $modulenote20non="checked='checked'"; }
	if (NOTATION15 == "oui")    { $modulenote15oui="checked='checked'"; }
	if (NOTATION15 == "non")    { $modulenote15non="checked='checked'"; }
	if (NOTATION10 == "oui")    { $modulenote10oui="checked='checked'"; }
	if (NOTATION10 == "non")    { $modulenote10non="checked='checked'"; }
	if (NOTATION5 == "oui")    { $modulenote5oui="checked='checked'"; }
	if (NOTATION5 == "non")    { $modulenote5non="checked='checked'"; }
	if (NOTATION6 == "oui")    { $modulenote6oui="checked='checked'"; }
	if (NOTATION6 == "non")    { $modulenote6non="checked='checked'"; }
	if (NOTATION30 == "oui")    { $modulenote30oui="checked='checked'"; }
	if (NOTATION30 == "non")    { $modulenote30non="checked='checked'"; }
	if (NOTATION40 == "oui")    { $modulenote40oui="checked='checked'"; }
	if (NOTATION40 == "non")    { $modulenote40non="checked='checked'"; }
	if (SEMAINEVENDREDI == "oui")    { $semainevendredioui="checked='checked'"; }
	if (SEMAINEVENDREDI == "non")    { $semainevendredinon="checked='checked'"; }
	if (PROFMOTIFABSRTD == "oui")    { $absprofmotifoui="checked='checked'"; }
	if (PROFMOTIFABSRTD == "non")    { $absprofmotifnon="checked='checked'"; }
	if (ACCESMESSTUTEUR == "oui")    { $accesmesstuteuroui="checked='checked'"; }
	if (ACCESMESSTUTEUR == "non")    { $accesmesstuteurnon="checked='checked'"; }
	if (ACCESMESSENVOITUTEUR == "oui")    { $accesmessenvoituteuroui="checked='checked'"; }
	if (ACCESMESSENVOITUTEUR == "non")    { $accesmessenvoituteurnon="checked='checked'"; }
	if (TUTEURENVOIPROF == "oui")    { $tuteurenvoiprofoui="checked='checked'"; }
	if (TUTEURENVOIPROF == "non")    { $tuteurenvoiprofnon="checked='checked'"; }
	if (TUTEURENVOITUTEUR == "oui")    { $tuteurenvoituteuroui="checked='checked'"; }
	if (TUTEURENVOITUTEUR == "non")    { $tuteurenvoituteurnon="checked='checked'"; }
	if (TUTEURENVOIPARENT == "oui")    { $tuteurenvoiparentoui="checked='checked'"; }
	if (TUTEURENVOIPARENT == "non")    { $tuteurenvoiparentnon="checked='checked'"; }
	if (TUTEURENVOIELEVE == "oui")    { $tuteurenvoieleveoui="checked='checked'"; }
	if (TUTEURENVOIELEVE == "non")    { $tuteurenvoielevenon="checked='checked'"; }
	if (TUTEURENVOIDIREC == "oui")    { $tuteurenvoidirecoui="checked='checked'"; }
	if (TUTEURENVOIDIREC == "non")    { $tuteurenvoidirecnon="checked='checked'"; }
	if (TUTEURENVOISCOLAIRE == "oui")    { $tuteurenvoiscolaireoui="checked='checked'"; }
	if (TUTEURENVOISCOLAIRE == "non")    { $tuteurenvoiscolairenon="checked='checked'"; }
	if (TUTEURENVOIEXT == "oui")    { $tuteurenvoiextoui="checked='checked'"; }
	if (TUTEURENVOIEXT == "non")    { $tuteurenvoiextnon="checked='checked'"; }
	if (TUTEURENVOIGRPELEV == "oui")    { $tuteurenvoigrpelevoui="checked='checked'"; }
	if (TUTEURENVOIGRPELEV == "non")    { $tuteurenvoigrpelevnon="checked='checked'"; }
	if (TUTEURENVOIDELEGUE == "oui")    { $tuteurenvoidelegueoui="checked='checked'"; }
	if (TUTEURENVOIDELEGUE == "non")    { $tuteurenvoideleguenon="checked='checked'"; }
	if (TUTEURENVOIPERSONNEL == "oui")    { $tuteurenvoipersonneloui="checked='checked'"; }
	if (TUTEURENVOIPERSONNEL == "non")    { $tuteurenvoipersonnelnon="checked='checked'"; }
	if (CHOIXMATIEREPROF == "0")    { $choixmatiereprofoui="checked='checked'"; }
	if (CHOIXMATIEREPROF == "1")    { $choixmatiereprofnon="checked='checked'"; }
	if (CARNETSUIVIPROF == "oui")    { $carnetsuiviprofoui="checked='checked'"; }
	if (CARNETSUIVIPROF == "non")    { $carnetsuiviprofnon="checked='checked'"; }
	if (TROMBIALLCLASSEPROF == "oui")    { $tombiallclasseprofoui="checked='checked'"; }
	if (TROMBIALLCLASSEPROF == "non")    { $tombiallclasseprofnon="checked='checked'"; }
	if (PROFSTAGEETUDIANTADMIN == "oui")    { $stageetudiantadminprofoui="checked='checked'"; }
	if (PROFSTAGEETUDIANTADMIN == "non")    { $stageetudiantadminprofnon="checked='checked'"; }
	if (PASSMODULEBILANFINANCIER == "oui")    { $passmodulebilanfinancieroui="checked='checked'"; }
	if (PASSMODULEBILANFINANCIER == "non")    { $passmodulebilanfinanciernon="checked='checked'"; }
	if (MODULEELEARNING == "dokeos")    { $moduleeLearningdoekeos="checked='checked'"; }
	if (MODULEELEARNING == "moodle")    { $moduleeLearningmoodle="checked='checked'"; }
	if (INTITULEDIRECTION == "direction")         { $intitule_direction1="selected='selected'"; }
	if (INTITULEDIRECTION == "administration")    { $intitule_direction2="selected='selected'"; }
	if (INTITULEDIRECTION == "directeur")         { $intitule_direction3="selected='selected'"; }
	if (INTITULEELEVE == "élève")         { $intitule_eleve1="selected='selected'"; }
	if (INTITULEELEVE == "étudiant")    { $intitule_eleve2="selected='selected'"; }
	if (INTITULEELEVE == "apprenant")    { $intitule_eleve3="selected='selected'"; }
	
	$intitule_enseignant1="";
	$intitule_enseignant2="";

	if (INTITULEENSEIGNANT == "enseignant")    { $intitule_enseignant1="selected='selected'"; }
	if (INTITULEENSEIGNANT == "formateur")    { $intitule_enseignant2="selected='selected'"; }

	$intitule_classe1="";
	$intitule_classe2="";

	if (INTITULECLASSE == "classe")    { $intitule_classe1="selected='selected'"; }
	if (INTITULECLASSE == "section")    { $intitule_classe2="selected='selected'"; }
	
	if (PASSOUBLIE == "oui")         { $passoublieoui="checked='checked'"; }
	if (PASSOUBLIE == "non")    { $passoublienon="checked='checked'"; }
	if (DIRCAHIERTEXTE == "oui")         { $dircahiertexteoui="checked='checked'"; }
	if (DIRCAHIERTEXTE == "non")    { $dircahiertextenon="checked='checked'"; }
	if (VIESCOLAIREMODIFETUDIANT == "oui") { $viescolairemodifetudiantoui="checked='checked'"; }
	if (VIESCOLAIREMODIFETUDIANT == "non") { $viescolairemodifetudiantnon="checked='checked'"; }
	if (MODIFNOTEAPRESARRET == "oui")      { $modifnoteapresarretoui="checked='checked'"; }
	if (MODIFNOTEAPRESARRET == "non")      { $modifnoteapresarretnon="checked='checked'"; }
	if (PROFPVIDEOPROJO == "oui")          { $profpVideoProjooui="checked='checked'"; }
	if (PROFPVIDEOPROJO == "non")          { $profpVideoProjonon="checked='checked'"; }
	if (PROFPMODIFAFFECT == "oui")         { $profpmodifaffectoui="checked='checked'"; }
	if (PROFPMODIFAFFECT == "non")         { $profpmodifaffectnon="checked='checked'"; }
	if (PROFPACCESNOTE == "oui")           { $profpaccesnoteoui="checked='checked'"; }
	if (PROFPACCESNOTE == "non")           { $profpaccesnotenon="checked='checked'"; }


	if (EXAMENIPAC == "non")    { $examenipacnon="checked='checked'"; }
	if (EXAMENIPAC == "oui")    { $examenipacoui="checked='checked'"; }

	if (EXAMENVATELREUNION == "non")    { $examenvatelreunionnon="checked='checked'"; }
	if (EXAMENVATELREUNION == "oui")    { $examenvatelreunionoui="checked='checked'"; }

	if (AUTOINE == "non")    { $autoINEnon="checked='checked'"; }
	if (AUTOINE == "oui")    { $autoINEoui="checked='checked'"; }


	if (ENTRETIENPROF == "non")    { $entretienprofnon="checked='checked'"; }
	if (ENTRETIENPROF == "oui")    { $entretienprofoui="checked='checked'"; }

	if (EXAMENJTC == "oui")    { $examenjtcoui="checked='checked'"; }
	if (EXAMENJTC == "non")    { $examenjtcnon="checked='checked'"; }

	if (AUTODATEABSRTD == "oui")    { $autodateabsrtdoui="checked='checked'"; }
	if (AUTODATEABSRTD == "non")    { $autodateabsrtdnon="checked='checked'"; }

	if (DEFILMESSAGEHORI == "oui")    { $checkmesshorioui="checked='checked'"; }
	if (DEFILMESSAGEHORI == "non")    { $checkmesshorinon="checked='checked'"; }

	if (PROFPACCESVISADIRECTION == "oui")    { $profpaccesvisadirectionoui="checked='checked'"; }
	if (PROFPACCESVISADIRECTION == "non")    { $profpaccesvisadirectionnon="checked='checked'"; }
	if (PROFPACCESABSRTD == "oui")    { $profpaccesabsrtdoui="checked='checked'"; }
	if (PROFPACCESABSRTD == "non")    { $profpaccesabsrtdnon="checked='checked'"; }

	if (AFFICHAGEVATEL == "oui")    { $affichageVateloui="checked='checked'"; }
	if (AFFICHAGEVATEL == "non")    { $affichageVatelnon="checked='checked'"; }

	if (MODIFTROMBIELEVE == "oui")    { $modiftrombieleveoui="checked='checked'"; }
	if (MODIFTROMBIELEVE == "non")    { $modiftrombielevenon="checked='checked'"; }
	if (defined("CNILPROTECTEUR"))    { $cnilprotecteur=CNILPROTECTEUR; }

	if (AFFICHAGEIA == "oui")    { $affichageIAOui="checked='checked'"; }
	if (AFFICHAGEIA == "non")    { $affichageIANon="checked='checked'"; }
	if (AFFICHAGESIGN == "oui")    { $affichageSIGNOui="checked='checked'"; }
	if (AFFICHAGESIGN == "non")    { $affichageSIGNNon="checked='checked'"; }






	
	if (defined("DEFILMESSAGEHORIY")) $messdefilhoriY=DEFILMESSAGEHORIY;
	if (defined("DEFILMESSAGEHORIX")) $messdefilhoriX=DEFILMESSAGEHORIX;

	if (TITREBANNIERE != "TITREBANNIERE") $titrebanniere=stripslashes(TITREBANNIERE); 

	if (!defined(MODULEELEARNING)) { $moduleeLearningdoekeos="checked='checked'"; }

	$urlsite=$_SERVER["SERVER_NAME"]."/".ECOLE."/"; 
	if (defined("URLSITE")) {
		if (URLSITE != "") { 
			$urlsite=URLSITE; 
		}
	}

	if (defined("HD_EDT")){$hd_edt=HD_EDT;}
	if (defined("HF_EDT")){$hf_edt=HF_EDT;}
	if (defined("MF_EDT")){$mf_edt=MF_EDT;}

	$cnil="";
	if (defined("CNILNUM")) { $cnil=CNILNUM; }

	$selected="<option  STYLE='color:#000066;background-color:#FCE4BA' value=\"".TIMEZONE."\" >".TIMEZONE." Heures</option>";
	$selectedregion="<option  STYLE='color:#000066;background-color:#FCE4BA' value=\"".METEOID.":".METEOVILLE."\" > ".METEOVILLE." </option>";
}
?>

<form name=formulaire  method=post action="configuration2.php" ENCTYPE="multipart/form-data" >
<table border="0" align=center width=100% >

<tr><td colspan=2 >&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Serveur</b></font> </td></tr>
<tr height=30 align=right>
<td>Y a t il un accès internet :  </td>
		<td align=left>
		<input type=radio <?php print $lanoui ?> name=lan value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $lannon ?> name=lan value="non" class=btradio1  > non
		</td>
</tr>
<tr height=30 align=right>
<td>Le serveur Triade utilise un proxy Internet :  </td>
<td align=left>
		<input type=radio <?php print $proxyoui ?> name=proxy value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $proxynon ?> name=proxy value="non" class=btradio1  > non
</td>
</tr>
<tr height=30 align=right>
<td>L'acc&egrave;s &agrave;  Triade utilise le protocole https :  </td>
<td align=left>
		<input type=radio <?php print $httpsoui ?> name=https value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $httpsnon ?> name=https value="non" class=btradio1  > non
</td>
</tr>
<tr height=30 align=right>
<td>Serveur en architecture 32 ou 64 bits :  </td>
<td align=left>
		<input type=radio <?php print $bit32 ?> name=bit value="32" class=btradio1  > 32 bits
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $bit64 ?> name=bit value="64" class=btradio1  > 64 bits
</td>
</tr>

<tr height=30 align=right>
	<td valign=top>Modifier heure Triade : </td>
	<td align=left valign=top>&nbsp;<select name="timezone">
			<?php print $selected ?>
			<option value="-12" class=bouton2  > -13 Heures</option>
			<option value="-12"  class=bouton2 > -12 Heures</option>
			<option value="-11" class=bouton2  > -11 Heures</option>
			<option value="-10" class=bouton2  > -10 Heures</option>
			<option value="-9" class=bouton2  > -9 Heures</option>
			<option value="-8" class=bouton2  > -8 Heures</option>
			<option value="-7" class=bouton2  > -7 Heures</option>
			<option value="-6" class=bouton2  > -6 Heures</option>
			<option value="-5" class=bouton2  > -5 Heures</option>
			<option value="-4" class=bouton2  > -4 Heures</option>
			<option value="-3" class=bouton2  > -3 Heures</option>
			<option value="-2" class=bouton2  > -2 Heures</option>
			<option value="-1" class=bouton2  > -1 Heure</option>
			<option value="0" >0</option>
			<option value="1" class=bouton2  > +1 Heure</option>
			<option value="2" class=bouton2  > +2 Heures</option>
			<option value="3" class=bouton2  > +3 Heures</option>
			<option value="4"  class=bouton2 > +4 Heures</option>
			<option value="5" class=bouton2  > +5 Heures</option>
			<option value="6" class=bouton2  > +6 Heures</option>
			<option value="7"  class=bouton2 > +7 Heures</option>
			<option value="8"  class=bouton2 > +8 Heures</option>
			<option value="9" class=bouton2  > +9 Heures</option>
			<option value="10" class=bouton2  > +10 Heures</option>
			<option value="11"  class=bouton2 > +11 Heures</option>
			<option value="12" class=bouton2  > +12 Heures</option>
			<option value="13"  class=bouton2 > +13 Heures</option>
		</select> <input type=text value="<?php print TIMEZONEMINUTE ?>" size=2 name="minute" > mn
</tr><tr><td colspan=2 align=center>
		Heure du serveur :  <?php print date("d/m/Y")." ".date("H:i:s") ?>
		<br>Heure de Triade&nbsp;:  <?php print dateDMY()." ".dateHIS() ?><br><br>
</td>
</tr>

<tr><td align=right >Adresse Internet du Site TRIADE : </td>
<td align=left><input type=text  name="urlsite" size=30 value="<?php print protohttps().$urlsite ?>" />
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>A</font>dresse Internet du site Triade http://.... </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</tr>

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Alerte Administrateur Triade</b></font> </td></tr>

<tr><td align=right >Email du lien "Contact Triade" : </td>
<td align=left><input type=text value="<?php print $mailcontact ?>"  name="mailcontact" size=30>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>E</font>mail se trouvant sur la page d\'accueil.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>

</tr>

<tr><td align=right>Email de l'administrateur Triade 1 : </td>
<td align=left><input type=text value="<?php print $mailadmin ?>"  name="mailadmin" size=30>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>E</font>mail permettant de recevoir les informations <br /> importantes de VOTRE Triade .</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</tr>

<tr><td align=right valign=top>Email de l'administrateur Triade 2 : </td>
<td align=left><input type=text value="<?php print $mailadmin2 ?>"  name="mailadmin2" size=30>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>E</font>mail permettant de recevoir les informations <br /> importantes de VOTRE Triade .</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
<br>
<?php 
if ($sendmail != 0) {
	print "[<a href='configuration_mail.php'>Configuration mail</a>]";
}
?>
</tr>


<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" >  <font class="T2"><b>Configuration Graphique</b></font></td></tr>

<!-- 
<tr><td align=right >Activation de l'agent web  : </td>
<td align=left><input type=radio <?php print $agentweboui ?> name="agentweb" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $agentwebnon ?> name="agentweb" value="non" class=btradio1  > non 

	       <A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<iframe width=100 height=100 src=\'agentweb.php\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe><br><font face=Verdana size=1><font color=red>L</font>\'agent web Lise permettra de guider vos utilisateurs de façon interactif.<br><br> <i>Accès à l\'internet obligatoire</i> </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>
-->

<?php 
if (file_exists("../common/config8.inc.php")) {
	include_once("../common/config8.inc.php");
	$prenomagentweb=AGENTWEBPRENOM;
}
if (trim($prenomagentweb) == "") { $prenomagentweb="Lise"; } 
?>
<!--
<tr><td align=right >Prénom de l'agent web : </td>
<td align=left><input  type=text value="<?php print $prenomagentweb ?>" name="prenomagentweb"  size=30 maxlength="15" ></td>
</tr>
-->

<tr><td align=right >Configuration jeux de caractères  :</td><td align=left >
	<select name="charset" >
		<?php
		print "<option value='$charset' id='select0'  class=bouton2 checked='checked' >".$charset."</option>";

		print "<option value='UTF-8' id='select1' class=bouton2  >UTF-8</option>";
		print "<option value='iso-8859-1' id='select1' class=bouton2  >iso-8859-1</option>";
		?>
	</select>
</td></tr>


<tr><td align=right >Supprimer le message défillant vertical : </td>
<td align=left><input type=radio <?php print $checkmessoui ?> name=messdefil value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $checkmessnon ?> name=messdefil value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Supprimer le message défillant horizontal : </td>
<td align=left><input type=radio <?php print $checkmesshorioui ?> name=messdefilhori value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $checkmesshorinon ?> name=messdefilhori value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Position du message horizontal : </td>
<td align=left> X <input type='text' name='messdefilhoriX' value="<?php print $messdefilhoriX ?>" class='btradio1' size=2 > px / Y <input type='text' name='messdefilhoriY' value="<?php print $messdefilhoriY ?>" class=btradio1  size=2 > px </td>
</tr>


<tr><td align=right >Supprimer la bannière du haut : </td>
<td align=left><input type=radio <?php print $checkpubhautoui ?> name=pubhaut value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $checkpubhautnon ?> name=pubhaut value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Bannière disponible : </td>
<td align=left><select name='banniere_dispo' >
		<?php 
		if (defined("BANNIEREDISPO")) {
			if (BANNIEREDISPO >= 1) {
				print "<option value='".BANNIEREDISPO."' id='select1' >Bannière ".BANNIEREDISPO."</option>";
				print "<option value='supprimer' id='select0' >Supprimer bannière</option>";
			}else{
				print "<option value='' id='select0' >Aucun</option>";
			}
		}else{
			print "<option value='' id='select0' >Aucun</option>";
		}	
		
		$nbimg=29;
		for($i=1;$i<=$nbimg;$i++) {
			print "<option value='$i' id='select1' >Bannière $i</option>";
		}
		?>
	       </select>
		&nbsp;&nbsp;[<a href='#' onclick="open('visubanniere.php','banniere','width=1100,height=700,scrollbars=yes');" >Consulter</a>]
</tr>

<?php
$file1="../data/image_banniere/banniere000.jpg";
$file2="../data/image_banniere/banniere000.png";
$file3="../data/image_banniere/banniere000.gif";

if (isset($_GET["Supp_ban"])) {
	include_once("./librairie_php/edit_fichier.php");
	banniere_edit("./librairie_js/menudepart.js","aucun","");
    	banniere_edit("../librairie_js/menudepart.js","aucun","");
    	banniere_edit("../librairie_js/menuadmin.js","aucun","");
    	banniere_edit("../librairie_js/menuparent.js","aucun","");
    	banniere_edit("../librairie_js/menuprof.js","aucun","");
    	banniere_edit("../librairie_js/menuscolaire.js","aucun","");
    	banniere_edit("../librairie_js/menueleve.js","aucun","");
    	banniere_edit("../librairie_js/menututeur.js","aucun","");
    	banniere_edit("../librairie_js/menupersonnel.js","aucun","");
	@unlink("../data/image_banniere/banniere000.jpg");
	@unlink("../data/image_banniere/banniere000.png");
	@unlink("../data/image_banniere/banniere000.gif");
}


if ((file_exists($file1)) || (file_exists($file2)) || (file_exists($file3))) {
?>
	<tr><td align=right valign=top >Bannière de l'établissement :</td>
	<td align=left >
	<input type="button" value="Supprimer" class='bouton2' onclick="open('configuration.php?Supp_ban=1','_parent','')" >
	<input type=hidden name="chg_ban" value='0'>
	</td></tr>
<?php
}else{
?>
	<tr><td align=right valign=top >Bannière de l'établissement :</td>
	<td align=left >
	<input type="file" name="fichier" size=20  > <br>
	image au format jpg
	<br><i>Hauteur :  <input type='text' name='hauteurbanniere' value='' size='2' > px <br>
	       Largeur : 1020 px </i><br>
	<input type=hidden name="chg_ban" value='1'>
	</td></tr>
<?php } ?>

<!-- <tr><td align=right > Titre de la banniere :</td><td align=left ><input type='text' name='titrebanniere' value="<?php print $titrebanniere ?>" size='30' maxlength=50'' /></tr> -->

<tr><td align=right >Configuration couleur :</td><td align=left >
	<select name="graph" >
		<?php
			if (defined("GRAPH")) {

				if (GRAPH == "0") { $val="Marine"; }
				if (GRAPH == "1") { $val="Saumon"; }
				if (GRAPH == "2") { $val="Corail"; }
				if (GRAPH == "3") { $val="Algue"; }
				if (GRAPH == "4") { $val="Citron (spécif. arabe)"; }
				if (GRAPH == "5") { $val="Printemps"; }
				if (GRAPH == "6") { $val="Rose"; }
				if (GRAPH == "7") { $val="Classique"; }
				if (GRAPH == "8") { $val="Figue"; }
				if (GRAPH == "9") { $val="Groupe Vatel"; }
				if (GRAPH == "10") { $val="ESAD (1)"; }
				if (GRAPH == "11") { $val="Classeur"; }
				if (GRAPH == "12") { $val="Numidia (1)"; }
				if (GRAPH == "13") { $val="MongilSchool"; }
				if (GRAPH == "14") { $val="Marine 2"; }
				if (GRAPH == "15") { $val="Nuage"; }
				if (GRAPH == "16") { $val="IPAC BTS"; }
				if (GRAPH == "17") { $val="IPAC Factory"; }
				if (GRAPH == "18") { $val="IPAC MBway"; }
				if (GRAPH == "19") { $val="Design Genève"; }
				if (GRAPH == "20") { $val="Pigier Performance"; }
				if (GRAPH == "21") { $val="Pigier Création"; }
				if (GRAPH == "22") { $val="Ecole-des-lys"; }
				if (GRAPH == "23") { $val="CNEAP"; }
				if (GRAPH == "24") { $val="LFMP"; }
				if (GRAPH == "25") { $val="AFTEC"; }
				if (GRAPH == "26") { $val="ESCO"; }
				if (GRAPH == "27") { $val="Privé George Sand"; }
				if (GRAPH == "28") { $val="La Source"; }
				if (GRAPH == "29") { $val="EPICOM"; }
				if (GRAPH == "30") { $val="WES'SUP"; }
				if (GRAPH == "31") { $val="VPCONSULT"; }

				print "<option value='".GRAPH."' STYLE='color:#000066;background-color:#FCE4BA' class=bouton2 checked='checked' >".$val."</option>";
			}
?>
		<optgroup label="TRIADE">
		<option value='0' class=bouton2 >Marine</option>
		<option value='1' class=bouton2 >Saumon</option>
		<option value='2' class=bouton2 >Corail</option>
		<option value='3' class=bouton2 >Algue</option>
		<option value='4' class=bouton2 >Citron (spécif. arabe)</option>
		<option value='5' class=bouton2 >Printemps</option>
		<option value='6' class=bouton2 >Rose</option>
		<option value='7' class=bouton2 >Classique</option>
		<option value='8' class=bouton2 >Figue</option>
		<option value='11' class=bouton2 >Classeur</option>
		<option value='14' class=bouton2 >Marine 2</option>
		<option value='15' class=bouton2 >Nuage</option>
		<optgroup label="ECOLE">
		<option value='13' class=bouton2 >MongilSchool</option>
		<option value='22' class=bouton2 >Ecole-des-lys</option>
		<option value='23' class=bouton2 >CNEAP</option>
		<option value='24' class=bouton2 >LFMP</option>
		<option value='25' class=bouton2 >AFTEC</option>
		<option value='26' class=bouton2 >ESCO</option>
		<option value='27' class=bouton2 >Privé George Sand</option>
		<option value='28' class=bouton2 >La Source</option>
		<option value='29' class=bouton2 >EPICOM</option>
		<option value='30' class=bouton2 >WES'SUP</option>
		<option value='31' class=bouton2 >VPCONSULT</option>
		<optgroup label="ESAD">
		<option value='10' class=bouton2 > ESAD (1)</option>
		<optgroup label="IPAC">
		<option value='16' class=bouton2 >BTS</option>
		<option value='17' class=bouton2 >Factory</option>
		<option value='18' class=bouton2 >MBway</option>
		<option value='19' class=bouton2 >Design Genève</option>
		<optgroup label="NUMIDIA">
		<option value='12' class=bouton2 >Numidia (1)</option>
		<optgroup label="PIGIER">
		<option value='20' class=bouton2 >Pigier Performance</option>		
		<option value='21' class=bouton2 >Pigier Création</option>		
		<optgroup label="VATEL">
		<option value='9' class=bouton2 >Groupe Vatel</option>
	</select>
</td></tr>


<tr><td align=right >Nom du lien 1 : </td>
<td align=left><input type=text value="<?php print stripslashes($nomdulien) ?>" name="nomdulien" size=30 maxlength="50" >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>e lien apparaît dans la barre de menu de la page d\'accueil. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>
<tr><td align=right >Adresse web du lien 1 : </td>
<td align=left><input  type=text value="<?php print $addressedulien ?>" name="adressedulien"  size=30 >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>ette adresse est la destination du lien précédent. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>

<tr><td align=right >Nom du lien 2 : </td>
<td align=left><input type=text value="<?php print stripslashes($nomdulien2) ?>" name="nomdulien2" size=30 maxlength="50" >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>e lien apparaît dans la barre de menu de la page d\'accueil. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>
<tr><td align=right >Adresse web du lien 2 : </td>
<td align=left><input  type=text value="<?php print $addressedulien2 ?>" name="adressedulien2"  size=30 >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>ette adresse est la destination du lien précédent. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>

<tr><td align=right >Nom du lien 3 : </td>
<td align=left><input type=text value="<?php print stripslashes($nomdulien3) ?>" name="nomdulien3" size=30 maxlength="50" >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>e lien apparaît dans la barre de menu de la page d\'accueil. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>
<tr><td align=right >Adresse web du lien 3 : </td>
<td align=left><input  type=text value="<?php print $addressedulien3 ?>" name="adressedulien3"  size=30 >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>ette adresse est la destination du lien précédent. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>

<tr><td align=right >Nom du lien 4 : </td>
<td align=left><input type=text value="<?php print stripslashes($nomdulien4) ?>" name="nomdulien4" size=30 maxlength="50" >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>e lien apparaît dans la barre de menu de la page d\'accueil. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>
<tr><td align=right >Adresse web du lien 4 : </td>
<td align=left><input  type=text value="<?php print $addressedulien4 ?>" name="adressedulien4"  size=30 >
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>ette adresse est la destination du lien précédent. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A></td>
</tr>






<tr><td align=right >Activer le module météo : </td>
<td align=left><input type=radio <?php print $meteooui ?> name=meteo value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $meteonon ?> name=meteo value="non" class=btradio1  > non
</td>
</tr>
<tr><td align=right >Zone géographique : </td>
<td align=left valign=top>&nbsp;<select name="meteoregion">
		<?php print $selectedregion ?>

		<optgroup label="France">
		<?php
		if (file_exists("./lib/weather_concept.fr")) {
			$handle=fopen("./lib/weather_concept.fr","r");
			while (!feof($handle)) {
				$ligne=fgets($handle, 1000); 
				list($code,$ville)= preg_split ("/:/", $ligne, 2);
				$ville=trim($ville);
				$ville2=trim(trunchaine($ville,15));
				$code=trim($code);
				if (($ville2 != "") && ($code != "")){
					print "<option value=\"$code:$ville\" class='bouton2' title=\"$ville\"  >$ville2</option>";
				}
  			}
			fclose($handle);
		}
		print "<optgroup label='Haiti' >";
		if (file_exists("./lib/weather.haiti")) {
			$handle=fopen("./lib/weather.haiti","r");
			while (!feof($handle)) {
				$ligne=fgets($handle, 1000); 
				list($code,$ville)= preg_split ("/:/", $ligne, 2);
				$ville=trim($ville);
				$ville2=trim(trunchaine($ville,15));
				$code=trim($code);
				if (($ville2 != "") && ($code != "")){
					print "<option value=\"$code:$ville\" class='bouton2' title=\"$ville\"  >$ville2</option>";
				}
  			}
			fclose($handle);
		}
		print "<optgroup label='Tunisie' >";
		if (file_exists("./lib/weather.tu")) {
			$handle=fopen("./lib/weather.tu","r");
			while (!feof($handle)) {
				$ligne=fgets($handle, 1000); 
				list($code,$ville)= preg_split ("/:/", $ligne, 2);
				$ville=trim($ville);
				$ville2=trim(trunchaine($ville,15));
				$code=trim($code);
				if (($ville2 != "") && ($code != "")){
					print "<option value=\"$code:$ville\" class='bouton2' title=\"$ville\"  >$ville2</option>";
				}
  			}
			fclose($handle);
		}

		
	
		?>

		</select>

</td></tr>

<tr><td align=right >Activer la visualisation de connexion  : </td>
<td align=left><input type=radio <?php print $traceoui ?> name="trace" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tracenon ?> name="trace" value="non" class=btradio1  > non
               <A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>P</font>ermet de visualiser les informations de connexion <br>des utilisateurs. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right >Visualiser les jours de fête  : </td>
<td align=left><input type=radio <?php print $feteoui ?> name="fete" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fetenon ?> name="fete" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Visualiser les anniversaires  : </td>
<td align=left><input type=radio <?php print $aniversaireoui ?> name="aniversaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $aniversairenon ?> name="aniversaire" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Indiquer le nom de famille pour les anniversaires  : </td>
<td align=left><input type=radio <?php print $nomaniversaireoui ?> name="nomaniversaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $nomaniversairenon ?> name="nomaniversaire" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Visualiser les informations D.S.T (accueil) : </td>
<td align=left><input type=radio <?php print $dstvisuaccueiloui ?> name="dstvisuaccueil" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $dstvisuaccueilnon ?> name="dstvisuaccueil" value="non" class=btradio1  > non
</td></tr>



<tr><td align=right >Heure de départ (EDT) : </td><td align=left><input type="text" name="hd_edt" size="3" value="<?php print $hd_edt ?>" /></td></tr>
<tr><td align=right >Heure de fin (EDT) : </td><td align=left><input type="text" name="hf_edt" size="3" value="<?php print $hf_edt ?>" /></td></tr>
<tr><td align=right >Suffixe minutes (EDT) : </td><td align=left><input type="text" name="mf_edt" size="3"  value="<?php print $mf_edt ?>" /></td></tr>

<tr><td align=right >Attribution&nbsp;des&nbsp;trimestres&nbsp;en&nbsp;mode&nbsp;automatique&nbsp;:&nbsp;</td>
<td align=left><input type=radio <?php print $visutriautooui ?> name="visutriauto" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $visutriautonon ?> name="visutriauto" value="non" class=btradio1  > non 
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>P</font>ermet d\'indiquer le trimestre en cours, lors de la saisie des commentaires pour les bulletins. (Pour les enseignants, la vie scolaire, etc... )</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>




<?php //$sendmail=0 pas d'envoi de mail ?>
<?php
	$diseabled="";
	$info="";
	$champemailreply="text";
	$infoemailreply="";
	if (defined("EMAILREPLY")) { 
		$mailreply=EMAILREPLY; 
		$readonlyreplymail="readonly=readonly" ; 
		$champemailreply="hidden";
		$infoemailreply=EMAILREPLY;
	}
if ( $sendmail == 0) {
	$checkoui="";
	$checknon="checked";
	$mailmessoui="";
	$mailmessnon="checked";
	if (defined("EMAILREPLY")) { $mailreply="EMAILREPLY"; $readonlyreplymail="readonly=readonly" ; }
	$mailnomreply="";
	$mailexterneoui="";
	$mailexternenon="checked";
	$diseabled="disabled='diseabled'";
	$info="<font color='red'>( Votre serveur ne peut envoyer d'email !! )</font>";
}
?>

	<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration email</b></font> <?php print $info ?></td></tr>

<tr height=30 align=right>
<td>Autoriser le transfert de mail : </td>
<td align=left>	<input type=radio <?php print $checkoui." ".$diseabled ?> name=forward value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $checknon." ".$diseabled ?> name=forward value="non" class=btradio1  > non
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>A</font>utorise la reception automatique de vos messages Triade internes sur votre messagerie personnelle.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
		</td>
</tr>

<!-- 
 savoir si le mail de la personne nouvellement créer est automatiquement validé pour la messagerie 
-->
<tr height=30 align=right>
<td>Valider le forwarding automatiquement  : </td>
<td align=left>	<input type=radio <?php print $mailmessoui." ".$diseabled ?> name=mailmess value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $mailmessnon." ".$diseabled ?> name=mailmess value="non" class=btradio1  > non
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>L</font>e mail est automatiquement valider pour le forwarding pour tout nouveau compte créé.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
		</td>
</tr>

<tr><td align=right >Interdire l'upload pour les parents : </td>
<td align=left><input type=radio <?php print $uploadimgoui ?> name=uploadimg value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $uploadimgnon ?> name=uploadimg value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Groupe mail pour les parents : </td>
<td align=left><input type=radio <?php print $grpmailparentoui ?> name=grpmailparent value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $grpmailparentnon ?> name=grpmailparent value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Email servant à la messagerie extérieure.  : </td>
<td align=left>
<?php print $infoemailreply ?>
<input type=<?php print $champemailreply ?> value="<?php print $mailreply ?>" <?php print $diseabled ?>  <?php print $readonlyreplymail ?> name=mailreply size=30>
<?php if ($infoemailreply == "") { ?>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>E</font>mail servant à la gestion de  la messagerie <br /> permettant de transmettre les messages à  une <br /> messagerie externe. <br><font color=red>Un email valide doit être indiqué si vous souhaitez que triade envoi des mails vers l\'extérieur. </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
<?php } ?>
</tr>

<tr><td align=right >Intitulé du mail pour la messagerie extérieure.  : </td>
<td align=left><input type=text value="<?php print $mailnomreply ?>"  <?php print $diseabled ?> name=mailnomreply size=30>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>e champs permet d\'indiqué le nom de l\'expéditeur des mails envoyés à l\'extérieur. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</tr>


<tr><td align=right >Envoi de mail vers une messagerie externe  : </td>
<td align=left><input type=radio <?php print $mailexterneoui." ".$diseabled ?> name=mailexterne value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $mailexternenon." ".$diseabled ?> name=mailexterne value="non" class=btradio1  > non </td>
</tr>

<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Enseignant</b></font></td></tr>

<tr><td align=right >Autorise à valider les D.S.T : </td>
<td align=left><input type=radio <?php print $dstprofoui ?> name=dstprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $dstprofnon ?> name=dstprof value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Autorise&nbsp;à&nbsp;valider&nbsp;les&nbsp;réservations&nbsp;:</td>
<td align=left><input type=radio <?php print $resvprofoui ?> name=resvprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resvprofnon ?> name=resvprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise à valider le calendrier : </td>
<td align=left><input type=radio <?php print $calprofoui ?> name=calprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $calprofnon ?> name=calprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autoriser la saisie des notes version U.S.A.  : </td>
<td align=left><input type=radio <?php print $noteusaoui ?> name=noteusa value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteusanon ?> name=noteusa value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autoriser la saisie de retenue  : </td>
<td align=left><input type=radio <?php print $retenuprofoui ?> name=retenuprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $retenuprofnon ?> name=retenuprof value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Autoriser la saisie des retards  : </td>
<td align=left><input type=radio <?php print $accesabsrtdprofoui ?> name="accesabsrtdprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesabsrtdprofnon ?> name="accesabsrtdprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autoriser la saisie des absences : </td>
<td align=left><input type=radio <?php print $absprofoui ?> name=absprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $absprofnon ?> name=absprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autoriser la saisie des motifs : </td>
<td align=left><input type=radio <?php print $absprofmotifoui ?> name=absprofmotif value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $absprofmotifnon ?> name=absprofmotif value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Autoriser la saisie des présents : </td>
<td align=left><input type=radio <?php print $presentprofoui ?> name=presentprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $presentprofnon ?> name=presentprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès aux informations d'absenteismes : </td>
<td align=left><input type=radio <?php print $absvisuprofoui ?> name=absvisuprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $absvisuprofnon ?> name=absvisuprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès info. médicales : </td>
<td align=left><input type=radio <?php print $infomedicoui ?> name=infomedic value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $infomedicnon ?> name=infomedic value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Maximum de note saisie en même temps : </td>
<td align=left><input type=radio <?php print $noteprof1 ?> name=noteprof value="1" class=btradio1  > 1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteprof2 ?> name=noteprof value="2" class=btradio1  > 2 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteprof3 ?> name=noteprof value="3" class=btradio1  > 3 </td>
</tr>

<tr><td align=right >Accès aux informations des notes des élèves : </td>
<td align=left><input type=radio <?php print $infonoteeleveoui ?> name="infonoteeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $infonoteelevenon ?> name="infonoteeleve" value="non" class=btradio1  > non </td>
</tr>

<script>
function validexamen(etat) {
	if (etat == '1') {
		document.formulaire.examenblanc[0].disabled=false;
		document.formulaire.examends[0].disabled=false;
		document.formulaire.examennamur[0].disabled=false;
		document.formulaire.examenkinshasa[0].disabled=false;
		document.formulaire.examenismap[0].disabled=false;
		document.formulaire.examencieformation[0].disabled=false;
		document.formulaire.exameneepp[0].disabled=false;
		document.formulaire.examen[0].disabled=false;
		document.formulaire.config-examen.disabled=false;
	}else{
		document.formulaire.examenblanc[0].disabled=true;		
		document.formulaire.examenblanc[0].disabled=true;
		document.formulaire.examends[0].disabled=true;
		document.formulaire.examennamur[0].disabled=true;
		document.formulaire.examenkinshasa[0].disabled=true;
		document.formulaire.examenismap[0].disabled=true;
		document.formulaire.examen[0].disabled=true;
		document.formulaire.examenblanc[1].checked=true;		
		document.formulaire.examenblanc[1].checked=true;
		document.formulaire.examends[1].checked=true;
		document.formulaire.examennamur[1].checked=true;
		document.formulaire.examenkinshasa[1].checked=true;
		document.formulaire.examenismap[1].checked=true;
		document.formulaire.examencieformation[1].checked=true;
		document.formulaire.exameneepp[1].checked=true;
		document.formulaire.examen[1].checked=true;
		document.formulaire.config-examen.disabled=true;
	}

}

</script>

<tr><td align=right >Possibilité de saisir des notes d'examens : </td>
<td align=left><input type=radio <?php print $noteexamenoui ?> name="noteexamen" value="oui" class=btradio1 onclick="validexamen('1')"  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteexamennon ?> name="noteexamen" value="non" class=btradio1 onclick="validexamen('0')" > non </td>
</tr>

<tr><td align=right >Configurer les types d'examen : </td>
<?php 
if ($noteexamennon == "checked") {
	$disabledexamen="disabled='disabled'";
}
?>
<td align=left><input type='button' value='Acceder' class='bouton2' onclick="open('config-examen.php','_parent','')" <?php print $disabledexamen ?> id='config-examen' name='config-examen' /></td>
</tr>

	
<?php 
	if ($noteexamennon == "checked") {
		$disabledexamen="disabled='disabled'";
		$examenblancnon="checked='checked'";
		$examendsnon="checked='checked'";
		$examendsnon="checked='checked'";
		$examennamurnon="checked='checked'";
		$examenkinshasanon="checked='checked'";
		$examenismapnon="checked='checked'";
		$examennon="checked='checked'";
		$examencieformationnon="checked='checked'";
		$exameneeppnon="checked='checked'";
		$examenpigiernimesnon="checked='checked'";
		$examenispacademiesnon="checked='checked'";
		$examenbrevetcollegenon="checked='checked'";
		$examenipacnon="checked='checked'";

	}else{
		$disabledexamen="";
	}
?>

	<tr ><td align=right >Examen type : <i>brevet collège</i> : </td>
	<td align=left><input type=radio <?php print $examenbrevetcollegeoui ?> name="examenbrevetcollege" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               	       <input type=radio <?php print $examenbrevetcollegenon ?> name="examenbrevetcollege" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr ><td align=right >Examen type : <i>Blanc</i> : </td>
	<td align=left><input type=radio <?php print $examenblancoui ?> name="examenblanc" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               	       <input type=radio <?php print $examenblancnon ?> name="examenblanc" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

        <tr ><td align=right >Examen type : <i>Spécif Kinshasa  </i> : </td>
        <td align=left><input type=radio <?php print $examenkinshasaoui ?> name="examenkinshasa" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
                       <input type=radio <?php print $examenkinshasanon ?> name="examenkinshasa" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
        </tr>

	<tr ><td align=right >Examen type : <i>DS </i> : </td>
	<td align=left><input type=radio <?php print $examendsoui ?> name="examends" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examendsnon ?> name="examends" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr ><td align=right >Examen type : <i>Spécif Namur </i> : </td>
	<td align=left><input type=radio <?php print $examennamuroui ?> name="examennamur" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examennamurnon ?> name="examennamur" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr ><td align=right >Examen type : <i>Spécif IPAC </i> : </td>
	<td align=left><input type=radio <?php print $examenipacoui ?> name="examenipac" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examenipacnon ?> name="examenipac" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr><td align=right >Examen type : <i>Spécif ISMAP</i> : </td>
	<td align=left><input type=radio <?php print $examenismapoui ?> name="examenismap" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examenismapnon ?> name="examenismap" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>


	<tr><td align=right >Examen type : <i>Spécif ISP Academies</i> : </td>
	<td align=left><input type=radio <?php print $examenispacademiesoui ?> name="examenispacademies" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examenispacademiesnon ?> name="examenispacademies" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>


	<tr><td align=right >Examen type : <i>Spécif Pigier </i> : </td>
	<td align=left><input type=radio <?php print $examenpigiernimesoui ?> name="examenpigiernimes" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examenpigiernimesnon ?> name="examenpigiernimes" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr><td align=right >Examen type : <i>Spécif Vatel Réunion </i> : </td>
	<td align=left><input type=radio <?php print $examenvatelreunionoui ?> name="examenvatelreunion" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examenvatelreunionnon ?> name="examenvatelreunion" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>
	

	<tr><td align=right >Examen type : <i>Spécif Cie. Formation</i> : </td>
	<td align=left><input type=radio <?php print $examencieformationoui ?> name="examencieformation" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examencieformationnon ?> name="examencieformation" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr><td align=right >Examen type : <i>Spécif EEPP</i> : </td>
	<td align=left><input type=radio <?php print $exameneeppoui ?> name="exameneepp" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $exameneeppnon ?> name="exameneepp" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr><td align=right >Examen type : <i>JTC</i> : </td>
	<td align=left><input type=radio <?php print $examenjtcoui ?> name="examenjtc" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examenjtcnon ?> name="examenjtc" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>

	<tr><td align=right >Examen type : <i>Examen</i> : </td>
	<td align=left><input type=radio <?php print $examenoui ?> name="examen" <?php print $disabledexamen ?> value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	               <input type=radio <?php print $examennon ?> name="examen" <?php print $disabledexamen ?> value="non" class=btradio1  > non </td>
	</tr>


</tr></td>

<tr><td align=right >Accès au module forum  : </td>
<td align=left><input type=radio <?php print $accesforumprofoui ?> name="accesforumprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesforumprofnon ?> name="accesforumprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un enseignant  : </td>
<td align=left><input type=radio <?php print $profenvoiprofoui ?> name="profenvoiprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoiprofnon ?> name="profenvoiprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un tuteur de stage  : </td>
<td align=left><input type=radio <?php print $profenvoituteuroui ?> name="profenvoituteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoituteurnon ?> name="profenvoituteur" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un groupe  : </td>
<td align=left><input type=radio <?php print $profenvoigroupeoui ?> name="profenvoigroupe" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoigroupenon ?> name="profenvoigroupe" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un parent  : </td>
<td align=left><input type=radio <?php print $profenvoiparentoui ?> name="profenvoiparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoiparentnon ?> name="profenvoiparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un élève  : </td>
<td align=left><input type=radio <?php print $profenvoieleveoui ?> name="profenvoieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoielevenon ?> name="profenvoieleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à la direction  : </td>
<td align=left><input type=radio <?php print $profenvoidirecoui ?> name="profenvoidirec" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoidirecnon ?> name="profenvoidirec" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à la vie scolaire  : </td>
<td align=left><input type=radio <?php print $profenvoiscolaireoui ?> name="profenvoiscolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoiscolairenon ?> name="profenvoiscolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message vers l'extérieur  : </td>
<td align=left><input type=radio <?php print $profenvoiextoui ?> name="profenvoiext" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoiextnon ?> name="profenvoiext" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un groupe d'élève  : </td>
<td align=left><input type=radio <?php print $profenvoigrpelevoui ?> name="profenvoigrpelev" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoigrpelevnon ?> name="profenvoigrpelev" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux délégués  : </td>
<td align=left><input type=radio <?php print $profenvoidelegueoui ?> name="profenvoidelegue" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoideleguenon ?> name="profenvoidelegue" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux personnels  : </td>
<td align=left><input type=radio <?php print $profenvoipersonneloui ?> name="profenvoipersonnel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profenvoipersonnelnon ?> name="profenvoipersonnel" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autoriser le changement de mot de passe : </td>
<td align=left><input type=radio <?php print $pwdprofoui ?> name="pwdprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $pwdprofnon ?> name="pwdprof" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Visualisation de l'EDT de toutes les classes : </td>
<td align=left><input type=radio <?php print $edtvisuoui ?> name="edtvisu" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $edtvisunon ?> name="edtvisu" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right > Nbr max de caractère pour les bulletins : </td>
<td align=left><select name="nbcarbull" >
	<?php
	if (defined("NBCARBULL")) {
		print "<option value='".NBCARBULL."' STYLE='color:#000066;background-color:#FCE4BA' >".NBCARBULL."</option>";
		$selected="";
	}else{
		$selected="selected='selected'"; 
	}
	?>
		<option value="3000" class="bouton2" >3000</option>
		<option value="2000" class="bouton2" >2000</option>
		<option value="1200" class="bouton2" >1200</option>
		<option value="1100" class="bouton2" >1100</option>
		<option value="1000" class="bouton2" >1000</option>
		<option value="900" class="bouton2" >900</option>
		<option value="800" class="bouton2" >800</option>
		<option value="700" class="bouton2" >700</option>
		<option value="600" class="bouton2" >600</option>
		<option value="500" class="bouton2" >500</option>
		<option value="400" class="bouton2" <?php print $selected ?> >400</option>
		<option value="350" class="bouton2" >350</option>
		<option value="300" class="bouton2" >300</option>
		<option value="280" class="bouton2" >280</option>
		<option value="250" class="bouton2" >250</option>
		<option value="200" class="bouton2" >200</option>
		<option value="140" class="bouton2" >140</option>
		</select>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>hoisissez le nombre de caractère maximum pour les commentaires des bulletins trimestriels ou semestriels pour les enseignants. Au dela de 400 risque de chevauchement de commentaire lors de la sortie des bulletins. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right >Paiement avec TVA pour les vacations : </td>
<td align=left><input type="radio" <?php print $tvavatationoui ?> name="tvavatation" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type="radio" <?php print $tvavatationnon ?> name="tvavatation" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Taux de la TVA pour les vacations : </td>
<td align=left><input type="text"  name="tvavatationtaux" value="<?php print $tvavatationtaux ?>" size="6" ></td></tr>


<tr><td align=right >Gestion des entreprises en stage pro : </td>
<td align=left><input type="radio" <?php print $profentroui ?> name="profentr" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type="radio" <?php print $profentrnon ?> name="profentr" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Autorise la gestion des conventions de stage : </td>
<td align=left><input type="radio" <?php print $profentrconventionoui ?> name="profentrconvention" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type="radio" <?php print $profentrconventionnon ?> name="profentrconvention" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Gestion des élèves en entreprise : </td>
<td align=left><input type=radio <?php print $profstageetudiantoui ?> name="profstageetudiant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profstageetudiantnon ?> name="profstageetudiant" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Gestion des affectations élèves aux entreprises : </td>
<td align=left><input type=radio <?php print $stageetudiantadminprofoui ?> name="stageetudiantadminprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageetudiantadminprofnon ?> name="stageetudiantadminprof" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Choix de la matière par défaut (notes et abs) : </td>
<td align=left><input type=radio <?php print $choixmatiereprofoui ?> name="choixmatiereprof" value="0" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $choixmatiereprofnon ?> name="choixmatiereprof" value="1" class=btradio1  > non </td>
</tr>


<tr><td align=right >Autorise l'accès au carnet de suivi : </td>
<td align=left><input type=radio <?php print $carnetsuiviprofoui ?> name="carnetsuiviprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $carnetsuiviprofnon ?> name="carnetsuiviprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès au trombinoscope de toutes les classes : </td>
<td align=left><input type=radio <?php print $tombiallclasseprofoui ?> name="tombiallclasseprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tombiallclasseprofnon ?> name="tombiallclasseprof" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Accès au module d'entretien : </td>
<td align=left><input type=radio <?php print $entretienprofoui ?> name="entretienprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $entretienprofnon ?> name="entretienprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise la modif./Supp. des notes après Trimestre/Semestre  : </td>
<td align=left>
<input type=radio <?php print $modifnoteapresarretoui ?> name="modifnoteapresarret" value="oui" class=btradio1   > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $modifnoteapresarretnon ?> name="modifnoteapresarret" value="non" class=btradio1   > non
</tr>


<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Professeur Principal</b></font></td></tr>

<tr><td align=right >Autoriser à imprimer son bulletin classe : </td>
<td align=left><input type=radio <?php print $profpbulletinoui ?> name="profpbulletin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpbulletinnon ?> name="profpbulletin" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Autoriser à imprimer son relevé de classe : </td>
<td align=left><input type=radio <?php print $profpreleveoui ?> name="profpreleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profprelevenon ?> name="profpreleve" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Autoriser à vérifier son bulletin classe : </td>
<td align=left><input type=radio <?php print $profpbulletinverifoui ?> name="profpbulletinverif" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpbulletinverifnon ?> name="profpbulletinverif" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Accès info. médicales : </td>
<td align=left><input type=radio <?php print $infomedic2oui ?> name=infomedic2 value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $infomedic2non ?> name=infomedic2 value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Permet la création de ses groupes de classe : </td>
<td align=left><input type=radio <?php print $groupeclasseprofpoui ?> name=groupeclasseprofp value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $groupeclasseprofpnon ?> name=groupeclasseprofp value="non" class=btradio1  > non </td>
</tr>




<tr><td align=right > Nbr max de caractère pour les bulletins : </td>
<td align=left><select name="nbcarbullprofp" >
	<?php
	if (defined("NBCARBULLPROFP")) {
		print "<option value='".NBCARBULLPROFP."' STYLE='color:#000066;background-color:#FCE4BA' >".NBCARBULLPROFP."</option>";
	}		
	?>
		<option value="1200" class="bouton2" >1200</option>
		<option value="1100" class="bouton2" >1100</option>
		<option value="1000" class="bouton2" >1000</option>
		<option value="900" class="bouton2" >900</option>
		<option value="800" class="bouton2" >800</option>
		<option value="700" class="bouton2" >700</option>
		<option value="600" class="bouton2" >600</option>
		<option value="500" class="bouton2" >500</option>
		<option value="400" class="bouton2" >400</option>
		<option value="300" class="bouton2" >300</option>
		<option value="250" class="bouton2" >250</option>
		</select>
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>C</font>hoisissez le nombre de caractère maximum pour les commentaires des bulletins trimestriels ou semestriels pour les professeurs principaux et la direction.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>


<tr><td align=right >Gestion des entreprises pour les stages : </td>
<td align=left><input type=radio <?php print $gestionentrepriseprofpoui ?> name="gestionentrepriseprofp" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $gestionentrepriseprofpnon ?> name="gestionentrepriseprofp" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Autorise la gestion des conventions de stage : </td>
<td align=left><input type="radio" <?php print $profpentrconventionoui ?> name="profpentrconvention" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type="radio" <?php print $profpentrconventionnon ?> name="profpentrconvention" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Permet la création de tuteur de stage : </td>
<td align=left><input type=radio <?php print $profpcreatetuteuroui ?> name=profpcreatetuteur value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpcreatetuteurnon ?> name=profpcreatetuteur value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Accès au module Vidéo Projecteur : </td>
<td align=left><input type=radio <?php print $profpVideoProjooui ?> name=profpVideoProjo value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpVideoProjonon ?> name=profpVideoProjo value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès au module de modification des affectations : </td>
<td align=left><input type=radio <?php print $profpmodifaffectoui ?> name='profpmodifaffect' value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpmodifaffectnon ?> name='profpmodifaffect' value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'accès au module de gestion de notes scolaires : </td>
<td align=left><input type=radio <?php print $profpaccesnoteoui ?> name='profpaccesnote' value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpaccesnotenon ?> name='profpaccesnote' value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'accès au module de visa direction : </td>
<td align=left><input type=radio <?php print $profpaccesvisadirectionoui ?> name='profpaccesvisadirection' value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpaccesvisadirectionnon ?> name='profpaccesvisadirection' value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'accès au module de gestion d'absence : </td>
<td align=left><input type=radio <?php print $profpaccesabsrtdoui ?> name='profpaccesabsrtd' value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $profpaccesabsrtdnon ?> name='profpaccesabsrtd' value="non" class=btradio1  > non </td>
</tr>


<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Vie Scolaire</b></font></td></tr>

<tr><td align=right >Accès au module historique cmd  : </td>
<td align=left><input type=radio <?php print $viescolairehistocmdoui ?> name="viescolairehistocmd" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $viescolairehistocmdnon ?> name="viescolairehistocmd" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Stage Pro : Gestion des dates de stage : </td>
<td align=left><input type=radio <?php print $viescolairestagedateoui ?> name="viescolairestagedate" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $viescolairestagedatenon ?> name="viescolairestagedate" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Stage Pro : Ajouter / Modifier / Supprimer entreprise : </td>
<td align=left><input type=radio <?php print $viescolairestageentoui ?> name="viescolairestageent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $viescolairestageentnon ?> name="viescolairestageent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Stage Pro : Affecter / Modifier / Supprimer étudiant : </td>
<td align=left><input type=radio <?php print $viescolairestageetudiantoui ?> name="viescolairestageetudiant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $viescolairestageetudiantnon ?> name="viescolairestageetudiant" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Mode notation : Affecter / Modifier / Supprimer : </td>
<td align=left><input type=radio <?php print $viescolairenoteenseignantoui ?> name="viescolairenoteenseignant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $viescolairenoteenseignantnon ?> name="viescolairenoteenseignant" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise la modification des fiches étudiantes : </td>
<td align=left><input type=radio <?php print $viescolairemodifetudiantoui ?> name="viescolairemodifetudiant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $viescolairemodifetudiantnon ?> name="viescolairemodifetudiant" value="non" class=btradio1  > non </td>
</tr>




<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Tuteur de stage</b></font></td></tr>

<tr><td align=right >Peut recevoir des messages  : </td>
<td align=left><input type=radio <?php print $accesmesstuteuroui ?> name="accesmesstuteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesmesstuteurnon ?> name="accesmesstuteur" value="non" class=btradio1  > non </td>
</tr>



<tr><td align=right >Peut envoyer des messages  : </td>
<td align=left><input type=radio <?php print $accesmessenvoituteuroui ?> name="accesmessenvoituteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesmessenvoituteurnon ?> name="accesmessenvoituteur" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages à des enseignants : </td>
<td align=left><input type=radio <?php print $tuteurenvoiprofoui ?> name="tuteurenvoiprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoiprofnon ?> name="tuteurenvoiprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux tuteurs de stage : </td>
<td align=left><input type=radio <?php print $tuteurenvoituteuroui ?> name="tuteurenvoituteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoituteurnon ?> name="tuteurenvoituteur" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux parents : </td>
<td align=left><input type=radio <?php print $tuteurenvoiparentoui ?> name="tuteurenvoiparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoiparentnon ?> name="tuteurenvoiparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux élèves : </td>
<td align=left><input type=radio <?php print $tuteurenvoieleveoui ?> name="tuteurenvoieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoielevenon ?> name="tuteurenvoieleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages à la direction : </td>
<td align=left><input type=radio <?php print $tuteurenvoidirecoui ?> name="tuteurenvoidirec" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoidirecnon ?> name="tuteurenvoidirec" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages à la vie scolaire : </td>
<td align=left><input type=radio <?php print $tuteurenvoiscolaireoui ?> name="tuteurenvoiscolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoiscolairenon ?> name="tuteurenvoiscolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message vers l'extérieur  : </td>
<td align=left><input type=radio <?php print $tuteurenvoiextoui ?> name="tuteurenvoiext" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoiextnon ?> name="tuteurenvoiext" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un groupe d'élève  : </td>
<td align=left><input type=radio <?php print $tuteurenvoigrpelevoui ?> name="tuteurenvoigrpelev" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoigrpelevnon ?> name="tuteurenvoigrpelev" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux délégués  : </td>
<td align=left><input type=radio <?php print $tuteurenvoidelegueoui ?> name="tuteurenvoidelegue" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoideleguenon ?> name="tuteurenvoidelegue" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux personnels  : </td>
<td align=left><input type=radio <?php print $tuteurenvoipersonneloui ?> name="tuteurenvoipersonnel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurenvoipersonnelnon ?> name="tuteurenvoipersonnel" value="non" class=btradio1  > non </td>
</tr>





<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Parent</b></font></td></tr>

<tr><td align=right >Peut recevoir des messages  : </td>
<td align=left><input type=radio <?php print $accesmessparentoui ?> name="accesmessparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesmessparentnon ?> name="accesmessparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages  : </td>
<td align=left><input type=radio <?php print $accesmessenvoiparentoui ?> name="accesmessenvoiparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesmessenvoiparentnon ?> name="accesmessenvoiparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages à des enseignants : </td>
<td align=left><input type=radio <?php print $parentenvoiprofoui ?> name="parentenvoiprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoiprofnon ?> name="parentenvoiprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux tuteurs de stage : </td>
<td align=left><input type=radio <?php print $parentenvoituteuroui ?> name="parentenvoituteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoituteurnon ?> name="parentenvoituteur" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux parents : </td>
<td align=left><input type=radio <?php print $parentenvoiparentoui ?> name="parentenvoiparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoiparentnon ?> name="parentenvoiparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages aux élèves : </td>
<td align=left><input type=radio <?php print $parentenvoieleveoui ?> name="parentenvoieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoielevenon ?> name="parentenvoieleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages à la direction : </td>
<td align=left><input type=radio <?php print $parentenvoidirecoui ?> name="parentenvoidirec" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoidirecnon ?> name="parentenvoidirec" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages à la vie scolaire : </td>
<td align=left><input type=radio <?php print $parentenvoiscolaireoui ?> name="parentenvoiscolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoiscolairenon ?> name="parentenvoiscolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message vers l'extérieur  : </td>
<td align=left><input type=radio <?php print $parentenvoiextoui ?> name="parentenvoiext" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoiextnon ?> name="parentenvoiext" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un groupe d'élève  : </td>
<td align=left><input type=radio <?php print $parentenvoigrpelevoui ?> name="parentenvoigrpelev" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoigrpelevnon ?> name="parentenvoigrpelev" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux délégués  : </td>
<td align=left><input type=radio <?php print $parentenvoidelegueoui ?> name="parentenvoidelegue" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoideleguenon ?> name="parentenvoidelegue" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux personnels  : </td>
<td align=left><input type=radio <?php print $parentenvoipersonneloui ?> name="parentenvoipersonnel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentenvoipersonnelnon ?> name="parentenvoipersonnel" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès au module note  : </td>
<td align=left><input type=radio <?php print $accesnoteparentoui ?> name="accesnoteparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesnoteparentnon ?> name="accesnoteparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès au module forum  : </td>
<td align=left><input type=radio <?php print $accesforumparentoui ?> name="accesforumparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesforumparentnon ?> name="accesforumparent" value="non" class=btradio1  > non </td>
</tr>



<tr><td align=right >Autoriser le changement de mot de passe  : </td>
<td align=left><input type=radio <?php print $pwdparentoui ?> name="pwdparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $pwdparentnon ?> name="pwdparent" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Accès au module trombinoscope  : </td>
<td align=left><input type=radio <?php print $trombinoParentoui ?> name="trombinoParent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $trombinoParentnon ?> name="trombinoParent" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Accès au module plan de classe : </td>
<td align=left><input type=radio <?php print $planclasseParentoui ?> name="planclasseParent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planclasseParentnon ?> name="planclasseParent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Accès à la messagerie pour les délégués  : </td>
<td align=left><input type=radio <?php print $messagedelegueparentoui ?> name="messagedelegueparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagedelegueparentnon ?> name="messagedelegueparent" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Autorise la création d'entreprise (stage pro)  : </td>
<td align=left><input type=radio <?php print $createntrparentoui ?> name="createntrparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $createntrparentnon ?> name="createntrparent" value="non" class=btradio1  > non
</td></tr>




<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Elève</b></font></td></tr>

<tr><td align=right >Peut recevoir des messages : </td>
<td align=left><input type=radio <?php print $accesmesseleveoui ?> name="accesmesseleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesmesselevenon ?> name="accesmesseleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages : </td>
<td align=left><input type=radio <?php print $accesmessenvoieleveoui ?> name="accesmessenvoieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesmessenvoielevenon ?> name="accesmessenvoieleve" value="non" class=btradio1  > non </td>
</tr>



<tr><td align=right >Peut envoyer des messages à des enseignants : </td>
<td align=left><input type=radio <?php print $eleveenvoiprofoui ?> name="eleveenvoiprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoiprofnon ?> name="eleveenvoiprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux tuteurs de stage : </td>
<td align=left><input type=radio <?php print $eleveenvoituteuroui ?> name="eleveenvoituteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoituteurnon ?> name="eleveenvoituteur" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages aux parents : </td>
<td align=left><input type=radio <?php print $eleveenvoiparentoui ?> name="eleveenvoiparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoiparentnon ?> name="eleveenvoiparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages aux élèves : </td>
<td align=left><input type=radio <?php print $eleveenvoieleveoui ?> name="eleveenvoieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoielevenon ?> name="eleveenvoieleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages à la direction : </td>
<td align=left><input type=radio <?php print $eleveenvoidirecoui ?> name="eleveenvoidirec" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoidirecnon ?> name="eleveenvoidirec" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Peut envoyer des messages à la vie scolaire : </td>
<td align=left><input type=radio <?php print $eleveenvoiscolaireoui ?> name="eleveenvoiscolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoiscolairenon ?> name="eleveenvoiscolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message vers l'extérieur  : </td>
<td align=left><input type=radio <?php print $eleveenvoiextoui ?> name="eleveenvoiext" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoiextnon ?> name="eleveenvoiext" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un groupe d'élève  : </td>
<td align=left><input type=radio <?php print $eleveenvoigrpelevoui ?> name="eleveenvoigrpelev" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoigrpelevnon ?> name="eleveenvoigrpelev" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux délégués  : </td>
<td align=left><input type=radio <?php print $eleveenvoidelegueoui ?> name="eleveenvoidelegue" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoideleguenon ?> name="eleveenvoidelegue" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message aux personnels  : </td>
<td align=left><input type=radio <?php print $eleveenvoipersonneloui ?> name="eleveenvoipersonnel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $eleveenvoipersonnelnon ?> name="eleveenvoipersonnel" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Accès au module note  : </td>
<td align=left><input type=radio <?php print $accesnoteeleveoui ?> name="accesnoteeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesnoteelevenon ?> name="accesnoteeleve" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Accès au module forum  : </td>
<td align=left><input type=radio <?php print $accesforumeleveoui ?> name="accesforumeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesforumelevenon ?> name="accesforumeleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autoriser le changement de mot de passe  : </td>
<td align=left><input type=radio <?php print $pwdeleveoui ?> name="pwdeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $pwdelevenon ?> name="pwdeleve" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Accès au module trombinoscope  : </td>
<td align=left><input type=radio <?php print $trombinoEleveoui ?> name="trombinoEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $trombinoElevenon ?> name="trombinoEleve" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Accès à la messagerie pour les délégués  : </td>
<td align=left><input type=radio <?php print $messagedelegueeleveoui ?> name="messagedelegueeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagedelegueelevenon ?> name="messagedelegueeleve" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Autorise l'élève à changer son email  : </td>
<td align=left><input type=radio <?php print $emailchangeeleveoui ?> name="emailchangeeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $emailchangeelevenon ?> name="emailchangeeleve" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Autorise l'élève à changer sa photo (trombinoscope)  : </td>
<td align=left><input type=radio <?php print $modiftrombieleveoui ?> name="modiftrombieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modiftrombielevenon ?> name="modiftrombieleve" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Autorise la création d'entreprise (stage pro)  : </td>
<td align=left><input type=radio <?php print $createntreleveoui ?> name="createntreleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $createntrelevenon ?> name="createntreleve" value="non" class=btradio1  > non
</td></tr>


<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration des membres de type personnel</b></font></td></tr>

<tr><td align=right >Peut envoyer des messages à des enseignants : </td>
<td align=left><input type=radio <?php print $personnelenvoiprofoui ?> name="personnelenvoiprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $personnelenvoiprofnon ?> name="personnelenvoiprof" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages aux parents : </td>
<td align=left><input type=radio <?php print $personnelenvoiparentoui ?> name="personnelenvoiparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $personnelenvoiparentnon ?> name="personnelenvoiparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Peut envoyer des messages aux élèves : </td>
<td align=left><input type=radio <?php print $personnelenvoieleveoui ?> name="personnelenvoieleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $personnelenvoielevenon ?> name="personnelenvoieleve" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Autorise l'envoi de message vers l'extérieur  : </td>
<td align=left><input type=radio <?php print $personnelenvoiextoui ?> name="personnelenvoiext" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $personnelenvoiextnon ?> name="personnelenvoiext" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Autorise l'envoi de message à un groupe d'élève  : </td>
<td align=left><input type=radio <?php print $personnelenvoigrpelevoui ?> name="personnelenvoigrpelev" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $personnelenvoigrpelevnon ?> name="personnelenvoigrpelev" value="non" class=btradio1  > non </td>
</tr>






<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Sécurité</b></font></td></tr>

<tr><td align=right >Autoriser le changement de mot de passe : </td>
<td align=left><input type=radio <?php print $pwdoui ?> name="pwd" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $pwdnon ?> name="pwd" value="non" class=btradio1  > non 
<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>P</font>our les comptes directions, tuteurs de stage, personnels et Vie Scolaire.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>


<tr><td align=right >Niveau de sécurité des mots de passe : </td>
<td align=left><input type=radio <?php print $securite1 ?> name=securite value="1" class=btradio1  > 1 
<A href='#' onMouseOver="AffBulle2('Sécurité Faible','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>M</font>inimum 4 caractères.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
&nbsp;&nbsp;
<input type=radio <?php print $securite2 ?> name=securite value="2" class=btradio1  > 2 
<A href='#' onMouseOver="AffBulle2('Sécurité Moyenne','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>M</font>inimum 8 caractères alphanumériques.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
&nbsp;&nbsp;
<input type=radio <?php print $securite3 ?> name=securite value="3" class=btradio1  > 3 
<A href='#' onMouseOver="AffBulle2('Sécurité Elevé','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>M</font>inimum 8 caractères alphanumériques, majuscules<br> et minuscules</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align="center" border=0></A>
</td>
</tr>



<tr><td align=right >Activer la vérification du mot de passe  : </td>
<td align=left><input type=radio <?php print $verifpassoui ?> name="verifpass" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $verifpassnon ?> name="verifpass" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Supprimer la demande de mot de passe : </td>
<td align=left><input type=radio <?php print $supppassmailoui ?> name="supppassmail" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $supppassmailnon ?> name="supppassmail" value="non" class=btradio1  > non 
&nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Sécurité Mail','../image/commun/warning.jpg','<font face=Verdana size=1><font color=red>P</font>ermet de supprimer la demande de mot de passe lors de la consultation de la messagerie Triade via une messagerie externe. <br><br> <font color=red>ATTENTION CETTE OPTION DOIT ETRE A \'NON\' POUR DES QUESTIONS DE SECURITE.</font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>


<tr><td align=right >Autorisé la complétion pour la connexion : </td>
<td align=left><input type=radio <?php print $autocompletionloginoui ?> name="autocompletionlogin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $autocompletionloginnon ?> name="autocompletionlogin" value="non" class=btradio1  > non 
</td></tr>

<tr><td align=right > Durée de la session (direction et vie scolaire) : </td>
<td align=left><select name="timesession" > 
	<?php
	if (defined("TIMESESSION")) {
		if (TIMESESSION == "3") { $val1="15 minutes"; }
		if (TIMESESSION == "6") { $val1="30 minutes"; }
		if (TIMESESSION == "9") { $val1="45 minutes"; }
		if (TIMESESSION == "12") { $val1="1 heure"; }
		if (TIMESESSION == "24") { $val1="2 heures"; }
		if (TIMESESSION == "36") { $val1="3 heures"; }
		print "<option value='".TIMESESSION."' STYLE='color:#000066;background-color:#FCE4BA' >".$val1."</option>";
	}else{

		$selectedTime="selected='selected'";
	}		
	?>
		<option value="3" class=bouton2 >15 minutes</option>
		<option value="6" class=bouton2 <?php print $selectedTime ?> >30 minutes</option>
		<option value="9" class=bouton2 >45 minutes</option>
		<option value="12" class=bouton2 >1 heure</option>
		<option value="24" class=bouton2 >2 heures</option>
		<option value="36" class=bouton2 >3 heures</option>
		</select>&nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Sécurité Session','../image/commun/warning.jpg','<font face=Verdana size=1><font color=red>C</font>ette option ne concerne que les comptes direction et vie scolaire. Elle permet d\'avoir une durée de connexion plus importante. Si aucune activité n\'est réalisé avant la fin de la durée, le compte est automatiquement mis en veille. </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>


<tr><td align=right > Durée de la session (enseignant) : </td>
<td align=left><select name="timesessioneprof" > 
	<?php
	if (defined("TIMESESSIONPROF")) {
		if (TIMESESSIONPROF == "3") { $val1="15 minutes"; }
		if (TIMESESSIONPROF == "6") { $val1="30 minutes"; }
		if (TIMESESSIONPROF == "9") { $val1="45 minutes"; }
		if (TIMESESSIONPROF == "12") { $val1="1 heure"; }
		if (TIMESESSIONPROF == "24") { $val1="2 heures"; }
		if (TIMESESSIONPROF == "36") { $val1="3 heures"; }
		print "<option value='".TIMESESSIONPROF."' STYLE='color:#000066;background-color:#FCE4BA' >".$val1."</option>";
	}else{

		$selectedTime="selected='selected'";
	}		
	?>
		<option value="3" class=bouton2 >15 minutes</option>
		<option value="6" class=bouton2 <?php print $selectedTime ?> >30 minutes</option>
		<option value="9" class=bouton2 >45 minutes</option>
		<option value="12" class=bouton2 >1 heure</option>
		<option value="24" class=bouton2 >2 heures</option>
		<option value="36" class=bouton2 >3 heures</option>
		</select>&nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Sécurité Session','../image/commun/warning.jpg','<font face=Verdana size=1><font color=red>C</font>ette option ne concerne que les comptes direction et vie scolaire. Elle permet d\'avoir une durée de connexion plus importante. Si aucune activité n\'est réalisé avant la fin de la durée, le compte est automatiquement mis en veille. </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right >Activer la sécurité sur le module entretien individuel  : </td>
<td align=left><input type=radio <?php print $passmoduleindividueloui ?> name="passmoduleindividuel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $passmoduleindividuelnon ?> name="passmoduleindividuel" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Activer la sécurité sur le module dossier médical  : </td>
<td align=left><input type=radio <?php print $passmodulemedicaloui ?> name="passmodulemedical" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $passmodulemedicalnon ?> name="passmodulemedical" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Activer la sécurité sur le module bilan financier  : </td>
<td align=left><input type=radio <?php print $passmodulebilanfinancieroui ?> name="passmodulebilanfinancier" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $passmodulebilanfinanciernon ?> name="passmodulebilanfinancier" value="non" class=btradio1  > non
</td></tr>


<tr><td align=right >Autoriser la demande de mot de passe oublié  : </td>
<td align=left><input type=radio <?php print $passoublieoui ?> name="passoublie" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $passoublienon ?> name="passoublie" value="non" class=btradio1  > non
</td></tr>


<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Générale</b></font></td></tr>


<tr><td align=right >Activer la vérification de saisie de notes  : </td>
<td align=left><input type=radio <?php print $verifsujetnoteoui ?> name="verifsujetnote" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $verifsujetnotenon ?> name="verifsujetnote" value="non" class=btradio1  > non &nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>R</font>ecommandé à <u>oui</u>. Dans le cas d\'un problème d\'activation des notes, positionner cette option à <u>non</u>. (Cela peut arriver en fonction de la configuration de votre DMZ)  </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right >Choix de la monnaie  : </td>
<td align=left>
<?php 
$selectedeuro="";
$selecteddollar="";
$selecteddinars="";
$selectedlivre="";
$selectedyen="";
$selecteddirham="";
$selectedFCFA="";
$selectedLAK="";
$selectedCFP="";
$selectedCHF="";
$selecteddollarCAD="";
$selectedFC="";

if (MONNAIE == "euro") 	{ $selectedeuro="selected='selected'"; }
if (MONNAIE == "dollar"){ $selecteddollar="selected='selected'"; }
if (MONNAIE == "dinars"){ $selecteddinars="selected='selected'"; }
if (MONNAIE == "CHF"){ $selectedchf="selected='selected'"; }
if (MONNAIE == "livre") { $selectedlivre="selected='selected'"; }
if (MONNAIE == "yen") 	{ $selectedyen="selected='selected'"; }
if (MONNAIE == "dirham") { $selecteddirham="selected='selected'"; }
if (MONNAIE == "FCFA") { $selectedFCFA="selected='selected'"; }
if (MONNAIE == "LAK") { $selectedLAK="selected='selected'"; }
if (MONNAIE == "CFP") { $selectedCFP="selected='selected'"; }
if (MONNAIE == "HTG") { $selectedHTG="selected='selected'"; }
if (MONNAIE == "CHF") { $selectedCHF="selected='selected'"; }
if (MONNAIE == "dollarCAD") { $selecteddollarCAD="selected='selected'"; }
if (MONNAIE == "FC") { $selectedFC="selected='selected'"; }

?>
<select name="monnaie" >
<option value="euro" 	class=bouton2 <?php print $selectedeuro ?> >Euro (&euro;) </option>
<option value="dollar" 	class=bouton2 <?php print $selecteddollar ?> >Dollar (&#36;) </option>
<option value="dollarCAD" class=bouton2 <?php print $selecteddollarCAD ?> >Dollar CAD (&#36;) </option>
<option value="dirham" 	class=bouton2 <?php print $selecteddirham ?> >Dirham (Dh) </option>
<option value="dinars" 	class=bouton2 <?php print $selecteddinars ?> >Dinars (Dt)</option>
<option value="CHF" 	class=bouton2 <?php print $selectedchf ?> >Franc Suisse (CHF)</option>
<option value="FC" 	class=bouton2 <?php print $selectedFC ?> >Franc Congolais (FC)</option>
<option value="FCFA" 	class=bouton2 <?php print $selectedFCFA ?> >Franc CFA (FCFA) </option>
<option value="CFP" 	class=bouton2 <?php print $selectedCFP ?> >Francs Pacifiques (CFP)</option>
<option value="HTG" 	class=bouton2 <?php print $selectedHTG ?> >Gourdes (HTG)</option>
<option value="LAK" 	class=bouton2 <?php print $selectedLAK ?> >Kip Laotien (LAK) </option>
<option value="livre" 	class=bouton2 <?php print $selectedlivre ?> >Livre sterling (&pound;) </option>
<option value="yen" 	class=bouton2 <?php print $selectedyen ?> >Yen (&yen;) </option>
</select>&nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>P</font>our ajouter une autre monnaie, contactez nous via support@triade-educ.com</font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<?php $nbdecimalmonnaie=(defined("NBDECIMALMONNAIE")) ? NBDECIMALMONNAIE : "2"; ?>
<tr><td align=right >Nombre de décimal pour la monnaie : </td>
<td align=left><input type=text  name="nbdecimalmonnaie" value="<?php print $nbdecimalmonnaie ?>" size=4 maxlength=5>
</td></tr>

<tr><td align=right >Activer l'audio au démarrage  : </td>
<td align=left><input type=radio <?php print $audiooui ?> name="audio" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $audionon ?> name="audio" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Activer l'accès à l'agenda en direct  : </td>
<td align=left><input type=radio <?php print $agendadirectoui ?> name="agendadirect" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $agendadirectnon ?> name="agendadirect" value="non" class=btradio1  > non &nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>L</font>\'accès à l\'agenda en direct autorise aussi la création de compte lié à l\'agenda. L\'adresse d\'accès est <br /><b><?php print "${urlsite}agenda.php" ?></b> </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right >Activer l'accès agenda pour téléphone portable  : </td>
<td align=left><input type=radio <?php print $agendapdaoui ?> name="agendapda" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $agendapdanon ?> name="agendapda" value="non" class=btradio1  > non &nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>L</font>\'accès à l\'agenda via votre téléphone portable. <br> L\'adresse d\'accès est <br /><b><?php print "${urlsite}/agenda/phenix/ppx.php" ?></b> </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>


<tr><td align=right >Activer l'accès à E.D.T. en direct  : </td>
<td align=left><input type=radio <?php print $edtdirectoui ?> name="edtdirect" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $edtdirectnon ?> name="edtdirect" value="non" class=btradio1  > non &nbsp;&nbsp;<A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>L</font>\'E.D.T. est accessible en direct sans demande de connexion pour la consultation des ressources. L\'adresse d\'accès est <br /><b><?php print "${urlsite}edtressource.php" ?></b> </font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right > Activer l'accès au stockage privé  : <?php // print "(".human_readable(diskfreespace("../"))." libre)"; ?>  </td>
<td align=left><input type=radio <?php print $accesstockageoui ?> name="accesstockage" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesstockagenon ?> name="accesstockage" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right > Limit Hard (en Mo) par utilisateur : </td>
<td align=left><select name="taillestockage" > 
	<?php
	if (defined("TAILLESTOCKAGE")) {
		if (TAILLESTOCKAGE == "0") { $val2="0Mo"; }
		if (TAILLESTOCKAGE == "2000000") { $val2="2Mo"; }
		if (TAILLESTOCKAGE == "5000000") { $val2="5Mo"; }
		if (TAILLESTOCKAGE == "7000000") { $val2="7Mo"; }
		if (TAILLESTOCKAGE == "10000000") { $val2="10Mo"; }
		if (TAILLESTOCKAGE == "60000000") { $val2="60Mo"; }
		if (TAILLESTOCKAGE == "100000000") { $val2="100Mo"; }
		print "<option value='".TAILLESTOCKAGE."' STYLE='color:#000066;background-color:#FCE4BA' >".$val2."</option>";
	}		
	?>
		<option value="0" class=bouton2 >0Mo</option>
		<option value="2000000" class=bouton2 >2Mo</option>
		<option value="5000000" class=bouton2 >5Mo</option>
		<option value="7000000" class=bouton2 >7Mo</option>
		<option value="10000000" class=bouton2 >10Mo</option>
		<option value="60000000" class=bouton2 >60Mo</option>
		<option value="100000000" class=bouton2 >100Mo</option>
		</select>
</td></tr>

<tr><td align=right > Limit Soft (en nb de fichier) par utilisateur : </td>
<td align=left><select name="inodestockage" >
	<?php
	if (defined("INODESTOCKAGE")) {
		print "<option value='".INODESTOCKAGE."' STYLE='color:#000066;background-color:#FCE4BA' >".INODESTOCKAGE."</option>";
	}		
	?>
		<option value="0" class="bouton2" >0</option>
		<option value="10" class="bouton2" >10</option>
		<option value="20" class="bouton2" >20</option>
		<option value="30" class="bouton2" >30</option>
		<option value="40" class="bouton2" >40</option>
		<option value="60" class="bouton2" >60</option>
		<option value="90" class="bouton2" >90</option>
		<option value="100" class="bouton2" >100</option>
		</select>
</td></tr>


<tr><td align=right >Automatiser la création du numéro de l'étudiant  : </td>
<td align=left><input type=radio <?php print $autoINEoui ?> name="autoINE" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $autoINEnon ?> name="autoINE" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Stockage accessible aux enseignants : </td>
<td align=left><input type=radio <?php print $accesstockageprofoui ?> name="accesstockageprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesstockageprofnon ?> name="accesstockageprof" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Stockage accessible à la vie scolaire : </td>
<td align=left><input type=radio <?php print $accesstockagecpeoui ?> name="accesstockagecpe" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesstockagecpenon ?> name="accesstockagecpe" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Stockage accessible aux parents : </td>
<td align=left><input type=radio <?php print $accesstockageparentoui ?> name="accesstockageparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesstockageparentnon ?> name="accesstockageparent" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Stockage accessible aux élèves : </td>
<td align=left><input type=radio <?php print $accesstockageeleveoui ?> name="accesstockageeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $accesstockageelevenon ?> name="accesstockageeleve" value="non" class=btradio1  > non
</td>
</tr>


<tr><td align=right >Planification jours fériés : </td>
<?php
$ferie=preg_replace("/'/","",$ferie);
?>
<td align=left><input type=text  name="ferie" value="<?php print $ferie ?>" size=30>
<A href='#' onMouseOver="AffBulle2('ATTENTION AU FORMAT','../image/commun/warning.jpg','<font face=Verdana size=1><font color=red>I</font>ndiquez les dates f&eacute;ri&eacute;s sous la forme :<br>jour/mois,jour/mois,...&nbsp;&nbsp;<br> <i>exemple : 31/01,25/12, etc...</i></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td>
</tr>

<tr><td align=right >La semaine commence un dimanche : </td>
<td align=left><input type=radio <?php print $semainedimancheoui ?> name="semainedimanche" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $semainedimanchenon ?> name="semainedimanche" value="non" class=btradio1  > non  <A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>S</font>i vous avez cours le dimanche,<br /> veuillez indiquer OUI. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>

<tr><td align=right >Cours le vendredi : </td>
<td align=left><input type=radio <?php print $semainevendredioui ?> name="semainevendredi" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $semainevendredinon ?> name="semainevendredi" value="non" class=btradio1  > non  <A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>S</font>i vous avez cours le vendredi,<br /> veuillez indiquer OUI. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td></tr>
<tr><td align=right > Notation sur 40 : </td>
<td align=left>
<input type=radio id='note40a' <?php print $modulenote40oui ?> name="modulenote40" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio id='note40b' <?php print $modulenote40non ?> name="modulenote40" value="non" class=btradio1  > non 
</tr>

<tr><td align=right > Notation sur 30 : </td>
<td align=left>
<input type=radio id='note30a' <?php print $modulenote30oui ?> name="modulenote30" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio id='note30b' <?php print $modulenote30non ?> name="modulenote30" value="non" class=btradio1  > non 
</tr>


<tr><td align=right > Notation sur 20 : </td>
<td align=left>
<input type=radio id='note20a' <?php print $modulenote20oui ?> name="modulenote20" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio id='note20b' <?php print $modulenote20non ?> name="modulenote20" value="non" class=btradio1  > non 
</tr>

<tr><td align=right > Notation sur 15 : </td>
<td align=left>
<input type=radio id='note15a' <?php print $modulenote15oui ?> name="modulenote15" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio id='note15b' <?php print $modulenote15non ?> name="modulenote15" value="non" class=btradio1  > non 
</tr>

<tr><td align=right > Notation sur 10 : </td>
<td align=left>
<input type=radio id='note10a' <?php print $modulenote10oui?> name="modulenote10" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio id='note10b' <?php print $modulenote10non ?> name="modulenote10" value="non" class=btradio1  > non 
</tr>


<tr><td align=right > Notation sur 5 : </td>
<td align=left>
<input type=radio id='note5a' <?php print $modulenote5oui ?> name="modulenote5" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio id='note5b' <?php print $modulenote5non ?> name="modulenote5" value="non" class=btradio1  > non 
</tr>

<tr><td align=right > Notation sur 6 (spécif) : </td>
<td align=left>
<input type=radio <?php print $modulenote6oui ?> name="modulenote6" value="oui" class=btradio1  onclick="notationsursix('oui')" > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $modulenote6non ?> name="modulenote6" value="non" class=btradio1  onclick="notationsursix('non')" > non 
</tr>

<script>
function notationsursix(val) {
	if (val == "oui") {
		document.getElementById('note5a').checked=false;	
		document.getElementById('note5b').checked=true;
		document.getElementById('note5a').disabled=true;
		document.getElementById('note5b').disabled=true;
		document.getElementById('note10a').checked=false;	
		document.getElementById('note10b').checked=true;
		document.getElementById('note10a').disabled=true;
		document.getElementById('note10b').disabled=true;
		document.getElementById('note15a').checked=false;	
		document.getElementById('note15b').checked=true;
		document.getElementById('note15a').disabled=true;
		document.getElementById('note15b').disabled=true;
		document.getElementById('note20a').checked=false;	
		document.getElementById('note20b').checked=true;
		document.getElementById('note20a').disabled=true;
		document.getElementById('note20b').disabled=true;
		document.getElementById('note30a').checked=false;	
		document.getElementById('note30b').checked=true;
		document.getElementById('note30a').disabled=true;
		document.getElementById('note30b').disabled=true;
		document.getElementById('note40a').checked=false;	
		document.getElementById('note40b').checked=true;
		document.getElementById('note40a').disabled=true;
		document.getElementById('note40b').disabled=true;
	}else{
		document.getElementById('note5a').disabled=false;
		document.getElementById('note5b').disabled=false;
		document.getElementById('note10a').disabled=false;
		document.getElementById('note10b').disabled=false;
		document.getElementById('note15a').disabled=false;
		document.getElementById('note15b').disabled=false;
		document.getElementById('note20a').disabled=false;
		document.getElementById('note20b').disabled=false;
		document.getElementById('note30a').disabled=false;
		document.getElementById('note30b').disabled=false;
		document.getElementById('note40a').disabled=false;
		document.getElementById('note40b').disabled=false;
	}
}


<?php
if ($modulenote6oui != "") {
	print  "document.getElementById('note5a').disabled=true;
                document.getElementById('note5b').disabled=true;
                document.getElementById('note10a').disabled=true;
                document.getElementById('note10b').disabled=true;
                document.getElementById('note15a').disabled=true;
                document.getElementById('note15b').disabled=true;
                document.getElementById('note20a').disabled=true;
                document.getElementById('note20b').disabled=true;
                document.getElementById('note30a').disabled=true;
                document.getElementById('note30b').disabled=true;
                document.getElementById('note40a').disabled=true;
                document.getElementById('note40b').disabled=true;
	";
}
?>
</script>


<tr><td align=right >Gestion calendrier (Férié le samedi matin) : </td>
<td align=left><input type=radio <?php print $calsamedimatinoui ?> name="calsamedimatin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $calsamedimatinnon ?> name="calsamedimatin" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Gestion calendrier (Férié le samedi apres-midi) : </td>
<td align=left><input type=radio <?php print $calsamediapoui ?> name="calsamediap" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $calsamediapnon ?> name="calsamediap" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Gestion calendrier (Férié le mercredi matin) : </td>
<td align=left><input type=radio <?php print $calmercredimatinoui ?> name="calmercredimatin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $calmercredimatinnon ?> name="calmercredimatin" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Gestion calendrier (Férié le mercredi apres-midi) : </td>
<td align=left><input type=radio <?php print $calmercrediapoui ?> name="calmercrediap" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $calmercrediapnon ?> name="calmercrediap" value="non" class=btradio1  > non
</td></tr>

<tr><td align=right >Adresse de votre Forum interne : </td>
<td align=left><input type=text  name="forum" value="<?php print $forum ?>" size=30>
<A href='#' onMouseOver="AffBulle2('Si besoin','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>I</font>ndiquez votre adresse  <br> exemple : http://www.domaine.fr/forumBB/ </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</td>
</tr>

<tr><td align=right >Num&eacute;ro D.P.O (Data Proctection Officier) : </td><td align=left><input type=text  name="cnil" size=30 value="<?php print $cnil ?>" /></tr>
<tr><td align=right >Email du responsable D.P.O : </td><td align=left><input type=text  name="cnilprotecteur" size=30 value="<?php print $cnilprotecteur ?>" /></tr>
<tr><td align=right >Comment cr&eacute;er le registre de traitement :  </td><td> <a href='https://www.cnil.fr/fr/RGDP-le-registre-des-activites-de-traitement' target='_blank' >Consulter ce lien</a></td></tr>

<?php
include_once("../librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	$disabledupload="";
}else{
	$disabledupload="disabled='disabled'";
}
?>
<tr><td align=right >Taille autorisé pour les téléchargements  : </td>
<td align=left>
<input type=radio <?php print $tailleupload2 ?> name="tailleupload" value="non" class=btradio1  <?php print $disabledupload ?> > 2Mo &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $tailleupload8 ?> name="tailleupload" value="oui" class=btradio1 <?php print $disabledupload ?> > 8Mo
</tr>



<tr><td align=right >Prise en charge de la civilité militaire  : </td>
<td align=left>
<input type='radio' <?php print $civarmeeoui ?> name="civarmee" value="oui" class='btradio1' > oui &nbsp;&nbsp;&nbsp;
<input type='radio' <?php print $civarmeenon ?> name="civarmee" value="non" class='btradio1' > non <A href='#' onMouseOver="AffBulle2('Listing','../image/commun/info.jpg','<font face=Verdana size=1>Général, Colonel, Lieutenant-colonel, Commandant, etc...  </font>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</tr>



<tr><td align=right >Intitulé du compte direction/administration  : </td>
<td align=left>
<select name='intitule_direction' ><option value='direction' id=select1 <?php print $intitule_direction1 ?> >direction</option><option value='administration' id=select1  <?php print $intitule_direction2 ?>  >administration</option>
<option value='directeur' id=select1  <?php print $intitule_direction3 ?>  >directeur</option>
</select>
</tr>


<tr><td align=right >Intitulé du compte élève/étudiant  : </td>
<td align=left>
<select name='intitule_eleve' ><option value='élève' id=select1 <?php print $intitule_eleve1 ?> >élève</option><option value='étudiant' id=select1  <?php print $intitule_eleve2 ?>  >étudiant</option>
<option value='apprenant' id=select1  <?php print $intitule_eleve3 ?>  >apprenant</option>
</select>
</tr>


<tr><td align=right >Intitulé du compte enseignant : </td>
<td align=left>
<select name='intitule_enseignant' >
<option value='enseignant' id=select1  <?php print $intitule_enseignant1 ?>  >enseignant</option>
<option value='formateur' id=select1  <?php print $intitule_enseignant2 ?>  >formateur</option>
</select>
</tr>


<tr><td align=right >Intitulé de la rubrique classe : </td>
<td align=left>
<select name='intitule_classe' >
<option value='classe' id=select1  <?php print $intitule_classe1 ?>  >classe</option>
<option value='section' id=select1  <?php print $intitule_classe2 ?>  >section</option>
</select>
</tr>



<tr><td align=right >Configuration des bulletins  : </td>
<td align=left>
<input type='button' value='Configurer' onclick="open('configbulletin.php','','width=700,height=500')" class='bouton2' />
</tr>

<tr><td align=right >Autorise la direction à saisir le cahier de texte  : </td>
<td align=left>
<input type=radio <?php print $dircahiertexteoui ?> name="dircahiertexte" value="oui" class=btradio1   > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $dircahiertextenon ?> name="dircahiertexte" value="non" class=btradio1   > non
</tr>




<tr><td align=right >Date du jour automatique pour la saisie des absences et retards  : </td>
<td align=left>
<input type=radio <?php print $autodateabsrtdoui ?> name="autodateabsrtd" value="oui" class=btradio1   > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $autodateabsrtdnon ?> name="autodateabsrtd" value="non" class=btradio1   > non
</tr>




<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" ><font class="T2"><b>Module Spécifique</b></font></td></tr>




<tr><td align=right >Note Vie Scolaire (comportement social)  : </td>
<td align=left>
<input type=radio <?php print $cmpsocialoui ?> name="cmpsocial" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $cmpsocialnon ?> name="cmpsocial" value="non" class=btradio1  > non
</tr>



<tr><td align=right >Note max Vie Scolaire : </td>
<td align=left><select name="maxnoteviescolaire" >
	<?php
	if (defined("MAXNOTEVIESCOLAIRE")) {
		print "<option value='".MAXNOTEVIESCOLAIRE."' STYLE='color:#000066;background-color:#FCE4BA' >".MAXNOTEVIESCOLAIRE."</option>";
	}		
	?>
		<option value="20" class="bouton2" >20</option>
		<option value="50" class="bouton2" >50</option>
		</select>
</td></tr>


<tr><td align=right >Commentaire bulletin spécifique (examen) : </td>
<td align=left>
<input type=radio <?php print $combulltypeoui ?> name="combulltype" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $combulltypenon ?> name="combulltype" value="non" class=btradio1  > non <A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>P</font>rise en charge par les bulletins Séminaires. </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</tr>

<?php
$intramessengeroui="";
$intramessengernon="";
$intramessengerpersnon="";
$intramessengerelevenon="";
$intramessengerpersoui="";
$intramessengereleveoui="";
if (file_exists("../common/config-messenger.php")) {
	$intramessengeroui="checked='checked'";
	include_once("../common/config-messenger.php");
	if (MESSENGERPERS == "oui") { $intramessengerpersoui="checked='checked'"; }
	if (MESSENGERPERS == "non") { $intramessengerpersnon="checked='checked'"; }
	if (MESSENGERELEV == "oui") { $intramessengereleveoui="checked='checked'"; }
	if (MESSENGERELEV == "non") { $intramessengerelevenon="checked='checked'"; }
}else{
	$intramessengernon="checked='checked'";
	$intramessengerpersnon="checked='checked'";
	$intramessengerelevenon="checked='checked'";
}
?>
<tr><td align=right >Intra-Messenger : </td>
<td align=left>
<input type=radio <?php print $intramessengeroui ?> name="intramessenger" value="oui" class=btradio1  > activé &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $intramessengernon ?> name="intramessenger" value="non" class=btradio1  > désactivé <A href='#' onMouseOver="AffBulle2('Information','../image/commun/info.jpg','<font face=Verdana size=1><font color=red>I</font>ntra-Messenger est un MSN local, utilisable seulement par les utilisateurs TRIADE.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='../image/help.gif' align=center border=0></A>
</tr>

<tr><td align=right >Intra-Messenger pour le personnel : </td>
<td align=left>
<input type=radio <?php print $intramessengerpersoui ?> name="intramessengerpers" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $intramessengerpersnon ?> name="intramessengerpers" value="non" class=btradio1  > non 
</tr>

<tr><td align=right >Intra-Messenger pour les élèves : </td>
<td align=left>
<input type=radio <?php print $intramessengereleveoui ?> name="intramessengereleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $intramessengerelevenon ?> name="intramessengereleve" value="non" class=btradio1  > non 
</tr>


<tr><td align='right' >Emargement type : </td>
<td align=left><select name="emargement" >
	<?php
	if (defined("EMARGEMENT")) {
		print "<option value='".EMARGEMENT."' id='select0' >".EMARGEMENT."</option>";
	}		
	?>
		<option value="STANDARD" id='select1' >STANDARD</option>
		<option value="ISMAP"  id='select1' >ISMAP</option>
		</select>
</td></tr>


<tr><td align=right >Module Financier (Vatel) : </td>
<td align=left>
<input type=radio <?php print $modulefinanciervateloui ?> name="modulefinanciervatel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $modulefinanciervatelnon ?> name="modulefinanciervatel" value="non" class=btradio1  > non 
</tr>

<tr><td align=right >Affichage Vatel : </td>
<td align=left>
<input type=radio <?php print $affichageVateloui ?> name="affichageVatel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $affichageVatelnon ?> name="affichageVatel" value="non" class=btradio1  > non 
</tr>

<tr><td align=right >Module eLearning : </td>
<td align=left>
<!-- <input type=radio <?php print $moduleeLearningdoekeos ?> name="moduleelearning" value="dokeos" class=btradio1  > Dokeos &nbsp;&nbsp;&nbsp; -->
<input type=radio <?php print $moduleeLearningmoodle ?> name="moduleelearning" value="moodle" class=btradio1 selected='selected' > Moodle 
</tr>

<tr><td align=right >Autoriser TRIADE-COPILOT : </td>
<td align=left>
<input type=radio <?php print $affichageIAOui ?> name="affichageia" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $affichageIANon ?> name="affichageia" value="non" class=btradio1  > non 
</tr>

<tr><td align=right >Autoriser TRIADE-SIGN : </td>
<td align=left>
<input type=radio <?php print $affichageSIGNOui ?> name="affichagesign" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
<input type=radio <?php print $affichageSIGNNon ?> name="affichagesign" value="non" class=btradio1  > non 
</tr>

<!-------------------------------- -- -->
<tr><td colspan=2><br>
<br><br><br>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("Valeur par défaut","configuration-default.php","_parent","","") //text,nomInput</script>
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
