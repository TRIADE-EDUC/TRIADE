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
include("./librairie_php/lib_licence.php"); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title><?php print LANGRESA44 ?></title>
</head>
<body id="bodyfond2" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$datedepart=dateDMY();
if (isset($_GET["saisiedate"])) {
	$datedepart=$_GET["saisiedate"];
}
$equipement=$_GET["equip"];
?>
<BR><center><font class="T2"><U><?php print LANGRESA51?></U> : <b><?php print recherche_equip($_GET[equip]) ?></b> <br>
<?php print LANGRESA48?> <?php print $datedepart?> </font></center>
<br><br>
<form method=get name=formulaire>
&nbsp;&nbsp;&nbsp;<font class="T2"><?php print LANGRESA49?></font> <input type=text size=10 name=saisiedate value="<?php print $datedepart?>" class="bouton2"  readonly>
<?php
include_once("librairie_php/calendar.php");
calendar("id1","document.formulaire.saisiedate",$_SESSION["langue"],"0");
?>

<input type=hidden name=equip value="<?php print $equipement?>" >
<input type=submit value="ok" class="bouton2"  >
</form>
<BR>
<table border="1" align=center width=95% bordercolor="#000000" bgcolor="#FFFFFF">
<?php
$data=planning_equipement($equipement,$datedepart);
//idmatos,idqui,quand,heure_depart,heure_fin,info,valider
?>
<tr bgcolor=yellow>
<td align=center><?php print LANGRESA36 ?></td>
<td align=center><?php print LANGRESA37 ?></td>
<td align=center><?php print LANGRESA38 ?></td>
<td align=center><?php print LANGRESA39 ?></td>
<td align=center><?php print LANGRESA40 ?></td>
<td align=center><?php print LANGRESA41 ?></td>
</tr>
<?php
for($i=0;$i<count($data);$i++) {
	$equip=$data[$i][6];
	if (DBTYPE == "pgsql") {
		if ($equip == TRUE) {
			$equip="&nbsp;".LANGRESA42."&nbsp;";
		}
		if ($equip == FALSE) {
			$equip="&nbsp;".LANGRESA43."&nbsp;";
		}
	}
	if (DBTYPE == "mysql")  {
		if ($equip == 1) {
			$equip="&nbsp;".LANGRESA42."&nbsp;";
		}
		if ($equip == 0) {
			$equip="&nbsp;".LANGRESA43."&nbsp;";
		}
	}
	print "<tr>";
	print "<td width=5>&nbsp;".dateForm($data[$i][2])."&nbsp;</td>";
	print "<td width=5>&nbsp;".$data[$i][3]."&nbsp;</td>";
	print "<td width=5>&nbsp;".$data[$i][4]."&nbsp;</td>";
	print "<td>&nbsp;".recherche_personne($data[$i][1])."</td>";
	print "<td>&nbsp;".$data[$i][5]."</td>";
	print "<td align=center width=5>".$equip."</td>";
	print "</tr>";

}
?>
</table>
<BR><BR>
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td></tr></table><br>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
        </BODY></HTML>



