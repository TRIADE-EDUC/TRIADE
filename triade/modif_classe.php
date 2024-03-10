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
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form  method=post onsubmit="return verifcreatclasse()" name="formulaire" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS407 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->

<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();


$id=$_GET["id"];
if(isset($_POST["modif"])):
	$classenom=$_POST["saisie_creat_classe"];
	$saisie_langue=$_POST["saisie_langue"];
	$saisie_niveau=$_POST["saisie_niveau"];
	$classenom=str_replace("\'","",$classenom);
	$classenom=str_replace("\"","",$classenom);
	$saisie_classe_long=$_POST["saisie_classe_long"];
	$saisie_classe_long=str_replace("\"","",$saisie_classe_long);
	$specification=$_POST["specification"];
	$offline=0;
        if ($_POST["offline"] == "1") $offline=1 ;
	$id=$_POST["id"];
        $cr=modif_classe22($classenom,$id,$saisie_classe_long,$_POST["saisie_site"],$saisie_langue,$saisie_niveau,$specification);
        if($cr):
		modifOffline($id,$offline);
                alertJs(LANGCLAS2);
        else:
              	alertJs(LANGCLAS3); 
        endif;
endif;

if (isset($_POST["desact"])) {
	$classenom=$_POST["saisie_creat_classe"];
	$classenom=str_replace("\'","",$classenom);
	$classenom=str_replace("\"","",$classenom);
	$saisie_classe_long=$_POST["saisie_classe_long"];
	$saisie_classe_long=str_replace("\"","",$saisie_classe_long);
	$cr=modif_classe_desact2($_POST["id"]);
}

$classenom=chercheClasse_nom($id);
$saisie_classe_long=chercheClasse_description($id);
$idsite=chercherIdSiteClasse($id);
$langue=chercherLangueClasse($id);
$niveau=chercherNiveauClasse($id);
$specification=chercherSpecificationClasse($id);
$offline=chercherOfflineClasse($id);

?>
<BR>
&nbsp;&nbsp;<font class=T2><?php print LANGGRP6?></font> : <input type=text name="saisie_creat_classe" size=30  maxlength='30' value="<?php print html_quotes($classenom) ?>" ><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGMESS410 ?></font> : <input type=text name="saisie_classe_long" size=60  maxlength='250'  value="<?php print stripslashes($saisie_classe_long) ?>" ><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGMESS411 ?></font> : <select name="saisie_site"><?php select_site($idsite) ?></select><BR><br>
&nbsp;&nbsp;<font class=T2><?php print LANGTMESS506 ?></font> : <input type=text name="specification" size=60  maxlength='200' value="<?php print stripslashes($specification) ?>" ><BR><br>
&nbsp;&nbsp;<font class=T2><?php print "Langue de la classe" ?></font> : <select name="saisie_langue">
                                                                         <option value='Français / French' id='select1' <?php if ($langue == "Français / French") print "selected='selected'" ?> >Français / French</option>
                                                                         <option value='Anglais / English' id='select1'  <?php if ($langue == "Anglais / English") print "selected='selected'" ?> >Anglais / English</option>
                                                                         <option value='Espagnol / Spanish' id='select1' <?php if ($langue == "Espagnol / Spanish") print "selected='selected'" ?> >Espagnol / Spanish</option>
                                                                         </select><BR><br>
&nbsp;&nbsp;<font class=T2><?php print "Niveau scolaire" ?></font> : <select name="saisie_niveau">
                                                                        <option value='' id='select0'><?php print LANGCHOIX ?></option>
									<optgroup label="Universitaire"></optgroup>
                                                                        <option value='A1' id='select1' <?php if ($niveau == "A1") print "selected='selected'" ?> >1er année</option>
                                                                        <option value='A2' id='select1' <?php if ($niveau == "A2") print "selected='selected'" ?> >2ieme année</option>
                                                                        <option value='A3' id='select1' <?php if ($niveau == "A3") print "selected='selected'" ?> >3ieme année</option>
                                                                        <option value='A4' id='select1' <?php if ($niveau == "A4") print "selected='selected'" ?> >4ieme année</option>
                                                                        <option value='A5' id='select1' <?php if ($niveau == "A5") print "selected='selected'" ?> >5ieme année</option>
                                                                        <option value='PREPA' id='select1' <?php if ($niveau == "PREPA") print "selected='selected'" ?> >PREPA</option>
                                                                        <option value='M1' id='select1' <?php if ($niveau == "M1") print "selected='selected'" ?> >Master 1</option>
                                                                        <option value='M2' id='select1' <?php if ($niveau == "M2") print "selected='selected'" ?> >Master 2</option>
									<optgroup label="Livret Scolaire"></optgroup>
                                                                        <option value="cycle 2" id='select1' <?php if ($niveau == "cycle 2") print "selected='selected'" ?>  >Cycle 2</option>
                                                                        <option value="cycle 3" id='select1' <?php if ($niveau == "cycle 3") print "selected='selected'" ?> >Cycle 3</option>
                                                                        <option value="cycle 4" id='select1' <?php if ($niveau == "cycle 4") print "selected='selected'" ?> >Cycle 4</option>

                                                                     </select><br><br>
<?php if ($offline == "1") $offline="checked='checked'"; ?>
&nbsp;&nbsp;<font class=T2>Bloquer l'accès des comptes utilisateurs assujetti à cette classe : <input type='checkbox' value='1' name='offline'  <?php print $offline ?>  /><br /><br/></font>

<BR><bR>
<input type=hidden name="id" value="<?php print $id?>" />
<?php if ($id != 0) { ?>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMAT4 ?>","modif"); //text,nomInput</script> <?php } ?>
<script language=JavaScript>buttonMagic("<?php print LANGCLAS1?>","list_classe.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGSUPP21 ?>","suppression_classe.php","_parent","","");</script>
<?php
$cr=ClasseIsOffline($id);
if ($cr == 1) { 
	print "<br><br><script language=JavaScript>buttonMagicSubmit('".LANGMESS408."','desact');</script>";
}else{
	print "<br><br><script language=JavaScript>buttonMagicSubmit('".LANGMESS409."','desact');</script>";
}
?>

<br><br><br>

<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
<?php Pgclose(); ?>
