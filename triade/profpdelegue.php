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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
	<?php
	// connexion (après include_once lib_licence.php obligatoirement)
	include_once("librairie_php/db_triade.php");
	validerequete("menuprof");
	$cnx=cnx();
	?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de la classe
$saisie_classe=$_GET["sClasseGrp"];
verif_profp_class($_SESSION["id_pers"],$saisie_classe);
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];


if (isset($_POST["create"])) {
	@delete_delegue($_POST["idclasse"]);
	//$parent1,$parent2,$eleve1,$eleve2,$idClasse,$telp1,$mailp1,$telp2,$mailp2
	create_delegue($_POST["parent1"],$_POST["parent2"],$_POST["eleve1"],$_POST["eleve2"],$_POST["idclasse"]);
	alertJs(LANGDONENR."\\n\\n"."L\'Equipe Triade");
	$data=aff_delegue($_POST["idclasse"]); //idclasse,nomparent1,nomparent2,eleve1,eleve2
}else{
	$data=aff_delegue($_GET["sClasseGrp"]); //idclasse,nomparent1,nomparent2,eleve1,eleve2
}	

if (count($data) > 0) {
	$idparent1=$data[0][1];
	$idparent2=$data[0][2];
	$ideleve1=$data[0][3];
	$ideleve2=$data[0][4];
}else{
	$idparent1="";
	$idparent2="";
	$ideleve1="";
	$ideleve2="";
}
 
$sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe='$_GET[sClasseGrp]' AND b.code_class='$_GET[sClasseGrp]' ORDER BY nom";
$res=execSql($sql);
$data_eleve=chargeMat($res);
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><font   id='menumodule1' ><b><?php print LANGPROFP12 ?></b><?php print LANGPROFP13 ?></font> <font id="color2" ><?php print $cl?></font> </td></tr>
<tr id='cadreCentral0' >
<td>
<br>
<form method=post>
<table border=0 align=center >
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP14 ?> 1 : <?php print "<font class=T1>Parent de </font>" ?></font></td>
<td><select name=parent1 >
    <?php print $parent1 ?>
    <option value="null" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
    <?php
for ($j=0;$j<count($data_eleve);$j++) {
		if ($idparent1 == $data_eleve[$j][1]) { $selected1="selected='selected'"; }else{ $selected1=""; }
		print "<option STYLE='color:#000066;background-color:#CCCCFF' $selected1 value=\"".$data_eleve[$j][1]."\">".ucwords(trim($data_eleve[$j][2]))." ".trunchaine(trim($data_eleve[$j][3]),15)."</option>";
    }
	?>
    </select>
</td>
</tr>
<tr><td align=center colspan=2  >&nbsp;</td></tr>
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP14 ?> 2 : <?php print "<font class=T1>Parent de </font>" ?> </font></td>
<td><select name=parent2 >
    <?php print $parent2 ?>
    <option value="null" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
    <?php
for ($j=0;$j<count($data_eleve);$j++) {
		if ($idparent2 == $data_eleve[$j][1]) { $selected2="selected='selected'"; }else{ $selected2=""; }
		print "<option STYLE='color:#000066;background-color:#CCCCFF' $selected2 value=\"".$data_eleve[$j][1]."\">".ucwords(trim($data_eleve[$j][2]))." ".trunchaine(trim($data_eleve[$j][3]),15)."</option>";
    }
	?>
    </select>
</td>
</tr>
<tr><td align=center colspan=2  >&nbsp;</td></tr>
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP16 ?> 1 : </font><?php print "<font class=T1>Elève </font>" ?></td>
<td><select name=eleve1 >
    <?php print $eleve1 ?>
    <option value="null" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
<?php
for ($j=0;$j<count($data_eleve);$j++) {
		if ($ideleve1 == $data_eleve[$j][1]) { $selected3="selected='selected'"; }else{ $selected3=""; }
		print "<option STYLE='color:#000066;background-color:#CCCCFF' $selected3 value=\"".$data_eleve[$j][1]."\">".ucwords(trim($data_eleve[$j][2]))." ".trunchaine(trim($data_eleve[$j][3]),15)."</option>";
    }
	?>
    </select>
</td>
</tr>
<tr><td align=center colspan=2  >&nbsp;</td></tr>

<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP16 ?> 2 : </font><?php print "<font class=T1>Elève </font>" ?></td>
<td><select name=eleve2 >
    <?php print $eleve2 ?>
    <option value="null" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
<?php
for ($j=0;$j<count($data_eleve);$j++) {
		if ($ideleve2 == $data_eleve[$j][1]) { $selected4="selected='selected'"; }else{ $selected4=""; }
		print "<option STYLE='color:#000066;background-color:#CCCCFF' $selected4 value=\"".$data_eleve[$j][1]."\">".ucwords(trim($data_eleve[$j][2]))." ".trunchaine(trim($data_eleve[$j][3]),15)."</option>";
    }
	?>
    </select>
</td>
</tr>

<tr><td align=center colspan=2  >&nbsp;</td></tr>
<tr><td align=center colspan=2  >

<table align=center border=0><tr><td>
<input type=hidden name="idclasse" value="<?php print $_GET["sClasseGrp"] ?>" />
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
<td><td><?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?></td>
</td></tr></table>

</td></tr>
<tr><td align=center colspan=2 >&nbsp;</td></tr>

</table>
</form>
<br>


</td>
</tr>
</table>


<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
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
</BODY>
</HTML>
