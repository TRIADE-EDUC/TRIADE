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
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
    if ((!verifDroit($_SESSION["id_pers"],"edt")) && (!verifDroit($_SESSION["id_pers"],"AESH"))) {
        accesNonReserveFen();
        exit;
    }
}else{
    validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGEDT9 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="display:flex;flex-direction:column;gap:16px;padding:12px 8px">

<!-- ── Import EDT ── -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Import EDT</span>
  </div>
  <div class="card-body">
    <form method="post" action="edt_import.php" name="formulaire">
      <div class="form-row">
        <span class="form-label"><?php print LANGEDT5 ?></span>
        <input type="radio" name="edt" value="VT" style="margin-top:3px">
      </div>
      <div class="form-row">
        <span class="form-label"><?php print LANGEDT6 ?></span>
        <input type="radio" name="edt" value="EX" style="margin-top:3px">
      </div>
      <div class="form-row" style="border-bottom:none;padding-top:8px">
        <script language="JavaScript">buttonMagicSubmit("<?php print VALIDER ?>","create")</script>
      </div>
    </form>
  </div>
</div>

<!-- ── Accès rapide ── -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Accès rapide</span>
  </div>
  <div class="card-body" style="padding:0">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-bottom:1px solid #eef0f8">
      <span style="font-size:12px;color:#333"><?php print LANGEDT7 ?></span>
      <input type="button" class="btn btn-action" onclick="open('edt_visu.php','edt','width=1400,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')" value="<?php print LANGBREVET1 ?>">
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-bottom:1px solid #eef0f8">
      <span style="font-size:12px;color:#333">Importer un EDT à Triade via TRIADE-COPILOT</span>
      <input type="button" class="btn btn-action" onclick="open('edt_import_ia.php','_parent','')" value="<?php print LANGBREVET1 ?>">
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-bottom:1px solid #eef0f8">
      <span style="font-size:12px;color:#333">Importer un EDT via un fichier csv</span>
      <input type="button" class="btn btn-action" onclick="open('edt_import_cvs.php','_parent','')" value="<?php print LANGBREVET1 ?>">
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-bottom:1px solid #eef0f8">
      <span style="font-size:12px;color:#333"><?php print LANGMESS229 ?></span>
      <input type="button" class="btn btn-action" onclick="open('gestion_vacation_horaire.php','_parent','')" value="<?php print LANGBREVET1 ?>">
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-bottom:1px solid #eef0f8">
      <span style="font-size:12px;color:#333"><?php print LANGTMESS489 ?></span>
      <input type="button" class="btn btn-action" onclick="open('edt_duplicate.php','_parent','')" value="<?php print LANGBREVET1 ?>">
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-bottom:1px solid #eef0f8">
      <span style="font-size:12px;color:#333">Remplacer un enseignant</span>
      <input type="button" class="btn btn-action" onclick="open('edt_remplace_enseignant.php','_parent','')" value="<?php print LANGBREVET1 ?>">
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 16px">
      <span style="font-size:12px;color:#333"><?php print LANGMESS228 ?></span>
      <input type="button" class="btn btn-action" onclick="open('gestion_suppr_edt.php','_parent','')" value="<?php print LANGBREVET1 ?>">
    </div>
  </div>
</div>

<!-- ── Période de l'EDT ── -->
<?php
if (isset($_POST["createEDT"])) {
    enr_parametrage("datefinEDT",$_POST["datefinEDT"]);
    enr_parametrage("datedebutEDT",$_POST["datedebutEDT"]);
}
$datefinEDT=aff_enr_parametrage("datefinEDT");
$datedebutEDT=aff_enr_parametrage("datedebutEDT");
?>
<div class="card">
  <div class="card-header">
    <span class="card-title"><?php print LANGMESS230 ?></span>
  </div>
  <div class="card-body">
    <form action='edt.php' method='post' name="form2">
      <input type='hidden' name='hauteur' value='<?php print $hauteur ?>'>
      <div class="form-row">
        <span class="form-label"><?php print LANGMESS109 ?></span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" name="datedebutEDT" value="<?php print $datedebutEDT[0][1] ?>" onclick="this.value=''" size="12" class="form-control" style="max-width:120px" onKeyPress="onlyChar(event)">
          <?php
          include_once("librairie_php/calendar.php");
          calendarEDT("id111","document.form2.datedebutEDT",$_SESSION["langue"],"0","0");
          ?>
        </div>
      </div>
      <div class="form-row">
        <span class="form-label"><?php print LANGMESS110 ?></span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" name="datefinEDT" value="<?php print $datefinEDT[0][1] ?>" onclick="this.value=''" size="12" class="form-control" style="max-width:120px" onKeyPress="onlyChar(event)">
          <?php calendarEDT("id222","document.form2.datefinEDT",$_SESSION["langue"],"0","0"); ?>
        </div>
      </div>
      <div class="form-row" style="border-bottom:none;padding-top:8px">
        <script language="JavaScript">buttonMagicSubmit3("<?php print "ok" ?>","createEDT","")</script>
      </div>
    </form>
  </div>
</div>

<!-- ── Emploi du temps image ── -->
<?php
if (isset($_POST["suppimg"])) {
    @unlink("data/image_pers/".$_POST["numimg"]."_edt.jpg");
    @unlink("data/image_pers/".$_POST["numimg"]."_edt.pdf");
    print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGTMESS430)." ".addslashes(LANGTMESS428)."'); });</script>";
}

