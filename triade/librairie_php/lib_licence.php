<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.org
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
//------------------------------------------------------------------------------
// section pour un acces non autorise

include_once("./librairie_php/lib_emul_register.php");

if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {
    print "<script type=\"text/javascript\">";
    print "location.href='./acces_refuse.php'";
    print "</script>";
    exit;
}

if (file_exists("./common/lib_triade_interne.php")) {
        if (file_exists("../../../common/config-all-site.php")) {
                include_once("../../../common/config-all-site.php");
        }
}


if (file_exists("./common/version.php")) include_once("./common/version.php");
if (file_exists("./common/lib_admin.php")) include_once("./common/lib_admin.php");
if (file_exists("./common/lib_crypt.php")) include_once("./common/lib_crypt.php");
if (file_exists("./common/lib_ecole.php")) include_once("./common/lib_ecole.php");
if (file_exists("./common/config2.inc.php")) include_once("./common/config2.inc.php");
if (file_exists("./common/config3.inc.php")) include_once("./common/config3.inc.php");
if (file_exists("./common/config4.inc.php")) include_once("./common/config4.inc.php");
if (file_exists("./common/config8.inc.php")) include_once("./common/config8.inc.php");
if (file_exists("./common/config-md5.php")) include_once("./common/config-md5.php");
if (file_exists("./librairie_php/timezone.php")) include_once("./librairie_php/timezone.php");

if (file_exists("./librairie_php/lib_error.php")) include_once("./librairie_php/lib_error.php");
if (file_exists("./librairie_php/licence_triade.php")) include_once("./librairie_php/licence_triade.php");
if (file_exists("./common/productId.php")) include_once("./common/productId.php");
if (file_exists("./librairie_ph/lib_context.php")) include_once("./librairie_php/lib_context.php");
if (file_exists("./common/config-module.php")) include_once("./common/config-module.php");
if (file_exists("./common/config-fen.php")) include_once("./common/config-fen.php");

// -----------------------------------------------------------------------------

if (!defined('INTITULEDIRECTION')) { define("INTITULEDIRECTION","direction"); }
if (!defined('INTITULEELEVE')) { define("INTITULEELEVE","élève"); }
if (!defined('INTITULECLASSE')) { define("INTITULECLASSE","classe"); }
if (!defined('INTITULEENSEIGNANT')) { define("INTITULEENSEIGNANT","enseignant"); }
if (!defined('LARGEURFEN')) { define("LARGEURFEN","780"); }
// -----------------------------------------------------------------------------
// syxtaxe d'utilisation
// verifplus("menuadmin",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menuparent",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menuprof",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menuscolaire",$_SESSION[id_pers],$_SESSION[membre]);
// verifplus("menudeux",$_SESSION[id_pers],$_SESSION[membre]);
// scolaire et admin
function verifplus($verifplus,$idpers,$idmembre) {
	if ($verifplus == "menudeux") {
		if (($idmembre == "menuadmin") || ($idmembre == "menuscolaire")) {
			$blackliste=0;
		}else{
			$blackliste=1;
		}
	}else {
		if ($idmembre != $verifplus) {
			$blackliste=1;
		}else{
			$blackliste=0;
		}
	}

	if ($blackliste == 1) {
    		print "<script type=\"text/javascript\">";
	    	print "location.href=\"./blacklist.php\"";
 		print "</script>";
		exit;
	}
}


// -----------------------------------------------------------------------------

function testadminplus() {
	if (empty($_SESSION["adminplus"])) {
	    print "<script type=\"text/javascript\">";
	    print "location.href=\"./affectation_creation_key.php\"";
	    print "</script>";
	    exit;
	}
}


//  brmozilla($_SESSION[navigateur]);
function brmozilla($navig) {
	if ($navig != "Internet Explorer") {
		print "<br />";
	}
}

if (file_exists("./common/lib_patch.php")){
	include_once('./common/lib_patch.php');
	$rev="<br>Rev : <em>".VERSIONPATCH."</em>  - <i>".VERSIONMD5."</i>";
}

