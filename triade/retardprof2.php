<?php
session_start();
if (isset($_POST["codebarre"])) {
	header("Location:gestion_abs_retard_codebar.php?smat=".$_POST["sMat"]);
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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Enseignant - Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script  language="JavaScript">
function fonc1() {
	var indexselect=document.formulaire.saisie_heure.options.selectedIndex;
	document.formulaire.reset();
	document.formulaire.retard_aucun.checked=true;
	document.formulaire.rien.disabled=false;
	document.getElementById('inf').style.visibility='hidden';
	document.formulaire.saisie_heure.options.selectedIndex=indexselect;
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
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<FORM name=formulaire  method=post action='retardprof3.php'>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROFR1 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<BR>
<!-- // fin  -->
<?php
// affichage de la classe
$ident=array('sClasseGrp','cgrp','sMat');
$HPV=hashPostVar($ident);
unset($ident);
$listTmp=explode(":",$HPV[cgrp]);
unset($HPV[cgrp]);
$HPV[cid]=$listTmp[0];
$HPV[gid]=$listTmp[1];
unset($listTmp);
if($HPV[gid]){
    $who="<font color=\"red\"> groupe : ".chercheGroupeNom($HPV[gid]) ."</font>";
    $nomclasse=chercheGroupeNom($HPV[gid]);
	$saisie_classe=$HPV[gid];
	if($HPV[gid]){
        	$gid=$HPV[gid];
	        $sqlIn=<<<SQL
        	SELECT
                	liste_elev
	        FROM
        	        ${prefixe}groupes
        	WHERE
                	group_id='$gid'
SQL;
	      	$curs=execSql($sqlIn);
        	$in=chargeMat($curs);
	      	freeResult($curs);
        	$in=$in[0][0];
	      	$in=substr($in,1);
      	  	$in=substr($in,0,-1);
		if ($in != "") {
	      		$sql="SELECT elev_id,elev_id, ";
		        $sql.=" CONCAT( upper(trim(nom)),' ',trim(prenom) ) ";
        		$sql.=" ,compte_inactif,compte_inactif FROM ${prefixe}eleves WHERE elev_id IN ($in) ORDER BY nom";
		      	unset($in);
        	  	$curs=execSql($sql);
	     		unset($sql);
	      		$data=chargeMat($curs);
          		freeResult($curs);
	      		unset($curs);
		}
	}
}else{
    $cl=chercheClasse($HPV[cid]);
	$saisie_classe=$HPV[cid];
	$nomclasse=$cl[0][1];
    $who=" en <font color=\"red\"> ". LANGABS31." ".$cl[0][1] ."</font>";
    unset($cl);
	$sql="SELECT libelle,elev_id,nom,prenom,compte_inactif FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
}
// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$nommatiere=chercheMatiereNom($_POST["sMat"]);
?>
<UL>  <font class=T2><?php print LANGPROFR2 ; print $who ?>
<br><br> <?php print LANGMESS428 ?> <?php print $nommatiere ?></font>
<?php
$data3=recupCreneauDefault("creneau"); // libelle,text
$date=dateDMY2();
$heure=dateHIS();
$idprof=$_SESSION["id_suppleant"];
$dataseance=recupInfoSeance2($date,$heure,$idprof,$_POST["sMat"],$saisie_classe,$gid); // idclasse,idmatiere,heure,duree,idgroupe
?>
<br><br>
<font class=T2> <?php print LANGMESS429 ?> 
<select name="saisie_heure" onChange="fonc2()">
<option id='select0' value='null' ><?php print LANGCHOIX ?></option>
<?php
if (count($dataseance)) {
	$heuredebut=$dataseance[0][2];
	$duree=$dataseance[0][3];

	$dureesec=conv_en_seconde($duree);
	$heuresec=conv_en_seconde($heuredebut);

	$sommeSec=$dureesec+$heuresec;

	$heurefin=trim(timeForm(calcul_hours($sommeSec)));
	
	$heuredebut=trim(timeForm($dataseance[0][2]));

	$optionCreneau="<option  id='select0' value=\""."EDT :"."#".$dataseance[0][2]."#".$heurefin.":00\" selected='selected' >EDT : ".$heuredebut." - ".$heurefin."</option>";
}else{
	$optionCreneau="";
}
print $optionCreneau ;
$disabled='disabled';
if (count($data3) > 0) {
	$disabled='';
	$data3=recupInfoCreneau($data3[0][1]);
	print "<option  id='select0' value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" selected='selected' >".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
}

select_creneaux2();
?>
	</select> - <?php print dateDMY() ?></font> 
<br><br>
</UL>
<table border="1" bordercolor="#000000" width="100%">
<?php
$sub=0;
if( count($data) <= 0 )
        {
        print("<tr><td align=center valign=center><BR><font class=T2>".LANGRECH1."</font><BR><BR></td></tr>");
        }
else {
?>
<tr>
<td  id="bordure" bgcolor="yellow" width='200'><B><?php print LANGNA1?> <?php print LANGNA2?> </B></td>
<td  id="bordure" bgcolor="yellow" align=center width=1%><B><?php print LANGABS20?></B></td>
<td  id="bordure" bgcolor="yellow" align=center width=1%><B><?php print LANGABS21?></B></td>
<?php if (PROFMOTIFABSRTD == "oui") { ?>
	<td id="bordure" bgcolor="yellow" width=100 align=center><B><?php print LANGABS22?></B></td>
<?php } ?>
<td  id="bordure" bgcolor="yellow" align=center width=1% ><B><?php print "Info."?></B></td>
</tr>
<?php
for($i=0;$i<count($data);$i++) {
	if ($data[$i][4] == "1") continue; 
	$disp=0;
	$rtdlien="";
	$displien="";
	// verif si deja absent ou retard
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$resu=dejaabs($data[$i][1]);
	if (count($resu) != 0) {
		for($ii=0;$ii<count($resu);$ii++){
			$photoeleve="image_trombi.php?idE=".$resu[$ii][0];
			?>
			<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'"><td  id="bordure" ><?php print "<a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".ucwords($data[$i][2])." ".ucwords($data[$i][3])?></a></td>
			<td align=center  colspan=4 bgcolor="#FFFFFF" id="bordure" >&nbsp;<?php print LANGABS18 ?>&nbsp;<?php print dateForm($resu[$ii][1])?>&nbsp;<?php print LANGABS19?>&nbsp;<?php print dateForm($resu[$ii][5])?>&nbsp;&nbsp;<font color=red>[</font><A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B><font color=black><?php print $resu[0][6] ?></font></B>&nbsp;</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><b><?php print LANGABS12 ?></b></a><font color=red>]&nbsp;&nbsp;</font>
</td>
			</tr>
			<?php
			continue;
		}
	}else{
		$resu=dejadisp($data[$i][1]);
		if (count($resu) != 0) {
			//elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM dispenses
			for($ii=0;$ii<count($resu);$ii++){
				$matiere=chercheMatiereNom($resu[$ii][1]);
				$disp=1;
			}
		}
        ?>
<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td>
<?php
$datartd=verifsiretard($data[$i][1]);
//elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere
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
$datartd=verifsiabs($data[$i][1]);
//elev_id, duree_heure, date_ab, date_saisie, origin_saisie, duree_ab , motif, idmatiere

if (count($datartd) > 0) {
	if ($dejaFait == 0) { $rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('";$dejaFait=1; }
	$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGMESS60."</b></font>".LANGMESS60bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";

	$ia=0;
	for($io=0;$io<count($datartd);$io++) {
		$ia++;
		$duree="(".preg_replace('/\./','h',$datartd[$io][1]).")";
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

$enstage=verifSiEleveEnStage($data[$i][1],dateDMY());

$photoeleve="image_trombi.php?idE=".$data[$i][1]; 
print "<a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".ucwords($data[$i][2])." ".trunchaine(ucwords($data[$i][3]),9)?></a>
</td>
<?php
if (($enstage == 1) && (VATEL != 1)) {
	print "<td align=center  id='bordure' colspan='4' >";
	print "<i>en stage aujourd'hui</i>";
	print "</td></tr>";

}else{ ?>

<td align=center  id="bordure" >
<?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i?>" onChange="DisplayLigne2('tr<?php print $i ?>',this.value);abs(<?php print $val?>);">
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<?php if (ACCESPROFABSRTD == "oui"){  ?>
	<option value=retard STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGRTD?></option>
<?php } 

if (ABSPROF == "oui"){
	print "<option value=absent STYLE='color:#000066;background-color:#CCCCFF'>".LANGABS."</option>";
}
?>
</select></td>
<td   id="bordure" align=center>
<select name="saisie_duree_<?php print $i?>" >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
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
</select></td>
<?php if (PROFMOTIFABSRTD == "oui") { ?>
	<td align=left id="bordure" ><select name="saisie_motif_<?php print $i?>"  >
	<option value="inconnu"  id='select0' ><?php print LANGINCONNU ?></option>
	<?php affSelecMotif() ?>
	</select><input type="checkbox" title="Valider comme justifier" name="saisie_justifier_<?php print $i?>" value="1" /></td>
<?php } ?>
<td  id="bordure"  align=center>
<input type=hidden name=saisie_pers_<?php print $i?> value="<?php print $data[$i][1]?>">
<?php print "&nbsp;".$rtdlien."&nbsp".$displien; ?>
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
<input type=hidden name=saisie_id value="<?php print count($data) ?>" >
<input type=hidden name=idmatiere value="<?php print $_POST["sMat"] ?>" >
<input type=hidden name=nomclasse value="<?php print $nomclasse ?>" >
<input type=hidden name=nommatiere value="<?php print $nommatiere ?>" >
<input type=hidden name=idprof value="<?php print $_SESSION["id_pers"] ?>" >
&nbsp;&nbsp;&nbsp;&nbsp;<?php print LANGABS53 ?> :  <input type=checkbox class="btradio1" name='retard_aucun' value="oui" onclick="fonc1();"> (<?php print LANGOUI?>)<br><br>
<table align=center><tr><td>
<?php if (count($dataseance)) { $disabled=""; } ?>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR?>","rien","<?php print $disabled ?>"); //text,nomInput</script>
</td></tr>
</table>
<br>
<div id="inf" style='color:red' ><center><i><?php print LANGMESS427 ?></i></center></div>
<?php if ((trim($disabled) == '') || (count($dataseance))) {
	print "<script>document.getElementById('inf').style.visibility='hidden';</script>";
}


?>
<br>
<?php } ?>
     <!-- // fin  -->
     </td></tr></table>
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
