<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
if (AFFICHAGEVATEL == "oui") header("Location:./vatel/");

if (defined("HTTPS")) {
        if (HTTPS == "oui") {
                if ($_SERVER['HTTPS'] != "on") {
                        $serv=$_SERVER["SERVER_NAME"];
                        header("Location: https://$serv/triade/acces_depart.php");
                }
        }
}


$ecoute=0;
if (!isset($_COOKIE["agentwebecole"])) {
        setcookie("agentwebecole","actu",time()+3600*24*2);
        $ecoute=1;
}

if (isset($_SESSION["nom"])) { header("Location: acces2.php?id"); }
if (isset($_COOKIE["langue-triade"])) {
	if ($_COOKIE["langue-triade"] == "en") {
		$choix="<option value='en' id='select0' >English</option>";
	}
	if ($_COOKIE["langue-triade"] == "fr") {
		$choix="<option value='fr' id='select0' >Fran&ccedil;ais</option>";
	}
	if ($_COOKIE["langue-triade"] == "es") {
			$choix="<option value='es'  id='select0' >Espagnol</option>";
	}
	if ($_COOKIE["langue-triade"] == "bret") {
			$choix="<option value='bret'  id='select0' >Breton</option>";
	}
	if ($_COOKIE["langue-triade"] == "arabe") {
			$choix="<option value='arabe' id='select0' >Arabe</option>";
	}
	if ($_COOKIE["langue-triade"] == "it") {
			$choix="<option value='it' id='select0' >Italien</option>";
	}
	if ($_COOKIE["langue-triade"] == "occitan") {
			$choix="<option value='occitan' id='select0' >Occitan</option>";
	}
}


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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
   <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
   <meta http-equiv="pragma" content = "no-cache">
   <meta http-equiv="Cache" content="no store" />
   <meta http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK REL="SHORTCUT ICON" href="./favicon.ico">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_type_navigateur.js"></script>
<title>Triade</title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>

<?php
include_once("./librairie_php/lib_netscape.php");
include_once("./librairie_php/lib_licence2.php");

include_once("./common/lib_ecole.php");
	if ($_COOKIE["langue-triade"] == "fr") {
        	include_once("./librairie_php/langue-text-fr.php");
	        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
        	print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
	}elseif ($_COOKIE["langue-triade"] == "en") {
        	print "<script type=text/javascript src='librairie_js/langueenmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueenfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-en.php");
	}elseif ($_COOKIE["langue-triade"] == "es") {
        	print "<script type=text/javascript src='librairie_js/langueesmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueesfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-es.php");
	}elseif ($_COOKIE["langue-triade"] == "bret") {
        	print "<script type=text/javascript src='librairie_js/languebretmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languebretfunction-depart.js'></script>\n";
		include_once("./librairie_php/langue-text-bret.php");
	}elseif ($_COOKIE["langue-triade"] == "arabe") {
        	print "<script type=text/javascript src='librairie_js/languearabemenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languearabefunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-arabe.php");
	}elseif ($_COOKIE["langue-triade"] == "it") {
        	print "<script type=text/javascript src='librairie_js/langueitmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueitfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-it.php");
	}elseif ($_COOKIE["langue-triade"] == "occitan") {
        	print "<script type=text/javascript src='librairie_js/langueocmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueocfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-oc.php");		
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}


if (preg_match('/triade-educ.com/',$_SERVER['DOCUMENT_ROOT'])) {
	if (!file_exists("./common/lib_triade_interne.php")) {
		touch("./common/lib_triade_interne.php");
	}
}

if (file_exists("./common/lib_triade_interne.php")) {
	if (file_exists("../../../common/maintenance.txt")) { 
		include_once("./librairie_php/popupmaintenance-generale.php");	
	} 
}

include_once("./librairie_php/popupmaintenance.php"); 
include_once("./common/config2.inc.php");

print "<script type='text/javascript'>var http='http://';</script>\n";