if (isset($_POST["img"])) {
    $photo=$_FILES['photo']['name'];
    $type=$_FILES['photo']['type'];
    $tmp_name=$_FILES['photo']['tmp_name'];
    $size=$_FILES['photo']['size'];
    $idclasse=$_POST["saisie_classe"];
    if ((!empty($photo)) && ($size <= 2000000)) {
        $type=str_replace("image/","",$type);
        $type=str_replace("application/","",$type);
        $type=str_replace("pjpeg","jpg",$type);
        $type=str_replace("x-png","jpg",$type);
        $type=str_replace("jpeg","jpg",$type);
        if (verifImageJpg($type)) {
            $nomphoto="${idclasse}_edt.$type";
            move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
            history_cmd($_SESSION["nom"],"EDT","AJOUT $nomphoto");
            print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGTMESS429)." ".addslashes(LANGTMESS428)."'); });</script>";
        }elseif(verifPdf($type)) {
            $nomphoto="${idclasse}_edt.pdf";
            move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
            history_cmd($_SESSION["nom"],"EDT","AJOUT $nomphoto");
            print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGTMESS427)." ".addslashes(LANGTMESS428)."'); });</script>";
        }else{
            print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error('".addslashes(LANGTRONBI3)."'); });</script>";
        }
    } else {
        print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error('".addslashes(LANGTRONBI4)."'); });</script>";
    }
}

$dataClasses=affClasse();
$deleteRows="";
for($i=0;$i<countTriade($dataClasses);$i++) {
    if (file_exists("./data/image_pers/".$dataClasses[$i][0]."_edt.jpg") || file_exists("./data/image_pers/".$dataClasses[$i][0]."_edt.pdf")) {
        $deleteRows.="<form method='post' style='display:flex;align-items:center;gap:10px;margin-bottom:6px;'>";
        $deleteRows.="<span style='font-size:12px;color:#333;flex:1'>Classe : <b>".htmlspecialchars($dataClasses[$i][1])."</b></span>";
        $deleteRows.="<input type='hidden' name='numimg' value='".htmlspecialchars($dataClasses[$i][0])."'>";
        $deleteRows.="<input type='submit' name='suppimg' value='Supprimer' class='btn btn-danger'>";
        $deleteRows.="</form>";
    }
}
?>
<div class="card">
  <div class="card-header">
    <span class="card-title"><?php print LANGMESS231 ?></span>
  </div>
  <div class="card-body">
    <form method="post" enctype="multipart/form-data">
      <div class="form-row">
        <span class="form-label"><?php print LANGMESS233 ?></span>
        <select id="saisie_classe" name="saisie_classe" class="form-control">
          <option id='select0'><?php print LANGCHOIX?></option>
          <?php select_classe(); ?>
        </select>
      </div>
      <div class="form-row">
        <span class="form-label">Fichier</span>
        <input type="file" name="photo" class="form-control">
      </div>
      <div style="font-size:11px;color:#888;font-style:italic;padding:2px 0 10px"><?php print LANGMESS232 ?></div>
      <div class="form-row" style="border-bottom:none;padding-top:4px">
        <input type="submit" name="img" value="<?php print LANGENR ?>" class="btn btn-primary">
      </div>
    </form>
    <?php if ($deleteRows) { ?>
    <div style="margin-top:16px;border-top:1px solid #eef0f8;padding-top:14px">
      <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">Supprimer un EDT image</div>
      <?php print $deleteRows; ?>
    </div>
    <?php } ?>
  </div>
</div>

</div>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php PgClose(); ?>
</BODY></HTML>