//------------------------------------------------------------------------------
// construction de la license
// pour Internet explorer

if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT']))
{
print "<div id=\"menu\" class=\"fond\" style=\"background-image:url(./image/commun/fond_inscrip.jpg);position:absolute;z-index:2;\">";
print "<div class=\"intitules\" url=\"\" align=\"left\">";
print "<br /><img src=\"./image/commun/logo_triade_licence.gif\" />";
print "        <br /><br />Version : <strong>".VERSION."</strong>";
print "	       $rev";
print "        <br /> Tous droits réservés <br />";
print "                Licence d'utilisation : ".LICENCE."<br />";
print "                Product&nbsp;ID&nbsp;=&nbsp;<font class='T1'>".PRODUCTID."</font>";
print "        <br />";
print "        <textarea cols=55 rows=5 style='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>";
droit();
print "</textarea>";
print "        <hr /><table width=95%><tr><td align=left> <font size=2 ><a href='http://www.triade-educ.org' target='_blank' >Triade©</a>, ".DATEOUT." </font></td><td align=right><input type=button value='Fermer Fenêtre' onclick='masque_menu()' class='bouton2' ></td></tr></table>";
print "<br /></div></div>";
print "<script type=\"text/javascript\">";
print "document.getElementById('menu').style.visibility='hidden'";
print "</script>";
}
 

//------------------------------------------------------------------------------
// declaration de variables
include_once("./common/config.inc.php");
//------------------------------------------------------------------------------
if  (preg_match("/http/",FORUM)) {
	print "<script type=\"text/javascript\"> var forum='".FORUM."'; forumtarget='_blank'; </script>";
}else{
	print "<script type=\"text/javascript\"> var forum='forum.php'; forumtarget='forum'; </script>";
}



//------------------------------------------------------------------------------
// interdit l'utilisation de la touche F11 et F5
/*
print <<<EOF
<script type="text/javascript">
function frapper(code) {
//	 alert("vous avez frapper la touche:"+code);
	if (code == 122) {
//	 location.href="error_f12.php";
	}
}

if (navigator.appName=="Microsoft Internet Explorer") {
 function toucheA() {frapper(event.keyCode)};
 document.onkeydown = toucheA;
}
else {
 function toucheB(evnt) {frapper(evnt.keyCode)};
 document.onkeydown = toucheB;
}
</script>
EOF;
 */
//----------------------------------------------------------------------------
function droit() {
	print DROITRIADE;
}

function TextNoAccentLicence($str){
	$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
	$str = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $str);
	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
	$str = preg_replace('#&[^;]+;#', '', $str);
	return($str);
}

include_once("mactu.php");

