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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
<?php include("librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form  method="post" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Unités d'enseignements</font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>
<?php
include_once('librairie_php/db_triade.php');
include_once("librairie_php/fonctions_vatel.php"); 
validerequete("menuadmin");
$cnx=cnx();
if (isset($_POST["create"])) {
        //$cr=vatel_modif_ue($_POST,$_POST['id_detail']);
	vatel_supp_ue($_POST['id_detail'],'ue_detail');
	vatel_supp_ue($_POST['id_detail'],'ue');
        alertJs("Suppresion effectuée");
}
$data_ue=vatel_liste_ue($_GET['id']); 
$classe=Vatel_affUneClasse($data_ue[0][1]);
if (count($data_ue) == 0) {
	print "<script>location.href='vatel_list_ue.php';</script>";
}
?>
<table border='0'>
<tr><td colspan="2"><font class=T2>Etes vous sur de vouloir supprimer l'unité d'enseignement suivante ?</font><br><br></td></tr>
<tr><td width=5% align='right' >&nbsp;&nbsp;<font class=T2>Nom&nbsp;:&nbsp;</font> </td><td><?php print $data_ue[0][4]?></td></tr>
<tr><td align='right'>&nbsp;&nbsp;<font class=T2>Numéro&nbsp;:&nbsp;</font> </td><td><?php print $data_ue[0][3]?></td></tr>
<tr><td align='right'>&nbsp;&nbsp;<font class=T2>Classe&nbsp;:&nbsp;</font> </td><td><?php print ucwords($classe[0][0])?></td></tr>
<tr><td align='right'>&nbsp;&nbsp;<font class=T2>Semestre&nbsp;:&nbsp;</font></td><td><?php print ($data_ue[0][2] == 0) ? "1 et 2" : $data_ue[0][2] ?><input type="hidden" value="<?php print $_GET['id']?>" name="id_detail"></td></tr>
<tr><td colspan="2"><br><br>
<script language=JavaScript>buttonMagicSubmit("Valider la suppression","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print "Liste / modification";?>","vatel_list_ue.php","_parent","","");</script>
<br><br><br>
</td></tr></table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
