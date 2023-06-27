<?php
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
</head>
<body id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/db_triade.php"); 
$idstage=trim($_GET["idstage"]);
$productid=$_GET["productid"];
$p=$_POST["p"];
$cnx=cnx();

$date=dateDMY();
$nbdemande=1;

if (isset($_POST["suppression"])) {
	$cr=suppressionInfoCentralStage($_POST["idcentralstagesouhait"]);
	if ($cr) {
		history_cmd($_SESSION["nom"],"SUPPRESSION","INFO Central-Stage");
		$message="<center><font class='T2' id='color3' >Souhait supprimé</font></center><br><br>";
	}
}


if (isset($_POST["modif"])) {
	$cr=modifInfoCentralStage($_POST["idcentralstagesouhait"],$_POST["identreprise"],$_POST["sexe"],$_POST["service"],$_POST["observation"],$_POST["nbdemande"],$_POST["idperiode"],$_POST["salaire"],$_POST["datedemande"],$_POST["logement"]);
	$idstage=$_POST["idcentralstagesouhait"];
	if ($cr) {
		history_cmd($_SESSION["nom"],"MODIFICATION","INFO Central-Stage ");
		$message="<center><font class='T2' id='color3' >Souhait modifié</font></center><br><br>";
	}
}


//datedemande,identreprise,sexe,service,observation,nbdemande,idperiode,null,salaire,logement
if (trim($idstage) != "") {
	$data=recupInfoCentralStage($idstage);
	$date=dateForm($data[0][0]);
	$identreprise=$data[0][1];
	$sexe=preg_replace('/"/',"'",$data[0][2]);
	$service=preg_replace('/"/',"'",$data[0][3]);
	$observation=preg_replace('/"/',"'",$data[0][4]);
	$nbdemande=$data[0][5];
	$attribution=$data[0][6];
	$idperiode=$data[0][6];
	$salaire=$data[0][8];
	$checkboxlogement=($data[0][9] == 1) ? "checked='checked'" : "";
}

if (!file_exists("./common/config.centralStage.php")) {
	verifAccesCentrale("$productid","$p");
}


if (isset($_POST["create"])) {
	if ($_POST["identreprise"] != "") {
		$cr=creationStageCentralSouhait($_POST["datedemande"],$_POST["identreprise"],$_POST["sexe"],$_POST["service"],$_POST["observation"],$_POST["idperiode"],$_POST["nbdemande"],$_POST["salaire"],$_POST['logement']);
		if ($cr) {
			print "<br><center><font class='T2' id='color3' >Demande enregistrée</font></center>";
		}
	}else{
		print "<br><center><font class='T2' id='color3' >Demande NON enregistrée, Entreprise non indiqué</font></center>";
	}
}
?>
<br>
<?php print $message ?>
<form method="post" name="formulaire" action="gestiondemandesouhaitcentral.php?idstage=<?php print $idstage?>&periode=<?php print $_GET["periode"] ?>&p=<?php print $p?>&productid=<?php print $productid ?>" >
<table width="100%" border="0" align="center" >
<tr>
<td align="right" ><font class="T2"><?php print "Date de la demande " ?> :</font></td><td><input type=text name='datedemande' value="<?php print $date?>"  size=12 class="bouton2" onKeyPress="onlyChar(event)">
<?php
include_once("librairie_php/calendar.php");
calendarDim("id1","document.formulaire.datedemande",$_SESSION["langue"],"1");
?></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Entreprise" ?> :</font></td><td><select name="identreprise" >
<?php 
if (isset($_GET["idstage"])) { 
	select_recherche_entreprise(25,$identreprise);
}else{ ?>
<option id='select0'><?php print LANGCHOIX ?></option>
<?php
}
select_entreprise_limit(25);
?></select></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Nombre de demandes" ?> :</font></td><td><input type="text" name="nbdemande" size=2 maxlength="3" value="<?php print $nbdemande ?>" ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Sexe" ?> :</font></td><td><input type="text" name="sexe" size=30 maxlength="250" value="<?php print $sexe ?>"  ></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Service" ?> :</font></td><td><input type="text" value="<?php print $service ?>"  name="service"  maxlength="250" size=30></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Salaire" ?> :</font></td><td><input type="text" value="<?php print $salaire ?>"  name="salaire"  maxlength="250" size=30></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Logement" ?> :</font></td><td><input type="checkbox" value="1"  <?php print $checkboxlogement ?> name="logement" /> (<i>oui</i>)</td>
</tr>

<tr>
<td align="right" ><font class="T2"><?php print "Observation" ?> :</font></td><td><input type="text" value="<?php print $observation ?>"  name="observation"  maxlength="250" size=30></td>
</tr>
<tr>
<td align="right" ><font class="T2"><?php print "Période demandée" ?> :</font></td>
<td>
<select name='idperiode' >
<?php
if ($idstage != "") { 
	$data=periodeStageCentralDateSelect($idperiode);
	print "<option id='select0' value='".$data[0][2]."' >(".$data[0][3].") ".dateForm($data[0][0])." - ".dateForm($data[0][1])."</option>";
}else{
	print "<option id='select0' value='0' >".LANGCHOIX."</option>";
}
$data=periodeStageCentralDate();
for($i=0;$i<count($data);$i++) {
	print "<option id='select1' value='".$data[$i][2]."' >(".$data[$i][3].") ".dateForm($data[$i][0])." - ".dateForm($data[$i][1])."</option>";
}
?>
</select>
</td>
</tr>
</table>
<table align="center"><tr><td valign='top' >
<?php if (trim($idstage) != "") { ?>
	<br><br>
	<script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30 ?>","modif");</script>
	<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT50 ?>","suppression");</script>&nbsp;&nbsp;
	<input type='hidden' name="idcentralstagesouhait" value="<?php print $_GET["idstage"] ?>" />
	</form>
	<form method='post' action='gestion_central_stage_visu2.php' >
	<input type='hidden' name="productid" value="<?php print $productid ?>" />
	<input type='hidden' name="p" value="<?php print $p ?>" />
	<input type='hidden' name="periode" value="<?php print $_GET["periode"] ?>" />
	<script language=JavaScript>buttonMagicSubmit("<?php print "Retour" ?>","rien");</script>
	</form>
<?php }else{ ?>
	<br><br>
	<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
	<script language=JavaScript>buttonMagicRetour2("gestion_central_stage.php","_top","Retour")</script>
	</form>
<?php } ?>
&nbsp;&nbsp;
</td></tr></table>
<BR><br>
</BODY></HTML>
