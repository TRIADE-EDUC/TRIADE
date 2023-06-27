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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond2' >
<?php
include("./librairie_php/lib_licence.php");
        if (empty($_SESSION["adminplus"])) {
                print "<script>";
                print "location.href='./base_de_donne_key.php'";
                print "</script>";
                exit;
       }
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$cdata=chercheClasse($_POST["saisie_classe_envoi"]);
$cid=$cdata[0][0];
$cnom=trim($cdata[0][1]);
$tri=$_POST["saisie_tri"];
$anneeScolaire=$_POST["anneeScolaire"];


// affectation
if(createAffectation($cdata)):
	alertJs(LANGPER32." $cnom ".LANGPER23bis);
	if ($_POST["suppnote"] == "oui") {
		vide_notes_classe($_POST["saisie_classe_envoi"],$_POST["anneeScolaire"]);
		history_cmd($_SESSION["nom"],"MODIF","Affectation - note supprime - $cnom - $anneeScolaire ");
	}else{
		history_cmd($_SESSION["nom"],"MODIF","Affectation - note non supprime - $cnom - $anneeScolaire ");
	}
else:
	alertJs(LANGPER32." $cnom ".LANGPER32bis);
	//error(0);
endif;
// affichage
$sql=<<<SQL
SELECT
	CONCAT(trim(m.libelle),' ',trim(m.sous_matiere)),
	CONCAT(upper(trim(p.nom)),' ',trim(p.prenom)),
	a.coef,
	trim(g.libelle),
	langue,
	visubull,
	visubullbtsblanc,
	nb_heure,
	ects,
	id_ue_detail,
	specif_etat,
	num_semestre_info,
	coef_certif,
	note_planche
FROM
	${prefixe}matieres m,${prefixe}personnel p,${prefixe}affectations a,${prefixe}groupes g
WHERE
	a.code_matiere = m.code_mat
AND a.code_prof = p.pers_id
AND a.code_groupe = g.group_id
AND p.type_pers = 'ENS'
AND a.code_classe = '$cid'
AND a.trim = '$tri'
AND a.annee_scolaire = '$anneeScolaire'
ORDER BY
	a.ordre_affichage
SQL;
$curs=execSql($sql);
$data=chargeMat($curs);
freeResult($curs);
?>

<table border="0" cellpadding="3" cellspacing="1" bgcolor="#0B3A0C" width=90%  align=center>
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE20?>&nbsp;<font  color="#99CCFF"><?php print $cnom?></font> / <?php print LANGBULL3 ?> : </font>
<font  color="#99CCFF"><?php print $anneeScolaire ?></font>

</b></td></tr>
<tr id='cadreCentral0'>
     <td >
	<br />
	<ul>
	<?php print LANGPER22?> :
	<a href="#" onclick="print_affectation()">
	<img src="./image/print.gif" alt="Imprimer" align="center" border="0" /></A>
	</ul>

     <!-- //  debut -->
	<table border="0" bgcolor="#ffffff" width="100%" style="border-collapse: collapse;" >
	<TR>
		<TD>
		<TABLE border="1"  width="100%">
		<tr bgcolor='yellow' >
			<!--
			<td align="center">
				Nb
			</td>
			-->
			<TD align="center">
			<?php print LANGPER17?>
			</TD>
			<TD align="center">
			<?php print LANGPER18?>
			</TD>
			<TD align="center"
			>&nbsp;&nbsp;<?php print LANGPER19?>&nbsp;&nbsp;
			</TD>
			<TD align="center">
			&nbsp;&nbsp;<?php print LANGPER20?>&nbsp;&nbsp;
			</TD>
			<TD align="center">
			<?php print LANGPER21?>
			</TD>
			<TD align="center">
			<?php print "Visu."?>
			</TD>
			<TD align="center">
			<?php print "Visu.&nbsp;BTS&nbsp;Blanc"?>
			</TD>
			<TD align="center">
			<?php print "Nb&nbsp;d'heure"?>
			</TD>
			<TD align="center">
			<?php print "ECTS"?>
			</TD>
			<TD align="center">
			<?php print "Unités&nbsp;Enseignements"?>
			</TD>
			<TD align="center">
			<?php print "Spécif." ?>
			</TD>
			<TD align="center">
			<?php print "Info Sem." ?>
			</TD>
			<TD align="center">
			<?php print "Coef Certif." ?>
			</TD>
			<TD align="center">
			<?php print "Note plancher" ?>
			</TD>

		</tr>
		<!-- ici résultat -->
		<?php
		htmlTrMatAffec($data);
		?>
		<!-- fin résultat -->
		</TD>
	</TR>
	</TABLE>
     <!-- // fin  -->
    </td>
</tr>
</table>
<br />
<center>
<input type=button value="<?php print LANGFERMERFEN?>" onclick="parent.window.close()" class='BUTTON' >
</center>
<br />
<?php Pgclose() ?>
</BODY>
</HTML>
