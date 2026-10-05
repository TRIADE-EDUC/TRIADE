<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css-v4.css" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css-v4-2.css" />
		<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
	</head>

	<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

	<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
	<?php
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade.php");
	include_once("./common/config2.inc.php");
	$cnx=cnx();
	verifCnxIntraMsn();
	?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "TRIADE-MSN" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td><br>
	<!-- // fin  -->

<?php
if (file_exists("./common/config-messenger.php")) include_once("./common/config-messenger.php");
$validemodule=false;
if ( ( (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menuprof")) && (MESSENGERPERS == "oui")) || (($_SESSION["membre"] == "menueleve") && (MESSENGERELEV == "oui")) ) {
	$validemodule=true;

	if ($_GET["error"] == '1') {
		print "<div style='text-align:center;color:red;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin-bottom:8px;'>Renseigner votre email dans le module \"Votre compte\" !!</div>";
	}
	?>

	<div style="font-size:12px;color:#333;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:2px 0 12px;margin-left:10px;display:flex;align-items:flex-start;gap:8px;">
	  <img src="./image/commun/personne.gif" style="flex-shrink:0;margin-top:2px;">
	  <span>TRIADE vous propose un service MSN local, lié aux seuls utilisateurs TRIADE de votre établissement.<br>
	  Pour activer cet outil, vous devez valider votre compte.
	  Pour cela merci de valider ces informations :</span>
	</div>

	<?php
	$email=recupEmail($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
	$unique_id=recupUniqueIdMsnByIdpers($_SESSION["id_pers"], $_SESSION["membre"]);
	?>

	<script>
	function getRequete3() {
		if (window.XMLHttpRequest) {
			result = new XMLHttpRequest();
		} else {
			if (window.ActiveXObject) {
				result = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		return result;
	}

	function changeImage(img) {
		var edit_save = document.getElementById("photo");
		var newImage = document.getElementById('avatar').options[document.getElementById('avatar').selectedIndex].value;
		var index = document.getElementById('avatar').selectedIndex;
		if (newImage == "./image/commun/photo_vide.jpg") {
			// rien
		} else {
			if (index == 0) { newImage="./tchat/php/images/"+newImage; }
		}
		edit_save.src=newImage;
		var requete = getRequete3();
		var corps="nb="+encodeURIComponent(newImage)+"&id=<?php print $unique_id ?>";
		requete.open("POST","updateAvatarIntraMSN.php",true);
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		requete.send(corps);
	}
	</script>

	<?php
	if (($_SESSION["membre"] != "menueleve") && ($_SESSION["membre"] != "menuparent")) {
		$photoLocal=recherche_photo_pers($_SESSION["id_pers"]);
		$photo="./data/image_pers/$photoLocal";
		$img=$_SESSION["membre"]."_$photoLocal";
		copy($photo,"./tchat/php/images/$img");
		$photo=$img;
	} else {
		if ($_SESSION["membre"] == "menueleve") {
			$photoLocal=recherche_photo_eleve($_SESSION["id_pers"]);
			$photo="./data/image_eleve/$photoLocal";
			$img=$_SESSION["membre"]."_$photoLocal";
			copy($photo,"./tchat/php/images/$img");
			$photo=$img;
		}
	}
	$img=recupImgMsnByIdpers($_SESSION["id_pers"], $_SESSION["membre"]);
	if ((trim($photoLocal) == "") || ($img == "")) $photo="./image/commun/photo_vide.jpg";
	?>

	<div style="display:flex;align-items:flex-start;gap:20px;max-width:480px;margin-left:10px;">
	  <div style="flex:1;">
	  <form method='post' action='tchat/php/signup.php'>
	  <div class="na-card">
	    <div class="na-row">
	      <span class="na-lbl">Nom :</span>
	      <input type='text' value="<?php print stripslashes($_SESSION['nom']) ?>" name='fname' readonly class="cc-select">
	    </div>
	    <div class="na-row">
	      <span class="na-lbl">Prénom :</span>
	      <input type='text' value="<?php print stripslashes($_SESSION['prenom']) ?>" name='lname' readonly class="cc-select">
	    </div>
	    <div class="na-row">
	      <span class="na-lbl">Email :</span>
	      <input type='text' value='<?php print $email ?>' name='email' readonly required class="cc-select">
	    </div>
	    <div class="na-row">
	      <span class="na-lbl">Avatar :</span>
	      <select name='image' onChange="changeImage()" id='avatar' class="cc-select">
	        <option value="<?php print $photo ?>" STYLE='color:#000066;background-color:#CCCCFF'>Trombinoscope</option>
	        <?php
	        for($i=1;$i<=26;$i++) {
	            if ($i<10) $i="0$i";
	            $select="";
	            if ($img == "Memoji-$i.png") $select="selected='selected'";
	            print "<option value='./image/Memoji-$i.png' $select STYLE='color:#000066;background-color:#CCCCFF'>Avatar $i</option>";
	        }
	        ?>
	      </select>
	    </div>
	  </div>

	  <?php if (!verifCompteMsnByIdpers($_SESSION["id_pers"], $_SESSION["membre"])) { ?>
	  <br>
	  <script>buttonMagicSubmit('<?php print VALIDER ?>','okmsn','ok')</script>
	  <br>
	  <?php } else { ?>
	  <br>
	  <script language=JavaScript>buttonMagic("<?php print "Connexion TRIADE-MSN" ?>","./tchat/login.php","intra-msn","width=490,height=650","");</script>
	  <br>
	  <?php } ?>

	  <input type='hidden' value='aucun' name='password'>
	  </form>
	  </div>

	  <div style="flex-shrink:0;padding-top:14px;border-left:1px solid #c8d0e0;padding-left:16px;">
	  <?php if ($img != "") { ?>
	    <img src="./tchat/php/images/<?php print $img ?>" id='photo' width='90' height='90'
	         style="border-radius:50%;box-shadow:0 4px 14px rgba(8,10,102,0.35);display:block;">
	  <?php } else { ?>
	    <img src="image_trombi.php?idP=<?php print $_SESSION['id_pers'] ?>" id='photo' width='90' height='90'
	         style="border-radius:50%;box-shadow:0 4px 14px rgba(8,10,102,0.35);display:block;">
	  <?php } ?>
	  </div>
	</div>

	<?php if (isset($_GET["ok"])) { ?>
	<div style="font-size:12px;color:red;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 0;">
	  Votre compte est maintenant créé.
	</div>
	<?php } ?>

	<div style="font-size:11px;color:#333;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin-top:10px;margin-left:10px;">
	  Toute la communauté de votre établissement <i>(<u>et seulement votre établissement</u>)</i> sera alors disponible directement via TRIADE-MSN.
	</div>

<?php
}

if ($validemodule == false) {
	print "<div style='text-align:center;color:red;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;'>".LANGMESS37.".</div>";
}

print "</td></tr></table>";
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
	print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY></HTML>
