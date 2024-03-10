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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Configuration des régimes" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
include_once('./librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$readonly="0";
if (isset($_POST["createregime"])) {
//	$libelle2=preg_replace('/ /','-',$_POST["libelle"]);
	$libelle2=$_POST["libelle"];
	$lundimidi=$_POST["lundimidi"];
	$lundisoir=$_POST["lundisoir"];
	$mardimidi=$_POST["mardimidi"];
	$mardisoir=$_POST["mardisoir"];
	$mercredimidi=$_POST["mercredimidi"];
	$mercredisoir=$_POST["mercredisoir"];
	$jeudimidi=$_POST["jeudimidi"];
	$jeudisoir=$_POST["jeudisoir"];
	$vendredimidi=$_POST["vendredimidi"];
	$vendredisoir=$_POST["vendredisoir"];
	$samedimidi=$_POST["samedimidi"];
	$samedisoir=$_POST["samedisoir"];
	$dimanchemidi=$_POST["dimanchemidi"];
	$dimanchesoir=$_POST["dimanchesoir"];
	if (trim($libelle2) != "") {
		$cr=enrRegime($libelle2,$lundimidi,$lundisoir,$mardimidi,$mardisoir,$mercredimidi,$mercredisoir,$jeudimidi,$jeudisoir,$vendredimidi,$vendredisoir,$samedimidi,$samedisoir,$dimanchemidi,$dimanchesoir);
		if ($cr == 2) { alertJs("Le libellé est déjà utilisé, merci dans choisir un autre."); }
	}
}

if (isset($_POST["updateregime"])) {
//	$libelle1=preg_replace('/ /','-',$_POST["libelle"]);
	$libelle1=$_POST["libelle"];
	$lundimidi=$_POST["lundimidi"];
	$lundisoir=$_POST["lundisoir"];
	$mardimidi=$_POST["mardimidi"];
	$mardisoir=$_POST["mardisoir"];
	$mercredimidi=$_POST["mercredimidi"];
	$mercredisoir=$_POST["mercredisoir"];
	$jeudimidi=$_POST["jeudimidi"];
	$jeudisoir=$_POST["jeudisoir"];
	$vendredimidi=$_POST["vendredimidi"];
	$vendredisoir=$_POST["vendredisoir"];
	$samedimidi=$_POST["samedimidi"];
	$samedisoir=$_POST["samedisoir"];
	$dimanchemidi=$_POST["dimanchemidi"];
	$dimanchesoir=$_POST["dimanchesoir"];
	if (trim($libelle1) != "") {
		updateRegime($libelle1,$lundimidi,$lundisoir,$mardimidi,$mardisoir,$mercredimidi,$mercredisoir,$jeudimidi,$jeudisoir,$vendredimidi,$vendredisoir,$samedimidi,$samedisoir,$dimanchemidi,$dimanchesoir,$_POST["id"]);
	}
}


if (isset($_POST["suppregime"])) {
	suppRegime($_POST["id"]);
}

if (isset($_GET["id"])) {
	$data=InfoRegime($_GET["id"]);
	//  `libelle` , `lundi_m` , `lundi_s` , `mardi_m` , `mardi_s` , `mercredi_m` , `mercredi_s` , `jeudi_m` , `jeudi_s` , `vendredi_m` , `vendredi_s` , `samedi_m` , `samedi_s` , `dimanche_m` , `dimanche_s`
	$libelle=str_replace('"',"",$data[0][0]);
	$LUM=($data[0][1] == 1) ? "checked='checked'" : "" ;
	$LUS=($data[0][2] == 1) ? "checked='checked'" : "" ;
	$MAM=($data[0][3] == 1) ? "checked='checked'" : "" ;
	$MAS=($data[0][4] == 1) ? "checked='checked'" : "" ;
	$MEM=($data[0][5] == 1) ? "checked='checked'" : "" ;
	$MES=($data[0][6] == 1) ? "checked='checked'" : "" ;
	$JEM=($data[0][7] == 1) ? "checked='checked'" : "" ;
	$JES=($data[0][8] == 1) ? "checked='checked'" : "" ;
	$VEM=($data[0][9] == 1) ? "checked='checked'" : "" ;
	$VES=($data[0][10] == 1) ? "checked='checked'" : "" ;
	$SAM=($data[0][11] == 1) ? "checked='checked'" : "" ;
	$SAS=($data[0][12] == 1) ? "checked='checked'" : "" ;
	$DIM=($data[0][13] == 1) ? "checked='checked'" : "" ;
	$DIS=($data[0][14] == 1) ? "checked='checked'" : "" ;
}

?>
<form method='post' action="regime_ajout.php" >
<table align='center'>

<tr><td align='right' ><font class='T2'>Nom du régime :</font></td>
	<?php  if ($_GET["id"] != "") { 
		print "<td>$libelle<input type=hidden name='libelle' size='30' maxlength='25' value=\"$libelle\" /></td>";
	   }else{
		print "<td><input type=text name='libelle' size='30' maxlength='25' value='' /></td>";
 	   }
      ?>
    </tr>
<tr><td align='left' colspan='2' >
<table>
<tr><td align='right'><font class='T2'>
<?php print LANGLETTRELUNDI ?> :</font></td><td><input type="checkbox" name="lundimidi" <?php print $LUM ?> value="1" /> (midi) / <input type="checkbox" name="lundisoir"  value="1"  <?php print $LUS ?> /> (soir)
</tr><tr><td align='right'><font class='T2'>
<?php print LANGLETTREMARDI ?> :</font></td><td><input type="checkbox" name="mardimidi" <?php print $MAM ?> value="1" /> (midi) / <input type="checkbox" name="mardisoir"  value="1"  <?php print $MAS ?> /> (soir)
</tr><tr><td align='right'><font class='T2'>
<?php print LANGLETTREMERCREDI ?> :</font></td><td><input type="checkbox" name="mercredimidi" <?php print $MEM ?> value="1" /> (midi) / <input type="checkbox" name="mercredisoir"  value="1"  <?php print $MES ?> /> (soir)
</tr><tr><td align='right'><font class='T2'>
<?php print LANGLETTREJEUDI ?>  :</font></td><td><input type="checkbox" name="jeudimidi" <?php print $JEM ?> value="1" /> (midi) / <input type="checkbox" name="jeudisoir"  value="1" <?php print $JES ?> /> (soir)
</tr><tr><td align='right'><font class='T2'>
<?php print LANGLETTREVENDREDI ?> :</font></td><td><input type="checkbox" name="vendredimidi" <?php print $VEM ?> value="1" /> (midi) / <input type="checkbox" name="vendredisoir"  value="1" <?php print $VES ?> /> (soir)
</tr><tr><td align='right'><font class='T2'>
<?php print LANGLETTRESAMEDI ?> :</font></td><td><input type="checkbox" name="samedimidi" <?php print $SAM ?> value="1" /> (midi) / <input type="checkbox" name="samedisoir"  value="1" <?php print $SAS ?> /> (soir)
</tr><tr><td align='right'><font class='T2'>
<?php print LANGLETTREDIMANCHE ?> :</font></td><td><input type="checkbox" name="dimanchemidi"  <?php print $DIM ?> value="1" /> (midi) / <input type="checkbox" name="dimanchesoir"  value="1" <?php print $DIS ?> /> (soir)
</td></tr></table>

</td></tr>
</td></tr></table>
<br><br>
<table  border="0" align="center">

<?php  if ($_GET["id"] != "") { ?>
<tr><td><script language=JavaScript>buttonMagicSubmit('<?php print LANGBT50?>','suppregime'); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagicSubmit('<?php print LANGPER30?>','updateregime'); //text,nomInput</script></td></tr>
<?php }else{ ?>
<tr><td><script language=JavaScript>buttonMagicSubmit('<?php print LANGENR?>','createregime'); //text,nomInput</script></td></tr>
<?php } ?>
</table>
	<input type=hidden value="<?php print $_GET["id"] ?>" name="id" />
</form>
<i>Vous pouvez créer les régimes <b>"demi-pension" </b>, <b>"demi pension" </b>, <b>"interne" </b> et <b>"externe" </b> déjà présent dans la fiche des régimes des élèves.</i>
<br><hr><br>
<table width="100%" border='1' style='border-collapse: collapse;' >
<tr>
<td bgcolor="yellow"><font class='T2'>&nbsp;&nbsp;Libellé</font></td>
<td bgcolor="yellow" colspan='2' align='center' >L</td>
<td bgcolor="yellow" colspan='2' align='center' >M</td>
<td bgcolor="yellow" colspan='2' align='center' >M</td>
<td bgcolor="yellow" colspan='2' align='center' >J</td>
<td bgcolor="yellow" colspan='2' align='center' >V</td>
<td bgcolor="yellow" colspan='2' align='center' >S</td>
<td bgcolor="yellow" colspan='2' align='center' >D</td>
</tr>
<tr>
<td></td>
<td bgcolor="yellow"  align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>
<td bgcolor="yellow" align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>
<td bgcolor="yellow" align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>
<td bgcolor="yellow" align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>
<td bgcolor="yellow" align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>
<td bgcolor="yellow" align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>
<td bgcolor="yellow" align='center' >M</td>
<td bgcolor="yellow" align='center' >S</td>

</tr>


<?php
$data=listingRegime();
// `libelle` , `lundi_m` , `lundi_s` , `mardi_m` , `mardi_s` , `mercredi_m` , `mercredi_s` , `jeudi_m` , `jeudi_s` , `vendredi_m` , `vendredi_s` , `samedi_m` , `samedi_s` , `dimanche_m` , `dimanche_s`,id
for($i=0;$i<count($data);$i++) { 
	$id=$data[$i][15];
	?>
	<tr>
	<td><a href="regime_ajout.php?id=<?php print $id ?>" title="Modif/Supp"><?php print $data[$i][0] ?></a></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][1] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][2] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][3] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][4] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][5] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][6] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][7] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][8] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][9] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][10] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][11] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][12] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][13] == 1) ? "checked='checked'" : "" ?> ></td>
	<td align='center'><input type='checkbox' <?php print ($data[$i][14] == 1) ? "checked='checked'" : "" ?> ></td>
	</tr>
<?php } ?>
</table>

<!-- // fin  -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
?>
</BODY></HTML>
