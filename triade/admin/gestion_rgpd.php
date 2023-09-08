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
error_reporting(0);
include_once("./librairie_php/lib_licence.php"); 
include_once("../librairie_php/timezone.php"); 
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>

<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion RGPD</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<br>
<b>Vue d'ensemble RGPD</b><br><br>
Le règlement général sur la protection des données (RGPD) est un règlement qui renforce et unifie la protection des données pour tous les individus au sein de l'Union européenne (UE).<br><br>

Si vous traitez des donn&eacute;es personnelles de citoyens europ&eacute;ens par le biais Triade m&ecirc;me si votre &eacute;tablissement est située en dehors de l'Europe, vous devez remplir les obligations du RGPD et ce guide vous aidera.<br><br>

Exercez les droits de vos utilisateurs grâce &agrave; nos procédures adaptées RGPD<br><br>

<b>Merci de renseigner ces informations</b> : <br><br>


<?php
if (isset($_POST['creatergpd'])) {

// 2023-06-23:test-triade.dev-triade-educ.org:15:9:b87d6eea7c474b43a981c7622db54c24
$fichier="../data/install_log/install.inc";
if (file_exists($fichier))  {
        $fichier=fopen($fichier,"r");
        $donnee=fread($fichier,10000);
	fclose($fichier);
	$tab=explode(" ",$donnee);
	$date_installe=stripslashes($tab[2]);
}
$date_update_info=dateDMY();

$texte="<?php\n";
$texte.="define(\"RGPD_NOM_ETAB\",\"".$_POST['nom_etablissement']."\");\n";
$texte.="define(\"RGPD_NOM_ACAD\",\"".$_POST['nom_academie']."\");\n";
$texte.="define(\"RGPD_NOM_RESP\",\"".$_POST['nom_resp']."\");\n";
$texte.="define(\"RGPD_PRENOM_RESP\",\"".$_POST['prenom_resp']."\");\n";
$texte.="define(\"RGPD_ADRESSE_RESP\",\"".$_POST['adresse_resp']."\");\n";
$texte.="define(\"RGPD_CCP_RESP\",\"".$_POST['ccp_resp']."\");\n";
$texte.="define(\"RGPD_VILLE_RESP\",\"".$_POST['ville_resp']."\");\n";
$texte.="define(\"RGPD_TEL_RESP\",\"".$_POST['tel_resp']."\");\n";
$texte.="define(\"RGPD_EMAIL_RESP\",\"".$_POST['email_resp']."\");\n";
$texte.="define(\"RGPD_NOM_DPO\",\"".$_POST['nom_dpo']."\");\n";
$texte.="define(\"RGPD_PRENOM_DPO\",\"".$_POST['prenom_dpo']."\");\n";
$texte.="define(\"RGPD_SOCIETE_DPO\",\"".$_POST['societe_dpo']."\");\n";
$texte.="define(\"RGPD_ADRESSE_DPO\",\"".$_POST['adresse_dpo']."\");\n";
$texte.="define(\"RGPD_CCP_DPO\",\"".$_POST['ccp_dpo']."\");\n";
$texte.="define(\"RGPD_VILLE_DPO\",\"".$_POST['ville_dpo']."\");\n";
$texte.="define(\"RGPD_TEL_DPO\",\"".$_POST['tel_dpo']."\");\n";
$texte.="define(\"RGPD_EMAIL_DPO\",\"".$_POST['email_dpo']."\");\n";
$texte.="define(\"RGPD_DATE_INSTALLE\",\"".$date_installe."\");\n";
$texte.="define(\"RGPD_UPDATE\",\"".$date_update_info."\");\n";
$texte.="define(\"RGPD_TRANS\",\"".$_POST['transf_hors_europe']."\");\n";
$texte.="define(\"RGPD_SAVE\",\"".$_POST['sauvegarde_manuel']."\");\n";
$texte.="define(\"RGPD_HTTPS\",\"".$_POST['https_utiliser']."\");\n";
$texte.="define(\"RGPD_PROXY\",\"".$_POST['proxy_utiliser']."\");\n";
$texte.="?>\n";
$fp=fopen("../common/config-rgpd.php","w");
fwrite($fp,"$texte");
fclose($fp);

include_once("../common/config-rgpd.php");

$nom_etab=RGPD_NOM_ETAB;
$nom_acad=RGPD_NOM_ACAD;
$nom_resp=RGPD_NOM_RESP;
$prenom_resp=RGPD_PRENOM_RESP;
$adresse_resp=RGPD_ADRESSE_RESP;
$ccp_resp=RGPD_CCP_RESP;
$ville_resp=RGPD_VILLE_RESP;
$tel_resp=RGPD_TEL_RESP;
$email_resp=RGPD_EMAIL_RESP;
$nom_dpo=RGPD_NOM_DPO;
$prenom_dpo=RGPD_PRENOM_DPO;
$societe_dpo=RGPD_SOCIETE_DPO;
$adresse_dpo=RGPD_ADRESSE_DPO;
$ccp_dpo=RGPD_CCP_DPO;
$ville_dpo=RGPD_VILLE_DPO;
$tel_dpo=RGPD_TEL_DPO;
$email_dpo=RGPD_EMAIL_DPO;
$date_installe=RGPD_DATE_INSTALLE;
$date_update=RGPD_UPDATE;
$transfert=RGPD_TRANS;
$sauvegarde=RGPD_SAVE;
$https=RGPD_HTTPS;
$proxy=RGPD_PROXY;
$date_installe=RGPD_DATE_INSTALLE;
$date_update=RGPD_UPDATE;
$transfert=RGPD_TRANS;
$sauvegarde=RGPD_SAVE;
$https=RGPD_HTTPS;
$proxy=RGPD_PROXY;
}


