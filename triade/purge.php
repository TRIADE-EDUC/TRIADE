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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGPURG1 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<script language="JavaScript">
function suite() {
  var confirmation = confirm('<?php print LANGPUR4 ?>','');
  if (confirmation) {
    location.href = "./base_de_donne_key.php?base=purge";
  }
}
</script>

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <div class="card" style="border-top:3px solid #c62828">
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

      <div style="background:#fff9f9;border:1px solid #f5c6cb;border-radius:6px;padding:12px 14px;display:flex;align-items:flex-start;gap:10px">
        <i class="bi bi-shield-exclamation" style="color:#c62828;font-size:18px;flex-shrink:0;margin-top:1px"></i>
        <p style="margin:0;font-size:12px;color:#c62828;font-weight:600;line-height:1.6">
          <?php print LANGPUR3 ?>
        </p>
      </div>

      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <button type="button" onclick="suite();"
                class="btn btn-danger" style="display:inline-flex;align-items:center;gap:7px">
          <i class="bi bi-trash3-fill"></i>
          <?php print LANGBTS ?>
        </button>
        <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
      </div>

    </div>
  </div>

</div>

<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
