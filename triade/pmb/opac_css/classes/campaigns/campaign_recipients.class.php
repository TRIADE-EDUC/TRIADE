<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_recipients.class.php,v 1.5 2023/02/08 13:17:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/campaigns/campaign_recipient.class.php");

class campaign_recipients{
	protected $num_campaign;
	protected $recipients;
	
	public function __construct($num_campaign=0) {
		$this->num_campaign = intval($num_campaign);		
		$this->fetch_data();
	}
	
	public function fetch_data() {		
		$this->recipients=array();
		$rqt = "select * from campaigns_recipients where campaign_recipient_num_campaign = '".$this->num_campaign."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_assoc($res)){
				$this->recipients[$row['id_campaign_recipient']] = new campaign_recipient($row['id_campaign_recipient']);
			}
		}
	}
	
	protected function gen_hash($id_empr, $to_mail, $corps) {
		return md5(md5($corps)."_".$to_mail."_".$id_empr."_".$this->num_campaign);
	}
	
	public function add($id_empr, $to_mail, $corps) {
		$campaign_recipient = new campaign_recipient();
		$campaign_recipient->set_num_campaign($this->num_campaign);
		$campaign_recipient->init_properties($id_empr, $to_mail, $corps);
		$campaign_recipient->save();
		return $campaign_recipient;
	}
	
	public static function delete($num_campaign=0) {
		$num_campaign = intval($num_campaign);
		$query = "delete from campaigns_recipients where campaign_recipient_num_campaign = '".$num_campaign."'";
		pmb_mysql_query($query);
	}
	
	public function get_recipient($id) {
		return $this->recipients[$id];
	}
	
	public static function get_number_recipients($num_campaign=0) {
		$num_campaign = intval($num_campaign);
		$query = "select count(*) from campaigns_recipients where campaign_recipient_num_campaign = '".$num_campaign."'";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}
	
	public static function get_possible_values_of_field($campaigns=array(), $field_name='') {
		global $msg;
		
		$values = array();
		$query = '';
		
		switch($field_name) {
			case 'categ':
				$query = "SELECT libelle as value FROM empr_categ JOIN campaigns_recipients ON campaign_recipient_empr_categ = id_categ_empr WHERE campaign_recipient_num_campaign IN (".implode(',', $campaigns).")";
				break;
			case 'codestat':
				$query = "SELECT libelle as value FROM empr_codestat JOIN campaigns_recipients ON campaign_recipient_empr_codestat = idcode WHERE campaign_recipient_num_campaign IN (".implode(',', $campaigns).")";
				break;
			case 'sexe':
				return array($msg['civilite_monsieur'], $msg['civilite_madame'], $msg['civilite_unknown']);
			case 'statut':
				$query = "SELECT statut_libelle as value FROM empr_statut JOIN campaigns_recipients ON campaign_recipient_empr_statut = idstatut WHERE campaign_recipient_num_campaign IN (".implode(',', $campaigns).")";
				break;
			case 'location':
				$query = "SELECT location_libelle as value FROM docs_location JOIN campaigns_recipients ON campaign_recipient_empr_location = idlocation WHERE campaign_recipient_num_campaign IN (".implode(',', $campaigns).")";
				break;
		}
		if ($query) {
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
					$values[] = $row['value'];
				}
			}
		}
		return $values;
	}
}// end class
