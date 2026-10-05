<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_recipient.class.php,v 1.6 2023/05/11 10:00:35 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class campaign_recipient{
	protected $id;
	protected $hash;
	protected $num_campaign;
	protected $details;
	public $recipients;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		if($this->id) {
			$this->fetch_data();
		}
	}
	
	public function fetch_data() {		
		$this->recipients=array();
		$query = "select * from campaigns_recipients where id_campaign_recipient = '".$this->id."'";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_assoc($result)){
			$this->hash = $row['campaign_recipient_hash'];
			$this->num_campaign = $row['campaign_recipient_num_campaign'];
			$this->details = array(
					'id' => $row['campaign_recipient_num_empr'],
					'cp' => $row['campaign_recipient_empr_cp'],
					'ville' => $row['campaign_recipient_empr_ville'],
					'prof' => $row['campaign_recipient_empr_prof'],
					'year' => $row['campaign_recipient_empr_year'],
					'categ' => $row['campaign_recipient_empr_categ'],
					'codestat' => $row['campaign_recipient_empr_codestat'],
					'sexe' => $row['campaign_recipient_empr_sexe'],
					'statut' => $row['campaign_recipient_empr_statut'],
					'location' => $row['campaign_recipient_empr_location']
			);
		}
	}
	
	protected function gen_hash($id_empr, $to_mail, $corps) {
		return md5(md5($corps)."_".$to_mail."_".$id_empr."_".$this->num_campaign);
	}
	
	public function init_properties($id_empr, $to_mail, $corps) {
		$emprunteur = new emprunteur($id_empr);
		$this->hash = $this->gen_hash($id_empr, $to_mail, $corps);
		$this->details = array(
				'id' => $emprunteur->id,
				'cp' => $emprunteur->cp,
				'ville' => $emprunteur->ville,
				'prof' => $emprunteur->prof,
				'year' => $emprunteur->birth,
				'categ' => $emprunteur->categ,
				'codestat' => $emprunteur->cstat,
				'sexe' => $emprunteur->sexe,
				'statut' => $emprunteur->empr_statut,
				'location' => $emprunteur->empr_location
		);
	}
	
	public function save() {
		$query = "insert into campaigns_recipients
			set campaign_recipient_hash = '".addslashes($this->hash)."',
			campaign_recipient_num_campaign = ".$this->num_campaign.",
			campaign_recipient_num_empr = '".addslashes($this->details['id'])."',
			campaign_recipient_empr_cp = '".addslashes($this->details['cp'])."',
			campaign_recipient_empr_ville = '".addslashes($this->details['ville'])."',
			campaign_recipient_empr_prof = '".addslashes($this->details['prof'])."',
			campaign_recipient_empr_year = '".addslashes($this->details['year'])."',
			campaign_recipient_empr_categ = '".$this->details['categ']."',
			campaign_recipient_empr_codestat = '".$this->details['codestat']."',
			campaign_recipient_empr_sexe = '".$this->details['sexe']."',
			campaign_recipient_empr_statut = '".$this->details['statut']."',
			campaign_recipient_empr_location = '".$this->details['location']."'
		";
		pmb_mysql_query($query);
		
		$this->id = pmb_mysql_insert_id();
		return $this->id;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_hash() {
		return $this->hash;
	}
	
	public function get_num_campaign() {
		return $this->num_campaign;
	}
	
	public function get_detail_label($key) {
		global $msg;
		
		switch($key) {
			case 'categ':
				$query = "SELECT libelle FROM empr_categ WHERE id_categ_empr = ".$this->details[$key];
				$result = pmb_mysql_query($query);
				return pmb_mysql_result($result, 0, 'libelle');
				break;
			case 'codestat':
				$query = "SELECT libelle FROM empr_codestat WHERE idcode = ".$this->details[$key];
				$result = pmb_mysql_query($query);
				return pmb_mysql_result($result, 0, 'libelle');
				break;
			case 'sexe':
				switch($this->details[$key]) {
					case 1:
						return $msg['civilite_monsieur'];
						break;
					case 2:
						return $msg['civilite_madame'];
						break;
					default:
						return $msg['civilite_unknown'];
						break;
				}
				break;
			case 'statut':
				$query = "SELECT statut_libelle FROM empr_statut WHERE idstatut = ".$this->details[$key];
				$result = pmb_mysql_query($query);
				return pmb_mysql_result($result, 0, 'statut_libelle');
				break;
			case 'location':
				$docs_location = new docs_location($this->details[$key]);
				return $docs_location->libelle;
				break;
			default:
				return $this->details[$key];
				break;
		}
	}
	
	public function get_detail($key) {
		return $this->details[$key];
	}
	
	public function set_num_campaign($num_campaign) {
		$this->num_campaign = intval($num_campaign);
	}

}// end class
