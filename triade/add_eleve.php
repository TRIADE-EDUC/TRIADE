<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");

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
$annee_scolaire=trim($_POST['annee_scolaire']);
$boursier=$_POST["saisie_boursier"];
$datedemande=dateDMY2();

$adresse_eleve=$_POST['saisie_adr_eleve'];
$codecompta=$_POST["saisie_codecompta"];
$code_post_adr_eleve=$_POST["saisie_code_post_adr_eleve"];
$commune_adr_eleve=$_POST["saisie_commune_adr_eleve"];
$pays_eleve=$_POST["saisie_pays_eleve"];
$tel_fixe_eleve=$_POST["saisie_tel_fixe_eleve"];

if (($nom == "") ||  ($prenom == "" ) ||  ($email_eleve == "" ) || ($passwd_eleve == "" ) || ($date_naissance == "") ) { 
	header("Location:preinscription_eleve.php?error");
}else{ 
	$cnx=cnx();
	// on ecris la requete sql 
	$sql = "SELECT * FROM ${prefixe}preinscription_eleves WHERE nom='$nom' AND prenom='$prenom' AND email_eleve='$email_eleve' ";
	$data=ChargeMat(execSql($sql));
	if (count($data) > 0) {
		$text="Candidature d&eacute;j&agrave; enregistr&eacute;e";
	}else{
		$sql = "INSERT INTO ${prefixe}preinscription_eleves (nom,prenom,classe,lv1,lv2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,tel_eleve,sexe,option2,date_demande,adr_eleve,ccp_eleve,commune_eleve,tel_fixe_eleve,pays_eleve,annee_scolaire,boursier) VALUES ('$nom','$prenom','$classe','$lv1','$lv2','$regime','$date_naissance','$lieu_naissance','$nationalite','$passwd','$passwd_eleve','$civ_1','$nomtuteur','$prenomtuteur','$adr1','$code_post_adr1','$commune_adr1','$tel_port_1','$civ_2','$nom_resp_2','$prenom_resp_2','$adr2','$code_post_adr2','$commune_adr2','$tel_port_2','$telephone','$profession_pere','$tel_prof_pere','$profession_mere','$tel_prof_mere','$nom_etablissement','$numero_etablissement','$code_postal_etablissement','$commune_etablissement','$numero_eleve','$photo','$email','$email_eleve','$email_resp_2','$class_ant','$annee_ant','$tel_eleve','$sexe','$option2','$datedemande','$adresse_eleve','$code_post_adr_eleve','$commune_adr_eleve','$tel_fixe_eleve','$pays_eleve','$annee_scolaire','$boursier');";
		$cr=execSql($sql);
		if ($cr) {
			$tab=affPersActif("ADM"); // pers_id, civ, nom, prenom, identifiant, offline, email 
			for($k=0;$k<count($tab);$k++){
				$destinataire=$tab[$k][0];
				$objet="Nouvel inscription";
				$message="Une nouvelle inscription vient d'être effectu&eacute;e.";
				$number="";
				$date=dateDMY2();
				$heure=dateHIS();
				$type_personne="ADM";
				$type_personne_dest="ADM";
				envoi_messagerie("-1",$destinataire,$objet,Crypte($message,$number),$date,$heure,$type_personne,$type_personne_dest,$number,'',0);	
			}
		}
		$text="Candidature enregistr&eacute;e";
	}	
	Pgclose();  // on ferme la connexion              
?>
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
	if (POPUP == "non") {
		print "<script type='text/javascript'>var popup='non';</script>\n";
	}else {
		print "<script type='text/javascript'>var popup='oui';</script>\n";
	}
	if (HTTPS == "non") {
		print "<script type='text/javascript'>var http='http://';</script>\n";
	}else{
		print "<script type='text/javascript'>var http='https://';</script>\n";
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
<tr id='coulBar0' ><td height="2" colspan=2 ><b><font   id='menumodule1' >Candidature Elève</B></font></td></tr>
<td id='cadreCentral0'  colspan=2>
<br>
<center><font class=T2><?php print $text ?></font></center>
<br>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>


<?php
}	
?> 
