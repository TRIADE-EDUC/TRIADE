<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_delete.inc.php,v 1.47 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

use Pmb\Digitalsignature\Models\DocnumCertifier;

global $class_path, $msg, $charset;
global $gestion_acces_active, $gestion_acces_user_notice, $pmb_confirm_delete_from_caddie, $caddie_confirmation;
global $pmb_archive_warehouse, $PMBuserid, $id;

require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/pnb/pnb.class.php");
require_once($class_path."/entity_locking.class.php");
require_once($class_path."/notice.class.php");
//verification des droits de modification notice
$acces_m=1;
if ($id!=0 && $gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$id,8);
}

if ($acces_m==0) {
	
	error_message('', htmlentities($dom_1->getComment('mod_noti_error'), ENT_QUOTES, $charset), 1, '');
	
} else {
	require_once($class_path."/parametres_perso.class.php");
	
	// suppression d'une notice
	print "<div class='row'><h1>{$msg[416]}</h1></div>";
	if($id) {
	    
	    $entity_locking = new entity_locking($id, TYPE_NOTICE);
	    if($entity_locking->is_locked()){
	        print $entity_locking->get_locked_form();
	    }else{
    	    $query = "select is_numeric from notices where notice_id=$id";
    	    $result = pmb_mysql_query($query);
    	    $is_numeric = pmb_mysql_result($result, 0, 0);
    	    	    
    		$query = "select count(1) as qte from exemplaires where expl_notice=$id";
    		$result = pmb_mysql_query($query);
    		$expl = pmb_mysql_result($result, 0, 0);
    		
    		$hasSignedDocnum = DocnumCertifier::hasSignedDocnumFromNoticeId($id);
    		
    		if($expl && !$is_numeric) {
    			// il y a des exemplaires : impossible de supprimer cette notice 
    			error_message($msg[416], $msg[420], 1, notice::get_permalink($id));
    		} elseif($hasSignedDocnum) {
    			// il y a des documents numerique signés : impossible de supprimer cette notice 
    			error_message($msg[416], $msg['digital_signature_error_delete_notice'], 1, notice::get_permalink($id));
    		} else {
    			$notice_relations = notice_relations_collection::get_object_instance($id);
    			$notc = $notice_relations->get_nb_childs();
    			if ($notc) {
    				error_message($msg[416], $msg["notice_parent_used"], 1, notice::get_permalink($id));
    			} else {
    				$query = "select count(1) from demandes where num_notice=$id";
    				$result = pmb_mysql_query($query);
    				$dmde = pmb_mysql_result($result, 0, 0); 
    				if($dmde) 
    					error_message($msg[416], $msg["notice_demande_used"], 1, notice::get_permalink($id));
    				else {
    					$abort_delete = 0;
    					$query = "select count(1) as qte, name from caddie_content, caddie where type='NOTI' and object_id='$id' and caddie_id=idcaddie group by name";
    					$result = pmb_mysql_query($query);
    					$caddie = @pmb_mysql_result($result, 0, 0);
    					// La notice est au moins dans un caddie
    					if ($caddie) {
    						$abort_delete = 1;
    						switch ($pmb_confirm_delete_from_caddie) {
    							case 0: //On interdit
    								$name = pmb_mysql_result($result, 0, 'name'); 
    								error_message($msg[416], $msg['suppr_notice_dans_caddie'].$name, 1, notice::get_permalink($id));							
    								break;
    							case 1: //
    								$abort_delete = 0;
    								break;
    							case 2:
    								if (isset($caddie_confirmation) && $caddie_confirmation) {
    									$abort_delete = 0;
    								}
    								else {
    									$name = pmb_mysql_result($result, 0, 'name');	
    									echo $msg['suppr_notice_dans_caddie_info'].$name."<br /><br />".$msg["confirm_suppr"]."?<br />";
    									echo '<input type="button" class="bouton" onClick="document.location = \'./catalog.php?categ=delete&id='.$id.'&caddie_confirmation=1\'" value="'.$msg['63'].'" />&nbsp;';
    									echo '<input type="button" class="bouton" onClick="history.go(-1)" value="'.$msg['76'].'" />';
    								}
    								break; 
    						}
    					} 
    					if (!$abort_delete){		// suppression de la notice
    						$ret_param="";
    						$notice_relations = notice_relations_collection::get_object_instance($id);
    						$first_parent = $notice_relations->get_first_parent();
    						if(is_object($first_parent) && $first_parent->get_linked_notice()) {
    							if ($first_parent->get_niveau_biblio() == 'm'|| $first_parent->get_niveau_biblio() == 'b') {
    								$ret_param="?categ=isbd&id=".$first_parent->get_linked_notice();
    							} elseif ($first_parent->get_niveau_biblio() == 's' || $first_parent->get_niveau_biblio() == 'a') {
    								$ret_param= "?categ=serials&sub=view&serial_id=".$first_parent->get_linked_notice();
    							}
    						}
    						
    						// On déclenche un événement sur la supression
    						$evt_handler = events_handler::get_instance();
    						$event = new event_entity("entity", "has_deletion_rights");
    						$event->set_entity_id($id);
    						$event->set_entity_type(TYPE_NOTICE);
    						$event->set_user_id($PMBuserid);
    						$evt_handler->send($event);
    						if($event->get_error_message()){
    							information_message($msg[416], $event->get_error_message(), 1, notice::get_permalink($id));
    						} else {
    							//archivage
    							if ($pmb_archive_warehouse) {
    								notice::save_to_agnostic_warehouse(array(0=>$id),$pmb_archive_warehouse);
    							}
    							
    							if ($is_numeric) {
    								pnb::delete_pnb_record_links($id);
    							}
    							notice::del_notice($id);
    							// affichage du message suppression en cours puis redirect vers page de catalogage
    							print "<div class=\"row\"><div class='msg-perio'>".$msg['suppression_en_cours']."</div></div>
    							<script type=\"text/javascript\">
    								document.location='./catalog.php".$ret_param."';
    							</script>";
    						}
    					}				
    				}
    			}	
    		}
	   }
	} else {
		error_message($msg[416], "{$msg[417]} : {$msg[418]}", 1, "./catalog.php");
	}

}
?>
