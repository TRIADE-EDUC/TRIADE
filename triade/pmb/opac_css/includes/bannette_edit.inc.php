<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_edit.inc.php,v 1.8 2023/08/02 06:21:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg, $charset;
global $opac_allow_bannette_priv, $id_empr, $id_bannette;
global $enregistrer, $bannette_diffuse_checked, $nom_bannette, $search;

$id_bannette = intval($id_bannette);

require_once($class_path."/search.class.php");
require_once($class_path."/bannette.class.php");
require_once($class_path."/equation.class.php");
require_once($base_path."/includes/bannette_func.inc.php");

if (!$opac_allow_bannette_priv || !bannette::has_rights($id_bannette)) die ("Acc&egrave;s interdit");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;

print "<div id='aut_details' class='aut_details_bannette'>\n";

if (isset($enregistrer) && $enregistrer==1 && !$nom_bannette) $enregistrer = 2 ;

$bannette = new bannette($id_bannette);
print common::format_title($msg['dsi_bannette_edit']);
//Instantiation d'une classe recherche
$search_class=new search();
if(isset($search) && $search) {
	$search_class->unhistorize_search();
	$search_class->strip_slashes();
	$equation = $search_class->serialize_search();
} else {
	$bannette_equations = new bannette_equations($bannette->id_bannette);
	$equations = $bannette_equations->get_equations();
	$instance_equation = new equation($equations[0]);
	if (!isset($enregistrer) || !$enregistrer) {
		$equation = $instance_equation->requete;
	} else {
		$equation = stripslashes($equation);
// 		$search_class->unserialize_search($equation);
	}
}

$search_class->unserialize_search($equation);
$equ_human = $search_class->make_serialized_human_query($equation);
if ($equation) {
	if (isset($enregistrer) && $enregistrer=='1') {
		if(!isset($instance_equation)) {
			$instance_equation = new equation();
		}
		$instance_equation->set_properties_from_form();
		$instance_equation->save();

		$bannette->set_properties_from_form();
		$bannette->save();
		
		$query = 'SELECT * FROM bannette_equation WHERE num_equation = '.$instance_equation->id_equation;
		$result = pmb_mysql_query($query);
		if (!pmb_mysql_num_rows($result)) {
		    $rqt_bannette_equation = "INSERT INTO bannette_equation (num_bannette, num_equation) VALUES (".$bannette->id_bannette.", $instance_equation->id_equation)";
		    pmb_mysql_query($rqt_bannette_equation);
		    
		    $bannette->set_bannette_equations();
		    
		    $bannette->vider();
		    $bannette->remplir();
		} else {
		    // mise à jour de l'instance bannette_equations de classe bannette
		    $bannette->set_bannette_equations();
		    
		    $bannette->vider();
		    $bannette->remplir();
		}

		// bannette modifiée, on supprime le bouton des rech multicritères
		$_SESSION['abon_edit_bannette_priv'] = 0 ;
		print "<br />" ;
		print str_replace("!!nom_bannette!!", $bannette->nom_bannette, $msg['dsi_bannette_saved']);
		print "<br /><br />" ;
		if (!empty($bannette_diffuse_checked)) {
		    $bannette->diffuser();
		}
		// pour construction correcte du mail de diffusion
		$liens_opac = array() ;
	} else {
	    print '<div id="equation_container">';
		print $equ_human;
		$search_class->unserialize_search($equation);
		print $search_class->make_hidden_search_form($base_path."/index.php?tab=dsi&bt_edit_bannette_priv=1&search_type_asked=extended_search&id_bannette=".$bannette->id_bannette,"bannette_search_form_".$bannette->id_bannette);
// 		$search_class->destroy_global_env();
		print "<a href=\"javascript:document.forms['bannette_search_form_".$bannette->id_bannette."'].submit();\" style='cursor : pointer'>";
		print "<img src='".get_url_icon('tag.png')."' alt='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' />";
		print "</a>";
		print "</div>";
		print "<br /><br />".$bannette->get_short_form($equation);
	}
} else {
    if (isset($enregistrer) && $enregistrer=='1') {         
        if(!isset($instance_equation)) {
            $instance_equation = new equation();
        }
        $instance_equation->set_properties_from_form();
        $instance_equation->save();
        
        $bannette->set_properties_from_form();
        $bannette->save();        
        $query = 'SELECT num_equation FROM bannette_equations WHERE num_equation = '.$instance_equation->id_equation. ' LIMIT 1';
        $result = pmb_mysql_query($query);
        
        if (!pmb_mysql_num_rows($result)) {
            $rqt_bannette_equation = "INSERT INTO bannette_equation (num_bannette, num_equation) VALUES (".$bannette->id_bannette.", $instance_equation->id_equation)";
            pmb_mysql_query($rqt_bannette_equation);
        }

        print "<br />";
        print str_replace("!!nom_bannette!!", $bannette->nom_bannette, $msg['dsi_bannette_saved']);
        print "<br /><br />".$msg['dsi_bannette_no_equation'];
        
    } else {
        print '<div id="equation_container">';
        print '<span>
            '.$msg['dsi_bannette_no_equation'].'
            </span>';
        print $search_class->make_hidden_search_form($base_path."/index.php?tab=dsi&bt_edit_bannette_priv=1&search_type_asked=extended_search&id_bannette=".$bannette->id_bannette,"bannette_search_form_".$bannette->id_bannette);
        print "<a href=\"javascript:document.forms['bannette_search_form_".$bannette->id_bannette."'].submit();\" style='cursor : pointer'>";
        print "<img src='".get_url_icon('tag.png')."' alt='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['edit'],ENT_QUOTES,$charset)."' />";
        print "</a>";
        print "</div>";
        print "<br /><br />".$bannette->get_short_form($equation);
    }
}

print "</div><!-- fermeture #aut_details -->\n";	
?>