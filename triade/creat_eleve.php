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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE43?></font></B></td>
</tr><tr id='cadreCentral0'>
<td >
<table align=center border=0 width='100%' ><tr><td>
<img src="image/commun/etudiant.png" align='center' />
</td><td>
<script language=JavaScript>buttonMagic("<?php print LANGMESS187 ?>","recherche_eleve.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGMESS188 ?>","base_de_donne_importation.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print LANGMESS189 ?>","suppression_compte_eleve.php","_parent","","");</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr></table>
<br /><br />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE10?></font></B></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>
<form method=post onsubmit="return valide_creat_eleve()" name="formulaire" >
<ul><font class=T1 color='#CC0000'><b><?php print LANGELE1?></b></font></ul>

  <TABLE border="0"  width=100% align="center">
     <tr><td width="50%" ><div align="right"><font class="T2"><?php print LANGELE2?> : </font></div></td>
        <td><input type="text" name="saisie_nom" maxlength=50 >&nbsp;<font id='color2' ><b>*</b></font></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE3?> :  </font></div></td>
        <td><input type="text" name="saisie_prenom"  maxlength=50 >&nbsp;<font id='color2' ><b>*</b></font></td>
    </tr>

<tr><td><div align="right"><font class="T2"><?php print LANGBULL3?> : </font></div></td>
        <td><select name="annee_scolaire" size="1">
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
filtreAnneeScolaireSelectFutur(); // creation des options
?>
            </select>&nbsp;<font id='color2' ><b>*</b></font>
        </td>
    </tr>


    <tr><td><div align="right"><font class="T2"><?php print LANGELE4?> : </font></div></td>
        <td><select name="saisie_classe" size="1">
            <option selected value='0'   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
            </select>&nbsp;<font id='color2' ><b>*</b></font>
        </td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGMESS190 ?> </font></div></td>
        <td><select name="saisie_lv1" size="1">
	<option selected value=''> </option>
<?php
select_matiere2(); // creation des options
?>
            </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGMESS191 ?> </font></div></td>
        <td><select name="saisie_lv2" size="1">
	<option selected value=''> </option>
<?php
select_matiere2(); // creation des options
?>
	    </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE5?> : </font></div></td>
    <td>
<select name="saisie_option" size="1">
	<option selected value=''> </option>
