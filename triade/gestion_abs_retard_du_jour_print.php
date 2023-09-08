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
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
$date_du_jour=dateForm($_GET["id"]);
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF"><?php print LANGABS35?>  <?php print $date_du_jour ?> </font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
     <!-- // fin  -->


<?php
// affichage de la liste d'élèves trouvées

if (isset($_GET["filtre"])) {
	$filtreCLasse=$_GET["filtre"];
}else{
	$filtreCLasse="tous";
}

$inconnu=$_GET["inconnu"];
$date=$_GET["id"];

$data=recup_abs_rtd_aucun($date);
// id,classe,date,heure,matiere
if (count($data) > 0) {
	print "<br><br><b>".LANGABS75." :</b> <br><br><ul>";
	for($j=0;$j<count($data);$j++) {
		print ucwords(LANGABS33)." ". $data[$j][1]." (".$data[$j][4].") ".LANGABS76." ".timeForm($data[$j][3])."<br>" ;
	}
	print "</ul><br><hr width=50%>";
}


?>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TD bgcolor=#FFFFFF width=15%> <B><?php print LANGNA1 ?></B><b><?php print LANGNA2 ?></B></TD>
<TD bgcolor=#FFFFFF width=10%><b><?php print LANGELE4 ?></B></TD>
<TD bgcolor=#FFFFFF width=30%><b><?php print LANGABS11 ?></b></TD>
<TD bgcolor=#FFFFFF  align=center width=20%><B><?php print LANGABS22 ?></b></TD>
<TD bgcolor=#FFFFFF  align=center width=20%><b>*<?php print LANGDISC17 ?></b></TD>
<?php

$data_2=affRetarddujour3($date);
//  elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie,  creneaux
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data_2);$j++) {
	if (($inconnu == "1") && ($data_2[$j][6] != "inconnu")) { continue; }
	$ideleve=$data_2[$j][0];
	$idmatiere=$data_2[$j][7];
	list($creneaux,$debcre,$fincre)=preg_split('/#/',$data_2[$j][10]);
	$etude="";
	if ($idmatiere < 0) { $etude="En Etude "; }
	if ($idmatiere != null) {
		$nomMatiere=chercheMatiereNom($idmatiere);
	}
	if ((strtolower($data_2[$j][6]) != "inconnu") && ($data_2[$j][5] != 0 ) ){
		$couleur="bgcolor='#FFFF99'";
	}
	$classe=chercheIdClasseDunEleve($ideleve);
	if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) {
		continue;
	}
	$classe=chercheClasse($classe);
	if ($data_2[$j][9] != "") {
		$heuresignale=timeForm($data_2[$j][9]);
	}else{
		$heuresignale="??:??";
	}

	if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }
	 
?>
	<TR>
	<td bgcolor='#FFFFFF' valign=top><?php print strtoupper(recherche_eleve_nom($ideleve)) ?> <?php print ucwords(recherche_eleve_prenom($ideleve))?></td>
	<td bgcolor='#FFFFFF' valign=top><?php print $classe[0][1]?></td>
	<td bgcolor='#FFFFFF' valign=top>En retard à <?php print timeForm($data_2[$j][1]) ?> le  <?php print dateForm($data_2[$j][2]); ?> <br><?php print "Créneau : $creneaux ($debcre - $fincre)" ?><br><i> Signalé le <?php print dateForm($data_2[$j][3])." - ".$heuresignale ?></i></td>
	<?php 
	$motiftext=$data_2[$j][6]; 
	if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } 
	if ($motiftext == "0") { $motiftext=LANGINCONNU; }
	?>
	<td bgcolor='#FFFFFF' valign=top ><?php print $motiftext ?></td>
	<td bgcolor='#FFFFFF' valign=top >
	D:<b><?php print  cherchetel($ideleve) ?></B><BR>
	Port1:<b><?php print  cherchetelportable1($ideleve) ?></B><BR>
	Port2:<b><?php print  cherchetelportable2($ideleve) ?></B><BR>
    	P:<b><?php print  cherchetelpere($ideleve) ?></b><BR>
   	M:<b><?php print cherchetelmere($ideleve)?></b>
	</td>
	</TR>

<?php } ?>

<?php


$data_3=affAbsence4($date);
//  elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere,heure_saisie,justifier,heuredabsence,creneaux
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data_3);$j++) {
	if ( ($inconnu == "1") && ($data_3[$j][6] != "inconnu") && ($data_3[$j][6] != "0") ) { continue; }
	//if (($inconnu == "1") && ($data_3[$j][6] != "inconnu") && ($data_3[$j][10] != "1")) { continue; }
	$couleur="class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"";
	$ideleve=$data_3[$j][0];
	$idmatiere=$data_3[$j][8];
	list($creneaux,$debcre,$fincre)=preg_split('/#/',$data_3[$j][12]);
	$nomMatiere=chercheMatiereNom($idmatiere);
	$heureabs=timeForm($data_3[$j][11]);
	$classe=chercheIdClasseDunEleve($data_3[$j][0]);
	if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) { continue; }
        $classe=chercheClasse($classe);
	if ((strtolower($data_3[$j][6]) != "inconnu")  && ($data_3[$j][4] != 0 ) ){ $couleur="bgcolor='#FFFF99'"; }
?>
	<TR>
	<td bgcolor='#FFFFFF' valign=top><?php print strtoupper(recherche_eleve_nom($ideleve)) ?> <?php print ucwords(recherche_eleve_prenom($ideleve))?></td>
	<td bgcolor='#FFFFFF' valign=top><?php print $classe[0][1]?></td>
	<td bgcolor='#FFFFFF' valign=top><?php print LANGABS42 ?> <?php 
		if ($data_3[$j][4] >= 0) {
			print dateForm($data_3[$j][1])?> <br />  A <?php print $heureabs ?> <?php print LANGABS43?> <?php
			if ($data_3[$j][4] == 0) {
				print "???";
			}else {
				print $data_3[$j][4];
				print " ".LANGABS44;
			}
		}else{
			print dateForm($data_3[$j][1])?> <br />  A <?php print $heureabs ?> <?php print LANGABS43?> <?php
			print  $data_3[$j][7];
			print "h";
		}
		if ($data_3[$j][9] != "") {
			$heuresignale=timeForm($data_3[$j][9]);
		}else{
			$heuresignale="??:??";
		}
		
?> 
	<br><?php print "Créneau : $creneaux ($debcre - $fincre)" ?>
	<br><i> Signalé le <?php print dateForm($data_3[$j][2])." - ".$heuresignale ?></i> 
	</td>
<?php 
	$motiftext=$data_3[$j][6]; 
	if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
	if ($motiftext == "0") { $motiftext=LANGINCONNU; } 
?>
	<td bgcolor='#FFFFFF' valign=top ><?php print $motiftext ?></td>
	<td bgcolor='#FFFFFF' valign=top >
	D:<b><?php print  cherchetel($ideleve) ?></B><BR>
	Port1:<b><?php print  cherchetelportable1($ideleve) ?></B><BR>
	Port2:<b><?php print  cherchetelportable2($ideleve) ?></B><BR>
    	P:<b><?php print  cherchetelpere($ideleve) ?></b><BR>
   	M:<b><?php print cherchetelmere($ideleve)?></b>
	</td>
	</TR>
<?php } ?>

</table><BR><BR>
<B>*<I> D</B>: Téléhone Domicile, <B>P</B>: Téléphone Profession Père, <B>M</B>: Téléphone Profession Mère</I>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<script language=Javascript>
window.print();
</script>
</BODY></HTML>
