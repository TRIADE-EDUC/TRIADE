<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2011 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
//
$c_lang = "";
$lang_file = "";
if (defined("_LANG")) $c_lang = _LANG;
if (isset($lang))
  if ($lang != '') $c_lang = $lang; // pour changement de langue dans l'interface admin.
//
if ($c_lang == '') $c_lang = 'EN';
//
if ($c_lang == 'EN') $lang_file = "lang/english-iso-8859-1.inc.php";
if ($c_lang == 'FR') $lang_file = "lang/french-iso-8859-1.inc.php";
if ($c_lang == 'BR') $lang_file = "lang/brazilian-iso-8859-1.inc.php";
if ($c_lang == 'PT') $lang_file = "lang/portuguese-iso-8859-1.inc.php";
if ($c_lang == 'IT') $lang_file = "lang/italian-iso-8859-1.inc.php";
if ($c_lang == 'RO') $lang_file = "lang/romana-iso-8859-1.inc.php";
if ($c_lang == 'DE') $lang_file = "lang/german-iso-8859-1.inc.php";
if ($c_lang == 'ES') $lang_file = "lang/spanish-iso-8859-1.inc.php";
if ($c_lang == 'NL') $lang_file = "lang/nederlands-iso-8859-1.inc.php";
//
if (is_readable($lang_file)) 
{
	require($lang_file);
}
else
{
	require("lang/english-iso-8859-1.inc.php");
	$c_lang = 'EN';
}
?>