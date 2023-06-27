<?php
session_start();
if (isset($_POST["type_bulletin"])) {
	$type_bulletin=$_POST["type_bulletin"];
	setcookie("type_bulletin",$type_bulletin,time()+36000*24*30);
}else{
	$type_bulletin=$_COOKIE["type_bulletin"];
}

if (isset($_POST["anneeScolaire"])) {
	setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
	$anneeScolaire=$_POST["anneeScolaire"];
}

if (isset($_POST["saisie_trimestre"])) {
	setcookie("saisie_trimestre",$_POST["saisie_trimestre"],time()+36000*24*30);
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_visadirec.js"></script>
<title>Triade Vidéo-Projecteur</title>
<script>
function reactualise() {
	document.getElementById('com2').value='';
	var contenue="";
	if (document.getElementById('leap1').checked == true) { contenue="leap_felicitation,"; }
	if (document.getElementById('leap2').checked == true) { contenue+="leap_encouragement,"; }
	if (document.getElementById('leap3').checked == true) { contenue+="leap_megcomp,"; }
	if (document.getElementById('leap4').checked == true) { contenue+="leap_megtrav,"; }
	document.getElementById('com2').value=contenue;
}
function reactualise1() {
	document.getElementById('com21').value='';
	var contenue="";
	if (document.getElementById('leap11').checked == true) { contenue="leap_felicitation,"; }
	if (document.getElementById('leap21').checked == true) { contenue+="leap_encouragement,"; }
	if (document.getElementById('leap31').checked == true) { contenue+="leap_megcomp,"; }
	if (document.getElementById('leap41').checked == true) { contenue+="leap_megtrav,"; }
	document.getElementById('com21').value=contenue;
}
</script>
</head>
<body style="" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');

if (isset($_POST["validenoteviescolaire"])) { $_SESSION["validenoteviescolaire"]=$_POST["validenoteviescolaire"]; }

$cnx=cnx();

if ($_SESSION["membre"] == "menupersonnel") {
	if ((!verifDroit($_SESSION["id_pers"],"ficheeleve")) && (!verifDroit($_SESSION["id_pers"],"videoprojo"))) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}

$validenoteviescolaire=$_POST["validenoteviescolaire"];
config_param_ajout($validenoteviescolaire,"validenoteviescolaire");
$afficheNotePartielVatel=$_POST["afficheNotePartielVatel"];
config_param_ajout($afficheNotePartielVatel,"affNotePartielVatel");


$ok=1;
if (isset($_POST["supp"])) {
	$idclasse=$_POST["saisie_classe"];
	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) <=  0 ){
		if ($_POST["fichier_origin"] == "profpprojo") {
			print "<script language=JavaScript>";
			print "location.href='profpprojo.php?info=1'";
			print "</script>";
		}else {
			print "<script language=JavaScript>";
			print "location.href='video-proj-index.php?info=1'";
			print "</script>";

		}
	}

}

// via precendent ou suivant
if (isset($_GET["apres"])) {
	$i=$_GET["apres"];
	$trimes=$_GET["saisie_trimestre"];
	$idclasse=$_GET["saisie_classe"];
	$anneeScolaire=$_GET["anneeScolaire"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	if ($i <= 0) {$i=0;}
	$ideleve=$data_eleve[$i][1];
	$ok=0;
	$iplus= $i + 1;
	$imoins = $i - 1;
}

// en direct avec le select
if (isset($_POST["direct_eleve"])) {
	$idclasse=$_POST["saisie_classe"];
	$anneeScolaire=$_GET["anneeScolaire"];
	$trimes=$_POST["saisie_trimestre"];
	$ok=0;
	$ideleve=$_POST["direct_eleve"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) {
			$i=$j;
			break;
		}
	}
	$iplus= $i + 1;
	$imoins = $i - 1;
}

// premier acces
if ($ok == 1) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	$ideleve=$data_eleve[0][1];
	$i=0;
	$iplus = $i + 1;
	$imoins = $i - 1;
}
//-----------------------------------------------//

