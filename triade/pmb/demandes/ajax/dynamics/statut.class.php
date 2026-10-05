<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.class.php,v 1.6.8.1 2025/05/06 15:04:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/demandes_actions.class.php");

class statut extends demande_dynamic_field{
	
    protected function get_query_display() {
        return "";
    }
    
	public function make_display(){
		global $msg,$charset;
		
		$display ="";
		$submit = "<input type='submit' class='bouton' name='soumission' id='soumission' value='".$msg['demandes_valid_progression']."'/>";
		$action = new demandes_actions($this->idobjet);
		switch($this->champ_entree){			
			case 'selector':
				$display = "
				<form method='post'>".$action->getStatutSelector($action->statut_action,true).$submit."</form>";
				break;
			default:
				$display = "<label id='".$this->id_element."' />".htmlentities($action->statut_action,ENT_QUOTES,$charset)."</label>";
				break;
		}
		$this->display = $display;
	}
	
	public function update(){		
		global $statut;		
		
		$req = "update demandes_actions set statut_action='".$statut."' where id_action='".$this->idobjet."'";
		pmb_mysql_query($req);
		$action = new demandes_actions($this->idobjet);
		$display = "";
		switch($this->champ_sortie){
			default:
				for($i=1;$i<count($action->list_statut)+1;$i++){
					if($action->list_statut[$i]['id'] == $statut){	
						$display =  $action->list_statut[$i]['comment'];
						break;
					}
				}
			break;
		}
		$this->display = $display;		
	}
}