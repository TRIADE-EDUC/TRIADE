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
$cnx=cnx();
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF">Absence - Retard -  de la classe  <?php print chercheClasse_nom($_POST["saisie_classe"]) ?>
   &nbsp;du  <?php print $_POST["saisie_date_debut"]  ?> au <?php print $_POST["saisie_date_fin"]  ?> </font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
     <!-- // fin  -->
<?php
// affichage de la liste d élèves trouvées
$idclasse=$_POST["saisie_classe"];

$sql=<<<EOF

SELECT nom,prenom,classe,elev_id,telephone,tel_prof_pere,tel_prof_mere
FROM ${prefixe}eleves
WHERE classe='$idclasse'
ORDER BY nom,prenom
EOF;
$res=execSql($sql);
$data=chargeMat($res);

if (count($data) <= 0) {
        print("<BR><center>".LANGABS67."<BR><BR></center>");
} else {
?>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TD bgcolor=#FFFFFF width=20%> <B>Nom</B></TD>
<TD bgcolor=#FFFFFF width=20%><b>Prénom</B></TD>
<TD bgcolor=#FFFFFF width=30%><b>Abs/Rtd</b></TD>
<TD bgcolor=#FFFFFF  align=center ><B>Détail</b></TD>
<?php
for($i=0;$i<count($data);$i++)
        {
	$data_2=affRetard_via_date($data[$i][3],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
	// $data : tab bidim - soustab 3 champ
	// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier,heure_saisie,creneaux
	for($j=0;$j<count($data_2);$j++)
        	{
			$idmatiere=$data_2[$j][7];
			list($creneau,$hC,$fC)=preg_split('/#/',$data_2[$j][10]);
			$date_retard=dateForm($data_2[$j][2]);
			if ($idmatiere != null) {
				$nomMatiere=chercheMatiereNom($idmatiere);
			}
			$classe=chercheClasse($data[$i][2]);
			$dataRattrapage=recupRattrappage($data_2[$j][11]); // date,heure_depart,duree,valider
			$infoRattrapage="";
			for($k=0;$k<count($dataRattrapage);$k++) {
				$rattragefait=($dataRattrapage[$k][3] == 1) ? LANGOUI : LANGNON;
				$infoRattrapage.=dateForm($dataRattrapage[$k][0])." à ".timeForm($dataRattrapage[$k][1])." durant ".timeForm($dataRattrapage[$k][2])." <i>Effectuer : $rattragefait</i><br>";
			}
			$etude="";
			if ($idmatiere < 0) { $etude=" / Etude "; }
			$duree=$data_2[$j][5];
			if ($data_2[$j][5]  == 0 ) { $duree="???"; }
			if ($data_2[$j][8]  == 1 ) { $justifier="(justifié)"; }else{ $justifier="(non justifié)"; }
?>
	<TR>
	<td bgcolor='#FFFFFF' valign=top><?php print ucwords($data[$i][0])?></td>
	<td bgcolor='#FFFFFF'  valign=top><?php print ucwords($data[$i][1])?></td>
	<td bgcolor='#FFFFFF' valign=top>En retard <?php print $etude ?> le <?php print $date_retard?> / <?php print "créneau : $creneau ($hC - $fC)" ?> - <i><?php print $justifier ?></i> <br> Matière : <?php print $nomMatiere?> <br></td>
	<?php $motiftext=$data_2[$j][6]; if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } if ($data_2[$j][6] == "0") { $motiftext=LANGINCONNU; }?>
	<td bgcolor='#FFFFFF' valign=top > - Motif : <?php print $motiftext ?>  <br>   - Durée : <?php print $duree ?> <br> - Saisie par : <?php print $data_2[$j][4] ?> le <?php print dateForm($data_2[$j][3])?><br>- Rattrapage : <?php print $infoRattrapage  ?></td>
	</TR>

<?php

        }
	$data_3=affAbsence2_via_date($data[$i][3],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
	// $data : tab bidim - soustab 3 champs
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier,creneaux,idrattrapage
	for($j=0;$j<count($data_3);$j++) {
		$classe=chercheClasse($data[$i][2]);
		$dataRattrapage=recupRattrappage($data_3[$j][12]); // date,heure_depart,duree,valider
		$infoRattrapage="";
		for($k=0;$k<count($dataRattrapage);$k++) {
			$rattragefait=($dataRattrapage[$k][3] == 1) ? LANGOUI : LANGNON;
			$infoRattrapage.=dateForm($dataRattrapage[$k][0])." à ".timeForm($dataRattrapage[$k][1])." durant ".timeForm($dataRattrapage[$k][2])." <i>Effectuer : $rattragefait</i><br>";
		}
		list($creneau,$hC,$fC)=preg_split('/#/',$data_3[$j][11]);
		$nomMatiere=chercheMatiereNom($data_3[$j][8]);
		if ($data_3[$j][10]  == 1 ) { $justifier="(justifié)"; }else{ $justifier="(non justifié)"; }
?>
	<TR>
	<td bgcolor='#FFFFFF' valign=top><?php print ucwords($data[$i][0])?></td>
	<td bgcolor='#FFFFFF' valign=top><?php print ucwords($data[$i][1])?></td>
	<td bgcolor='#FFFFFF' valign=top>Absent(e) -  <i><?php print $justifier ?></i><br>Matière : <?php print $nomMatiere?> <BR> du <?php print dateForm($data_3[$j][1])?> / <?php print "créneau : $creneau ($hC - $fC)" ?>
	 
<?php
		$duree=$data_3[$j][4]." Jour(s)";
		if ($data_3[$j][4]  == 0 ) { $duree="???"; }
		if ($data_3[$j][4] == -1) { $duree=$data_3[$j][7]."H" ; }
		
	?> </td>
	<?php $motiftext=$data_3[$j][6];  if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }  if (trim($data_3[$j][6]) == "0") { $motiftext=LANGINCONNU; }?>
	<td bgcolor='#FFFFFF' valign=top >- Motif : <?php print $motiftext ?>  <br>   - Durée : <?php print $duree ?> <br> - Saisie par : <?php print $data_3[$j][3] ?> le <?php print dateForm($data_3[$j][2])?><br>- Rattrapage : <?php print $infoRattrapage  ?></td>
	</TR>

<?php
		}
        }
print "</table>";
}
?>
<BR>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
