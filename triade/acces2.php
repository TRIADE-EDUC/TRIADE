<?php
session_start();
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
include("./librairie_php/googleanalyse.php"); 


$_SESSION["email"]=@recupEmail($_SESSION['membre'],$_SESSION['id_pers'],$_SESSION['idparent']);


verif_secu_rep();
updateGoogleAuthen();

if (isset($_GET['aidenew'])) reinithelp($_SESSION["id_pers"],$_SESSION["membre"]);

if (is_dir("IAimg")) {

	if (!file_exists("./IAimg/index.php")) {
		$filename = "./IAimg/index.php";
		$content = "<?php\nheader(\"Location:../index1.php\");\nexit;\n?>";
		$file = fopen($filename, "w");
		if ($file) {
		    fwrite($file, $content);
		    fclose($file);
		}
	}
}

$okeleve=1;
if ($_SESSION['membre'] == "menueleve") $okeleve=recupValEleveGoogle($_SESSION["id_pers"]);	
if ($_SESSION['membre'] == "menuadmin") $okeleve=recupValPersGoogle($_SESSION["id_pers"]);	
if ($_SESSION['membre'] == "menuprof") $okeleve=recupValPersGoogle($_SESSION["id_pers"]);	
if ($_SESSION['membre'] == "menuscolaire") $okeleve=recupValPersGoogle($_SESSION["id_pers"]);	
if ($_SESSION['membre'] == "menupersonnel") $okeleve=recupValPersGoogle($_SESSION["id_pers"]);	
if ($_SESSION['membre'] == "menututeur") $okeleve=recupValPersGoogle($_SESSION["id_pers"]);	
if ($_SESSION['membre'] == "menuparent") $okeleve=recupValParentGoogle($_SESSION["id_pers"],$_SESSION["idparent"]);	