if ($ecoute == 1) {
	print "<script type='text/javascript'>var vocalmess='accueil';</script>\n";
}else{
	print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
}
print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";
$nocache=time();
?>
<script type="text/javascript" >var mailcontact="<?php if (MAILCONTACT != "") { print MAILCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact="<?php if (URLCONTACT != "") { print URLCONTACT; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact="<?php if (URLNOMCONTACT != "") { print URLNOMCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include_once("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">

<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepartconnection1.js"></SCRIPT>
<script language="JavaScript"> var contenu=""; </script>
<?php
$efface="";
$titre3=strtolower(htmlspecialchars($_GET["saisie_titre"]));
if ($titre3 == "administrateur"){ $titre3="administration"; }
if ($titre3 == "direction")	{ $titre3="administration"; }
if ($titre3 == "parents") 	{ $titre3="parents"; 	}
if ($titre3 == "eleves") 	{ $titre3="elèves"; 	}
if ($titre3 == "vie scolaire") 	{ $titre3="vie Scolaire"; }
if ($titre3 == "tuteur de stage") { $titre3="tuteur de stage"; }
if ($titre3 == "personnel") { $titre3="personnel"; }

if (isset($_GET["message"])) {
	$efface="onclick='document.getElementById(\"erreur\").style.visibility=\"hidden\"' ";

$mess=LANGacce_dep2." ".LANGacce_dep2bis;
$information=LANGacce_dep1." : ".ucfirst($titre3);
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent web ".AGENTWEBPRENOM;
	$mess="<iframe width=100 height=100 src=\'./agentweb/agentmel.php?inc=5&m=M11&mess=M11\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".LANGacce_dep2ter ;
}
?>
<script language="JavaScript">
var 	strTitre="<?php  print $information ?>";
var 	strIcone="./image/commun/stop.jpg";
var 	texte="<?php print $mess ?>";
var	contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';
	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}
	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';
	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';
</script>
<?php
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTTITRE1?> <?php print ucfirst($titre3) ?></font> &nbsp;&nbsp;&nbsp;&nbsp;</b><font id="color2"><?php print htmlspecialchars($_GET["message"]) ?></FONT></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<?php
// verification d'accès
// verification si ecriture dans un repertoire

$action="acces.php";
$verif=0;
if (is_writable("./data/install_log/install.inc")) {
	$verif=0;
}else {
	$verif=1;
	$disabled="disabled";
	$action="acces_depart.php";
	$codeErreur="<i>(Code : 0A02)</i>" ;
}

include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
adsens();

if (file_exists("./data/install_log/noaccess.inc")) {   // Interruption du service unique
	$verif=2;
	$disabled="disabled";
	$action="acces_depart.php";
}else{
	$cnx=cnx();
	if ($cnx == 0) {
		$verif=1;
		$disabled="disabled";
		$action="acces_depart.php";
		$codeErreur="<i>(Code : 0A01)</i>" ;
	}
}

if (defined("RESILIATION")) {
        $datedujour=date("Ymd");
        if ($datedujour >= RESILIATION) {
                $verif=2;
                $disabled="disabled";
                $action="acces_depart.php";
                touch("./data/install_log/noaccess.inc");
        }
}


if (file_exists("./common/lib_triade_interne.php")) {
	if (file_exists("../../../common/stop.triade")) { $verif=1; }  // Interruption du service général
}

if ($_GET["saisie_titre"] == "Elèves") {
	$titre1=LANGNA1." ".LANGDEPART1;
	$titre2=LANGNA2." ".LANGDEPART1;
}elseif($_GET["saisie_titre"] == "Parents") {
	$titre1=LANGNA1." ".LANGDEPART1;
	$titre2=LANGNA2." ".LANGDEPART1;
}else {
	$titre1=LANGNA1;
	$titre2=LANGNA2;
}
 
if ($_GET["bl"] == "1") {
	$verif2=1;
	$text=LANGTBLAKLIST0;
	$disabled="disabled";
	$action="acces_depart.php";
}

if (ini_get('register_globals')) {
//	$verif2=2;
//	$text2=LANGDEPART2;
//	$disabled="disabled";
}


