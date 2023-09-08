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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once('./librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$idpers=$_GET["saisie_id"];

if (isset($_POST["create"])) {
	$params["cantine"]=$_POST["cantine"];
	$params["droitStageProRead"]=$_POST["droitStageProRead"];
	$params["trombinoscopeRead"]=$_POST["trombinoscopeRead"];
	$params["consultationRead"]=$_POST["consultationRead"];
	$params["cahiertextRead"]=$_POST["cahiertextRead"];
	$params["resaressource"]=$_POST["resaressource"];
	$params["vatelcompta"]=$_POST["vatelcompta"];
	$params["vatelchambre"]=$_POST["vatelchambre"];
	$params["ficheeleve"]=$_POST["ficheeleve"];
	$params["carnetnotes"]=$_POST["carnetnotes"];
	$params["cahiertextes"]=$_POST["cahiertextes"];
	$params["imprbulletin"]=$_POST["imprbulletin"];
	$params["imprtableau"]=$_POST["imprtableau"];
	$params["visadirection"]=$_POST["visadirection"];
	$params["videoprojo"]=$_POST["videoprojo"];
	$params["entretien"]=$_POST["entretien"];
	$params["edt"]=$_POST["edt"];
	droitModule($_POST["idpers"],$params);
	$idpers=$_POST["idpers"]; 	
	$message="<br><center><font id=color3 class=T2 ><b>Permissions enregistrées</b></center><br><br>";
}
$data=affPers_nom($idpers); // pers_id, civ, nom, prenom
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method="post" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Permission sur l'accès aux modules de <?php print civ($data[0][1])." ".strtoupper($data[0][2])." ".ucfirst($data[0][3]) ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
print $message; 
?>
<table width=100% border=1 bordercolor='#000000'>
<?php 
$perm=(verifDroit($idpers,"cantine")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module gestionnaire de cantine : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='cantine' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"droitStageProRead")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module gestion stage pro (Lecture) : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='droitStageProRead' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"trombinoscopeRead")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Trombinoscope (consultation) : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='trombinoscopeRead' <?php print $perm  ?> /></td>
</tr>


<?php 
$perm=(verifDroit($idpers,"consultationRead")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Consultation élève (Listing) : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='consultationRead' <?php print $perm  ?> /></td>
</tr>


<?php 
$perm=(verifDroit($idpers,"cahiertextRead")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Cahier de textes (consultation) [<a href="#" onclick="open('cahiertextpermission.php?id=<?php print $idpers ?>','','width=600,height=500,scrollbars=no'); return false;">enseignants</a>] : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='cahiertextRead' <?php print $perm  ?> /></td>
</tr>


<?php 
$perm=(verifDroit($idpers,"resaressource")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module réservation des ressources (validation) : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='resaressource' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"vatelchambre")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Chambre (vatel) : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='vatelchambre' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"vatelcompta")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Comptabilité  (vatel) : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='vatelcompta' <?php print $perm  ?> /></td>
</tr>

<?php // ------------------------------------------------ ?>




<?php 
$perm=(verifDroit($idpers,"ficheeleve")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Fiche <?php print INTITULEELEVE ?> : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='ficheeleve' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"carnetnotes")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Carnet de notes : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='carnetnotes' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"cahiertextes")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Cahier de textes  : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='cahiertextes' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"imprbulletin")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Imprimer bulletins : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='imprbulletin' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"imprtableau")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Imprimer tableau : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='imprtableau' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"visadirection")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Visa Direction : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='visadirection' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"videoprojo")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module Vidéo-projecteur : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='videoprojo' <?php print $perm  ?> /></td>
</tr>


<?php 
$perm=(verifDroit($idpers,"entretien")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module entretien individuel : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='entretien' <?php print $perm  ?> /></td>
</tr>

<?php 
$perm=(verifDroit($idpers,"edt")) ? "checked='checked'" : ""; 
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td id=bordure align='right'><font class=T2> Module emploi du temps : </font></td>
<td id=bordure ><input type='checkbox' value='1' name='edt' <?php print $perm  ?> /></td>
</tr>

<td colspan='2' align='center' id='bordure' ><table><tr><td><script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","create",""); //text,nomInput</script></td></tr></table></td>
</tr>

</table>
</td></tr></table>
<input type=hidden name="idpers" value="<?php print $_GET["saisie_id"] ?>" />
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>



