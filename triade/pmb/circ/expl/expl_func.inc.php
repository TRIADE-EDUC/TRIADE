<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_func.inc.php,v 1.13 2016-05-09 10:13:02 dgoron Exp $


if (stristr ( $_SERVER ['REQUEST_URI'], ".inc.php" ))
	die ( "no access" );
	
// fonctions pour la gestion des exemplaires pour le prêt


// récupération des templates
include ("$include_path/templates/expl.tpl.php");

// form de saisie cb expl
function get_cb_expl($title, $message, $title_form, $form_action,$type_form="") {

	print do_cb_expl($title, $message, $title_form, $form_action,$type_form="" );
}

function do_cb_expl($title, $message, $title_form, $form_action,$type_form="") {
	global $expl_cb_tmpl, $expl_cb_retour_tmpl, $expl_cb_retour_confirm_tmpl;
	global $expl_cb_tmpl_recep;
	global $expl_script;
	global $form_cb_expl;
	//	print "<h2>".strpos($form_action,'/circ.php?categ=retour')."</h2>";
	if ($form_action == './circ.php?categ=retour')
		$cb_tmpl = $expl_cb_retour_tmpl; 
	else {
		if ($type_form == 'recep')
			$cb_tmpl = $expl_cb_tmpl_recep; 
		else
			$cb_tmpl = $expl_cb_tmpl;
	}
	
	$cb_tmpl = str_replace ( "!!script!!", $expl_script, $cb_tmpl );
	$cb_tmpl = str_replace('!!expl_cb!!', $form_cb_expl, $cb_tmpl);
	$cb_tmpl = str_replace ( "!!titre_formulaire!!", $title_form, $cb_tmpl );
	$cb_tmpl = str_replace ( "!!form_action!!", $form_action, $cb_tmpl );
	
	if ($title)
		$cb_tmpl = str_replace ( "<h1>!!title!!</h1>", "<h1>" . $title . "</h1>", $cb_tmpl ); else
		$cb_tmpl = str_replace ( "<h1>!!title!!</h1>", "", $cb_tmpl );
	
	$cb_tmpl = str_replace ( "!!message!!", $message, $cb_tmpl );
	
	return $cb_tmpl;
}