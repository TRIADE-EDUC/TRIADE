<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["annee_scolaire"])) {
        $anneeScolaire=$_POST["annee_scolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa Vie Scolaire" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<form method=post name="formulaire" action="visa_scolaire3.php" >

     <!-- // debut form  -->
<?php
if (isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];

	$sql=" SELECT s.*   FROM (SELECT libelle,elev_id,nom,prenom  FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' UNION ALL SELECT c.libelle,e.elev_id,e.nom,e.prenom  FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY s.nom";

//	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// nom classe
	$cl=$data[0][0];
	$tri=$_POST["saisie_trimestre"];
	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;Année Scolaire : <b>$anneeScolaire</b></font><br />";
	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;Classe : <b>$cl</b></font><br />";
	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;Commentaire pour le $tri <br /><br />";
	print "<table align=center width=100%>";
	if( count($data) > 0 ) {
		for($i=0;$i<count($data);$i++) {
			$ideleve=$data[$i][1];
			$photoeleve="image_trombi.php?idE=".$ideleve;
			print "<tr>";
			print "<td valign='top' width='5' ><img src='$photoeleve' align='left'></td>";
			print "<td valign='top' >";
			print "<input type=hidden value=\"".$data[$i][1]."\" name='eleveid[]' />";
			print "Commentaire pour l'élève : <b> ".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</b>";
			$com=recherche_com_scolaire($ideleve,$tri,$anneeScolaire);
			print "<br><textarea cols=60 rows=5 name='comm[]' onkeypress=\"compter(this,'134', this.form.CharRestant_$i)\" >$com</textarea>";
			$nbtexte=strlen($com);
			print "&nbsp;<input type=text name='CharRestant_$i' size=3 disabled='disabled' value='$nbtexte' />";
			print "<br /><br /></td>";
			print "</tr>";
		}
		$valider=VALIDER;
		print "<tr><td colspan=2 ><hr><script language=JavaScript>buttonMagicSubmit('$valider','create');</script></td></tr>";
		print '<input type=hidden name="saisie_trimestre" value="'.$tri.'" />';
		print "<input type=hidden name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" />";
		print "<input type=hidden name='saisie_nb' value='".count($data)."' />";
		print "</form>";	
	}else{
		print("<tr><td align=center ><font class=T2>".LANGRECH1."</font></td></tr>");
	}
	print "</table>";
}

?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>


<!-- // fin form -->
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
</BODY>
</HTML>
