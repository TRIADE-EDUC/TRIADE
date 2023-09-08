<?php
session_start();
error_reporting(0);
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
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Pré-inscriptions" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<br>
<?php

if (isset($_GET["ideleve"])) {
	$ideleve=$_GET["ideleve"];
}

if  (isset($_POST["modif"])) {
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
	$ideleve=$_POST["id_eleve"];
	$annee_scolaire=$_POST["saisie_annee_scolaire"]." - ".$_POST["saisie_annee_scolaire"]+1;
	$information=$_POST["saisie_info_eleve"];
	$adr_eleve=$_POST["saisie_adr_eleve"];
	$ccp_eleve=$_POST["saisie_code_post_adr_eleve"];
	$commune_eleve=$_POST["saisie_commune_adr_eleve"];
	$tel_fixe_eleve=$_POST["saisie_tel_fixe_eleve"];
	$pays_eleve=$_POST["saisie_pays_eleve"];
	$boursier=$_POST["saisie_boursier"];

	$sql = "UPDATE ${prefixe}preinscription_eleves set 
		information='$information',
		adr_eleve='$adr_eleve',
		ccp_eleve='$ccp_eleve',
		commune_eleve='$commune_eleve',
		tel_fixe_eleve='$tel_fixe_eleve',
		pays_eleve='$pays_eleve',
		nom='$nom',
		prenom='$prenom',
		classe='$classe',
		lv1='$lv1',
		lv2='$lv2',
		regime='$regime',
		date_naissance='$date_naissance',
		lieu_naissance='$lieu_naissance',
		nationalite='$nationalite',
		passwd='$passwd',
		passwd_eleve='$passwd_eleve',
		civ_1='$civ_1',
		nomtuteur='$nomtuteur',
		prenomtuteur='$prenomtuteur',
		adr1='$adr1',
		code_post_adr1='$code_post_adr1',
		commune_adr1='$commune_adr1',
		tel_port_1='$tel_port_1',
		civ_2='$civ_2',
		nom_resp_2='$nom_resp_2',
		prenom_resp_2='$prenom_resp_2',
		adr2='$adr2',
		code_post_adr2='$code_post_adr2',
		commune_adr2='$commune_adr2',
		tel_port_2='$tel_port_2',
		telephone='$telephone',
		profession_pere='$profession_pere',
		tel_prof_pere='$tel_prof_pere',
		profession_mere='$profession_mere',
		tel_prof_mere='$tel_prof_mere',
		nom_etablissement='$nom_etablissement',
		numero_etablissement='$numero_etablissement',
		code_postal_etablissement='$code_postal_etablissement',
		commune_etablissement='$commune_etablissement',
		numero_eleve='$numero_eleve',
		email='$email',
		email_eleve='$email_eleve',
		email_resp_2='$email_resp_2',
		class_ant='$class_ant',
		annee_ant='$annee_ant',
		tel_eleve='$tel_eleve',
		sexe='$sexe',
		option2='$option2',
		boursier='$boursier'
		WHERE elev_id='$ideleve'";
		execSql($sql);
		alertJs("Candidature modifiée");
	
}

if (isset($_POST["accepte"])) { 
	$ideleve=$_POST["id_eleve"];
	modifPreinscription($ideleve,"Accepté");	
}



if (isset($_POST["refus"])) {
	$ideleve=$_POST["id_eleve"];
	modifPreinscription($ideleve,"Refusé");
}

if (isset($_POST["attente"])) {
	$ideleve=$_POST["id_eleve"];
	modifPreinscription($ideleve,"En attente");
}


