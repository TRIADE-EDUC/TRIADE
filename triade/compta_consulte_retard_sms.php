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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

$tabideleve=$_POST['ideleve'];
foreach($tabideleve as $key => $ideleve) { $data[$ideleve]=$ideleve; }

if(isset($_POST["consult"])) { 
	$o=0;
	$filtreSMS=config_param_visu('smsfiltre');
	$filtreSMS=$filtreSMS[0][0];

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print "SMS pour impayé(s) ou paiement(s) incomplet(s)"?> / <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b> </font></b></td></tr>
<tr id='cadreCentral0' >
<td valign="top">
	<form method="post" action="sms-mess-classe1.php" >
	<table width=100%>
<?php 
	if( count($data) <= 0 ) {
		print("<tr><td align=center valign=center id='cadreCentral0'><font class=T2>".LANGRECH1."</font></td></tr>");
	} else {
?>
		<tr >
		<td bgcolor="yellow" ><B><?php print ucwords(LANGIMP8)." "; print ucwords(LANGIMP9); ?></B></td>
		<td bgcolor="yellow" width="50%" align="center"><b><?php print ucwords("Envoyer")?></b></td></tr>
<?php
		foreach($data as $key => $ideleve) {
			$idEleve=$ideleve;
			$nomeleve=recherche_eleve_nom($idEleve);
			$prenomeleve=recherche_eleve_prenom($idEleve);
			
	?>
	<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td><?php print strtoupper($nomeleve)." " ; print trunchaine(ucwords($prenomeleve),30); ?></td>
	<td>
<?php
	$tel1=cherchetelportable1($idEleve); 
	$tel2=cherchetelportable2($idEleve); 
	$tel=cherchetel($data[$i][1]);
	print "<table border=0 width='100%' >";
	if (preg_match("/^$filtreSMS/",$tel1)) {
		$tel1=preg_replace('/ /',"",$tel1);
		$tel1=preg_replace('/\./',"",$tel1);
		if (is_numeric($tel1)) { 
			$o++; 
			print "<tr><td align='right' width=50% >Tél Portable 1 : </td><td>$tel1 <input type=checkbox value='$idEleve#$tel1' name='tel$o' ></tr>"; 
		}
	}
	if (preg_match("/^$filtreSMS/",$tel2)) {
		$tel2=preg_replace('/ /',"",$tel2);
		$tel2=preg_replace('/\./',"",$tel2);
		if (is_numeric($tel2)) { 
			$o++; 
			print "<tr><td align='right' width=50% >Tél Portable 2 : </td><td>$tel2 <input type=checkbox value='$idEleve#$tel2' name='tel$o' ></tr>"; 
		}
	}
	if (preg_match("/^$filtreSMS/",$tel)) {
		$tel=preg_replace('/ /',"",$tel);
		$tel=preg_replace('/\./',"",$tel);
		if (is_numeric($tel)) { 
			$o++; 
			print "<tr><td align='right' width=50% >Téléphone : </td><td>$tel  <input type=checkbox value='$idEleve#$tel' name='tel$o' ></tr>"; 
		}
	}
	print "</table>";
?>
</td>
	</tr>
	<?php
	}
	print "<tr><td colspan='2' bgcolor='#FFFFFF' align='center' ><br><br><input type='submit' name='envSmsClasse' value='Enregistrer' class='BUTTON' /><br><br>";
	print "<input type='hidden' name='nbtel' value='$o' /></td></tr>";
}
	print "</table>";
	print "</form>";
}
?>


<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
