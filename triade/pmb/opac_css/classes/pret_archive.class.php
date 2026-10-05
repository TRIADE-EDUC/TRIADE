<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pret_archive.class.php,v 1.1 2023/07/26 14:47:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class pret_archive {
	
	protected $arc_id;
	
	protected $arc_debut;
	
	protected $arc_fin;
	
	protected $arc_id_empr;
	
	protected $arc_empr_cp;
	
	protected $arc_empr_ville;
	
	protected $arc_empr_prof;
	
	protected $arc_empr_year;
	
	protected $arc_empr_categ;
	
	protected $arc_empr_codestat;
	
	protected $arc_empr_sexe;
	
	protected $arc_empr_statut;
	
	protected $arc_empr_location;
	
	protected $arc_type_abt;
	
	protected $arc_expl_typdoc;
	
	protected $arc_expl_cote;
	
	protected $arc_expl_statut;
	
	protected $arc_expl_location;
	
	protected $arc_expl_location_origine;
	
	protected $arc_expl_location_retour;
	
	protected $arc_expl_codestat;
	
	protected $arc_expl_owner;
	
	protected $arc_expl_section;
	
	protected $arc_expl_id;
	
	protected $arc_expl_notice;
	
	protected $arc_expl_bulletin;
	
	protected $arc_groupe;
	
	protected $arc_niveau_relance;
	
	protected $arc_date_relance;
	
	protected $arc_printed;
	
	protected $arc_cpt_prolongation;
	
	protected $arc_short_loan_flag;
	
	protected $arc_pnb_flag;
	
	public $id_empr;
	public $id_expl;
	
	public function __construct($arc_id= 0) {
		$this->arc_id = intval($arc_id);
		$this->fetch_data();
	}

	protected function fetch_data() {
		if($this->arc_id) {
			$query = "SELECT * FROM pret_archive WHERE arc_id =".$this->arc_id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->arc_debut = $row->arc_debut;
			$this->arc_fin = $row->arc_fin;
			$this->arc_id_empr = $row->arc_id_empr;
			$this->arc_empr_cp = $row->arc_empr_cp;
			$this->arc_empr_ville = $row->arc_empr_ville;
			$this->arc_empr_prof = $row->arc_empr_prof;
			$this->arc_empr_year = $row->arc_empr_year;
			$this->arc_empr_categ = $row->arc_empr_categ;
			$this->arc_empr_codestat = $row->arc_empr_codestat;
			$this->arc_empr_sexe = $row->arc_empr_sexe;
			$this->arc_empr_statut = $row->arc_empr_statut;
			$this->arc_empr_location = $row->arc_empr_location;
			$this->arc_type_abt = $row->arc_type_abt;
			$this->arc_expl_typdoc = $row->arc_expl_typdoc;
			$this->arc_expl_cote = $row->arc_expl_cote;
			$this->arc_expl_statut = $row->arc_expl_statut;
			$this->arc_expl_location = $row->arc_expl_location;
			$this->arc_expl_location_origine = $row->arc_expl_location_origine;
			$this->arc_expl_location_retour = $row->arc_expl_location_retour;
			$this->arc_expl_codestat = $row->arc_expl_codestat;
			$this->arc_expl_owner = $row->arc_expl_owner;
			$this->arc_expl_section = $row->arc_expl_section;
			$this->arc_expl_id = $row->arc_expl_id;
			$this->arc_expl_notice = $row->arc_expl_notice;
			$this->arc_expl_bulletin = $row->arc_expl_bulletin;
			$this->arc_groupe = $row->arc_groupe;
			$this->arc_niveau_relance = $row->arc_niveau_relance;
			$this->arc_date_relance = $row->arc_date_relance;
			$this->arc_printed = $row->arc_printed;
			$this->arc_cpt_prolongation = $row->arc_cpt_prolongation;
			$this->arc_short_loan_flag = $row->arc_short_loan_flag;
			$this->arc_pnb_flag = $row->arc_pnb_flag;
			
			$this->id_empr = $row->arc_id_empr;
			$this->id_expl = $row->arc_expl_id;
		}
	}

	public function get_arc_debut() {
		return $this->arc_debut;
	}
	
	public function get_arc_fin() {
		return $this->arc_fin;
	}
	
	public function get_arc_id_empr() {
		return $this->arc_id_empr;
	}
	
	public function get_arc_empr_cp() {
		return $this->arc_empr_cp;
	}
	
	public function get_arc_empr_ville() {
		return $this->arc_empr_ville;
	}
	
	public function get_arc_empr_prof() {
		return $this->arc_empr_prof;
	}
	
	public function get_arc_empr_year() {
		return $this->arc_empr_year;
	}
	
	public function get_arc_empr_categ() {
		return $this->arc_empr_categ;
	}
	
	public function get_arc_empr_codestat() {
		return $this->arc_empr_codestat;
	}
	
	public function get_arc_empr_sexe() {
		return $this->arc_empr_sexe;
	}
	
	public function get_arc_empr_statut() {
		return $this->arc_empr_statut;
	}
	
	public function get_arc_empr_location() {
		return $this->arc_empr_location;
	}
	
	public function get_arc_type_abt() {
		return $this->arc_type_abt;
	}
	
	public function get_arc_expl_typdoc() {
		return $this->arc_expl_typdoc;
	}
	
	public function get_arc_expl_cote() {
		return $this->arc_expl_cote;
	}
	
	public function get_arc_expl_statut() {
		return $this->arc_expl_statut;
	}
	
	public function get_arc_expl_location() {
		return $this->arc_expl_location;
	}
	
	public function get_arc_expl_location_origine() {
		return $this->arc_expl_location_origine;
	}
	
	public function get_arc_expl_location_retour() {
		return $this->arc_expl_location_retour;
	}
	
	public function get_arc_expl_codestat() {
		return $this->arc_expl_codestat;
	}
	
	public function get_arc_expl_owner() {
		return $this->arc_expl_owner;
	}
	
	public function get_arc_expl_section() {
		return $this->arc_expl_section;
	}
	
	public function get_arc_expl_id() {
		return $this->arc_expl_id;
	}
	
	public function get_arc_expl_notice() {
		return $this->arc_expl_notice;
	}
	
	public function get_arc_expl_bulletin() {
		return $this->arc_expl_bulletin;
	}
	
	public function get_arc_groupe() {
		return $this->arc_groupe;
	}
	
	public function get_arc_niveau_relance() {
		return $this->arc_niveau_relance;
	}
	
	public function get_arc_date_relance() {
		return $this->arc_date_relance;
	}
	
	public function get_arc_printed() {
		return $this->arc_printed;
	}
	
	public function get_arc_cpt_prolongation() {
		return $this->arc_cpt_prolongation;
	}
	
	public function get_arc_short_loan_flag() {
		return $this->arc_short_loan_flag;
	}
	
	public function get_arc_pnb_flag() {
		return $this->arc_pnb_flag;
	}
	
	public function get_exemplaire() {
		if(!isset($this->exemplaire)) {
			$this->exemplaire = new exemplaire('', $this->arc_expl_id);
		}
		return $this->exemplaire;
	}
	
	public function get_emprunteur() {
		if(!isset($this->emprunteur)) {
			$this->emprunteur = new emprunteur($this->arc_id_empr);
		}
		return $this->emprunteur;
	}
	
	public static function get_last_return_date($arc_expl_id=0) {
		$arc_expl_id = intval($arc_expl_id);
		$query = "SELECT arc_fin FROM pret_archive WHERE arc_expl_id =".$arc_expl_id." ORDER BY arc_fin DESC LIMIT 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			return $row->arc_fin;
		}
		return '';
	}
	
	public static function get_formatted_last_return_date($arc_expl_id=0) {
		$last_return_date = static::get_last_return_date($arc_expl_id);
		return formatdate($last_return_date);
	}
	
	public static function extendLoan($arc_id, $date_retour){
	    global $msg;
	    
	    $query = "SELECT arc_cpt_prolongation FROM pret_archive WHERE arc_id = $arc_id";
	    $r = pmb_mysql_query($query);
	    if (!pmb_mysql_num_rows($r)){
	        return ["status"=>false, "message"=>$msg["pnb_extend_loan_fail"], "infos"=>"infos"];
	    }
	    
	    $result = pmb_mysql_fetch_array($r);
	    $new_cpt_prolongation = intval($result['arc_cpt_prolongation']) + 1;
	    
	    $query = "UPDATE pret_archive SET arc_fin = '$date_retour', arc_cpt_prolongation = $new_cpt_prolongation WHERE arc_id = $arc_id";
	    $result = pmb_mysql_query($query);
	    
	    return true;
	}
}