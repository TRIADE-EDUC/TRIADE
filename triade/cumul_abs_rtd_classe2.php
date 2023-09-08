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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF"><?php print LANGABS73 ?> <?php print chercheClasse_nom($_POST["saisie_classe"]) ?>
   &nbsp;<?php print LANGAGENDA137 ?>  <?php print $_POST["saisie_date_debut"]  ?> <?php print LANGABS19 ?> <?php print $_POST["saisie_date_fin"]  ?> </font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
     <!-- // fin  -->
<?php
// affichage de la liste d élèves trouvées
$idclasse=$_POST["saisie_classe"];

if ($idclasse == "tous") {
	$sql="SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere FROM ${prefixe}eleves ORDER BY nom,prenom ";
}else{
	$sql="SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere FROM ${prefixe}eleves  WHERE classe='$idclasse' ORDER BY nom,prenom ";
}

$res=execSql($sql);
$data=chargeMat($res);

if (count($data) <= 0) {
        print("<BR><center>".LANGABS67."<BR><BR></center>");
} else {
?>
	<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;" >
	<tr>
	<TD bgcolor=yellow width=20%><B><?php print LANGNA1 ?></B></TD>
	<TD bgcolor=yellow width=20%><b><?php print LANGNA2 ?></B></TD>
	<TD bgcolor=yellow width=20%><b><?php print LANGABS71 ?></b></TD>
	<TD bgcolor=yellow width=20%><b><?php print LANGABS72 ?></b></TD>
	<?php
	for($i=0;$i<count($data);$i++) {

		$nbabs=0;
		$nbrtd=0;
		$cumulabs=0;
		$cumulrtd=0;
		$bgcolor="#FFFFFF";
		$cumulabsheure=0;

		$data_2=affRetard_via_date($data[$i][3],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
		// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere
		for($j=0;$j<count($data_2);$j++) {
				$bgcolor="#CCCCCC";
				$nbminute=preg_replace('/mn/','',$data_2[$j][5]);
				if (preg_match('/[0-9]h/',$data_2[$j][5])) {
						$minute=0;
						list($heure,$minute) =preg_split('/h/', $data_2[$j][5], 2 );
						$nbminute=$heure * 60 + $minute;
				}
				$cumulrtd=$cumulrtd + $nbminute ;

				$nbrtd++;
				if ($cumulrtd == 0 ) { $cumulrtd="<font color=green><b>???</b></font>"; }
		}

		$data_3=affAbsence2_via_date($data[$i][3],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
		// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier,creneaux
		for($j=0;$j<count($data_3);$j++) {
				$bgcolor="#CCCCCC";
				if ($data_3[$j][4] == -1) { 
					$cumulabsheure+=$data_3[$j][7]; 
				}else {
					$cumulabs=$cumulabs + $data_3[$j][4];
				}	
				$nbabs++;
				// if ($cumulabs == 0 ) { $cumulabs="<font color=green><b>???</b></font>"; }
		}

?>
	<TR>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print ucwords($data[$i][0])?></td>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print ucwords($data[$i][1])?></td>
	<?php if ($nbabs > 0) { $bold="<b>"; $fbold="</b>" ; }else{ $bold=""; $fbold=""; } ?>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print "Nb:&nbsp;$bold ${nbabs} $fbold &nbsp;abs&nbsp;/&nbsp;Cumul&nbsp;:&nbsp;$bold ${cumulabs} $fbold &nbsp; Jour(s) /  $cumulabsheure Heure(s)"?></td>
	<?php if ($nbrtd > 0) { $bold="<b>"; $fbold="</b>" ; }else{ $bold=""; $fbold=""; } ?>
	<td bgcolor='<?php print $bgcolor?>' valign=top><?php print "Nb:&nbsp;$bold ${nbrtd} $fbold &nbsp;rtd&nbsp;/&nbsp;Cumul&nbsp;:&nbsp;$bold ${cumulrtd} $fbold &nbsp; minute(s)"?></td>
	</TR>
<?php
    }
print "</table><br>";
}
Pgclose();
?>
</BODY></HTML>