$data=infoPreinscription($ideleve);
$elev_id=$data[0][0];
$nom=$data[0][1];
$prenom=$data[0][2];
$classe=$data[0][3];
$lv1=$data[0][4];
$lv2=$data[0][5];
$option2=$data[0][6];
$regime=$data[0][7];
$date_naissance=dateForm($data[0][8]);
$lieu_naissance=$data[0][9];
$nationalite=$data[0][10];
$passwd=$data[0][11];
$passwd_eleve=$data[0][12];
$civ_1=$data[0][13];
$nomtuteur=$data[0][14];
$prenomtuteur=$data[0][15];
$adr1=$data[0][16];
$code_post_adr1=$data[0][17];
$commune_adr1=$data[0][18];
$tel_port_1=$data[0][19];
$civ_2=$data[0][20];
$nom_resp_2=$data[0][21];
$prenom_resp_2=$data[0][22];
$adr2=$data[0][23];
$code_post_adr2=$data[0][24];
$commune_adr2=$data[0][25];
$tel_port_2=$data[0][26];
$telephone=$data[0][27];
$profession_pere=$data[0][28];
$tel_prof_pere=$data[0][29];
$profession_mere=$data[0][30];
$tel_prof_mere=$data[0][31];
$nom_etablissement=$data[0][32];
$numero_etablissement=$data[0][33];
$code_postal_etablissement=$data[0][34];
$commune_etablissement=$data[0][35];
$numero_eleve=$data[0][36];
$photo=$data[0][37];
$email=$data[0][38];
$email_eleve=$data[0][39];
$email_resp_2=$data[0][40];
$class_ant=$data[0][41];
$annee_ant=$data[0][42];
$numero_gep=$data[0][43];
$valid_forward_mail_eleve=$data[0][44];
$valid_forward_mail_parent=$data[0][45];
$tel_eleve=$data[0][46];
$code_compta=$data[0][47];
$sexe=$data[0][48];
$decision=$data[0][49];
$date_demande=$data[0][50];
$date_decision=dateForm($data[0][51]);
$annee_scolaire=$data[0][52];
$information=$data[0][53];
$adr_eleve=$data[0][54];
$ccp_eleve=$data[0][55];
$commune_eleve=$data[0][56];
$tel_fixe_eleve=$data[0][57];
$pays_eleve=$data[0][58];
$boursier=$data[0][59];

if ($decision == "Accepté") {
	$message="<font class='T2' ><b>&nbsp;&nbsp;L'élève a été accepté le $date_decision</b><br /></font>";
}

if ($decision == "Refusé") {
	$message="<font class='T2' ><b>&nbsp;&nbsp;L'élève a été refusé le $date_decision</b><br /></font>";
}


print $message;
?>



<form method=post action="preinscriptiondetail.php"  name="formulaire"  >

<input type=hidden name="id_eleve" value="<?php print "$elev_id" ?>" />

<ul><font class=T2 color='#CC0000'><b>Renseignements sur l'élève</b></font></ul>

  <TABLE border="0"  width=100% align="center">
     <tr><td width="50%" ><div align="right"><font class="T2">Nom : </font></div></td>
        <td><input type="text" name="saisie_nom" maxlength=30 value="<?php print "$nom" ?>" ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Prénom :  </font></div></td>
        <td><input type="text" name="saisie_prenom"  maxlength=50 value="<?php print "$prenom" ?>" ></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Classe demandée : </font></div></td>
        <td><select name="saisie_classe" >
	<option  id='select0' value='<?php print $classe ?>' ><?php print chercheClasse_nom($classe) ?></option>
<?php
select_classe2(20); // creation des options
?>
            </select>
        </td>
    </tr>

<tr><td><div align="right"><font class="T2">Année scolaire :  </font></div></td>
        <td><input type="text" name="saisie_annee_scolaire" size=12 maxlength=11 value="<?php print $annee_scolaire ?> - <?php print $annee_scolaire+1 ?>" disabled='disabled' ></td>
    </tr>


    <tr><td><div align="right"><font class="T2">LV1 : </font></div></td>
        <td><select name="saisie_lv1" size="1">
	<option  id='select0' value="<?php print $lv1 ?>" ><?php print ucwords($lv1) ?></option>
<?php
select_matiere2(); // creation des options
?>
            </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2">LV2 : </font></div></td>
        <td><select name="saisie_lv2" size="1">
	<option id='select0' value="<?php print $lv2 ?>" ><?php print ucwords($lv2) ?></option>
<?php
select_matiere2(); // creation des options
?>
	    </select>
	</td>
    </tr>
    <tr><td><div align="right"><font class="T2">Option : </font></div></td>
    <td>
<select name="saisie_option2" size="1">
	<option  id='select0' value="<?php print $option2 ?>" ><?php print ucwords($option2) ?></option>