// AfficheAttente();
function attente() {
	print "<div id='attenteDiv' style='position:absolute;top:0;left:0;visibility:hidden'>";
	print "<table border=1 width=250 bordercolor='#000000' height=60 id='bodyfond2' >";
	print "<tr><td align=center id='bordure'><br><font class=T2>".LANGattente2."</center><center><br>";
	print "<table border='0'><tr><td><img src='./image/commun/indicator.gif' align='center' id='imgattente' name='imgattente' >";
	print "</td></tr></table><br><font class=T2>".LANGattente3."</font></center><br></td></tr></table></div>";
	print "<script language='JavaScript'>";
	print "var top1=(screen.height-60)/2;";
	print "var left1=(screen.width-250)/2;";
	print "document.getElementById('attenteDiv').style.top=top1;";
	print "document.getElementById('attenteDiv').style.left=left1;";
	print "document.getElementById('imgattente').src='./image/commun/indicator.gif'";
	print "</script>";

}
//----------------------------------------------------------------
// Config droit d'acces module
print "<script>";
print "var lan='".LAN."';";
print "var moduledispence='".DISPENSE."'; ";
print "var moduledst='".DST."'; ";
print "var moduleplandeclasse='".PLANCLASSE."'; ";
print "var modulecahierdetexte='".CAHIERDETEXTE."'; ";
print "var modulediscipline='".DISCIPLINE."'; ";
print "var modulevisudevoirprof='".VISUDEVOIRPROF."'; ";
print "var modulesuppdevoirprof='".SUPPDEVOIRPROF."'; ";
print "var modulecahiertextprof='".CAHIERTEXTPROF."'; ";
print "var modulesanctionprof='".SANCTIONPROF."'; ";
print "var moduleficheeleveprof='".FICHEELEVEPROF."'; ";
print "var modulelisteleveprof='".LISTEELEVEPROF."'; ";
print "var moduleplanprof='".PLANPROF."'; ";
print "var modulestageproprof='".STAGEPROPROF."'; ";
print "var modulesdstprofacces='".DSTPROFACCES."'; ";
print "var moduledokeoseleve='".DOKEOSELEVE."'; ";
print "var moduledokeosprof='".DOKEOSPROF."'; ";
print "var moduleparentdispence='".PARENTDISPENSE."'; ";
print "var moduleparentdst='".PARENTDST."'; ";
print "var moduleparentplandeclasse='".PARENTPLANCLASSE."'; ";
print "var moduleparentcahierdetexte='".PARENTCAHIERDETEXTE."'; ";
print "var moduleparentdiscipline='".PARENTDISCIPLINE."'; ";
print "var modulecomptaprof='".COMPTAPROF."'; ";
print "var moduleparenttrombinoscope='".PARENTTROMBINOSCOPE."'; ";
print "var moduleparentstage='".STAGEPROPARENT."'; ";
print "var moduleelevestage='".STAGEPROELEVE."'; ";
print "var modulestockageprof='".STOCKAGEPROF."'; ";
print "var moduleintramsnprof='".INTRAMSNPROF."'; ";
print "var moduleagendaprof='".AGENDAMSNPROF."'; ";
print "var modulefluxrssprof='".FLUXRSSPROF."'; ";
print "var modulenotesprof='".NOTESPROF."'; ";
print "var modulebulletinprof='".BULLETINPROF."'; ";
print "var moduleresaprof='".RESAPROF."'; ";
print "var modulecirculaireprof='".CIRCULAIREPROF."'; ";
print "var moduleinformationprof='".INFORMATIONPROF."'; ";
print "var modulecalendrierprof='".CALENDRIERPROF."'; ";
print "var modulestockageviescolaire='".STOCKAGEVIESCOLAIRE."'; ";
print "var moduleintramsnviescolaire='".INTRAMSNVIESCOLAIRE."'; ";
print "var moduleagendaviescolaire='".AGENDAVIESCOLAIRE."'; ";
print "var modulefluxrssviescolaire='".FLUXRSSVIESCOLAIRE."'; ";
print "var moduleetudeviescolaire='".ETUDEVIESCOLAIRE."'; ";
print "var modulecirculaireviescolaire='".CIRCULAIREVIESCOLAIRE."'; ";
print "var moduledstviescolaire='".DSTVIESCOLAIRE."'; ";
print "var modulevisaviescolaire='".VISAVIESCOLAIRE."'; ";
print "var modulenoteviescolaire='".NOTEVIESCOLAIRE."'; ";
print "var moduleimptableauviescolaire='".IMPTABLEAUVIESCOLAIRE."'; ";
print "var modulebulletinviescolaire='".BULLETINVIESCOLAIRE."'; ";
print "var moduleperiodeviescolaire='".PERIODEVIESCOLAIRE."'; ";
print "var modulevideoprojoviescolaire='".VIDEOPROJOVIESCOLAIRE."'; ";
print "var moduleplanclasseviescolaire='".PLANCLASSEVIESCOLAIRE."'; ";
print "var modulehistoryviescolaire='".HISTORYVIESCOLAIRE."'; ";
print "var moduleresaviescolaire='".RESAVIESCOLAIRE."'; ";
print "var moduleexportviescolaire='".EXPORTVIESCOLAIRE."'; ";
print "var modulestageviescolaire='".STAGEVIESCOLAIRE."'; ";
print "var modulevacationviescolaire='".VACATIONVIESCOLAIRE."'; ";
print "var noteenseignantviascolaire='".NOTEENSEIGNANTVIASCOLAIRE."'; ";
print "var modulecantineprof='".MODULECANTINEPROF."'; ";
print "var modulecantineviescolaire='".MODULECANTINEVIESCOLAIRE."'; ";
print "var modulestockageadmin='".STOCKAGEADMIN."'; ";
print "var moduleintramsnadmin='".INTRAMSNADMIN."'; ";
print "var moduleagendaadmin='".AGENDAADMIN."'; ";
print "var modulefluxrssadmin='".FLUXRSSADMIN."'; ";
print "var moduleresaadmin='".RESAADMIN."'; ";
print "var modulevacationadmin='".VACATIONADMIN."'; ";
print "var modulehistoryadmin='".HISTORYADMIN."'; ";
print "var modulecantineadmin='".MODULECANTINEADMIN."'; ";
print "var moduledroitscolariteadmin='".DROITSCOLARITEADMIN."'; ";
print "var moduleprofviaadmin='".NOTEPROFVIAADMIN."'; ";
print "var moduleparentabsence='".PARENTABSENCE."'; ";
print "var moduleparentretard='".PARENTRETARD."'; ";
print "var modulefinanciervateladmin='".MODULEFINANCIERADMIN."'; ";
print "var modulechambrevateladmin='".MODULECHAMBRESADMIN."'; ";
print "var moduletuteurnote='".MODULETUTEURNOTE."'; ";
print "var moduletuteurdiscipline='".MODULETUTEURDISCIPLINE."'; ";
print "var moduletuteurabs='".MODULETUTEURABS."'; ";
print "var moduletuteurdispence='".MODULETUTEURDISPENSE."'; ";
print "var moduletuteuredt='".MODULETUTEUREDT."'; ";
print "var moduletuteurcahierdetexte='".MODULETUTEURCAHIERDETEXTE."'; ";
print "var moduletuteurcirculaire='".MODULETUTEURCIRCULAIRE."'; ";
print "var moduletuteurcalendrier='".MODULETUTEURCALENDRIER."'; ";
print "var moduleprofemargement='".MODULEPROFEMARGEMENT."'; ";

