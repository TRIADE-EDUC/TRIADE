<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analysis_form.inc.php,v 1.3 2019/05/28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $id_sug, $id_analysis, $analysis_type_form;

require_once($include_path."/templates/serials.tpl.php");
require_once($class_path."/serials.class.php");
require_once($class_path."/suggestions.class.php");

$sug = new suggestions($id_sug);


$myAnalysis = new analysis($id_analysis);
if(!$myAnalysis->id){
	$myAnalysis->tit1 = $sug->titre;
	$myAnalysis->lien = $sug->url_suggestion;
	$myAnalysis->n_gen = $sug->commentaires;
	$myAnalysis->typdoc = "a";
}

$analysis_type_form = str_replace('!!id_sug!!',$sug->id_suggestion,$analysis_type_form);
print "<div class='row'>".$myAnalysis->analysis_form(true)."</div>";
	
?>