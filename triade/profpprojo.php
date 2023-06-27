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
$anneeScolaire=$_COOKIE["anneeScolaire"];
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade Vidéo-Projecteur</title>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_attente.php"); ?>
<?php include("./librairie_php/lib_licence.php"); ?>
<BR><BR>
<form method=post action="video-proj-affichage.php" name="formulaire0" 
onSubmit="document.formulaire0.supp.value='Patientez S.V.P.';
	  document.formulaire.supp.disabled=true;
	  document.formulairean.supp.disabled=true;
	  document.formulaire0.supp.disabled=true;AfficheAttente();
" >
<table border=1 bordercolor="#000000" width=550 align=center bgcolor="#FFFFFF" height="100">
<tr>
<td align=center  id='bordure' colspan=2 id="bordure" ><font size=3><strong><?php print LANGPROFP32 ?></strong></font><br><br>
</tr>

    <tr><td align=right  valign=top id="bordure" ><font class=T2><?php print LANGBULL3?> : </font></td>
     <td  valign=top id="bordure" >
     <select name='annee_scolaire'>
     <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
     </select>
     </td></tr>

<tr><td width=50% align=right id='bordure' height='20'>
<input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
<input type=hidden name="fichier_origin" value="profpprojo">
</tr><tr>
<td align=right  id='bordure' ><font class=T2><?php print LANGPROJ2?> :</font> </td>
<td id='bordure' >
<select name="saisie_trimestre">
<option value="trimestre1" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
<option value="trimestre2" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
<option value="trimestre3" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?> </option>
</Select>
</td>
</tr>
<?php if (MODNAMUR0 == "oui") { ?>
<tr><td height='20'  id='bordure' ></td></tr>
<tr>
<td align=right id="bordure"  ><font size=3><?php print "Avec note vie scolaire "?> :</font> </td>
<td id="bordure" >
<input type='radio' name='validenoteviescolaire' value='oui' checked='checked' /> <i>(oui)</i>
<input type='radio' name='validenoteviescolaire' value='non'  /> <i>(non)</i>
</td>
</tr>
<?php } ?>
<tr>
<td  colspan=2 align=center id='bordure' >
<br /><br />
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("3");
}
$valeur=aff_Trimestre();
if (count($valeur)) {
        $disabled="";
        $alert="";
}else{
        $disabled="disabled=disabled";
        $alert=LANGMESS10."<br><br>".LANGMESS11."<br><br>".LANGMESS12;
}
?>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
<br /><br />
<?php
if (isset($_GET["info"])) {
	print "<br><center><font color=red>".LANGPROJ6."</font></center><br>";

}
?>
</td></tr></table>
</td></tr></table>
</form>

<form method=post action="tableaupp2.php" name="formulaire" 
onSubmit="document.formulaire.supp.value='Patientez S.V.P.';
	  document.formulaire.supp.disabled=true;
	  document.formulairean.supp.disabled=true;
	  document.formulaire0.supp.disabled=true;
	  AfficheAttente();" >

<table border=1 bordercolor="#000000" width=550 align=center bgcolor="#FFFFFF" height="100">
<tr><td width=50% align=right id='bordure'>
<input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
</tr>
<tr>
<td align=center id='bordure' colspan=2 id="bordure" ><font size=3><strong><?php print LANGPROFP31 ?></strong></font><br><br>
</tr>

<tr><td width=50% align=right id='bordure'>
<input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
</tr>


<TR><br><TD align="right"  id="bordure"  valign=top ><font class="T2"><?php print LANGBASE40 ?></font> 
<select name="typetrisem" onchange="trimes();" >
     <option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     </select> <font class=T2> : </font> </TD>
     <TD  valign=top id="bordure" ><select name="saisie_trimestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
              </Select>
         </TD>
     </TR>

<tr><td height="20" colspan="2"  id='bordure' ></td></tr>

    <tr><td align=right  valign=top id="bordure" ><font class=T2><?php print LANGBULL3?> : </font></td>
     <td  valign=top id="bordure" >
     <select name='annee_scolaire'>
     <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
     </select>
     </td></tr>
<tr><td height="20" colspan="2"  id='bordure' ></td></tr>
     <tr><td align=right  valign=top id="bordure"  ><font class="T2"><?php print "Afficher le classement " ?> :</font> </td>
     <td  valign=top id="bordure"  >
     <input type="checkbox" name="affrang" value="1" /> (<i>oui</i>)
     </td></tr>
     <tr><td  valign=top id="bordure" colspan=2 ><br />
     <tr><td align=right id="bordure" valign=top ><font class="T2"><?php print "Afficher les colonnes vides " ?> :</font> </td>
     <td  valign=top id="bordure">
     <input type="checkbox" name="affcolvide" value="1" /> (<i>oui</i>)
     </td></tr>