<?php
select_matiere2(); // creation des options
?>
	    </select>
    </td>
    </tr>
    <tr><td><div align="right"><font class="T2">R&eacute;gime : </font></div></td>
<?php
if ($regime== "Interne") {
        $checkedInterne="checked='checked'";
}elseif($regime == "Demi-pension") {
	$checkedDemiPens="checked='checked'";
}elseif($regime == "Externe"){
        $checkedExterne="checked='checked'";
}else{
        // rien
}

?>

    <td><input type="radio" name="saisie_regime" value="Interne" id='.btradio1' <?php print $checkedInterne ?> > Interne <br>
    <input type="radio" name="saisie_regime" value="Demi-pension" id='.btradio1' <?php print $checkedDemiPens ?> > Demi-pensionnaire <br>
   <input type="radio" name="saisie_regime" value="Externe" id='.btradio1' <?php print $checkedExterne ?> > Externe</td>
      </tr>


    <tr><td><div align="right"><font class="T2">Boursier : </font></div></td>
<?php
$checkedboursierNon=$checkedboursierOui="";
if ($boursier == "0") {
	$checkedboursierNon="checked='checked'"; 
}else{
	$checkedboursierOui="checked='checked'";
}
?>
   <td><input type="radio" name="saisie_boursier" value="1" id='.btradio1' <?php print $checkedboursierOui ?> /> oui 
    <input type="radio" name="saisie_boursier" value="0" id='.btradio1' <?php print $checkedboursierNon ?> /> non <br>
      </tr>



    <tr><td><div align="right"><font class="T2">Date de naissance : </font></div></td>
        <td>
	<input type="text" name="saisie_date_naissance"  value="<?php print "$date_naissance" ?>" />
	<?php
	include_once("librairie_php/calendar.php");
	calendarpopupDim('id1','document.formulaire.saisie_date_naissance',$_COOKIE["langue-triade"],"1","0");
	?>
 </td>
    </tr>
<?php
if ($sexe == "f") {
	$checkedF="checked='checked'"; 
}else{
	$checkedM="checked='checked'";
}
?>
    <tr><td><div align="right"><font class="T2">Sexe : </font></div></td>
    <td> M <input type="radio" name="saisie_sexe"  value="m" <?php print $checkedM ?> > -  F <input type="radio" name="saisie_sexe"  value="f" <?php print $checkedF ?> >
    </td></tr>
    <tr><td><div align="right"><font class="T2">Nationalité : </font></div></td>
        <td><input type="text" name="saisie_nationalite"  maxlength=20 value="<?php print "$nationalite" ?>" >
    </td></tr>
    <tr><td><div align="right"><font class="T2">Lieu de naissance : </font></div></td>
        <td><input type="text" name="saisie_lieu_naissance"  maxlength='40' value="<?php print "$lieu_naissance" ?>" >
    </td></tr>
    
    <tr><td><div align="right"><font class="T2">Mot de passe élève :</font></div></td>
	         <td><input type="passwd" name="saisie_passwd_eleve"  maxlength=50 value="<?php print "$passwd_eleve" ?>" > </td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro étudiant :</font></div></td>
         <td><input type="text" name="saisie_numero_eleve" maxlength=30 value="<?php print "$numero_eleve" ?>" ></td>
    </tr>

    <tr><td><div align="right"><font class="T2"><?php print "Adresse" ?> : </font></div></td>
                <td><input type="text" name="saisie_adr_eleve" size="30"  maxlength=100 value="<?php print "$adr_eleve" ?>"></td>
     </tr>
         <tr><td><div align="right"><font class="T2"><?php print LANGELE15?>  : </font></div></td>
     <td><input type="text" name="saisie_code_post_adr_eleve" size="30"  maxlength=6  value="<?php print "$ccp_eleve" ?>"></td>
        </tr>
    <tr><td><div align="right"><font class="T2"><?php print LANGELE16?>  : </font></div></td>
        <td><input type="text" name="saisie_commune_adr_eleve" size="30"  maxlength=40  value="<?php print "$commune_eleve" ?>"></td>
   </tr>
      <tr><td><div align="right"><font class="T2"><?php print "Pays"?>  : </font></div></td>
         <td><input type="text" name="saisie_pays_eleve" size="30"  maxlength=50  value="<?php print "$pays_eleve" ?>"></td>
     </tr>
       <tr><td><div align="right"><font class="T2"><?php print "Téléphone " ?>  : </font></div></td>
   <td><input type="text" name="saisie_tel_fixe_eleve" size="30"  maxlength=25 value="<?php print "$tel_fixe_eleve" ?>"></td>
      </tr>

     <tr><td><div align="right"><font class="T2"><?php print "Tél. Portable" ?> <?php print LANGTITRE40?> :  </font></div></td>
   <td><input type="text" name="saisie_tel_eleve"  maxlength=25 size="30" value="<?php print "$tel_eleve" ?>"  ></td>
      </tr>
      <tr><td><div align="right"><font class="T2"><?php print LANGELE244?> <?php print LANGTITRE40?> :  </font></div></td>
      <td><input type="text" name="saisie_email_eleve"  maxlength=48 size=30 value="<?php print "$email_eleve" ?>" ></td>
    </tr>
    <tr><td><div align="right"><font class="T2"><?php print "Information"?> <?php print LANGTITRE40?> :  </font></div></td>
     <td><textarea name="saisie_info_eleve"  cols=40 rows=3><?php print $information ?></textarea></td>
        </tr>
    
    </tr>
  </table>


