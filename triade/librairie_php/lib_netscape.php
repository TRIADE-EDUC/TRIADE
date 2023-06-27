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

if (preg_match('/msie/i',$_SERVER['HTTP_USER_AGENT']) && !preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT'])) {
// Internet Explorer
$navigateur="Internet Explorer";
$ok=oui;
$info_nav="IE";
}
elseif (preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT']))
{
// Opera
$navigateur="Opera";
$ok=oui;
$info_nav="OP";
}
elseif (preg_match('/Mozilla\/4\./i', $_SERVER['HTTP_USER_AGENT']))
{
// Netscape 4.x
$navigateur="Netscape 4.x";
$ok=non;
$info_nav="MO";
}
elseif (preg_match('/Mozilla\/5\.0/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Konqueror/i', $_SERVER['HTTP_USER_AGENT']))
{
// Netscape 6
$navigateur="Netscape 6.x";
$ok=oui;
$info_nav="MO";
}
elseif (preg_match('/Mozilla\/5\.0/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Konqueror/i', $_SERVER['HTTP_USER_AGENT']))
{
// Netscape 7
$navigateur="Netscape 7.x";
$ok=oui;
$info_nav="MO";
}
else
{
// Autres navigateurs
$navigateur="Navigateur inconnu";
$ok=non;
$info_nav="??";
}


// test la version du navigateur exclu netscape//
print "<script type=\"text/javascript\">\n";
print "var navigateur='".$ok."';\n";
print "if  (navigateur == 'non') {   alert(\" Pour information, votre navigateur n'a pas été testé sous Triade.\\n\\n Si vous rencontrez des problèmes, n'hésitez à contacter via notre site http://www.triade-educ.org \\n  si tout se passe bien, avertissez-nous, afin de valider ce navigateur dans la configuration de Triade. \\n\\n Merci de votre compréhension, \\n\\n L'Equipe Triade \\n \") } \n";
print "</script>\n";
?>
