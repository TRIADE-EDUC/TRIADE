<?php
session_start();
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}else{
	$anneeScolaire=$_COOKIE["anneeScolaire"];
}
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
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script>
      function demandeMotif(id,valeur) {
              if (valeur == "autre") {
                        document.getElementById('motif'+id).style.display='none';
                        document.getElementById('saisie_motif_'+id).style.display='block';
                }else{
                        document.getElementById('saisie_motif_'+id).value=valeur;
              }
      }

        function demandeMotif2(id,valeur) {
                if (valeur == "autre") {
                        document.getElementById('motif2'+id).style.display='none';
                        document.getElementById('saisie_motif2_'+id).style.display='block';
                }else{
                        document.getElementById('saisie_motif2_'+id).value=valeur;
                }

      }
      </script>

</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
$refRattrapage="";
//--------------------------------------------------//
if(isset($_POST["supp_retard"])) {
	$motif="saisie_modif_".$_POST["saisie_id_champ"];
	$duree_retourner="saisie_duree_retourner_".$_POST["saisie_id_champ"];
	$justifier="saisie_justifier_".$_POST["saisie_id_champ"];
	$cr=modif_retard2($_POST["saisie_eleve_id"],$_POST["saisie_heure_ret"],$_POST["saisie_date_ret"],$_POST[$duree_retourner],$_POST[$motif],dateDMY2(),$_SESSION["nom"],$_POST[$justifier],$_POST["saisie_heuredoriginsaisie"],$_POST["saisie_date_ret_origine"],$refRattrapage) ;
	if ($cr == "-1") { alertJs("Retard déjà enregistré pour cette même période."); }
}
//--------------------------------------------------//
if(isset($_POST["supp_absence"])) {
	$motif="saisie_modif_".$_POST["saisie_id_champ"];
	$duree_retourner="saisie_duree_retourner_".$_POST["saisie_id_champ"];
	$justifier="saisie_justifier_".$_POST["saisie_id_champ"];
	$cr=modif_absence($_POST["saisie_eleve_id_2"],$_POST["saisie_date_ret_2"],$_POST["saisie_date_saisie"],$_SESSION["nom"],$_POST[$motif],$_POST[$duree_retourner],$_POST["saisie_time"],$_POST["saisie_matiere"],$_POST[$justifier],$_POST["saisie_heuredoriginsaisie"],$_POST["saisie_date_ret_origine"],$_POST["saisie_heuredabsence"],$refRattrapage);
}
//--------------------------------------------------//

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS61 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >



<?php

if (isset($_GET["ideleve"])) {
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id FROM ${prefixe}eleves e, ${prefixe}classes c WHERE e.elev_id = '".$_GET["ideleve"]."' AND c.code_class = e.classe ORDER BY c.libelle, e.nom, e.prenom";
	$res=execSql($sql);
	$data=chargeMat($res);
}else{
	// affichage de la liste d'élèves trouvées
	$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
	$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id FROM ${prefixe}eleves e, ${prefixe}classes c WHERE lower(e.nom) LIKE '%$motif%' AND c.code_class = e.classe ORDER BY c.libelle, e.nom, e.prenom";
	$res=execSql($sql);
	$data=chargeMat($res);
}

if( count($data) <= 0 )
        {
        print("<BR><center><font size=3>".LANGDISP1."</font><BR><BR></center>");
        }
