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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();


if (isset($_POST["rien"])) {

	$param["Francais"]=$_POST["coefFrancais"];
	$param["Maths"]=$_POST["coefMath"];
	$param["LV1"]=$_POST["coefLV1"];
	$param["SVT"]=$_POST["coefSVT"];
	$param["PhyChimi"]=$_POST["coefPhyChimi"];
	$param["EPS"]=$_POST["coefEPS"];
	$param["ArtsPlas"]=$_POST["coefArtPlast"];
	$param["Music"]=$_POST["coefMusic"];
	$param["Techo"]=$_POST["coefTechno"];
	$param["LV2"]=$_POST["coefLV2"];
	$param["VieScolaire"]=$_POST["coefVieScolaire"];
	$param["DP6"]=$_POST["coefDp6"];
	$cr=enregCoefBrevet($param,'college');
	if($cr) {
		alertJs(LANGDONENR);
	}

}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Fiche Brevet série collège - Configuration coefficient"?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<br>
&nbsp;&nbsp;<font class=T2>Indiquer le coefficient pour chaque matière.</font><br><br>
<form method=post action="gestion_examen_brevet_config_classe3.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'"  ><td>&nbsp;<font class="T2">Français</font>&nbsp;</td><td width=5><input type=text size=2 value="<?php print rechercheCoefBrevet('Francais','college'); ?>" name="coefFrancais" />
<tr   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Mathématiques</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('Maths','college'); ?>" name="coefMath" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Langue vivante 1</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('LV1','college'); ?>" name="coefLV1" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">S.V.T.</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('SVT','college'); ?>" name="coefSVT" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Physique-Chimie</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('PhyChimi','college'); ?>" name="coefPhyChimi" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Education physique et sportive</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('EPS','college'); ?>" name="coefEPS" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Arts Platiques</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('ArtsPlas','college'); ?>" name="coefArtPlast" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Education musicale</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('Music','college'); ?>" name="coefMusic" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Technologie</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('Techo','college'); ?>" name="coefTechno" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Langue Vivante 2</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('LV2','college'); ?>" name="coefLV2" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Vie Scolaire</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('VieScolaire','college'); ?>" name="coefVieScolaire" />
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" ><td>&nbsp;<font class="T2">Découverte professionelle 6 heures</font>&nbsp;</td><td><input type=text size=2 value="<?php print rechercheCoefBrevet('DP6','college'); ?>" name="coefDp6" />
</table>
<br><br>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","rien"); //text,nomInput</script></td></tr></table>

</form>
</td></tr></table>
</ul>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
