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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>

<title>Ajout d'une activité</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<BR>
<form method="post" name="formulaire" >
<center>
<font class=T2><?php print LANGSTAGE23 ?> :</font> <input type=text name=activite size=40  maxlength=60>
</center>

<BR>&nbsp;&nbsp;
<table align=center>
<tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture()</script>&nbsp;&nbsp;
</td></tr></table>
</form>
<?php
if (isset($_POST["create"])) {
	if (strlen($_POST["activite"]) >= 2) {
	 		$cr=activite_ajout($_POST["activite"]);
        		if($cr == 1){
                		// alertJs("Activité Enregistrée -- Service Triade");
				history_cmd($_SESSION["nom"],"AJOUT","ACTIVITE STAGE");
			}
     	}
	print "<script>parent.window.close();</script>"	;
}

Pgclose();
?>
</BODY></HTML>
