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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond2' >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);
if (!empty($_SESSION["adminplus"])) {
	$cnx=cnx();
	verif_table_groupe();
	verif_table_matiere();
	$tri=$_POST['saisie_tri'];
	$anneeScolaire=$_POST["anneeScolaire"];
	$cdata=chercheClasse($_POST["saisie_classe_envoi"]);
	$cid=$cdata[0][0];
	$cnom=trim($cdata[0][1]);
	if(createAffectation($cdata)):
		alertJs(LANGPER23." $cnom ".LANGPER23bis);
		history_cmd($_SESSION["nom"],LANGaffec_cre31,"$cnom");
	else:
		alertJs( LANGPER23." $cnom ".LANGPER24 );
	endif;
	// la fonction initcap n'existe pas en MySQL (sous cette forme en tout cas)
	// CONCAT remplace ||
	$sql="SELECT CONCAT(trim(m.libelle),' ',trim(m.sous_matiere)),CONCAT(upper(trim(p.nom)),' ',trim(p.prenom)),a.coef,trim(g.libelle),langue,visubull,visubullbtsblanc,nb_heure,ects,num_semestre_info,coef_certif,note_planche  FROM ${prefixe}matieres m,${prefixe}personnel p,${prefixe}affectations a,${prefixe}groupes g WHERE a.code_matiere = m.code_mat  AND a.code_prof = p.pers_id AND a.code_groupe = g.group_id AND p.type_pers = 'ENS' AND a.code_classe = '$cid' AND trim = '$tri' AND a.annee_scolaire='$anneeScolaire' ORDER BY a.ordre_affichage";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	freeResult($curs);
}
?>
<table border="0" cellpadding="3" cellspacing="1" bgcolor="#0B3A0C" width=70%  align=center>
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE17?> <font id="color2"><?php print $cnom?></font></font></b>
<font   id='menumodule1' >pour l'ann&eacute;e scolaire </font><font id="color2"><?php print $anneeScolaire ?></font></b>
</td></tr>
<tr id='cadreCentral0' >
     <td >
	<br />
	<ul>
	<?php print LANGPER22?> :
	<a href="#" onclick="print_affectation()">
	<img src="./image/print.gif" alt="<?php print LANGaffec_cre41?>" align="center" border="0" /></A>
	</ul>

     <!-- //  debut -->
	<table border="0" bgcolor="#ffffff" width="100%" style='border-collapse: collapse;' >
	<TR>
		<TD>
		<TABLE border="1"  width="100%">
		<tr>
			<!--
			<td align="center">
				Nb
			</td>
			-->
			<TD align="center" bgcolor='yellow' >
			<?php print LANGPER17?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print LANGPER18?>
			</TD>
			<TD align="center"
			 bgcolor='yellow' >&nbsp;&nbsp;<?php print LANGPER19?>&nbsp;&nbsp;
			</TD>
			<TD align="center" bgcolor='yellow' >
			&nbsp;&nbsp;<?php print LANGPER20?>&nbsp;&nbsp;
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print LANGPER21?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "Visu"?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "Visu BTS Blanc"?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "Nb d'heure"?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "ECTS"?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "Info Sem."?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "Coef Certif"?>
			</TD>
			<TD align="center" bgcolor='yellow' >
			<?php print "Note Plancher"?>
			</TD>
	
		</tr>
		<!-- ici résultat -->
		<?php
		htmlTrMat($data);
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
<input type=button value="<?php print LANGFERMERFEN?>"  class="bouton2" onClick="parent.window.close()" />
</center>
<br />
<?php Pgclose() ?>
</BODY>
</HTML>
