<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.class.php,v 1.4 2017-06-30 14:08:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/demandes_actions.class.php");

class statut{
	
	public $id_element = 0;
	public $champ_entree = "";
	public $champ_sortie = "";
	public $display="";
	public $idobjet = 0;
	
	public function __construct($id_elt,$fieldElt){
		global $quoifaire;
		
		$this->id_element = $id_elt;
		$format_affichage = explode('/',$fieldElt);
		$this->champ_entree = $format_affichage[0];
		if($format_affichage[1]) $this->champ_sortie = $format_affichage[1];		
		$ids = explode("_",$id_elt);
		$this->idobjet = $ids[1];
		
		switch($quoifaire){
			
			case 'edit':
				$this->make_display();
				break;
			case 'save':
				$this->update();
				break;
		}
	}
	
	public function make_display(){
		global $msg, $dbh,$charset;
		
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
		global $dbh, $statut;		
		
		$req = "update demandes_actions set statut_action='".$statut."' where id_action='".$this->idobjet."'";
		pmb_mysql_query($req,$dbh);
		
		
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
?>