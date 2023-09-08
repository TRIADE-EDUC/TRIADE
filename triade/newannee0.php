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
        <?php include("./librairie_php/lib_licence.php");
        if (empty($_SESSION["adminplus"])) {
               print "<script>";
                print "location.href='./base_de_donne_key.php'";
                print "</script>";
                exit;
        }
	// connexion (après include_once lib_licence.php obligatoirement)
	include_once("librairie_php/db_triade.php");
	$cnx=cnx();
	?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire"  >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Nouvelle année scolaire" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<br>
<ul>
<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppression des notes de brevet </font><br />
<?php purge_brevetCollege();  ?>

<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppression des notes vie scolaire élèves </font><br />
<?php vide_notes_scolaire(); ?>

<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppression des études d'élèves. </font><br />
<?php purgeEleveEtude(); ?>

<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppression des dispenses d'élèves. </font><br />
<?php vide_dispenses(); purge_present(); ?>

<?php $datedujour=date("Y-m-d"); ?>
<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppr. calendrier de D.S.T avant le <?php print $_GET["supp_date_dst"]  ?> </font><br />
<?php purge_dst2(dateFormBase($_GET["supp_date_dst"])); ?>

<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppr. calendrier des événements  avant le <?php print $_GET["supp_date_cal"] ?></font><br />
<?php purge_evenement2(dateFormBase($_GET["supp_date_cal"])); ?>

<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppr. emploi du temps (EDT)  avant le <?php print $_GET["supp_date_edt"] ?> </font><br />
<?php purgeEdtSeance2(dateFormBase($_GET["supp_date_edt"])); ?>

<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Suppression des délégués. </font><br />
<?php purge_delete_delegue(); ?>

<?php $anneescolaire=paramnouvelleannee(); ?>
<font class=T2>&nbsp;&nbsp;<img src='image/commun/stat1.gif' /> Activation de la nouvelle ann&eacute;e scolaire <?php print $anneescolaire ?> </font><br />
</ul>
<br><br>
<center><font class=T2><font id='color3'>Vous pouvez maintenant effectuer le changement de classe <br> des élèves, puis valider ensuite les pré-inscriptions.</font></font></center><br /><br />
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print "En mode manuel"?>","chgmentClas.php","_parent","","")</script>
</td><td>
<script language=JavaScript>buttonMagic("<?php print "Via importation xls"?>","base_de_donne_importation.php","_parent","","")</script>
</td></tr></table>
<br><br>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
