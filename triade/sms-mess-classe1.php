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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Envoi message SMS" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<form method="post" name="form" action="sms-mess-classe2.php" ><br>
<!-- // debut form  -->
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");

if (LAN == "oui") {
	if (file_exists("./common/config-sms.php")) {
		include_once("./common/config-sms.php");
		$idsms=SMSKEY;
		$inc=GRAPH;
		$urlsms=SMSURL;
		$oo=0;

		$nbtel=$_POST["nbtel"];
		if ($nbtel == "0") {
			$pasdetel="oui";
		}else{
			$pasdetel="non";
			for($i=0;$i<=$nbtel;$i++) {
				$o="tel$i";
				$telsms=$_POST[$o];
				if ($telsms != "") {
					$tab=preg_split('/#/',$telsms);
					$telsms=$tab[1];
					$idEleve=$tab[0];
					if (is_numeric($telsms)) {
						$liste.=$telsms.", ";
						print "<input type='hidden' name='sms[]' value='$idEleve#".$telsms."' />";
						$oo++;
					}
				}
			}
		}
?>
	
		<table border=0 align=center >
		<tr><td><font class=T2><?php print LANGSMS3 ?> : </font><?php print $liste ?> 
		<br /><br /></td></tr>
		<tr><td>
		<?php print LANGSMS5 ?> : (<?php print LANGSMS4 ?>) <br>
		<textarea cols=84 rows=4 name="message" onkeypress="compter(this,'150', this.form.CharRestant)" ></textarea><br><input type='text' name='CharRestant' size='2' disabled='disabled'> <font size=1><i><?php print LANGSMS6 ?>.</i></font>
		<br><br><br>
		<input type='hidden' name='nb' value='<?php print $oo ?>' />
<?php 
		$nb=0;
		$nb=file_get_contents($urlsms."sms-info-nb.php?idsms=$idsms");
		if ($nb > 0) {
			if ($pasdetel != "oui") {
				print "<script language=JavaScript>buttonMagicSubmit('Envoyer','create'); //text,nomInput</script>";	
			}else{
				print "<font id='color3' class='T2'>Aucun numéro de téléphone d'indiquer.</font>";
				print "<br><br><script language=JavaScript>buttonMagicRetour('compta_consulte_retard.php','_self'); //text,nomInput</script>";	
			}
		}else{
			print "<img src='image/commun/warning2.gif' align='center'> <font class=T2><b>Crédit SMS Epuisé !!</b></font>";
		}
		?>
		</td></tr>
		</table><br>	
<?php			
	}else{
		print "<center><font color=red class='T2' >".LANGMESS37.".</font></center>";
	}
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR3."</i></center>";
}

?>


<!-- // fin form -->
</td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY>
</HTML>
