<?php
session_start();
error_reporting(0);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script language="JavaScript" src="../librairie_js/acces.js"></script>
<script language="JavaScript" src="../librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="../librairie_js/function.js"></script>
<title>Triade - Forum</title>
<style>
.frm-post-card    { max-width:720px; margin:14px auto; border-radius:7px; box-shadow:0 2px 8px rgba(8,10,102,.11); overflow:hidden; }
.frm-post-header  { background:#080A66; color:#fff; padding:10px 16px; font-weight:600; font-size:.95em; }
.frm-post-body    { background:#fff; padding:16px 20px; }
.frm-post-row     { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.frm-post-row label { width:68px; text-align:right; font-size:.85em; color:#555; flex-shrink:0; }
.frm-post-row input[type=text] {
    flex:1; padding:6px 10px; border:1px solid #c5cadf; border-radius:5px;
    font-size:.9em; background:#f8f9ff;
}
.frm-post-textarea {
    width:100%; padding:10px; border:1px solid #c5cadf; border-radius:5px;
    font-size:.9em; line-height:1.6; resize:vertical; background:#f8f9ff;
    box-sizing:border-box; min-height:160px;
}
.frm-post-footer  { background:#f8f9ff; border-top:1px solid #eaecf7; padding:10px 16px; display:flex; gap:10px; align-items:center; }
</style>
</head>
<body id='bodyforum' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("../librairie_php/lib_licence_forum.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>
  <i class="bi bi-pencil-square"></i>
  <?php echo defined('LANGFORUM5') ? LANGFORUM5 : 'Poster un message'; ?>
  </font></b>
  <?php if (defined('LANGFORUM6')): ?>
  &nbsp;&mdash;&nbsp;
  <a href="#" onclick="open('charte.html','charte','width=300,height=500,scrollbars=yes')" style="color:#FFD700;">
    <i class="bi bi-file-text"></i> <?php echo LANGFORUM6; ?>
  </a>
  <?php endif; ?>
</td></tr>
<tr id='cadreCentral0'>
<td valign='top'>
<?php include_once("./post.php"); ?>
</td></tr></table>
</BODY></HTML>
