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
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();

if (($_GET["membre"] != "menueleve") && (isset($_GET['membre']))) {
	$membre=renvoiTypePersonne($_GET["membre"]);	
	$sql="SELECT type_pers,pers_id,nom,prenom FROM ${prefixe}personnel WHERE type_pers='$membre'  ORDER BY nom";
}else{
	$saisie_classe=$_GET["idclasse"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves,${prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
}
$res=execSql($sql);
$data=chargeMat($res);
if ($_GET["membre"] != "menueleve") {
	$classe_nom=renvoiMembreFormatePersonne($_GET["membre"]);
}else{
	$classe_nom=chercheClasse_nom($saisie_classe);
}
print "<table width='100%'  border='0' align='left' >";
print "<tr>";
$j=0;
for($i=0;$i<count($data);$i++) {
	$texte=recupIdCodeBar($data[$i][1],"menueleve");
	print "<td align='center' valign='top' >";
	print "<img id='codeim$i' src=\"./codebar/image-impr.php?code=".$_GET["codebase"]."&text=".$texte."\"><br><br>";
	print "<b><font size=3>&nbsp;".strtoupper(trim($data[$i][2]))." ".ucfirst(trim(trunchaine($data[$i][3],15)))."</font></b>";
	print "<br/><font size=1>&nbsp;".$classe_nom."</font>";
	print "</td>";
	$j++;
	if ($j == 3) { print "</tr><tr><td height='30' colspan=3 ><hr></td></tr><tr>"; $j=0; }
}
print "</tr>";
print "</table>";

// deconnexion en fin de fichier
Pgclose();
?>
</form>
</body>
</html>