<tr><td height="20" colspan="2"  id='bordure' ></td></tr>
    <tr><td align=right  id="bordure" valign=top ><font class="T2"><?php print "Prise en compte note examen " ?> :</font> </td>
     <td   id="bordure" valign=top>
     <input type="checkbox" name="noteexamen" id="noteexamen" value="oui"  /> (<i>oui</i>)
     </td></tr>
<tr><td height="10" id="bordure" ></td><td id="bordure" ></td></tr>
     <tr><td align=right  valign=top id="bordure" ><font class="T2"><?php print "Seulement les notes de type examen"  ?> :</font> </td><td id="bordure" valign=top><?php include("typeexamen.php"); ?></td></tr>
     <tr><td  valign=top id="bordure" colspan=2 ><br /><br />
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
<br /><br />
</td></tr></table>
</td></tr></table>
</form>

<form method=post action="tableaupp2an.php" name="formulairean"
onSubmit="document.formulairean.supp.value='Patientez S.V.P.';
	  document.formulaire.supp.disabled=true;
	  document.formulairean.supp.disabled=true;
	  document.formulaire0.supp.disabled=true;AfficheAttente();" >
<table border=1 bordercolor="#000000" width=550 align=center bgcolor="#FFFFFF" height="100">
<tr><td width=50% align=right id='bordure'>
<input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
</tr>
<tr>
<td align=center id='bordure' colspan=2 id="bordure" ><font class=T2><strong><?php print LANGPROFP39 ?></strong></font><br><br>
</tr>

<tr><td width=50% align=right id='bordure'>
<input type=hidden name="saisie_classe" value="<?php print $_GET["idClasse"]?>">
</tr>


<TR><br><TD align="right"  id="bordure"  valign=top ><font class="T2"><?php print "Jusqu'au:" ?></font> <select name="typetriseman" onChange="trimesan();" >
     <option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     </select> <font class=T2> : </font></TD>
     <TD  valign=top id="bordure" ><select name="saisie_trimestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
              </Select>
         </TD>
     </TR>


<tr><td height="20" colspan="2"  id='bordure' ></td></tr>

    <tr><td align=right  valign=top id="bordure" ><font class=T2><?php print LANGBULL3?> : </font></td>
     <td  valign=top id="bordure" >
     <select name='annee_scolaire'>
     <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
     </select>
     </td></tr>
    <tr><td height="10"  colspan="2"  id='bordure' ></td></tr>
    <tr><td align=right   id="bordure" valign=top ><font class="T2"><?php print "Afficher le classement " ?> :</font> </td>
     <td  valign=top   id="bordure">
     <input type="checkbox" name="affrang" value="1" /> (<i>oui</i>)
     </td></tr>
     <tr><td  valign=top id="bordure" colspan=2 ><br />
     <tr><td align=right id="bordure" valign=top ><font class="T2"><?php print "Afficher les colonnes vides " ?> :</font> </td>
     <td  valign=top id="bordure">
     <input type="checkbox" name="affcolvide" value="1" /> (<i>oui</i>)
     </td></tr>
    <tr><td height="20" colspan="2"  id='bordure' ></td></tr>
    <tr><td align=right  id="bordure" valign=top ><font class="T2"><?php print "Prise en compte note examen " ?> :</font> </td>
     <td   id="bordure" valign=top>
     <input type="checkbox" name="noteexamen" id="noteexamen" value="oui"  /> (<i>oui</i>)
     </td></tr>
     <tr><td  valign=top id="bordure" colspan=2 ><br />
	 <tr><td height="10" id="bordure" ></td><td id="bordure" ></td></tr>

     <tr><td align=right  valign=top id="bordure" ><font class="T2"><?php print "Seulement les notes de type examen"  ?> :</font> </td><td  valign=top id="bordure" ><?php include("typeexamen.php"); ?></td></tr>

     <tr><td  id="bordure" align='center' colspan='2' ><br><br>
<table align=center><tr><td  id='bordure' >
<script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
<br /><br />
</td></tr>

</td></tr></table>
</td></tr></table>
</form>
<br><br>



<?php attente(); Pgclose(); ?>
</body>
</html>
