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
if (!file_exists("./common/config.centralStage.php")) {
	verifAccesCentrale("$productid","$p");
}

$number="";
if (isset($_POST["number"])) {
	$number=$_POST["number"];
}

if (isset($_POST["createmail"])) {
	$objet=$_POST["objet"];
	$message=$_POST["message"];
	$cc=$_POST["ccmail"];
	$savenumber=$_POST["savenumber"];
	$number=$_POST["savenumber"];
	enr_parametrage("${savenumber}mailentrobj",$objet);
	enr_parametrage("${savenumber}mailentrmess",$message);
	enr_parametrage("${savenumber}mailentrcc",$cc);
	$messagetitre="<font class='T2' id='color3' >Donn&eacute;es  Enregistr&eacute;es</font>";
}

$objet=aff_valeur_parametrage("${number}mailentrobj");
$message=aff_valeur_parametrage("${number}mailentrmess");
$ccmail=aff_valeur_parametrage("${number}mailentrcc");
Pgclose();

$objet=str_replace('"','\\\"',$objet);

?>
<br>
<form method='post' action="gestionconfigemailsouhaitcentral.php" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Config email Numéro : </i><select name='number' onChange="this.form.submit()">
							
							  <option value=''   <?php if ($number == "") print "selected='selected'" ?> ></option>
							  <option value='1_' <?php if ($number == "1_") print "selected='selected'" ?> >1</option>
							  <option value='2_' <?php if ($number == "2_") print "selected='selected'" ?> >2</option>
							  <option value='3_' <?php if ($number == "3_") print "selected='selected'" ?> >3</option>
							  <option value='4_' <?php if ($number == "4_") print "selected='selected'" ?> >4</option>
							  <option value='5_' <?php if ($number == "5_") print "selected='selected'" ?> >5</option>
							  </select>
</form>
<br>
<form method='post' action='gestionconfigemailsouhaitcentral.php' >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font class='T2'><?php print LANGTE5 ?> : <input type='text' name='objet' value="<?php print $objet ?>" size='60'  />
<br /><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font class='T1'>
<i>Mot clef : <br /> 
<ul>
NOMPRENOMTUDIANT pour le nom et pr&eacute;nom de l'&eacute;tudiant.<br />
NOMENTREPRISE pour le nom de l'entreprise<br />
NOMCONTACT pour le nom du contact dans l'entreprise <br />
NOMETABLISSEMENTSCOLAIRE pour le nom de l'&eacute;tablissement de l'&eacute;tudiant<br />
VILLETABLISSEMENTSCOLAIRE pour la ville de l'&eacute;tablissement de l'&eacute;tudiant<br />
PAYSETABLISSEMENTSCOLAIRE pour le pays de l'&eacute;tablissement de l'&eacute;tudiant<br />
DEPARTEMENT pout le nom du d&eacute;partement affect&eacute; pour l'&eacute;tudiant<br />
</ul>
</font>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Message :<br><br> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea cols='80' rows='25' name="message"  ><?php print $message ?></textarea>
<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Recevoir une copie par mail : <input type='text' name="ccmail" size='30'  value="<?php print $ccmail ?>" onblur="verifEmail(this)"  />
<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sauvegarde sous le numéro : </i><select name='savenumber'>
						  <option value=''   <?php if ($number == "") print "selected='selected'" ?> ></option>
						  <option value='1_' <?php if ($number == "1_") print "selected='selected'" ?> >1</option>
						  <option value='2_' <?php if ($number == "2_") print "selected='selected'" ?> >2</option>
						  <option value='3_' <?php if ($number == "3_") print "selected='selected'" ?> >3</option>
						  <option value='4_' <?php if ($number == "4_") print "selected='selected'" ?> >4</option>
						  <option value='5_' <?php if ($number == "5_") print "selected='selected'" ?> >5</option>
						  </select>
<br><br>
<ul>
<table border='0'>
<tr>
<td><script language="JavaScript" >buttonMagicSubmit3('<?php print LANGAGENDA28 ?>','createmail','')</script></td>
<td><script language="JavaScript" >buttonMagicRetour2('gestion_central_stage.php','_top','Retour')</script></td>
<td><?php print $messagetitre ?></td></tr>
</table></ul></form>
</body>
</html>
