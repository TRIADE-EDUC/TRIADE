<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="stylesheet" type="text/CSS" href="./librairie_css/css.css" media="screen" />
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Envoi des candidatures</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
  <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
  <script language="JavaScript" src="./librairie_js/lib_css.js"></script>
	<?php
	include_once("./librairie_php/lib_netscape.php");
	include_once("./librairie_php/lib_licence2.php");
	include_once("./common/lib_ecole.php");
	include_once("./common/config2.inc.php");
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
	include_once("./common/version.php");
	if ($_COOKIE["langue-triade"] == "fr") {
        	include_once("./librairie_php/langue-text-fr.php");
	        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
        	print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
	}elseif ($_COOKIE["langue-triade"] == "en") {
        	print "<script type=text/javascript src='librairie_js/langueenmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueenfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-en.php");
	}elseif ($_COOKIE["langue-triade"] == "es") {
        	print "<script type=text/javascript src='librairie_js/langueesmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueesfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-es.php");
	}elseif ($_COOKIE["langue-triade"] == "bret") {
        	print "<script type=text/javascript src='librairie_js/languebretmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languebretfunction-depart.js'></script>\n";
		include_once("./librairie_php/langue-text-bret.php");
	}elseif ($_COOKIE["langue-triade"] == "arabe") {
        	print "<script type=text/javascript src='librairie_js/languearabemenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languearabefunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-arabe.php");
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}
	if (HTTPS == "non") {
		print "<script type='text/javascript'>var http='http://';</script>\n";
	}else{
		print "<script type='text/javascript'>var http='https://';</script>\n";
	}
	if (POPUP == "non") {
		print "<script type='text/javascript'>var popup='non';</script>\n";
	}else {
		print "<script type='text/javascript'>var popup='oui';</script>\n";
	}
	print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
	print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";

	?>
	<script type="text/javascript" >var mailcontact="<?php 
		if ((MAILCONTACT != "") && (defined("MAILCONTACT")) ) { 
			print MAILCONTACT; 
		}else{ 
			print ""; 
		} ?>";</script>
	<script type="text/javascript" >var urlcontact="<?php 
		if ((URLCONTACT != "") && (defined("URLCONTACT"))) { 
			print URLCONTACT; 
		}else{ 
			print ""; 
		}  ?>"; </script>
	<script type="text/javascript" >var urlnomcontact="<?php 
		if ((URLNOMCONTACT != "") && (defined("URLNOMCONTACT"))) { 
			$urlnomcontact=preg_replace('/ /',"&nbsp;",URLNOMCONTACT);
			print URLNOMCONTACT; 
		}else{ 
			print ""; 
		} ?>"; </script>

<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>

	<script type="text/javascript" src="./librairie_js/menudepart.js"></script>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center" >
	<div align='center'><?php top_h(); ?>
	<script type="text/javascript" src="./librairie_js/menudepart1.js"></script>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Candidature Elève</font></B></td></tr>
	<tr id='cadreCentral0'>
	<td >
	<!-- // fin  -->
<BR>
<?php 
$cnx=cnx();
?>
<form method=post action="add_eleve.php" onsubmit="return valide_creat_eleve3()" name="formulaire"  >

<ul><font class=T2 color='#CC0000'><b>Renseignements Elève</b></font></ul>

  <TABLE border="0"  width=100% align="center">
     <tr><td width="50%" ><div align="right"><font class="T2">Nom : </font></div></td>
        <td><input type="text" name="saisie_nom" maxlength=30 > &nbsp;<font id='color2' ><b>*</b></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Prénom :  </font></div></td>
        <td><input type="text" name="saisie_prenom"  maxlength=50 > &nbsp;<font id='color2' ><b>*</b> </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Classe : </font></div></td>
        <td><select name="saisie_classe" >
	<option  STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
<?php
select_classe2(20); // creation des options
?>
            </select> &nbsp;<font id='color2' ><b>*</b>
        </td>
    </tr>
  <tr><td><div align="right"><font class="T2">Pour l'année scolaire :  </font></div></td>
	<td><select name='annee_scolaire'>
<?php
filtreAnneeScolaireSelectFutur(); // creation des options
?>
	    </select> &nbsp;<font id='color2' ><b>*</b> </td>
    </tr>
    <tr><td><div align="right"><font class="T2">LV1 : </font></div></td>
        <td><select name="saisie_lv1" size="1">
	<option selected value=''> </option>
<?php
select_matiere2(); // creation des options
?>
            </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2">LV2 : </font></div></td>
        <td><select name="saisie_lv2" size="1">