<BR>
<ul><font class=T2 color='#CC0000'><b>Renseignements sur la famille</b></font></ul>
 <TABLE border="0"  width=100% align=center>
<tr><td align=right ><font class="T2">Civ 1 : </font></td><td>
<select name="saisie_civ_1" >
<option value='<?php print $civ_1 ?>' id='select0' ><?php print civ($civ_1) ?></option>
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
       <td><input type="text" name="saisie_nomtuteur" size="30"  maxlength=30  value="<?php print "$nomtuteur" ?>"></td>
   </tr>
    <tr><td><div align="right"><font class="T2">Prénom resp. 1 :  </font></div></td>
        <td><input type="text" name="saisie_prenomtuteur" size="30"  maxlength=30  value="<?php print "$prenomtuteur" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Adresse 1 : </font></div></td>
        <td><input type="text" name="saisie_adr1" size="30"  maxlength=100 value="<?php print "$adr1" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Code postal 1 : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr1" size="30"  maxlength=6  value="<?php print "$code_post_adr1" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Commune 1 : </font></div></td>
      <td><input type="text" name="saisie_commune_adr1" size="30"  maxlength=40  value="<?php print "$commune_adr1" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Tél. portable  1 : </font></div></td>
      <td><input type="text" name="saisie_tel_port_1" size="30"  maxlength=25  value="<?php print "$tel_port_1" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Email tuteur 1 :  </font></div></td>
      <td><input type="text" name="saisie_email" size="30"  maxlength=150  value="<?php print "$email" ?>"></td>
    </tr>

<tr><td align=right ><font class="T2">Civ 2 : </td><td>
<select name="saisie_civ_2" >
<option value='<?php print $civ_2 ?>' id='select0' ><?php print civ($civ_2) ?></option>
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
       <td><input type="text" name="saisie_nom_resp_2" size="30"  maxlength=30 value="<?php print "$nom_resp_2" ?>" ></td>
   </tr>
    <tr><td><div align="right"><font class="T2">Prénom resp. 2 :  </font></div></td>
        <td><input type="text" name="saisie_prenom_resp_2" size="30"  maxlength=30 value="<?php print "$prenom_resp_2" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Adresse 2 : </font></div></td>
        <td><input type="text" name="saisie_adr2" size="30"  maxlength=100 value="<?php print "$adr2" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Code postal 2 : </font></div></td>
      <td><input type="text" name="saisie_code_post_adr2" size="30"  maxlength=6 value="<?php print "$code_post_adr2" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Commune 2 : </font></div></td>
      <td><input type="text" name="saisie_commune_adr2" size="30"  maxlength=40 value="<?php print "$commune_adr2" ?>"></td>
</tr>
<tr><td><div align="right"><font class="T2">Tél. portable  2 : </font></div></td>
      <td><input type="text" name="saisie_tel_port_2" size="30"  maxlength=25 value="<?php print "$tel_port_2" ?>"></td>
    </tr>
