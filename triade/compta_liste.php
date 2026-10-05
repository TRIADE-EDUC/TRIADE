<?php
      session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/rechercheV4.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.ca-search-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; }
.ca-search-body { padding:28px 20px 20px; display:flex; flex-direction:column; align-items:center; gap:12px; }
.ca-search-icon { font-size:32px; color:#c5cae9; line-height:1; }
.ca-search-hint { font-size:12px; color:#9e9e9e; text-align:center; }
.ca-search-input-wrap { position:relative; width:100%; max-width:340px; }
.ca-search-input-wrap input[type=text] { width:100%; padding:10px 14px; border:2px solid #c5cae9; border-radius:8px; font-size:14px; box-sizing:border-box; outline:none; font-family:inherit; transition:border-color .18s; }
.ca-search-input-wrap input[type=text]:focus { border-color:#1e4d8c; }
#resultats { position:absolute; left:0; right:0; z-index:100; background:#fff; border:1px solid #c5cae9; border-radius:6px; box-shadow:0 4px 14px rgba(0,0,0,.13); margin-top:3px; }
.ca-search-actions { padding:14px 16px; display:flex; gap:10px; align-items:center; justify-content:center; border-top:1px solid #eef0f8; background:#f5f7ff; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Modifier un encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:8px 4px;">

<form method=post onsubmit="return valide_recherche_eleve()" name="formulaire" id="formulaire" action="compta_liste2.php">
<div class="ca-search-card">
  <div class="ca-search-body">
    <div class="ca-search-icon">&#128269;</div>
    <div class="ca-search-hint">Saisissez le nom de l'&eacute;l&egrave;ve pour acc&eacute;der &agrave; ses encaissements</div>
    <div class="ca-search-input-wrap">
      <input type="text" name="saisie_nom_eleve" id="search" autocomplete="off"
             placeholder="<?php print LANGABS3 ?>..."
             onkeyup="searchRequestV4('search','eleve','resultats','formulaire','saisie_nom_eleve')" />
      <div id="resultats"></div>
    </div>
  </div>
  <div class="ca-search-actions">
    <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create");</script>
    <script language=JavaScript>buttonMagicRetour("comptavers.php","_self");</script>
  </div>
</div>
</form>

     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