<option selected value=''> </option>
<?php
select_matiere2(); // creation des options
?>
	    </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2">Option : </font></div></td>
    <td>
<select name="saisie_option2" size="1">
<option selected value=''> </option>
<?php
select_matiere2(); // creation des options
?>
	    </select>
    </td>
    </tr>
    <tr><td><div align="right"><font class="T2">R&eacute;gime : </font></div></td>
    <td><input type="radio" name="saisie_regime" value="Interne" id='.btradio1'> Interne <br>
   <input type="radio" name="saisie_regime" value="Demi-pension" id='.btradio1'> Demi-pensionnaire <br>
   <input type="radio" name="saisie_regime" value="Externe" id='.btradio1'> Externe</td>
      </tr>

    <tr><td><div align="right"><font class="T2">Boursier : </font></div></td>
   <td><input type="radio" name="saisie_boursier" value="1" id='.btradio1'  /> oui <input type="radio" name="saisie_boursier" value="0" id='.btradio1'  /> non <br>
      </tr>


    <tr><td><div align="right"><font class="T2">Date de naissance : </font></div></td>
        <td>
	<input type="text" name="saisie_date_naissance" readonly="readonly">
	<?php
	include_once("librairie_php/calendar.php");
	calendarpopupDim('id1','document.formulaire.saisie_date_naissance',$_COOKIE["langue-triade"],"1","0");
	?>
 &nbsp;<font id='color2' ><b>*</b> </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Sexe : </font></div></td>
        <td> M <input type="radio" name="saisie_sexe"  value="m" > -  F <input type="radio" name="saisie_sexe"  value="f" >
    </td></tr>
    <tr><td><div align="right"><font class="T2">Nationalité : </font></div></td>
        <td><input type="text" name="saisie_nationalite"  maxlength=20 >
    </td></tr>
    <tr><td><div align="right"><font class="T2">Lieu de naissance : </font></div></td>
        <td><input type="text" name="saisie_lieu_naissance"  maxlength='40' >
    </td></tr>
    
    <tr><td><div align="right"><font class="T2">Mot de passe élève :</font></div></td>
	<td><input type="passwd" name="saisie_passwd_eleve"  maxlength=50 > &nbsp;<font id='color2' ><b>*</b> </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro étudiant :</font></div></td>
        <td><input type="text" name="saisie_numero_eleve" maxlength=30  ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Tél Portable élève :  </font></div></td>
        <td><input type="text" name="saisie_tel_eleve"  maxlength=18 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Email élève :  </font></div></td>
        <td><input type="text" name="saisie_email_eleve"  maxlength=48 size=30 > &nbsp;<font id='color2' ><b>*</b></td>
    </tr>


    <tr><td><div align="right"><font class="T2"><?php print "Adresse élève" ?> : </font></div></td>
            <td><input type="text" name="saisie_adr_eleve" size="30"  maxlength=100></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE15?> élève  : </font></div></td>
    <td><input type="text" name="saisie_code_post_adr_eleve" size="30"  maxlength=6 ></td>
   </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE16?> élève  : </font></div></td>
    <td><input type="text" name="saisie_commune_adr_eleve" size="30"  maxlength=40 ></td>
   </tr>
   <tr><td><div align="right"><font class="T2"><?php print "Pays"?> élève : </font></div></td>
   <td><input type="text" name="saisie_pays_eleve" size="30"  maxlength=50 ></td>
   </tr>
   <tr><td><div align="right"><font class="T2"><?php print "Téléphone fixe" ?> élève : </font></div></td>
   <td><input type="text" name="saisie_tel_fixe_eleve" size="30"  maxlength=25 ></td>
   </tr>

  </table>


<BR>
<ul><font class=T2 color='#CC0000'><b>Renseignements Famille</b></font></ul>
 <TABLE border="0"  width=100% align=center>
<tr><td align=right ><font class="T2">Civ 1 : </font></td><td>
<select name="saisie_civ_1" >
<option value='6' id='select1' >M. ou Mme</option>
<option value='0' id='select1' >M.</option>
<option value='1' id='select1'>Mme</option>
<option value='2' id='select1'>Mlle</option>
<option value='3' id='select1'>Ms</option>
<option value='4' id='select1'>Mr</option>
<option value='5' id='select1' >Mrs</option>
</select>
</td></tr>
<tr><td width="50%" ><div align="right"><font class="T2">Nom resp. 1 : </font></div></td>
       <td><input type="text" name="saisie_nomtuteur" size="30"  maxlength=30 ></td>
   </tr>
    <tr><td><div align="right"><font class="T2">Prénom resp. 1 :  </font></div></td>
        <td><input type="text" name="saisie_prenomtuteur" size="30"  maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Adresse 1 : </font></div></td>
        <td><input type="text" name="saisie_adr1" size="30"  maxlength=100></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Code postal 1 : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr1" size="30"  maxlength=6 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Commune 1 : </font></div></td>
      <td><input type="text" name="saisie_commune_adr1" size="30"  maxlength=40 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Tél. portable  1 : </font></div></td>
      <td><input type="text" name="saisie_tel_port_1" size="30"  maxlength=25 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Email tuteur 1 :  </font></div></td>
      <td><input type="text" name="saisie_email" size="30"  maxlength=150 ></td>
    </tr>

