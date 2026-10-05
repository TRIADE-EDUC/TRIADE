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
<form method="post" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des Unités d'enseignements</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('./librairie_php/db_triade.php');
include("./librairie_php/fonctions_vatel.php");
$cnx = cnx();
validerequete("menuadmin");

$vmu_success = "";
$nomclasse   = "";

if (isset($_POST["create"])) {
	$cr1 = vatel_modif_ue($_POST, $_POST['id_detail']);
	vatel_supp_ue($_POST['id_detail'], 'ue_detail');
	for ($i = 0; $i <= $_POST["nb"]; $i++) {
		$code_matiere = $_POST["code_matiere_$i"];
		if ($code_matiere > 0) {
			$idprof = $_POST["idprof_$i"];
			vatel_create_due_bis($code_matiere, $_POST['id_detail'], $idprof);
		}
	}
	if ($cr1) {
		$vmu_success = LANGDONENR . " — ATTENTION : METTRE À JOUR LES AFFECTATIONS DE LA CLASSE SUR LES DONNÉES UE !";
	}
}

$data_ue = vatel_liste_ue($_GET['id']);
// code_ue,code_classe,semestre,num_ue,nom_ue,coef_ue,ects,idpers_prof,nom_ue_en,annee_scolaire
?>

<?php if ($vmu_success): ?>
<script>document.addEventListener('DOMContentLoaded', function(){ alertify.success(<?php echo json_encode($vmu_success); ?>, 8); });</script>
<?php endif; ?>

<div style="padding:16px;">

<!-- Champs principaux -->
<div class="card">
  <div class="card-header">Modifier l'unité d'enseignement</div>
  <div class="card-body" style="padding:0;">
    <div class="form-row">
      <label class="form-label"><?php print LANGBULL3 ?></label>
      <select name='annee_scolaire' class="form-control" style="max-width:160px;">
        <?php filtreAnneeScolaireSelectNote($data_ue[0][9], 3); ?>
      </select>
    </div>
    <div class="form-row">
      <label class="form-label">Nom</label>
      <input type="text" name="nom_ue" class="form-control" maxlength="255"
             value="<?php print htmlspecialchars(stripslashes($data_ue[0][4])) ?>">
    </div>
    <div class="form-row">
      <label class="form-label">Code UE</label>
      <input type="text" name="matricule_ue" class="form-control" style="max-width:120px;" maxlength="15"
             value="<?php print htmlspecialchars(stripslashes($data_ue[0][10])) ?>">
    </div>
    <div class="form-row">
      <label class="form-label">Ordre d'apparition</label>
      <input type="text" name="num_ue" class="form-control" style="max-width:60px;"
             value="<?php print $data_ue[0][3] ?>">
      <span style="font-size:12px;color:#888;">(au sein du bulletin, de 0 à n)</span>
    </div>
    <div class="form-row">
      <label class="form-label">Coef.</label>
      <input type="text" name="coef_ue" class="form-control" style="max-width:60px;"
             value="<?php print $data_ue[0][5] ?>">
    </div>
    <div class="form-row">
      <label class="form-label">ECTS</label>
      <input type="text" name="ects_ue" class="form-control" style="max-width:60px;"
             value="<?php print $data_ue[0][6] ?>">
    </div>
    <div class="form-row">
      <label class="form-label">Classe</label>
      <select name='code_classe' class="form-control" style="max-width:220px;">
        <option value=''></option>
        <?php
        $data = affClasse();
        for ($i = 0; $i < countTriade($data); $i++) {
            $sel = ($data_ue[0][1] == $data[$i][0]) ? " selected" : "";
            if ($data_ue[0][1] == $data[$i][0]) $nomclasse = $data[$i][1];
            print "<option value='".$data[$i][0]."'$sel>".$data[$i][1]."</option>";
        }
        ?>
      </select>
    </div>
    <div class="form-row">
      <label class="form-label">Semestre</label>
      <select name="semestre" class="form-control" style="max-width:100px;">
        <option value="0" <?php if ($data_ue[0][2] == 0) print 'selected'; ?>>1 et 2</option>
        <option value="1" <?php if ($data_ue[0][2] == 1) print 'selected'; ?>>1</option>
        <option value="2" <?php if ($data_ue[0][2] == 2) print 'selected'; ?>>2</option>
      </select>
    </div>
    <div class="form-row">
      <label class="form-label">Professeur Principal</label>
      <select name='idpers_profp' class="form-control" style="max-width:260px;">
        <?php
        $idprofp = $data_ue[0][7];
        if ($idprofp > 0) {
            print "<option id='select1' value='$idprofp' selected='selected'>"
                . strtoupper(recherche_personne_nom($idprofp, 'ENS'))." "
                . ucfirst(recherche_personne_prenom($idprofp, 'ENS'))."</option>";
        }
        ?>
        <option id='select0' value='0'><?php print LANGCHOIX ?></option>
        <?php select_personne_2('ENS', '40'); ?>
      </select>
    </div>
  </div>
</div>

<!-- Matières -->
<div class="card">
  <div class="card-header">Matières associées</div>
  <div class="card-body" style="padding:0;">
    <table class="table table-hover">
      <thead>
        <tr>
          <th style="width:36px;"></th>
          <th>Matière</th>
          <th>Enseignant</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $data_detail = vatel_liste_uedetail($_GET['id']);
      $data = affMatiere();

      for ($i = 0; $i < countTriade($data); $i++) {
          $checked        = "";
          $code_enseignant = "0";
          if ($data[$i][1] != "") {
              for ($j = 0; $j < countTriade($data_detail); $j++) {
                  if ($data_detail[$j][2] == $data[$i][0]) {
                      $checked         = "checked='checked'";
                      $code_enseignant = $data_detail[$j][3];
                      break;
                  }
              }
              print "<tr>";
              print "<td style='text-align:center;'><input type='checkbox' $checked name='code_matiere_$i' value='".$data[$i][0]."'></td>";
              print "<td>".strtolower(stripslashes($data[$i][1]))."</td>";
              print "<td><select name='idprof_$i' class='form-control'>";
              if ($code_enseignant > 0) {
                  print "<option id='select1' value='$code_enseignant'>"
                      . ucfirst(recherche_personne_nom($code_enseignant, "ENS"))." "
                      . ucfirst(recherche_personne_prenom($code_enseignant, "ENS"))."</option>";
              } else {
                  print "<option id='select0' value='0'>".LANGCHOIX."</option>";
              }
              select_personne_2('ENS', '20');
              print "</select></td></tr>";
          }
      }
      Pgclose();
      ?>
      </tbody>
    </table>
  </div>
</div>

<input type="hidden" value="<?php print (int)$_GET['id'] ?>" name="id_detail">
<input type='hidden' name='nb' value='<?php print countTriade($data) ?>'>

<div style="padding:10px 0;">
  <script language=JavaScript>buttonMagicSubmit("<?php print "Valider modif." ?>","create");</script>
  <script language=JavaScript>buttonMagic("<?php print "Lister / Modifier" ?>","vatel_list_ue.php","_parent","","");</script>
  <script language=JavaScript>buttonMagic("<?php print "Supprimer" ?>","vatel_supp_ue.php?id=<?php print (int)$_GET['id'] ?>","_parent","","");</script>
</div>

</div>

<?php brmozilla($_SESSION["navigateur"]); ?>

<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
