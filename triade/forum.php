<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<table border="0" cellpadding="0" cellspacing="0" width="100%" height='100%'>
<tr>
<td>
<!-- // fin  -->
<?php
$acces="<iframe  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=auto name=forum src='./forum/forum.php' width='100%' height='100%'></iframe>";
if ($_SESSION["membre"] == "menueleve") {
	if (ACCESFORUMELEVE == "non") {
    		print "<center><font class=T2 color='red'>Cet accès n'est pas autorisé par l'administrateur Triade.<br><br>L'Equipe Triade.</font></center>";
	}else{
		print $acces;
	}
}elseif ($_SESSION["membre"] == "menuprof") {
	if (ACCESFORUMPROF == "non") {
    		print "<center><font class=T2 color='red'>Cet accès n'est pas autorisé par l'administrateur Triade.<br><br>L'Equipe Triade.</font></center>";
	}else{
		print $acces;
	}
}elseif ($_SESSION["membre"] == "menuparent") {
	if (ACCESFORUMPARENT == "non") {
    		print "<center><font class=T2 color='red'>Cet accès n'est pas autorisé par l'administrateur Triade.<br><br>L'Equipe Triade.</font></center>";
	}else{
		print $acces;
	}
}else{
	print $acces;
}

?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
