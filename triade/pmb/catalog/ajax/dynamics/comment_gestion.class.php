<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comment_gestion.class.php,v 1.3 2017-04-26 10:20:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class comment_gestion{
	
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
		global $msg, $charset,$dbh;

		$req="SELECT * from perio_relance where rel_id=".$this->idobjet."";	
		$res= pmb_mysql_query($req);
		$act = pmb_mysql_fetch_object($res);
		
		$display ="";
		$submit = "<input type='submit' class='bouton' name='soumission' id='soumission' value='".$msg['demandes_valid_progression']."'/>";
		switch($this->champ_entree){			
			case 'text':
				$display = "<form method='post' name='edition'><textarea  id='save_".$this->id_element."' name='save_".$this->id_element."' cols='50' rows='6' wrap='virtual'>".htmlentities($act->rel_comment_gestion,ENT_QUOTES,$charset)."</textarea> $submit</form>
				<script type='text/javascript' >document.forms['edition'].elements['save_".$this->id_element."'].focus();</script>";
				break;
			default:
				$display = "<label id='".$this->id_element."' />".htmlentities($act->rel_comment_gestion,ENT_QUOTES,$charset)."</label>";
				break;
		}
		$this->display = $display;
	}
	
	public function update(){
		
		global $dbh, $comment_gestion;		
		
		$req = "update perio_relance set rel_comment_gestion='".$comment_gestion."' where rel_id='".$this->idobjet."'";
		pmb_mysql_query($req,$dbh);
		
		switch($this->champ_sortie){
			default :				
				$this->display = $comment_gestion;			
			break;
		}
	
	}
}
?>