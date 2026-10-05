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
<?php
include_once("./librairie_php/lib_licence.php");
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

<div style="padding:12px 8px">

<?php
if (isset($_POST["supp"])) {
    $dateDebut=dateFormBase($_POST["datedebutEDT"]);
    $dateFin=dateFormBase($_POST["datefinEDT"]);
    $dateD=preg_replace('/-/','',$dateDebut);
    $dateF=preg_replace('/-/','',$dateFin);
    if ((trim($_POST["datedebutEDT"]) != "") && (trim($_POST["datedebutEDT"]) != "")) {
        if ($dateF < $dateD) {
            print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error('ERREUR SUR LES DATES !'); });</script>";
        }else{
            suppPeriodeEdt($dateDebut,$dateFin);
            print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('Suppression effectuée.'); });</script>";
        }
    }
}
?>

<div class="card">
  <div class="card-body">
    <form action='gestion_suppr_edt.php' method='post' name="form2">

      <div class="form-row">
        <span class="form-label">Période à supprimer — du</span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" name="datedebutEDT" value="<?php print htmlspecialchars($_POST["datedebutEDT"] ?? '') ?>"
            onclick="this.value=''" size="12" class="form-control" style="max-width:120px" onKeyPress="onlyChar(event)">
          <?php
          include_once("librairie_php/calendar.php");
          calendar("id111","document.form2.datedebutEDT",$_SESSION["langue"],"0","0");
          ?>
          <span style="font-size:12px;color:#555">au</span>
          <input type="text" name="datefinEDT" value="<?php print htmlspecialchars($_POST["datefinEDT"] ?? '') ?>"
            onclick="this.value=''" size="12" class="form-control" style="max-width:120px" onKeyPress="onlyChar(event)">
          <?php calendar("id222","document.form2.datefinEDT",$_SESSION["langue"],"0","0"); ?>
        </div>
      </div>

      <div class="form-row" style="border-bottom:none;padding-top:8px;gap:8px">
        <script language="JavaScript">buttonMagicSubmit3("Valider la suppression","supp","")</script>
        <script language="JavaScript">buttonMagicRetour("edt.php","_self")</script>
      </div>

    </form>
  </div>
</div>

</div>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php PgClose(); ?>
</BODY></HTML>