else {
for($i=0;$i<count($data);$i++)
        {
        ?>
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;"  >
<tr>
<td bgcolor="#FFFFFF" width=55%><?php print LANGTP1 ?> : <B><?php print ucwords(trim($data[$i][1]))?> <?php infoBulleEleve($data[$i][3]); ?></b></td>
<td bgcolor="#FFFFFF"><?php print LANGCALEN7 ?> : <font color=red><?php print trim($data[$i][0])?></font>
</td></tr>
<tr>
<td bgcolor="#FFFFFF"><?php print LANGTP2 ?> : <b><?php print ucwords(trim($data[$i][2]))?></b></td>
<td bgcolor="#FFFFFF"> <?php print LANGABS62 ?></td>
</tr>
<tr>
<td bgcolor="#FFFFFF" colspan='2'>Cumul : <b><span id="<?php print "cumul$i" ?>"></span></b></td>

</tr>

</table>
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;"  >
<TR>
<TD bgcolor='yellow' align=center width=20%><?php print LANGABS13 ?></td>
<TD bgcolor='yellow' align=center width=20%><?php print "Créneaux" ?> </td>
<TD bgcolor='yellow' align=center width=15%><?php print LANGABS60 ?> </td>
<TD bgcolor='yellow' align=center width=20%><?php print LANGABS12 ?> </td>
<TD bgcolor='yellow' align=center width=10%><?php print LANGAGENDA30 ?></td>
</TR>
<?php
$cumulretard=0;
$nbabs=0;
$nbheureab=0;

$data_2=affRetard($data[$i][3],$anneeScolaire);
// $data : tab bidim - soustab 3 champs
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux, idrattrapage
$cumulretard=count($data_2);
for($j=0;$j<count($data_2);$j++) {
	list($creneaux,$crenDebut,$crenFin)=preg_split('/#/',$data_2[$j][10]);
	$idrattrapage=$data_2[$j][11];
	$elev_id=$data_2[$j][0];
	$heure_ret=$data_2[$j][1];
	$date_ret=$data_2[$j][2];
	$date_saisie=$data_2[$j][3];
	$duree_ret=$data_2[$j][5];
	$idmatiere=$data_2[$j][7];
	$justifier=$data_2[$j][8];
	$heure_saisie=$data_2[$j][9];
       	$creneaux=addslashes($data_2[$j][10]);
	if ($idrattrapage == "") {
		$idrattrapage=verifRattrapageRetards($elev_id, $heure_ret, $date_ret, $date_saisie, $duree_ret, $idmatiere, $justifier, $heure_saisie, $creneaux);
	}

	$matiere=chercheMatiereNom($data_2[$j][7]);
	if (($matiere == "") || ($matiere < 0)) { $matiere="";  }
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method='POST' name="formulaire_<?php print $i.$j?>" >
<TD align=center valign=top><?php print date_jour(dateForm($data_2[$j][2])); ?><br>

<span id='ida_<?php print $j?>' ><a href="#" onclick="document.getElementById('ida_<?php print $j?>').style.display='none';document.getElementById('idaa_<?php print $j?>').style.display='block'; return false;" ><?php print dateForm($data_2[$j][2])?></a></span>
<input type=text size=9 style="display:none" name='saisie_date_ret' id="idaa_<?php print $j?>" value="<?php print dateForm($data_2[$j][2])?>" onKeyPress="onlyChar(event)" />
</td>
<TD  align=center valign=top><?php print timeForm($data_2[$j][1]) ?> - <?php print $crenFin ?> (<?php print trunchaine(trim($matiere),11) ?>) </td>
<TD  align=center valign=top>
<select name="saisie_duree_<?php print $i?>" onChange="chargement_pendant('','<?php print $i?>','<?php print $i.$j?>')" >
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
</select>
<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>" value="<?php print $data_2[$j][5]?>"  >
<?php
$yy=$data_2[$j][5];
if ($data_2[$j][5] == 0) {
	$yy="???";
}
?>
<script langage=Javascript>
chargement_pendant('<?php print trim($yy)?>','<?php print $i?>','<?php print $i.$j?>');
</script>
</td>
<TD  valign=top>
<?php
	$motiftext=$data_2[$j][6] ;
	if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
	if (trim($data_2[$j][6]) == "0") { $motiftext=LANGINCONNU; }
	$motiftext=preg_replace('/"/'," ",$motiftext);
?>
<select onChange="demandeMotif2('<?php print $i.$j?>',this.value)" id="motif2<?php print $i.$j?>" >
<option value="<?php print $motiftext ?>"  STYLE="color:#000066;background-color:#FCE4BA" ><?php print $motiftext ?></option>
<?php affSelecMotif() ?>
<option value="autre" STYLE='color:red;background-color:#CCCCFF' ><?php print "autre" ?></option>
</select>
<input type='text' value="<?php print $motiftext ?>" name="saisie_modif_<?php print $i?>" style="display:none" id="saisie_motif2_<?php print $i.$j?>" />
<br>
( <input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" <?php if ($data_2[$j][8] == 1) { print "checked='checked'"; } ?> > <?php print LANGRTDJUS ?>)
</td>
<TD  align=center valign=top>
<input type="submit" name="supp_retard" value="<?php print LANGPER30 ?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br><br>
<input type="hidden" name="saisie_eleve_id" value="<?php print $data[$i][3]?>">
<input type="hidden" name="saisie_heure_ret" value="<?php print $data_2[$j][1]?>">
<input type="hidden" name="saisie_date_ret_origine" value="<?php print $data_2[$j][2]?>">
<input type="hidden" name="saisie_id_champ" value="<?php print $i?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1]?>">
<input type="hidden" name="saisie_heuredoriginsaisie" value="<?php print $data_2[$j][9]?>">
</form>
<form method='post' action='rattrapage.php' onsubmit="var Nwin = window.open('rattrapage.php', 'Nwin', 'width=430,height=230,toolbar=no,location=no,directories=no, status=no,scrollbars=no,resizable=no,menubar=no'); return true;" target='Nwin'  >
<input type="submit" value="<?php print "Rattrap." ?>" name='acces'  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" title="Rattrapage" >
<input type='hidden' name=idrattrappage value="<?php print $idrattrapage?>">
</form>

</td></TR>
<?php
        }
?>
</table>
<BR>
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;"  >
<TR>
<TD bgcolor='yellow' align=center width=20%><?php print LANGPARENT8 ?> </td>
<TD bgcolor='yellow' align=center width=15%><?php print LANGABS60 ?> </td>
<TD bgcolor='yellow' align=center width=25%>&nbsp;<?php print "Créneaux" ?>&nbsp;</td>
<TD bgcolor='yellow' align=center width=20%><?php print LANGABS12 ?> </td>
<TD bgcolor='yellow' align=center width=10%><?php print LANGAGENDA30 ?></td>
</TR>

<?php
$data_3=affAbsence($data[$i][3],$anneeScolaire);
//   elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier,  heure_saisie, heuredabsence, creneaux, smsenvoye , idrattrapage
$nbjoursabs=0;$nbheureabs=0;
for($j=0;$j<count($data_3);$j++) {

	$idrattrapage=$data_3[$j][15];
	if (trim($idrattrapage) == "") {
		$elev_id=$data_3[$j][0];
		$date_saisie=$data_3[$j][2];
		$idmatiere=$data_3[$j][8];
		$heure_saisie=$data_3[$j][11];
		$creneaux=addslashes($data_3[$j][13]);
		$date_ab=$data_3[$j][1];
		$duree_ab=$data_3[$j][4];
		$date_fin=$data_3[$j][5]; 
		$time=$data_3[$j][9];

		$idrattrapage=verifRattrapageAbsences($elev_id, $date_ab, $date_saisie, $duree_ab, $date_fin,  $idmatiere, $time, $heure_saisie, $creneaux);
	}

	if ($data_3[$j][13] != "") {
		list($creneaux,$crenDebut,$crenFin)=preg_split('/#/',$data_3[$j][13]);
	}else{
		$crenDebut="??:??:??";
		$crenFin="??:??:??";
	}
	$heuredabsence=$data_3[$j][12];
	$matiere=chercheMatiereNom($data_3[$j][7]);
	$nomMatiere=chercheMatiereNom($data_3[$j][8]);

	if ($data_3[$j][4] > 0) {
		$nbjoursabs = $nbjoursabs + $data_3[$j][4];
	}else{
		$nbheureabs = $nbheureabs + $data_3[$j][7];	
	}

	if ($data_3[$j][14] == 1) { $imgsms="<img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }

?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method=POST name="formulaire_3_<?php print $i.$j?>" >
<TD  align=center valign=top><?php print date_jour(dateForm($data_3[$j][1])); ?><br>
<span id='idb_<?php print $i.$j?>' ><a href="#" onclick="document.getElementById('idb_<?php print $i.$j?>').style.display='none';document.getElementById('saisie_date_ret_2_<?php print $i.$j?>').style.display='block';return false;" ><?php print dateForm($data_3[$j][1])?></a></span>
<input type=text size=9 name='saisie_date_ret_2' style="display:none" value="<?php print dateForm($data_3[$j][1])?>" onKeyPress="onlyChar(event)" id="saisie_date_ret_2_<?php print $i.$j ?>"/>
</td>
<TD  align=center valign=top>
<select name="saisie_duree_<?php print $i?>" onChange="chargement_pendant_jour('','<?php print $i?>','<?php print $i.$j?>')" >
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
</select>
<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>" value="<?php print $data_3[$j][4]?>"  >
<?php
$yy=$data_3[$j][4]." J";
if ($data_3[$j][4] == 0) {
	$yy="???";
}
if ($data_3[$j][4] == -1) {
	$yy=preg_replace('/\./','H',$data_3[$j][7]);
//	$yy=$data_3[$j][7]."H";
}
?>
<script language='Javascript'>
chargement_pendant_jour('<?php print trim($yy)?>','<?php print $i?>','<?php print $i.$j?>');
</script>
	<TD align=center valign=top><?php print timeForm($crenDebut)."&nbsp;-&nbsp;".timeForm($crenFin) ?> 
		<?php $idclasse=chercheClasseEleve($data_3[$j][0]); ?>
		<br><br><a href='#' onclick="open('edt_visu.php?idclasse=<?php print $idclasse ?>&date=<?php print $data_3[$j][1] ?>','edt','width=1050,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes'); return false;" ><img src="image/commun/calendar3.gif" border='0' /></a>
	</td>
<TD valign=top>
<?php $motiftext=$data_3[$j][6];
      if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
      if (trim($data_3[$j][6]) == "0") { $motiftext=LANGINCONNU; }
      $motiftext=preg_replace('/"/'," ",$motiftext);
?>
<select  onchange="demandeMotif('<?php print $i.$j?>',this.value)" id="motif<?php print $i.$j?>" >
<option value="<?php print $motiftext ?>"  STYLE="color:#000066;background-color:#FCE4BA" title="<?php print $motiftext ?>"  ><?php print trunchaine($motiftext,30) ?></option>
<?php affSelecMotif() ?>
<option value="autre" STYLE='color:red;background-color:#CCCCFF' ><?php print "autre" ?></option>
</select>
<input type='text' value="<?php print $motiftext ?>" name="saisie_modif_<?php print $i?>" style="display:none" id="saisie_motif_<?php print $i.$j?>" />
<br>
<?php print $imgsms ?> (<input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" <?php if ($data_3[$j][10] == 1) { print "checked='checked'"; } ?> > <?php print LANGRTDJUS?>) <br> Matière : <a title="<?php print $nomMatiere?>"><?php print trunchaine($nomMatiere,15) ?></a>

</td>
<TD align=center valign=top><input type=submit name=supp_absence value="<?php print LANGPER30 ?>" name="supp_absent" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br> le <?php print dateJJMM($data_3[$j][2])?> <?php if (($data_3[$j][11] != "") && ($data_3[$j][11] != "00:00:00") ){ print timeForm($data_3[$j][11]); }?> 
<br />
<input type=hidden name=saisie_eleve_id_2 value="<?php print $data[$i][3]?>">
<input type=hidden name=saisie_date_ret_origine value="<?php print $data_3[$j][1]?>">
<input type=hidden name=saisie_nom_eleve value="<?php print $data[$i][1]?>">
<input type=hidden name=saisie_id_champ value="<?php print $i?>">
<input type=hidden name=saisie_time value="<?php print $data_3[$j][9]?>">
<input type=hidden name=saisie_matiere value="<?php print $data_3[$j][8]?>">
<input type=hidden name=saisie_heuredoriginsaisie value="<?php print $data_3[$j][11]?>">
<input type=hidden name=saisie_date_saisie value="<?php print $data_3[$j][2]?>">
<input type=hidden name=saisie_heuredabsence value="<?php print $heuredabsence?>">
</form>
<form method='post' action='rattrapage.php' onsubmit="var Nwin = window.open('rattrapage.php', 'Nwin', 'width=430,height=230,toolbar=no,location=no,directories=no, status=no,scrollbars=no,resizable=no,menubar=no'); return true;" target='Nwin'  >
<input type="submit" value="<?php print "Rattrap." ?>" name='acces'  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" title="Rattrapage" >
<input type="hidden" name=idrattrappage value="<?php print $idrattrapage?>" />
</form>
</td>
</TR>
<?php
	}

	$nbabs=$nbjoursabs * 2;
?>

</table>
<BR>
<form method=post action="gestion_abs_retard_impr.php" >
<input type=submit  value="Imprimer Rtd/Abs de <?php print ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2])) ?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" />
<input type='hidden' name="idEleve" value="<?php print $data[$i][3]?>" />
<input type='hidden' name="saisie_nom_eleve" value="<?php print trim($_POST["saisie_nom_eleve"]) ?>" />
<?php 
print "&nbsp;&nbsp;<script language='JavaScript'>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour menu')</script> ";
?>
</form>
<hr>

<BR><BR>
<?php
	print "<script>document.getElementById('cumul$i').innerHTML=\"Nbr de retards: $cumulretard / Nbr d'absences: $nbabs demi-journée(s) - $nbheureabs heure(s)\"; </script>";
	}
	$cumulretard=0;
	$nbabs=0;
	$nbheureabs=0;
	
}


// $cumulretard
// $nbabs
// $nbheureab


?>


     <!-- // fin  -->
     </td></tr></table>
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
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
