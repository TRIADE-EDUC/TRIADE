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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP28bis ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>
<?php
// affichage de l'élève (lecture seule)
$eid=$_GET["eid"];
$nomEleve=recherche_eleve_nom($eid);
$prenomEleve=recherche_eleve_prenom($eid);


?>
<font class="T2">
&nbsp;&nbsp;Indiquer les groupes pour l'élève : <b><?php print trunchaine($nomEleve." ".$prenomEleve,40) ?></b> <br /><br />
</font>

<!-- // debut form  -->
<?php
$sql=<<<EOF
SELECT group_id,libelle,liste_elev FROM ${prefixe}groupes ORDER BY libelle
EOF;
$res=execSql($sql);
$liste_gid=chargeMat($res);

for($cpt=0;$cpt<count($liste_gid);$cpt++){
	if ($liste_gid[$cpt][0] != 0) {
		$classesDsGroupe[$liste_gid[$cpt][0]."|".$liste_gid[$cpt][1]] = $liste_gid[$cpt][2] ;
	}
}

foreach($classesDsGroupe as $cle => $value)
{
	$liste_eleves = substr($value,1);
	$liste_eleves = substr($liste_eleves,0,strlen($liste_eleves)-1);
	if($liste_eleves)
	{
		$sql = "SELECT libelle FROM ${prefixe}classes , ${prefixe}eleves  WHERE classe = code_class AND elev_id IN ($liste_eleves)";
		$res = execSql($sql);
		$data =  chargeMat($res);
		for($cpt2=0;$cpt2<count($data);$cpt2++)
		{
			$classesDsGroupe_tmp[$cle][] = $data[$cpt2][0];
		}
	}else{
		if ($cle) { $classesDsGroupe_tmp[$cle][] = ""; }
	}
}

$classesDsGroupe =  $classesDsGroupe_tmp ;
unset($classesDsGroupe_tmp);
print "<form method='post' action='modif_groupes_ajout_multi3.php' >";
print "<TABLE border=0 width=100% ><tr>";
$jj=0;
foreach($classesDsGroupe as $cle => $value){
	$disabled="";
	$select="";
	$liste_classe='';
	$aff=preg_split('/\|/',$cle);
	$affnomgroupe=$aff[1];
	if (trim($affnomgroupe) == "") { continue; }
	sort($value);
	$value = array_unique ($value);
	foreach($value as $tmp) {
		if ($tmp != "") {
			$liste_classe = $liste_classe." ".$tmp;
		}
	}
	if ($liste_classe == "") {$liste_classe="&nbsp;<i>aucun élève</i>"; }
	if ($aff[0] != "") { $gid=$aff[0]; }
	$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
	$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
	$tabListe=explode(',',$liste_eleves);
	foreach($tabListe as $key=>$value) {
		if  ($value == $eid) { 
			$disabled="disabled='disabled'";
			$select="checked='checked'";
			break;
		}
	}
	if ($liste_eleves != "") {
		$sql="SELECT nom,prenom,libelle FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves) ORDER BY nom ";
		$res=execSql($sql);
		$data3=chargeMat($res);
		$liste="";
		for($p=0;$p<count($data3);$p++) {
			$nomeleve=addslashes(ucwords($data3[$p][0]));
			$prenomeleve=addslashes(ucwords($data3[$p][1]));
			$classeeleve=addslashes($data3[$p][2]);
			$liste.=" <img src=\'image/on10.gif\' align=\'center\' /> ".$nomeleve." ".$prenomeleve." / classe : ".$classeeleve."<br />";
		}
	}else{
		$liste="Aucun élève.";
	}
	?>
	<td><input type='checkbox' name='idgrp[]' <?php print $disabled ; print $select ?> value="<?php print $aff[0] ?>" ><a href='#'  onMouseOver="AffBulle3('Liste des élèves','./image/commun/info.jpg','<?php print $liste ?>');"  onMouseOut="HideBulle()";><?php print $affnomgroupe?></a></td>


<?php
	$jj++;
	if ($jj == 3) { print "</tr><tr>";$jj=0; }
	?>
	


<?php
	$i++;
}
?>
</tr></table>
<input type='hidden' name="eid" value='<?php print $_GET["eid"]?>' />
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create"); //text,nomInput</script></UL></UL></UL>
</form>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
</BODY>
</HTML>
