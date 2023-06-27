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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd2.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body bgcolor="#FFFFFF" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
$cnx=cnx();

// affichage de la liste d'élèves trouvées
$date_du_jour=dateDMY2();
$afficbt="1";
?>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TD align=left bgcolor="yellow" >&nbsp;<?php print LANGEL1?>&nbsp;<?php print LANGEL2?></TD>
<TD align=left bgcolor="yellow" ><?php print LANGEL3?></TD>
<TD align=center colspan=2 bgcolor="yellow" ><?php print LANGABS11 ?></TD>
<TD align=left bgcolor="yellow"  ><?php print LANGABS12 ?></TD>
<TD align=center bgcolor="yellow"  ><?php print LANGABS77 ?></TD>
<TD align=center bgcolor="yellow"  ><?php print "Créneau" ?></TD>
<TD align=center bgcolor="yellow"  ><?php print "Tél." ?></TD>
<?php
$a=0;
if ($_GET["filtre"] != "") { 

	$data_2=affRetardNonJustifie22Limit($_GET["filtre"],$_GET["depart"],$_GET["nbaff"]);
	// $data : tab bidim - soustab 3 champs
	// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux
	for($j=0;$j<count($data_2);$j++) {
			$ideleve=$data_2[$j][0];
			if ($ideleve == "-4") { continue; }
			$classe=chercheIdClasseDunEleve($ideleve);
			$classe=chercheClasse($classe);
			$classe=preg_replace('/ /',"&nbsp;",$classe[0][1]);
?>
	<TR>
	<td id=bordure bgcolor="#CCCCCC" valign='top'  >&nbsp;<?php print strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve))) ?></td>
	<td id=bordure bgcolor="#A0A0A0" valign='top'  >&nbsp;<?php print ucwords($classe) ?>&nbsp;</td>
	<td align=center  bgcolor="#CCCCCC"  valign='top' id='bordure'>
	Rtd
	</td>
	<td  bgcolor="#A0A0A0" align='center' valign='top' id='bordure'>
	<?php print $dureee?>
	</td>
	<td bgcolor="#CCCCCC"  id='bordure' valign='top' >
	<?php $text=$data_2[$j][6]; $value=$data_2[$j][6];  if ( $data_2[$j][6] == "inconnu") { $text=LANGINCONNU; $value=0; } if ( trim($data_2[$j][6]) == "0") { $text=LANGINCONNU; $value=0; } ?>
	<?php print $text ?>
	</td>

	<td bgcolor="#A0A0A0" align='center' id='bordure' valign='top'  >
	<?php print dateForm($data_2[$j][2])?> <?php print timeForm($data_2[$j][1])?>
	</td>

	<td bgcolor="#CCCCCC" align='left' id='bordure' valign='top'  >
	<?php list($cre,$dC,$fC)=preg_split('/#/',$data_2[$j][10]);
		print $cre." (".timeForm($dC)."-".timeForm($fC).") ";
	?>
	<?php $a++; ?>
	</td>

	<td bgcolor="#A0A0A0" valign="top"  align='center' id='bordure' valign='top'  >
	<?php 
	$telportable1=cherchetelportable1($ideleve);
	$telportable2=cherchetelportable2($ideleve);
	$tel=cherchetel($ideleve);
	$telprofP=cherchetelpere($ideleve);
	$telprofM=cherchetelmere($ideleve);
?>
	<table border='1' width='100%' bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" >
	<tr><td colspan=2>Tél. : <?php print $tel ?></td></tr>
	<tr><td>Tél. Tuteur 1 : <?php print $telportable1 ?></td>
	    <td>Tél. Tuteur 2 : <?php print $telportable2 ?></td></tr>
	<tr><td>Tél. Prof. Pére : <?php print $telprofP ?></td>
	    <td>Tél. Prof. Mére : <?php print $telprofM ?></td></tr>
	</table>

	</td>

	</TR>
	<tr><td colspan="8" id=bordure ><hr></td></tr>
<?php
}  } 
print "</table>";
Pgclose();
?>
<script language=Javascript>
window.print();
</script>
</BODY></HTML>
