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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>

<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script  language="JavaScript">
function fonc1() {
	// document.formulaire.reset();
	document.formulaire.retard_aucun.checked=true;
	document.formulaire.rien.disabled=false;
	document.getElementById('inf').style.visibility='hidden';
}
function fonc2() {
	var op=document.formulaire.saisie_heure.options.selectedIndex;
	if (document.formulaire.saisie_heure.options[op].value == "null") {
		document.formulaire.rien.disabled=true;
		document.getElementById('inf').style.visibility='visible';
	}else{
		document.formulaire.rien.disabled=false;
		document.getElementById('inf').style.visibility='hidden';
	}
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<script language="JavaScript" >var envoiform=true; </script>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS25 ?></font></b></td></tr>
<tr  id='cadreCentral0' >
<td>
<BR>
<form method='post' name='formulaire0' action="gestion_abs_retard_suite.php" >
<!-- // fin  -->
<?php

$idmatiere="";
$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);

// affichage de la classe
if (isset($_POST["class"])) {
	$idClasse=$_POST["saisie_classe"];
	$saisie_classe=$_POST["saisie_classe"];
	$typevaleur=$_POST["saisie_classe"];
	$typechamps="saisie_classe";
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
	print "<input type='hidden' name='class' value=\"".$_POST["class"]."\" >";
	print "<input type='hidden' name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" >";
}


if (isset($_POST["grp"])) {
	$gid=$_POST["saisie_groupe"];
	$idgroupe=$_POST["saisie_groupe"];
	$typevaleur=$_POST["saisie_groupe"];
	$typechamps="saisie_groupe";
	$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
	$nomgrp=$data[0][0];
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
	$res=execSql($sql);
	$data=chargeMat($res);
	print "<input type='hidden' name='grp' value=\"".$_POST["grp"]."\" >";
	print "<input type='hidden' name='saisie_groupe' value=\"".$_POST["saisie_groupe"]."\" >";
}


if (isset($_POST["etude"])) {
	$idetude=$_POST["saisie_etude"];
	$typevaleur=$_POST["saisie_etude"];
	$typechamps="saisie_etude";
	$sql="SELECT id_etude,id_eleve FROM ${prefixe}etude_affect WHERE id_etude='$idetude' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$idmatiere="-".$data[0][0]; // correspond à l'etude avec le "-"
	$cl=rechercheEtude($data[0][0]);
	for($i=0;$i<count($data);$i++) {
		$liste_eleves.=$data[$i][1].",";
	}
	$liste_eleves=preg_replace('/,$/',"",$liste_eleves);
	if ($liste_eleves != "") {
		$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
		$res=execSql($sql);
		$data=chargeMat($res);
	}
	print "<input type='hidden' name='etude' value=\"".$_POST["etude"]."\" >";
	print "<input type='hidden' name='saisie_etude' value=\"".$_POST["saisie_etude"]."\" >";
}


?>
<UL><?php print "<font class='T2'>Absences ou retards en classe de"  ?> : <font id="color3" ><?php print ucwords($cl)?></font></font><br>
<br>
<?php
if (isset($_POST["datedepart"])) {
	$datedepart=$_POST["datedepart"];
	$disabledT="";
	$mess="";
}elseif(AUTODATEABSRTD == "oui") {
	$datedepart=dateDMY();
	$disabledT="";
	$mess="";
}else{
	$datedepart="dd/mm/aaaa";
	$disabledT="disabled='disabled'";
	$mess="<b><font id='color2' >Indiquer la date</font></b>";
}


?>

	<font class='T2'>Pour le : </font><input type=text name="datedepart" value="<?php print $datedepart ?>" size=12  onclick="this.value=''" onKeyPress="onlyChar(event)"  class="bouton2" onChange="this.form.submit();" />&nbsp;&nbsp;<?php print $mess ?>
<br /> 
</form>
<br>
<form name="formulaire"  method='post' action='gestion_abs_retard_suite2.php' >