if (file_exists("../common/config-rgpd.php")) {
        include_once("../common/config-rgpd.php");        
	$nom_etab=RGPD_NOM_ETAB;        
	$nom_acad=RGPD_NOM_ACAD;
        $nom_resp=RGPD_NOM_RESP;
        $prenom_resp=RGPD_PRENOM_RESP;
        $adresse_resp=RGPD_ADRESSE_RESP;
        $ccp_resp=RGPD_CCP_RESP;
        $ville_resp=RGPD_VILLE_RESP;
        $tel_resp=RGPD_TEL_RESP;
        $email_resp=RGPD_EMAIL_RESP;
        $nom_dpo=RGPD_NOM_DPO;
        $prenom_dpo=RGPD_PRENOM_DPO;
        $societe_dpo=RGPD_SOCIETE_DPO;
        $adresse_dpo=RGPD_ADRESSE_DPO;
        $ccp_dpo=RGPD_CCP_DPO;
        $ville_dpo=RGPD_VILLE_DPO;
        $tel_dpo=RGPD_TEL_DPO;
        $email_dpo=RGPD_EMAIL_DPO;
	$date_installe=RGPD_DATE_INSTALLE;
        $date_update=RGPD_UPDATE;
        $transfert=RGPD_TRANS;
        $sauvegarde=RGPD_SAVE;
        $https=RGPD_HTTPS;
        $proxy=RGPD_PROXY;
}

?>

<form method='post'  >
<table border='0' style='border-collapse: collapse;' align='center'  >
<tr><td align=right width='50%' >Nom de l'&eacute;tablissement : </td><td><input type='text' name='nom_etablissement' size='20' value="<?php print $nom_etab ?>"  /></td></tr>
<tr><td align=right >Nom de l'acad&eacute;mie : </td><td><input type='text' name='nom_academie' size='20' value="<?php print $nom_acad ?>" /></td></tr>

