<?php
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
$fichier = "../data/install_log/install.inc";
if (file_exists($fichier)) {
	header("Location: index.php?inst=1");
	exit;
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
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet"
		      href="librairie/css.css" />

		<title>Triade Installation</title>

		<script type="text/javascript" src="../librairie_js/info-bulle.js"></script>

	</head>

	<body>

	<!-- "text-align: center" à cause du bug centrage d'IE :( -->
	<div style="text-align: center;">

		<div id="mainInst2">

			<img src="./image/logo_triade_licence.gif" alt="logo_triade_licence" />

<?php




	$repok = 1;
	$droitok = 0;
	$chekbox = 0;

	function get_php_setting($val) {
		$r =  (ini_get($val) == '1' ? 1 : 0);
		return $r ? 'ON' : 'OFF';
	}

	print '			<!-- foutu IE : le div est là à cause du bug centrage d\'IE -->'."\n";
	print '			<div style="text-align: center;">'."\n";
	print '				<table style="text-align: left; border: 0px; margin-top: 1em; margin-bottom: 2em; margin-left: auto; margin-right: auto; width: 60%;" >'."\n";
	print '					<tr>'."\n";
	print '						<td>'."\n";
	print '							<img src="image/on1.gif" alt="on1" style="width: 8px; height: 8px" />'."\n";
	print '							<span class="T1">Version Php <= 7.0&nbsp;:</span>'."\n";
	print '						</td>'."\n";

	print '						<td>'."\n";

	if (phpversion() <= '7.0') {
		$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
		$txt.= '	<span style="color: red; font-weight: bold;">V</span>ersion ';
		$txt.= '	Php suppérieur à 5 pour une optimisation complète de Triade';
		$txt.= '</span>';
		$txt = htmlspecialchars($txt);

		print '	 <a href="#" '.
			'onmouseover="AffBulle2(\'INFORMATION\',\'../image/commun/info.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" '.
			'onmouseout="HideBulle()">'."\n";
		print '							<img src="./image/stat3.gif" alt="stat2" style="border: 0;" /></a>'."\n";
	}
	else{
		print '							<img src="./image/stat1.gif" alt="stat1" style="border: 0;" />'."\n";
	}

	print '						</td>'."\n";
	print '					</tr>'."\n";




	// verification si ecriture dans un repertoire
	if (is_writable("../common/")) {
		$droitok=1;
	}

	print '	<tr>'."\n";
	print '		<td>'."\n";
	print '			<img src="image/on1.gif" alt="on1" style="width: 8px; height: 8px" />'."\n";
	print '			<span class="T1">Permission d\'&eacute;criture : </span>'."\n";
	print '		</td>'."\n";

	print '		<td>'."\n";

	if ($droitok == 1) {
		print '<img src="./image/stat1.gif" alt="stat1" />'."\n";
	}
	else{
		$disable='disabled="disabled"';
		
		$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
		$txt.= '	<span style="color: red; font-weight: bold;">T</span>ous les ';
		$txt.= '	fichiers Triade</u> doivent etre en acc&egrave;s &eacute;criture. Modifier ';
		$txt.= '	les droits de l\\\'aborescence Triade pour etre conforme &agrave; son ';
		$txt.= '	utilisation.';
		$txt.= '</span>';

		$txt = htmlspecialchars($txt);

		print '			<a href="#" '.
			'onmouseover="AffBulle2(\'ATTENTION\',\'../image/commun/warning.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" '.
			'onmouseout="HideBulle()">'."\n";
		print '				<img src="./image/stat3.gif" alt="stat3" style="border: 0;" />'."\n";
		print '			</a>'."\n";
	}

	print '		</td>'."\n";
	print '	</tr>'."\n";


	print '	<tr>'."\n";
	print '		<td>'."\n";
	print '			<img src="image/on1.gif" alt="on1" style="width: 8px; height: 8px" />'."\n";
	print '			<span class="T1">Support extension Mysql&nbsp;: </span>'."\n";
	print '		</td>'."\n";

	print '		<td>'."\n";

	if (function_exists( 'mysqli_connect' )) {
		print '			<img src="./image/stat1.gif" alt="stat1" />'."\n";
	}
	else{
		$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
		$txt.= '	<span style="color: red; font-weight: bold;">L</span>\\\'extension ';
		$txt.= '	<b>MySql</b> doit etre charg&eacute; pour pouvoir utiliser une base ';
		$txt.= '	de donn&eacute;e MySql.';
		$txt.= '</span>';

		$txt = htmlspecialchars($txt);

		print '			<a href="#" '.
			'onmouseover="AffBulle2(\'Information\',\'../image/commun/info.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" '.
			'onmouseout="HideBulle()">'."\n";
		print '				<img src="./image/stat3.gif" alt="stat0" style="border: 0;" />'."\n";
		print '			</a>'."\n";
	}

	print '		</td>'."\n";
	print '	</tr>'."\n";

	

	include_once("../librairie_php/lib_get_init.php");
	/*
	print '	<tr>'."\n";
	print '		<td>'."\n";
	print '			<img src="image/on1.gif" alt="on1" style="width: 8px; height: 8px" />'."\n";
	print '			<span class="T1">Support extension dbase&nbsp;: </span>'."\n";
	print '		</td>'."\n";


	print '		<td>'."\n";

	if (php_module_load("dbase") != 1) {
		$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
		$txt.= '	<span style="color: red; font-weight: bold;">L</span>\\\'extension ';
		$txt.= '	<b>dbase</b> doit être chargé pour pouvoir importer une base GEP.';
		$txt.= '</span>';

		$txt = htmlspecialchars($txt);

		print '			<a href="#" '.
			'onmouseover="AffBulle2(\'Information\',\'../image/commun/info.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" '.
			'onmouseout="HideBulle()">'."\n";
		print '				<img src="./image/stat3.gif" alt="stat0" style="border: 0;" />'."\n";
		print '			</a>'."\n";
	}
	else{
		print '<img src="./image/stat1.gif" alt="stat1" />'."\n";
	}

	print '		</td>'."\n";
	print '	</tr>'."\n";
	 */
	print '	<tr>'."\n";
	print '		<td>'."\n";
	print '			<img src="image/on1.gif" alt="on1" width="8" height="8" />'."\n";
	print '			<span class="T1">Support extension gd&nbsp;: </span>'."\n";
	print '		</td>'."\n";

	print '		<td>'."\n";

	if (php_module_load("gd") != 1) {
		$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
		$txt.= '	<span style="color: red; font-weight: bold">L</span>\\\'extension ';
		$txt.= '	<b>gd</b> doit etre charg&eacute; pour pouvoir visualiser les ';
		$txt.= '	graphiques des élèves.';
		$txt.= '</span>';

		$txt = htmlspecialchars($txt);

		print '			<a href="#" '.
			'onmouseover="AffBulle2(\'Information\',\'../image/commun/info.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" '.
			'onmouseout="HideBulle()">'."\n";
		print '				<img src="./image/stat3.gif" alt="stat0" style="border: 0;" />'."\n";
		print '		</a>'."\n";
	}
	else{
		print '<img src="./image/stat1.gif" alt="stat1" />'."\n";
	}

	print '		</td>'."\n";
	print '	</tr>'."\n";




	print '	<tr>'."\n";
	print '		<td>'."\n";
	print '			<img src="image/on1.gif" alt="on1" style="width: 8px; height: 8px" />'."\n";
	print '			<span class="T1">Support extension SQLite : </span>'."\n";
	print '		</td>'."\n";

	print '		<td>'."\n";

	if (php_module_load( 'sqlite3' )) {
		print '			<img src="./image/stat1.gif" alt="stat1" />'."\n";
	}
	else{
		$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
		$txt.= '	<span style="color: red; font-weight: bold;">L</span>\\\'extension ';
		$txt.= '	<b>SQLite</b> doit etre charg&eacute; pour pouvoir utiliser le module ';
		$txt.= '	d\\\'archivage de Triade.';
		$txt.= '</span>';

		$txt = htmlspecialchars($txt);

		print '			<a href="#" '.
			'onmouseover="AffBulle2(\'Information\',\'../image/commun/info.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" '.
			'onmouseout="HideBulle()">'."\n";
		print '				<img src="./image/stat3.gif" alt="stat0" style="border: 0;" />'."\n";
		print '			</a>'."\n";
	}

	print '		</td>'."\n";
	print '	</tr>'."\n";

	print '	<tr>'."\n";
	print '		<td colspan="2"><hr /></td>'."\n";
	print '	</tr>'."\n";


	$php_recommended_settings = array(array ('Safe Mode','safe_mode','OFF'),
		array ('Display Errors','display_errors','ON'),
		array ('File Uploads','file_uploads','ON'),
		array ('Magic Quotes GPC','magic_quotes_gpc','OFF'),
		array ('Magic Quotes Runtime','magic_quotes_runtime','OFF'),
		array ('Register Globals','register_globals','OFF'),
		array ('Output Buffering','output_buffering','OFF'),
		array ('Session auto start','session.auto_start','OFF'),
	);

	foreach ($php_recommended_settings as $phprec) {
?>

	<tr>
		<td>
			<img src="image/on1.gif" alt="on1" style="width: 8px; height: 8px" />
			<span class="T1"><?php echo $phprec[0]; ?>&nbsp;: </span>
		</td>
		<td>

<?php	if ( get_php_setting($phprec[1]) == $phprec[2] ) {	?>
		<img src="./image/stat1.gif" alt="stat1" />

<?php
	}
	else {
		if ($phprec[0] == "Register Globals") {
			$disable = 'disabled="disabled"';
			$chekbox = "1";

			$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
			$txt.= '	<span style="color: red; font-weight: bold;">C</span>ette ';
			$txt.= '	variable doit etre à OFF pour des questions de s&eacute;curit&eacute;.';
			$txt.= '</span>';

			$txt = htmlspecialchars($txt);

			print '<a href="#" onmouseover="AffBulle2(\'ATTENTION\',\'../image/commun/warning.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" onmouseout="HideBulle()">'."\n";
			print '<img src="./image/stat3.gif" alt="stat3" style="border: 0;" />'."\n";
			print '</a>'."\n";
		}
		else{
			$txt = '<span style="font-family: Verdana; font-size: 0.9em;">';
			$txt.= '	<span style="color: red; font-weight: bold;">R</span>ecommand&eacute; a '.$phprec[2];
			$txt.= '</span>';

			$txt = htmlspecialchars($txt);

			print '<a href="#" onmouseover="AffBulle2(\'Recommand&eacute;\',\'../image/commun/info.jpg\',\''.$txt.'\'); window.status=\'\'; return true;" onmouseout="HideBulle()">'."\n";
			print '	<img src="./image/stat0.gif" alt="stat0" style="border: 0;" />'."\n";
			print '</a>'."\n";
		}
	}

?>

	</td>
</tr>

<?php

	}

	if ($chekbox == "1") {
		print '<tr><td colspan="2" style="padding-top:2em;">'."\n";
		print '<input type="checkbox" onclick="document.getElementById(\'form\').val.disabled=false; this.disabled=true;" />'."\n";
		print '<span class="T1">'."\n";
		print '	<b>Forcer l\'installation.</b><br />'."\n";
		print '	<i>Je prends en compte les remarques de l\'installation.</i>'."\n";
		print '</span>'."\n";
		print '</td></tr>'."\n";
	}
?>

				</table>
				</div>
				<form action="suite.php" id="form" >
					<div style="text-align: right; padding-right: 100px; margin-bottom: 1em;" >
						<input type="submit" name="val"
						       onclick="this.value='Patientez S.V.P.';"
						       value=" Suivant --> "
						       class="BUTTON" <?php print $disable; ?> />
					</div>
				</form>
	</div>
	</div>

	<?php include_once("librairie/pied_page.php");?>

	<script type="text/javascript">
		InitBulle("#000000","#FCE4BA","red",1);
	</script>

	</body>

</html>
