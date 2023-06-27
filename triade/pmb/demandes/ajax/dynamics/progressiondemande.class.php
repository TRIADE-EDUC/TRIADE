<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: progressiondemande.class.php,v 1.5 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class progressiondemande{
	
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
		
	}
	
	public function make_display(){
		global $msg, $dbh,$charset;
		
		$rqt = "select progression from demandes where id_demande='".$this->idobjet."'";
		$res = pmb_mysql_query($rqt,$dbh);
		$act = pmb_mysql_fetch_object($res);
		
		$display ="";
		$submit = "<input type='submit' class='bouton' name='soumission' id='soumission' value='".$msg['demandes_valid_progression']."'/>";
		switch($this->champ_entree){			
			case 'text':
				$display = "<form method='post'><input type='text' class='saisie-5em' id='save_".$this->id_element."' name='save_".$this->id_element."' value='".htmlentities($act->progression,ENT_QUOTES,$charset)."' />$submit</form>";
				break;
			case 'img';
				$display = "<form method='post'><input type='text' class='saisie-5em' id='save_".$this->id_element."' name='save_".$this->id_element."' value='".htmlentities($act->progression,ENT_QUOTES,$charset)."' />$submit</form>";
				break;
			default:
				$display = "<label id='".$this->id_element."' />".htmlentities($act->progression,ENT_QUOTES,$charset)."</label>";
				break;
		}		
		$this->display = $display;
	}
	
	public function update(){
		
		global $dbh, $progressiondemande;		
		
		$req = "update demandes set progression='".$progressiondemande."' where id_demande='".$this->idobjet."'";
		pmb_mysql_query($req,$dbh);
		
		switch($this->champ_sortie){
			case 'img':
				$this->display = "<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$progressiondemande."%\" title='".$progressiondemande."%' />";
				break;
			default:
				$this->display = $progressiondemande."%";
				break;
		}
				
	}
}
?>