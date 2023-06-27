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
if ( (empty($_POST["anneeScolaireSource"])) || (empty($_POST["saisie_classe_source"])) || (empty($_POST["saisie_classe_destination"])) || (empty($_POST["anneeScolaireDest"]))  ) {
        header("Location:affectation_creation.php?errorcopy");
        exit();
}
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
	print "<script>";
        print "location.href='./affectation_creation_key.php'";
        print "</script>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE17?> <font id="color2"><?php print $cnom?></font></font></b>
<font   id='menumodule1' >pour l'ann&eacute;e scolaire </font><font id="color2"><?php print $_POST["anneeScolaireDest"] ?></font></b>
</td></tr>
<tr id='cadreCentral0' >
<td>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]); 
if (!empty($_SESSION["adminplus"])) {
	$cnx=cnx();
	verif_table_groupe();
	verif_table_matiere();
	$anneeScolaireDest=$_POST["anneeScolaireDest"];
	$anneeScolaireSource=$_POST["anneeScolaireSource"];
	$cdata=chercheClasse($_POST["saisie_classe_destination"]);
	$cidDest=$cdata[0][0];
	$cnom=trim($cdata[0][1]);

	$source=chercheClasse($_POST["saisie_classe_source"]);
	$cnomSource=trim($source[0][1]);
	$cidSource=$source[0][0];

	if (($cidSource == $cidDest) && ($anneeScolaireSource == $anneeScolaireDest)) {
		print "<br><center><font id='color3' class='T2 shadow'>ERREUR : vous ne pouvez copier une classe sur elle même.</font><br>"; 
	}else{
		createAffectationCopy($cidSource,$anneeScolaireSource,$cidDest,$anneeScolaireDest);
		history_cmd($_SESSION["nom"],LANGaffec_cre31." COPY de $cnomSource","$cnom");

		// la fonction initcap n'existe pas en MySQL (sous cette forme en tout cas)
		// CONCAT remplace ||
		$sql="SELECT a.trim,CONCAT(trim(m.libelle),' ',trim(m.sous_matiere)),CONCAT(upper(trim(p.nom)),' ',trim(p.prenom)),a.coef,trim(g.libelle),langue,visubull,nb_heure,ects FROM ${prefixe}matieres m,${prefixe}personnel p,${prefixe}affectations a,${prefixe}groupes g WHERE a.code_matiere = m.code_mat  AND a.code_prof = p.pers_id AND a.code_groupe = g.group_id AND p.type_pers = 'ENS' AND a.code_classe = '$cidDest' AND  a.annee_scolaire='$anneeScolaireDest' ORDER BY a.trim,a.ordre_affichage";
		//print $sql;
		$curs=execSql($sql);
		$data=chargeMat($curs);
		freeResult($curs);
	}
}
?>
<br />
	<ul>
	<font class='T2'>Création effectuée classe : <b><?php print $cnom ?></b></font><br><br>
	<?php print LANGPER22?> :
	<a href="#" onclick="print_affectation()">
	<img src="./image/print.gif" alt="<?php print LANGaffec_cre41?>" align="center" border="0" /></A>
	</ul>
     <!-- //  debut -->
	<table border="0" bgcolor="#ffffff" width="100%">
	<TR>
		<TD>
		<TABLE border="1"  width="100%" style="border-collapse: collapse;" >
		<tr>
			<td align="center" bgcolor='yellow' >
		  	   Trimestre 
			</td>
			<TD align="center"  bgcolor='yellow'  >
			<?php print LANGPER17?>
			</TD>
			<TD align="center"  bgcolor='yellow' >
			<?php print LANGPER18?>
			</TD>
			<TD align="center"  bgcolor='yellow' 
			>&nbsp;&nbsp;<?php print LANGPER19?>&nbsp;&nbsp;
			</TD>
			<TD align="center"  bgcolor='yellow' >
			&nbsp;&nbsp;<?php print LANGPER20?>&nbsp;&nbsp;
			</TD>
			<TD align="center"  bgcolor='yellow' >
			<?php print LANGPER21?>
			</TD>
			<TD align="center"  bgcolor='yellow' >
			<?php print "Visu"?>
			</TD>
			<TD align="center"  bgcolor='yellow' >
			<?php print "Nb d'heure"?>
			</TD>
			<TD align="center"  bgcolor='yellow'  >
			<?php print "ECTS"?>
			</TD>
	
		</tr>
		<!-- ici résultat -->
		<?php
		htmlTrMatCopy($data);
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
	<font class='T2 shadow'>
  	&nbsp;&nbsp;Utiliser le module "Modification" des affectations pour adpater vos données.
        </font>
<br />
<br />
<br />
<table align='center' ><tr>
<td><script language=JavaScript> buttonMagic("<?php print "Visualiser les affectations" ?>","affectation_visu.php","_parent","","")</script></td>
<td><script language=JavaScript> buttonMagic("<?php print "Création d'une affectation" ?>","affectation_creation.php","_parent","","")</script></td>
<td><script language=JavaScript> buttonMagic("<?php print "Modifier une affectation" ?>","modifaffect.php","_parent","","")</script></td>
</tr></table>
<br><br>
<?php Pgclose() ?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
