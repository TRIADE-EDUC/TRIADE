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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuscolaire") {
	if (MODULEPREINSCRIPTIONVIESCOLAIRE != "oui") validerequete("menuadmin");
}else{
	validerequete("menuadmin");
}
$cnx=cnx();

if (isset($_POST["create"])) {
	$nom = trim($_POST['saisie_nom']);
	$prenom = trim($_POST['saisie_prenom']);
	$classe = trim($_POST['saisie_classe']);
	$lv1 = trim($_POST['saisie_lv1']);
	$lv2 = trim($_POST['saisie_lv2']);
	$regime = trim($_POST['saisie_regime']);
	$date_naissance = dateFormBase(trim($_POST['saisie_date_naissance']));
	$lieu_naissance = trim($_POST['saisie_lieu_naissance']);
	$nationalite = trim($_POST['saisie_nationalite']);
	$passwd = trim($_POST['saisie_passwd']);
	$passwd_eleve = trim($_POST['saisie_passwd_eleve']);
	$civ_1 = trim($_POST['saisie_civ_1']);
	$nomtuteur = trim($_POST['saisie_nomtuteur']);
	$prenomtuteur = trim($_POST['saisie_prenomtuteur']);
	$adr1 = trim($_POST['saisie_adr1']);
	$code_post_adr1 = trim($_POST['saisie_code_post_adr1']);
	$commune_adr1 = trim($_POST['saisie_commune_adr1']);
	$tel_port_1 = trim($_POST['saisie_tel_port_1']);
	$civ_2 = trim($_POST['saisie_civ_2']);
	$nom_resp_2 = trim($_POST['saisie_nom_resp_2']);
	$prenom_resp_2 = trim($_POST['saisie_prenom_resp_2']);
	$adr2 = trim($_POST['saisie_adr2']);
	$code_post_adr2 = trim($_POST['saisie_code_post_adr2']);
	$commune_adr2 = trim($_POST['saisie_commune_adr2']);
	$tel_port_2 = trim($_POST['saisie_tel_port_2']);
	$telephone = trim($_POST['saisie_telephone']);
	$profession_pere = trim($_POST['saisie_profession_pere']);
	$tel_prof_pere = trim($_POST['saisie_tel_prof_pere']);
	$profession_mere = trim($_POST['saisie_profession_mere']);
	$tel_prof_mere = trim($_POST['saisie_tel_prof_mere']);
	$nom_etablissement = trim($_POST['saisie_nom_etablissement']);
	$numero_etablissement = trim($_POST['saisie_numero_etablissement']);
	$code_postal_etablissement = trim($_POST['saisie_code_postal_etablissement']);
	$commune_etablissement = trim($_POST['saisie_commune_etablissement']);
	$numero_eleve = trim($_POST['saisie_numero_eleve']);
	$photo = trim($_POST['saisie_photo']);
	$email = trim($_POST['saisie_email']);
	$email_eleve = trim($_POST['saisie_email_eleve']);
	$email_resp_2 = trim($_POST['saisie_email_resp_2']);
	$class_ant = trim($_POST['saisie_classe_ant']);
	$annee_ant = trim($_POST['saisie_date_ant']);
	$valid_forward_mail_eleve = trim($_POST['saisie_valid_forward_mail_eleve']);
	$valid_forward_mail_parent = trim($_POST['saisie_valid_forward_mail_parent']);
	$tel_eleve = trim($_POST['saisie_tel_eleve']);
	$sexe = trim($_POST['saisie_sexe']);
	$option2 = trim($_POST['saisie_option2']);
	$annee_scolaire=$_POST['saisie_annee_scolaire']." - ".$_POST['saisie_annee_scolaire']+1;
	$datedemande=dateDMY2();
	$adresse_eleve=$_POST['saisie_adr_eleve'];
	$codecompta=$_POST["saisie_codecompta"];
	$code_post_adr_eleve=$_POST["saisie_code_post_adr_eleve"];
	$commune_adr_eleve=$_POST["saisie_commune_adr_eleve"];
	$pays_eleve=$_POST["saisie_pays_eleve"];
	$tel_fixe_eleve=$_POST["saisie_tel_fixe_eleve"];
	$info_eleve=$_POST["saisie_info_eleve"];
	
	// on ecris la requete sql 
	$sql = "SELECT * FROM ${prefixe}preinscription_eleves WHERE nom='$nom' AND prenom='$prenom' AND date_naissance='$date_naissance' ";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		alertJs("Candidature déjà enregistrée");
	}else{
		$sql = "INSERT INTO ${prefixe}preinscription_eleves (nom,prenom,classe,lv1,lv2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,tel_eleve,sexe,option2,date_demande,annee_scolaire,information,adr_eleve,ccp_eleve,commune_eleve,tel_fixe_eleve,pays_eleve) VALUES ('$nom','$prenom','$classe','$lv1','$lv2','$regime','$date_naissance','$lieu_naissance','$nationalite','$passwd','$passwd_eleve','$civ_1','$nomtuteur','$prenomtuteur','$adr1','$code_post_adr1','$commune_adr1','$tel_port_1','$civ_2','$nom_resp_2','$prenom_resp_2','$adr2','$code_post_adr2','$commune_adr2','$tel_port_2','$telephone','$profession_pere','$tel_prof_pere','$profession_mere','$tel_prof_mere','$nom_etablissement','$numero_etablissement','$code_postal_etablissement','$commune_etablissement','$numero_eleve','$photo','$email','$email_eleve','$email_resp_2','$class_ant','$annee_ant','$tel_eleve','$sexe','$option2','$datedemande','$annee_scolaire','$info_eleve','$adresse_eleve','$code_post_adr_eleve','$commune_adr_eleve','$tel_fixe_eleve','$pays_eleve');";
		execSql($sql);
		alertJs("Candidature enregistrée");
	}	
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Liste des pré-inscriptions" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<br>
<form method=post action="preinscription_direction.php" onsubmit="return valide_creat_eleve3()" name="formulaire"  >

