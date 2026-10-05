<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demande_dynamic_field.class.php,v 1.1.4.2 2025/05/06 15:04:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class demande_dynamic_field {
	
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
		if (!empty($format_affichage[1])) {
		    $this->champ_sortie = $format_affichage[1];		
		}
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
	
	protected function get_query_display() {
	    return "";
	}
	
	protected function get_value($row) {
	    return '';
	}
	
	public function make_display(){
		global $msg, $charset;
		
		$query = $this->get_query_display();
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		
		$display ="";
		$submit = "<input type='submit' class='bouton' name='soumission' id='soumission' value='".$msg['demandes_valid_progression']."'/>";
		switch($this->champ_entree){			
			case 'text':
			    $display = "<form method='post'><input type='text' class='saisie-5em' id='save_".$this->id_element."' name='save_".$this->id_element."' value='".htmlentities($this->get_value($row), ENT_QUOTES, $charset)."' />$submit</form>";
				break;
			case 'img';
                $display = "<form method='post'><input type='text' class='saisie-5em' id='save_".$this->id_element."' name='save_".$this->id_element."' value='".htmlentities($this->get_value($row),ENT_QUOTES,$charset)."' />$submit</form>";
    			break;
			default:
				$display = "<label id='".$this->id_element."' />".htmlentities($this->get_value($row),ENT_QUOTES,$charset)."</label>";
				break;
		}
		$this->display = $display;
	}
	
	public function update(){
		$query = "";
		pmb_mysql_query($query);
		
		switch($this->champ_sortie){
			default :
                break;
		}
	}
}