<tr><td><div align="right"><font class="T2">Email tuteur 2 :  </font></div></td>
      <td><input type="text" name="saisie_email_resp_2" size="30"  maxlength=150 value="<?php print "$email_resp_2" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro de téléphone : </font></div></td>
      <td><input type="text" name="saisie_telephone" size="30"  maxlength=18 value="<?php print "$telephone" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Profession du père : </font></div></td>
      <td><input type="text" name="saisie_profession_pere" size="30"  maxlength=20 value="<?php print "$profession_pere" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Téléphone du père :  </font></div></td>
      <td><input type="text" name="saisie_tel_prof_pere" size="30"  maxlength=18 value="<?php print "$tel_prof_pere" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Profession de la mère : </font></div></td>
      <td><input type="text" name="saisie_profession_mere" size="30"  maxlength=20 value="<?php print "$profession_mere" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Téléphone de la mère :  </font></div></td>
      <td><input type="text" name="saisie_tel_prof_mere" size="30"  maxlength=18 value="<?php print "$tel_prof_mere" ?>"></td>
    </tr>
   <tr><td><div align="right"><font class="T2">Mot de passe parent  :</font></div></td>
         <td><input type="passwd" name="saisie_passwd"  size="30" maxlength=50 value="<?php print "$passwd" ?>" ></td>
    </tr> 
  </table>

<BR>
<ul><font class=T2 color='#CC0000'><b>Ecole antérieure</b></font></ul>
  <TABLE border="0" width=100% align="center">
    <tr><td width="50%"><div align="right"><font class="T2">Nom de l'établissement :</font></div></td>
          <td><input type="text" name="saisie_nom_etablissement" size="35"  maxlength=30 value="<?php print "$nom_etablissement" ?>"></td>
    </tr>
    <tr><td><div align="right"><font class="T2">Numéro établissement :</font></div></td>
      <td><input type="text" name="saisie_numero_etablissement" size="35"  maxlength=30 value="<?php print "$numero_etablissement" ?>"></td>
      <tr><td><div align="right"><font class="T2">Classe antérieure :</font></div></td>
      <td><input type="text" name="saisie_classe_ant" size="35"  maxlength=30 value="<?php print "$class_ant" ?>"></td>
</tr>
    <tr><td><div align="right"><font class="T2">Année antérieure : </font></div></td>
        <td><input type="text"  name="saisie_date_ant" size="35" value="<?php print "$annee_ant" ?>" >
 </td>
    </tr>
<tr><td><div align="right"><font class="T2">Code postal : </font></div></td>
<td><input type="text" name="saisie_code_postal_etablissement" size="35"  maxlength=6 value="<?php print "$code_postal_etablissement" ?>" ></td>
</tr>
<tr><td><div align="right"><font class="T2">Commune : </font></div></td>
<td><input type="text" name="saisie_commune_etablissement" size="35"  maxlength=30 value="<?php print "$commune_etablissement" ?>" ></td>
</tr>
</table>

<br>
<table  border="0" align="center" width='100%' >
<tr><td height="53">
<div align="center">

<script language=JavaScript>buttonMagicSubmit('Modifier donnée','modif'); //text,nomInput</script>
<?php if ($decision == "Accepté") { ?>
<script language=JavaScript>buttonMagicSubmit('Candidat en attente','attente'); //text,nomInput</script>
<?php }else{ ?>
<script language=JavaScript>buttonMagicSubmit('Accepter candidat','accepte'); //text,nomInput</script>
<?php } ?>

<?php if ($decision == "Refusé") { ?>
<script language=JavaScript>buttonMagicSubmit('Candidat en attente','attente'); //text,nomInput</script>
<?php }else{ ?>
<script language=JavaScript>buttonMagicSubmit('Refuser candidat','refus'); //text,nomInput</script>
<?php } ?>
</form>
<br><br><br>
<form method=post action="listepreinscription.php"  name="formulaire"  >
<input type=hidden name="id_eleve" value="<?php print "$elev_id" ?>" />
<script language=JavaScript>buttonMagicSubmit('Supprimer cette fiche de pré-inscription','suppfiche'); //text,nomInput</script>
</form>
<br><br>
</div></td></tr>
</table>


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
