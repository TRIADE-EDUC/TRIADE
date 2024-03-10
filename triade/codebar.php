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
<html>
<head>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/ajax_codebarre.js"></script>
<script language="JavaScript">
function newkey(form,variable){
	form.text2display.value += variable;
}
function newkeyCode(form,variable){
	form.text2display.value += String.fromCharCode(variable);
}
</script>
</head>
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();

if (VATEL == 1) {
		print "<body bgcolor='#F4F5F7' marginheight='0' marginwidth='0' leftmargin='0' topmargin='0'>";
}else{
		print "<body id='coulfond1' marginheight='0' marginwidth='0' leftmargin='0' topmargin='0'>";	
}


$version=phpversion();
if (!preg_match('/^7/',$version)) {
	$mess="<center><b><br><b><font color=red>ATTENTION</font> : ".LANGCODEBAR2."</b><br><br></center>";
}

$saisie_classe=$_GET["idclasse"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
if (preg_match('/^7/',$version)) {
	print "<br><table><tr><td><input type=button value='Imprimer' onclick=\"open('codebarimpr.php?idclasse=$saisie_classe&codebase=".$_GET["codebase"]."','_blank','')\"  class='button'\"></td>";
}
print "$mess";

if (isset($_GET["idvalide"])) {
	valideIdCodeBar($_GET["idvalide"],"menueleve",$_GET["valide"]);
}
if (isset($_GET["idsupp"])) {
	suppIdCodeBar($_GET["idsupp"],"menueleve");
}


print "<form><table width='100%'  border='0' align='left' >";
$x=3;
$y=3;

for($i=0;$i<count($data);$i++) {
	$texte=recupIdCodeBar($data[$i][1],"menueleve");
	if (VATEL == 1) {
		$bgcolor=($bgcolor=="#87C1E6") ? $bgcolor="#BCDAF0" : $bgcolor="#87C1E6" ;
		print "<tr bgcolor='$bgcolor' >\n";
	}else{
		print "<tr>";
	}
	print "<td align=left><b>&nbsp;".strtoupper(trim($data[$i][2]))." ".ucfirst(trim(trunchaine($data[$i][3],10)))."</b></td>";
	print "<td align=left>&nbsp;";
	if (verifCodebarre($data[$i][1],"menueleve")) {
		print "	<img src='./image/commun/stat1.gif' title='actif' />&nbsp;";
		$actvalide=0;
	}else{
		print "	<img src='./image/commun/stat2.gif' title='non actif' />&nbsp;";
		$actvalide=1;
	}
	print "<a href='codebar.php?idclasse=$saisie_classe&idvalide=".$data[$i][1]."&valide=$actvalide&codebase=".$_GET["codebase"]."'><img src='./image/commun/img_ssl_mini.png' border='0' title='bloquer code barre' /></a>&nbsp;&nbsp;";
	print "<a href='codebar.php?idclasse=$saisie_classe&idsupp=".$data[$i][1]."&codebase=".$_GET["codebase"]."'><img src='./image/commun/recycle.jpg' border='0' title='Nouveau code' /></a> ";
	print "<a href=\"#\" onclick=\"document.getElementById('codeim$i').style.display='none';  document.getElementById('codeinput$i').style.display='inline'; return false; \"><img src='./image/commun/editer.gif' border='0' title='Enregistrer un code' /></a>";
	print "</td>";
	if (preg_match('/^7/',$version)) {
		print "<td align=center>";
		print "";
		print "<img id='codeim$i' src=\"./codebar/image.php?code=".$_GET["codebase"]."&text=".$texte."\">";
		print "<span id='codespan$i'><input type=text id='codeinput$i' size='10' style='display:none' onchange=\"enrModifCodebarre(this.value,'".$data[$i][1]."','codespan$i','menueleve')\" ></span>";
		print "</td>";
	}else {
		print "<td></td>";
	}
	print "</tr>";
}
	print "</table></form>";
// deconnexion en fin de fichier
Pgclose();




?>
</form>
</body>
</html>
