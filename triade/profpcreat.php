<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("menuadmin");

if (isset($_GET["suppidprof"])) {
	@delete_profp2($_GET["suppidprof"],$_GET["idclass"],$_GET["anneeScolaire"]);
}

$__successMsg = null;
if (isset($_POST["create"])) {
	for($i=0;$i<$_POST["nb"];$i++) {
		$saisie_classe=$_POST["saisie_classe_$i"];
		@create_profp($_POST["saisie_prof"],$saisie_classe,$anneeScolaire);
	}
	$__successMsg = LANGDONENR;
}
?>
<?php if ($__successMsg): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('<?php print addslashes($__successMsg) ?>'); });</script>
<?php endif; ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTITRE15?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- //  debut -->

<div style="display:flex;flex-direction:column;gap:16px;padding:12px 8px">

<!-- Filtre année scolaire -->
<div class="toolbar">
  <form method='post' action='profpcreat.php' style="display:inline-flex;align-items:center;gap:8px">
    <span class="form-label" style="margin:0"><?php print LANGBULL3 ?> :</span>
    <select name='anneeScolaire' class="form-control" onChange="this.form.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,10); ?>
    </select>
  </form>
</div>

<!-- Formulaire d'affectation -->
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="bi bi-person-badge"></i> <?php print LANGPER6 ?></span>
  </div>
  <div class="card-body">
    <form method='post' action='profpcreat.php'>

      <div class="form-row">
        <span class="form-label"><?php print LANGPER6 ?> :</span>
        <select name="saisie_prof" class="form-control">
          <option value="rien" style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
          <?php select_personne_2("ENS",35); ?>
        </select>
      </div>

      <div class="form-row" style="flex-direction:column;align-items:flex-start;gap:8px">
        <span class="form-label"><?php print LANGPER7 ?> :</span>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px 16px;width:100%;padding:10px;background:#f8f9ff;border:1px solid #e0e4f4;border-radius:8px">
          <?php
          $data=affClasse();
          for($i=0;$i<countTriade($data);$i++) {
              $nomclasse=$data[$i][1];
              $idclasse=$data[$i][0];
              print "<label style='display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer'>";
              print "<input type='checkbox' value='$idclasse' name='saisie_classe_$i' style='accent-color:#080A66'> $nomclasse";
              print "</label>";
          }
          ?>
        </div>
      </div>

      <input type='hidden' name='nb' value='<?php print countTriade($data) ?>' />
      <input type='hidden' name='anneeScolaire' value="<?php print $anneeScolaire ?>" />

      <div class="form-row" style="border-bottom:none;padding-top:8px;gap:8px">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT18?>","create");</script>
        <script language=JavaScript>buttonMagicRetour("javascript:history.back()");</script>
      </div>

    </form>
  </div>
</div>

<!-- Liste des affectations existantes -->
<?php
nettoyageProfP();
$data=aff_prof_p($anneeScolaire);
?>
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="bi bi-list-ul"></i> Affectations existantes</span>
  </div>
  <table class="table" style="margin:0">
    <thead>
      <tr>
        <th class="cc-th" style="width:45%">Enseignant</th>
        <th class="cc-th">Classe</th>
        <th class="cc-th" style="width:70px;text-align:center">Action</th>
      </tr>
    </thead>
    <tbody>
    <?php for($i=0;$i<countTriade($data);$i++) { ?>
      <tr class="cc-tr-data">
        <td><?php $nomprenom=recherche_personne($data[$i][0]); print ucwords(strtolower($nomprenom)); ?></td>
        <td><?php $data2=chercheClasse($data[$i][1]); print ucwords(preg_replace('/ /',"&nbsp;",$data2[0][1])); ?></td>
        <td style="text-align:center">
          <a href="profpcreat.php?suppidprof=<?php print $data[$i][0]?>&idclass=<?php print $data[$i][1]?>&anneeScolaire=<?php print $anneeScolaire ?>"
             style="color:#c62828;font-size:15px;text-decoration:none" title="<?php print LANGacce21 ?>">
            <i class="bi bi-trash"></i>
          </a>
        </td>
      </tr>
    <?php } ?>
    <?php if (countTriade($data)==0) { ?>
      <tr><td colspan="3" style="text-align:center;color:#888;font-style:italic;padding:12px">Aucune affectation</td></tr>
    <?php } ?>
    </tbody>
  </table>
</div>

</div>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