print "var moduleparentagenda='".MODULEPARENTAGENDA."'; ";
print "var moduleparentstockage='".MODULEPARENTSTOCKAGE."'; ";
print "var moduleparentmsn='".MODULEPARENTMSN."'; ";
print "var moduleparentcompta='".MODULEPARENTCOMPTA."'; ";
print "var moduleparentrss='".MODULEPARENTRSS."'; ";
print "var moduleparentcantine='".MODULEPARENTCANTINE."'; ";

print "var moduleeleveagenda='".MODULEELEVEAGENDA."'; ";
print "var moduleelevestockage='".MODULEELEVESTOCKAGE."'; ";
print "var moduleelevemsn='".MODULEELEVEMSN."'; ";
print "var moduleelevecompta='".MODULEELEVECOMPTA."'; ";
print "var moduleeleverss='".MODULEELEVERSS."'; ";
print "var moduleelevecantine='".MODULEELEVECANTINE."'; ";
print "var modulenewspageviescolaire='".MODULENEWSPAGEVIESCOLAIRE."'; ";
print "var modulenewsviescolaire='".MODULENEWSVIESCOLAIRE."'; ";
print "var moduleboursieradmin='".MODULEBOURSIERADMIN."'; ";

print "var modulemessagerieadmin='".MODULEMESSAGERIEADMIN."'; ";
print "var modulemessagerieprof='".MODULEMESSAGERIEPROF."'; ";
print "var modulemessagerieeleve='".MODULEMESSAGERIEELEVE."'; ";
print "var modulemessagerieparent='".MODULEMESSAGERIEPARENT."'; ";
print "var modulemessagerietuteur='".MODULEMESSAGERIETUTEUR."'; ";
print "var modulemessagerieviescolaire='".MODULEMESSAGERIESCOLAIRE."'; ";
print "var modulepreinscriptionviescolaire='".MODULEPREINSCRIPTIONVIESCOLAIRE."'; ";
print "var moduleelearning='".MODULEELEARNING."'; ";
print "var INTITULEDIRECTION='".ucfirst(INTITULEDIRECTION)."'; ";
print "var intituledirection='".INTITULEDIRECTION."'; ";
print "var INTITULEELEVE='".ucfirst(TextNoAccentLicence(INTITULEELEVE))."'; ";
print "var intituleeleve='".INTITULEELEVE."'; ";
print "var intituleclasse='".INTITULECLASSE."'; ";
print "var intituleenseignant='".INTITULEENSEIGNANT."'; ";

