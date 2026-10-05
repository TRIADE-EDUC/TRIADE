<?php
session_start();
error_reporting(0);
if (empty($_SESSION["nom"])) {
    header('Location: ../acces_refuse.php');
    exit;
}
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
.frm-thread-icon { color:#080A66; font-size:.78em; margin-right:5px; }
.frm-reply-icon  { color:#8a94b8; font-size:.78em; margin-right:5px; }
.frm-new-badge   { display:inline-block; background:#e8edff; color:#080A66; font-size:.68em; padding:1px 7px; border-radius:10px; margin-left:6px; vertical-align:middle; font-weight:600; }
.frm-empty-msg   { padding:32px 16px; text-align:center; color:#7a82a6; }
.frm-empty-msg i { font-size:2.2em; display:block; margin-bottom:10px; color:#c5cadf; }
</style>
</head>
<body id='bodyforum' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("../librairie_php/lib_licence_forum.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>
  <i class="bi bi-chat-dots"></i>
  <?php
  if ($_SESSION["membre"] == "menueleve")    echo "Forum Élève";
  elseif ($_SESSION["membre"] == "menuadmin")    echo "Forum Direction";
  elseif ($_SESSION["membre"] == "menuparent")   echo "Forum Parent";
  elseif ($_SESSION["membre"] == "menuprof")     echo "Forum Enseignant";
  elseif ($_SESSION["membre"] == "menuscolaire") echo "Forum Vie Scolaire";
  if (defined("LANGFORUM1")) echo " &mdash; " . LANGFORUM1;
  ?>
  </font></b>
</td></tr>
<tr id='cadreCentral0'>
<td valign='top'>
<?php
if (!file_exists("../data/forum")) {
    @mkdir("../data/forum", 0755);
    $text = "<Files \"*\">\nOrder Deny,Allow\nDeny from all\n</Files>";
    $fp = fopen("../data/forum/.htaccess", "w");
    fwrite($fp, $text);
    fclose($fp);
}
$repforum = "../data/forum/" . $_SESSION['membre'];
if (!file_exists($repforum)) {
    @mkdir($repforum, 0755);
    $text = "<Files \"*\">\nOrder Deny,Allow\nDeny from all\n</Files>";
    $fp = fopen("{$repforum}/.htaccess", "w");
    fwrite($fp, $text);
    fclose($fp);
}
include_once("./listemessages.php");
?>
</td></tr></table>
</BODY></HTML>
