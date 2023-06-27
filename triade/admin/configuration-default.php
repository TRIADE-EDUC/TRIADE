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
//error_reporting(0);

include_once("./librairie_php/edit_fichier.php");
include_once("../common/config.inc.php");

$repecole = REPECOLE;
$repadmin = REPADMIN;

@copy("./default/config2.inc.php","../common/config2.inc.php");



$ficsource="../librairie_css/css.css-15";
$ficdest="../librairie_css/css.css";
if (file_exists($ficsource)) {
	@unlink("../librairie_css/css.css");
	copy($ficsource,$ficdest);
}

banniere_edit("./librairie_js/menudepart.js","aucun");
banniere_edit("../librairie_js/menudepart.js","aucun");
banniere_edit("../librairie_js/menuadmin.js","aucun");
banniere_edit("../librairie_js/menuparent.js","aucun");
banniere_edit("../librairie_js/menuprof.js","aucun");
banniere_edit("../librairie_js/menuscolaire.js","aucun");

@unlink("../data/image_banniere/banniere000.jpg");
@unlink("../data/image_banniere/banniere000.png");
@unlink("../data/image_banniere/banniere000.gif");
@unlink("../common/config-messenger.php");


// -----------------------------------------------------------------------------------//
$texte="<?php\n";
$texte.="define(\"MAILBLACKLIST\",\"non\");\n";
$texte.="define(\"MAILMESSSYS\",\"non\");\n";
$texte.="define(\"MAILMESSINTER\",\"non\");\n";
$texte.="?>\n";

$fp=fopen("../common/config4.inc.php","w");
fwrite($fp,"$texte");
fclose($fp);
// -----------------------------------------------------------------------------------//
$text = '';
$text.= 'ErrorDocument 404 /'.$repecole.'/err404.php'."\n";
$text.= 'ErrorDocument 403 /'.$repecole.'/err403.php'."\n";

$fp=fopen("../.htaccess","w");
fwrite($fp,$text);
fclose($fp);
	
//------------------------------------------------------------------
$text2 = '<?php'."\n";
$text2.= 'define("MAXUPLOAD","non");'."\n";
$text2.= '?>'."\n";

$fp=fopen("../common/config6.inc.php","w");
fwrite($fp,"$text2");
fclose($fp);
//-------------------------------------------------------------------
//------------------------------------------------------------------
$fp=fopen("../common/config5.inc.php","w");
$text3 = '<?php'."\n";
$text3.= 'define("CHARSET","iso-8859-1");'."\n";
$text3.= '?>'."\n";
fwrite($fp,"$text3");
fclose($fp);
//-------------------------------------------------------------------
//
//
//

header("Location:configuration3.php");

?>
