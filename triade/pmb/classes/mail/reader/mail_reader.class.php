<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader.class.php,v 1.2 2019-05-11 15:09:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader {
	
	protected function get_parameter_prefix() {
		return '';
	}
	
	protected function get_evaluated_parameter($parameter_name) {
		global $biblio_name, $biblio_phone, $biblio_email;
		
		global ${$parameter_name};
		eval ("\$evaluated=\"".${$parameter_name}."\";");
		return $evaluated;
	}
	
	protected function get_parameter_value($name) {
		$parameter_name = $this->get_parameter_prefix().'_'.$name;
		return $this->get_evaluated_parameter($parameter_name);
	}
	
	protected function _init_parameter_value($name, $value) {
		$parameter_name = $this->get_parameter_prefix().'_'.$name;
		global ${$parameter_name};
		if(empty(${$parameter_name})) {
			${$parameter_name} = $value;
		}
	}
	
	protected function get_empr_coords($id_empr=0, $id_groupe=0) {
		global $msg;
		
		/* Récupération du nom, prénom et mail de l'utilisateur */
		$query = "select id_empr, empr_mail, empr_nom, empr_prenom, empr_cb, ";
		$query .= "date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_date_expiration ";
		$query .= "from empr ";
		if ($id_groupe) {
			$query .= ", groupe where empr.id_empr=groupe.resp_groupe and id_groupe=$id_groupe";
		} else {
			$query .= "where id_empr=$id_empr";
		}
		$result = pmb_mysql_query($query);
		return pmb_mysql_fetch_object($result);
	}
	
	protected function get_text_madame_monsieur($id_empr) {
		$query = "select empr_nom, empr_prenom from empr where id_empr='".$id_empr."'";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$text_madame_monsieur=str_replace("!!empr_name!!", $row->empr_nom,$this->get_parameter_value('madame_monsieur'));
		$text_madame_monsieur=str_replace("!!empr_first_name!!", $row->empr_prenom,$text_madame_monsieur);
		return $text_madame_monsieur;
	}
	
	protected function get_mail_bloc_adresse() {
		return mail_bloc_adresse();
	}
}