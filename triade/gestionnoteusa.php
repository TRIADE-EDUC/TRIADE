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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/font-awesome.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGNOTEUSA1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
if (NOTEUSA == "non") {
	print "<div class='alert alert-warning' style='margin:16px;'><i class='fa fa-exclamation-triangle'></i>&nbsp;".LANGMESS37.".</div>";
} else {

	include_once("librairie_php/db_triade.php");
	validerequete("menuadmin");
	$cnx = cnx();
	error($cnx);

	$gnu_success = "";
	$gnu_error   = "";

	if (isset($_GET["idsupp"])) {
		$cr = supp_config_note_usa($_GET["idsupp"]);
		if ($cr) {
			history_cmd($_SESSION["nom"], "SUPPRESSION", "config note USA");
			$gnu_success = "Configuration supprimée avec succès.";
		}
	}

	if (isset($_POST["create"])) {
		if ((empty($_POST["libelle"])) || (!is_numeric($_POST["max"])) || (!is_numeric($_POST["min"]))) {
			$gnu_error = LANGCOM2;
		} else {
			if ($_POST["min"] >= $_POST["max"]) {
				$gnu_error = LANGCOM1;
			} else {
				$cr = enr_config_note_usa(trim($_POST["libelle"]), trim($_POST["min"]), trim($_POST["max"]));
				if ($cr) {
					history_cmd($_SESSION["nom"], "CONFIG", "note USA");
					$gnu_success = "Configuration enregistrée avec succès.";
				}
			}
		}
	}
?>

<div style="padding:16px;">

<?php if ($gnu_success): ?>
<script>document.addEventListener('DOMContentLoaded', function(){ alertify.success(<?php echo json_encode($gnu_success); ?>); });</script>
<?php endif; ?>
<?php if ($gnu_error): ?>
<script>document.addEventListener('DOMContentLoaded', function(){ alertify.error(<?php echo json_encode($gnu_error); ?>); });</script>
<?php endif; ?>

<!-- Description -->
<div class="card">
  <div class="card-header">
    <?php print LANGNOTEUSA2 ?>
  </div>
  <div class="card-body">
    <p style="margin:0 0 4px;color:#555;font-size:13px;"><?php print LANGNOTEUSA3 ?></p>
    <p style="margin:0;color:#aaa;font-size:12px;font-style:italic;">(Ne rentrer que des entiers.)</p>
  </div>
</div>

<!-- Formulaire ajout -->
<div class="card">
  <div class="card-header">
    Nouvelle configuration
  </div>
  <div class="card-body">
    <form method="post">
      <div class="form-row">
        <label class="form-label"><?php print LANGNOTEUSA4 ?></label>
        <input type="text" name="min" class="form-control" style="max-width:70px;"
               value="<?php print htmlspecialchars(isset($_POST['min']) ? $_POST['min'] : '') ?>">
        <label class="form-label"><?php print LANGNOTEUSA4bis ?></label>
        <input type="text" name="max" class="form-control" style="max-width:70px;"
               value="<?php print htmlspecialchars(isset($_POST['max']) ? $_POST['max'] : '') ?>">
        <label class="form-label"><?php print LANGNOTEUSA4ter ?></label>
        <input type="text" name="libelle" class="form-control" style="max-width:150px;"
               value="<?php print htmlspecialchars(isset($_POST['libelle']) ? $_POST['libelle'] : '') ?>">
      </div>
      <div style="padding:10px 14px;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
      </div>
    </form>
  </div>
</div>

<!-- Liste -->
<div class="card">
  <div class="card-header">
    Configurations enregistrées
  </div>
  <div class="card-body" style="padding:0;">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th><?php print LANGNOTEUSA5 ?></th>
          <th><?php print LANGNOTEUSA5bis ?></th>
          <th><?php print LANGNOTEUSA5ter ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
<?php
$data = aff_config_note_usa();
if (countTriade($data) == 0) {
	echo "<tr><td colspan='4' class='text-center text-muted' style='padding:20px;'>"
	   . "<i class='fa fa-inbox'></i>&nbsp;Aucune configuration enregistrée</td></tr>";
} else {
	for ($i = 0; $i < countTriade($data); $i++) {
		$id      = (int)$data[$i][0];
		$libelle = htmlspecialchars($data[$i][1]);
		$min     = $data[$i][2];
		$max     = $data[$i][3];
		print "<tr>";
		print "<td class='text-center'>$min</td>";
		print "<td class='text-center'>$max</td>";
		print "<td><b>$libelle</b></td>";
		print "<td style='text-align:center;'><button class='btn btn-danger' onclick='suppNoteUSA($id)'>"
		    . "<i class='fa fa-trash'></i>&nbsp;Supprimer</button></td>";
		print "</tr>";
	}
}
?>
      </tbody>
    </table>
  </div>
</div>

</div>

<script>
function suppNoteUSA(id) {
  alertify.confirm('Supprimer cette configuration ?', function(e) {
    if (e) { open('gestionnoteusa.php?idsupp=' + id, '_parent', ''); }
  });
}
</script>

<?php brmozilla($_SESSION["navigateur"]); ?>

<?php } ?>

<!-- // fin  -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>

</BODY></HTML>
