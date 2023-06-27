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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onUnload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL7?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");

?>
<br>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire" action="gestion_examen_listing_2.php">
<table width="90%" border="0" align="center">

<tr><td width="50%" align="right" valign=top ><font class="T2"><?php print LANGBULL3 ?>: </font></td><td><?php print $_COOKIE["anneeScolaire"] ?></td></tr>
<tr><td height='20'></td></tr>


<tr>
     <td width="50%" align="right" valign=top ><font class="T2"><?php print LANGBULL2?> : </font></td>
     <td  valign=top>
<?php if (isset($idclasse)) { ?>
	<b><?php print chercheClasse_nom($idclasse) ?></b>
	<input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>" />
<?php }else{ ?>
     <select name="saisie_classe" >
     <option selected value=0   id='select0' ><?php print LANGCHOIX?></option>
<?php
		if (count($tabClasse)  > 0) {
			for($i=0;$i<count($tabClasse);$i++) {
				print "<option  value='".$tabClasse[$i][1]."' id='select1' >".$tabClasse[$i][0]."</option>";
			}
		}else{
			select_classe(); // creation des options
		}
		 print "</select>";
	} 
?>
     </td>
     </tr>
<tr><td height='20'></td></tr>
     <TR><br><TD align="right"  valign=top ><font class="T2"><?php print LANGBASE40 ?></font> <select name="typetrisem" onchange="trimes();" >
     <option value=0  id='select0' ><?php print LANGCHOIX?></option>
     <option value="trimestre" id='select1'><?php print LANGPARAM28?></option>
     <option value="semestre"  id='select1'><?php print LANGPARAM29?></option>
     </select> <font class="T2"> : </font></TD>
     <TD  valign=top><select name="saisie_trimestre">
                     <option id='select1'>        </option>
                     <option id='select1'>        </option>
                     <option id='select1'>        </option>
              </Select>
         </TD>
     </TR>
<tr><td height='20'></td></tr>

<tr><td align='right'><font class="T2"><?php print "Type d'examen"  ?> :</font></td>
<td>
<select name="saisie_examen">
<option value="" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
<?php if (EXAMENBLANC == "oui") { ?>
	<optgroup label="Blanc" />
        <option value="Brevet Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Brevet Blanc</option>
        <option value="Brevet Professionnel Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Brevet Professionnel Blanc</option>
        <option value="BAC Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BAC Blanc</option>
        <option value="CAP Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>CAP Blanc</option>
        <option value="BEP Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BEP Blanc</option>
        <option value="BTS Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BTS Blanc</option>
        <option value="Partiel Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Partiel Blanc</option>
	<option value="Concours Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Concours Blanc</option>
<?php } ?>
<?php if (EXAMENNAMUR == "oui") { ?>							
	<optgroup label="Spécif. Namur" />
        <option value="décembre"  STYLE='color:#000066;background-color:#CCCCFF'>Décembre</option>
	<option value="juin" STYLE='color:#000066;background-color:#CCCCFF'>Juin</option>
<?php } ?>
<?php if (EXAMENISMAP == "oui") { ?>
    	<optgroup label="ISMAP" />
    	<option value="CC" STYLE='color:#000066;background-color:#CCCCFF'>CC - Participation</option>
    	<option value="DST" STYLE='color:#000066;background-color:#CCCCFF'>DST</option>
    	<option value="Partiel" STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option>
    	<option value="Soutenance" STYLE='color:#000066;background-color:#CCCCFF'>Soutenance</option>
    	<option value="Rapport" STYLE='color:#000066;background-color:#CCCCFF'>Rapport</option>
    	<option value="Fiche de lecture" STYLE='color:#000066;background-color:#CCCCFF'>Fiche de lecture</option>
   	<option value="Exposé" STYLE='color:#000066;background-color:#CCCCFF'>Exposé</option>
	<option value="Dad" STYLE='color:#000066;background-color:#CCCCFF'>Dad</option>
	<option value="Lecture" STYLE='color:#000066;background-color:#CCCCFF'>Lecture</option>
        <option value="Examen écrit" STYLE='color:#000066;background-color:#CCCCFF'>Examen écrit</option>
        <option value="Recopiage vocabulaire" STYLE='color:#000066;background-color:#CCCCFF'>Recopiage vocabulaire</option>
        <option value="Mémoire Ip" STYLE='color:#000066;background-color:#CCCCFF'>Mémoire Ip</option>
        <option value="Evaluation Tutorat" STYLE='color:#000066;background-color:#CCCCFF'>Evaluation Tutorat</option>
<?php } ?>
<?php if (EXAMENDS == "oui") { ?>
	<optgroup label="DS" />
	<option value="DS1"  STYLE='color:#000066;background-color:#CCCCFF'>DS1</option>
	<option value="DS2"  STYLE='color:#000066;background-color:#CCCCFF'>DS2</option>
	<option value="DS3"  STYLE='color:#000066;background-color:#CCCCFF'>DS3</option>
	<option value="DS4"  STYLE='color:#000066;background-color:#CCCCFF'>DS4</option>
<?php } ?>
<?php if (EXAMEN == "oui") { ?>	
	<optgroup label="Examen" />
	<option value="Partiel"  STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option>
<?php } ?>
<?php if (EXAMENCIEFORMATION == "oui") { ?>							
	<optgroup label="Spécif. Cie. Formation" />
        <option value="TAS"  STYLE='color:#000066;background-color:#CCCCFF'>TAS</option>
	<option value="BTS Blanc" STYLE='color:#000066;background-color:#CCCCFF'>BTS Blanc</option>
<?php } ?>
<?php if (EXAMENEEPP == "oui") { ?>
	<optgroup label="Spécif. EEPP" />
   	<option value="semestre" STYLE='color:#000066;background-color:#CCCCFF'>Semestriel</option>
   	<option value="2session" STYLE='color:#000066;background-color:#CCCCFF'>2ème session</option>
<?php } ?>

