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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<?php include("./librairie_php/lib_licence.php"); ?>
<title><?php print LANGBASE27 ?></title>
</HEAD>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<div style="padding:16px;display:flex;flex-direction:column;gap:12px">

  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-question-circle" style="margin-right:5px"></i><?php print LANGBASE27 ?></span>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:10px;font-size:12px;color:#333;line-height:1.7">

      <div>
        <?php print LANGBASE28 ?>
        <ul style="margin:4px 0 0 16px;padding:0">
          <li><?php print LANGBASE29 ?></li>
        </ul>
      </div>

      <div>
        <?php print LANGBASE30 ?>
        <ul style="margin:4px 0 0 16px;padding:0">
          <li><?php print "suppression des infos brevets" ?></li>
        </ul>
      </div>

      <div>
        <?php print LANGBASE32 ?>
        <ul style="margin:4px 0 0 16px;padding:0">
          <li><?php print LANGBASE33 ?></li>
          <li><?php print LANGBASE34 ?></li>
          <li><?php print LANGBASE35 ?></li>
        </ul>
      </div>

    </div>
  </div>

  <div style="text-align:center">
    <script language=JavaScript>buttonMagicFermeture();</script>
  </div>

</div>

</BODY></HTML>
