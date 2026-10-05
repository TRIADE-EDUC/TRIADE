<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
error_reporting(0);
header('Content-type: text/html; charset=iso-8859-1');
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Accès non autorisé — Triade</title>
<style>
.ar-wrap {
    display:flex; align-items:center; justify-content:center;
    min-height:100vh; padding:40px 16px; box-sizing:border-box;
    background:linear-gradient(135deg,#0d1b4a 0%,#1a3a6a 60%,#1e4d8c 100%);
}
.ar-card {
    background:#fff; border-radius:16px;
    box-shadow:0 8px 40px rgba(0,0,0,.35);
    max-width:520px; width:100%; padding:48px 40px 36px; text-align:center;
}
.ar-icon { font-size:56px; color:#c0392b; margin-bottom:14px; }
.ar-title {
    font-family:Electrolize,'Trebuchet MS',Arial,sans-serif;
    font-size:22px; font-weight:800; color:#c0392b;
    letter-spacing:2px; text-transform:uppercase; margin:0 0 10px;
}
.ar-sub { font-size:13px; color:#444; margin:0 0 28px; line-height:1.6; }
.ar-footer { margin-top:28px; font-size:10px; color:#999; line-height:1.5; }
</style>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<div class="ar-wrap">
  <div class="ar-card">
    <div class="ar-icon">&#9888;</div>
    <div class="ar-title">ACCES NON AUTORISE</div>
    <div class="ar-sub">Pour acc&eacute;der &agrave; votre compte, vous devez vous connecter.</div>
    <table align='center'><tr><td><form method="post" action="index.html">
      <script language="JavaScript">buttonMagicSubmit("Connexion","create");</script>
    </form></td></tr></table>
    <div class="ar-footer">
      La <b>T</b>ransparence et la <b>R</b>apidit&eacute; de l'<b>I</b>nformatique
      <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>
      &copy; 2000 - <?php echo date("Y"); ?> TRIADE - Tous droits r&eacute;serv&eacute;s
    </div>
  </div>
</div>

</body></html>
