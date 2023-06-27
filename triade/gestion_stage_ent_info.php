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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Information Entreprise</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<BR><BR>
<ul>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if (isset($_GET["id"])) {
	$data=recherche_activite_id($_GET["id"]);
	//id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus
	for($i=0;$i<count($data);$i++) {
?>
		<font class=T2>
		<?php print LANGSTAGE39 ?> : <b><font color=red><?php print $data[$i][1] ?></font></b> <br><br>
		<?php print LANGSTAGE40 ?> : <b> <?php print  $data[$i][7] ?></b><br><br>
		<?php print LANGSTAGE28 ?> :<b> <?php print $data[$i][3] ?></b>  <br><br>
		<?php print LANGSTAGE30 ?> : <b>  <?php print $data[$i][5] ?> </b> <br><br>
		<?php print LANGSTAGE29 ?> :<b> <?php print $data[$i][4] ?></b> <br><br>
		<?php print LANGSTAGE27 ?> : <b><?php print $data[$i][2] ?> </b><br><br>
		<?php print LANGSTAGE42 ?> : <b><?php print $data[$i][8] ?> / <?php print $data[$i][9] ?></b> <br><br>
		<?php print LANGSTAGE36 ?> :<b> <?php print $data[$i][10] ?> </b><br><br>
		<?php print LANGSTAGE37 ?> :<b> <?php print $data[$i][11] ?></b> <br><br>
 		</font>

<?php
	}
}
?>
</ul>
<BR>
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture(); </script></td></tr></table><br>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
        </BODY></HTML>



