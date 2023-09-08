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
<html>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
        if (!verifDroit($_SESSION["id_pers"],"edt")) {
                accesNonReserveFen();
                exit;
        }
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Ajustement des horaires de prestation " ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<br><br>
<?php
if (isset($_POST["modif"])) {

}

?>
</form>

<form method="post" >
<font class="T2">&nbsp;&nbsp;Liste des horaires de prestation </font> &nbsp;&nbsp; <a href='gestion_vacation_horaire.php'><img src="./image/commun/recycle.jpg" border='0' align='center' title='Actualiser la liste' /></a>  <br><br>
<table width="100%" border="1" bordercolor="#000000" >
<tr>
<td bgcolor="yellow" id="bordure" width="1%"><font class="T2">&nbsp;Date&nbsp;</font></td>
<td bgcolor="yellow" id="bordure" width="1%"><font class="T2">&nbsp;Classe&nbsp;</font></td>
<td bgcolor="yellow" id="bordure" ><font class="T2">&nbsp;Enseignant&nbsp;</font></td>
<td bgcolor="yellow" id="bordure" width="1%"><font class="T2">&nbsp;Heure&nbsp;</font></td>
<td bgcolor="yellow" id="bordure" width="1%"><font class="T2">&nbsp;Durée&nbsp;</font></td>
<td bgcolor="yellow" id="bordure" width="1%"><font class="T2">&nbsp;<font color='green'>Correctif</font>&nbsp;</font></td>
<td bgcolor="yellow" id="bordure" width="1%"><font class="T2">&nbsp;<font color='red'>Supprimer</font>&nbsp;</font></td>
</tr>
<?php 
$data=listePrestaHoraire(); //id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$heure=timeForm($data[$i][4]);
	$duree=timeForm($data[$i][5]);
	$classe=chercheClasse_nom($data[$i][7]);
	$nomprenom=recherche_personne($data[$i][8]);
	$idprestation=$data[$i][9];
	$date=$data[$i][3];
	
	$heurenew=$heure;
	$dureenew=$duree;

	list($H,$M)=preg_split('/:/',$heure);
	if ($M <= 10) { $M="00"; }
	if (($M >= 20) && ($M <= 40))  { $M="30"; }
	if ($M >= 50) { $M="00"; $H++; }
	$heurenew="$H:$M";
	

	list($H,$M)=preg_split('/:/',$duree);
        if ($M <= 10) { $M="00"; }
        if (($M >= 20) && ($M <= 40))  { $M="30"; }
        if ($M >= 50) { $M="00"; $H++; }
	if ($H < 10) $H="0$H";
        $dureenew="$H:$M";



	
	print "<tr class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
	print "<td id='bordure' >&nbsp;".dateForm($date)."&nbsp;</td>";
	print "<td id='bordure' >&nbsp;".ucwords($classe)."</td>";
	print "<td id='bordure' ><div id='el$i' >$affiche &nbsp; $nomprenom</div></td>";
	print "<td id='bordure' align='center'><input type='text' size='6' value='$heure' onchange=\"AjuteEDTHoraire('$id',this.value,'','el$i','5')\" /></td>";
	print "<td id='bordure' align='center'><input type='text' size='6' value='$duree' onchange=\"AjuteEDTHoraire('$id','',this.value,'el$i','5')\" /></td>"; 
	print "<td id='bordure' align='center'><a href='#' title='Valider ce correctif'
		onClick=\"AjuteEDTHoraire('$id','$heurenew','','el$i','5'); 
			  AjuteEDTHoraire('$id','','$dureenew','el$i','5')\" 
		>&nbsp;$heurenew&nbsp;-&nbsp;$dureenew&nbsp;</a></td>";
	print "<td id='bordure' align='center'><input type='checkbox' onClick=\"AjuteEDTHoraire('$id','','','el$i','5')\" /></td>";
	print "</tr>";
}

?>
</table>
</form>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
