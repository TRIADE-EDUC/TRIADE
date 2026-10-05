<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_mail.class.php,v 1.3 2022/09/16 07:08:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/modules/module.class.php");

class module_mail extends module{
	
	public function proceed_mail_relance_adhesion() {
		global $id_empr;
		
		$this->load_class("/mail/reader/mail_reader_relance_adhesion.class.php");
		$mail_reader_relance_adhesion = mail_reader_relance_adhesion::get_instance();
		$mail_reader_relance_adhesion->set_mail_to_id($id_empr);
		$mail_reader_relance_adhesion->send_mail();
	}
	
	public function proceed_mail_retard() {
		global $relance, $id_empr;
		
		if (empty($relance)) $relance = 1;
		
		$this->load_class("/mail/reader/loans/mail_reader_loans_late.class.php");
		mail_reader_loans_late::set_niveau_relance($relance);
		$mail_reader_loans_late = mail_reader_loans_late::get_instance();
		$mail_reader_loans_late->set_mail_to_id($id_empr);
		$mail_reader_loans_late->send_mail();
	}
	
	public function proceed_mail_prets() {
		global $relance, $id_empr, $id_groupe;
		
		if (!$relance) $relance=1;
		
		$this->load_class("/mail/reader/loans/mail_reader_loans.class.php");
		$mail_reader_loans = mail_reader_loans::get_instance();
		$mail_reader_loans->set_mail_to_id($id_empr);
		$mail_reader_loans->set_id_group($id_groupe);
		$mail_reader_loans->send_mail();
	}
	
	public function proceed_mail_retard_groupe() {
		global $relance, $id_groupe, $selected_objects;
		global $group_name; //est-ce utilisé plus tard ?
		
		$this->load_class("/mail/reader/loans/mail_reader_loans_late_group.class.php");
		
		if (empty($relance)) $relance = 1;
		if ($id_groupe) {
			$id_groupe = intval($id_groupe);
			$req = "select libelle_groupe from groupe where id_groupe='".$id_groupe."'";
			$res = pmb_mysql_query($req);
			if ($res && pmb_mysql_num_rows($res)) {
				$row = pmb_mysql_fetch_object($res);
				$group_name = $row->libelle_groupe;
			}
			mail_reader_loans_late_group::set_niveau_relance($relance);
			$mail_reader_loans_late_group = mail_reader_loans_late_group::get_instance();
			$mail_reader_loans_late_group->set_id_group($id_groupe);
			$mail_reader_loans_late_group->send_mail();
		} elseif(!empty($selected_objects)) {
			mail_reader_loans_late_group::set_niveau_relance($relance);
			$mail_reader_loans_late_group = mail_reader_loans_late_group::get_instance();
			$groups = explode(',', $selected_objects);
			foreach ($groups as $id_groupe) {
				$mail_reader_loans_late_group->set_id_group($id_groupe);
				$mail_reader_loans_late_group->send_mail();
			}
		}
	}
}