<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_func.inc.php,v 1.9 2016/05/09 10:13:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// form de saisie cb expl
function get_cb_expl($title, $title_form, $form_action) {
	global $expl_cb_caddie_tmpl;
	global $expl_script;
	global $idcaddie;
	$expl_cb_caddie_tmpl = str_replace("!!script!!", $expl_script, $expl_cb_caddie_tmpl);
	$expl_cb_caddie_tmpl = str_replace("!!titre_formulaire!!", $title_form, $expl_cb_caddie_tmpl);
	$expl_cb_caddie_tmpl = str_replace("!!form_action!!", $form_action, $expl_cb_caddie_tmpl);
	$expl_cb_caddie_tmpl = str_replace("!!title!!", $title, $expl_cb_caddie_tmpl);
	$expl_cb_caddie_tmpl = str_replace("!!message!!", "", $expl_cb_caddie_tmpl);
	$expl_cb_caddie_tmpl = str_replace("!!idcaddie!!", "$idcaddie",$expl_cb_caddie_tmpl );
	return $expl_cb_caddie_tmpl;
}