<?php
select_matiere2(); // creation des options
?>
	    </select>
    </td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE6?> : </font></div></td>
    <td>
	<select name="saisie_regime" >
		<option value="" id='select0' ><?php print LANGCHOIX ?></option>
		<option value="Interne"  id='select1'  ><?php print LANGELE7 ?></option>
		<option value="Demi-pension"  id='select1'  ><?php print LANGELE8 ?></option>
		<option value="Externe"  id='select1' ><?php print LANGELE9 ?></option>
		<optgroup label="Personnalisé">
		<?php
			selectRegime();
		?>
			</select> [<a href="regime_ajout.php"><?php print LANGTMESS440 ?></a>]
      </tr>

    <tr><td><div align="right"><font class="T2"><?php print LANGTMESS441 ?> : </font></div></td>
        <td><input type="text" name="saisie_rangement"  maxlength='200' >
    </td></tr>

    <tr><td><div align="right"><font class="T2"><?php print LANGELE10?> : </font></div></td>
        <td><input type="text" name="saisie_date_naissance" >
	<?php
	include_once("librairie_php/calendar.php");
	calendarpopupDim('id1','document.formulaire.saisie_date_naissance',$_SESSION["langue"],"1","0");
	?>
 	&nbsp;<font id='color2' ><b>*</b></font></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGMESS293 ?> </font></div></td>
        <td> M <input type="radio" name="saisie_sexe"  value="m" > -  F <input type="radio" name="saisie_sexe"  value="f" >
    </td></tr>
    <tr><td><div align="right"><font class="T2"><?php print  LANGMESS192 ?> : </font></div></td>
    <td> Oui <input type="radio" name="saisie_boursier"  value="1" > -  <?php print LANGNON ?> <input type="radio" name="saisie_boursier"  value="0" checked='checked'>
    </td></tr>
   </td></tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGMESS193 ?> : </font></div></td>
	<td> Oui <input type="radio" name="saisie_BDE"  value="1" > -  <?php print LANGNON ?> <input type="radio" name="saisie_BDE"  value="0" checked='checked' >
    </td></tr>
   </td></tr>
    <tr><td><div align="right"><font class="T2"><?php print  LANGMESS194 ?> : </font></div></td>
	<td> Oui <input type="radio" name="saisie_CDI"  value="1" > -  <?php print LANGNON ?> <input type="radio" name="saisie_CDI"  value="0" checked='checked' >
    </td></tr>
    <tr><td><div align="right"><font class="T2"><?php print  LANGMESS195 ?> : </font></div></td>
	<td> <input type="text" name="saisie_boursier_montant"   >
    </td></tr>
    <tr><td><div align="right"><font class="T2"><?php print  LANGMESS196 ?> : </font></div></td>
    <td> <input type="text" name="saisie_indemnite_stage"  size=4 > <?php print LANGTMESS442 ?>. <?php print LANGTMESS443 ?> :<input type="text" name="saisie_nbmoisindemnite_stage"  size=2 >
    </td></tr>


    <tr><td><div align="right"><font class="T2"><?php print LANGELE11?> : </font></div></td>
        <td><input type="text" name="saisie_nationalite"  maxlength='20' >
    </td></tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGEDIT6 ?> : </font></div></td>
        <td><input type="text" name="saisie_lieunais"  maxlength='40' >
    </td></tr>
    
	<tr><td><div align="right"><font class="T2"><?php print "D&eacute;partement de naissance" ?> &nbsp;:&nbsp; </font></div></td>
        <td><input type="text" name="saisie_departementnais"  maxlength='40' > <i>(ex : 75013)</i>
    </td></tr>
	
    <tr><td><div align="right"><font class="T2"><?php print LANGNA3ter?>:</font></div></td>
	         <td><input type="passwd" name="saisie_passwd_eleve"  maxlength=50 >&nbsp;<font id='color2' ><b>*</b></font> </td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE12?> :</font></div></td>
    <td><input type="text" name="saisie_numnational" maxlength=30  <?php if (AUTOINE == "oui") print "disabled='disabled' value='auto-création'" ?> ></td>
    </tr>
<tr><td><div align="right"><font class="T2"><?php print LANGTMESS444 ?> :</font></div></td>
         <td><input type="text" name="saisie_codecompta" maxlength=30  ></td>
    </tr>



<tr><td><div align="right"><font class="T2"><?php print LANGAGENDA63 ?> : </font></div></td>
        <td><input type="text" name="saisie_adr_eleve" size="30"  maxlength=100></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE15?>  : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr_eleve" size="30"  maxlength=6 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE16?>  : </font></div></td>
      <td><input type="text" name="saisie_commune_adr_eleve" size="30"  maxlength=40 ></td>
    </tr>
