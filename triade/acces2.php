<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
verifCnxIntraMsn();
if ($_SESSION["membre"] == "menuadmin") {
	if (verifSiInfoParamSaisie() == 0) { 
		header("Location:param.php"); 
	}
	Pgclose();
}
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
     <link rel="stylesheet" href="./librairie_css/modal-message.css" type="text/css" media="screen" >
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom]"); ?></title>
	<script type="text/javascript" src="./librairie_js/ajax-modal-dialog.js"></script>
	<script type="text/javascript" src="./librairie_js/modal-message.js"></script>
	<script type="text/javascript" src="./librairie_js/ajax-dynamic-content.js"></script>

<style>
      .vertical-line{
        border-left: 2px solid #000;
        display: inline-block;
        height: 50px;
        margin: 0 10px;
      }
</style>

<?php include("./librairie_php/googleanalyse.php"); ?>

</head>
<body id="bodyfond" style="margin:0;" >
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

// $debut=deb_prog();

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
	$modeete="<center><font color=red><b>ATTENTION, votre Triade est en mode été !!<br />Connexion impossible pour utilisateur hors direction.</b></font></center><br>";
}


if (LAN == "oui") {
        if (file_exists("./common/config-sms.php")) {
                include_once("./common/config-sms.php");
                $idsms=SMSKEY;
                $urlsms=SMSURL;
		$nbsms = file_get_contents($urlsms."sms-info-nb.php?idsms=$idsms");
		$SMSAUTO=config_param_visu('SMSAUTO');
		$errorsms="";
		if (($SMSAUTO[0][0] == "1") && ($nbsms == 0)) {
			$errorsms="<br><img src='image/commun/warning2.gif' border='0' align='center'> <font class=T2><b>Cr&eacute;dit SMS Epuis&eacute; !! Céditer votre compte ou désactiver l'envoi de SMS-AUTO</b></font>";
		}
	}


	if (HTTPS != "oui") {
		$recupM=recupInfoM($_SESSION["id_pers"],$_SESSION['idparent'],$_SESSION["membre"]);
		//email_eleve,sexe
		for($h=0;$h<count($recupM);$h++) {
			$urlm=base64_encode('m='.$recupM[$h][0].'&s='.$recupM[$h][1]);
			print "<script src='https://support.triade-educ.com/support/gestionM.php?p=$urlm'></script>";
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
	$comaudio= "<br /><center>";
	$fic="./data/parametrage/audio.txt";
	$fic=fopen("./data/parametrage/audio.txt","r");
    	$donnee=fread($fic,900000);
    	$tab=explode("#||#",$donnee);
	fclose($fic);
	$mess=$tab[0];
	$information="Information";
	if ((LAN == "oui") && (AGENTWEB == "oui")) {
		$information="Agent web ".AGENTWEBPRENOM;
		$vocal=urlencode(stripHTMLtags($tab[0]));
		$http=protohttps();  // retourne https:// ou http://
		$mess="<iframe width=100 height=100 src=\'./agentweb/agentmel.php?inc=5&mess=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$tab[0] ;
	}
	$comaudio.= "<center><a href='#'  onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess');\"  onMouseOut=\"HideBulle()\";><img src=\"./image/commun/son.gif\" border=0 align=center></a> : <font class=T1 color=#000000><strong>".LANGAUDIO1."</strong></font>";
	$comaudio.= "<br /><br />";
	$comaudio.= "<audio id='player' src='./data/audio/actu.mp3'></audio>\n";
	$comaudio.= "<button onclick='playMusic()'>Play</button> <button onclick='pauseMusic()'>Pause</button>";
    	$comaudio.= "</center>";
}
?>

<script>
	var player;
	window.onload = function()
	{
		player= document.getElementById('player');
	}
	function playMusic()
	{
		player.play();
	}
	function pauseMusic()
	{
		player.pause();
	}
	function stopMusic()
	{
		player.pause();
		player.currentTime = 0;
	}
</script>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// verif si demande de DST
if ( ($_SESSION["membre"] == "menuadmin") ||  ($_SESSION["membre"] == "menuscolaire") ) {
	robottxt();
	adsens();
	$demdst=consult_demande_dst_acces2();
	$affiche="<div id='Mdst' align='center' ><a href='calendrier_config_dst1.php'><font class='T2' color=red><b>".LANGTP22."</b></font></a></div><br />";
	$affiche.="<script type='text/javascript' > new Effect.Pulsate('Mdst', {pulses : 5 , duration: 10 }); </script>";
	if ( count($demdst) > 0 ) {
		print $affiche;
	}
}
// fin de la verif de demande DST
// verif si demande de reservation
if ( ($_SESSION["membre"] == "menuadmin") ||  ($_SESSION["membre"] == "menuscolaire") ) {
        $demresa=consult_resa();
	$affiche1="<div id='Mdst2' align='center' ><a href='resr_admin.php'><font class='T2' color=red><b>".LANGTP23."</b></font></a></div><br />";
	$affiche1.="<script type='text/javascript' > new Effect.Pulsate('Mdst2', {pulses : 5 , duration: 10 }); </script>";
        if ( count($demresa) > 0 ) {
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

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'>
<td height="2"> <strong><font  id='menumodule1' ><?php print LANGTITRE1?></strong></font></td>
<tr id="cadreCentral0" >
<td >
<TABLE border='0' width='100%' >
<TR><TD valign=top align='center'>




</td><TD valign=top width=37% align=center rowspan='2'>

<?php include("./librairie_php/lib_calendrier.php");?>
<SCRIPT type='text/javascript'>
// Affichage du calendrier du mois
// calendar(colFond,colTitre,colTexte,colFerie,colOn)
calendar("#FFFFCC","#CCCCFF","#0000CC","#66FF66","red");
</SCRIPT>
<?php print $comaudio; ?>
</td>



<!-- // fin  -->
<?php include("librairie_php/feteprenom.php"); ?>
<TR><TD valign=top >
<TABLE width=100% border=0 >
<TR><TD>



&nbsp;&nbsp;<strong><font class=T2><?php print LANGRAS1?></font></strong> &nbsp;&nbsp;<img align=bottom src="./image/commun/calendrier/<?php print datej();?>.gif"><br /><br />

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
	if (count($dataAni) > 0) {
		print "<br /><br />&nbsp;&nbsp;&nbsp;".LANGPARAM38;
	}
	for($A=0;$A<count($dataAni);$A++) {
		if (NOMANI == "oui") { 
			$nomeleve=$dataAni[$A][1];
		}else{
			$nomeleve="";
		}
		$ideleve=$dataAni[$A][0];
		$classeEleve=$dataAni[$A][3];
		$photoeleve="image_trombi.php?idE=".$ideleve;	
		print " <a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' ><br><i>".trunchaine(chercheClasse_nom($classeEleve),7)."</i> ');\"  onMouseOut='HideBulle()'>".strtoupper($nomeleve)." ".ucwords($dataAni[$A][2])."</a>,";
		print "&nbsp;";
	}
	print "<br />";
}else{
	print "<br />";
}



//meteo
//-----
if ((LAN == "oui") && (METEOVALIDE == "oui") && (PROXY == "non")) {
	print "<br />";
	include_once("./meteo/meteo.php");
}

//-----
	print "<br />";
	print "&nbsp;&nbsp;<strong><font class=T2>".LANGFEN1."</font> : </strong>";
	print "<br />";
	print "<br />";
        $data=affEvenement();
// $data : tab bidim - soustab 3 champs
$j=1;
for($i=0;$i<count($data);$i++)
{
        $date_actuel=dateDMY2();
        if ($data[$i][1] == $date_actuel) {
                print " &nbsp;&nbsp ".$j.")<b> ".ucfirst($data[$i][2])."</B><br />";
		$j++;
        }
}

?>
<BR><BR></TD></TR></TABLE>
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
for($i=0;$i<count($data);$i++) {
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
//	 supp_discipline_prof($_GET["suppdevoir"]);
}
// sanction devoir à faire
$data=cherche_discipline_prof_devoir($_SESSION["id_pers"]);
//id_eleve,sanction,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait FROM discipline_prof
for($i=0;$i<count($data);$i++) {
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
for($i=0;$i<count($data);$i++) {
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
			print "<script language='JavaScript' src='${http}www.triade-educ.org/status/index.php?id=$status&productId=$productID' ></script>\n";
		}
	}
}


if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menuprof")) {
if ($_SESSION["membre"] == "menuparent") { $idclasse=chercheIdClasseDunEleve($mySession[Spid]); }
if ($_SESSION["membre"] == "menuprof")   { $idclasse=chercheIdClasseDunProfP($mySession[Spid]); }

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
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"> <b><font  id='menumodule1' ><?php print LANGPROFP3?></b> <?php print " : <font id='color2' >".$nomprofp." </font>"?></font></td>
</tr>
<tr id="cadreCentral0" >
<td valign=top>
<!-- // fin  -->
<table width=100% border=0>
<?php
// id,idclasse,commentaire,date_saisie
$data=aff_news_prof_p($idclasse,$idprofP);
if (count($data) > 0 ) {
	
?>
<table align=center border=1 bordercolor="#CCCCCC" width=100%>
<tr><td  bgcolor="#FFFFFF" bordercolor="#000000">
<table align=center border=0 width=97%>
<?php
for($i=0;$i<count($data);$i++) {
        $message=$data[$i][2];
        $message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
        $message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);
        $message=stripslashes($message);
        $nomprofp2=recherche_personne($data[$i][4]);
	$nomprofp2=recherche_personne($data[$i][4]);
?>
<tr><td >
&nbsp;<?php print $message ?>
<br />
<div align=right><?php print dateForm($data[$i][3])?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php print $nomprofp2 ?></div>
</td>
</tr>
<tr><td><hr width=97%></td></tr>
<?php
}
?>
</table>
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
if (LAN == "oui") {
?>
<script>
var idactu="KO";
var triadeactu="KO"; 
var triadeactutest="KO"; 
var messageactu=""; 
</script>
<script src='https://support.triade-educ.org/support/triade-actu.php'></script>
<script>
function triadeactufnc(val) {
	document.getElementById('triadeactu').style.display="none";
	setCookie("TRIADE-ACTU",val,15);
}

var dev="<?php print DEV ?>";
var idactulocal=getCookie("TRIADE-ACTU");
if ((idactulocal != idactu) || (dev == "1")) {
	if ((triadeactu == '1') || ((triadeactutest == dev) && (triadeactutest == '1'))) { 
	    if (messageactu != "") {

document.write("<style>");
document.write(".cadre_central { border: 1px solid #DAD9D9;border-radius: 5px 5px 5px 5px;color: #333333;width:97%;padding:10px;padding-top:20px;padding-bottom:30px; vertical-align: top; background-color: #F9F9F9; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); vertical-align: top;  } ");
document.write(".shadow { text-shadow: 2px 4px 3px rgba(0, 0, 0, 0.3); } ");
document.write(".font1 { font-family: 'Electrolize',cursive; color:#544F93; font-size: 16px; } ");
document.write("</style>");

document.write("<br><br><div class='cadre_central' id='triadeactu' style='background-color:#FFFFFF;'  >");
document.write("<table border='0' width='100%'><tr><td width='50%'><font class='font1 shadow' ><b>TRIADE-ACTU</b></font></td>");
document.write("<td align='right'><a href='#' onClick='triadeactufnc(idactu)' ><img src='./image/closeb.gif' border='0' ></a>&nbsp;&nbsp;</td></tr></table>");
document.write("<br/>");
document.write(messageactu);
document.write("</div>");
		}
	}
}
</script>

<?php } ?>

<!----------------------------------------->
<br /><br />
     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
     <tr  id='coulBar0'>
     <td height="2"> <b><font  id='menumodule1' ><?php print LANGTITRE2?></font></b></td>
     </tr>
     <tr id="cadreCentral0" >
<td valign=top>
<!-- // fin  -->
<?php
$recupMessAdmin=consultMessAdmin();
if (count($recupMessAdmin)) { $nbbordure='1'; }else{ $nbbordure='0'; }
?>
<TABLE width='100%'  bgcolor="#FFFFFF" bordercolor="#000000" border='<?php print $nbbordure ?>' >
<?php
for($i=0;$i<count($recupMessAdmin);$i++) {
	$imgfilm=$recupMessAdmin[$i][7];
	//id,nom,prenom,date,heure,titre,texte
	$text1=$recupMessAdmin[$i][6];
	$titre1=stripslashes($recupMessAdmin[$i][5]);
	$heure1=timeForm($recupMessAdmin[$i][4]);
	$date1=dateForm($recupMessAdmin[$i][3]);
	$prenom1=$recupMessAdmin[$i][2];
	$nom1=$recupMessAdmin[$i][1];
	$id1=$recupMessAdmin[$i][0];
	if (trim($titre1) == "") {
		$titre1="<i>sans objet</i>";
	}
	if ($imgfilm == "video") {
		$imgfilm="<img src='./image/commun/video-icon.png' border='0' align='center' width='25' />&nbsp;";
	}else{
		$imgfilm="<img src='./image/commun/newspaper.png' border='0' align='center' width='25' />&nbsp;&nbsp;";
	}
?>

<TR><TD bordercolor="#FFFFFF" id="bordure" valign=top height=1% > <?php print $imgfilm ?><a href="#" onclick="displayMessage('messageAccueil.php?id=<?php print $id1 ?>');return false;" title="Cliquez ici" ><b><?php print $titre1?></b></a></TD>
<TD width=25% align=left  id="bordure" bordercolor="#FFFFFF" > <U><?php print LANGTE2?></u> : <i><?php print $date1?></i> - <i><?php print $heure1?></i></TD></TR>
<?php } ?>
</TABLE>

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
     	   if ($_SESSION["id_suppleant"] > 0) {
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



<script type="text/javascript">
messageObj = new DHTML_modalMessage();	// We only create one object of this class
messageObj.setShadowOffset(5);	// Large shadow
function displayMessage(url) {
	messageObj.setSource(url);
	messageObj.setCssClassMessageBox(false);
	<?php if ($navigateur == "IE") { ?>
		messageObj.setSize(700,400);
	<?php }else{ ?>
		messageObj.setSize(700,'100%');
	<?php } ?>
	messageObj.setShadowDivVisible(true);	// Enable shadow for these boxes
	messageObj.display();
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
</BODY></HTML>
<?php
// fin_prog($debut);
Pgclose();
include_once("./librairie_php/finbody.php"); 
include_once("librairie_php/lib_verif_nav.php");
$navigateur=verif_navigateur();
file_get_contents("https://www.triade-educ.org/accueil/infoTriade.php");
?>
