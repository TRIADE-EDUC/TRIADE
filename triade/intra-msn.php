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
		<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
	</head>

	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

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
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "TRIADE-MSN" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td ><br />
	<!-- // fin  -->

<?php
if (file_exists("./common/config-messenger.php")) include_once("./common/config-messenger.php");
$validemodule=false;
if ( ( (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menuprof")) && (MESSENGERPERS == "oui")) || (($_SESSION["membre"] == "menueleve") && (MESSENGERELEV == "oui")) ) { 
	$validemodule=true;
		
	if ($_GET["error"] == '1') { print "<center><font color=red class='T2' >Renseigner votre email dans le module \"Votre compte\" !! </font></center><br><br>"; }	

	?>
	<font class='T2'><img src="./image/commun/personne.gif" align='left'/> &nbsp;&nbsp; TRIADE vous propose un service MSN local, lié aux seuls utilisateurs TRIADE de votre établissement.
	&nbsp;&nbsp;Pour activer cet outil, vous devez valider votre compte.
	Pour cela merci de valider ces informations : <br><br>
	
	<?php
	$email=recupEmail($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
	?>
	
	<form method='post' action='tchat/php/signup.php' >	
	<table>
	<tr><td align='right'>Nom :</td><td><input type='text' value="<?php print $_SESSION['nom'] ?>" name='fname' readonly /></td></tr>
	<tr><td align='right'>Pr&eacute;nom : </td><td><input type='text' value="<?php print $_SESSION['prenom'] ?>" name='lname' readonly /></td></tr>
	<tr><td align='right'>Email : </td><td><input type='text' value='<?php print $email ?>' name='email' readonly required /></td></tr>

	<?php
	$unique_id=recupUniqueIdIntraMsn($email);
	if (($_SESSION["membre"] != "menueleve") && ($_SESSION["membre"] != "menuparent")) { 
		$photoLocal=recherche_photo_pers($_SESSION["id_pers"]);
	        $photo="./data/image_pers/$photoLocal";
		$img=$_SESSION["membre"]."_$photoLocal";
		copy($photo,"./tchat/php/images/$img");
		$photo=$img;
	}else{
		if ($_SESSION["membre"] == "menueleve") {
			$photoLocal=recherche_photo_eleve($_SESSION["id_pers"]);
        	        $photo="./data/image_eleve/$photoLocal";
               	 	$img=$_SESSION["membre"]."_$photoLocal";
	                copy($photo,"./tchat/php/images/$img");
        	        $photo=$img;
		}
	}
	$img=recupImgIntraMsn($email);
	if ((trim($photoLocal) == "") || ($img == ""))  $photo="./image/commun/photo_vide.jpg";
	?>

	<script>
	function getRequete3() {
	        if (window.XMLHttpRequest) {
                	result = new XMLHttpRequest();     // Firefox, Safari, ...        
		}else {
	              	if (window.ActiveXObject)  {
        	      		result = new ActiveXObject("Microsoft.XMLHTTP");    // Internet Explorer              
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
		}else{
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

	<tr><td align='right'>Avatar : </td><td>
	<select name='image' onChange="changeImage()" id='avatar' > 
	<option value="<?php print $photo ?>"  STYLE='color:#000066;background-color:#CCCCFF' >Trombinoscope</option>
	<?php
	for($i=1;$i<=26;$i++) {
		if ($i<10) $i="0$i";
		$select="";
		if ($img == "Memoji-$i.png") $select="selected='selected'" ;
		print "<option value='./image/Memoji-$i.png' $select STYLE='color:#000066;background-color:#CCCCFF' >Avatar $i</option>";
	} 
	?>
	</select></td></tr>		
	<tr><td colspan='2' height='20'></td></tr>
	<?php
	if (!verifCompteIntraMsn($email)) {
	?>
		<tr><td colspan='2'><script>buttonMagicSubmit('<?php print VALIDER ?>','okmsn','ok')</script></td></tr>
	<?php }else{ ?>
		<tr><td colspan='2' align='center'><table><tr><td><script language=JavaScript>buttonMagic("<?php print "Connexion TRIADE-MSN" ?>","./tchat/login.php","intra-msn","width=490,height=600","");</script></td></tr></table></td></tr>	
	<?php } ?>
	</table>
	<input type='hidden' value='aucun' name='password' />
	</form>
<?php if (isset($_GET["ok"])) print "<ul><font color=red >Votre compte est maintenant cr&eacute;&eacute;.</font></ul>"; ?>
	<ul>
	&nbsp;&nbsp;Toute la communauté de votre établissement <i>(<u>et seulement votre établissement</u>)</i> sera alors disponible directement via TRIADE-MSN 
	</ul>
	<div style='position:relative;top:-190px;left:330px' >
	<?php
	if ($img != "") { ?>
		<img src="./tchat/php/images/<?php print $img ?>" id='photo' width='90' height='90' />
	<?php }else{ ?>
		<img src="image_trombi.php?idP=<?php print $_SESSION['id_pers']?>" id='photo' width='90' height='90' />
	<?php } ?>
	</div>
	<?php
}

if ($validemodule == false) {
	print "<center><font color=red class='T2' >".LANGMESS37.".</font></center><br><br>";
}



print "</td></tr></table>";
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
     	print "</SCRIPT>";
      	top_d();
	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
	print "</SCRIPT>";
}
Pgclose();
?>
</BODY></HTML>
