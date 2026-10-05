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
include("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<table border="0" cellpadding="0" cellspacing="0" width="100%" height='100%'>
<tr>
<td>
<!-- // fin  -->
<?php
$acces="<iframe  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=auto name=forum src='./forum/forum.php' width='100%' height='100%'></iframe>";
$msgAccesRefuse = '<div style="margin:32px auto;max-width:520px;background:#fff3cd;border:1px solid #ffc107;border-radius:7px;padding:20px 24px;display:flex;align-items:flex-start;gap:14px;">'
    . '<i class="bi bi-shield-exclamation" style="font-size:1.8em;color:#e67e22;margin-top:2px;"></i>'
    . '<div><b style="color:#7a4500;">Accès non autorisé</b><br>'
    . '<span style="font-size:.9em;color:#555;">Cet accès n\'est pas autorisé par l\'administrateur Triade.<br>L\'Équipe Triade.</span></div></div>';

if ($_SESSION["membre"] == "menueleve") {
    print (defined('ACCESFORUMELEVE') && ACCESFORUMELEVE == "non") ? $msgAccesRefuse : $acces;
} elseif ($_SESSION["membre"] == "menuprof") {
    print (defined('ACCESFORUMPROF') && ACCESFORUMPROF == "non") ? $msgAccesRefuse : $acces;
} elseif ($_SESSION["membre"] == "menuparent") {
    print (defined('ACCESFORUMPARENT') && ACCESFORUMPARENT == "non") ? $msgAccesRefuse : $acces;
} else {
    print $acces;
}

?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
