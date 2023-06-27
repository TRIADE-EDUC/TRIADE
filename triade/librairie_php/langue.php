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

include_once("./common/choixlangue.php");  // fichier sur la langue a utiliser
include_once("./common/lib_ecole.php");   // nom du repertoire de l'ecole
include_once("./common/version.php");
include_once('./librairie_pdf/php.arabe/Arabic.php');


// --------------------------------------
// debut pour le franais
// --------------------------------------
if ( LANGUE == "french" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-fr.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-fr.js"></script>
<script type="text/javascript" > var langv = '<?php print VERSIONLETTRE?>'; </script>
<script type="text/javascript" > var lang_lang = 'fr_FR'; </script>
<?php
include_once("langue-text-fr.php");
}
//--------------------------------------
//--------------------------------------

// --------------------------------------
// debut pour l'arabe
// --------------------------------------
if ( LANGUE == "arabe" ) {
?>

<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-arabe.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-arabe.js"></script>
<script type="text/javascript" > var lang_lang = 'ar'; </script>
<?php
include_once("langue-text-arabe.php");
}
// --------------------------------------
// debut pour l'italien
// --------------------------------------
if ( LANGUE == "italien" ) {
?>

<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-it.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-it.js"></script>
<script type="text/javascript" > var lang_lang = 'it'; </script>
<?php
include_once("langue-text-it.php");
}
// --------------------------------------
// debut pour l'anglais
// --------------------------------------
if ( LANGUE == "anglais" ) {
?>
<script type="text/javascript" > var langv = '<?php print VERSIONLETTRE?>'; </script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-en.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-en.js"></script>
<script type="text/javascript" > var lang_lang = 'en'; </script>
<?php
include_once("langue-text-en.php");
}
//--------------------------------------
//--------------------------------------


// --------------------------------------
// debut pour l'espagnol
// --------------------------------------
if ( LANGUE == "espagnol" ) {
?>
<script type="text/javascript" > var langv = '<?php print VERSIONLETTRE?>'; </script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-es.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-es.js"></script>
<script type="text/javascript" > var lang_lang = 'es'; </script>
<?php
include_once("langue-text-es.php");
}


// --------------------------------------
// debut pour breton
// --------------------------------------
if ( LANGUE == "breton" ) {
?>
<script type="text/javascript" > var langv = '<?php print VERSIONLETTRE?>'; </script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-bret.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-bret.js"></script>
<script type="text/javascript" > var lang_lang = 'fr_breton'; </script>	
<?php
include_once("langue-text-bret.php");
}
// --------------------------------------
// debut pour occitan
// --------------------------------------
if ( LANGUE == "occitan" ) {
?>
<script type="text/javascript" > var langv = '<?php print VERSIONLETTRE?>'; </script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-menu-oc.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langue-function-oc.js"></script>
<script type="text/javascript" > var lang_lang = 'fr_occitan'; </script>	
<?php
include_once("langue-text-oc.php");
}

//--------------------------------------
//--------------------------------------

?>