if ((file_exists("./data/parametrage/noacces.ete")) && ($_GET["saisie_membre"] != "administrateur")) {
        $verif=3;
        $disabled="disabled";
        $action="acces_depart.php";
        $codeErreur="" ;
        $messageaccueil="L'ENT Triade est en vacances, l'&eacute;tablissement pr&eacute;pare la nouvelle ann&eacute;e, merci de revenir plus tard.";
}

if ((file_exists("./data/parametrage/noacces.parent")) && ($_GET["saisie_membre"] == "parent")) {
	$verif=3;
	$disabled="disabled";
	$action="acces_depart.php";
	$codeErreur="" ;
}

if ((file_exists("./data/parametrage/noacces.eleve")) && ($_GET["saisie_membre"] == "eleve")) {
	$verif=3;
	$disabled="disabled";
	$action="acces_depart.php";
	$codeErreur="" ;
}


$autocomplete="autocomplete='off'";
if (defined("AUTOCOMPLETIONLOGIN")) { if (AUTOCOMPLETIONLOGIN == "oui") { $autocomplete=""; } }

?>
<form action='<?php print $action ?>' method='POST' name='inscripform' onsubmit="return Validate()"  >
<p align="center"><font color="#000000">
<TABLE border=0 width="400">
<TR>
<TR>
<TD align=right><font class="T2"><?php print $titre1 ?> : </font></TD>
<TD ><input type=text name=saisienom size=30 <?php print $efface ?> class="idAccesNom" <?php print $autocomplete ?> onblur="this.className='idAccesNom'" onfocus="this.className='idAccesNom2'"  ></TD></TR>
<TR>
<TD align=right ><font class="T2"><?php print $titre2 ?> : </font></TD>
<TD ><input type=text name=saisieprenom size=30 <?php print $efface ?> class="idAccesNom" <?php print $autocomplete ?> onblur="this.className='idAccesNom'" onfocus="this.className='idAccesNom2'" >   </TD>
<TD align=center  valign="middle" >
<?php
if (trim($disabled) == "") {
	print "<script language=JavaScript>buttonMagicSubmitAtt(\"".LANGTCONNEXION."\",'rien',\" $disabled \");</script>";
}else{
	print "<script language=JavaScript>buttonMagicAlert(\"".LANGTCONNEXION."\",\"Connexion impossible pour le moment.\\\\n\\\\nMerci de revenir plus tard.\\\\n\\\\nEquipe Triade.\");</script>";
}
?>
</TD></TR>
<TR>
<TD align=right ><font class="T2"><?php print LANGNA3?> : </font></TD>
<TD width="2"><input type=password name=saisiepasswd size=20 <?php print $efface ?>  class="idAccesNom" onblur="this.className='idAccesNom'" onfocus="this.className='idAccesNom2'" >
<input type=hidden name='saisie_membre' value="<?php print htmlspecialchars($_GET["saisie_membre"]) ?>" >
<input type=hidden name='saisie_titre' value="<?php print htmlspecialchars($_GET["saisie_titre"]) ?>" >
<input type=hidden name='saisiewidth' id='saisiewidth' >
<script>
if (screen.width >= 800) { document.getElementById('saisiewidth').value='780'; }
if (screen.width >= 1024) { document.getElementById('saisiewidth').value='1020'; }
</script>
<input type=hidden name='info_nav' >
<script language=JavaScript>document.inscripform.info_nav.value=nom;</script>
</TD></TR>
<TR>
<TD align=right><font class="T2"><?php print LANGPER21?> :</font></TD>
<TD >
<select name='saisielangue'>
<?php print $choix ?>
<option value='fr'   id='select1' >Fran&ccedil;ais</option>
<option value='en'   id='select1'  >Anglais</option>
<option value='arabe'  id='select1'  >Arabe</option>
<option value='es'  id='select1'  >Espagnol</option>
<option value='it'   id='select1'  >Italien</option>
<optgroup label="---------------------">
<option value='bret' id='select1'  >Breton</option> 
<option value='occitan' id='select1'  >Occitan</option> 
</select>
<div name="erreur"  id="erreur" style="POSITION:absolute;z-index:2"><script>document.write(contenu);</script></div>

