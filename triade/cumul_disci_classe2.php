<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF">
		<?php print "Cumul des sanctions "  ?> <?php print chercheClasse_nom($_POST["saisie_classe"]) ?>
   &nbsp;<?php print LANGAGENDA137 ?>  <?php print $_POST["saisie_date_debut"]  ?> <?php print LANGABS19 ?> <?php print $_POST["saisie_date_fin"]  ?> </font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
     <!-- // fin  -->
<?php
// affichage de la liste d élèves trouvées
$idclasse=$_POST["saisie_classe"];

$sql="SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere FROM ${prefixe}eleves  WHERE classe='$idclasse' ORDER BY nom,prenom ";
$res=execSql($sql);
$data=chargeMat($res);

if (count($data) <= 0) {
        print("<BR><center>".LANGABS67."<BR><BR></center>");
} else {
?>
	<table border="1" bordercolor="#000000" width="100%">
	<tr>
	<TD bgcolor=#FFFFFF width=20%><B><?php print LANGNA1 ?></B></TD>
	<TD bgcolor=#FFFFFF width=20%><b><?php print LANGNA2 ?></B></TD>
	<TD bgcolor=#FFFFFF width=20%><b><?php print "Nombre de sanction" ?></B></TD>
	<TD bgcolor=#FFFFFF width=20%><b><?php print "Nombre de retenue" ?></b></TD>
	<?php
	for($i=0;$i<count($data);$i++) {

		$cumulsanc=0;	
		$bgcolor="#FFFFFF";
		$cumulret=0;

		$data_2=affDiscipline_via_date($data[$i][3],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
		//id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire
		for($j=0;$j<count($data_2);$j++) {
				$bgcolor="#CCCCCC";
				$cumulsanc=$cumulsanc + 1 ;
		}
		// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire
		$data_3=affRetenue_via_date($data[$i][3],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
		for($j=0;$j<count($data_3);$j++) {
				$bgcolor="#CCCCCC";
				$cumulret=$cumulret + 1 ;
		}
?>
	<TR>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print ucwords($data[$i][0])?></td>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print ucwords($data[$i][1])?></td>
	<?php if ($cumulsanc > 0) { $bold="<b>"; $fbold="</b>" ; }else{ $bold=""; $fbold=""; } ?>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print "Nb:&nbsp;$bold ${cumulsanc} $fbold &nbsp;sanction(s)&nbsp;"; ?></td>
	<?php if ($cumulret > 0) { $bold="<b>"; $fbold="</b>" ; }else{ $bold=""; $fbold=""; } ?>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print "Nb:&nbsp;$bold ${cumulret} $fbold &nbsp;Retenue(s)" ?></td>
	</TR>
<?php
    }
print "</table><br>";
}
Pgclose();
?>
</BODY></HTML>
