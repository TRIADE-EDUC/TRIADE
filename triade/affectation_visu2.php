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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$saisie_tri=$_POST["saisie_tri"];
$anneeScolaire=$_POST["anneeScolaire"];

if (isset($_GET["saisie_tri"])) { $saisie_tri=$_GET["saisie_tri"]; }
if (isset($_GET["annee_scolaire"])) { $anneeScolaire=$_GET["annee_scolaire"]; }

$saisie_classe=$_POST["saisie_classe"];
if (isset($_GET["saisie_classe"])) { $saisie_classe=$_GET["saisie_classe"]; }

if (!isset($_GET["visu"])) { 
?> 
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php } 

if (!isset($_GET["visu"])) { 
	$width="100%";
}else{
	$width="100%";
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="<?php print $width ?>" bgcolor="#0B3A0C" height="85" style="border-collapse: collapse;" >
<tr id='coulBar0' ><td height="2"><div style="float:left" ><b><font   id='menumodule1' ><?php print LANGPER28?> <b><font color=yellow><?php $classe=chercheClasse($saisie_classe) ; print $classe[0][1]; ?></font></div>
<div align='right'  >
	<?php 
	if (!isset($_GET["visu"])) { ?>
		<a href="affectation_visu2.php?saisie_tri=<?php print $saisie_tri ?>&saisie_classe=<?php print $saisie_classe ?>&annee_scolaire=<?php print $anneeScolaire ?>" onclick="open('affectation_visu2.php?saisie_tri=<?php print $saisie_tri ?>&visu&saisie_classe=<?php print $saisie_classe ?>&annee_scolaire=<?php print $anneeScolaire ?>','visu','width=800,height=600,resizable=yes,scrollbars=yes');" alt='agrandir'><img src="./image/commun/fen.gif" title="agrandir" border="0" /></a>&nbsp;</span>
	<?php } ?>
</b></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top>
<br>
<table>
<tr>
<?php if (!isset($_GET["imprime"])) { ?>
<td><script>buttonMagic("Imprimer",'affectation_visu2.php?saisie_tri=<?php print $saisie_tri ?>&visu&saisie_classe=<?php print $saisie_classe ?>&annee_scolaire=<?php print $anneeScolaire ?>&imprime','imp','width=800,height=600,resizable=yes,scrollbars=yes','')</script></td><td><script>buttonMagicRetour("affectation_visu.php","_self")</script></td>
<?php } ?>
<td> <font class='T2 shadow'>Ann&eacute;e Scolaire : <?php print $anneeScolaire ?></font></td></tr></table>
<br><br>
<!-- //  debut -->
<?php 
$libelle=libelleTrimestre($saisie_tri);
if (($saisie_tri != "tous") && ($saisie_tri != "")) { 
	print "<font class='T2'>&nbsp;&nbsp;&nbsp;$libelle</font>";
	print "<br /><br />"; 
}
?>



<table border='1' bordercolor=#000000" align='center' width='100%' style="border-collapse: collapse;" >
<TR>
<!-- importance du champ apparition ??? -->
<td bgcolor="yellow" align=center><?php print LANGPER17?></td>
<td bgcolor="yellow" align=center><?php print LANGPER18?></td>
<td bgcolor="yellow" align=center><?php print LANGPER19?></td>
<td bgcolor="yellow" align=center><?php print LANGPER20?></td>
<td bgcolor="yellow" align=center><?php print "Lang."?></td>
<td bgcolor="yellow" align=center><?php print LANGMESS363."&nbsp;1<i>*</i>"?></td>
<td bgcolor="yellow" align=center><?php print "Visu&nbsp;2"."<i>**</i>"?></td>
<td bgcolor="yellow" align=center><?php print "Nb&nbsp;H."?></td>
<td bgcolor="yellow" align=center><?php print "ECTS"?></td>
<td bgcolor="yellow" align=center><?php print LANGMESS364 ?></td>
<td bgcolor="yellow" align=center><?php print LANGTMESS470 ?></td>
<td bgcolor="yellow" align=center><?php print "Info<br>Sem." ?></td>
<td bgcolor="yellow" align=center><?php print "Coef<br>certif." ?></td>
<td bgcolor="yellow" align=center><?php print "Note<br>plancher" ?></td>
</TR>
<?php
$data=visu_affectation_detail_2($saisie_classe,$saisie_tri,$anneeScolaire);

/*      ordre_affichage,
        code_matiere,
        code_prof,
        code_classe,
        coef,
        g.libelle,
        a.langue,
        a.avec_sous_matiere,
        a.visubull,
        a.nb_heure,
        a.ects,
        a.id_ue_detail,
        a.specif_etat,
        a.annee_scolaire,
        a.visubullbtsblanc,
        a.num_semestre_info,
        a.trim,
        a.coef_certif,
        a.note_planche
*/
for($i=0;$i<count($data);$i++) {
	$ue=$data[$i][11];


?>
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<TD><?php print stripslashes(ucwords(chercheMatiereNom($data[$i][1])))?></td>
	<TD><?php print recherche_personne($data[$i][2])?></td>
	<TD><?php print (trim($data[$i][4]) == "") ? "&nbsp;" : $data[$i][4];?></td>
	<TD><?php print (trim($data[$i][5]) == "") ? "&nbsp;" : $data[$i][5];?></td>
	<TD><?php print preg_replace('/^0$/','',$data[$i][6])?></td>
	<TD><?php print ($data[$i][8] == 1) ? "<img src='image/commun/valid.gif' align='center' />" : "non" ?></td>
	<TD><?php print ($data[$i][14] == 1) ? "<img src='image/commun/valid.gif' align='center' />" : "non" ?></td>
	<TD><?php print (trim($data[$i][9]) == "") ? "&nbsp;" : $data[$i][9];?></td>
	<TD><?php print trim($data[$i][10])?></td>

<?php 
	if ($ue > 0) {
		$tab=recupNomUE($ue);
		$nom_ue=$tab[0][0];
		$ue=$tab[0][1];
		$nom_ueTitle=$nom_ue;
		$nom_ue=trunchaine($nom_ue,40);
		print "<TD>$nom_ue</TD>";
	}else{
		print "<TD>&nbsp;</TD>";
	}
?>
	<TD><?php if ($data[$i][12] == "etudedecasipac" ) print LANGTMESS471 ;  ?></td>
	<TD><?php print trim($data[$i][15])?></td>
	<TD><?php print trim($data[$i][17])?></td>
	<TD><?php print trim($data[$i][18])?></td>
	</tr>
<?php
	}
Pgclose();
?>
</table><BR>
<i><?php print LANGTMESS472 ?> / ** Visu 2: pour Config BTS Blanc et Pigier Partiel Paris </i>
<!-- // fin  -->
</td></tr></table>
</form>
<?php if (!isset($_GET["visu"])) {  ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php } ?>
<?php if (isset($_GET["imprime"])) {  ?>
<script>window.print();</script>
<?php } ?>



</BODY></HTML>
