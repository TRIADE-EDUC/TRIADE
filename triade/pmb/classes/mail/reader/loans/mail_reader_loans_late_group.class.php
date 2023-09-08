<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans_late_group.class.php,v 1.1 2019-04-12 13:08:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/mail/reader/loans/mail_reader_loans_late.class.php");

class mail_reader_loans_late_group extends mail_reader_loans_late {
	
	protected function get_parameter_value($name) {
		$parameter_name = $this->get_parameter_prefix().'_'.static::$niveau_relance.$name.'_group';
		$parameter_value = $this->get_evaluated_parameter($parameter_name);
		if($parameter_value) {
			return $parameter_value;
		} else {
			return parent::get_parameter_value($name);
		}
	}
	
	protected function get_mail_content($id_empr=0, $id_groupe=0) {
		global $msg, $charset;
	
		$mail_content = '';
		if($this->get_parameter_value('madame_monsieur')) {
			$mail_content .= $this->get_parameter_value('madame_monsieur')."\r\n\r\n";
		}
		if($this->get_parameter_value('before_list')) {
			$mail_content .= $this->get_parameter_value('before_list')."\r\n\r\n";
		}
	
		//requete par rapport à un groupe d'emprunteurs
		$rqt1 = "select empr_id, empr_nom, empr_prenom from empr_groupe, empr, pret where groupe_id='".$id_groupe."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
		$req1 = pmb_mysql_query($rqt1);
		while ($data1=pmb_mysql_fetch_array($req1)) {
			$id_empr=$data1['empr_id'];
			
			//Récupération des exemplaires
			$query = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_retour < curdate() and pret_idexpl=expl_id order by pret_date " ;
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
		$mail_content .= $this->get_mail_bloc_adresse() ;
		return $mail_content;
	}
	
	public function send_mail($id_empr=0, $id_groupe=0) {
		global $msg, $charset;
		global $biblio_name, $biblio_email, $PMBuseremailbcc;
		
		$coords = $this->get_empr_coords($id_empr, $id_groupe);
		$headers = "Content-type: text/plain; charset=".$charset."\n";
		$mail_content = $this->get_mail_content($id_empr, $id_groupe);
		
		$res_envoi=mailpmb($coords->empr_prenom." ".$coords->empr_nom, $coords->empr_mail, $this->get_mail_object(),$mail_content, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc, 1);
		
		if ($res_envoi) echo "<h3>".sprintf($msg["mail_retard_succeed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a><br /><br />".nl2br($mail_content);
		else echo "<h3>".sprintf($msg["mail_retard_failed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
	}
}