?>
<table border=0 width=100% align=center bgcolor="#FFFFFF"  height="100%">
<tr>
	<td colspan=2 >
	<table width=100% border=0 ><tr>
	<td width=40% valign=top >
	<form method=post onsubmit="return valide_supp_choix('direct_eleve','un élève')" name="formulaire" >
	<input type=button class=BUTTON value="<-- Précédent" onclick="open('video-proj-affichage.php?apres=<?php print $imoins?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&anneeScolaire=<?php print $anneeScolaire ?>','video','');this.disabled=true">
	<?php 
	include_once("./librairie_php/lib_conexpersistant.php"); 
	connexpersistance("color:red;font-weight:bold;font-size:11px;text-align: center;"); 
	?>
	</td>
        <td align=center valign=top >
	<script language="JavaScript">buttonMagicImprimer();</script>
<!--	<input type=text  value="<?php print ucwords($trimes)?>" size=10 class=BUTTON readonly> -->
	&nbsp;&nbsp;&nbsp;
	<input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
	<input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
	<input type=hidden name="anneeScolaire" value="<?php print $anneeScolaire?>">
	<select name="direct_eleve">
	    <option  id="select0" ><?php print LANGCHOIX?></option>
<?php
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
$res=execSql($sql);
$data_eleve=chargeMat($res);
for ($j=0;$j<count($data_eleve);$j++) {
?>
<option STYLE='color:#000066;background-color:#CCCCFF'  value="<?php print $data_eleve[$j][1]?>"><?php print ucwords(trim($data_eleve[$j][2]))." ".trim($data_eleve[$j][3])?></option>
<?php
}
?>
	</select> <input type=submit class=BUTTON value="<?php print LANGPER27 ?>" name=rien >
</td>
<td align=right valign=middle ><input type=button class=BUTTON value="Suivant -->" onclick="open('video-proj-affichage.php?apres=<?php print $iplus ?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&anneeScolaire=<?php print $anneeScolaire ?>','video','');this.disabled=true">
</form>
</td></tr></table>
</td></tr>
<?php // ---------------------------------------------------------- ?>

<tr>


<td valign=top width=70% height=100%  >
	<table border=0 width=100% height=100% >
	<tr><td valign=top height=100% ><iframe width='100%' height='100%'  src="video-proj-bulletin.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&type_bulletin=<?php print $type_bulletin ?>" MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=auto  ></iframe></td></tr>
	</table>