print "var moduleplanningeleve='".MODULEPLANNINGELEVE."'; ";
print "var moduleplanningparent='".MODULEPLANNINGPARENT."'; ";
print "var moduleplanningprof='".MODULEPLANNINGPROF."'; ";

print "var rubriquebulletin='".RUBRIQUEBULLETIN."'; ";
print "var rubriqueannexe='".RUBRIQUEANNEXE."'; ";
print "var rubriquegestion='".RUBRIQUEGESTION."'; ";
print "var rubriqueaffectation='".RUBRIQUEAFFECTATION."'; ";
print "var rubriqueetablissement='".RUBRIQUEETABLISSEMENT."'; ";
print "var rubriqueviescolaire='".RUBRIQUEVIESCOLAIRE."'; ";
print "var rubriqueetudiant='".RUBRIQUEETUDIANT."'; ";
print "var rubriqueactualite='".RUBRIQUEACTUALITE."'; ";

print "var moduleadmincdi='".MODULEADMINCDI."'; ";
print "var moduleadminnotanet='".MODULEADMINNOTANET."'; ";
print "var moduleadmingestionsms='".MODULEADMINGESTIONSMS."'; ";
print "var moduleadminfourniture='".MODULEADMINFOURNITURE."'; ";
print "var moduleadminexambrevet='".MODULEADMINEXAMBREVET."'; ";
print "var moduleadmingestionetude='".MODULEADMINGESTIONETUDE."'; ";
print "var moduleadmingestiondiscipline='".MODULEADMINGESTIONDISCIPLINE."'; ";
print "var moduleadminretenudj='".MODULEADMINRETENUDJ."'; ";
print "var moduleadminsanctiondujour='".MODULEADMINSANCTIONDUJOUR."'; ";
print "var moduleadmingestiondispense='".MODULEADMINGESTIONDISPENSE."'; ";
print "var moduleadmindosmedical='".MODULEADMINDOSMEDICAL."'; ";
print "var moduleadminplanclasse='".MODULEADMINPLANCLASSE."'; ";
print "var moduleadmingestiondelegue='".MODULEADMINGESTIONDELEGUE."'; ";
print "var moduleadminsousmatiere='".MODULEADMINSOUSMATIERE."'; ";
print "var moduleadminsuppleant='".MODULEADMINSUPPLEANT."'; ";

print "var moduleadminprofp='".MODULEADMINPROFP."'; ";
print "var moduleadminconfignoteusa='".MODULEADMINCONFIGNOTEUSA."'; ";
print "var moduleadminentretienindividuel='".MODULEADMINENTRETIENINDIVIDUEL."'; ";
print "var moduleadmincarnetsuivi='".MODULEADMINCARNETSUIVI."'; ";
print "var moduleadminverifbulletin='".MODULEADMINVERIFBULLETIN."'; ";
print "var moduleadminnoteviescolaire='".MODULEADMINNOTEVIESCOLAIRE."'; ";
print "var moduleadminimprperiode='".MODULEADMINIMPRPERIODE."'; ";
print "var moduleadminabsrtd='".MODULEADMINABSRTD."'; ";
print "var moduleadminpreinscription='".MODULEADMINPREINSCRIPTION."'; ";

