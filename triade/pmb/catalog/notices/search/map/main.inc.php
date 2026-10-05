<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2021/03/17 13:32:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sh;

// recherche notice (catalogage) : page de switch recherche cartes

// on commence par créer le champs de sélection de document
// récupération des types de documents utilisés.
require_once($class_path."/searcher.class.php");
require_once($class_path."/notice.class.php");

notice::init_globals_patterns_links();

$sh=new searcher_map("./catalog.php?categ=search&mode=11",true);