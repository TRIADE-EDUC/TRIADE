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
<script language="JavaScript" src="./librairie_js/jquery-min.js" ></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
ul { style-type:none;list-style: none; cursor:pointer;margin-left: 3px;padding-left: 0; }
li { padding:7x; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE59 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br><br>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
include_once("librairie_php/ajaxrecherche.php");

$cnx=cnx();
error($cnx);
validerequete("3");
?>
<form method=post action="gestion_stage_ent_modif2.php">
<center>
<font class=T2><?php print LANGSTAGE60 ?> : </font>
<select name=activite>
<option value="inconnu" id='select0' ><?php print LANGINCONNU ?></option>
<?php
$data=activite_liste();
for($i=0;$i<count($data);$i++) {
	print "<option value='".$data[$i][0]."' id='select1' title=\"".$data[$i][0]."\" >".trunchaine($data[$i][0],30)."</option>";
}
?>
</select>
<br><br>
<font class=T2>dans le département : </font>
<select name="departement" >
	<option value="tous" id='select0'>Tous</option>
	<?php 
	recherche_codeP_ent();
	?>
</select>
</center><br><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rech1"); //text,nomInput</script>
<?php 
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}elseif(($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_el.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>
</td></tr></table>
</form>
<hr>
<form method=post action="gestion_stage_ent_modif_nom.php" name="formulaire_2">
<center>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<tr><td align='right' ><font class=T2><?php print LANGSTAGE61 ?> : </font></td><td><input type=text id='search' name=recherche size=30  style="width:24em" autocomplete='off'  ></td></tr>
<tr><td></td><td style="padding-top:0px;"><div id="userList" style="width:23em;border-style:none; background-color:#EEEEEE;"></div></td>
</tr>
</table>
<br><br><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rech2"); //text,nomInput</script>
<?php 
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}elseif(($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_el.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>
</td></tr></table>
</form>
</td></tr></table>

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
<?php js_search_entreprise(); ?>
</BODY></HTML>
