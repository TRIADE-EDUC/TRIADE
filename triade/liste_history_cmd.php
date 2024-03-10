<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
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
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
$valide=0;

if ((VIESCOLAIREHISTORYCMD == "oui") && ($_SESSION["membre"] == "menuscolaire")) { $valide=1; }
if ($_SESSION["admin1"] == "Administrateur") { $valide=1; }
if ($_SESSION["membre"] == "menuadmin") { $valide=1; }
if ($valide == 0) {
        print "<font color=red class=T2><br><br><center>".LANGMESS37."</center></font>" ;
}else{

?>
<BR><center><font size=3>Liste des opérations effectuées </font><i>(400 dernières)</i></center><br>
<?php

include_once('librairie_php/db_triade.php');
if (file_exists("./data/install_log/access.log")) {
	print "<br><ul>Fichier LOG des activités des utilisateurs Triade : ";
	if (($_SESSION["membre"] == "menuadmin") || (isset($_SESSION['admin1']))) {
		print "<a href='visu_accesslog.php' target='_blank' ><img src='./image/stockage/txt.gif' align=center border=0 /></a>";
	}

	print "</ul>";
}
?>
<BR>
<table border="1" align=center width=95% bordercolor="#000000" style="border-collapse: collapse;" >
<tr>
<td align=center bgcolor="yellow">Date</td>
<td align=center bgcolor="yellow">Individu</td>
<td align=center bgcolor="yellow">Opération</td>
<td align=center bgcolor="yellow">Commentaire</td>
</tr>
<?php
$cnx=cnx();
$data=affHistoryCmd();
// $data : tab bidim - soustab 3 champs
// time_cmd,date_cmd,user_cmd,cmd,commentaire
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr>
	<td align=center width=60 bgcolor="#FFFFFF"><?php print dateForm($data[$i][1])?><br><?php print $data[$i][0]?></td>
	<td  bgcolor="#FFFFFF">&nbsp;<?php print $data[$i][2]?></td>
	<td  bgcolor="#FFFFFF">&nbsp;<?php print $data[$i][3]?></td>
	<td  bgcolor="#FFFFFF">&nbsp;<?php print $data[$i][4] ?></td>
	</tr>
	<?php
	}
?>
</table>
<?php
// deconnexion en fin de fichier
Pgclose();

} 
?>
        </BODY></HTML>

