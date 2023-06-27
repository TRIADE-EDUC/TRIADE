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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("7");
$cnx=cnx();
//error($cnx);
$etatmodif=0;
$intitule="";
$cacher="";
$public="";
if (isset($_GET["id"])) {
	$data=verifGroupMail($_GET["id"],$_SESSION["id_pers"]);
	if (count($data) > 0) {  //id,idpers,liste_id,libelle,cacher,public
		$etatmodif=1;
		$idgroupemail=$_GET["id"];
		$intitule=$data[0][3];
		$cacher=($data[0][4] == 1) ? "checked='checked'" : "" ;
		$public=($data[0][5] == 1) ? "checked='checked'" : "" ;
		$liste=$data[0][2];
	}

}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post name="formulaire" action='./messagerie_creat_grpmail2.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS23?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<ul><BR>
<font class="T2"><?php print LANGGRP1?> :</font> <input type=text name='saisie_intitule' size=35 maxlength=15 value="<?php print $intitule ?>" ><BR>
<BR><U><?php print LANGMESS24?></U><BR><BR></UL>
<center>
<table width=100% border=0>
<TR><TD>&nbsp;&nbsp;
<select align=top name="saisie_liste[]" size=20  style="width:190px" multiple="multiple">
<?php
if ($etatmodif == 1) {
	$liste=preg_replace('/\{/',"",$liste);
	$liste=preg_replace('/\}/',"",$liste);
	$liste=explode(",",$liste);
	print "<optgroup label='".LANGGEN1."'>\n";
	select_personne_grpmail('ADM',$liste);
	print "<optgroup label='".LANGGEN2."'>\n";
	select_personne_grpmail('MVS',$liste);
	print "<optgroup label='".LANGGEN3."'>\n";
	select_personne_grpmail('ENS',$liste);
	print "<optgroup label='"."Personnels"."'>";
	select_personne_grpmail('PER',$liste);
	print "<optgroup label='"."Tuteurs de stage"."'>";
	select_personne_grpmail('TUT',$liste);
}else{
	print "<optgroup label='".LANGGEN1."'>";
	select_personne('ADM');
	print "<optgroup label='".LANGGEN2."'>";
	select_personne('MVS');
	print "<optgroup label='".LANGGEN3."'>";
	select_personne('ENS');
	print "<optgroup label='"."Personnels"."'>";
	select_personne('PER');
	print "<optgroup label='"."Tuteurs de stage"."'>";
	select_personne('TUT',$liste);
}
?>
</select>
</TD>
<TD valign=top><ul>
<TABLE border="1" width=80% bordercolor="#000000">
<TR><TD bgcolor="#FFFFFF">
<?php print LANGMESS25?> <font color=red><B><?php print LANGGRP4?></b></font> <?php print LANGGRP5?><BR>  <BR>
</td></tr>
</table>
<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire"))  {  ?>
<br><br>
<input type=checkbox name='public' value="on" <?php print $public ?> class='btradio1' onclick="document.formulaire.cacher.checked=false" > <?php print LANGMESS35?>
<?php } ?>
<br><br>
<input type=checkbox name='cacher' value="on" <?php print $cacher ?> class='btradio1' onclick="document.formulaire.public.checked=false" > <?php print "Cacher la liste des pers."?>
<input type=hidden value="<?php print $idgroupemail ?>" name='idgroupemail' />
</ul></TD></TR></TABLE></center>
<BR><BR><UL>
<?php if ($etatmodif == 1) { $bt="Valider la modification"; }else{ $bt=LANGMESS26; } ?>
<script language=JavaScript>buttonMagic("<?php print LANGAGENDA30." / ".LANGAGENDA26 ?>","messagerie_liste_grpmail.php","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print $bt ?>","rien"); //text,nomInput</script>
<script language=JavaScript>buttonMagicRetour("messagerie_envoi.php",'_self'); //text,nomInput</script>
<ul><br> <br><br>
<!-- // fin  -->
</td></tr></table>
</form>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")){
     	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      	print "</SCRIPT>";
      	top_d();
      	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
	print "</SCRIPT>";
}
?>
</BODY></HTML>
