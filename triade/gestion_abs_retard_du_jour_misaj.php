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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$date=$_GET["date"];
validerequete("2");
$cnx=cnx();
$taille="width='100%' height='100%'"; 
$nbcar=30;
?>
<?php if ($_GET["visu"] != "all") { ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<?php
	$nbcar=10;
	$taille="width='100%'"; 
} ?>
	<table border="0" cellpadding="3" cellspacing="1" <?php print $taille ?> bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2">
	<table border=0 cellpadding="0" cellspacing="0" width="100%">
	<tr><td width="100%"><b><font   id='menumodule1' ><?php print LANGABS35 ?> <?php print dateForm($date)?> </font></b></td>
	<td align='right'  >
	<?php if ($_GET["visu"] != "all") { ?>
	<a href="gestion_abs_retard_du_jour.php" onclick="open('gestion_abs_retard_du_jour_misaj.php?date=<?php print $_GET["date"]?>&filtre=<?php print $_GET["filtre"] ?>&visu=all','visu','width=800,height=600,resizable=yes,scrollbars=yes');" alt='agrandir'><img src="./image/commun/fen.gif" title="agrandir" border="0" /></a>&nbsp;</td></tr>
	<?php } ?>
	</table>
</td></tr><tr id='cadreCentral0' ><td valign=top >
<!-- // fin  -->
<form method=post name=formulaire action="gestion_abs_retard_du_jour_misaj2.php" >
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;" >
<tr>
<?php
$size="15";
if ($_GET["visu"] == "all") {
	$size="35";
}
?>
<TD align=center bgcolor=yellow width=5%><?php print LANGNA1?>&nbsp;<?php print LANGNA2?></TD>
<TD align=center colspan=2 bgcolor=yellow ><?php print LANGABS20?></TD>
<TD align=center bgcolor=yellow width=240 ><?php print LANGABS22?> / <?php print LANGRTDJUS." <i>(".LANGOUI.")</i>" ?></TD>
<TD align=center bgcolor=yellow  ><?php print LANGABS46?></TD>
<?php
if (isset($_GET["filtre"])) {
	$filtreCLasse=$_GET["filtre"];
}else{
	$filtreCLasse="tous";
}	
$nb=0;
$i=0;
	$data_2=affRetarddujour2($date);
	// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier, heure_saisie,idprof,idrattrapage,smsenvoye
	// $data : tab bidim - soustab 3 champs
	for($j=0;$j<count($data_2);$j++) {
                $ideleve=$data_2[$j][0];
		$idmatiere=$data_2[$j][7];
		$idrattrapage=$data_2[$j][11];
                if ($idmatiere != null) {
                        $nomMatiere=chercheMatiereNom($idmatiere);
                }
		$classe=chercheIdClasseDunEleve($ideleve);
		if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) {
			continue;
		}
                $classe=chercheClasse($classe);
		$nb++;
		$i++;

		if ($data_2[$j][12] == '1') { $imgsms="<br><img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }


		$valeurHeure1=$valeurDate1=$valeurDuree1=$valeurHeure2="";
		$valeurDate2=$valeurDuree2=$valeurHeure3=$valeurDate3="";
		$valeurDuree3="";


		$dataRattra=recupRattrappage($idrattrapage); //date,heure_depart,duree
		for($P=0;$P<count($dataRattra);$P++) {
			if ($P == 0) {
				$valeurHeure1=timeForm($dataRattra[$P][1]);
				$valeurDate1=dateForm($dataRattra[$P][0]);
				$valeurDuree1=timeForm($dataRattra[$P][2]);
			}
			if ($P == 1) {
				$valeurHeure2=timeForm($dataRattra[$P][1]);
				$valeurDate2=dateForm($dataRattra[$P][0]);
				$valeurDuree2=timeForm($dataRattra[$P][2]);
			}
			if ($P == 2) {
				$valeurHeure3=timeForm($dataRattra[$P][1]);
				$valeurDate3=dateForm($dataRattra[$P][0]);
				$valeurDuree3=timeForm($dataRattra[$P][2]);
			}
		}

	?>
	<TR id="tr<?php print $j ?>" class="tabnormal2" onmouseover="this.className='tabover';" onmouseout="this.className='tabnormal2'" >
	<td valign="top" id='bordure' ><?php infoBulleEleveSansLoupe($ideleve,strtoupper(recherche_eleve_nom($ideleve))."&nbsp;".ucwords(strtolower(recherche_eleve_prenom($ideleve))))  ?><br>
	<font color='blue'><?php print $classe[0][1] ?></font>
	</td>
	<td align=center   valign="top" bgcolor="#FFFFFF" id='bordure' >
	<?php $val="'".$i."','".dateHI()."','".dateForm($_GET["date"])."'"; ?>
	<select name="saisie_<?php print $i?>" onChange="abs2(<?php print $val?>)">
	<option value="retard" STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRTD?></option>
	<option value="absent" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS?></option>
	<option value="100" STYLE='color:#000066;background-color:yellow'><?php print LANGEDIT20bis ?></option>
	</select></td>
	<td  bgcolor="#FFFFFF" valign="top" align=center id='bordure' >
	<select name="saisie_duree_<?php print $i?>" >
	<?php
	$dureee=$data_2[$j][5];
	if ($data_2[$j][5] == 0) { $dureee="???"; $dureevaleur="value='0'";  }else{ unset($dureevaleur);  }
	?>
	<option STYLE='color:#000066;background-color:#CCCCFF' <?php print $dureevaleur?> ><?php print $dureee?></option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>5 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>10 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>15 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>20 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>25 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>30 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>35 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>45 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h15</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h45</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2h</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2h30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>3h</option>
	</select></td>
	<td id='bordure' valign="top">
	<table border='0' cellpadding='0' cellspacing='0' ><tr><td valign='top'>
	<?php 
	$motiftext=$data_2[$j][6];
	$value=$data_2[$j][6] ; 
	if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; $value=0; } 
	if (trim($data_2[$j][6]) == "0") { $motiftext=LANGINCONNU; $value=0; }
	if ($_SESSION['widthfen'] >= 1020) $nbcar=30;
	?>
	<select name="saisie_motifs_<?php print $i?>" onChange="motifabsretad('<?php print $i?>',this.value)" >
	<option value="<?php print $value?>"  STYLE="color:#000066;background-color:#FCE4BA" ><?php print trunchaine($motiftext,$nbcar) ?></option>
	<?php affSelecMotif() ?>
	<option value="1" STYLE='color:#000066;background-color:#CCCCFF' ><?php print "autre" ?></option>
	</select><input type="checkbox" name="saisie_justifier_<?php print $i?>" value="1" onClick="DisplayLigne('tr<?php print $j ?>');" title="Justifier" />
	&nbsp;[<a href="#" onclick="AffBulleAvecQuit('Information rattrapage','image/commun/info.jpg','Pour le <input  size=12 type=text title=\'jj/mm/aaaa\' id=r_d_1<?php print $i?> onblur=document.getElementById(\'rattra_date_1<?php print $i?>\').value=this.value;  onKeyPress=\'onlyChar(event)\' /> à <input id=r_h_1<?php print $i?> type=text size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_heure_1<?php print $i?>\').value=this.value; title=\'hh:mm\' /> durant <input id=r_duree_1<?php print $i?> type=text  size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_duree_1<?php print $i?>\').value=this.value; title=\'hh:mm\' /><br />Pour le <input  size=12 type=text title=\'jj/mm/aaaa\' id=r_d_2<?php print $i?> onblur=document.getElementById(\'rattra_date_2<?php print $i?>\').value=this.value;  onKeyPress=\'onlyChar(event)\' /> à <input id=r_h_2<?php print $i?> type=text size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_heure_2<?php print $i?>\').value=this.value; title=\'hh:mm\' /> durant <input id=r_duree_2<?php print $i?> type=text  size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_duree_2<?php print $i?>\').value=this.value; title=\'hh:mm\' /> <br />Pour le <input  size=12 type=text title=\'jj/mm/aaaa\' id=r_d_3<?php print $i?> onblur=document.getElementById(\'rattra_date_3<?php print $i?>\').value=this.value;  onKeyPress=\'onlyChar(event)\' /> à <input id=r_h_3<?php print $i?> type=text size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_heure_3<?php print $i?>\').value=this.value; title=\'hh:mm\' /> durant <input id=r_duree_3<?php print $i?> type=text  size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_duree_3<?php print $i?>\').value=this.value; title=\'hh:mm\' /> <br /><i>La durée et le motif doivent être indiquées pour valider le rattrapage.</i>'); 

document.getElementById('r_h_1<?php print $i?>').value=document.getElementById('rattra_heure_1<?php print $i?>').value; 
document.getElementById('r_d_1<?php print $i?>').value=document.getElementById('rattra_date_1<?php print $i?>').value; 
document.getElementById('r_duree_1<?php print $i?>').value=document.getElementById('rattra_duree_1<?php print $i?>').value; 
document.getElementById('r_h_2<?php print $i?>').value=document.getElementById('rattra_heure_2<?php print $i?>').value; 
document.getElementById('r_d_2<?php print $i?>').value=document.getElementById('rattra_date_2<?php print $i?>').value; 
document.getElementById('r_duree_2<?php print $i?>').value=document.getElementById('rattra_duree_2<?php print $i?>').value; 
document.getElementById('r_h_3<?php print $i?>').value=document.getElementById('rattra_heure_3<?php print $i?>').value; 
document.getElementById('r_d_3<?php print $i?>').value=document.getElementById('rattra_date_3<?php print $i?>').value; 
document.getElementById('r_duree_3<?php print $i?>').value=document.getElementById('rattra_duree_3<?php print $i?>').value; 

return false; " >rattrapage</a>]&nbsp;</td><td valign='top' ><?php print $imgsms ?></td></tr></table>
	<input type=hidden name="saisie_motif_<?php print $i?>" value="<?php print $motiftext ?>" size=10 id="saisie_motif_<?php print $i?>" >
	</td>
	<td  align=center id='bordure' valign="top" >
	<input type=text size=10  name="saisie_heure_<?php print $i?>" value="<?php print $data_2[$j][1]?>" onblur="verifHeure2(this.value,'document.formulaire.saisie_heure_<?php print $i?>','document.formulaire.saisie_<?php print $i?>')" onKeyPress="onlyChar2(event)" ><br />

	<input type=hidden name="saisie_pers_<?php print $i?>" value="<?php print $data_2[$j][0]?>">
	<input type=hidden name="id_i[]" value="<?php print $i?>">
	<input type=hidden name="base_depart[]" value="retard">
	<!--  elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif -->
	<input type=hidden name=departheurertd[]  readonly value="<?php print $data_2[$j][1]?>">
	<input type=hidden name=departdatesaisie[]  readonly value="<?php print $data_2[$j][3]?>">
	<input type=hidden name=heuredatesaisie[]  readonly value="<?php print $data_2[$j][9]?>">
    	<input type=hidden name=departdate[]  readonly value="<?php print $data_2[$j][2]?>">
    	<input type=hidden name=idprof[]  readonly value="<?php print $data_2[$j][10]?>">
	<input type=hidden  name="heurederetard_<?php print $i?>"  readonly value="<?php print $data_2[$j][1]?>">
	<input type=hidden name="time_<?php print $i?>"  readonly value="rien">
	<input type=hidden name="matiere_<?php print $i?>"  readonly value="<?php print $idmatiere ?>">
	<input type=hidden name="saisie_heure_2<?php print $i?>"  readonly='readonly' value="<?php print $data_2[$j][1]?>" >


	<input type=hidden name="idrattrapage<?php print $i?>"  readonly='readonly' value="<?php print $data_2[$j][11]?>" >

	<input type=hidden name="rattra_heure_1<?php print $i?>" readonly id="rattra_heure_1<?php print $i?>" value='<?php print $valeurHeure1 ?>' >
	<input type=hidden name="rattra_date_1<?php print $i?>" readonly id="rattra_date_1<?php print $i?>"   value='<?php print $valeurDate1 ?>'  >
	<input type=hidden name="rattra_duree_1<?php print $i?>" readonly id="rattra_duree_1<?php print $i?>" value='<?php print $valeurDuree1 ?>' >

	<input type=hidden name="rattra_heure_2<?php print $i?>" readonly id="rattra_heure_2<?php print $i?>" value='<?php print $valeurHeure2 ?>' >
	<input type=hidden name="rattra_date_2<?php print $i?>" readonly id="rattra_date_2<?php print $i?>"   value='<?php print $valeurDate2 ?>' >
	<input type=hidden name="rattra_duree_2<?php print $i?>" readonly id="rattra_duree_2<?php print $i?>" value='<?php print $valeurDuree2 ?>' >

	<input type=hidden name="rattra_heure_3<?php print $i?>" readonly id="rattra_heure_3<?php print $i?>" value='<?php print $valeurHeure3 ?>' >
	<input type=hidden name="rattra_date_3<?php print $i?>" readonly id="rattra_date_3<?php print $i?>"   value='<?php print $valeurDate3 ?>' >
	<input type=hidden name="rattra_duree_3<?php print $i?>" readonly id="rattra_duree_3<?php print $i?>" value='<?php print $valeurDuree3 ?>' >

	</td>
	</TR>
<?php
	}
	print "<tr><td colspan='6' bgcolor='#CCCCCC' heigth='5'>&nbsp;</td></tr>";
	$data_3=affAbsence3($date);
	//$data : tab bidim - soustab 3 champs
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie, idprof, heuredabsence, idrattrapage, smsenvoye
	for($j=0;$j<count($data_3);$j++){
		$ideleve=$data_3[$j][0];
		$classe=chercheIdClasseDunEleve($ideleve);
		$idrattrapage=$data_3[$j][14];
		$heureabs=$data_3[$j][13];
		if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) {
			continue;
		}
                $classe=chercheClasse($classe);
		$nb++;
		$i++;

		if ($data_2[$j][12] == '1') { $imgsms="<br><img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }

		$valeurHeure11=$valeurDate11=$valeurDuree11=$valeurHeure22="";
		$valeurDate22=$valeurDuree22=$valeurHeure33=$valeurDate33="";
		$valeurDuree33="";

		$dataRattra=recupRattrappage($idrattrapage); //date,heure_depart,duree
		for($P=0;$P<count($dataRattra);$P++) {
			if ($P == 0) {
				$valeurHeure11=timeForm($dataRattra[$P][1]);
				$valeurDate11=dateForm($dataRattra[$P][0]);
				$valeurDuree11=timeForm($dataRattra[$P][2]);
			}
			if ($P == 1) {
				$valeurHeure22=timeForm($dataRattra[$P][1]);
				$valeurDate22=dateForm($dataRattra[$P][0]);
				$valeurDuree22=timeForm($dataRattra[$P][2]);
			}
			if ($P == 2) {
				$valeurHeure33=timeForm($dataRattra[$P][1]);
				$valeurDate33=dateForm($dataRattra[$P][0]);
				$valeurDuree33=timeForm($dataRattra[$P][2]);
			}
		}
?>
	<TR id="ta<?php print $j ?>" class="tabnormal2" onmouseover="this.className='tabover';  " onmouseout="this.className='tabnormal2'">
	<td valign="top" ><?php infoBulleEleveSansLoupe($ideleve,strtoupper(recherche_eleve_nom($ideleve))."&nbsp;".ucwords(strtolower(recherche_eleve_prenom($ideleve))))  ?><br>
	<br><font color='blue'><?php print $classe[0][1]?></font>
	</td>
	<td align=center valign="top"  bgcolor="#FFFFFF">
	<?php $val="'".$i."','".dateHI()."','".dateForm($_GET["date"])."'"; ?>
	<select name="saisie_<?php print $i?>" onChange="abs2(<?php print $val?>)">
	<option value=absent STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGABS?></option>
	<option value=retard STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGRTD?></option>
	<option value="100" STYLE='color:#000066;background-color:yellow'><?php print LANGEDIT20bis ?></option>
	</select></td>
	<td  bgcolor="#FFFFFF" valign="top" align=center>
	<select name="saisie_duree_<?php print $i?>" >
	<?php
	$dureevaleur="value='".$data_3[$j][4]."'";
        $dureee=$data_3[$j][4]." J";
        if ($data_3[$j][4] == 0) { $dureee="???"; $dureevaleur="value='0'";  }else{ unset($dureevaleur);  }
	if ($data_3[$j][4] == -1) { 
		$dureee=preg_replace('/\./','H',$data_3[$j][7]); 
		$dureevaleur="value='$duree'";  
	}else{ 
		unset($dureevaleur);  
	}
       ?>
        <option STYLE='color:#000066;background-color:#CCCCFF' <?php print $dureevaleur?> ><?php print $dureee?></option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>1H00</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>1H30</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>2H00</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>2H30</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>3H00</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>3H30</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>4H00</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>0.5 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>1 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>2 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>3 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>4 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>5 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>6 J</option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>7 J</option>
	</select></td>
	<td  valign="top" >
	<?php $motiftext=$data_3[$j][6]; $value=$data_3[$j][6] ;  if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU;$value=0; } 
	if (trim($data_3[$j][6]) == "0") { $motiftext=LANGINCONNU; $value=0; }
	?>
	<table width='100%' border='0'><tr><td>
	<select name="saisie_motifs_<?php print $i?>" onChange="motifabsretad11('<?php print $i?>',this.value)" id="saisie_motifs_<?php print $i?>" >
        <option value="<?php print $value?>"  STYLE="color:#000066;background-color:#FCE4BA" ><?php print trunchaine($motiftext,$nbcar) ?></option>
        <?php affSelecMotif() ?>
        <option value="1" STYLE='color:#000066;background-color:#CCCCFF' ><?php print "autre" ?></option>
	</select><input type=text id="saisie_motif_<?php print $i?>" name="saisie_motif_<?php print $i?>" value="<?php print $motiftext ?>" size=10 style="display:none" >
	<input type="checkbox" name="saisie_justifier_<?php print $i?>" value="1" onClick="DisplayLigne('ta<?php print $j ?>');" title="Justifier" />
	&nbsp;[<a href="#" onClick="AffBulleAvecQuit('Information rattrapage','image/commun/info.jpg','Pour le <input  size=12 type=text title=\'jj/mm/aaaa\' id=r_d_1<?php print $i?> onblur=document.getElementById(\'rattra_date_1<?php print $i?>\').value=this.value;  onKeyPress=\'onlyChar(event)\' /> à <input id=r_h_1<?php print $i?> type=text size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_heure_1<?php print $i?>\').value=this.value; title=\'hh:mm\' /> durant <input id=r_duree_1<?php print $i?> type=text  size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_duree_1<?php print $i?>\').value=this.value; title=\'hh:mm\' /><br />Pour le <input  size=12 type=text title=\'jj/mm/aaaa\' id=r_d_2<?php print $i?> onblur=document.getElementById(\'rattra_date_2<?php print $i?>\').value=this.value;  onKeyPress=\'onlyChar(event)\' /> à <input id=r_h_2<?php print $i?> type=text size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_heure_2<?php print $i?>\').value=this.value; title=\'hh:mm\' /> durant <input id=r_duree_2<?php print $i?> type=text  size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_duree_2<?php print $i?>\').value=this.value; title=\'hh:mm\' /> <br />Pour le <input  size=12 type=text title=\'jj/mm/aaaa\' id=r_d_3<?php print $i?> onblur=document.getElementById(\'rattra_date_3<?php print $i?>\').value=this.value;  onKeyPress=\'onlyChar(event)\' /> à <input id=r_h_3<?php print $i?> type=text size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_heure_3<?php print $i?>\').value=this.value; title=\'hh:mm\' /> durant <input id=r_duree_3<?php print $i?> type=text  size=4  onKeyPress=\'onlyChar2(event)\' onblur=document.getElementById(\'rattra_duree_3<?php print $i?>\').value=this.value; title=\'hh:mm\' /> <br /><i>La durée et le motif doivent être indiquées pour valider le rattrapage.</i>');  
document.getElementById('r_h_1<?php print $i?>').value=document.getElementById('rattra_heure_1<?php print $i?>').value; 
document.getElementById('r_d_1<?php print $i?>').value=document.getElementById('rattra_date_1<?php print $i?>').value; 
document.getElementById('r_duree_1<?php print $i?>').value=document.getElementById('rattra_duree_1<?php print $i?>').value; 
document.getElementById('r_h_2<?php print $i?>').value=document.getElementById('rattra_heure_2<?php print $i?>').value; 
document.getElementById('r_d_2<?php print $i?>').value=document.getElementById('rattra_date_2<?php print $i?>').value; 
document.getElementById('r_duree_2<?php print $i?>').value=document.getElementById('rattra_duree_2<?php print $i?>').value; 
document.getElementById('r_h_3<?php print $i?>').value=document.getElementById('rattra_heure_3<?php print $i?>').value; 
document.getElementById('r_d_3<?php print $i?>').value=document.getElementById('rattra_date_3<?php print $i?>').value; 
document.getElementById('r_duree_3<?php print $i?>').value=document.getElementById('rattra_duree_3<?php print $i?>').value; 

return false; " >rattrapage</a>]&nbsp;<td valign='top' ><?php print $imgsms ?></td></tr></table>
	 
	</td>
	<td  valign="top" align=center>
	<input type=text onMouseOver="AffBulle('Heure d\'absence <?php print timeForm($heureabs) ?>'); window.status=''; return true;" onMouseOut='HideBulle()' size=10 name="saisie_heure_<?php print $i?>"  value="<?php print dateForm($data_3[$j][1])?>"  onblur="verifHeure2(this.value,'document.formulaire.saisie_heure_<?php print $i?>','document.formulaire.saisie_<?php print $i?>')" onKeyPress="onlyChar2(event)"><br />

	<input type=hidden name="saisie_pers_<?php print $i?>" value="<?php print $data_3[$j][0]?>">
	<input type=hidden name="id_i[]" value="<?php print $i?>">
	<input type=hidden name="base_depart[]" value="absent">
	<!-- elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif -->
	<input type=hidden name=departdatesaisie[]  readonly value="<?php print $data_3[$j][2]?>">
        <input type=hidden name=departdate[]  readonly value="<?php print $data_3[$j][1]?>">
	<input type=hidden name="heurederetard_<?php print $i?>"  readonly value="">
	<input type=hidden name="time_<?php print $i?>"  readonly value="<?php print $data_3[$j][9]?>" >
	<input type=hidden name="matiere_<?php print $i?>"  readonly value="<?php print $data_3[$j][8]?>" >
	<input type=hidden name="heuredatesaisie[]"  readonly value="<?php print $data_3[$j][11]?>" >
	<input type=hidden name="dateorigineret[]"  readonly value="<?php print $data_3[$j][1]?>" >
	<input type=hidden name=idprof[]  readonly value="<?php print $data_3[$j][12]?>">
	<input type=hidden name=heuredabsence[]  readonly value="<?php print $heureabs ?>">

	<input type=hidden name="idrattrapage<?php print $i?>"  readonly='readonly' value="<?php print $data_3[$j][14]?>" >

	<input type=hidden name="rattra_heure_1<?php print $i?>" readonly id="rattra_heure_1<?php print $i?>" value='<?php print $valeurHeure11 ?>' >
	<input type=hidden name="rattra_date_1<?php print $i?>" readonly id="rattra_date_1<?php print $i?>"   value='<?php print $valeurDate11 ?>'  >
	<input type=hidden name="rattra_duree_1<?php print $i?>" readonly id="rattra_duree_1<?php print $i?>" value='<?php print $valeurDuree11 ?>' >

	<input type=hidden name="rattra_heure_2<?php print $i?>" readonly id="rattra_heure_2<?php print $i?>" value='<?php print $valeurHeure22 ?>' >
	<input type=hidden name="rattra_date_2<?php print $i?>" readonly id="rattra_date_2<?php print $i?>"   value='<?php print $valeurDate22 ?>' >
	<input type=hidden name="rattra_duree_2<?php print $i?>" readonly id="rattra_duree_2<?php print $i?>" value='<?php print $valeurDuree22 ?>' >

	<input type=hidden name="rattra_heure_3<?php print $i?>" readonly id="rattra_heure_3<?php print $i?>" value='<?php print $valeurHeure33 ?>' >
	<input type=hidden name="rattra_date_3<?php print $i?>" readonly id="rattra_date_3<?php print $i?>"   value='<?php print $valeurDate33 ?>' >
	<input type=hidden name="rattra_duree_3<?php print $i?>" readonly id="rattra_duree_3<?php print $i?>" value='<?php print $valeurDuree33 ?>' >

	</td>
	</TR>
<?php
	}
print "</table>";
?>
<br>
<input type=hidden name="datecal"  readonly value="<?php print dateForm($_GET["date"]) ?>" >
<input type=hidden name="nb_pers" readonly value="<?php print $nb;?>" >
<input type=hidden name="date_ref"  readonly value="<?php print $date?>" >
<input type=hidden name="filtre"  readonly value="<?php print $_GET["filtre"]?>" >
<input type=hidden name="visu"  readonly value="<?php print $_GET["visu"]?>" >
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGABS45?>","rien"); //text,nomInput</script>
</td></tr></table><br><br>
</form>
     <!-- // fin  -->
     </td></tr></table>
<?php if ($_GET["visu"] != "all") { ?>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
}
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
