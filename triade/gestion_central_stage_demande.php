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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/centralestage.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visualisation des demandes d'affiliation" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>

<table width="100%" border="0" align="center" bgcolor='#FFFFF'>
<?php
if (isset($_POST["ok"])) { actionDemandeValidation($_POST["productid"],"ok",$_POST["pwd"]);$etat="1"; }
if (isset($_POST["pasok"])) { actionDemandeValidation($_POST["productid"],"pasok",'');$etat="2"; }
if (isset($_POST["supp"])) { actionDemandeValidation($_POST["productid"],"supp",'');$etat="3"; }
if ($etat != "") {
	print "<script language='JavaScript' src='https://support.triade-educ.org/centralestage/etataffiliation.php?productidclient=".$_POST["productid"]."&productidcentral=".PRODUCTID."&etat=$etat' ></script>";
}

$data=listeDemandeAffiliation();
//datedemande,nom,email,etablissement,ville,pays,productid,autorise,password
for($i=0;$i<count($data);$i++) {
	$etablissement=$data[$i][3];
	$date=dateForm($data[$i][0]);
	$ville=$data[$i][4];
	$pays=$data[$i][5];
	$productid=$data[$i][6];
	$autorise=$data[$i][7];
	$contact=strtoupper($data[$i][1]);
	$email=$data[$i][2];
	$auto="";
	$pass=trim($data[$i][8]);
	if ($pass == "") { $pass=md5(rand(1000,9999)); }
	$disabledauto1="";$disabledauto2="";
	if ($autorise == 1) { $disabledauto1="disabled='disabled'"; $auto="<font class=T2 color=red>Autorisé</font>"; } 
	if ($autorise == 0) { $disabledauto2="disabled='disabled'"; $auto="<font class=T2 color=red>Non autorisé</font>"; } 
	print "<form method=post name=formulaire action='gestion_central_stage_demande.php'>";
	print "<tr>";
	print "<td><font class=T2>";
	print "$date - L'Etablissement : $etablissement <br><br>";
	print "Contact : $contact ($email) ";
	if ((ValideMail($email)) && (LAN == "oui")){
		print "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value=\"Envoi du mot de passe à $contact\" class='bouton2' 
			onclick=\"open('gestion_central_stage_envoimail.php?id=$productid','_parent','')\" />";
	}
	print "<br><br>";
	print "Ville : $ville ($pays) <br><br>";
	print "Mot de passe : $pass <input type=hidden name='pwd' value='$pass' /> <br><br>";
	print "Action : <input type=submit class=button name='ok' onclick=\"InfoCentralTriade('1','$productid','".PRODUCTID."')\" value=\"Autoriser\" $disabledauto1 /> ";
	print "<input type=submit class=button name='pasok' onclick=\"InfoCentralTriade('0','$productid','".PRODUCTID."')\" value=\"Interdire\" $disabledauto2 /> ";
	print "<input type=submit class=button name='supp' onclick=\"InfoCentralTriade('0','$productid','".PRODUCTID."')\" value=\"Supprimer\" />";
	print "<input type=hidden name=productid value=\"$productid\" /> $auto";
	print "</td>";
	print "</tr></form>";
	print "<tr><td><hr></td></tr>";

}
?>

</table>
</form>
</script>
<br>
     <!-- // fin  -->
     </td></tr></table>
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
