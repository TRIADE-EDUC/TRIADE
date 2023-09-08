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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade Vidéo-Projecteur</title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body style="" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<BR><BR><BR><BR>
<form method=post action="video-proj-affichage.php" onsubmit="return valide_choix_projo()" name="formulaire">
<table border=1 bordercolor="#000000" width=500 align=center bgcolor="#FFFFFF" height="100" style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);'>
<tr><td height='20' id="bordure" ></td></tr>
<tr><td width=50% align=right id="bordure" >
<font size='3'><?php print LANGBULL3 ?> :</td><td id="bordure" >
<select name='anneeScolaire' >
<?php
$anneeScolaire=$_COOKIE["anneeScolaire"];
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select></font></td></tr>
<tr><td height='20' id="bordure" ></td></tr>
<tr><td width=50% align=right id="bordure" >

<font size='3'><?php print LANGPROJ1?> :</font> </td><td id="bordure" ><select name="saisie_classe">
<option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
validerequete("7");
if ($_SESSION["membre"] == "menuprof") {
	$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
	$mySession=hashSessionVar($ident);
	unset($ident);
	$donne=$_SESSION["id_suppleant"];
	$sql="SELECT idprof,idclasse FROM ${prefixe}prof_p WHERE idprof='$donne'";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	// patch pour problème sous-matière à 0
	for($i=0;$i<count($data);$i++){
		$nomclasse=chercheClasse($data[$i][1]);
		$nomclasse=$nomclasse[0][1];
		print "<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][1]."'>$nomclasse</option>\n";
	}
	// fin patch
	freeResult($curs);
	unset($curs);
}else{
	select_classe(); // creation des options
}
?>
</select></td>
</tr><tr>
<tr><td height='20' id="bordure" ></td></tr>
<tr><td align=right id="bordure"  ><font size=3><?php print LANGPROJ2?> :</font> </td>
<td id="bordure" >
<select name="saisie_trimestre">
<option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<option value="trimestre1" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
<option value="trimestre2" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
<option value="trimestre3" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
<option value="annee" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGMESS430 ?></option>
</Select>
</td>
</tr>
<?php if (MODNAMUR0 == "oui") { 
	$datap=config_param_visu("validenoteviescolaire");
	$validenoteviescolaire=$datap[0][0];
	if ($validenoteviescolaire == "oui") { 
		$validenoteviescolaireOUI='checked="checked"';
       		$validenoteviescolaireNON='';
	}else{
		$validenoteviescolaireNON='checked="checked"';
       		$validenoteviescolaireOUI='';
	}
?>
<tr><td height='20' id="bordure" ></td></tr>
<tr>
<td align=right id="bordure"  ><font size=3><?php print LANGTMESS487 ?> :</font> </td>
<td id="bordure" >
<input type='radio' name='validenoteviescolaire' value='oui' <?php print $validenoteviescolaireOUI ?> /> <i>(<?php print LANGOUI ?>)</i>
<input type='radio' name='validenoteviescolaire' value='non' <?php print $validenoteviescolaireNON ?>  /> <i>(<?php print LANGNON ?>)</i>
</td>
</tr>
<?php } ?>
<tr><td height='20' id="bordure" ></td></tr>
<?php
	$datap=config_param_visu("affNotePartielVatel");
	$affNotePartielVatel=$datap[0][0];
	if ($affNotePartielVatel == "oui") { 
		$affNotePartielVatelOUI='checked="checked"';
       		$affNotePartielVatelNON='';
	}else{
		$affNotePartielVatelNON='checked="checked"';
       		$affNotePartielVatelOUI='';
	}
?>
<tr>
<td align=right id="bordure"  ><font size=3><?php print LANGMESS431 ?> :</font> </td>
<td id="bordure" >
<input type='radio' name='afficheNotePartielVatel' value='oui'  <?php print $affNotePartielVatelOUI ?> /> <i>(<?php print LANGOUI ?>)</i>
<input type='radio' name='afficheNotePartielVatel' value='non'  <?php print $affNotePartielVatelNON ?> /> <i>(<?php print LANGNON ?>)</i>
</td>
</tr>


<tr><td height='20' id="bordure" ></td></tr>
<tr><td align=right id="bordure"  ><font size=3><?php print LANGMESS432 ?> :</font> </td>
<td id="bordure">
<select name="type_bulletin" onChange="modifForm();" >
	<?php
	if (isset($_COOKIE["type_bulletin"])) {
		print "<option id='select0'  value='".$_COOKIE["type_bulletin"]."' >Bulletin ".strtoupper($_COOKIE["type_bulletin"])."</option>";
	}
	?>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='default' >Standard</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='UE' >Avec unité enseignement</option>
	<optgroup label="Montessori" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='montessori' >Bulletin Montessori standard</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='montessori_spec' >Bulletin Montessori Spécif.</option>
	<optgroup label="Seminaire" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='seminaire' >Bulletin Seminaire standard</option>
	<optgroup label="LEAP" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='leap' >Bulletin LEAP standard</option>
	<optgroup label="ISMAPP" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='ismapp' >Bulletin ISMAPP standard</option>
     </select></Select>


<tr>
<tr><td height='20' id="bordure" ></td></tr>
<td  colspan=2 align=center id="bordure"  >
<table align=center><tr><td>
<?php
$valeur=aff_Trimestre();
if (count($valeur)) {
	$disabled="";
	$alert="";
}else{
	$disabled="disabled=disabled";
	$alert=LANGMESS10."<br><br>".LANGMESS11."<br><br>".LANGMESS12;
}
?>
<script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
</td></tr></table>
<tr><td height='20' id="bordure" ></td></tr>
</td></tr></table>
</form>
<br><br>
<?php
if (isset($_GET["info"])) {
	print "<center><b>".LANGPROJ6."</b></center>";
}
?>
<center><font class=T3><b><?php print $alert ?></b></font></center>
<?php Pgclose(); ?>
<script>
function modifForm() {
	var a=document.formulaire.type_bulletin.options.selectedIndex;	
	if (document.formulaire.type_bulletin.options[a].value == "UE") {
		document.formulaire.action="video-proj-affichage-UE.php";
	}

}
</script>
</body>
</html>
