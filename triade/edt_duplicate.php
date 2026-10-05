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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTMESS490 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="padding:12px 8px">

<?php
if (isset($_POST["duplique"])) {
    duplicateEDT($_POST["idclasseSource"],$_POST["idclasseDestination"],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
    print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGDONENR)."'); });</script>";
}

$data=affClasseSansOffline(); //code_class,libelle,desclong,offline,idsite
?>

<div class="card">
  <div class="card-body">
    <form method="post" action="edt_duplicate.php" name="formulaire">

      <div class="form-row">
        <span class="form-label">Classe source</span>
        <select name="idclasseSource" class="form-control">
          <option id='select0'><?php print LANGCHOIX ?></option>
          <?php for($i=0;$i<countTriade($data);$i++) {
              print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>";
          } ?>
        </select>
      </div>

      <div class="form-row">
        <span class="form-label">Classe destination</span>
        <select name="idclasseDestination" class="form-control">
          <option id='select0'><?php print LANGCHOIX ?></option>
          <?php for($i=0;$i<countTriade($data);$i++) {
              print "<option id='select1' value='".$data[$i][0]."'>".$data[$i][1]."</option>";
          } ?>
        </select>
      </div>

      <div class="form-row">
        <span class="form-label"><?php print LANGTMESS491 ?> — <?php print LANGMESS109 ?></span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" name="saisie_date_debut" size="12" class="form-control" style="max-width:120px" onKeyPress="onlyChar(event)">
          <?php
          include_once("librairie_php/calendar.php");
          calendar("idZ1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0");
          ?>
        </div>
      </div>

      <div class="form-row">
        <span class="form-label"><?php print LANGMESS110 ?></span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" name="saisie_date_fin" onclick="this.value=''" size="12" class="form-control" style="max-width:120px" onKeyPress="onlyChar(event)">
          <?php calendar("idZ2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0"); ?>
        </div>
      </div>

      <div class="form-row" style="border-bottom:none;padding-top:8px;gap:8px">
        <script language="JavaScript">buttonMagicSubmit("<?php print VALIDER ?>","duplique")</script>
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