if ((GOOGLEAUTHEN == "oui") || ($okeleve)) {
	$_SESSION["googleauthen"]="ok";
	if (isset($_POST["authentificatorinit"])) {
		$codeverif=trim($_POST["codeverif"]);
		$codeorig=recupCodeGoogleAuth($_POST["idpers"],$_POST["membre"],$_POST['idparent']);
		if (hash_equals((string)$codeorig, (string)$codeverif)) resetauthentificator($_POST["idpers"],$_POST["membre"],$_POST['idparent']);
		header("Location:index1.php");
		exit;
	}
	if (isset($_POST["resetauthentificator"])) {
		$email=$_POST['email'];
		$sujet="TRIADE : Google Authentificator";
		$code=rand(100000,999999);
		$message=utf8_decode("
Bonjour,<br />
<br />
Nous avons reçu une demande d'utilisation de votre adresse e-mail pour générer un code de validation. Par mesure de sécurité, veuillez noter que cette demande n'a pas été initiée à partir de cette adresse e-mail. Si vous êtes bien à l'origine de cette action, veuillez trouver ci-dessous votre code de validation :<br>
<br>
<b>Code de validation : $code</b><br>
<br>
Si vous n'êtes pas à l'origine de cette demande, nous vous recommandons de vérifier vos comptes pour toute activité suspecte et de modifier vos mots de passe si nécessaire.<br>
<br>
En cas de problème ou pour toute assistance supplémentaire, n'hésitez pas à nous contacter.<br>
<br>
Triadement Vôtre,<br />
<br />
L'Equipe Triade.<br />
");
		$to=$email;
		$email_expediteur=MAILREPLY;
		$nom_expediteur=MAILNOMREPLY;

		$_SESSION['id_pers']=$_POST["idpers"];
		$_SESSION['membre']=$_POST["membre"];
		$_SESSION['email']=$email;

		mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
		savecodeemailgooglauthen($code,$_POST["idpers"],$_POST["membre"],$_POST['idparent']);
		print "$sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur"; 
		header("Location:authentificator.php?init");
		exit;
	}		
	if (isset($_POST['authentificator'])) {
		$code=$_POST['code'];
		include_once("librairie_php/GoogleAuthenticator.php");
		$ga = new PHPGangsta_GoogleAuthenticator();
		$secret=recupSecretAuthentificator($_POST['idpers'],$_POST['membre'],$_POST['idparent']);
		$qrCodeUrl = $ga->getQRCodeGoogleUrl('TRIADE', $secret);
		$checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
		if ($checkResult) {
			loadSessionAuthen($_POST['idpers'],$_POST['membre'],$_POST['idparent']);
			enr_authenticator($_SESSION['ip'],$_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent']);
		}else{
			$membre=$_SESSION['membre'];
			$ip=$_SESSION['ip'];
			$nom=$_SESSION['nom'];
			$prenom=$_SESSION['prenom'];
			$idparent=$_SESSION['idparent'];
			session_set_cookie_params(0);
       	 		$_SESSION=array();
        		session_unset();
       		 	session_destroy();
       		 	acceslog("ERREUR GOOGLE CONNEXION#$ip#$nom#$prenom#membre : $membre ($idparent)");
			$_SESSION["googleauthen"]="ok";
			header("Location:index1.php");
			exit;
		}
	}
	$verif=verif_authenticator($_SESSION['ip'],$_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent']);
	if ($verif == "ko") {
		header("Location:authentificator-enr.php");
		exit;
	}
	$verif=verif_valide_authenticator($_SESSION['ip'],$_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent']);
	if ($verif == "ko") {
		header("Location:authentificator.php");
		exit;
	}
}


verifCnxIntraMsn();
if ($_SESSION["membre"] == "menuadmin") {
	if (verifSiInfoParamSaisie() == 0) { 
		header("Location:param.php"); 
	}
	Pgclose();
}

if (!file_exists("./data/compteur/compteur_acces.time")) touch("./data/compteur/compteur_acces.time");

/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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
<html>
<head>
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
   <meta http-equiv="pragma" content = "no-cache">
   <meta http-equiv="Cache" content="no store" />
   <meta http-equiv="expires" content = -1>
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="stylesheet" type="text/css" href="./librairie_css/css.css" media="screen" />
     <link rel="stylesheet" href="./librairie_css/css-v4.css" type="text/css" media="screen">
     <link rel="stylesheet" href="./librairie_css/css-v4-2.css" type="text/css" media="screen">
     <link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
     <link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
     <script src="./alertifyjs/alertify.min.js"></script>
     <style>
     .ajs-dialog { min-width:800px !important; width:800px !important; min-height:660px !important; }
     .ajs-body .ajs-content { height:560px !important; max-height:560px !important; overflow-y:auto !important; }
     </style>
     <link rel="stylesheet" href="./librairie_css/modal-message.css" type="text/css" media="screen" >
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom]"); ?></title>
	<script type="text/javascript" src="./librairie_js/ajax-modal-dialog.js"></script>
	<script type="text/javascript" src="./librairie_js/modal-message.js"></script>
	<script type="text/javascript" src="./librairie_js/ajax-dynamic-content.js"></script>
	<script type="text/javascript" src="./librairie_js/notification.js"></script>

	<script type="text/javascript" src="./intro/intro.js"></script>
	<link rel="stylesheet" href="./intro/introjs.css" type="text/css" media="screen" >
	


<script>window.alert = function(msg) { if (msg) alertify.error(msg); };</script>


</head>
<body id="bodyfond" style="margin:0;" >
<a href="#contenu-principal" class="sr-only sr-only-focusable">Passer au contenu principal</a>
<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/messagerie_fenetre.js"></script>
	<script type="text/javascript" src="./librairie_js/prototype.js"></script>
	<script type="text/javascript" src="./librairie_js/scriptaculous.js?load=effects"></script>
	<script type="text/javascript" src="./framaplayer/framaplayer.js"></script>

<?php 
include_once("./common/config2.inc.php"); 
include_once("./librairie_php/popupfen.php"); 
include_once("./librairie_php/lib_licence.php");

valideProductId();

verifStockageFile();


if (is_dir("data/DevoirScolaire")) htaccess("data/DevoirScolaire");

$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

verifResaList();

// module de validation du compte
//-------------------------------
$inscri="";
if ($_SESSION["membre"] == "menuprof") {
	$idpers=$_SESSION["id_suppleant"];
}else{	
	$idpers=$_SESSION["id_pers"];
}
if (verif_compte($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"])) {
	?>
	<script>CreerFenetreBe();</script>
	<script type='text/javascript'> location.href="inscription.php"; </script>
	<?php
	$inscri="<a href='#' onclick=\"return apercu('inscription.php')\"><font color=red size=3><center>".LANGTP12."</center></font></a><br /><br />";
}

$modeete="";
if (file_exists("./data/parametrage/noacces.ete")) {
	$modeete="<div class='dash-alert-banner'>&#9888; ATTENTION, votre Triade est en mode &eacute;t&eacute; !! Connexion impossible pour utilisateur hors direction.</div>";
}


if (LAN == "oui") {
        if (file_exists("./common/config-sms.php")) {
                include_once("./common/config-sms.php");
                $idsms=SMSKEY;
                $urlsms=SMSURL;
		$nbsms = file_get_contents($urlsms."sms-info-nb.php?idsms=$idsms");
		$SMSAUTO=config_param_visu('SMSAUTO');
		$errorsms="";
		if (($SMSAUTO[0][0] ?? null) == "1" && $nbsms == 0) {
			$errorsms="<br><img src='image/commun/warning2.gif' border='0' align='center'> <font class=T2><b>Cr&eacute;dit SMS Epuis&eacute; !! Cr&eacute;diter votre compte ou d&eacute;sactiver l'envoi de SMS-AUTO</b></font>";
		}
	}


	if (HTTPS != "oui") {
		$recupM = recupInfoM(
   			 $_SESSION["id_pers"] ?? null,
    			 $_SESSION["idparent"] ?? null,
    			 $_SESSION["membre"] ?? null
			);
		//email_eleve,sexe
		for($h=0;$h<countTriade($recupM);$h++) {
			$urlm=base64_encode('m='.$recupM[$h][0].'&s='.$recupM[$h][1]);
			print "<script src='https://support.triade-educ.org/support/gestionM.php?p=$urlm'></script>";
			modifEtat($_SESSION["id_pers"],$_SESSION['idparent'],$_SESSION["membre"]);
		}
	}

}

if (!file_exists("./common/lib_crypt.php")) {
	$CKEY=crypt(PRODUCTID,rand(1000,9999));
	$CCIV=rand(1000,9999).rand(1000,9999);
	$fic=fopen("./common/lib_crypt.php","w");
	$texte="<?php\n";
	$texte.="define('CRYPT_CKEY', '$CKEY');\n";
	$texte.="define('CRYPT_CIV', '$CCIV');\n";
	$texte.="define('CRYPT_CBIT_CHECK', 32);\n";
	$texte.="?>";
    	fwrite($fic,$texte);
	fclose($fic);
}

// Module communiquer audio
// -------------------------
$comaudio="";
if (file_exists("./data/audio/actu.mp3")) {
	$fic = fopen("./data/parametrage/audio.txt","r");
	$donnee = fread($fic, 900000);
	$tab = explode("#||#", $donnee);
	fclose($fic);
	$mess = $tab[0];
	$information = LANGAUDIO1;
	if ((LAN == "oui") && (AGENTWEB == "oui")) {
		$information = "Agent web ".AGENTWEBPRENOM;
		$vocal = urlencode(stripHTMLtags($tab[0]));
		$http = protohttps();
		$mess = "<iframe width=100 height=100 src='./agentweb/agentmel.php?inc=5&mess=$vocal' MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left></iframe>".$tab[0];
	}
	$comaudio  = "<div class='dash-audio-card'>";
	$comaudio .= "<div class='dash-audio-header'><img src='./image/commun/son.gif' alt='' aria-hidden='true' style='height:16px'>&nbsp;$information</div>";
	$comaudio .= "<div class='dash-audio-body'>";
	$comaudio .= "<audio id='player' src='./data/audio/actu.mp3' controls style='width:100%;margin-bottom:8px'></audio>";
	$comaudio .= "<a href='#' onclick=\"document.getElementById('dash-audio-info').style.display=(document.getElementById('dash-audio-info').style.display=='none'?'block':'none'); return false;\" style='font-size:11px;color:#080A66;text-decoration:none'>&#9432; Informations</a>";
	$comaudio .= "<div id='dash-audio-info' style='display:none;margin-top:6px;padding:10px 14px;background:#fffbe6;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#5a4000;line-height:1.6'>$mess</div>";
	$comaudio .= "</div></div>";
}
?>

<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<main id="contenu-principal" tabindex="-1">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php


// verif si demande de DST
if ( ($_SESSION["membre"] == "menuadmin") ||  ($_SESSION["membre"] == "menuscolaire") ) {
	robottxt();
	adsens();
	$demdst=consult_demande_dst_acces2();
	$affiche="<div id='Mdst' align='center' ><a href='calendrier_config_dst1.php'><font class='T2' color=red><b>".LANGTP22."</b></font></a></div><br />";
	$affiche.="<script type='text/javascript' > new Effect.Pulsate('Mdst', {pulses : 5 , duration: 10 }); </script>";
	if ( countTriade($demdst) > 0 ) {
		print $affiche;
	}
}
// fin de la verif de demande DST
// verif si demande de reservation
if ( ($_SESSION["membre"] == "menuadmin") ||  ($_SESSION["membre"] == "menuscolaire") ) {
        $demresa=consult_resa();
	$affiche1="<div id='Mdst2' align='center' ><a href='resr_admin.php'><font class='T2' color=red><b>".LANGTP23."</b></font></a></div><br />";
	$affiche1.="<script type='text/javascript' > new Effect.Pulsate('Mdst2', {pulses : 5 , duration: 10 }); </script>";
        if ( countTriade($demresa) > 0 ) {
                print $affiche1;
	}
}

if ( ($_SESSION["membre"] == "menuadmin") ||  ($_SESSION["membre"] == "menuscolaire") ) {
	$affiche3="<div id='Mdst3' align='center' ><a href='sms.php'><font class='T2' color=red><b>$errorsms</b></font></a></div><br />";
	$affiche3.="<script type='text/javascript' > new Effect.Pulsate('Mdst3', {pulses : 5 , duration: 10 }); </script>";
        if ($errorsms != "") {
                print $affiche3;
	}
}



// fin de la verif de demande DST


if (isset($_POST["createvideo"])) {
	if ((trim($_POST["saisie_lien"]) != "") && (trim($_POST["saisie_lien"]) != "http://")) {
		$source=$_POST["saisie_lien"];
		if (GRAPH == 0) {
			$configflv.="playercolor=506E87\n";
			$configflv.="bgcolor1=B2CADE\n";
			$configflv.="bgcolor2=B2CADE\n";
		}elseif(GRAPH == 1) {
			$configflv.="playercolor=666666\n";
			$configflv.="bgcolor1=666666\n";
			$configflv.="bgcolor2=666666\n";
		}else{
			$configflv.="playercolor=506E87\n";
			$configflv.="bgcolor1=B2CADE\n";
			$configflv.="bgcolor2=B2CADE\n";
		}
		
		$resultatvideo="<br><br><table align='center' style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);' ><tr><td>";
		$resultatvideo.="
		<video controls width='420' height='280' poster='' autoplay >
		<source src='$source' type='video/mp4'>

  		<!-- fallback pour les navigateurs qui ne supportent pas mp4 -->
	 	<source src='$source' type='video/webm'>

		</video>	
		";
		$resultatvideo.="</tr></table><br /><br />";

	}else{
		$lienyoutube=$_POST["saisie_lien_youtube"];
		$lienyoutube=preg_replace('/watch\?v=/',"v/",$lienyoutube);
		$resultatvideo="<br><br><table align='center' style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);' ><tr><td>";
		$resultatvideo.="<object width='520' height='300'>";
		$resultatvideo.="<param name='movie' value='$lienyoutube?fs=1&amp;hl=fr_FR&amp;rel=0'></param>";
		$resultatvideo.="<param name='allowFullScreen' value='false'></param>";
		$resultatvideo.="<param name='allowscriptaccess' value='always'></param>";
		$resultatvideo.="<embed src='$lienyoutube?fs=1&amp;hl=fr_FR&amp;rel=0' "; 
		$resultatvideo.=" type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='false' width='520' height='300'></embed>";
		$resultatvideo.="</object></td></tr></table><br /><br />";
	}
	$cr=create_news_video($_POST["saisie_titre"],addslashes($resultatvideo),$_SESSION["nom"],$_SESSION["prenom"],'video',$indice);
	history_cmd($_SESSION["nom"],"COMMUNIQUER","Vidéo");
	
}


?>

<?php print $modeete ?>

<?php print $inscri ?>

<?php if (ishttps()) { ?>
<?php if (($_SESSION["membre"] == 'menuadmin') || ($_SESSION["membre"] == 'menuscolaire') || ($_SESSION["membre"] == 'menuprof')) { ?>
<?php if ($_COOKIE["notification"] != '1') { ?>
<style>
.button1 {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
  color:red;
}

.button2:hover {
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
}
</style>
<br />
<div align='center'><button id='notifier-btn' class='button1' onClick="activeNotification()" >Activer la notification windows</button></div>
<br />
<?php } } }

$afficheintro="ok";
if (isset($_POST["kointro"])) {
	$afficheintro="ko";
	if (preg_match('/demo.triade-educ.net/',WEBROOT)) {
		print "<script>alert(\"Action non validé en version de démonstration !\")</script>";
	}else{
		introPrezModif($afficheintro,$_SESSION['id_pers'],$_SESSION['membre']);
	}
}

if (isset($_POST["okintro"])) {
	$afficheintro="ok";
	introPrezModif($afficheintro,$_SESSION['id_pers'],$_SESSION['membre']);
}


$afficheintro=verifIntroPrez($afficheintro,$_SESSION['id_pers'],$_SESSION['membre']);

$infopreztexte="";
if ($_SESSION["membre"] == "menuadmin") $infopreztexte="de direction";
if ($_SESSION["membre"] == "menuprof") $infopreztexte="d'enseignant";
if ($_SESSION["membre"] == "menueleve") $infopreztexte="d'étudiant";
//if ($_SESSION["membre"] == "menuscolaire") $infopreztexte="de vie scolaire";
if ($_SESSION["membre"] == "menuparent") $infopreztexte=" parent";

$modeintro="<form method='post' action='acces2.php?id'  ><div class='intro' ><font class=T2>Bonjour et bienvenue sur TRIADE ! <br> Souhaitez vous une pr&eacute;sentation de votre interface $infopreztexte ? <br><br><input type='submit' value='Oui avec plaisir' name='okintro' class='button' >&nbsp;<input type='submit' class='button' value='Ne plus me demander' name='kointro' data-title=\"Présentation terminée\" data-step='99' data-intro =\"La pr&eacute;sentation est terminée, merci de votre attention, vous pouvez maintenant clique sur ce bouton.\"  ></font></div></form><br>"; 

if (($afficheintro == "") || ($afficheintro == "ok")) {
	if ($infopreztexte != "") print $modeintro ;
}


?>
<div id='updatetriade' style='display:none;'><center><a href='./admin/' target='_blank' ><font class='T2' color='red'>ATTENTION des mises à jour TRIADE sont disponibles, consulter le compte administrateur.</font></a></font><br><br></center></div>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" role="presentation">
<tr class='coulBar0'>
<td height="2"> <strong><span class='menumodule1'><?php print LANGTITRE1?></span></strong></td>
<tr id="cadreCentral0" >
<td >
<TABLE border='0' width='100%' role="presentation">
<TR><TD valign=top align='center'>
</td><TD valign=top width=37% align=center rowspan='2' style="padding-right:14px">
<?php include("./librairie_php/lib_calendrier.php");?>
<SCRIPT type='text/javascript'>
// Affichage du calendrier du mois
// calendar(colFond,colTitre,colTexte,colFerie,colOn)
calendar("#FFFFCC","#CCCCFF","#0000CC","#66FF66","red");
</SCRIPT>
<div id="cal-info" class="cal-info" style="display:none"></div>
<?php print $comaudio; ?>
</td>



<!-- // fin  -->
<?php include("librairie_php/feteprenom.php"); ?>
<TR><TD valign=top >
<TABLE width=100% border=0 role="presentation">
<TR><TD>



&nbsp;&nbsp;<strong><font class=T2><?php print LANGRAS1?></font></strong> &nbsp;&nbsp;<img align=bottom src="./image/commun/calendrier/<?php print datej();?>.gif" alt="" aria-hidden="true"><br /><br />

<?php
// fete
if (FETE != "non") {
	print "&nbsp;&nbsp";
	print "&nbsp;&nbsp;<i>".LANGFETE;
	print fete_prenom();
}


// anniversaire
if (ANI == "oui") {
	$dataAni=chercheAnniversaire(); //elev_id,nom,prenom,classe
	if (countTriade($dataAni) > 0) {
		print "<br /><br />&nbsp;&nbsp;&nbsp;".LANGPARAM38;
	}
	for($A=0;$A<countTriade($dataAni);$A++) {
		if (NOMANI == "oui") { 
			$nomeleve=$dataAni[$A][1];
		}else{
			$nomeleve="";
		}
		$ideleve=$dataAni[$A][0];
		$classeEleve=$dataAni[$A][3];
		$photoeleve="image_trombi.php?idE=".$ideleve;	
		print " <a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' ><br><i>".trunchaine(chercheClasse_nom($classeEleve),10)."</i> ');\"  onMouseOut='HideBulle()'>".strtoupper($nomeleve)." ".ucwords($dataAni[$A][2])."</a>,";
		print "&nbsp;";
	}
	print "<br />";
}else{
	print "<br />";
}



//meteo
//-----
if ((LAN == "oui") && (METEOVALIDE == "oui")) {
	print "<br />";
	print "<div style='display:inline-block;width:66%;vertical-align:top'>";
	include_once("./meteo/meteo.php");
	print "</div>";
}

//-----
	print "<br /><br />";
	print "&nbsp;&nbsp;<strong><font class=T2>".LANGFEN1."</font> : </strong>";
	print "<br />";
	print "<br />";
        $data=affEvenement();
// $data : tab bidim - soustab 3 champs
$j=1;
for($i=0;$i<countTriade($data);$i++)
{
        $date_actuel=dateDMY2();
        if ($data[$i][1] == $date_actuel) {
                print " &nbsp;&nbsp ".$j.")<b> ".ucfirst($data[$i][2])."</B><br />";
		$j++;
        }
}

?>
</TD></TR></TABLE>
</TD>
</TR>
<tr><td colspan=2>
<?php 
if (DSTVISUACCUEIL != "non") {
?>
<table  border=0 width=100% >
<TR><TD>
&nbsp;&nbsp;<strong><font class=T2><?php print ucfirst(LANGCALEN9) ?></font> : </strong><BR><BR>
<?php
$data=affDst();
// $data : tab bidim - soustab 3 champs
for($i=0;$i<countTriade($data);$i++) {
	$date_actuel=dateDMY2();
	if ($data[$i][1] == $date_actuel) {
		print "&nbsp;&nbsp; ".LANGCALEN7." : <B>".trim($data[$i][3])."</B> ";
      		print " <BR>&nbsp;&nbsp ". LANGCALEN8 ." :<b> ".trim($data[$i][2])."</B> à ".timeForm($data[$i][4])." durée ".$data[$i][5]." heure(s) <BR><BR>";
	}
}

?>
<BR></TD></TR></TABLE>
<?php } ?>


<?php
if ($_SESSION["membre"] == "menuprof") {
?>
<br />
<table   border=0 width=100% >
<TR><TD>
&nbsp;&nbsp;<strong><font class=T2><?php print LANGacce6 ?></font> : </strong><BR><BR>
<?php
if (isset($_GET["suppdevoir"])) {
	 supp_discipline_prof($_GET["suppdevoir"]);
}
// sanction devoir à faire
$data=cherche_discipline_prof_devoir($_SESSION["id_pers"]);
//id_eleve,sanction,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait FROM discipline_prof
for($i=0;$i<countTriade($data);$i++) {
	$nom_eleve=recherche_eleve_nom($data[$i][0]);
	$prenom_eleve=recherche_eleve_prenom($data[$i][0]);
	$sanction=rechercheCategory($data[$i][1]);
	$devoir_a_faire=$data[$i][2];
	$motif=$data[$i][7];
	$classe=$data[$i][9];
	$description_fait=trim($data[$i][11]);
	print "&nbsp;".LANGacce1." <b>$nom_eleve $prenom_eleve</b> ($classe) ".LANGacce12." <u>$sanction</u> ".LANGacce13." : <b>$motif</b>";
	print "<br />&nbsp;"."Description des faits : ".$description_fait;
	print "<br />&nbsp;".LANGacce14." <i>$devoir_a_faire</i>";
	print "<br />&nbsp;"."<strong>".LANGacce16."</strong><br />";
//	print "<div align=right>[<a href='acces2.php?suppdevoir=".$data[$i][10]."' >".LANGacce21."</a>]&nbsp;&nbsp;&nbsp;</div>";
        print "<hr width='80%' align='left'>";
}


$data=cherche_discipline_prof_devoir_retenu($_SESSION["id_pers"]);
//id_eleve,sanction,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait FROM discipline_prof
for($i=0;$i<countTriade($data);$i++) {
        $nom_eleve=recherche_eleve_nom($data[$i][0]);
        $prenom_eleve=recherche_eleve_prenom($data[$i][0]);
        $sanction=rechercheCategory($data[$i][1]);
        $devoir_a_faire=$data[$i][2];
        $motif=$data[$i][7];
	$classe=$data[$i][9];
	$description_fait=trim($data[$i][11]);
        print "&nbsp;".LANGacce3."<b>$nom_eleve $prenom_eleve</b> ($classe) <font color=red><b>".LANacce31." <u>$sanction</u> ".LANacce32."<b>$motif</b>";
	print "<br />&nbsp;"."Description des faits : ".$description_fait;
	print "<br />&nbsp;".LANGacce4." <i>$devoir_a_faire</i><br />";
 //       print "<div align=right>[<a href='acces2.php?suppdevoir=".$data[$i][10]."' >".LANGacce5."</a>]&nbsp;&nbsp;&nbsp;</div>";
        print "<hr width='80%' align='left'>";
}
?>
<BR></TD></TR></TABLE>
<?php } ?>

<?php if ((LAN == "oui") && (HTTPS != "oui")) { include_once('./librairie_php/xiti.php'); } ?>
</td></tr>
</TABLE>
<!-- // fin  -->
</td></tr></table>
<!----------------------------------------->

<?php
if (LAN == "oui") {
	if ($_SESSION["membre"] == "menuadmin") {
		$updatestatus=0;
		$status=nbpersreeel();
		$datestatus=dateDMY2();
		$productID=PRODUCTID;
		if (file_exists("./data/install_log/status.log")) {
			$info=file_get_contents("./data/install_log/status.log");
			list($datestatus0,$host,$nbeleve,$nbpers,$productID0)=preg_split("/:/",$info);	
			if (($datestatus0 != $datestatus) || ($productID != $productID0)) {
				$fd=fopen("./data/install_log/status.log","w");
			       	fwrite($fd,"$datestatus:$status:$productID");
				fclose($fd);
				$updatestatus=1;
			}
		}else{
			$fd=fopen("./data/install_log/status.log","w");
		       	fwrite($fd,"$datestatus:$status:$productID");
			fclose($fd);
			$updatestatus=1;
		}

		if ($updatestatus == 1) {
			$status=nbpers();
			$http=protohttps(); // return http:// ou https://
			print "<script language='JavaScript' src='".$http."www.triade-educ.org/status/index.php?id=$status&productId=$productID' ></script>\n";
		}
	}
}


if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menuprof")) {
if ($_SESSION["membre"] == "menuparent") { $idclasse=chercheIdClasseDunEleve($mySession['Spid']); }
if ($_SESSION["membre"] == "menuprof")   { $idclasse=chercheIdClasseDunProfP($mySession['Spid']); }

$nomclasse=chercheClasse($idclasse);
$idprofP=aff_prof_p_classe($idclasse);
$nonprofP=0;
if ($idprofP > 0) {
	$nonprofP=1;
	$nomprofp=recherche_personne($idprofP);
}
?>
<BR><bR>
<?php
if ($nonprofP != 0) {
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" role="presentation">
<tr class='coulBar0'><td height="2"> <b><span class='menumodule1'><?php print LANGPROFP3?></span></b> <?php print " : <font id='color2' >".$nomprofp." </font>"?></td>
</tr>
<tr id="cadreCentral0" >
<td valign=top>
<!-- // fin  -->
<table width=100% border=0>
<?php
// id,idclasse,commentaire,date_saisie
$data=aff_news_prof_p($idclasse,$idprofP);
if (countTriade($data) > 0 ) {
	
?>
<?php
for($i=0;$i<countTriade($data);$i++) {
	$message=stripslashes(preg_replace(['/<p>\&nbsp;<\/p>/','#(\\\\r|\\\\r\\\\n|\\\\n)#'],['',' '],$data[$i][2]));
	$nomprofp2=recherche_personne($data[$i][4]);
?>
<div class="dash-profp-msg">
	<?php print $message ?>
	<div class="dash-profp-meta"><?php print dateForm($data[$i][3]) ?> — <?php print $nomprofp2 ?></div>
</div>
<?php } ?>
<?php

}else {
?>
<br /><br />
<center><font size=2><?php print LANGPARENT1?></font></center>


<?php
}
?>
</tr></td></table>

<!-- // fin  -->
</td></tr></table>
<?php } } ?>
<!----------------------------------------->
<?php
if (file_exists("./common/config-ia.php")) {
    include_once("common/productId.php");
    include_once("common/config-ia.php");
    $dash_pid  = defined('PRODUCTID') ? PRODUCTID : '';
    $dash_ikey = defined('IAKEY')     ? IAKEY     : '';
    if ($dash_pid && $dash_ikey):
?>
<br /><br />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" role="presentation">
<tr class='coulBar0'>
<td height="2"><b><span class='menumodule1'><span aria-hidden="true">&#129302; </span>Agents IA — TRIADE-COPILOT</span></b></td>
</tr>
<tr id="cadreCentral0">
<td>
<div id="dash_agents_list" style="min-height:40px;"><em style="font-size:12px;color:#aaa;padding:8px 4px;display:block;">Chargement...</em></div>
</td>
</tr>
</table>

<!-- Popup chat agent dashboard -->
<div id="dash_chat_popup" style="display:none;position:fixed;bottom:20px;right:20px;width:360px;height:460px;
     background:#fff;border:1px solid #dde3f5;border-radius:12px;box-shadow:0 8px 32px rgba(8,10,102,.18);
     z-index:9999;flex-direction:column;overflow:hidden;">
  <div style="background:#080A66;color:#fff;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;border-radius:12px 12px 0 0;">
    <span style="font-size:13px;font-weight:700;">&#129302; <span id="dash_chat_titre">Agent</span></span>
    <button onclick="fermerChatDash()" style="background:transparent;border:none;color:#fff;font-size:18px;cursor:pointer;line-height:1;">&#10005;</button>
  </div>
  <div id="agent_chat_history" style="flex:1;overflow-y:auto;padding:10px;background:#fafbff;"></div>
  <div id="agent_chat_result" style="padding:0 10px;"></div>
  <div style="display:flex;gap:6px;padding:8px;border-top:1px solid #eee;background:#fff;">
    <input id="agent_chat_input" type="text" placeholder="Votre message…"
           onkeydown="if(event.key==='Enter')document.getElementById('btn_agent_send').click();"
           style="flex:1;padding:6px 10px;border:1px solid #dde3f5;border-radius:6px;font-size:12px;outline:none;" />
    <button id="btn_agent_send" onclick="envoyerChatDash()"
            style="padding:6px 14px;background:#080A66;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">Envoyer</button>
  </div>
</div>

<script type="text/javascript" src="./librairie_js/ajaxIA.js"></script>
<script>
var _dashAgentId  = '';
var _dashAgentNom = '';
var _dashPid      = <?php echo json_encode($dash_pid); ?>;
var _dashIkey     = <?php echo json_encode($dash_ikey); ?>;

function ouvrirChat(agentId, agentNom) {
    _dashAgentId  = agentId;
    _dashAgentNom = agentNom;
    document.getElementById('dash_chat_titre').textContent = agentNom;
    document.getElementById('agent_chat_history').innerHTML = '';
    document.getElementById('agent_chat_result').innerHTML  = '';
    document.getElementById('agent_chat_input').value = '';
    document.getElementById('dash_chat_popup').style.display = 'flex';
    document.getElementById('agent_chat_input').focus();
}
function ouvrirAgentDash(agentId, agentNom) { ouvrirChat(agentId, agentNom); }

function fermerChatDash() {
    document.getElementById('dash_chat_popup').style.display = 'none';
    _dashAgentId = '';
}

function envoyerChatDash() {
    if (!_dashAgentId) return;
    ajaxRunAgent(_dashAgentId, _dashAgentNom, _dashPid, _dashIkey);
}
(function() {
    var req = new XMLHttpRequest();
    req.open('POST', 'proxy-ia.php?e=agent-list.php', true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onload = function() {
        var el = document.getElementById('dash_agents_list');
        if (!el) return;
        if (req.status === 200) {
            el.innerHTML = req.responseText;
        } else {
            el.innerHTML = "<em style='font-size:11px;color:#c00;padding:6px 4px;display:block;'>Erreur " + req.status + " — " + req.responseText.substring(0, 150) + "</em>";
        }
    };
    req.onerror = function() {
        var el = document.getElementById('dash_agents_list');
        if (el) el.innerHTML = "<em style='font-size:11px;color:#c00;padding:6px 4px;display:block;'>Erreur réseau — impossible de joindre le serveur.</em>";
    };
    req.send('product_id=' + encodeURIComponent(_dashPid) + '&key=' + encodeURIComponent(_dashIkey));
})();
</script>
<?php endif; } ?>
<!----------------------------------------->
<?php
if (LAN == "oui") {
?>
<script>
var idactu="KO";
var update="";
var triadeactu="KO"; 
var triadeactutest="KO"; 
var messageactu=""; 
var messagerecup="";
var messageaff="";
var messageactueleve=""; 
var messageactustage="";
var messageactupers="";
</script>
<script src='https://support.triade-educ.org/support/triade-actu.php'></script>
<?php
if ($_SESSION["membre"] == "menuadmin") {
       $data=list_patch();
        $nb=countTriade($data);
	print "<script src='https://support.triade-educ.org/support/update-triade-dir.php?version=".VERSION."&nb=$nb' ></script>\n";
	print "<script>\n";
	print "if (update == \"oui\") {\n";
	print "\tdocument.getElementById('updatetriade').style.display='block'\n";
	print "}\n";
	print "</script>\n";
}
?>
<script>
function triadeactufnc(val) {
	document.getElementById('triadeactu').style.display="none";
	setCookie("TRIADE-ACTU",val,15);
}

var dev="<?php print DEV ?>";
var idactulocal=getCookie("TRIADE-ACTU");
if ((idactulocal != idactu) || (dev == "1")) {
	if ((triadeactu == '1') || ((triadeactutest == dev) && (triadeactutest == '1'))) { 
	   
	    <?php if ($_SESSION['membre'] == "menuadmin") 	{ print "messagerecup=messageactupers;"; } ?>
	    <?php if ($_SESSION['membre'] == "menuscolaire") 	{ print "messagerecup=messageactupers;"; } ?>
	    <?php if ($_SESSION['membre'] == "menuprof") 	{ print "messagerecup=messageactupers;"; } ?>
	    <?php if ($_SESSION['membre'] == "menueleve") 	{ print "messagerecup=messageactueleve;"; } ?>
	    <?php if ($_SESSION['membre'] == "menuparent") 	{ print "messagerecup=messageactueleve;"; } ?>
	    <?php if ($_SESSION['membre'] == "menututeur") 	{ print "messagerecup=messageactustage;"; } ?>
	    <?php if ($_SESSION['membre'] == "menupersonnel") 	{ print "messagerecup=messageactupers;"; } ?>

	    if (messagerecup == "") { 
		messageaff=messageactu; 
	    }else{
 		messageaff=messagerecup; 
	    }
 
	    if (messageaff != "") {
document.write("<style>");
document.write(".cadre_central { border: 1px solid #DAD9D9;border-radius: 5px 5px 5px 5px;color: #333333;width:97%;padding:10px;padding-top:20px;padding-bottom:30px; vertical-align: top; background-color: #F9F9F9; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); vertical-align: top;  } ");
document.write(".shadow { text-shadow: 2px 4px 3px rgba(0, 0, 0, 0.3); } ");
document.write(".font1 { font-family: 'Electrolize',cursive; color:#544F93; font-size: 16px; } ");
document.write("</style>");

document.write("<br><br><div class='cadre_central' id='triadeactu' style='background-color:#FFFFFF;'  >");
document.write("<table border='0' width='100%'><tr><td width='50%'><font class='font1 shadow' ><b>TRIADE-ACTU</b></font></td>");
document.write("<td align='right'><a href='#' onClick='triadeactufnc(idactu); return false;' role='button' aria-label='Fermer actualité' ><img src='./image/closeb.gif' border='0' alt='Fermer' ></a>&nbsp;&nbsp;</td></tr></table>");
document.write("<br/>");
document.write(messageaff);
document.write("</div>");
		}
	}
}
</script>

<?php } ?>

<!----------------------------------------->
<br /><br />
     <table role="presentation" border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
     <tr class='coulBar0'>
     <td height="2"> <b><span class='menumodule1'><?php print LANGTITRE2?></span></b></td>
     </tr>
     <tr id="cadreCentral0" >
<td valign=top>
<!-- // fin  -->
<?php
$recupMessAdmin=consultMessAdmin();
if (countTriade($recupMessAdmin)) { $nbbordure='1'; }else{ $nbbordure='0'; }
?>
<table class="dash-news-table">
<?php
for($i=0;$i<countTriade($recupMessAdmin);$i++) {
	$imgfilm=$recupMessAdmin[$i][7];
	$titre1=stripslashes($recupMessAdmin[$i][5]);
	$heure1=timeForm($recupMessAdmin[$i][4]);
	$date1=dateForm($recupMessAdmin[$i][3]);
	$id1=$recupMessAdmin[$i][0];
	if (trim($titre1) == "") $titre1="<i>sans objet</i>";
	$icon = ($imgfilm == "video")
		? "<img src='./image/commun/video-icon.png' border='0' width='20' alt='Vidéo' style='vertical-align:middle;margin-right:6px'>"
		: "<img src='./image/commun/newspaper.png' border='0' width='20' alt='Article' style='vertical-align:middle;margin-right:6px'>";
?>
<tr class="dash-news-row">
	<td><?php print $icon ?><a href="#" onclick="displayMessage('messageAccueil.php?id=<?php print $id1 ?>', <?php print htmlspecialchars(json_encode(strip_tags($titre1))) ?>);return false;" class="dash-news-link"><?php print $titre1 ?></a></td>
	<td class="dash-news-meta"><?php print LANGTE2 ?> : <?php print $date1 ?> — <?php print $heure1 ?></td>
</tr>
<?php } ?>
</table>

<script type="text/javascript">
	setSlideDownSpeed(4);
	function inverseimage(img) {
		var re = /za\.png/;
		if (re.test(document.getElementById(img).src)) {
			document.getElementById(img).src="image/commun/za2.png";
		}else{
			document.getElementById(img).src="image/commun/za.png";
		}

	}
	
</script>

	



     <!-- // fin  -->
     </td></tr></table>




</main>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
     print "<SCRIPT type='text/javascript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
     print "</SCRIPT>";
else :
     print "<SCRIPT type='text/javascript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
     print "</SCRIPT>";
     top_d();
     print "<SCRIPT type='text/javascript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
     print "</SCRIPT>";
endif ;
//------------------------------
	   	// module messagerie
	  
     	   // print $_SESSION["id_suppleant"];
     	   if ((isset($_SESSION["id_suppleant"])) && ( $_SESSION["id_suppleant"] > 0)) {
     	   	$id_pers=$_SESSION["id_suppleant"];
	   }else{
		$id_pers=$_SESSION["id_pers"];
	   }
	   popupfen($id_pers,$_SESSION["membre"],$_SESSION["nom"],$_SESSION["prenom"],$_SESSION["ip"],$_SESSION["os"],$_SESSION["nav"],$_SESSION["id_session"]);
?>
	   <SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
	   <script type="text/javascript" >new Effect.Pulsate("PAIE1", {pulses : 5 , duration: 10 }); </script>
	   <script type="text/javascript" >new Effect.Pulsate("PAIE2", {pulses : 5 , duration: 10 }); </script>
	   <script type="text/javascript" >new Effect.Pulsate("PAIE3", {pulses : 5 , duration: 10 }); </script>

<?php
	   if (!isset($navigateur)) $navigateur='';
?>

<script type="text/javascript">
messageObj = new DHTML_modalMessage();	// We only create one object of this class
messageObj.setShadowOffset(5);	// Large shadow
function displayMessage(url, titre) {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	xhr.onload = function() {
		if (xhr.status === 200) {
			var html = xhr.responseText
				.replace(/<html[^>]*>|<\/html>|<\/body>/gi, '')
				.replace(/<head[\s\S]*?<\/head>/gi, '')
				.replace(/<body[^>]*>/gi, '');
			alertify.alert(
				titre || 'Actualité',
				'<div style="width:100%;overflow-y:auto;padding:6px 12px;font-size:13px;color:#333;line-height:1.6">' + html + '</div>'
			).set('resizable', true).set('movable', true).set('closable', false).set('closableByDimmer', false);
		}
	};
	xhr.send();
}
function displayStaticMessage(messageContent,cssClass) {
	messageObj.setHtmlContent(messageContent);
	<?php if ($navigateur == "IE") { ?>
		messageObj.setSize(700,400);
	<?php }else{ ?>
		messageObj.setSize(700,'100%');
	<?php } ?>
	messageObj.setCssClassMessageBox(cssClass);
	messageObj.setSource(false);	// no html source since we want to use a static message here.
	messageObj.setShadowDivVisible(false);	// Disable shadow for these boxes	
	messageObj.display();
}
function closeMessage() { messageObj.close(); }
</script>
<?php if (md5_file("librairie_php/mactu.php") != "7f1e92090ce16c5d90d1acf8e1077010") { ?>
<?php } ?>
<script> 
<?php if ((isset($_POST["okintro"])) || (isset($_GET['aidenew']))) { ?>
	   introJs().setOptions({ nextLabel: 'Suivant', prevLabel: 'Précédent', doneLabel: 'Terminé' , exitOnOverlayClick: false , showProgress: true  }).start();
<?php } ?>
</script>
</BODY></HTML>
<?php
// fin_prog($debut);
Pgclose();
include_once("./librairie_php/finbody.php"); 
include_once("librairie_php/lib_verif_nav.php");
$navigateur=verif_navigateur();
file_get_contents("https://www.triade-educ.org/accueil/infoTriade.php");
?>