<tr><td><div align="right"><font class="T2"><?php print  LANGAGENDA73 ?>  : </font></div></td>
      <td><input type="text" name="saisie_pays_eleve" size="30"  maxlength=50 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGMESS199 ?>  : </font></div></td>
      <td><input type="text" name="saisie_tel_fixe_eleve" size="30"  maxlength=25 ></td>
    </tr>

 <tr><td><div align="right"><font class="T2"><?php print LANGMESS200 ?> <?php print LANGTITRE40?> :  </font></div></td>
      <td><input type="text" name="saisie_portable_eleve"  maxlength=25 size="30"  ></td>
    </tr>
 <tr><td><div align="right"><font class="T2"><?php print LANGELE244?> <?php print LANGTITRE40?> :  </font></div></td>
      <td><input type="text" name="saisie_email_eleve"  maxlength=48 size=30 ></td>
    </tr>
 <tr><td><div align="right"><font class="T2"><?php print LANGELE244 ?> <?php print  LANGTMESS445 ?> :  </font></div></td>
      <td><input type="text" name="saisie_emailpro_eleve"  maxlength=48 size=30 ></td>
    </tr>
	
	 <tr><td><div align="right"><font class="T2"><?php print "S&eacute;rie du bac" ?> &nbsp;:&nbsp;  </font></div></td>
      <td><input type="text" name="saisie_serie_bac"  maxlength=48 size=30 ></td>
    </tr>
	
	 <tr><td><div align="right"><font class="T2"><?php print "Ann&eacute;e d'obtention du bac" ?> &nbsp;:&nbsp;  </font></div></td>
      <td><input type="text" name="saisie_annee_bac"  maxlength=48 size=30 ></td>
    </tr>
	
	
	 <tr><td><div align="right"><font class="T2"><?php print "D&eacute;partement du bac" ?>  &nbsp;:&nbsp;  </font></div></td>
      <td><input type="text" name="saisie_departement_bac"  maxlength=48 size=30 ></td>
    </tr>
	
<tr><td><div align="right"><font class="T2"><?php print LANGRESA40 ?> <?php print LANGTITRE40?> :  </font></div></td>
      <td><textarea name="saisie_info_eleve"  cols=40 rows=3></textarea></td>
    </tr>
  </table>


<BR>
<ul><font class=T1 color='#CC0000'><b><?php print LANGELE13?></b></font></ul>
 <TABLE border="0"  width=100% align=center>

<tr><td align="right"><font class="T2"><?php print LANGMESS203 ?> : </font></td>
      <td><select name='situation_familiale'>
	   <option id='select1' value=''></option>
	   <option id='select1' value='<?php print LANGSITU1 ?>'><?php print LANGSITU1 ?></option>
	   <option id='select1' value='<?php print LANGSITU2 ?>'><?php print LANGSITU2 ?></option>
	   <option id='select1' value='<?php print LANGSITU3 ?>'><?php print LANGSITU3 ?></option>
	   <option id='select1' value='<?php print LANGSITU4 ?>'><?php print LANGSITU4 ?></option>
	   <option id='select1' value='<?php print LANGSITU5 ?>'><?php print LANGSITU5 ?></option>
	   <option id='select1' value='<?php print LANGSITU6 ?>'><?php print LANGSITU6 ?></option>
	   <option id='select1' value='<?php print LANGSITU7 ?>'><?php print LANGSITU7 ?></option>
	   <option id='select1' value='<?php print "Décédé(e)" ?>'><?php print "Décédé(e)" ?></option>
	   

	  </select></td>
    </tr>

    <tr><td align=right ><font class="T2"><?php print LANGMESS178 ?> 1 : </font></td><td>
<select name="saisie_civ1" >
<?php listingCiv() ?>
</select> [<a href='#' onclick="copieAdresse(); return false;" ><?php print LANGMESS204 ?></a>]
</td></tr>
<tr><td width="50%" ><div align="right"><font class="T2"><?php print LANGELE2?> <?php print LANGTITRE41 ?> 1 : </font></div></td>
       <td><input type="text" name="saisie_nomtuteur" size="30"  maxlength=30 ></td>
   </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE3?> <?php print LANGTITRE41 ?> 1 :  </font></div></td>
        <td><input type="text" name="saisie_prenomtuteur" size="30"  maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE14?> : </font></div></td>
        <td><input type="text" name="saisie_adr1" size="30"  maxlength=100></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE15?> 1 : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr1" size="30"  maxlength=6 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE16?> 1 : </font></div></td>
      <td><input type="text" name="saisie_commune_adr1" size="30"  maxlength=40 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGMESS200 ?> 1 : </font></div></td>
      <td><input type="text" name="saisie_tel_port_1" size="30"  maxlength=25 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE244?> <?php print LANGTITRE42 ?> 1 :  </font></div></td>
      <td><input type="text" name="saisie_email" size="30"  maxlength=150 ></td>
    </tr>
   <tr><td><div align="right"><font class="T2"><?php print LANGNA3bis ?> 1 :</font></div></td>
         <td><input type="passwd" name="saisie_passwd"  size="30" maxlength=50 ></td>
    </tr> 

    <tr><td align=right ><font class="T2"><?php print LANGMESS178 ?> 2 : </td><td>