<font class=T2> Horaire : 
<select name="saisie_heure" onChange="fonc2()" <?php print $disabledT ?> >
<option id='select0' value="null" ><?php print LANGCHOIX ?></option>
<?php
$disabled="disabled";
$data3=recupCreneauDefault("creneau"); // libelle,text
if (count($data3) > 0) {
	$data3=recupInfoCreneau($data3[0][1]);
	print "<option  id='select0' value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" selected='selected' >".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
	$disabled="";
}else{
?>
<option id='select0' value="null" ><?php print LANGCHOIX ?></option>
<?php
}
select_creneaux2();
?>
<?php
$dataEdt=recupCoursDuJourViaClasse($datedepart,$idClasse); // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule
if (count($dataEdt)) {
	print "<optgroup label='EDT'>";
	for($i=0;$i<count($dataEdt);$i++) {

		list($h,$m,$s)=preg_split('/:/',$dataEdt[$i][4]);
		$h=$h+TIMEZONE;
		$m=$m+TIMEZONEMINUTE;
		if ($h < 10) $h="0$h";
		if ($m < 10) $m="0$m";
		$dataEdt[$i][4]="$h:$m:$s";

		$secondeT=conv_en_seconde($dataEdt[$i][4]);
                $secondeT+=conv_en_seconde($dataEdt[$i][5]);
                $heureFin=calcul_hours($secondeT);
		$infotime=timeForm($dataEdt[$i][4])." - ".timeForm($heureFin);
		// nuit#22:30:00#06:30:00
		$infotimeText="Edt#".$dataEdt[$i][4]."#".$heureFin;
		print "<option id='select1' value='$infotimeText' >Edt : $infotime</option>";
	}
	print "</optgroup>";

}
?>


	</select>  <input type='hidden' name="datedepart" value="<?php print $datedepart ?>" />

</font> 
<br><br>


<?php
if (ISMAPP == 1) { 
	include_once("./librairie_php/ajax-nosubmit.php");
	ajax_js();

?>

<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print "Matière" ?> : </font> <input type="text" name="idmatiere" size="40" id="search" autocomplete="off" onkeyup="searchRequest(this,'matiere','target','formulaire','idmatiere')"   style="width:36em;" />
</td></tr><tr><td style="padding-top:0px;padding-left:70px"><div id="target" style="width:36em;" ></div>  </td></tr>
</table>



<?php }else{ ?>
	<font class=T2>Matière : </font>
	<select name="idmatiere" <?php print $disabledT ?> >
	<option id='select0' value="" ><?php print LANGCHOIX ?></option>
	<?php select_matiere3("50") ?>
	</select>
	<br>
<?php } ?>
<br />

<font class=T2>Enseignant : </font><select name="idprof" <?php print $disabledT ?> >
<option id='select0' value="" ><?php print LANGCHOIX ?></option>
<optgroup label="Enseignant">
<?php select_personne_nom_len_id('ENS',25) ?>
<optgroup label="Vie Scolaire">
<?php select_personne_nom_len_id('MVS',25) ?>
</select>

 </UL><br><br>

<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;" >
<?php
$sub=0;
if( count($data) <= 0 )
        {
        print("<tr><td id=bordure align=center valign=center><BR><font size=3>".LANGPROJ6."</font><BR><BR></td></tr>");
        }
