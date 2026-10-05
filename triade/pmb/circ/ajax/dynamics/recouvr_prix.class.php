<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recouvr_prix.class.php,v 1.4 2021/04/23 08:32:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class recouvr_prix{
	
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
		if(!empty($format_affichage[1])) $this->champ_sortie = $format_affichage[1];		
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
		global $msg, $charset;

		$rqt="select montant, recouvr_type from recouvrements where recouvr_id='".$this->idobjet."'";
		$res = pmb_mysql_query($rqt);
		$act = pmb_mysql_fetch_object($res);
		
		$display ="";
		$submit = "<input type='submit' class='bouton' name='soumission' id='soumission' value='".$msg['demandes_valid_progression']."'/>";
		switch($this->champ_entree){			
			case 'text':
				$display = "<form method='post' name='edition'><input type='text' class='saisie-5em' id='save_".$this->id_element."' name='save_".$this->id_element."' value='".htmlentities($act->montant,ENT_QUOTES,$charset)."' />$submit</form>
				<script type='text/javascript' >document.forms['edition'].elements['save_".$this->id_element."'].focus();</script>";
				break;
			default:
				$display = "<label id='".$this->id_element."' />".htmlentities($act->montant,ENT_QUOTES,$charset)."</label>";
				break;
		}
		$this->display = $display;
	}
	
	public function update(){
		global $recouvr_prix, $pmb_gestion_devise;		
		
		$req = "update recouvrements set montant='".$recouvr_prix."' where recouvr_id='".$this->idobjet."'";
		pmb_mysql_query($req);
		if(!$recouvr_prix)$recouvr_prix="0.00";
		switch($this->champ_sortie){
			default :
				if(strpos($recouvr_prix,$pmb_gestion_devise) !== false)
					$this->display = $recouvr_prix;
				else $this->display = $recouvr_prix." ".$pmb_gestion_devise;
			break;
		}
	
	}
}
?>