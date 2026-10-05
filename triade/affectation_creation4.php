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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<style>
.tabnormal2            { background:#f5f7ff; }
.tabnormal2 td         { padding:5px 8px; font-size:12px; border-bottom:1px solid #e4e9f8; vertical-align:middle; }
.tabover               { background:#e8ecff !important; }
.tabover td            { padding:5px 8px; font-size:12px; border-bottom:1px solid #e4e9f8; vertical-align:middle; }
</style>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond2'>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);
if (!empty($_SESSION["adminplus"])) {
	$cnx=cnx();
	verif_table_groupe();
	verif_table_matiere();
	$tri=$_POST['saisie_tri'];
	$anneeScolaire=$_POST["anneeScolaire"];
	$cdata=chercheClasse($_POST["saisie_classe_envoi"]);
	$cid=$cdata[0][0];
	$cnom=trim($cdata[0][1]);
	if(createAffectation($cdata)):
		echo "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGPER23." $cnom ".LANGPER23bis)."'); });</script>";
		history_cmd($_SESSION["nom"],LANGaffec_cre31,"$cnom");
	else:
		echo "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error('".addslashes(LANGPER23." $cnom ".LANGPER24)."'); });</script>";
	endif;
	$sql="SELECT CONCAT(trim(m.libelle),' ',trim(m.sous_matiere)),CONCAT(upper(trim(p.nom)),' ',trim(p.prenom)),a.coef,trim(g.libelle),langue,visubull,visubullbtsblanc,nb_heure,ects,num_semestre_info,coef_certif,note_planche  FROM {$prefixe}matieres m,{$prefixe}personnel p,{$prefixe}affectations a,{$prefixe}groupes g WHERE a.code_matiere = m.code_mat  AND a.code_prof = p.pers_id AND a.code_groupe = g.group_id AND p.type_pers = 'ENS' AND a.code_classe = '$cid' AND trim = '$tri' AND a.annee_scolaire='$anneeScolaire' ORDER BY a.ordre_affichage";
	$curs=execSql($sql);
	$data=chargeMat($curs);
	freeResult($curs);
}
?>

<table border="0" cellpadding="3" cellspacing="1" bgcolor="#0B3A0C" width="100%">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGTITRE17?> <font id="color2"><?php print $cnom?></font></font></b>
  &nbsp;<font id='menumodule1'>pour l'ann&eacute;e scolaire </font><font id="color2"><?php print $anneeScolaire?></font>
</td></tr>
<tr id='cadreCentral0'>
<td>

<div style="padding:8px 4px">

  <div style="margin-bottom:10px">
    <a href="#" onclick="print_affectation()" style="color:#3949ab;text-decoration:none;font-size:12px;display:inline-flex;align-items:center;gap:5px">
      <i class="bi bi-printer" style="font-size:15px"></i>
      <?php print LANGPER22?>
    </a>
  </div>

  <div style="overflow-x:auto">
    <table class="table" style="min-width:700px;margin:0">
      <thead>
        <tr>
          <th class="cc-th"><?php print LANGPER17?></th>
          <th class="cc-th"><?php print LANGPER18?></th>
          <th class="cc-th" style="text-align:center"><?php print LANGPER19?></th>
          <th class="cc-th"><?php print LANGPER20?></th>
          <th class="cc-th"><?php print LANGPER21?></th>
          <th class="cc-th" style="text-align:center">Visu</th>
          <th class="cc-th" style="text-align:center">Visu&nbsp;BTS&nbsp;Blanc</th>
          <th class="cc-th" style="text-align:center">Nb&nbsp;h.</th>
          <th class="cc-th" style="text-align:center">ECTS</th>
          <th class="cc-th" style="text-align:center">Info&nbsp;Sem.</th>
          <th class="cc-th" style="text-align:center">Coef&nbsp;Certif</th>
          <th class="cc-th" style="text-align:center">Note&nbsp;Plancher</th>
        </tr>
      </thead>
      <tbody>
        <?php htmlTrMat($data); ?>
      </tbody>
    </table>
  </div>

</div>

</td>
</tr>
</table>

<div style="text-align:center;padding:14px">
  <button type="button" onclick="parent.window.close()"
          style="background:#080A66;color:#fff;border:none;border-radius:6px;padding:8px 28px;font-size:13px;cursor:pointer;font-family:Electrolize,Arial,sans-serif;display:inline-flex;align-items:center;gap:6px">
    <i class="bi bi-x-circle"></i> <?php print LANGFERMERFEN?>
  </button>
</div>

<?php Pgclose() ?>
</BODY>
</HTML>
