<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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


include_once("choixlangue.php");  // fichier sur la langue a utiliser
include_once("./common/lib_ecole.php");   // nom du repertoire de l'ecole


// --------------------------------------
// debut pour le français
// --------------------------------------
if ( LANGUE == "french" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/languefrmenu-depart.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/languefrfunction-depart.js"></script>
<?php
include_once("langue-depart-fr.php");
}
//--------------------------------------
//--------------------------------------


// --------------------------------------
// debut pour l'anglais
// --------------------------------------
if ( LANGUE == "anglais" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langueenmenu-depart.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langueenfunction-depart.js"></script>
<?php
include_once("langue-depart-en.php");
}
//--------------------------------------
//--------------------------------------



// --------------------------------------
// debut pour l'espagnol
// --------------------------------------
if ( LANGUE == "espagnol" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langueesmenu-depart.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langueesfunction-depart.js"></script>
<?php
include_once("langue-depart-es.php");
}
//--------------------------------------
//--------------------------------------

// --------------------------------------
// debut pour le breton
// --------------------------------------
if ( LANGUE == "breton" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/languebretmenu-depart.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/languebretfunction-depart.js"></script>
<?php
include_once("langue-depart-es.php");
}
//--------------------------------------
//--------------------------------------
//
// --------------------------------------
// debut pour l'arabe
// --------------------------------------
if ( LANGUE == "arabe" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/languearabemenu-depart.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/languearabefunction-depart.js"></script>
<?php
include_once("langue-depart-arabe.php");
}
//--------------------------------------
//--------------------------------------
//
// --------------------------------------
// debut pour l'italien
// --------------------------------------
if ( LANGUE == "italien" ) {
?>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langueitmenu-depart.js"></script>
<script type="text/javascript" src="/<?php print REPECOLE?>/librairie_js/langueitfunction-depart.js"></script>
<?php
include_once("langue-depart-it.php");
}
?>
