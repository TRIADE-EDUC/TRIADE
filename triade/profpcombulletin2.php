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
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa du Professeur Principal." ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
$anneeScolaire=$_COOKIE["anneeScolaire"];
?>
<form method=post name="formulaire" action="profpcombulletin3.php" >
<?php
if (isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];
//	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$sql="SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$saisie_classe' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT c.libelle, e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 3";
	$res=execSql($sql);
	$data=chargeMat($res);

	// nom classe
	$cl=$data[0][0];
	$tri=$_POST["saisie_trimestre"];
	$triMessage=" le $tri ";
	if ($tri == "exam_juin") { $triMessage="l'examen de Juin" ; }
	if ($tri == "exam_dec") { $triMessage="l'examen de Décembre" ; }
	if ($tri == "periode1") { $triMessage="1er période" ; }
	if ($tri == "periode2") { $triMessage="2ieme période" ; }
	if ($tri == "periode3") { $triMessage="3ieme période" ; }
	if ($tri == "periode4") { $triMessage="4ieme période" ; }
	if ($tri == "periode5") { $triMessage="5ieme période" ; }
	if ($tri == "periode6") { $triMessage="6ieme période" ; }
	if ($tri == "periode7") { $triMessage="7ieme période" ; }
	if ($tri == "periode8") { $triMessage="8ieme période" ; }
	if ($tri == "periode9") { $triMessage="9ieme période" ; }
		
	print "<br /><table width=100%><tr><td><font class=T2>&nbsp;&nbsp;&nbsp;Classe : <b>$cl</b> / ".LANGBULL3." : <b>$anneeScolaire</b> </font></td><td>";
	print "<script language=JavaScript>buttonMagic(\"Moyennes, Graphs, ...\",\"profpprojo.php?idClasse=$saisie_classe\",'video','width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes','');</script></td></tr></table>";
	print "<br />";


	if ($_POST["type_bulletin"] == "paravenir") $type_commentaire_bulletin="==> Parcours avenir";
	if ($_POST["type_bulletin"] == "parcitoyen") $type_commentaire_bulletin="==> Parcours citoyen";
	if ($_POST["type_bulletin"] == "pareducart") $type_commentaire_bulletin="==> Parcours d'éducation artistique et culturelle";
	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;Commentaire pour $triMessage  <font id='colort1' >$type_commentaire_bulletin</font><br /><br />";


	if (defined("NBCARBULLPROFP")) { $nbcar=NBCARBULLPROFP;  }else{ $nbcar="500"; }

	print "<table align=center width=100%>";
	if( count($data) > 0 ) {
			
		for($i=0;$i<count($data);$i++) {
			$ideleve=$data[$i][1];
			$photoeleve="image_trombi.php?idE=".$ideleve;
			print "<tr>";
			print "<td valign='top' width='5' ><img src='$photoeleve' $taille align='left'></td>";
			print "<td valign='top' >";
			print "<input type=hidden value=\"".$data[$i][1]."\" name='eleveid[]' />";
			print " <b> ".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</b>";
			$com=recherche_com_profP($ideleve,$tri,$anneeScolaire,$_POST["type_bulletin"]);
			print "<br><textarea cols=60 rows=5 name='comm[]' onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" >$com</textarea>";
			$nbtexte=strlen($com);
			print "&nbsp;<input type=text name='CharRestant_$i' size=3 disabled='disabled' value='$nbtexte' />";
			print "<br /><br /></td>";
			print "</tr>";
			if ($_POST["type_bulletin"] == "leap") {
				$leap=rechercheleap($ideleve,$_POST["type_bulletin"],$tri); //leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav
			      	if ($leap[0][1] == "1") { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
			      	if ($leap[0][2] == "1") { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
			      	if ($leap[0][0] == "1") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
			      	if ($leap[0][3] == "1") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
			      	print "<tr><td colspan='2' >\n";
			      	print "&nbsp;&nbsp;";
			  //  	print "Aucun <input type='checkbox' name='montessori_${ideleve}' value='' />&nbsp;&nbsp;\n";
			      	print "Félicitations <input type='checkbox' name='leap_felicitation_${ideleve}' value='1' $checkedmont1 />&nbsp;&nbsp;\n";
			      	print "Encour. <input type='checkbox' name='leap_encouragement_${ideleve}' value='1' $checkedmont3 title='Encouragement' />&nbsp;&nbsp;\n";
			      	print "MEG Comp. <input type='checkbox' name='leap_megcomp_${ideleve}' value='1' $checkedmont2 title='Mise en garde comportement' />&nbsp;&nbsp;\n";
			      	print "MEG Trav. <input type='checkbox' name='leap_megtrav_${ideleve}' value='1' $checkedmont4 title='Mise en garde travail' />&nbsp;&nbsp;\n";
			      	print "</td></tr>";
			}

		}
		$valider=VALIDER;
		print "<tr><td colspan=2 ><hr><script language=JavaScript>buttonMagicSubmit('$valider','create');</script></td></tr>";
		print '<input type=hidden name="saisie_trimestre" value="'.$tri.'" />';
		print "<input type=hidden name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" />";
		print "<input type=hidden name='saisie_nb' value='".count($data)."' />";
		print "<input type=hidden name='type_bulletin' value='".$_POST["type_bulletin"]."' />";
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