<tr><td colspan='2' align='center' height=10></td></tr>
<tr><td colspan='2' align='center'><u>Coordonn&eacute;es du responsable de l'organise</u></td></tr>
<tr><td colspan='2' align='center' height=10></td></tr>
 
<tr><td align=right >Nom : </td><td><input type='text' name='nom_resp' size='20' value="<?php print $nom_resp ?>" /></td></tr>
<tr><td align=right >Pr&eacute;nom : </td><td><input type='text' name='prenom_resp' size='20' value="<?php print $prenom_resp ?>" /> </td></tr>
<tr><td align=right >Adresse : </td><td><input type='text' name='adresse_resp' size='20' value="<?php print $adresse_resp ?>" /> </td></tr>
<tr><td align=right >code postal : </td><td><input type='text' name='ccp_resp' size='10' value="<?php print $ccp_resp   ?>" /> </td></tr>
<tr><td align=right >ville :</td><td><input type='text' name='ville_resp' size='20' value="<?php print $ville_resp   ?>" /> </td></tr>
<tr><td align=right >tel : </td><td><input type='text' name='tel_resp' size='20' value="<?php print $tel_resp   ?>" /> </td></tr>
<tr><td align=right >email : </td><td><input type='text' name='email_resp' size='20' value="<?php print $email_resp  ?>" /> </td></tr>

<tr><td colspan='2' align='center' height=10></td></tr>
<tr><td align='center' colspan='2' ><u>Nom et coordonn&eacute;es du d&eacute;l&eacute;gu&eacute; &agrave; la protection des donn&eacute;es</u></td></tr>
<tr><td colspan='2' align='center' height=10></td></tr>

<tr><td align=right >Nom du DPO : </td><td><input type='text' name='nom_dpo' size='20' value="<?php print $nom_dpo   ?>" /></td></tr>
<tr><td align=right >Pr&eacute;nom du DPO  : </td><td><input type='text' name='prenom_dpo' size='20' value="<?php print $prenom_dpo   ?>" /></td></tr>
<tr><td align=right >Soci&eacute;t&eacute; (si DPO externe) : </td><td> <input type='text' name='societe_dpo' size='20' value="<?php print $societe_dpo   ?>" /> </td></tr>
<tr><td align=right >Adresse : </td><td><input type='text' name='adresse_dpo' size='20' value="<?php print $adresse_dpo   ?>" /></td></tr>
<tr><td align=right >CP : </td><td><input type='text' name='ccp_dpo' size='20' value="<?php  print $ccp_dpo  ?>" /></td>
<tr><td align=right >Ville : </td><td><input type='text' name='ville_dpo' size='20' value="<?php print $ville_dpo   ?>" /><br></td></tr>
<tr><td align=right >T&eacute;l&eacute;phone :</td><td><input type='text' name='tel_dpo' size='20' value="<?php print $tel_dpo   ?>" /></td></tr>
<tr><td align=right >Email :</td><td><input type='text' name='email_dpo' size='20' value="<?php print $email_dpo   ?>" /></td></tr>


<tr><td colspan='2' align='center' height=10></td></tr>
<tr><td align=right >Date d'installation du logiciel TRIADE : </td><td><input type='text' name='date_installe' size='10' value="<?php print $date_installe   ?>" readonly="readonly" /> </td> </tr>
<tr><td align=right >Date de derniere mise &agrave; jour de la fiche : </td><td><input type='text' name='date_update_info' size='10' value="<?php print $date_update   ?>" readonly="readonly" /> </tr>
<tr><td align=right >Transferts des donn&eacute;es hors UE : </td><td><input type='checkbox' name='transf_hors_europe' size='10' value='oui'  <?php if ($transfert == "oui") print "checked='checked'" ?>  /> OUI </td> </tr>

<tr><td align=right >Backups r&eacute;alis&eacute;s manuellement : </td><td><input type='checkbox' name='sauvegarde_manuel' size='10' value='oui' <?php if ($sauvegarde == "oui") print "checked='checked'" ?> /> OUI </td></tr>

