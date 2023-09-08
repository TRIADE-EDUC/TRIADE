<?php
if (!isset($_GET["id"])) {
	include_once("./common/config2.inc.php");
	if (defined("PASSOUBLIE")) {
		if (PASSOUBLIE == "oui") {
			header("Location:probleme_acces_2.php");
			exit;
		}
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php 
include_once("./librairie_php/lib_netscape.php"); 
include_once("./librairie_php/lib_licence2.php");
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
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}


if (isset($_POST["saisie_question"])) {
	if ( (!empty($_POST["saisie_question"])) && (!empty($_POST["email"])) && (!empty($_POST["nom"])) && (!empty($_POST["prenom"]) ) ) {
		include_once("./librairie_php/timezone.php");
		$saisie_question=$_POST["saisie_question"];
		$email=$_POST["email"];
		$nom=$_POST["nom"];
		$prenom=$_POST["prenom"];
     		$today= dateDMY();
	     	$fichier=fopen("./data/fic_probleme.txt","a+");
	     	fwrite($fichier,"<font color=red>Le $today : </font> $saisie_question <br><br><u>Contact</u> : $nom $prenom <br><br> <u>Email</u> : $email<br><br>");
     		fclose($fichier);
	     	include_once("./librairie_php/db_triade.php");
     		include_once("./common/config4.inc.php");
	     	if (MAILMESSSYS == "oui") {
				mailAdmin("Problème d'accès à un compte");
		}
	     	print "<script>alert(\"".LANGTPROBL12."\")</script>";
     		print "<script>location.href='index.html'</script>";
	}else{
		print "<script>alert(\"".LANGPROBLE2."\")</script>";
	}
}

include_once("./common/config2.inc.php");

if (HTTPS == "non") {
	print "<script type='text/javascript'>var http='http://';</script>\n";
}else{
	print "<script type='text/javascript'>var http='https://';</script>\n";
}
print "<script type='text/javascript'>var vocalmess='problemeacces';</script>\n";
print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";
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
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepartconnection1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTTITRE5 ?></FONT></td>
</tr>
<tr id='cadreCentral0'>
<td >
<?php
$cnx=cnx();
$data=visu_paramViaIdSite('1');
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire
$nom_etablissement=$data[0][0];
$tel=$data[0][4];
$email=$data[0][5];
$urlsite=$data[0][7];
Pgclose();
?>
<br>
<table bgcolor='#FFFFFF' border='1' bordercolor='#000000' align='center' width='70%'  style="box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); border-radius: 25px ; -webkit-border-radius: 25px; -moz-border-radius: 25px; " >
<tr><td id='bordure' align='center'><br />&nbsp;&nbsp;<font class='T2'><b><font size=4><?php print $nom_etablissement ?></font></b><br><?php print $tel." ".$email ?><br><?php print $urlsite ?>&nbsp;&nbsp;</font><br><br></td></tr>
</table>
<br>
<?php
if (preg_match('/demo.triade-educ.net/',WEBROOT)) {
	$disabled="disabled='disabled'";
	$message="Version de démonstration, vous êtes actuellement sur la platforme de démonstration.\n\n(Saisie Impossible)\n\nL'Equipe Triade";
}
?>

<form name="formulaire" method="post">
<table border='0' width=100%>
<tr>
<td>
<ul>
<br>
<B><font class=T2><?php print LANGTPROBL5 ?></B> : 
<BR><BR>
<br>
<?php print LANGFORUM10 ?> : <input type=text name="nom" size=40 <?php print $disabled ?> value="<?php print $_POST["nom"] ?>" ><BR><BR>

<?php print LANGFORUM10bis ?> : <input type=text name="prenom" size=40 <?php print $disabled ?> value="<?php print $_POST["prenom"] ?>" ><BR><BR>

<?php print LANGELE244 ?> : <input type=text name="email" size=40 <?php print $disabled ?> value="<?php print $_POST["email"] ?>" > <i><font class=T1>(<?php print LANGPROBLE1?>)</font></i> <br><br>
</ul></td></tr>
<tr><td colspan='2' align='center'>
<?php print LANGASS27 ?> : <br>
<textarea name="saisie_question" cols="75" rows="9" <?php print $disabled ?> >
<?php print $message ?>
<?php print $_POST["saisie_question"] ?>
</textarea>
</td></tr>
</table>
<br>
<BR><center><input type=submit value="<?php print LANGTPROBL10 ?>" class='bouton2' <?php print $disabled ?> ></center>
</form>
<br /><br />
</tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
