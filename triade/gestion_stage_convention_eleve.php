<?php
session_start();
include_once("./common/config.inc.php");
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
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

$typeout=$_POST["typeout"]; 



?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85"><tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGSTAGE115 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<br><br>
<table border=0 align=center>
<tr><td valign="top">
<form method=post onsubmit="return valide_consul_classe()" name="formulaire" action="gestion_stage_convention_eleve.php" >
<font class=T2><?php print LANGELE4?> :</font> <select id="saisie_classe" name="saisie_classe">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
if ($_SESSION["membre"] == "menuprof") {
	if (PROFSTAGEETUDIANT == "oui") {
		select_classe(); // creation des options
	}else{
		select_classe_profp($_SESSION["id_pers"]); // creation des options
	}
}else{
	select_classe(); // creation des options
}
?>
</select></td>



<td  valign="top">
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
<?php 
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>

</td></tr>
<tr><td colspan=2 height=10></td></tr>
<tr><td colspan=2 ><input type='checkbox' name="typeout" value='1' <?php if ($typeout == '1') { print "checked='checked'"; } ?> /> <font class='T2'><?php print LANGSTAGE116 ?></font></td></tr>

<tr><td>
</form>
</td></tr></table>
<br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>


<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe


if ((isset($_GET["idclasse"])) || (isset($_POST["consult"]))) {	
	if  (isset($_POST["consult"])) $saisie_classe=$_POST["saisie_classe"];
	if  (isset($_GET["idclasse"])) $saisie_classe=$_GET["idclasse"];
	$sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e,${prefixe}classes c WHERE e.classe='$saisie_classe' AND c.code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan="3"><b><font   id='menumodule1' >
<?php print LANGELE4?> : <font id='color2'><B><?php print $cl?></font></font></td></tr>
<?php
if( count($data) <= 0 )	{
	print("<tr><td align=center valign=center  id='cadreCentral0' >".LANGRECH1."</td></tr>");
}else {
?>
<!--
<tr  id='cadreCentral0'><td colspan=3>
<br><br>
<form method=post action="gestion_stage_convention_eleve1.php">
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT49?>","tous"); //text,nomInput</script>
<input type=hidden name="idclasse" value="<?php print $_POST["saisie_classe"] ?>">
</form><br><br><br>
</td></tr>
-->

<!--
<tr  id='coulBar0' >
<td align=right width=45% valign="top" ><font class="T2"><?php print LANGSTAGE48 ?> :</font></td>
<td align=left valign="top" colspan=2>
<?php
//radiobox_stage($_POST["saisie_classe"]);
?>
</td>
</tr>
-->

<tr bgcolor="yellow"><td><B><?php print ucwords(LANGIMP8)." ".ucwords(LANGIMP9)?></B></td><td title="Num&eacute;ro Convention" ><b>Conv.</b></td><td><B><?php print "Convention" ?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++) {
?>
	<form action="gestion_stage_convention_eleve1.php"  method="post" name="formulaire<?php print $i ?>" >
	<tr>
	<td bgcolor="#FFFFFF"><?php infoBulleEleveSansLoupe($data[$i][1],strtoupper($data[$i][2]))?> <?php print ucwords($data[$i][3])?> </td>
	<td bgcolor="#FFFFFF" width=5%><select name='nbconv' ><option></option><option value='_conv_A' id='select1' >A</option><option value='_conv_B' id='select1' >B</option><option id='select1' value='_conv_C'>C</option></select></td>
	<td bgcolor="#FFFFFF" width=5>
	<select name='choix_stage' onChange='document.formulaire<?php print $i ?>.submit()' >
	<option id='select0' ><?php print LANGCHOIX ?></option>
	<?php 
		if ($typeout != "1") {
			selectStage($data[$i][1]);
		}else{
			selectStage2($data[$i][1]);
		}
	?>
	</select>
	<input type="hidden" name="eid" value="<?php print $data[$i][1] ?>" />
	</td>
	</tr>
	</form>
	<?php
	}
      }
print "</table>";
}

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
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>
