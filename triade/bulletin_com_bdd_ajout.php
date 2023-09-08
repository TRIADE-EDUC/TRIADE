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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php");
$commentaireeleve=urldecode($_GET['com']);
if ($commentaireeleve !='') {
	$cr=create_com_bulletin($commentaireeleve,$_SESSION["id_pers"]);
	if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION COM. BULLETIN",$_SESSION["nom"]." id :".$_SESSION["id_pers"] );
	}
	print "<br /><br /><center><font class=\"T2\">".LANGRESA69.".</font><br><br>";
}else {
	print "<br /><center><font class=\"T2\">".LANGMESS133." <br>".LANGOU."<br> ".LANGMESS134." !!!</font><br><br>";
}

?>
</center>
</br>
</body>
</html>