<td  valign=top>
	<table width=100% height=100% border=0 >
	<tr><td valign=top height=150 id='bordure' >
	<iframe height=100% src="video-proj-fiche-eleve.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>" width=100% MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe>
	</td></tr>
	<tr><td valign=top  id='bordure' >
	<div id="visdir" style="position:absolute;top:140;left:330;display:none;width:550px;height:310px;padding:1px;border:1px #666 solid;background-color:#ddd;z-index:1000">
		<?php $commentaireD=recherche_com($ideleve,$trimes,$type_bulletin,$anneeScolaire);  ?>
		<form><br /><br />
			&nbsp;&nbsp;&nbsp;<font class=T2><b>Visa Direction</b></font>
			&nbsp;&nbsp;<font class=T1>(Type du bulletin : <?php print $type_bulletin ?>)</font><br /><br />	
			&nbsp;&nbsp;&nbsp;<textarea cols=58 rows=7 name='com' onkeypress="compter(this,'500', this.form.CharRestant_2)" style="font-size: 12pt;" ><?php print $commentaireD ?></textarea>
			&nbsp;<input type=text name='CharRestant_2' size=3 disabled='disabled' value='0' /><br><br>
			<input type='hidden' name='com2' id='com2' value='' />
			&nbsp;&nbsp;&nbsp;<?php 
			if ($_SESSION["membre"] == "menuadmin") {
				print "<input type='button' onclick=\"enrVisaDir('$ideleve',this.form.com.value,'$trimes','retourenr0',this.form.com2.value,'".$type_bulletin."','".$anneeScolaire."')\" value=\"".LANGENR."\" class='bouton2' />";
			} ?>
			&nbsp;&nbsp;<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('visdir', 1)" />
			<span id='retourenr0' style='color:red; '></span>		
			<?php
			if (($type_bulletin == "montessori") || ($type_bulletin == "montessori_spec")){
			      $montessori=recherchemontessori($ideleve,$type_bulletin,$trimes,$anneeScolaire);
			      $montessori=$montessori[0][0];
			      if (trim($montessori) == "felicitation")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
			      if ($montessori == "satisfaction")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
			      if ($montessori == "encouragement") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
			      print "<br><br>&nbsp;&nbsp;&nbsp;";
			      print "Aucun <input type='radio' name='montessori' value='' />&nbsp;&nbsp;\n";
			      print "Félicitations <input name='montessori'  onclick=\"this.form.com2.value=this.value\" type='radio' value='felicitation' $checkedmont1 />&nbsp;&nbsp;\n";
			      print "Satisfactions <input name='montessori'   type='radio' onclick=\"this.form.com2.value=this.value\" value='satisfaction' $checkedmont2 />&nbsp;&nbsp;\n";
			      print "Encouragements <input name='montessori'  type='radio' onclick=\"this.form.com2.value=this.value\" value='encouragement' $checkedmont3 />&nbsp;&nbsp;\n";
			}

			if ($type_bulletin == "leap") {
			      $leap=rechercheleap($ideleve,$type_bulletin,$trimes,$anneeScolaire); //leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav
			      if ($leap[0][1] == "1") { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
			      if ($leap[0][2] == "1") { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
			      if ($leap[0][0] == "1") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
			      if ($leap[0][3] == "1") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
			      print "<br><br>&nbsp;&nbsp;&nbsp;";
			      print "Félicitations <input id='leap1' type='checkbox' onclick=\"reactualise()\" value='1' $checkedmont1 />&nbsp;&nbsp;\n";
			      print "Encour. <input type='checkbox'  id='leap2' onclick=\"reactualise()\" value='1' $checkedmont3 title='Encouragement' />&nbsp;&nbsp;\n";
			      print "MEG Comp. <input type='checkbox'  id='leap3'  onclick=\"reactualise()\"  value='1' $checkedmont2 title='Mise en garde comportement' />&nbsp;&nbsp;\n";
			      print "MEG Trav. <input type='checkbox'  id='leap4'  onclick=\"reactualise()\" value='1' $checkedmont4 title='Mise en garde travail' />&nbsp;&nbsp;\n";
				?>

				<script>reactualise();</script>
				<?php

			}

			if ($type_bulletin == "seminaire") {
			      $montessori=recherchemontessori($ideleve,$type_bulletin,$trimes,$anneeScolaire);
			      if ($montessori == "felicitation")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
			      if ($montessori == "tabhonneur")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
			      if ($montessori == "encouragement") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
			      if ($montessori == "deconduite")  { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
			      if ($montessori == "detravail") { $checkedmont5="checked='checked'"; }else{ $checkedmont5=""; }
			      print "<br><br>&nbsp;&nbsp;&nbsp;";
			      print "&nbsp;&nbsp;";
			      print "Aucun <input name='montessori'  type='radio' onclick=\"this.form.com2.value=this.value\"  value='' />&nbsp;&nbsp;\n";
			      print "Félicitations <input name='montessori' type='radio' onclick=\"this.form.com2.value=this.value\" value='felicitation' $checkedmont1 />&nbsp;&nbsp;\n";
			      print "Tableau d'Honneur <input name='montessori' type='radio' onclick=\"this.form.com2.value=this.value\" value='tabhonneur' $checkedmont2 />&nbsp;&nbsp;\n";
			      print "Encouragements <input name='montessori' type='radio' onclick=\"this.form.com2.value=this.value\" value='encouragement' $checkedmont3 />&nbsp;&nbsp;\n";
			      print "Avertissement de conduite <input name='montessori' type='radio' onclick=\"this.form.com2.value=this.value\" value='deconduite' $checkedmont4 />&nbsp;&nbsp;\n";
			      print "Avertissement de travail <input name='montessori' type='radio' onclick=\"this.form.com2.value=this.value\" value='detravail' $checkedmont5 />&nbsp;&nbsp;\n";
			} ?>

		</form>
	</div>
	<div id="visprofp" style="position:absolute;top:140;left:330;display:none;width:550px;height:280px;padding:1px;border:1px #666 solid;background-color:#ddd;z-index:1000">
		<?php $commentaireP=recherche_com_profP($ideleve,$trimes,$anneeScolaire);  ?>
		<form><br /><br />
		&nbsp;&nbsp;&nbsp;<font class=T2><b>Visa Professeur Principal</b></font><br /><br />
		&nbsp;&nbsp;&nbsp;<textarea cols=58 rows=7 name='com' onkeypress="compter(this,'500',this.form.CharRestant_2)" style="font-size: 12pt;" ><?php print $commentaireP ?></textarea>
		&nbsp;<input type=text name='CharRestant_2' size=3 disabled='disabled' value='0' /><br><br>
		&nbsp;&nbsp;&nbsp;<?php
				

			if ($type_bulletin == "leap") {
			      $leap=rechercheleap($ideleve,$type_bulletin,$trimes,$anneeScolaire); //leap_encouragement,leap_felicitation,leap_meg_comp,leap_meg_trav
			      if ($leap[0][1] == "1") { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
			      if ($leap[0][2] == "1") { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
			      if ($leap[0][0] == "1") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
			      if ($leap[0][3] == "1") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
			  
			      print "Félicitations <input id='leap11' type='checkbox' onclick=\"reactualise1()\" value='1' $checkedmont1 />&nbsp;&nbsp;\n";
			      print "Encour. <input type='checkbox'  id='leap21' onclick=\"reactualise1()\" value='1' $checkedmont3 title='Encouragement' />&nbsp;&nbsp;\n";
			      print "MEG Comp. <input type='checkbox'  id='leap31'  onclick=\"reactualise1()\"  value='1' $checkedmont2 title='Mise en garde comportement' />&nbsp;&nbsp;\n";
			      print "MEG Trav. <input type='checkbox'  id='leap41'  onclick=\"reactualise1()\" value='1' $checkedmont4 title='Mise en garde travail' />&nbsp;&nbsp;\n";
				?>
				<input type='hidden' name='com21' id='com21' value='' />
				<br><br><script>reactualise1();</script>&nbsp;&nbsp;&nbsp;
				<?php

			}else{
				print "<input type='hidden' name='com21' id='com21' value='' />";
			}
			if  (($_SESSION["membre"] == "menuadmin") || ( ($_SESSION["membre"] == "menuprof") && (verif_profp_class2($_SESSION["id_pers"],$idclasse))) ) {
					print "<input type='button' onclick=\"enrVisaProfp('$ideleve',this.form.com.value,'$trimes','retourenr1',this.form.com21.value,'".$type_bulletin."','".$anneeScolaire."')\" value=\"".LANGENR."\" class='bouton2' />";
			} 
			?>
		&nbsp;&nbsp;<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('visprofp', 1)" />
		<span id='retourenr1' style='color:red;'></span>
		</form>
	</div>
	<iframe width='100%' height='100%' src="video-proj-eleve-scol.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>"  FRAMEBORDER=0 SCROLLING=no ></iframe></td></tr>
	<tr><td valign=top height=250 id='bordure' >
	<font class=T2>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Commentaires</u> :</b><br><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='image/on10.gif'  /> <a href='#' onclick="new Effect.Grow('visdir', 1); return false;">Commentaire de la direction.</a><br><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='image/on10.gif'  /> <a href='#' onclick="new Effect.Grow('visprofp', 1);  return false;" >Commentaire du professeur principal.</a></font><br><br></font>
	<table border=0><tr><td  colspan='3' >
	<font class="T2">&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Graphiques</u> :</b></font><br><br>
	<tr><td>
	&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="open('video_projo0.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>','_blank','resizable=yes,width=500,height=350')"><img src="image/commun/graphDemo1.jpg" border="0" itle="Visualiser" /></a> 		
	</td><td>
	<a href="#" onclick="open('video_projo1.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>','_blank','resizable=yes,width=950,height=250')"><img src="image/commun/graphDemo2.jpg" border="0" title="Visualiser" /></a>
	</td><td>
	<a href="#" onclick="open('video_projo2.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>','_blank','resizable=yes,width=530,height=550')"><img src="image/commun/graphDemo3.jpg" border="0" title="Visualiser" /></a>
	</td>

	</td></tr></table>

	</table>
</td></tr></table>
<?php Pgclose(); ?>
</body>
</html>
