<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans_late_group.class.php,v 1.10.2.2 2024/09/24 13:17:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_loans_late_group extends mail_reader_loans_late {
	
	protected function get_parameter_value($name) {
		$parameter_name = static::get_parameter_prefix().'_'.static::$niveau_relance.$name.'_group';
		if($this->is_exist_parameter($parameter_name)) {
			return $this->get_evaluated_parameter($parameter_name);
		}
		return parent::get_parameter_value($name);
	}
	
	protected function _init_default_parameters() {
		$this->_init_parameter_value('list_order', 'empr_nom, empr_prenom, pret_date');
	    parent::_init_default_parameters();
	}
	
	protected function get_query_list_order() {
	    return "order by ".$this->get_parameter_value('list_order');
	}
	
	protected function get_query_list($id) {
		$id = intval($id);
		return "select empr_id, empr_nom, empr_prenom from empr_groupe, empr, pret where groupe_id='".$id."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id ".$this->get_query_list_order();
	}
	
	protected function get_query_list_base_from_empr() {
	    return "
            SELECT pret_idempr, expl_id, expl_cb, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit
            FROM pret
            join exemplaires ON pret_idexpl=expl_id
            LEFT JOIN notices as notices_m ON notices_m.notice_id = exemplaires.expl_notice and expl_notice <> 0
            LEFT JOIN bulletins ON bulletins.bulletin_id = exemplaires.expl_bulletin
            LEFT JOIN notices AS notices_s ON bulletins.bulletin_notice = notices_s.notice_id
        ";
	}
	
	protected function get_query_list_order_from_empr() {
	    return "order by pret_date";
	}
	
	protected function get_query_list_from_empr($id_empr) {
	    return $this->get_query_list_base_from_empr()." where pret_idempr='".$id_empr."' and pret_retour < curdate() ".$this->get_query_list_order_from_empr();
	}
	
	protected function get_mail_to_name() {
		$coords = $this->get_empr_coords();
		return $coords->empr_prenom." ".$coords->empr_nom;
	}
	
	protected function get_mail_to_mail() {
		$coords = $this->get_resp_coords();
		return $coords->empr_mail;
	}
	
	protected function get_mail_content() {
		$mail_content = '';
		if($this->get_parameter_value('madame_monsieur')) {
			$mail_content .= $this->get_parameter_value('madame_monsieur')."\r\n\r\n";
		}
		if($this->get_parameter_value('before_list')) {
			$mail_content .= $this->get_parameter_value('before_list')."\r\n\r\n";
		}
	
		//requete par rapport à un groupe d'emprunteurs
		$rqt1 = $this->get_query_list($this->id_group);
		$req1 = pmb_mysql_query($rqt1);
		while ($data1=pmb_mysql_fetch_array($req1)) {
			$id_empr=$data1['empr_id'];
			
			//Récupération des exemplaires
			$query = $this->get_query_list_from_empr($id_empr);
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$mail_content .= $data1['empr_nom']." ".$data1['empr_prenom']."\r\n\r\n";
			}
			while ($data = pmb_mysql_fetch_array($result)) {
				$mail_content .= $this->get_mail_expl_content($data['expl_cb']);
			}
		}	
		
		$mail_content .= "\r\n";
		if($this->get_parameter_value('after_list')) {
			$mail_content .= $this->get_parameter_value('after_list')."\r\n\r\n";
		}
		if($this->get_parameter_value('fdp')) {
			$mail_content .= $this->get_parameter_value('fdp')."\r\n\r\n";
		}
		$mail_content .= $this->get_mail_bloc_adresse();
		return $mail_content;
	}
	
	public function send_mail() {
	    $sended = false;
		$coords = $this->get_empr_coords();
		if($coords->empr_mail) {
		    $this->set_language($coords->empr_lang);
		    $sended = $this->mailpmb();
			$this->restaure_language();
			if ($sended) {
			    echo $this->get_display_sent_succeed();
			} else {
			    echo $this->get_display_sent_failed();
			}
		} else {
		    echo $this->get_display_unknown_mail();
		}
		return $sended;
	}
}