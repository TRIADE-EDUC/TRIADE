<?php

include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("choixlangue.php");  // fichier sur la langue a utiliser


// --------------------------------------
// debut pour le français
// --------------------------------------
if ( LANGUE == "french" ) {
?>
<script language=JavaScript src="/<?php print REPECOLE?>/<?php print REPADMIN?>/librairie_js/langue-menu-admin-fr.js"></script>
<script language=JavaScript src="/<?php print REPECOLE?>/<?php print REPADMIN?>/librairie_js/langue-function-admin-fr.js"></script>
<?php
include_once("langue-text-admin-fr.php");	
}
//--------------------------------------
//--------------------------------------


// --------------------------------------
// debut pour l'anglais
// --------------------------------------
else if ( LANGUE == "anglais" ) {
?>
<script language=JavaScript src="/<?php print REPECOLE?>/<?php print REPADMIN?>/librairie_js/langueangmenu.js"></script>
<?php
include_once("langue-text-ang.php");	
}else {
//--------------------------------------
?>
<script language=JavaScript src="/<?php print REPECOLE?>/<?php print REPADMIN?>/librairie_js/langue-menu-admin-fr.js"></script>
<script language=JavaScript src="/<?php print REPECOLE?>/<?php print REPADMIN?>/librairie_js/langue-function-admin-fr.js"></script>
<?php
include_once("langue-text-admin-fr.php");	
}
?>
