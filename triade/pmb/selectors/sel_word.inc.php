<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_word.inc.php,v 1.9 2020/03/24 07:56:29 dgoron Exp $letter $mot

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");		

// contenu popup sélection de mot
require('./selectors/templates/sel_word.tpl.php');	

// la variable $caller, passée par l'URL, contient le nom du form appelant
$baseurl = "./select.php?what=synonyms&caller=$caller&p1=$p1&p2=$p2";

$selector_word = new selector_word(stripslashes($user_input));
$selector_word->proceed();

?>