<select name="saisie_civ2" >
<?php listingCiv() ?>
</select> 
</td></tr>
   <tr><td width="50%" ><div align="right"><font class="T2"><?php print LANGELE2?> <?php print LANGTITRE41 ?> 2 : </font></div></td>
       <td><input type="text" name="saisie_nomtuteur2" size="30"  maxlength=30 ></td>
   </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE3?> <?php print LANGTITRE41 ?> 2 :  </font></div></td>
        <td><input type="text" name="saisie_prenomtuteur2" size="30"  maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE17?> : </font></div></td>
        <td><input type="text" name="saisie_adr2" size="30"  maxlength=100 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE15?> 2 : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr2" size="30"  maxlength=6 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE16?> 2 : </font></div></td>
      <td><input type="text" name="saisie_commune_adr2" size="30"  maxlength=40 ></td>
</tr>
<tr><td><div align="right"><font class="T2"><?php print LANGMESS200 ?> 2 : </font></div></td>
      <td><input type="text" name="saisie_tel_port_2" size="30"  maxlength=25 ></td>
    </tr>
<tr><td><div align="right"><font class="T2"><?php print LANGELE244?> <?php print LANGTITRE42 ?> 2 :  </font></div></td>
      <td><input type="text" name="saisie_email_2" size="30"  maxlength=150 ></td>
    </tr>
   <tr><td><div align="right"><font class="T2"><?php print LANGNA3bis ?> 2 :</font></div></td>
         <td><input type="passwd" name="saisie_passwd_parent2"  size="30" maxlength=50 ></td>
    </tr> 

    <tr><td><div align="right"><font class="T2"><?php print LANGELE20?> : </font></div></td>
      <td><input type="text" name="saisie_telephone" size="30"  maxlength=18 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE21?> : </font></div></td>
      <td><input type="text" name="saisie_profession_pere" size="30"  maxlength=20 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE22?> :  </font></div></td>
      <td><input type="text" name="saisie_tel_prof_pere" size="30"  maxlength=18 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE23?> : </font></div></td>
      <td><input type="text" name="saisie_profession_mere" size="30"  maxlength=20 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE24?> :  </font></div></td>
      <td><input type="text" name="saisie_tel_prof_mere" size="30"  maxlength=18 ></td>
    </tr>
   
  </table>

<BR>
<ul><font class=T1 color='#CC0000'><b><?php print LANGELE25?></b></font></ul>
  <TABLE border="0" width=100% align="center">
    <tr><td width="50%"><div align="right"><font class="T2"><?php print LANGELE26?> :</font></div></td>
          <td><input type="text" name="saisie_nom_etablissement" size="35"  maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE27?> :</font></div></td>
      <td><input type="text" name="saisie_numero_etablissement" size="35"  maxlength=30 ></td>
      <tr><td><div align="right"><font class="T2"><?php print LANGbasededoni41 ?> :</font></div></td>
      <td><input type="text" name="saisie_classe_ant" size="35"  maxlength=30 ></td>
</tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGbasededoni42 ?> : </font></div></td>
        <td><input type="text"  name="saisie_date_ant" size="35">
 </td>
    </tr>
<tr><td><div align="right"><font class="T2"><?php print LANGELE15?> : </font></div></td>
<td><input type="text" name="saisie_code_postal_etablissement" size="35"  maxlength=6 ></td>
</tr>
<tr><td><div align="right"><font class="T2"><?php print LANGELE16?> : </font></div></td>
<td><input type="text" name="saisie_commune_etablissement" size="35"  maxlength=30 ></td>
</tr>
</table>

