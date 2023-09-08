<?php
session_start();
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");

if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE69 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<blockquote><BR>
<font class="T2"><?php print LANGELE4?> :</font> <select id="saisie_classe" name="saisie_classe">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
if (($_SESSION["membre"] == "menuprof") && (isset($_GET["nc"]))) {
	select_classe(); // creation des options
}elseif ($_SESSION["membre"] == "menuprof") {
	select_classe_profp($_SESSION["id_pers"]); // creation des options
}else{
	select_classe(); // creation des options
}
?>
</select> <BR>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
<?php 
if (($_SESSION["membre"] == "menuprof") && (!isset($_GET["nc"]))) {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}elseif(isset($_GET["nc"])) {
	print "";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>
</UL></UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if(isset($_POST["consult"]) || isset($_POST["saisie_classe"]) ) {

$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print LANGELE4?> : <font id="color2"><B><?php print $cl?></font>
	</font></td>
</tr>
<?php
if( count($data) <= 0 ) {
	print("<tr  id='cadreCentral0' ><td align=center valign=center>".LANGRECH1."</td></tr>");
}else{
?>
<tr   id='cadreCentral0'><td colspan=3><br><br>
<form method=post action="gestion_stage_visu_eleve_imprim.php" target="_blank">
&nbsp;&nbsp;<font class="T2"><?php print LANGSTAGE70 ?></font> <select name=idstage>
<?php
select_stage($saisie_classe);
?>
</select>
<font class=T2>de cette classe</font> <input type=submit value="<?php print LANGaffec_cre41 ?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<input type=hidden name="saisie_classe" value="<?php print $saisie_classe?>"><br><br>
</form>
</td></tr>
<tr ><td bgcolor="yellow" width=50% > <B><?php print ucwords(LANGIMP8)?> <?php print  ucwords(LANGIMP9)?></B></td><td colspan=1 bgcolor="yellow" ><B><?php print "Action"?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td><?php print infoBulleEleveSansLoupe($data[$i][1],strtoupper($data[$i][2])." ".ucwords($data[$i][3]))?></td>
	<?php if (isset($_GET["nc"])) {
		$nc="&nc";
	}
	print "<td width=35% align='right' >";
	if ($_SESSION["membre"] == "menuprof") {
		if ((verifProfVisiteur($_SESSION["id_pers"],$data[$i][1])) || (verif_profp_eleve2($data[$i][1],$_SESSION["id_pers"]))) {
			print "<input type=button onclick=\"open('gestion_stage_rapport_visite0.php?id=".$data[$i][1]."&idclasse=$saisie_classe$nc','_parent','')\" value=\"Rapport de Visite\" STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\">&nbsp;";
		}
	}
	if ($_SESSION["membre"] == "menuadmin") {
		print "<input type=button onclick=\"open('gestion_stage_rapport_visite0.php?id=".$data[$i][1]."&idclasse=$saisie_classe$nc','_parent','')\" value=\"Rapport de Visite\" STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\">&nbsp;";
	}
	print "<input type=button onclick=\"open('gestion_stage_visu_eleve_2.php?id=".$data[$i][1]."&idclasse=$saisie_classe$nc','_parent','')\" value=\"".LANGPER27."\" STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'></td></tr>";
	}
      }
print "</table>";
}
?>

<?php if ((PROFSTAGEENTR == "oui") || ($_SESSION["membre"] == "menupersonnel")) { ?>
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE8?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center >
<tr>
<form action='gestion_stage_ent_visu.php'>
<td align=right><font class="T2"><?php print LANGSTAGE9 ?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>


<tr><td></td></tr>
<tr><td></td></tr>
<?php if ($_SESSION["membre"] != "menupersonnel") { ?>
<tr>
<form action='gestion_stage_ent_ajout.php'>
<td align=right><font class="T2"><?php print LANGSTAGE10?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_ent_modif.php'>
<td align=right><font class="T2"><?php print LANGSTAGE11 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<?php } ?>
</table>
<br>
</table>
<?php } ?>

<?php if ((PROFSTAGEETUDIANT == "oui") || ($_SESSION["membre"] == "menupersonnel"))  { ?>
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE13?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<table border=0 align=center >
<tr>
<form action='gestion_stage_visu_eleve_liste.php'  method="post">
<input type=hidden name="saisie_idclasse" value="<?php print $_GET["sClasseGrp"] ?>" >
<td align=right><font class="T2"><?php print LANGSTAFE91 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
</form>
<tr>
<form action='gestion_stage_visu_eleve_liste.php?tous=1'  method="post">
<td align=right><font class="T2"><?php print "Liste des étudiants en entreprise"?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<?php if ((PROFSTAGEETUDIANTADMIN == "oui") && ($_SESSION["membre"] == "menuprof"))  { ?>
<tr>
<form action='gestion_stage_affec_eleve.php' method="post">
<input type=hidden name="saisie_idclasse" value="<?php print $_GET["sClasseGrp"] ?>" >
<td align=right><font class="T2"><?php print LANGSTAGE15?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE4?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_modif_eleve.php'>
<input type=hidden name="saisie_idclasse" value="<?php print $_GET["sClasseGrp"] ?>" >
<td align=right><font class="T2"><?php print LANGSTAGE16?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='gestion_stage_supp_eleve.php'>
<input type=hidden name="saisie_idclasse" value="<?php print $_GET["sClasseGrp"] ?>" >
<td align=right><font class="T2"><?php print LANGSTAGE17?>:</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBT50 ?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<?php } ?>
<tr><td></td></tr>
<tr><td colspan=2><hr></td></tr>
<tr><td></td></tr>

<tr>
<form action='gestion_stage_convention_eleve.php'>
<input type=hidden name="saisie_idclasse" value="<?php print $_GET["sClasseGrp"] ?>" >
<td align=right><font class="T2"><?php print LANGSTAGE90 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>

<?php if (PROFENTRCONVENTION == "oui") { ?>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr>
	<td align=right><font class="T2"><?php print LANGSTAGE89 ?>:</font></td>
	<td align=left><script language=JavaScript>buttonMagic("<?php print LANGPROFB3 ?>","gestion_stage_param_convention.php","conven_create","scollbars=yes,width=700,height=650",""); //text,nomInput</script></td>
	</tr>
<?php } ?>
</form>




</table>
<br><br>
</table>
<?php } ?>




<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY>
</HTML>
