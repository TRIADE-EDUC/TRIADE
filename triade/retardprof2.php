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
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<style>
.na-card  { background:#fff !important; border:1px solid #c5cae9 !important; border-radius:8px !important; padding:14px 16px !important; margin:10px 0 10px !important; }
.na-row   { display:flex !important; align-items:center !important; margin-bottom:8px !important; gap:8px !important; flex-wrap:wrap !important; }
.na-lbl   { font-size:12px !important; font-weight:600 !important; color:#333 !important; min-width:140px !important; flex-shrink:0 !important; }
.na-foot  { margin-top:8px !important; overflow:hidden !important; }
</style>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script language="JavaScript">
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<FORM name=formulaire method=post action='retardprof3.php'>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
<?php print LANGPROFR1 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td>
<?php
$ident=array('sClasseGrp','cgrp','sMat');
$HPV=hashPostVar($ident);
unset($ident);
$listTmp=explode(":",$HPV['cgrp']);
unset($HPV['cgrp']);
$HPV['cid']=$listTmp[0];
$HPV['gid']=$listTmp[1];
unset($listTmp);
if($HPV['gid']){
    $who="<font color=\"red\"> groupe : ".chercheGroupeNom($HPV['gid']) ."</font>";
    $nomclasse=chercheGroupeNom($HPV['gid']);
	$saisie_classe=$HPV['gid'];
	if($HPV['gid']){
        	$gid=$HPV['gid'];
	        $sqlIn=<<<SQL
        	SELECT
                	liste_elev
	        FROM
        	        {$prefixe}groupes
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
        		$sql.=" ,compte_inactif,compte_inactif FROM {$prefixe}eleves WHERE elev_id IN ($in) ORDER BY nom";
		      	unset($in);
        	  	$curs=execSql($sql);
	     		unset($sql);
	      		$data=chargeMat($curs);
          		freeResult($curs);
	      		unset($curs);
		}
	}
}else{
    $cl=chercheClasse($HPV['cid']);
	$saisie_classe=$HPV['cid'];
	$nomclasse=$cl[0][1];
    $who=" en <font color=\"red\"> ". LANGABS31." ".$cl[0][1] ."</font>";
    unset($cl);
	$sql="SELECT libelle,elev_id,nom,prenom,compte_inactif FROM {$prefixe}eleves ,{$prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
}
$nommatiere=chercheMatiereNom($_POST["sMat"]);
?>

<div class="na-card">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFR2 ?> :</span>
    <span><?php print $who ?></span>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS428 ?> :</span>
    <span class="T2"><?php print $nommatiere ?></span>
  </div>
  <?php
  $data3=recupCreneauDefault("creneau");
  $date=dateDMY2();
  $heure=dateHIS();
  $idprof=$_SESSION["id_suppleant"];
  $dataseance=recupInfoSeance2($date,$heure,$idprof,$_POST["sMat"],$saisie_classe,$gid);
  ?>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS429 ?> :</span>
    <select name="saisie_heure" class="cc-select" onChange="fonc2()">
      <option value='null'><?php print LANGCHOIX ?></option>
      <?php
      if (countTriade($dataseance)) {
          $heuredebut=$dataseance[0][2];
          $duree=$dataseance[0][3];
          $dureesec=conv_en_seconde($duree);
          $heuresec=conv_en_seconde($heuredebut);
          $sommeSec=$dureesec+$heuresec;
          $heurefin=trim(timeForm(calcul_hours($sommeSec)));
          $heuredebut=trim(timeForm($dataseance[0][2]));
          $optionCreneau="<option value=\""."EDT :"."#".$dataseance[0][2]."#".$heurefin.":00\" selected='selected' >EDT : ".$heuredebut." - ".$heurefin."</option>";
      }else{
          $optionCreneau="";
      }
      print $optionCreneau;
      $disabled='disabled';
      if (countTriade($data3) > 0) {
          $disabled='';
          $data3=recupInfoCreneau($data3[0][1]);
          print "<option value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" selected='selected' >".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
      }
      select_creneaux2();
      ?>
    </select>
    <span class="T2"> - <?php print dateDMY() ?></span>
  </div>
</div>

<table class="brs-table" width="100%">
<?php
$sub=0;
if( countTriade($data) <= 0 ) {
        print("<tr><td class='brs-td' align=center valign=center><BR><font class=T2>".LANGRECH1."</font><BR><BR></td></tr>");
}
else {
?>
<thead>
<tr>
<th class="brs-th" style="text-align:left !important; width:200px !important;"><?php print LANGNA1 ?> <?php print LANGNA2 ?></th>
<th class="brs-th"><?php print LANGABS20 ?></th>
<th class="brs-th"><?php print LANGABS21 ?></th>
<?php if (PROFMOTIFABSRTD == "oui") { ?>
<th class="brs-th" style="width:100px !important;"><?php print LANGABS22 ?></th>
<?php } ?>
<th class="brs-th">Info.</th>
</tr>
</thead>
<tbody>
<?php
for($i=0;$i<countTriade($data);$i++) {
	if ($data[$i][4] == "1") continue;
	$disp=0;
	$rtdlien="";
	$displien="";
	// verif si deja absent ou retard
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$resu=dejaabs($data[$i][1]);
	if (countTriade($resu) != 0) {
		for($ii=0;$ii<countTriade($resu);$ii++){
			$photoeleve="image_trombi.php?idE=".$resu[$ii][0];
			?>
			<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'"><td class="brs-td"><?php print "<a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".ucwords($data[$i][2])." ".ucwords($data[$i][3])?></a></td>
			<td align=center colspan=4 bgcolor="#FFFFFF" class="brs-td">&nbsp;<?php print LANGABS18 ?>&nbsp;<?php print dateForm($resu[$ii][1])?>&nbsp;<?php print LANGABS19?>&nbsp;<?php print dateForm($resu[$ii][5])?>&nbsp;&nbsp;<font color=red>[</font><A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B><font color=black><?php print $resu[0][6] ?></font></B>&nbsp;</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><b><?php print LANGABS12 ?></b></a><font color=red>]&nbsp;&nbsp;</font>
</td>
			</tr>
			<?php
			continue;
		}
	}else{
		$resu=dejadisp($data[$i][1]);
		if (countTriade($resu) != 0) {
			//elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3 FROM dispenses
			for($ii=0;$ii<countTriade($resu);$ii++){
				$matiere=chercheMatiereNom($resu[$ii][1]);
				$disp=1;
			}
		}
        ?>
<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td class="brs-td">
<?php
$datartd=verifsiretard($data[$i][1]);
//elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere
$rtdlien="";
$dejaFait=0;
if (countTriade($datartd) > 0) {
	$rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('";
	$dejaFait=1;
}

if (countTriade($datartd) > 0) {
	$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGABS32."</b></font>".LANGABS32bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";
	$ia=0;
	for($io=0;$io<countTriade($datartd);$io++) {
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

if (countTriade($datartd) > 0) {
	if ($dejaFait == 0) { $rtdlien="<font color=red>[</font><A href='#' onMouseOver=\"AffBulle('";$dejaFait=1; }
	$rtdlien.="<font face=Verdana size=1><font color=red><b> ".LANGMESS60."</b></font>".LANGMESS60bis."&nbsp;".LANGMESS63."&nbsp;</font><br>";

	$ia=0;
	for($io=0;$io<countTriade($datartd);$io++) {
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
	print "<td align=center class='brs-td' colspan='4' >";
	print "<i>en stage aujourd'hui</i>";
	print "</td></tr>";

}else{ ?>

<td align=center class="brs-td">
<?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i?>" class="cc-select" onChange="DisplayLigne2('tr<?php print $i ?>',this.value);abs(<?php print $val?>);">
<option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<?php if (ACCESPROFABSRTD == "oui"){  ?>
	<option value=retard style='color:#000066;background-color:#CCCCFF'><?php print LANGRTD?></option>
<?php }

if (ABSPROF == "oui"){
	print "<option value=absent style='color:#000066;background-color:#CCCCFF'>".LANGABS."</option>";
}
?>
</select></td>
<td class="brs-td" align=center>
<select name="saisie_duree_<?php print $i?>" class="cc-select">
<option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
<option style='color:#000066;background-color:#CCCCFF'></option>
</select></td>
<?php if (PROFMOTIFABSRTD == "oui") { ?>
	<td align=left class="brs-td"><select name="saisie_motif_<?php print $i?>" class="cc-select">
	<option value="inconnu"><?php print LANGINCONNU ?></option>
	<?php affSelecMotif() ?>
	</select><input type="checkbox" title="Valider comme justifier" name="saisie_justifier_<?php print $i?>" value="1" /></td>
<?php } ?>
<td class="brs-td" align=center>
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
print "</tbody></table>";
?>
<?php if ($sub == 1) { ?>
<br>
<div class="na-card">
  <input type=hidden name=saisie_id value="<?php print countTriade($data) ?>" >
  <input type=hidden name=idmatiere value="<?php print $_POST["sMat"] ?>" >
  <input type=hidden name=nomclasse value="<?php print $nomclasse ?>" >
  <input type=hidden name=nommatiere value="<?php print $nommatiere ?>" >
  <input type=hidden name=idprof value="<?php print $_SESSION["id_pers"] ?>" >
  <div class="na-row">
    <span class="na-lbl"><?php print LANGABS53 ?> :</span>
    <input type=checkbox class="btradio1" name='retard_aucun' value="oui" onclick="fonc1();">
    <span class="T2">(<?php print LANGOUI ?>)</span>
  </div>
</div>
<div class="na-foot">
  <?php if (countTriade($dataseance)) { $disabled=""; } ?>
  <script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR?>","rien","<?php print $disabled ?>"); //text,nomInput</script>
</div>
<br>
<div id="inf" style='color:red; text-align:center;'><i><?php print LANGMESS427 ?></i></div>
<?php if ((trim($disabled) == '') || (countTriade($dataseance))) {
	print "<script>document.getElementById('inf').style.visibility='hidden';</script>";
}
?>
<br>
<?php } ?>

</td></tr></table>
</FORM>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