<br>
<table  border="0" align="center">
<tr><td height="53">
<div align="center">
<script language=JavaScript>buttonMagicSubmit('<?php print LANGBT7?>','create'); //text,nomInput</script>
<br><br>
</div></td></tr>
</table>
</form>

	<script>
	function copieAdresse() {
		document.formulaire.saisie_nomtuteur.value=document.formulaire.saisie_nom.value;
		document.formulaire.saisie_adr1.value=document.formulaire.saisie_adr_eleve.value;
		document.formulaire.saisie_code_post_adr1.value=document.formulaire.saisie_code_post_adr_eleve.value;
		document.formulaire.saisie_commune_adr1.value=document.formulaire.saisie_commune_adr_eleve.value;
		document.formulaire.saisie_telephone.value=document.formulaire.saisie_tel_fixe_eleve.value
		document.formulaire.saisie_nomtuteur2.value=document.formulaire.saisie_nom.value;
		document.formulaire.saisie_adr2.value=document.formulaire.saisie_adr_eleve.value;
		document.formulaire.saisie_code_post_adr2.value=document.formulaire.saisie_code_post_adr_eleve.value;
		document.formulaire.saisie_commune_adr2.value=document.formulaire.saisie_commune_adr_eleve.value;
	}
	</script>

<!-- // fin  -->
</td></tr></table>


<?php
if(isset($_POST["create"])):
        // connexion P

