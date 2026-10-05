<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transfert_demande.class.php,v 1.5 2021/12/23 13:46:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/expl.class.php');
require_once($class_path.'/transfert.class.php');

class transfert_demande {
	
	protected $id;
	
	protected $num_transfert;
	
	protected $date_creation;
	
	protected $formatted_date_creation;
	
	protected $sens_transfert;
	
	protected $num_location_source;
	
	protected $num_location_dest;
	
	protected $num_expl;
	
	protected $etat_demande;
	
	protected $date_visualisee;
	
	protected $formatted_date_visualisee;
	
	protected $date_envoyee;
	
	protected $formatted_date_envoyee;
	
	protected $date_reception;
	
	protected $formatted_date_reception;
	
	protected $motif_refus;
	
	protected $statut_origine;
	
	protected $section_origine;
	
	protected $resa_trans;
	
	protected $resa_arc_trans;
	
	protected $exemplaire;
	
	protected $transfert;
	
	public function __construct($id) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->num_transfert = 0;
		$this->date_creation = '';
		$this->formatted_date_creation = '';
		$this->sens_transfert = '';
		$this->num_location_source = 0;
		$this->num_location_dest = 0;
		$this->num_expl = 0;
		$this->etat_demande = '';
		$this->date_visualisee = '';
		$this->formatted_date_visualisee = '';
		$this->date_envoyee = '';
		$this->formatted_date_envoyee = '';
		$this->date_reception = '';
		$this->formatted_date_reception = '';
		$this->motif_refus = '';
		$this->statut_origine = '';
		$this->section_origine = '';
		$this->resa_trans = '';
		$this->resa_arc_trans = '';
		if($this->id) {
			$query = "select * from transferts_demande where id_transfert_demande = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->num_transfert = $row->num_transfert;
			$this->date_creation = $row->date_creation;
			$this->formatted_date_creation = formatdate($row->date_creation);
			$this->sens_transfert = $row->sens_transfert;
			$this->num_location_source = $row->num_location_source;
			$this->num_location_dest = $row->num_location_dest;
			$this->num_expl = $row->num_expl;
			$this->etat_demande = $row->etat_demande;
			$this->date_visualisee = $row->date_visualisee;
			$this->formatted_date_visualisee = formatdate($row->date_visualisee);
			$this->date_envoyee = $row->date_envoyee;
			$this->formatted_date_envoyee = formatdate($row->date_envoyee);
			$this->date_reception = $row->date_reception;
			$this->formatted_date_reception = formatdate($row->date_reception);
			$this->motif_refus = $row->motif_refus;
			$this->statut_origine = $row->statut_origine;
			$this->section_origine = $row->section_origine;
			$this->resa_trans = $row->resa_trans;
			$this->resa_arc_trans = $row->resa_arc_trans;
		}
	}
		
	public function get_id() {
		return $this->id;
	}
	
	public function get_num_transfert() {
		return $this->num_transfert;
	}
	
	public function get_date_creation() {
		return $this->date_creation;
	}
	
	public function get_formatted_date_creation() {
		return $this->formatted_date_creation;
	}
	
	public function get_sens_transfert() {
		return $this->sens_transfert;
	}
	
	public function get_num_location_source() {
		return $this->num_location_source;
	}
	
	public function get_num_location_dest() {
		return $this->num_location_dest;
	}
	
	public function get_etat_demande() {
		return $this->etat_demande;
	}
	
	public function get_date_visualisee() {
		return $this->date_visualisee;
	}
	
	public function get_formatted_date_visualisee() {
		return $this->formatted_date_visualisee;
	}
	
	public function get_date_envoyee() {
		return $this->date_envoyee;
	}
	
	public function get_formatted_date_envoyee() {
		return $this->formatted_date_envoyee;
	}
	
	public function get_date_reception() {
		return $this->date_reception;
	}
	
	public function get_formatted_date_reception() {
		return $this->formatted_date_reception;
	}
	
	public function get_motif_refus() {
		return $this->motif_refus;
	}
	
	public function get_resa_trans() {
		return $this->resa_trans;
	}
	
	public function get_exemplaire() {
		if(!isset($this->exemplaire)) {
			$this->exemplaire = new exemplaire('', $this->num_expl);
		}
		return $this->exemplaire;
	}
	
	public function get_transfert() {
		if(!isset($this->transfert)) {
			$this->transfert = new transfert($this->num_transfert);
		}
		return $this->transfert;
	}
}