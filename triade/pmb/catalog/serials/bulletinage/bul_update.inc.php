<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_update.inc.php,v 1.60 2022/06/09 08:51:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $bul_id, $serial_id, $gestion_acces_active, $gestion_acces_user_notice, $pmb_synchro_rdf, $PMBuserid;
global $serial_header, $current_module, $id_form;

require_once($class_path."/authperso_notice.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/vedette/vedette_link.class.php");
require_once($class_path."/notice_relations.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path."/indexation_stack.class.php");

if($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}

require_once($class_path."/index_concept.class.php");
$entities_bulletin_controller = new entities_bulletinage_controller($bul_id);

if (!empty($serial_id)) {
    $entities_bulletin_controller->set_serial_id($serial_id);
}

if($entities_bulletin_controller->has_rights()) {
    //verification des droits de modification notice
    $acces_m=1;
    if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
    	$dom_1= $ac->setDomain(1);
    	$acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
    }
    
    if ($acces_m==0) {
    	
    	if (!$bul_id) {
    		error_message('', htmlentities($dom_1->getComment('mod_seri_error'), ENT_QUOTES, $charset), 1, '');
    	} else {
    		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
    	}
    		
    } else {
    
        // mise a jour de l'entete de page
        echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['catalog_serie_modif_bull'], $serial_header);
        
        if($pmb_synchro_rdf){
            require_once($class_path."/synchro_rdf.class.php");
        }
        
        $saved = $entities_bulletin_controller->proceed_update();
        if($saved) {
            print $entities_bulletin_controller->get_display_view($saved);
        } else {
            // echec de la requete
            error_message('', $msg[281], 1, "./catalog.php");
        }
    }
}