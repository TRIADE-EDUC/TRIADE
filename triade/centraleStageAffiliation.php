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
<title>Triade - Affiliation </title>
</head>
<?php
include_once('common/config.inc.php');
include_once('common/config2.inc.php');
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=visu_param();
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,$anneeScolaire
for($i=0;$i<count($data);$i++) {
	$nom_etablissement=trim($data[$i][0]);
	$adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]);
	$ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]);
	$mail=trim($data[$i][5]);
	$directeur_etablissement=trim($data[$i][6]);
	$urlsite=trim($data[$i][7]);
	$accademie=trim($data[$i][8]);
	$pays=trim($data[$i][9]);
	$departement=trim($data[$i][10]);
	$anneeScolaire=trim($data[$i][11]);
}
?>
<body id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<br>
<ul>
<font class=T2><b>Centrale Stage</b></font>
<form method='post' action='centraleStageAffiliation2.php' >
<table>

<tr> 
<td align=right><font class="T2"><?php print "Etablissement" ?> :</font></td>
<td align=left><b><?php print $nom_etablissement ?></b></td>
</tr>


<tr> 
<td align=right><font class="T2"><?php print "Ville" ?> :</font></td>
<td align=left><?php print $ville ?></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Pays" ?> :</font></td>
<td align=left><?php print $pays ?></td>
</tr>
</table>

<hr>
<br>
<font class=T2><b>Vos informations : </b></font><br><br>
</ul>
<table>
<tr> 
<td align=right><font class="T2"><?php print "Votre nom" ?> :</font></td>
<td align=left><input type=text name=contact size=40 /></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Votre email" ?> :</font></td>
<td align=left><input type=text name=email size=40 /></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Nom établissement scolaire" ?> :</font></td>
<td align=left><input type=text name=etablissement size=40/></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Ville de l'établissement scolaire" ?> :</font></td>
<td align=left><input type=text name=ville size=40/></td>
</tr>


<tr> 
<td align=right><font class="T2"><?php print "Pays de l'établissement scolaire" ?> :</font></td>
<td align=left><input type=text name=pays size=40 /></td>
</tr>
</table>
<br><br>
<table align='center'>
<tr> 
<td align=right><script language=JavaScript>buttonMagicSubmit("<?php print "Valider la demande" ?>","rien"); //text,nomInput</script></td>
<td align=left><script language=JavaScript>buttonMagic("Listing des centrales","https://support.triade-educ.org/centralestage/listing.php?inc=<?php print GRAPH ?>","_self","","")</script></td>
</tr>

</table>

<input type='hidden' name=productidclient value="<?php print $_GET["productid"] ?>" />
</form>
</BODY>
</HTML>