<tr><td align=right >Utilisation de l'HTTPS : </td><td><input type='checkbox' name='https_utiliser' value='oui'  <?php if ($https == "oui") print "checked='checked'" ?>  /> OUI </td></tr>

<tr><td align=right >Utilisation de proxy : </td><td><input type='checkbox' name='proxy_utiliser'  value='oui' <?php if ($proxy == "oui") print "checked='checked'" ?> /> OUI</td></tr>
<tr><td colspan='2' align='center' height=10></td></tr>
<tr><td colspan="2" align='center' ><input type='submit' value='Enregistrer' class='button'  name='creatergpd' /></td></tr>
<tr><td colspan='2' align='center' height=10></td></tr>
</table>
</form>

<?php
if (file_exists("../common/config-rgpd.php")) {

	$fic="../data/parametrage/registre_RGPD_triade.rtf";
        @unlink("$fic");

	$TempFilename="./lib/registre_rgpd_triade.rtf";
        $fichier=fopen($TempFilename,"r");
        $longueur=90000000;
        $data=fread($fichier,$longueur);
        fclose($fichier);


/*
        $nom_etab
        $nom_acad
        $nom_resp
	$prenom_resp
	$adresse_resp
        $ccp_resp
	$ville_resp
	$tel_resp
	$email_resp
	$nom_dpo
        $prenom_dpo
        $societe_dpo
        $adresse_dpo
        $ccp_dpo
        $ville_dpo
        $tel_dpo
        $email_dpo
        $date_installe
        $date_update
        $transfert
        $sauvegarde
        $https
        $proxy
*/

	$data=preg_replace('#-NomEtablissement-#',$nom_etab,$data);
	$data=preg_replace('#-Nom-#',$nom_resp,$data);
	$data=preg_replace('#-nomacademie-#',$nom_acad,$data);
	$data=preg_replace('#-Prenom-#',$prenom_resp,$data);
	$data=preg_replace('#-Adresse-#',"$adresse_resp",$data);
	$data=preg_replace('#-CodePostal-#',$ccp_resp,$data);
	$data=preg_replace('#-Ville-#',$ville_resp,$data);
	$data=preg_replace('#-telephone-#',$tel_resp,$data);
	$data=preg_replace('#-email-#',$email_resp,$data);
	$data=preg_replace('#-NomDPO-#',$nom_dpo,$data);
	$data=preg_replace('#-PrenomDPO-#',$prenom_dpo,$data);
	$data=preg_replace('#-SocieteDPO-#',$societe_dpo,$data);
	$data=preg_replace('#-AdresseDPO-#',$adresse_dpo,$data);
	$data=preg_replace('#-CodePostalDPO-#',$ccp_dpo,$data);
	$data=preg_replace('#-VilleDPO-#',$ville_dpo,$data);
	$data=preg_replace('#-TelephoneDPO-#',$tel_dpo,$data);
	$data=preg_replace('#-emailDPO-#',$email_dpo,$data);
	$data=preg_replace('#-InstalleTriade-#',$date_installe,$data);
	$data=preg_replace('#-UpdateFiche-#',$date_update,$data);
	$data=preg_replace('#-backup-#',$sauvegarde,$data);
	$data=preg_replace('#-https-#',$https,$data);
	$data=preg_replace('#-proxy-#',$proxy,$data);
	$data=preg_replace('#-transhorsEU-#',$transfert,$data);

	$fichier=fopen("$fic","a");
        fwrite($fichier,$data);
        fclose($fichier);


	print "<br/><hr><br/>";
	print "<div align='center'><input type='button' value='Editer le registre RGPD' onClick=\"open('telecharger.php?fichier=/data2/parametrage/registre_RGPD_triade.rtf','_blank','')\" STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\"  /></div><br/>";

}
?>
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
