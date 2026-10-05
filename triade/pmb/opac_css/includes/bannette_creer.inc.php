<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_creer.inc.php,v 1.38 2023/08/02 06:21:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $opac_allow_bannette_priv, $id_empr, $instance_equation;
global $enregistrer, $nom_bannette;

global $serialized_search, $segment_json_search;

if (!$opac_allow_bannette_priv) die ("Accès interdit") ; 

require_once($class_path."/search.class.php");
require_once($class_path."/bannette.class.php");
require_once($class_path."/equation.class.php");
require_once($base_path."/includes/bannette_func.inc.php");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;

print "<div id='aut_details' class='aut_details_bannette'>\n";

if (isset($enregistrer) && $enregistrer==1 && !$nom_bannette) $enregistrer = 2 ;

$bannette = new bannette();
print common::format_title($msg['dsi_bt_bannette_priv']);
$s = new search() ;
if ($serialized_search) {
    $equation = $serialized_search;
}

if ((!isset($enregistrer) || !$enregistrer) && !$serialized_search) {
	$s->unhistorize_search();
	$s->strip_slashes();
	$equation = $s->serialize_search();
} else {
	$equation = stripslashes($equation);
	$s->unserialize_search($equation);
}
// on arrive de la rech multi-critères
$equ_human = $s->make_serialized_human_query($equation);

if ($equation) {
	if (isset($enregistrer) && $enregistrer=='1') {
		if(!isset($instance_equation)) {
			$instance_equation = new equation();
		}
		$instance_equation->set_properties_from_form();
		$instance_equation->save();

		$bannette->set_properties_from_form();
		$bannette->save();

		$rqt_bannette_equation = "INSERT INTO bannette_equation (num_bannette, num_equation) VALUES (".$bannette->id_bannette.", $instance_equation->id_equation)" ;
		pmb_mysql_query($rqt_bannette_equation);
		// mise à jour de l'instance bannette_equations de classe bannette
		$bannette->set_bannette_equations();
		
		$rqt_bannette_abon = "INSERT INTO bannette_abon (num_bannette, num_empr, actif) VALUES (".$bannette->id_bannette.", $id_empr, 0)" ;
		pmb_mysql_query($rqt_bannette_abon);

		// bannette créée, on supprime le bouton des rech multicritères
		$_SESSION['abon_cree_bannette_priv'] = 0 ;
		print "<br />" ;
		print str_replace("!!nom_bannette!!", $bannette->nom_bannette, $msg['dsi_bannette_creer_resultat']);
		print "<br /><br />" ;
		// pour construction correcte du mail de diffusion
		$liens_opac = array() ;
		$bannette->vider();
		print pmb_bidi($bannette->remplir());
		$bannette->diffuser();
	} else {
		print $equ_human;
		print "<br /><br />".$bannette->get_short_form($equation);
	}
} else {
	// y'a un binz, pas d'équation...
}

print "</div><!-- fermeture #aut_details -->\n";	
?>