<ul><font class=T2 color='#CC0000'><b>Renseignements sur l'élève</b></font></ul>

  <TABLE border="0"  width=100% align="center">
     <tr><td width="50%" ><div align="right"><font class="T2">Nom : </font></div></td>
        <td><input type="text" name="saisie_nom" maxlength=30 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Prénom :  </font></div></td>
        <td><input type="text" name="saisie_prenom"  maxlength=50 ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Classe demandée : </font></div></td>
        <td><select name="saisie_classe" >
	<option  STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
<?php
select_classe2(20); // creation des options
?>
            </select>
        </td>
    </tr>

<tr><td><div align="right"><font class="T2">Année scolaire :  </font></div></td>
        <td><input type="text" name="saisie_annee_scolaire" size=4 maxlength=4 value="<?php print date("Y") ?>" ></td>
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
    <tr><td><div align="right"><font class="T2">Date de naissance : </font></div></td>
        <td>
	<input type="text" name="saisie_date_naissance" onKeyPress="onlyChar(event)" >
	<?php
	include_once("librairie_php/calendar.php");
	calendarpopupDim('id1','document.formulaire.saisie_date_naissance',$_COOKIE["langue-triade"],"1","0");
	?>
 </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Sexe : </font></div></td>
        <td> M <input type="radio" name="saisie_sexe"  value="m" > -  F <input type="radio" name="saisie_sexe"  value="f" >
    </td></tr>
    <tr><td><div align="right"><font class="T2">Nationalité : </font></div></td>
        <td><input type="text" name="saisie_nationalite"  maxlength=20 >
    </td></tr>
    <tr><td><div align="right"><font class="T2">Lieu de naissance : </font></div></td>
        <td><input type="text" name="saisie_lieu_naissance"  maxlength=25 >
    </td></tr>
    
    <tr><td><div align="right"><font class="T2">Mot de passe élève :</font></div></td>
	         <td><input type="passwd" name="saisie_passwd_eleve"  maxlength=50 > </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro étudiant :</font></div></td>
         <td><input type="text" name="saisie_numero_eleve" maxlength=30  ></td>
    </tr>
    
    <tr><td><div align="right"><font class="T2"><?php print "Adresse" ?> : </font></div></td>
            <td><input type="text" name="saisie_adr_eleve" size="30"  maxlength=100></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE15?>  : </font></div></td>
    <td><input type="text" name="saisie_code_post_adr_eleve" size="30"  maxlength=6 ></td>
   </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE16?>  : </font></div></td>
    <td><input type="text" name="saisie_commune_adr_eleve" size="30"  maxlength=40 ></td>
   </tr>
   <tr><td><div align="right"><font class="T2"><?php print "Pays"?>  : </font></div></td>
   <td><input type="text" name="saisie_pays_eleve" size="30"  maxlength=50 ></td>
   </tr>
   <tr><td><div align="right"><font class="T2"><?php print "Téléphone " ?>  : </font></div></td>
   <td><input type="text" name="saisie_tel_fixe_eleve" size="30"  maxlength=25 ></td>
   </tr>

   <tr><td><div align="right"><font class="T2"><?php print "Tél. Portable" ?> <?php print LANGTITRE40?> :  </font></div></td>
   <td><input type="text" name="saisie_tel_eleve"  maxlength=25 size="30"  ></td>
   </tr>
<tr><td><div align="right"><font class="T2"><?php print LANGELE244?> <?php print LANGTITRE40?> :  </font></div></td>
   <td><input type="text" name="saisie_email_eleve"  maxlength=48 size=30 ></td>
   </tr>
<tr><td><div align="right"><font class="T2"><?php print "Information"?> <?php print LANGTITRE40?> :  </font></div></td>
 <td><textarea name="saisie_info_eleve"  cols=40 rows=3></textarea></td>
   </tr>
  </table>


<BR>
<ul><font class=T2 color='#CC0000'><b>Renseignements sur la famille</b></font></ul>
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
</select>  [<a href='#' onclick="copieAdresse(); return false;" >copier adresse</a>]
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

	<script>
	function copieAdresse() {
		document.formulaire.saisie_nomtuteur.value=document.formulaire.saisie_nom.value;
		document.formulaire.saisie_adr1.value=document.formulaire.saisie_adr_eleve.value;
		document.formulaire.saisie_code_post_adr1.value=document.formulaire.saisie_code_post_adr_eleve.value;
		document.formulaire.saisie_commune_adr1.value=document.formulaire.saisie_commune_adr_eleve.value;
		document.formulaire.saisie_telephone.value=document.formulaire.saisie_tel_fixe_eleve.value
		document.formulaire.saisie_nom_resp_2.value=document.formulaire.saisie_nom.value;
		document.formulaire.saisie_adr2.value=document.formulaire.saisie_adr_eleve.value;
		document.formulaire.saisie_code_post_adr2.value=document.formulaire.saisie_code_post_adr_eleve.value;
		document.formulaire.saisie_commune_adr2.value=document.formulaire.saisie_commune_adr_eleve.value;
	}
	</script>


<br></td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      print "</SCRIPT>";
      top_d();
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
      print "</SCRIPT>";
    endif ;

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