if (verif_compte_cree($_POST["saisie_nom"],$_POST["saisie_prenom"])) {


// création du tableau de hash contenant les paramètres de la fonction create_eleve
$params[ne]=		$_POST["saisie_nom"];
$params[pe]=		$_POST["saisie_prenom"];
$params[ce]=		$_POST["saisie_classe"];
$params[lv1]=		$_POST["saisie_lv1"];
$params[lv2]=		$_POST["saisie_lv2"];
$params[option]=	$_POST["saisie_option"];
$params[regime]=	$_POST["saisie_regime"];
$params[naiss]=		$_POST["saisie_date_naissance"];
$params[nat]=		$_POST["saisie_nationalite"];
$params[mdp]=		$_POST["saisie_passwd"];
$params[mdp2]=		$_POST["saisie_passwd_parent2"];
$params[mdpeleve]=  	$_POST["saisie_passwd_eleve"];
$params[nt]=		$_POST["saisie_nomtuteur"];
$params[pt]=		$_POST["saisie_prenomtuteur"];
$params[adr1]=		$_POST["saisie_adr1"];
$params[cpadr1]=	$_POST["saisie_code_post_adr1"];
$params[commadr1]=	$_POST["saisie_commune_adr1"];
$params[nadr2]=		$_POST["saisie_num_adr2"];
$params[adr2]=		$_POST["saisie_adr2"];
$params[cpadr2]=	$_POST["saisie_code_post_adr2"];
$params[commadr2]=	$_POST["saisie_commune_adr2"];
$params[tel]=		$_POST["saisie_telephone"];
$params[profp]=		$_POST["saisie_profession_pere"];
$params[telprofp]=	$_POST["saisie_tel_prof_pere"];
$params[profm]=		$_POST["saisie_profession_mere"];
$params[telprofm]=	$_POST["saisie_tel_prof_mere"];
$params[nomet]=		$_POST["saisie_nom_etablissement"];
$params[numet]=		$_POST["saisie_numero_etablissement"];
$params[cpet]=		$_POST["saisie_code_postal_etablissement"];
$params[commet]=	$_POST["saisie_commune_etablissement"];
$params[numero_eleve]=	$_POST["saisie_numnational"];
$params[email]=		$_POST["saisie_email"];
$params[classe_ant]=    $_POST["saisie_classe_ant"];
$params[annee_ant]=     $_POST["saisie_date_ant"];
$params[civ_1]=		$_POST["saisie_civ1"];
$params[civ_2]=		$_POST["saisie_civ2"];
$params[tel_eleve]=	$_POST["saisie_portable_eleve"];
$params[mail_eleve]=	$_POST["saisie_email_eleve"];
$params[mailpro_eleve]=	$_POST["saisie_emailpro_eleve"];
$params[nom_resp2]=	$_POST["saisie_nomtuteur2"];
$params[prenom_resp2]=	$_POST["saisie_prenomtuteur2"];
$params[lieunais]=	$_POST["saisie_lieunais"];
$params[tel_port_1]=	$_POST["saisie_tel_port_1"];
$params[tel_port_2]=	$_POST["saisie_tel_port_2"];
$params[email_2]=	$_POST["saisie_email_2"];
$params[codecompta]=    $_POST["saisie_codecompta"];
$params[sexe]=    	$_POST["saisie_sexe"];
$params[information]=  	$_POST["saisie_info_eleve"];
$params[adr_eleve]= 	$_POST["saisie_adr_eleve"];
$params[commune_eleve]= $_POST["saisie_commune_adr_eleve"];
$params[ccp_eleve]= 	$_POST["saisie_code_post_adr_eleve"];
$params[tel_fixe_eleve]=$_POST["saisie_tel_fixe_eleve"];
$params[pays_eleve]    =$_POST["saisie_pays_eleve"];

$params[boursier]      =$_POST["saisie_boursier"];
$params[cdi]           =$_POST["saisie_CDI"];
$params[bde]           =$_POST["saisie_BDE"];

$params[boursier_montant]=$_POST["saisie_boursier_montant"];
$params[indemnite_stage]=$_POST["saisie_indemnite_stage"];
$params[nbmoisindemnite_stage]=$_POST["saisie_nbmoisindemnite_stage"];
$params[rangement]=$_POST["saisie_rangement"];
$params[situation_familiale]=$_POST["situation_familiale"];
$params[annee_scolaire]=$_POST["annee_scolaire"];

$params[saisie_serie_bac]=$_POST["saisie_serie_bac"];
$params[saisie_annee_bac]=$_POST["saisie_annee_bac"];
$params[saisie_departement_bac]=$_POST["saisie_departement_bac"];
$params[saisie_departementnais]=$_POST["saisie_departementnais"];


// nouvelle version de create_eleve()
$ascii=1;
$cr=create_eleve($params,$ascii);
        if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION","élève ".$_POST["saisie_nom"]);
                alertJs(LANGELE28);
		 if (FINANCIERVATEL == "oui") { 
		//****** APRES_MAJ_TRIADE_AUTO - 20100826232122 - IGONE : CODE AJOUTE AUTOMATIQUEMENT PAR SCRIPT 'admin_apres_maj_triade' ******
		// 20100512 - AP : on veut proposer la saisie du RIB une fois l'eleve cree 
		
				// Recherche de l'eleve a partir de ce qui a ete donne dans le formulaire
				$sql_eleve  = "SELECT elev_id ";
				$sql_eleve .= "FROM " . PREFIXE . "eleves ";
				$sql_eleve .= "WHERE nom = '" . $params[ne] . "' ";
				$sql_eleve .= "AND prenom = '" . $params[pe] . "' ";
				$sql_eleve .= "AND classe = " . $params[ce] . " ";
				$sql_eleve .= "AND date_naissance = '" . dateFormBase($params[naiss]) . "' ";
				//echo $sql_eleve;
				$res_eleve=execSql($sql_eleve);
				
				// on verifie si on a bien trouve le nouvel eleve
				if($res_eleve->numRows() > 0) {
					$ligne_eleve = &$res_eleve->fetchRow();
					// On demande a l'utilisateur si il veut gérer les RIB maintenant ou on
				?>
                	<script language="javascript">
						if(confirm("<?php echo LANGTMESS446 ?>")) {
							open('module_financier/rib_editer.php?elev_id=<?php echo $ligne_eleve[0];?>','rib','width=550,height=320')
						}
					</script>
                <?php
				
				}
				//***************************************************************************
  }

       }else if ($cr == -3) {
				$affiche=affichageMessageSecurite2();	
				alertJs($affiche);
		}else{
                alertJs(LANGPASSG3);
        }
}else {
	alertJs(LANGELE29);
}

endif;
Pgclose();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