</select></td></tr>

<?php
$datap=config_param_visu("affNomEleExam");
$affNomEleExam=$datap[0][0];
$datap=config_param_visu("affMatriEleExam");
$affMatriEleExam=$datap[0][0];
if ($affNomEleExam == "oui") { $affNomEleExam="checked='checked'"; }else{  $affNomEleExam=""; }
if ($affMatriEleExam == "oui") { $affMatriEleExam="checked='checked'"; }else{  $affMatriEleExam=""; }
?>
<tr><td height='20'></td></tr>
<tr><td align="right"><font class="T2"><?php print "Indiquer le nom de l'élève " ?> : </font></td>
    <td><input type='checkbox' name='saisie_avec_nom' value="oui" <?php print $affNomEleExam ?> /> <i>(oui)</i></td>
</tr>

<tr><td height='20'></td></tr>
<tr><td align="right"><font class="T2"><?php print "Indiquer le matricule élève " ?> : </font></td>
<td><input type='checkbox' name='saisie_avec_matricule' value="oui" <?php print $affMatriEleExam ?> /> <i>(oui)</i></td>
</tr>



<tr><td height='20'></td></tr>
<tr><td align="right"><font class="T2"><?php print "Hauteur des lignes" ?> : </font></td>
        <td>
	<select name="hauteur" size=1>
		<option value="6" STYLE='color:#000066;background-color:#CCCCFF'>06</option>
		<option value="7" STYLE='color:#000066;background-color:#CCCCFF'>07</option>
		<option value="8" STYLE='color:#000066;background-color:#CCCCFF'>08</option>
		<option value="9" STYLE='color:#000066;background-color:#CCCCFF'>09</option>
		<option value="10" STYLE='color:#000066;background-color:#CCCCFF'>10</option>
		<option value="11" STYLE='color:#000066;background-color:#CCCCFF'>11</option>
		<option value="12" STYLE='color:#000066;background-color:#CCCCFF'>12</option>
		<option value="13" STYLE='color:#000066;background-color:#CCCCFF'>13</option>
		<option value="14" STYLE='color:#000066;background-color:#CCCCFF'>14</option>
		<option value="15" STYLE='color:#000066;background-color:#CCCCFF'>15</option>
	</select>
	 </td>
    </tr>
</table>
<br><br>
<table border=0 align=center width="250" ><tr><td align="center">
<script language=JavaScript>buttonMagicSubmit3("<?php print "Valider la sélection" ?>","rien","onclick='attente()'");</script>
</td></tr></table>
<input type=hidden name="type_notation" value="<?php print $_POST["type_notation"] ?>" />
</form>
<br /><br />
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
