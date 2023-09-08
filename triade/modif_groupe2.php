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

<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Liste Elève </title>
</head>
<body id='bodyfond2' >
<?php 
include_once("./librairie_php/lib_licence.php"); 
?>

<center>
<?php
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuprof") {
	if (isset($_GET["sClasseGrp"])) {
		$saisie_classe=$_GET["sClasseGrp"];
	}else{
		$saisie_classe=$_POST["sClasseGrp"];
	}
	$cnx=cnx();
	verif_profp_class($_SESSION["id_pers"],$saisie_classe);
	$nomClasse=chercheClasse_nom($saisie_classe);
}else{
	$saisie_classe="";
	validerequete("menuadmin");
	$cnx=cnx();
}

// module de modification
if(isset($_POST["create"])) {
	if (trim($_POST["idgroupe"]) != "") {
		$nbEl=$_POST["nbEl"];
		for ($i=0;$i<=$nbEl;$i++) {
			$saisie_choix_eleve="saisie_choix_eleve_$i";
			if ($_POST[$saisie_choix_eleve] >= 0) { $params[liste_eleve].=$_POST[$saisie_choix_eleve].","; }

			$saisie_eleve_supp="saisie_eleve_supp_$i";
			if ($_POST[$saisie_eleve_supp] >= 0) { $params2[liste_eleve_supp].=$_POST[$saisie_eleve_supp].","; }
		}
		$params[liste_eleve]=preg_replace('/,+/',',',$params[liste_eleve]);
		$params[liste_eleve]=preg_replace('/,+$/','',$params[liste_eleve]);
		$params[liste_eleve]=preg_replace('/^,+/','',$params[liste_eleve]);

		$params2[liste_eleve_supp]=preg_replace('/,+/','',$params2[liste_eleve_supp]);
		$params2[liste_eleve_supp]=preg_replace('/,+$/','',$params2[liste_eleve_supp]);
		$params2[liste_eleve_supp]=preg_replace('/^,+/','',$params2[liste_eleve_supp]);

		$params[nomgr]=trim($_POST["saisie_intitule"]);
		

		if(modif_group($params)):
		
			if (trim($params2[liste_eleve_supp]) != "") { supprime_note_groupe($params2,$_POST["idgroupe"]); }
			history_cmd($_SESSION["nom"],"MODIFICATION","Suppression eleve dans groupe".$_POST["saisie_intitule"]." ");
	       	 	alertJs("Groupe modifié -- Service Triade ");
		else:
       			error(0);
		endif;
	
	
	}
}
// fin de la modif groupe


$gid=$_GET["gid"];

$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";

$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];
$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
if ($liste_eleves != "") {
	$sql="SELECT nom,prenom,libelle,elev_id FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves) ";
	$res=execSql($sql);
	$data=chargeMat($res);
	$pasdeleve="non";

}else {

	$pasdeleve="oui";

}
?>
<script>
function check(i,y) {
	var supp1="document.formulaire.saisie_eleve_supp_"+i;
	var valid1="document.formulaire.saisie_choix_eleve_"+i;
	var supp=eval(supp1);
	var valid=eval(valid1);
	if (y == "out") {
		if (supp.checked == true) { 
			valid.checked=false;
		}else{
			valid.checked=true;
		}

	}

	if (y == "in") {
		if (valid.checked == true) { 
			supp.checked=false;
		}else{
			supp.checked=true;
		}

	}

}
</script>



<font class=T2><?php print LANGGRP23 ?> <font color="red"><b><?php print $nomgrp?></b></font></font>
<br>
<i><?php print LANGGRP59 ?></i>
<br>
<form method=post name="formulaire">
<input type=hidden name='saisie_intitule' readonly  value="<?php print trim($nomgrp)?>">
<table border="1" width=99% bordercolor="#000000">
<TR>
<TD bgcolor="yellow" ><B><?php print LANGNA1 ?></B></TD>
<TD bgcolor="yellow" ><B><?php print LANGNA2 ?></B></TD>
<TD bgcolor="yellow" align=center width=15%><B><?php print LANGELE4 ?></B></TD>
<TD bgcolor="yellow" width=5% align=center><B><?php print LANGGRP57 ?></B></TD>
<TD bgcolor="yellow" width=5% align=center><B><?php print "Supprimer" ?></B></TD>
</tr>
<?php
// debut for
if ( $pasdeleve != "oui" ) {
	for($i=0;$i<count($data);$i++) {
?>
<tr class="tabnormal2" onmouseover="this.className='tabover2'" onmouseout="this.className='tabnormal2'">
	<td ><?php print ucwords($data[$i][0])?></td>
	<td ><?php print ucwords($data[$i][1])?></td>
	<td align=center ><?php print $data[$i][2]?></td>
	<td align=center ><input type="checkbox" checked name="saisie_choix_eleve_<?php print $i ?>" onclick="check(<?php print $i ?>,'in');" value="<?php print $data[$i][3]?>"></td>
	<td align=center ><input type="checkbox" name="saisie_eleve_supp_<?php print $i ?>" onclick="check(<?php print $i ?>,'out');" value="<?php print $data[$i][3]?>"></td>
</tr>
<?php
	} // fin for
?>
</table>
<BR><BR>
<b><font color=red class="T2" ><?php print LANGGRP58 ?></font></b><br><br>
<input type=hidden name="nbEl" value="<?php print count($data)?>">
<input type=hidden name="idgroupe" value="<?php print $gid ?>">
<input type=hidden name="sClasseGrp" value="<?php print $saisie_classe ?>">
<table align=center><tr><td>
<script language=JavaScript>buttonMagicFermeture()</script></td>
<td><script language=JavaScript>buttonMagicSubmit("<?php print LANGGRP36 ?>","create"); </script>
</tr></table>
</center>
</form>
<?php
}else {
	print "</table>";
	print "<br>";
	print LANGGRP39;
	print "<br>";
	print "<br>";
	print "<br>";
	print "<br>";
	print "<input type=button value=\"".LANGFERMERFEN."\"  onclick='parent.window.close();'>";



}
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
