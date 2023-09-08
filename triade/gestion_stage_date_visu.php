<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
}
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_stage.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'>
<?php print LANGSTAGE18 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br>
<table align=center><tr><td align=center>
<script language=JavaScript>
buttonMagic("<?php print LANGSTAGE63 ?>","gestion_stage_date_visuall.php","visudate","width=700,height=600,scrollbars=yes,resizable=yes",'')
</script>
<?php 
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>
<br><br><br>
</td></tr></table>
<!-- // fin  -->
<table border=1 width=100%  bordercolor='#CCCCCC' style="border-collapse: collapse;" >
<?php
// connexion (après include_once lib_licence.php obligatoirement)
$cnx=cnx();
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
$data=listestagenum();
for($i=0;$i<count($data);$i++) {
	$data22=$data[$i][0];
	$nomstage=$data[$i][1];
	$datanum[$data22]=$nomstage;
}
print "<tr>";
print "<td></td>";
$nb=0;
foreach($datanum as $key => $value) {
		$nb++;
		if ($nb == 5) { break; }
		print "<td align='center'  bgcolor='#FFFFFF' bordercolor='#000000' >".LANGSTAGE19."&nbsp;N°&nbsp;".$key." <br> <b>".trunchaine($value,20)."</b></td>";
}
print "</tr>";
$data=listestageclasse();
for($i=0;$i<count($data);$i++) {
        $data22=$data[$i][0];
        $dataclasse[$data22]=$data22;
}
foreach($dataclasse as $key => $value) {
		$nb=0;
		$nomClasse=chercheClasse_nom($key);
		if ($nomClasse == "") continue;
		print "<tr>";
		print "<td width=5  bgcolor='#FFFFFF' bordercolor='#000000' >&nbsp;".preg_replace('/ /','&nbsp;',$nomClasse)."&nbsp;</td>";
		foreach($datanum as $key2 => $value2) {
			$nb++;
			if ($nb == 5) { break; }
			$datestage=listestageclassenum($key2,$key);
			print "<td align=center class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" bordercolor='#000000' >";
			if (($_SESSION["membre"] != "menupersonnel") && ($_SESSION["membre"] != "menuprof")) { 
				print "<a href='gestion_stage_date_modif2.php?id=$key2'>";
			}		
			print "$datestage";
			if ($_SESSION["membre"] != "menupersonnel") {
				print "</a>";
			}
			print "</td>";
		}
		print "</tr>";
}
?>
</table>
<!-- // fin  -->
</td></tr></table>


<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
// deconnexion en fin de fichier
	Pgclose();
?>
</BODY></HTML>
