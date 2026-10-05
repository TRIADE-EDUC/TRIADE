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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "Gestion des versements" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="padding:16px;">
<br>

<div class="card">
  <div class="card-header">Recherche par élève</div>
  <div class="card-body">
    <form method="post" onsubmit="return valide_recherche_eleve()" name="formulaire">
      <div class="form-row">
        <label class="form-label"><?php print LANGABS3 ?></label>
        <input type="text" name="saisie_nom_eleve" class="form-control" style="max-width:220px;"
               id="search" autocomplete="off"
               onkeyup="searchRequest(this,'eleve','target','formulaire','saisie_nom_eleve')">
      </div>
      <div id="target" style="width:220px;margin-left:170px;"></div>
      <div style="padding:10px 14px;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT39 ?>","create");</script>
      </div>
    </form>
  </div>
</div>

</div>

</td></tr></table>
<br /><br />

<?php
if (isset($_POST["saisie_nom_eleve"])) {
	$saisie_nom_eleve = trim($_POST["saisie_nom_eleve"]);
	$motif = strtolower($saisie_nom_eleve);
	$sql = <<<EOF
SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM {$prefixe}eleves e, {$prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom
EOF;
	$res  = execSql($sql);
	$data = chargeMat($res);
?>

<div style="padding:0 16px 16px;">
<div class="card">
  <div class="card-header">
    <?php print LANGRECH2 ?> : <?php print htmlspecialchars(ucwords(stripslashes($motif))) ?>
  </div>
  <div class="card-body" style="padding:0;">
<?php if (countTriade($data) <= 0): ?>
    <div class="alert alert-warning" style="margin:14px;"><?php print LANGRECH3 ?></div>
<?php else: ?>
    <table class="table table-hover">
      <thead>
        <tr>
          <th><?php print ucwords(LANGIMP10) ?></th>
          <th><?php print LANGIMP8 ?></th>
          <th><?php print LANGIMP9 ?></th>
        </tr>
      </thead>
      <tbody>
      <?php for ($i = 0; $i < countTriade($data); $i++): ?>
      <tr>
        <td><?php print ucfirst($data[$i][0]) ?></td>
        <td><a href="comptaconfigeleve.php?eid=<?php print $data[$i][3] ?>"
               title="Accès gestion versement"
               style="text-decoration:underline;">
          <?php print strtoupper($data[$i][1]) ?>
        </a></td>
        <td><?php print ucwords($data[$i][2]) ?></td>
      </tr>
      <?php endfor; ?>
      </tbody>
    </table>
<?php endif; ?>
  </div>
</div>
</div>

<?php } ?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY></HTML>
