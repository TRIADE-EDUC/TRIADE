<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thesaurus.tpl.php,v 1.6 2023/08/03 12:34:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour la gestion des thesaurus

global $msg, $thes_browser, $thes_js_form;

// $thes_browser : template du browser de thesaurus
$thes_browser = "
";


// $thes_js_form : template JS du form de thesaurus
$thes_js_form = "
";
