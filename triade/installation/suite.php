<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
$fichier = "../data/install_log/install.inc";
if (file_exists($fichier)) {
	header('Location: ./index.php?inst=1');
	exit ;
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php define("CHARSET","iso-8859-1"); ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<script type="text/javascript" src="../librairie_js/info-bulle.js"></script>
		<link title="style" type="text/css" rel="stylesheet" href="librairie/css.css" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<title>Triade Installation</title>
	</head>

	<body>

<?php
	$messerror = "";

	if (isset($_GET["erreur"])) {
		$messerror = utf8_encode("<script type=\"text/javascript\">alert('Tous les champs doivent être remplis. ')</script>");
	}
?>

<?php  print $messerror; ?>

	<div style="text-align: center;">
		<div id="mainInst3">
			<img src="./image/logo_triade_licence.gif"
			     alt="logo triade licence" />
<!--
<tr>
<td  align=right><font class=T1> Choix de la langue :</font></td>
<td> <select name="choix_lang">
<option STYLE='color:#000066;background-color:#FCE4BA'  value="french" >Français</option>
</select>
</tr>
-->
		<br /><br />
		<form method="post" action="recup.php" id="form11" onsubmit="document.getElementById('form11').validation.disabled=true" >

			<div style="text-align: center;">
				<table style="text-align: left;
				              border: 0;
				              margin-right: auto;
				              margin-left: auto;
				              width: 100%">
					<tr>
						<td style="text-align: right;">
							<span class="T2">Choix de la base&nbsp;:</span>
						</td>
						<td>
						<select name="choix_base">
						<option style="color:#000066;background-color:#FCE4BA" value="mysql" selected="selected" >MySql</option>
						</select>
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="T2">Type du serveur&nbsp;:</span>
						</td>
						<td>
						<select name="typeserveur">
						<option style="color:#000066;background-color:#CCCCFF" value="">Choix</option>
						<optgroup label="Serveur D&eacute;di&eacute;">
						<option style="color:#000066;background-color:#FCE4BA" value="LINUX">Linux</option>
						<option style="color:#000066;background-color:#FCE4BA" value="LINUX">Unix</option>
						</optgroup>
						<optgroup label="Serveur Cl&eacute;s en mains">
                                                <option style="color:#000066;background-color:#FCE4BA" value="FREEEOS">Free-OS</option>
                                                </optgroup>
						<optgroup label="Serveur Windows">
						<option style="color:#000066;background-color:#FCE4BA" value="EASYPHP">Easyphp</option>
						<option style="color:#000066;background-color:#FCE4BA" value="WAMP310">WampServer</option>
<!--						<option style="color:#000066;background-color:#FCE4BA" value="IIS">Serveur IIS</option> -->
						</optgroup>
						<optgroup label="Serveur Mutualis&eacute;">
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURFREE">Serveur Free</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURKWARTZ">Serveur KWARTZ</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURAUTRENET">Serveur Autre.net</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEUROVH">Serveur OVH</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEUR1AND1">Serveur 1and1</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURAMEN">Serveur Amen</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURONLINE">Serveur Online.net</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURVIPDOMAINE">Serveur VIPDOMAINE</option>
						<option style="color:#000066;background-color:#FCE4BA" value="SERVEURMUTUA">autre...</option>
						</optgroup>
						</select>
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="T2">Host de la base&nbsp;:</span>
						</td>
						<td>
							<input type="text" name="hostbase" value="localhost" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="T2">Nom de la base&nbsp;:</span>
						</td>
						<td>
							<input type="text" name="nombase" />
							<?php
								$txt = '<span style="font-family: Verdana; font-size: 0.9em">';
								$txt.=  '<span style="color: red; font-weight: bold;">L</span>a ';
								$txt.=  'base de donnée <b>doit</b> être créée.';
								$txt.= '</span>';

								$txt = htmlspecialchars($txt);
							?>

							<a href="#" onmouseover="AffBulle2('ATTENTION','../image/commun/warning.jpg','<?php echo $txt; ?>'); window.status=''; return true;" onmouseout='HideBulle()'>
								<img src="image/help.gif" style="border: 0;" alt="" />
							</a>
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="T2">Login d'acc&egrave;s &agrave; la base&nbsp;:</span>
						</td>
						<td>
							<input type="text" name="login" />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="T2">Mot de passe d'acc&egrave;s la base&nbsp;:</span>
						</td>
						<td>
							<input type="text" name="password" />
						</td>
					</tr>
					<tr>
						<td colspan="2"><hr /></td>
					</tr>
					<tr>
						<td style="text-align: right;">
							<span class="T2">Nom du r&eacute;pertoire racine&nbsp;:</span>
						</td>
						<td>

<?php
	// pour récupérer le répertoire d'installation actuel
	// nécessite document_root de php

	
		$repecole = 'triade';
	
?>

							<input type="text" name="repecole" value="<?php echo $repecole; ?>" />
						</td>
					</tr>
				<!--	<tr>
						<td style="text-align: right;">
							<span class="T2">Nom du répertoire admin&nbsp;:</span>
						</td>
						<td>
							<input type="hidden" name="repadmin" value="admin" />
						</td>
					</tr> -->
					<tr>
						<td style="text-align: right;">
							<span class="T2">Pr&eacute;fixe des tables&nbsp;:</span>
						</td>
						<td>
							<input type="text" name="prefixe" value="tria_" />
						</td>
					</tr>

					<tr>
						<td  style="text-align: right;"> 
							<span class=T2> Type des tables&nbsp;:</span>
						</td>
						<td>
							<select name="typetable" >
								<option STYLE='color:#000066;background-color:#CCCCFF'  value="MYISAM">Par d&eacute;faut</option>
								<option STYLE='color:#000066;background-color:#FCE4BA'  value="MYISAM">MyISAM</option>
								<option STYLE='color:#000066;background-color:#FCE4BA'  value="InnoDB">InnoDB</option>
								<option STYLE='color:#000066;background-color:#FCE4BA'  value="BerkeleyDB">BerkeleyDB</option>
							</select>
						</td>
					</tr>

					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td colspan="2" style="text-align: center;">
							<input type="submit" onclick="this.value='Patientez S.V.P.';"  name="validation" value="Enregistrer" class="BUTTON" />
						</td>
					</tr>
				</table>
			</div>
		<input type="hidden" name="repadmin" value="admin" />
		</form>
		</div>
	</div>

<?php  include("librairie/pied_page.php")?>

		<script type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</script>

	</body>

</html>
