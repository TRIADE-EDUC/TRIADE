<?php
include("../common/lib_admin.php");
include("../common/lib_ecole.php");
if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT']))
{
// Internet Explorer
$navigateur="Internet Explorer";
$ok="oui";
}
elseif (preg_match('/opera/i', $_SERVER['HTTP_USER_AGENT']))
{
// Opera
$navigateur="Opera";
$ok="non";
}
elseif (preg_match('/Mozilla\/4./i', $_SERVER['HTTP_USER_AGENT']))
{
// Netscape 4.x
$navigateur="Netscape 4.x";
$ok="non";
}
elseif (preg_match('/Mozilla\/5.0/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Konqueror/i', $_SERVER['HTTP_USER_AGENT']))
{
// Netscape 6
$navigateur="Netscape 6.x";
$ok="oui";
}
elseif (preg_match('/Mozilla\/5.0/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Konqueror/i', $_SERVER['HTTP_USER_AGENT']))
{
// Netscape 7
$navigateur="Netscape 7.x";
$ok="oui";
}
else
{
// Autres navigateurs
$navigateur="Navigateur inconnu";
$ok="non";
}


// test la version du navigateur exclu netscape//
print "<script language=JavaScript>\n";
print "var navigateur='".$ok."';\n";
print "if  (navigateur == 'non') {   alert(\" Pour information, votre navigateur n'a pas été testé sous Triade.\\n\\n Si vous rencontrez des problèmes, n'hésitez pas à nous contacter via notre site http://www.triade-educ.com \\n de même si tout se passe bien, confirmer nous aussi, afin de valider le navigateur dans la configuration de Triade. \\n\\n Merci de votre compréhension, \\n\\n L'Equipe Triade \\n \") } \n";
print "</script>\n";
?>
