<?php
session_start();
error_reporting(0);
$nom=$_SESSION["nom"];
$prenom=$_SESSION["prenom"];
$membre=$_SESSION["membre"];
$langue=$_SESSION["langue"];


session_set_cookie_params(0);
$_SESSION=array();
session_unset();
session_destroy();
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); 
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
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}


?>

<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_type_navigateur.js"></script>
<title>Triade</title>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include("./librairie_php/lib_licence2.php"); 
?>
<br><br><br>
<form method=post name="inscripform" action="acces.php">
<table border=1 bordercolor="#000000" width=400 align=center bgcolor="#FFFFFF" height="140" style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);' >
<tr><td colspan=2 id="bordure" >
<font class=T2><img src="image/commun/img_ssl.gif" align="center" />&nbsp; Compte de <b><?php print strtoupper($nom) ?> <?php print ucfirst($prenom) ?></b>  verrouillé.

<br /><br />&nbsp; Mot de passe  : <input type="password" name="saisiepasswd" /> <input type=submit class=button value="connexion" name=rien /> 
<input type="button" class=button value="Quitter" onclick="open('index.html','_parent','')" />
</font></td></tr>

<tr><td  id="bordure" ><?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;");
?></td></tr>
</table>


<?php
if ($membre == "menuadmin") { $membre="administrateur";}
if ($membre == "menuparent") { $membre="parent";}
if ($membre == "menuprof") { $membre="enseignant";}
if ($membre == "menueleve") { $membre="eleve";}
if ($membre == "menuscolaire") { $membre="vie scolaire";}
if ($membre == "menututeur") { $membre="tuteur";}
?>
<input type="hidden" name="saisienom" value="<?php print strtolower($nom) ?>" />
<input type="hidden" name="saisieprenom" value="<?php print strtolower($prenom) ?>" />
<input type="hidden" name="saisie_membre" value="<?php print $membre ?>" />
<input type="hidden" name="saisielangue" value="<?php print $langue ?>" />
<input type="hidden" name=info_nav>
<script language=JavaScript>document.inscripform.info_nav.value=nom;</script>
</form>

<br><br><br>
<div align='center'><?php top_p();?></div>


<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</BODY>
</HTML>
