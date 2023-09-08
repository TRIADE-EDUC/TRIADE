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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("menuprof");

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa du Responsable d'Unité Enseignement." ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<center>
<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
?>
</center><br>
<form method=post name="formulaire" action="profpUE4.php" >
<?php
if (isset($_POST["consult"])) {
	if (defined("NBCARBULLPROFP")) { $nbcar=NBCARBULLPROFP;  }else{ $nbcar="500"; }
	$saisie_classe=$_POST["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$dataE=chargeMat($res);
	
	$cl=$data[0][0];
	$tri=$_POST["saisie_trimestre"];
	
	$data=recupUECode_UE($_SESSION["id_pers"],$saisie_classe);

	print "<table align=center width='100%' border='0' >";
	if( count($dataE) > 0 ) {	
		for($j=0;$j<count($dataE);$j++) {
			$ideleve=$dataE[$j][1];
			$photoeleve="image_trombi.php?idE=".$ideleve;
			print "<tr>";
			print "<td valign='top' width='5' ><img src='$photoeleve' $taille align='left' /> ";
			print "<input type=hidden value=\"".$dataE[$j][1]."\" name='eleveid_$j' />";
			print "<b> ".ucfirst($dataE[$j][3])." ".strtoupper($dataE[$j][2])."</b>";
			print "</td>";
			print "<td valign='top' >";

			if (count($data) > 0 ) {
				print "<table align=center width='100%' border='0'  >";
				for($i=0;$i<count($data);$i++) {
					$code_eu=$data[$i][0];
					print "<tr>";
					print "<td valign='top' >";
					print "<input type=hidden value='$code_eu' name='id_ue_$j$i' />";
					print "<input type=hidden value=\"".$data[$i][1]."\" name='name_ue_$j$i' />";
					print "<b> ".$data[$i][1]."</b>";
					$com=recherche_com_profp_ue($ideleve,$tri,$code_eu,$saisie_classe); 
					print "<br><textarea cols=60 rows=5 name='comm_$j$i' onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" >$com</textarea>";
					$nbtexte=strlen($com);
					print "&nbsp;<input type=text name='CharRestant_$i' size=3 disabled='disabled' value='$nbtexte' />";
					print "<br /><br /></td>";
					print "</tr>";
				}
				print "</table>";
			}
			print "</td></tr>";
			print "<tr><td colspan='2' ><hr></td></tr>";
		}
		$valider=VALIDER;
		print "<tr><td colspan=2 ><hr><script language=JavaScript>buttonMagicSubmit('$valider','create');</script></td></tr>";
		print '<input type=hidden name="saisie_trimestre" value="'.$tri.'" />';
		print "<input type=hidden name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" />";
		print "<input type=hidden name='saisie_nb'     value='".count($dataE)."' />";
		print "<input type=hidden name='saisie_nb_ue'  value='".count($data)."'  />";
			
	}else{
		print("<tr><td align=center ><font class=T2>".LANGPROJ6."</font></td></tr>");
	}
	print "</table>";
}
?>
</form>
<br>
<!-- // fin form -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
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
</BODY>
</HTML>
