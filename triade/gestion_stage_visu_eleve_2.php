<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
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
<script language="JavaScript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/scriptaculous.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE71 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>
<?php
if (isset($_GET["id"])) {
	$prenom=recherche_eleve_prenom($_GET["id"]);
	$nom=recherche_eleve_nom($_GET["id"]);
	$idclasse=chercheIdClasseDunEleve($_GET["id"]);
	$ideleve=$_GET["id"];
}
if (isset($_GET["nc"])) {
	$nc="?nc";
	$nc2="&nc";
}

if (isset($_GET["supphisto"])) {
	$identreprise=$_GET['identreprise'];
	$periode=$_GET['periode'];
	$classeeleve=$_GET['idclasse'];
	$ideleve=$_GET['ideleve'];
	supphistoStage($identreprise,$ideleve,$classeeleve,$periode);
}

print "<form method=post action='gestion_stage_visu_eleve.php$nc'>";
print "<table border=0 width=100%><tr><td valign=top><b>".ucwords($prenom)." ".strtoupper($nom)."</b></td><td valign=top>";
print "<input type=submit value='".LANGSTAGE73."' class='BUTTON' >";
print "<input type=hidden name=saisie_classe value='".chercheIdClasseDunEleve($_GET["id"])."'></form>";
print "</td></tr></table>";

print "&nbsp;&nbsp;[<a href='#' onclick=\"Effect.SlideDown('histo'); return false;\" >Historique des stages</a>] <br><br>";
print "<div style='display:none;' id='histo'>";
print "<table width=100% border=1 style='border-collapse: collapse;' >";
print "<tr>";
print "<td width=5 bgcolor='yellow' >&nbsp;Période&nbsp;</td>";
print "<td align=center  bgcolor='yellow'  >&nbsp;Classe&nbsp;</td>";
print "<td align=center  bgcolor='yellow'  >&nbsp;".LANGSTAGE39."&nbsp;</td>";
print "<td align=center  bgcolor='yellow' width='5%' >&nbsp;"."Supprimer"."&nbsp;</td>";
print "</tr>";
$data=recherche_stage_historique($ideleve); //e.nom,s.nomprenomeleve,s.classeeleve,s.periodestage
for($i=0;$i<count($data);$i++) {
	$nom_entreprise=$data[$i][0];
	$periode=preg_replace('/ /','&nbsp;',$data[$i][3]);
	$classe=$data[$i][2];
	$identreprise=$data[$i][7];
	print "<tr bgcolor='#FFFFFF' >";
	print "<td width=5 >&nbsp;$periode&nbsp;</td>";
	print "<td >&nbsp;$classe&nbsp;</td>";
	print "<td >&nbsp;<a href='gestion_stage_ent_visu_rech_nom.php?recherche=$nom_entreprise' title='Consulter' >$nom_entreprise</a>&nbsp;</td>";
	print "<td><input type=button onclick=\"open('gestion_stage_visu_eleve_2.php?supphisto=1&identreprise=$identreprise&periode=$periode&idclasse=$classe&ideleve=$ideleve&id=$ideleve','_parent','')\" value='Supprimer' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'></td>";
	print "</tr>";
}
print "</table><br><br>";
print "</div>";

print "<table width=100% border=1 style='border-collapse: collapse;' >";
print "<tr ><td width=5 bgcolor='yellow' >&nbsp;N°&nbsp;Stage&nbsp;</td>";
print "<td align=center  bgcolor='yellow' >".LANGSTAGE72."</td>";
print "<td align=center  bgcolor='yellow'  width=40%>&nbsp;".LANGSTAGE39."&nbsp;</td>";
print "<td align=center  bgcolor='yellow' width='5%'  >&nbsp;".LANGSTAGE37."&nbsp;</td></tr>";
$data=recherche_stage_eleve($ideleve);
// id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,id,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance
for($i=0;$i<count($data);$i++) {
	if ($data[$i][17] == 1) { 
		$etat="Alternance"; 
		$date=dateForm($data[$i][19]).' au '.dateForm($data[$i][20]);
	}else{ 
		$etat=LANGSTAGE50; 
		$date=recherchedatestage2($data[$i][11],$idclasse);
	}
?>
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td><?php print $etat ?> <?php print rechercheNumStage($data[$i][11]) ?></td>
	<td align=center><?php print $date ?></td>
	<td>&nbsp;<?php $numstage=rechercheNumStage($data[$i][11]); $identr=$data[$i][1]; print "<a href='gestion_stage_ent_visu_rech_nom.php?recherche=$identr' title='Consulter' >".trunchaine(recherche_entr_nom_via_id($identr),20); ?></a> </td>
	<td width=5 align=center><input type=button onclick="open('gestion_stage_visu_eleve_3.php?id=<?php print $_GET["id"]?>&idclasse=<?php print $idclasse?>&idstage=<?php print $data[$i][13]?><?php print $nc2 ?>','_parent','')" value="<?php print LANGBT28 ?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td>
	</tr>
<?php
}
?>
</table>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
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
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