print "var moduleadminnouvelleannee='".MODULEADMINNOUVELLEANNEE."'; ";
print "var moduleadminarchivage='".MODULEADMINARCHIVAGE."'; ";
print "var moduleadminnewsdefilant='".MODULEADMINNEWSDEFILANT."'; ";
print "var moduleadminpurgerinfo='".MODULEADMINPURGERINFO."'; ";
print "var moduleelevecdi='".MODULEELEVECDI."'; ";
print "var GRAPH='".GRAPH."'; ";
print "var VATEL='".VATEL."'; ";
print "var moduleadmingestionsavoiretre='".MODULEADMINGESTIONSAVOIRETRE."'; ";
print "var moduleprofgestionsavoiretre='".MODULEPROFGESTIONSAVOIRETRE."'; ";
print "var moduleelevegestionsavoiretre='".MODULEELEVEGESTIONSAVOIRETRE."'; ";
print "var moduleparentgestionsavoiretre='".MODULEPARENTGESTIONSAVOIRETRE."'; ";
print "var modulebulletinvisuparent='".MODULEBULLETINVISUPARENT."'; ";
print "var modulebulletinvisueleve='".MODULEBULLETINVISUELEVE."'; ";
print "var modulebulletinvisututeur='".MODULEBULLETINVISUTUTEUR."'; ";
print "var moduleviescolairegestionsavoiretre='".MODULEVIESCOLAIREGESTIONSAVOIRETRE."'; ";
print "var moduletuteurgestionsavoiretre='".MODULETUTEURGESTIONSAVOIRETRE."'; ";
print "var moduleviescolairecahierdetexte='".VIESCOLAIRECAHIERDETEXTE."'; ";
print "var moduleviescolairechambre='".MODULECHAMBRESVIESCOLAIRE."'; ";
print "var moduleradio='".MODULERADIO."'; ";
print "var modulefourniturescolaire='".MODULEFOURNITURESCOLAIRE."';";
print "var moduledelegueparent='".MODULEDELEGUEPARENT."';";
print "var moduleadminevalens='".MODULEADMINEVALENS."';";
print "var largeurfen='".LARGEURFEN."';";
print "if (screen.width >= 800) { largeurfen='780'; }";
print "if (screen.width >= 1024) { largeurfen='1020'; }";
//print "if (screen.width >= 1920) { largeurfen='1500'; }";
//print "alert(largeurfen);";
//print "alert(screen.width);";




if (defined('FOOTERSPECIAL')) { 
	print "var footer=\"".FOOTERSPECIAL."\";";
	print "var footerlien=\"".FOOTERLIEN."\";";
	$footer=FOOTERSPECIAL;
	$lienfooter=FOOTERLIEN;
}else{
	print "var footer='';";
	print "var footerlien='';";
	$footer="";$lienfooter="";
}
if (defined('DOKEOSV2')) {
	print "var dokeosv2=\"".DOKEOSV2."\"; ";
}else{
	print "var dokeosv2='0'; ";
}

if (defined('WEBRAD')) {
	print "var webrad=\"".WEBRAD."\"; ";
}else{
	print "var webrad='oui'; ";
}

if (defined('LIENASSIST')) {
	print "var lienassist=\"".LIENASSIST."\"; ";
}else{
	print "var lienassist='0'; ";
}


print "\n";


