<?php
error_reporting(0);
session_start();
include_once("./common/config2.inc.php");
if (!isset($_POST["passwd"]) && (SUPPPASSMAIL != "oui") ) {
	session_set_cookie_params(0);
	$_SESSION=array();
	session_unset();
	session_destroy();
	session_start();
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
<?php 
if (!isset($_POST["passwd"]) && (SUPPPASSMAIL != "oui") ) {
?>
	<HTML>
	<HEAD>
	<META http-equiv="CacheControl" content = "no-cache">
	<META http-equiv="pragma" content = "no-cache">
	<META http-equiv="expires" content = -1>
	<meta name="Copyright" content="Triade©, 2001">
	<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
	<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./FCKeditor/editor/css/fck_editorarea.css">
	<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
	<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
	<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
	<script language="JavaScript" src="./librairie_js/function.js"></script>
	<LINK REL="SHORTCUT ICON" href="./favicon.ico">
	<title>TRIADE - Messagerie</title>
	</head>
	<body>
<?php
	include_once("./common/lib_admin.php");
	include_once("./common/lib_ecole.php");
	include_once("./librairie_php/langue.php");
	include_once("./librairie_php/lib_error.php");
	include_once("./common/config2.inc.php");
	include_once("./librairie_php/timezone.php");
?>
	<script language="JavaScript"> var contenu=""; </script>
	<?php
	$efface="";
	if (isset($_GET["erreur"])) {
		$efface="onclick='document.getElementById(\"erreur\").style.visibility=\"hidden\"' ";
	?>
	<script language="JavaScript">
	var 	strTitre="<?php  print LANGacce_dep1  ?>";
	var 	strIcone="./image/commun/stop.jpg";
	var 	texte="<?php print "Vérifier vos identifiants de connexion, si le problème persiste avertisser votre administrateur Triade."  ?>";
	var	contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
		contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
		contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
		contenu += '</tr>';
		if ( strTitre != "" ){
			contenu += '<tr style="height: 30px;">';
			contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
			contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
			contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
			contenu +=   '<b>' + strTitre + '</b>';
			contenu +=  '</td>';
			contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
			contenu += '</tr>';
		}
		contenu +=  '<tr> ';
		contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div style="overflow:auto; width: 300px;">' + texte + '</div></td>';
		contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '</tr>';
		contenu +=  '<tr style="height: 10px;">';
		contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
		contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
		contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
		contenu +=  '</tr>';
		contenu += '</table>';
	</script>
		<?php
		}

		if (isset($_GET["id"])) { $id=$_GET["id"]; }
		if (isset($_POST["id"])) { $id=$_POST["id"]; }
		if (isset($_GET["idp"])) { $idp=$_GET["idp"]; }
		if (isset($_POST["idp"])) { $idp=$_POST["idp"]; }
		?>
		<br><br><br>
		<center>
		<form method='post' action='consult.php' >
		<table width=300><tr><td><img src="./image/commun/logo_triade_licence.gif"></td></tr></table>
		<br><br>
		<table border=1 bordercolor="#000000" cellPadding="0" cellSpacing="0"><tr><td  bordercolor="#FCE4BA" >
		<table bordercolor="#000000" border="0" id='bodyfond2' height="100" width="300" cellPadding="0" cellSpacing="0" >
		<tr><td align="center">Pour consulter votre message,<br /> veuillez indiquer votre mot de passe</td></tr>
		<td align="center" valign="center" ><input type=password name="passwd" size=20 class=bouton2  <?php print $efface ?> ><br />
		<div name="erreur"  id="erreur" style="POSITION:absolute;z-index:2"><script>document.write(contenu);</script></div>
		<br /></td></tr>
		</table></td></tr></table>
		<br><br>
		<table align=center><tr><td>
		<script language=JavaScript>buttonMagicSubmit("Valider","create"); //text,nomInput</script>
		</td></tr></table>
		<br><br> Version : <b><?php print VERSION ?></b>
		<input type=hidden name="id" value="<?php print $id ?>" >
		<input type=hidden name="idpers" value="<?php print $idp ?>" >
		</form>
		</center>
		<div align='center'><?php print LANGPIEDPAGE ?></div></td></tr></table></div>
<?php
}else {
		include_once("./common/version.php");
		include_once("./common/lib_admin.php");
		include_once("./common/lib_ecole.php");
		include_once("./common/config.inc.php");
		include_once("./librairie_php/timezone.php");
		include_once("./librairie_php/langue.php");
		include_once("./librairie_php/lib_error.php");
		include_once("./librairie_php/mactu.php");

		include_once('./librairie_php/db_triade.php');
		$cnx=cnx();

		$passwd=$_POST["passwd"];
		
		if (SUPPPASSMAIL == "oui") {
			$idmess=$_GET["id"];
			$idpers=$_GET["idpers"];
		}else{
			$idmess=$_POST["id"];
			$idpers=$_POST["idpers"];
		}
		if (verifmessage($idmess,$idpers)) {
			$passvrai=verifpasswd($passwd,$idmess,$idpers);
			$cpwd=cryptage($passwd);
			if (($cpwd == $passvrai) || (SUPPPASSMAIL == "oui")) {
				valide_message_lu_number($idmess,$idpers);
				$data=affichage_messagerie_message_via_mail($idmess);
				// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest,idforward_mail,repertoire,idmess_envoyer,idpiecejointe,idgroupe
				if ((trim($data[0][7]) == "ADM")||(trim($data[0][7]) == "ENS")||(trim($data[0][7]) == "MVS")||(trim($data[0][7]) == "TUT") ) {
					$emetteur=recherche_personne($data[0][1]);
					$_SESSION["nomEmetteur"]=recherche_personne_nom($data[0][1],$data[0][7]);
					$_SESSION["prenomEmetteur"]=recherche_personne_prenom($data[0][1],$data[0][7]);
					$_SESSION["id_pers"]=$idpers;
				}else {
					$emetteur=recherche_eleve($data[0][1]);
					$nomeleve=recherche_eleve_nom($id_eleve);
					$prenomeleve=recherche_eleve_prenom($id_eleve);
					$_SESSION["nomEmetteur"]="$nomeleve";
					$_SESSION["prenomEmetteur"]="$prenomeleve";
					$_SESSION["id_pers"]=$idpers;

				}

				if (count($data) > 0 ) {
					$number=$data[0][10];
		?>
					<HTML>
					<HEAD>
					<META http-equiv="CacheControl" content = "no-cache">
					<META http-equiv="pragma" content = "no-cache">
					<META http-equiv="expires" content = -1>
					<meta name="Copyright" content="Triade©, 2001">
					<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
					<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./FCKeditor/editor/css/fck_editorarea.css">
					<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
					<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
					<script language="JavaScript" src="./librairie_js/function.js"></script>
					<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
					<LINK REL="SHORTCUT ICON" href="./favicon.ico">
					<title>TRIADE - Messagerie</title>
					</head>
					<body>
					<table width="100%" height=100% border="0" bordercolor="#000000">
					<tr valign="top" bgcolor="#FFFFFF">
	    				<td height="117" >
      					<table width="100%" border="1" bgcolor="#CCCCCC">
        				<tr><td  bgcolor="#FFFFFF" height="16">
	            			<div style="float:left"><?php print ucwords(LANGTE3)?> :
					<?php print $emetteur; 	?>
					</div>
          				</td>
          				<td  bgcolor="#FFFFFF" height="16">
				        <div align="center"><?php print dateForm($data[0][4])?> - <?php print $data[0][5]?></div>
        	  			</td></tr>
        				<tr bgcolor="#FFFFFF">
          				<td height="19" valign=top><div align="left" ><?php print LANGTE5?> : <?php print stripslashes($data[0][8])?></div>
					</td>
					</td>
		       		   	<td height="19" width="18%"><div align="center"><a href='messagerie_reponse_via_mail.php?saisie_id_message=<?php print $data[0][0]?>' ><img src="./image/repondre.gif" align="absmiddle" alt="Répondre" border=0></A>
&nbsp;&nbsp;<a href='#' onclick="imprimer();"><img src="./image/print.gif" align="absmiddle" alt="Imprimer" border=0></A></div></td></tr>
				        </table><br>
					<table  width='100%'  border='0' bordercolor='#000000' ><tr><td>
					<?php
					$tabficJ=fichierJointExiste($data[0][13]); // md5,nom
					if (count($tabficJ) > 0) {
					        $listingdll="<font class=T1>";
					        for($j=0;$j<count($tabficJ);$j++) {
					                $nom=$tabficJ[$j][1];
					                $md5=$tabficJ[$j][0];
					                $listingdll.=" - <a href=\'accessfichier.php?id=$md5\' target=\'_blank\'>".$nom."</a><br />";
					        }
					        $listingdll.="</font>";
				        ?>
				        <i>Pièce(s) Jointe(s) :</i> <a href="#" onclick="AffBulleAvecQuit('Liste des fichiers disponibles','image/commun/info.jpg','<?php print $listingdll ?>'); return false;" ><img src="image/commun/download.png"  border='0' /></a>
					<?php
					}
					?>
					</td></tr></table><br>
					<table  align=center width=97% border=0>
					<?php
					$message=Decrypte($data[0][3],$number);
					$message=stripslashes($message);
					$message=preg_replace('#\"#','',$message);
					$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
					$message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);
					$message=stripslashes($message);
					?>
					<tr><td><div style=''><?php print $message ?></div></td></tr></table>
					<br><br><br>
					<hr>
					<div align='center'><?php top_p();?><br /><br /><?php print LANGPIEDPAGE ?> </div></td></tr></table>
					</td></tr>
					</table>
				<?php
					$destinataire=chercheIdEleve(strtolower(addslashes($_SESSION["nomEmetteur"])),addslashes($_SESSION["prenomEmetteur"]));
					if ($destinataire == $data[0][2]) {
						lecture_message($data[0][0]);
					}
				}else {
					print "<font size=2><center><b>".LANGMESS34."</b></center></font>";
					print "</body></html>";
					exit;
				}
				Pgclose();
			}else {
				$info=$_POST["passwd"];
				acceslog("Erreur de connexion pour la consultation de message via Email (mdp $info) ");
				print "<script type=\"text/javascript\">location.href='consult.php?idp=".$idpers."&id=".$idmess."&erreur=1';</script>";
			}
		}else{
			print "<font size=2><center><b>".LANGMESS34."</b></center></font>";
		
		}
	}
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</body>
</html>