<tr><td align=right ><font class="T2">Civ 2 : </td><td>
<select name="saisie_civ_2" >
<option value='6' id='select1' >M. ou Mme</option>
<option value='0' id='select1' >M.</option>
<option value='1' id='select1'>Mme</option>
<option value='2' id='select1'>Mlle</option>
<option value='3' id='select1'>Ms</option>
<option value='4' id='select1'>Mr</option>
<option value='5' id='select1' >Mrs</option>
</select>
</td></tr>
   <tr><td width="50%" ><div align="right"><font class="T2">Nom resp. 2 : </font></div></td>
       <td><input type="text" name="saisie_nom_resp_2" size="30"  maxlength=30 ></td>
   </tr>
    <tr><td><div align="right"><font class="T2">Prénom resp. 2 :  </font></div></td>
        <td><input type="text" name="saisie_prenom_resp_2" size="30"  maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Adresse 2 : </font></div></td>
        <td><input type="text" name="saisie_adr2" size="30"  maxlength=100 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Code postal 2 : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr2" size="30"  maxlength=6 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Commune 2 : </font></div></td>
      <td><input type="text" name="saisie_commune_adr2" size="30"  maxlength=40 ></td>
</tr>
<tr><td><div align="right"><font class="T2">Tél. portable  2 : </font></div></td>
      <td><input type="text" name="saisie_tel_port_2" size="30"  maxlength=25 ></td>
    </tr>
<tr><td><div align="right"><font class="T2">Email tuteur 2 :  </font></div></td>
      <td><input type="text" name="saisie_email_resp_2" size="30"  maxlength=150 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro de téléphone : </font></div></td>
      <td><input type="text" name="saisie_telephone" size="30"  maxlength=18 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Profession du père : </font></div></td>
      <td><input type="text" name="saisie_profession_pere" size="30"  maxlength=20 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Téléphone du père :  </font></div></td>
      <td><input type="text" name="saisie_tel_prof_pere" size="30"  maxlength=18 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Profession de la mère : </font></div></td>
      <td><input type="text" name="saisie_profession_mere" size="30"  maxlength=20 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Téléphone de la mère :  </font></div></td>
      <td><input type="text" name="saisie_tel_prof_mere" size="30"  maxlength=18 ></td>
    </tr>
   <tr><td><div align="right"><font class="T2">Mot de passe parent  :</font></div></td>
         <td><input type="passwd" name="saisie_passwd"  size="30" maxlength=50 ></td>
    </tr> 
  </table>

<BR>
<ul><font class=T2 color='#CC0000'><b>Ecole antérieure</b></font></ul>
  <TABLE border="0" width=100% align="center">
    <tr><td width="50%"><div align="right"><font class="T2">Nom de l'établissement :</font></div></td>
          <td><input type="text" name="saisie_nom_etablissement" size="35"  maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro établissement :</font></div></td>
      <td><input type="text" name="saisie_numero_etablissement" size="35"  maxlength=30 ></td>
      <tr><td><div align="right"><font class="T2">Classe antérieure :</font></div></td>
      <td><input type="text" name="saisie_classe_ant" size="35"  maxlength=30 ></td>
</tr>
    <tr><td><div align="right"><font class="T2">Année antérieure : </font></div></td>
        <td><input type="text"  name="saisie_date_ant" size="35">
 </td>
    </tr>
<tr><td><div align="right"><font class="T2">Code postal : </font></div></td>
<td><input type="text" name="saisie_code_postal_etablissement" size="35"  maxlength=6 ></td>
</tr>
<tr><td><div align="right"><font class="T2">Commune : </font></div></td>
<td><input type="text" name="saisie_commune_etablissement" size="35"  maxlength=30 ></td>
</tr>
</table>

<br>
<table  border="0" align="center">
<tr><td height="53">
<div align="center">

<script language=JavaScript>buttonMagicSubmit('Envoyer ma candidature','create'); //text,nomInput</script>
<br><br>
</div></td></tr>
</table>
</form>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