else {
?>
<tr>
<td bgcolor="yellow" width=25%><B><?php print LANGNA1 ?> <?php print LANGNA2 ?></B></td>
<td bgcolor="yellow" align=center width=5%><B><?php print LANGABS20?></B></td>
<td bgcolor="yellow" align=center width=5%><B><?php print LANGABS21?></B></td>
<td bgcolor="yellow" width=5% align=center><B><?php print LANGABS22?></B></td>
<td bgcolor="yellow" width=5% align=center><B><a href='#' title='Justifier' ><?php print "Just." ?></a></B></td>
<td bgcolor="yellow" align=center width=5%><B><a href='#' title='Informations' ><?php print "Info."?></a></B></td>
</tr>
<?php
for($i=0;$i<count($data);$i++) {
	$disp=0;


	$enstage=verifSiEleveEnStage($data[$i][1],$datedepart);

	$datartd=verifsiretardAvecDate($data[$i][1],$datedepart);

	//elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, creneaux
	$rtdlien="";
	$dejaFait=0;
	if (count($datartd) > 0) {
		$rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('";
		$dejaFait=1;
	}

	if (count($datartd) > 0) {	
		$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGABS32."</b></font>".LANGABS32bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";
		$ia=0;
		for($io=0;$io<count($datartd);$io++) {
			$ia++;
			$duree="(".$datartd[$io][5].")";
			if ($datartd[$io][5] == 0 ) { $duree="(???)"; }
			$matierenom=chercheMatiereNom($datartd[$io][7]);
			if (trim($matierenom) == "") { $matierenom="???"; }
			list($creneau,$dC,$fC)=preg_split('/#/',$datartd[$io][8]);
			$cre="($dC - $fC)";
			$rtdlien.="<font face=Verdana size=1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".addslashes(LANGABS33)."&nbsp;".addslashes($matierenom)."&nbsp;".$cre."&nbsp;".$duree."</font><br>";
		}
	}
	//-----------------------------------------//
	$datartd=verifsiabsAvecDate($data[$i][1],$datedepart);
	//elev_id, duree_heure, date_ab, date_saisie, origin_saisie, duree_ab , motif, idmatiere, creneaux
	
	if (count($datartd) > 0) {
		if ($dejaFait == 0) { $rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('";$dejaFait=1; }
		$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGMESS60."</b></font>".LANGMESS60bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";
	
		$ia=0;
		for($io=0;$io<count($datartd);$io++) {
			$ia++;
			$duree="(".$datartd[$io][1]."h)";
			if ($datartd[$io][1] == 0 ) { $duree="(???)"; }
			$matierenom=chercheMatiereNom($datartd[$io][7]);
			if (trim($matierenom) == "") { $matierenom="???"; }
			list($creneau,$dC,$fC)=preg_split('/#/',$datartd[$io][8]);
			$cre="($dC - $fC)";
			$rtdlien.="<font face=Verdana size=1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".addslashes(LANGABS33)."&nbsp;".addslashes($matierenom)."&nbsp;".$cre."&nbsp;".$duree."</FONT><br>";
		}
	}
	//-----------------------------------------//
	if ($dejaFait == 1) {
		$rtdlien.="'); window.status=''; return true;\" onMouseOut='HideBulle()'><b>Info</b></a><font color=red>]</font>";
	}
	
	if ($disp == 1) {
		$displien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('<font face=Verdana size=1><B><font color=red>".LANGABS29."</font></B>".LANGABS29bis."&nbsp; <br> $matiere </FONT>'); window.status=''; return true;\" onMouseOut='HideBulle()'><b>".LANGABS30."</b></a><font color=red>]</font>";
	}

	// verif si deja absent ou retard
	$datedebut=$datedepart;
	$resu=dejaabsviaDate($data[$i][1],$datedebut);
	if (count($resu) != 0) {
		for($ii=0;$ii<count($resu);$ii++){
			?>
			<tr id='tr<?php print $i ?>' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'"><td><?php print ucwords($data[$i][2])." ".ucwords($data[$i][3])?></td>
			<td align=center  colspan=5 bgcolor="#FFFFFF"> <?php print LANGABS18 ?> <?php print dateForm($resu[$ii][1])?> <?php print LANGABS19 ?>  <?php print dateForm($resu[$ii][5])?></td>
			</tr>
			<?php
			continue;
		}
	}else{
		$resu=dejadispViaDate($data[$i][1],$datedebut);
		if (count($resu) != 0) {
			//elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM dispenses
			for($ii=0;$ii<count($resu);$ii++){
				$matiere=chercheMatiereNom($resu[$ii][1]);
				$disp=1;
			}
		}
        ?>
<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td>
<?php
$photoeleve="image_trombi.php?idE=".$data[$i][1];
$infoProba=getProbaEleve($data[$i][1]);
if ($infoProba == 1) {
	$infoprobatoire="<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" />";
}else{
	$infoprobatoire="";
}
print "$infoprobatoire&nbsp;&nbsp;".infoBulleEleveSansLoupe($data[$i][1],ucwords($data[$i][2])." ".ucwords($data[$i][3]));
if ($disp == 1) { ?>&nbsp;[<A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B><font color=red>D</font></B>ispenser de :&nbsp; <br> <?php print $matiere?>.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><b><font color=red>Disp</font></b></a>]
	<?php
}
?>


</td>
<?php
if (($enstage == true) && (VATEL !=  1)) {
	print "<td align=center colspan='5' bgcolor='#FFFFFF'>";
	print "<i>en stage aujourd'hui</i>";
	print "</td></tr>";
}else{ ?>
<td align=center  bgcolor="#FFFFFF">
<?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i?>" onChange="DisplayLigne2('tr<?php print $i?>',this.value);abs(<?php print $val?>);">
<option value=0 id='select0' ><?php print LANGRIEN?></option>
<option value="absent" id='select1'><?php print LANGABS?></option>
<option value="retard" id='select1'><?php print LANGRTD?></option>
</select></td>
<td  bgcolor="#FFFFFF" align=center>
<select name="saisie_duree_<?php print $i?>" onChange="abs3(<?php print $val?>);verifjustifier('<?php print $i ?>')" >
<option value='0' id='select0'><?php print LANGRIEN?></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
<option  id='select1'></option>
</select></td>
<td bgcolor="#FFFFFF">
<select onChange="motifabsretad22('<?php print $i ?>',this.value); verifjustifier('<?php print $i ?>')" name="saisie_motifs_<?php print $i ?>" id="motif_<?php print $i?>" >
<option value="0"  id='select0' ><?php print LANGINCONNU ?></option>
<?php affSelecMotif() ?>
<option value="autre"  id='select1' ><?php print "autre" ?></option>
</select>
<input type="text" name="saisie_motif_<?php print $i?>" size="19" value="<?php print LANGINCONNU ?>" id="saisie_motif_<?php print $i?>"  style="display:none" />
</td>
<td>
<input type="checkbox" name="saisie_justifie_<?php print $i?>" value="1" disabled='disabled' />
</td>


<td align=center>
<?php print "&nbsp;".$rtdlien."&nbsp".$displien; ?>
<input type=hidden size=12 name="saisie_duree1_<?php print $i?>" >
<input type=hidden name=saisie_pers_<?php print $i?> value="<?php print $data[$i][1]?>">
</td>
</tr>
<?php
}
        }
	$sub=1;
	}
      }
print "</table>";
?>
<?php if ($sub == 1) { ?>
<BR>
<input type=hidden name="saisie_id" value="<?php print count($data)?>">
<input type=hidden name=nomclasse value="<?php print $cl ?>">
<input type=hidden name=nommatiere value="<?php print "" ?>">
</b>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print LANGABS53 ?> :  <input type=checkbox class="btradio1" name='retard_aucun' value="oui" onclick="fonc1();"> (<?php print LANGOUI?>)<br><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR?>","rien","<?php print $disabled ?>"); //text,nomInput</script>
</td></tr>
</table>
<br>
<div id="inf" style='color:red' ><center><i>Indiquer heure d'abs/rtd</i></center></div>
<?php if ($disabled == '') {
	print "<script>document.getElementById('inf').style.visibility='hidden';</script>";
}
?>
<br>
<?php } ?>
     <!-- // fin  -->
     </td></tr></table>
<input type='hidden' name='type' value="<?php print $typechamps ?>"  />
<input type='hidden' name='typevaleur' value="<?php print $typevaleur ?>" />
     </form>
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
     <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>

   </BODY></HTML>
