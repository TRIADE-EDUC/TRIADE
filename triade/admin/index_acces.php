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
//error_reporting(0);

function error ($error_message) {
        echo $error_message."<br />";
        exit;
}
include_once("../common/lib_acces_inc.php");
if (file_exists("../../../common/lib_acces_inc.php")) {
	include_once("../../../common/lib_acces_inc.php");
}
include_once("../common/config.inc.php");

$disabled="";
if (file_exists("../data/install_log/noaccess.inc")) {   // Interruption du service unique
	$verif=2;
	$disabled="disabled";
}
$affiche="oui";
//if ((SERVEURTYPE == "SERVEURMUTUA") || (SERVEURTYPE == "SERVEUROVH") ||  (isset($_POST["create"])) ) {
if (true) {
	if (!isset($_POST["create"])) {
	
		include_once("../common/lib_admin.php");
		include_once("../common/lib_ecole.php");
		include_once("./librairie_php/langue.php");
		include_once("./librairie_php/lib_licence_text.php");
		include_once("./librairie_php/lib_error.php");
		include_once("./librairie_php/mactu.php");
		include_once("../common/config2.inc.php");
		include_once("../librairie_php/timezone.php");
		 
?>
<html>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<LINK REL="SHORTCUT ICON" href="./favicon.ico">

		<TITLE>TRIADE -  ADMINISTRATEUR</TITLE>
		</head>
		<body>
		<br><br><br>
		<center>
		<form method='post'>
		<table width=300><tr><td><img src="../image/commun/logo_triade_licence.gif"></td></tr></table>
		<br><br>
		<table border=1 bordercolor="#000000" cellPadding="0" cellSpacing="0"><tr><td  bordercolor="#FCE4BA" >
		<table bordercolor="#000000" border="0" id='bodyfond2' height="100" width="300" cellPadding="0" cellSpacing="0" style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);' >
		<tr><td align=right ><font class="T2">Login :&nbsp;</font></td>
		<td>&nbsp;<input type=text name="login" size=20 class=bouton2 >&nbsp;</td></tr>
		<tr><td align=right><font class="T2">&nbsp;&nbsp;Mot&nbsp;de&nbsp;passe&nbsp;:&nbsp;</font></td>
		<td>&nbsp;<input type=password name="passwd" size=20 class=bouton2 >&nbsp;&nbsp;&nbsp;</td></tr>
		</table></td></tr></table>
		<br><br>
		<table align=center><tr><td>
		<script language=JavaScript>buttonMagicSubmitAtt("Valider","create","<?php print $disabled?>"); //text,nomInput</script>
		</td></tr></table>
		<br><br> Version : <b><?php print VERSION ?></b>
		</form>

<?php
		if ($verif == 2) {
        		include_once("../librairie_php/langue-text-fr.php");
			print "<br>";
			print "<table width=450 align=center><tr><td><img src='../image/commun/warning.png' align=left><font class='T2' color='red'>";
			print "<b>".LANGDEPART3bis."</b> ";
			print LANGDEPART4bis ;
			print "</font></td></tr></table><br>";
		}
?>
		</center>
		<div align='center'><?php print PIEDPAGE ?>
		<img src='../image/commun/triade-xhtml.jpg' alt='XHTML'>  
		<img src='../image/commun/triade-w3C.jpg' alt='w3C'> 
		<img src='../image/commun/triade-css.png' alt='css' > 
		<a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' >
			<img border='0' src='../image/commun/triade_paypal.png' alt='Paypal' >
		</a>
		<br /><br />
		</div></td></tr></table></div>
		</body>
		</html>
<?php
		$affiche="non";
	}else{
		if ((file_exists("../../../common/lib_acces_inc.php")) && ($verif != 2))   {
			if ( (($_POST["login"] == $LOGIN )  &&  (crypt(md5($_POST["passwd"]),"T2") == $PASSWORD ))   || 
			     (($_POST["login"] == $LOGINT )  &&  (crypt(md5($_POST["passwd"]),"T2") == $PASSWORDT ))  ) {
				$admin1="Administrateur";
				$langue="fr";
				$_SESSION["admin1"]=$admin1;
				$_SESSION["langue"]=$langue;      
				?>
				<HTML>
				<HEAD>
				<META http-equiv="CacheControl" content = "no-cache">
				<META http-equiv="pragma" content = "no-cache">
				<META http-equiv="expires" content = -1>
				<meta name="Copyright" content="Triade©, 2001">
				<TITLE>TRIADE</TITLE>
				<LINK REL="SHORTCUT ICON" href="./favicon.ico">
				<script language=JavaScript src="librairie_js/clickdroit2.js"></script>
				<SCRIPT LANGUAGE=JavaScript>
				function ouvert() {
		        		location.href="index1.php";
				}
				</script>
				</HEAD>
				<BODY  background="image/attente.jpg" OnLoad="ouvert();">
				</BODY>
				</HTML>
			
			<?php
			}else{
	        		error("Erreur d'accés ...");
			}
	
			
		}else{

			if ( ($_POST["login"] == $LOGIN )  &&  (crypt(md5($_POST["passwd"]),"T2") == $PASSWORD ) ) {
				$admin1="Administrateur";
				$langue="fr";
				$_SESSION["admin1"]=$admin1;
				$_SESSION["langue"]=$langue;
		?>
				<HTML>
				<HEAD>
				<META http-equiv="CacheControl" content = "no-cache">
				<META http-equiv="pragma" content = "no-cache">
				<META http-equiv="expires" content = -1>
				<meta name="Copyright" content="Triade©, 2001">
				<TITLE>TRIADE</TITLE>
				<LINK REL="SHORTCUT ICON" href="./favicon.ico">
				<script language=JavaScript src="librairie_js/clickdroit2.js"></script>
				<SCRIPT LANGUAGE=JavaScript>
				function ouvert() {
		        		location.href="index1.php";
				}
				</script>
				</HEAD>
				<BODY  background="image/attente.jpg" OnLoad="ouvert();">
				</BODY>
				</HTML>
			
			<?php
			}else{
	        		error("Erreur d'accés ...");
			}
		}

	}
}else{
	if ( (!isset($_SERVER['PHP_AUTH_USER'])) || ! (($_SERVER['PHP_AUTH_USER'] == "$LOGIN") && ( crypt(md5($_SERVER['PHP_AUTH_PW']),"T2")  == "$PASSWORD" )) ) {
        	header("WWW-Authenticate: Basic entrer=\"Form2txt admin\"");
	        header("HTTP/1.0 401 Unauthorized");
        	error("Erreur d'accés ...");
	}
}

if ($affiche != "non") {
	$admin1="Administrateur";
	$langue="fr";
	$_SESSION["admin1"]=$admin1;
	$_SESSION["langue"]=$langue;
?>
	<HTML>
	<HEAD>
	<META http-equiv="CacheControl" content = "no-cache">
	<META http-equiv="pragma" content = "no-cache">
	<META http-equiv="expires" content = -1>
	<meta name="Copyright" content="Triade©, 2001">
	<TITLE>TRIADE</TITLE>
	<LINK REL="SHORTCUT ICON" href="./favicon.ico">
	<script language=JavaScript src="librairie_js/clickdroit2.js"></script>
	<SCRIPT LANGUAGE=JavaScript>
	function ouvert() {
	        location.href="index1.php";
	}
	</script>
	</HEAD>
	<BODY  background="image/attente.jpg" OnLoad="ouvert();">
	</BODY>
	</HTML>
<?php
}
?>