print "if (moduleadmincdi == 'MODULEADMINCDI') { moduleadmincdi='oui'; } \n";
print "if (moduleadminnotanet == 'MODULEADMINNOTANET') { moduleadminnotanet='oui'; } \n";
print "if (moduleadmingestionsms == 'MODULEADMINGESTIONSMS') { moduleadmingestionsms='oui'; } \n";
print "if (moduleadminfourniture == 'MODULEADMINFOURNITURE') { moduleadminfourniture='oui'; } \n";
print "if (moduleadminexambrevet == 'MODULEADMINEXAMBREVET') { moduleadminexambrevet='oui'; } \n";
print "if (moduleadmingestionetude == 'MODULEADMINGESTIONETUDE') { moduleadmingestionetude='oui'; } \n";
print "if (moduleadmingestiondiscipline == 'MODULEADMINGESTIONDISCIPLINE') { moduleadmingestiondiscipline='oui'; } \n";
print "if (moduleadminretenudj == 'MODULEADMINRETENUDJ') { moduleadminretenudj='oui'; } \n";
print "if (moduleadminsanctiondujour == 'MODULEADMINSANCTIONDUJOUR') { moduleadminsanctiondujour='oui'; } \n";
print "if (moduleadmingestiondispense == 'MODULEADMINGESTIONDISPENSE') { moduleadmingestiondispense='oui'; } \n";
print "if (moduleadmindosmedical == 'MODULEADMINDOSMEDICAL') { moduleadmindosmedical='oui'; } \n";
print "if (moduleadminplanclasse == 'MODULEADMINPLANCLASSE') { moduleadminplanclasse='oui'; } \n";
print "if (moduleadmingestiondelegue == 'MODULEADMINGESTIONDELEGUE') { moduleadmingestiondelegue='oui'; } \n";
print "if (moduleadminsousmatiere == 'MODULEADMINSOUSMATIERE') { moduleadminsousmatiere='oui'; } \n";
print "if (moduleadminsuppleant == 'MODULEADMINSUPPLEANT') { moduleadminsuppleant='oui'; } \n";
print "if (modulemessagerieadmin == 'MODULEMESSAGERIEADMIN') { modulemessagerieadmin='oui'; } \n";
print "if (modulemessagerieprof == 'MODULEMESSAGERIEPROF') { modulemessagerieprof='oui'; } \n";
print "if (modulemessagerieeleve == 'MODULEMESSAGERIEELEVE') { modulemessagerieeleve='oui'; } \n";
print "if (modulemessagerieparent == 'MODULEMESSAGERIEPARENT') { modulemessagerieparent='oui'; } \n";
print "if (modulemessagerietuteur == 'MODULEMESSAGERIETUTEUR') { modulemessagerietuteur='oui'; } \n";
print "if (modulemessagerieviescolaire == 'MODULEMESSAGERIESCOLAIRE') { modulemessagerieviescolaire='oui'; } \n";
print "if (moduleelearning == 'MODULEELEARNING') { moduleelearning='dokeos'; } \n";
print "if (moduleradio == 'MODULERADIO') { moduleradio='oui'; } \n";
print "if (modulefourniturescolaire == 'MODULEFOURNITURESCOLAIRE') { modulefourniturescolaire='oui'; } \n";
print "if (moduledelegueparent == 'MODULEDELEGUEPARENT') { moduledelegueparent='oui'; } \n";

include_once('librairie_php/db_triade.php');
$cnx=cnx();
if (verifDroit($_SESSION["id_pers"],'vatelcompta')) { $ok="oui"; }else{ $ok='non'; }
print "var modulefinanciervatelpersonnel='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'vatelchambre')) { $ok="oui"; }else{ $ok='non'; }
print "var modulechambrevatelpersonnel='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'cantine')) { $ok="oui"; }else{ $ok='non'; }
print "var modulecantine='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'droitStageProRead')) { $ok="oui"; }else{ $ok='non'; }
print "var moduledroitStageProRead='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'trombinoscopeRead')) { $ok="oui"; }else{ $ok='non'; }
print "var moduletrombinoscopeRead='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'consultationRead')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleconsultationRead='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'cahiertextRead')) { $ok="oui"; }else{ $ok='non'; }
print "var modulecahiertextRead='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'resaressource')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleresaressource='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'ficheeleve')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleficheeleve='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'carnetnotes')) { $ok="oui"; }else{ $ok='non'; }
print "var modulecarnetnotes='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'cahiertextes')) { $ok="oui"; }else{ $ok='non'; }
print "var modulecahiertextes='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'imprbulletin')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleimprbulletin='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'imprtableau')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleimprtableau='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'visadirection')) { $ok="oui"; }else{ $ok='non'; }
print "var modulevisadirection='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'videoprojo')) { $ok="oui"; }else{ $ok='non'; }
print "var modulevideoprojo='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'entretien')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleentretien='$ok';\n";
if (verifDroit($_SESSION["id_pers"],'edt')) { $ok="oui"; }else{ $ok='non'; }
print "var moduleedt='$ok';\n";

print "</script>";

include_once("./librairie_php/langue.php");
?>