</TD></TR></TABLE></font></p></FORM>
<?php
if (preg_match('/demo.triade-educ.net/',WEBROOT)) {
        print "<br>";
        if ($_GET["saisie_membre"] == "administrateur") print "&nbsp;&nbsp;&nbsp;<i>Nom : neo - Pr&eacute;nom : neo - mot de passe : matrix</i>";
        if ($_GET["saisie_membre"] == "parent")         print "&nbsp;&nbsp;&nbsp;<i>Nom : merovingien - Pr&eacute;nom : merovingien - mot de passe : matrix</i>";
        if ($_GET["saisie_membre"] == "eleve") print "&nbsp;&nbsp;&nbsp;<i>Nom : merovingien - Pr&eacute;nom : merovingien - mot de passe : matrix</i>";
        if ($_GET["saisie_membre"] == "enseignant") print "&nbsp;&nbsp;&nbsp;<i>Nom : trinity - Pr&eacute;nom : trinity - mot de passe : matrix</i>";
        if ($_GET["saisie_membre"] == "vie scolaire") print "&nbsp;&nbsp;&nbsp;<i>Nom : smith - Pr&eacute;nom : smith - mot de passe : matrix</i>";
        if ($_GET["saisie_membre"] == "tuteurstage") print "&nbsp;&nbsp;&nbsp;<i>Nom : oracle - Pr&eacute;nom : oracle - mot de passe : matrix</i>";
        if ($_GET["saisie_membre"] == "personnel") print "&nbsp;&nbsp;&nbsp;<i>Nom : sati - Pr&eacute;nom : sati - mot de passe : matrix</i>";
}
?>

<br />
<?php
if (isset($_GET["securite"])) {
	print "<br><center><font class=T2 id=color2 >Connexion en mode sécurisée,<br>merci de renouveler votre demande'authentification.</font></center><br><br>";
}

if ($messageaccueil != "") print "<p align=center><font class=T2 color=red><b>$messageaccueil</b></font></p><br/>";
?>

<!-- // fin  -->
</td>
</tr></table>
<?php
if ($verif == 1) {
	print "<br>";
	print "<img src='image/commun/kitwarning.gif' align=left><font class='T2' color='red'>";
	print "<b>".LANGDEPART3."</b> ";
	print LANGDEPART4 ;
	print $codeErreur."<br>";
}

if ($verif == 2) {
	print "<br>";
	print "<img src='image/commun/warning.png' align=left><font class='T2' color='red'>";
	print "<b>".LANGDEPART3bis."</b> ";
	print LANGDEPART4bis ;
	print "<br>";
}

if ($verif == 3) {
	if (file_exists("./data/parametrage/acces.commentaire")) {
		$fp=fopen("./data/parametrage/acces.commentaire","r");
		$donne=fread($fp,9000000);
		$donne=preg_replace("/&lt;br \/&gt;/","<br />",$donne);
		fclose($fp);
		print "<br>";
		print "<img src='image/commun/info2.gif' align=left><font class='T2'>";
		if ($donne == "") $donne=LANGTMESS503;
		print stripslashes($donne) ;
		print "<br>";
	}
}


if ($verif2 == 1) {
	print "<br>";
	print "<img src='image/commun/kitwarning.gif' align=left> $text ";
}

if ($verif2 == 2) {
	print "<br>";
	print "<img src='image/commun/kitwarning.gif' align=left> $text2 ";
}

if (isset($_GET["expire"])) {
	print "<br>";
	print "<img src='image/commun/warning.png' align=left><font class='T2' color='red'>";
	print "<font color='red' class='T2'><b>Votre compte suppléant est expiré !!</b> <br>Merci de contacter la direction afin de revalider votre compte.</font>";
}
?>
</font>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
<?php include_once("./librairie_php/finbody.php"); ?>
<?php if (md5_file("librairie_php/mactu.php") != "7f1e92090ce16c5d90d1acf8e1077010") { ?>


<?php } ?>
</body>
</html>
