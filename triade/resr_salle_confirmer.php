<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");	
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
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
<?php print LANGRESA4?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<form method=post name='formulaire' >
<table width=100% border=1 bordercolor="#000000" bgcolor="#FFFFFF" style="border-collapse: collapse;" >
<tr>
<td bgcolor=yellow align=center width=5>&nbsp;Date&nbsp;</td>
<td bgcolor=yellow align=center ><?php print LANGRESA59?></td>
<td bgcolor=yellow align=center >&nbsp;<?php print LANGVALIDE?>&nbsp;<input type='radio' onclick="checkRadio('1')" name="tous"  />&nbsp;</td>
<td bgcolor=yellow align=center ><?php print LANGRESA63 ?>&nbsp;<input type='radio' onclick="checkRadio('0')" name="tous" />&nbsp;</td>
</tr>


<?php

if (isset($_POST["create"])) {
        for($i=1;$i<=$_POST["nbtotal"];$i++) {
                $valide="valide$i";
                $id="id$i";
                valide_equip($_POST[$valide],$_POST[$id],$_SESSION["id_pers"],$_SERVER["SERVER_NAME"]);
        }
}


$data=list_equip_valide('salle');
// id,idmatos,idqui,quand,heure_depart,heure_fin,info,valider
$j=0;
for($i=0;$i<count($data);$i++) {
	$j++;
	$heureDepart=timeForm($data[$i][4]);
	$heurefin=timeForm($data[$i][5]);
	$info=html_quotes($data[$i][6]);
	if ($data[$i][3] == "0000-00-00") { supp_resa($data[$i][0],"oui"); continue; } 
        print "<tr id='tr$i' class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
        print "<td width=5>";
        print "&nbsp;le&nbsp;".dateForm($data[$i][3])."&nbsp;<br>&nbsp;de&nbsp;$heureDepart&nbsp;&agrave;&nbsp;$heurefin";
        print "</td>";
        print "<td valign='top' ><a href='#' onMouseOver=\"AffBulle('<font class=\'T1\'>entre $heureDepart et $heurefin <br /> $info </font>'); window.status=''; return true;\" onMouseOut='HideBulle()' >&nbsp;".recherche_equip($data[$i][1])."<input type=hidden name='id$j' value='".$data[$i][0]."'>";
        print " pour ".recherche_personne($data[$i][2])."</a></td>";
        print "<td align=center><input type=radio name='valide$j' value='1' onclick=\"DisplayLigne2('tr$i',this.value);\" ></td>";
        print "<td align=center><input type=radio name='valide$j' value='2' onclick=\"DisplayLigne2('tr$i',this.value);\" ></td>";
        print "</tr>";
}

?>
</table>
<script>
function checkRadio(etat) {
	var nb="<?php print count($data) * 3 + 3?>";
	for(var i=2; i< nb;i++) {
		if ((etat == "1") && (document.formulaire.elements[i].value == '1')) {
			document.formulaire.elements[i].checked='true';	
		}
		if ((etat == "0") && (document.formulaire.elements[i].value == '2')) {
			document.formulaire.elements[i].checked='true';
		}
	}
}
</script>
<br>
<input type=hidden name=nbtotal value="<?php print count($data)?>">
<script language=JavaScript>buttonMagicRetour("resr_admin.php","_parent")</script>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create")</script>&nbsp;&nbsp;
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
<br><br>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
