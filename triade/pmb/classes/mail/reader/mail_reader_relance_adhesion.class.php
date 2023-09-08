<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_relance_adhesion.class.php,v 1.1 2019-04-12 13:08:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/mail/reader/mail_reader.class.php");
require_once($class_path."/emprunteur.class.php");

class mail_reader_relance_adhesion extends mail_reader {
	
	protected function get_parameter_value($name) {
		$parameter_name = 'mailrelanceadhesion_'.$name;
		return $this->get_evaluated_parameter($parameter_name);
	}
	
	protected function get_mail_object() {
		return $this->get_parameter_value('objet');
	}
	
	protected function get_query_all() {
		global $pmb_lecteurs_localises, $empr_location_id, $deflt2docs_location;
		global $empr_statut_edit, $empr_categ_filter, $empr_codestat_filter;
		global $restricts;
		
		// restriction localisation le cas échéant
		if ($pmb_lecteurs_localises) {
			if ($empr_location_id=="") $empr_location_id = $deflt2docs_location ;
			if ($empr_location_id!=0) $restrict_localisation = " AND empr_location='$empr_location_id' ";
				else $restrict_localisation = "";
		}
	
		// filtré par un statut sélectionné
		$restrict_statut="";
		if ($empr_statut_edit) {
			if ($empr_statut_edit!=0) $restrict_statut = " AND empr_statut='$empr_statut_edit' ";
		}
		$restrict_categ = '';
		if($empr_categ_filter) {
			$restrict_categ = " AND empr_categ= '".$empr_categ_filter."' ";
		}
		$restrict_codestat = '';
		if($empr_codestat_filter) {
			$restrict_codestat = " AND empr_codestat= '".$empr_codestat_filter."' ";
		}
		$requete = "SELECT empr.id_empr  FROM empr, empr_statut ";
		$restrict_empr = " WHERE 1 ";
		$restrict_requete = $restrict_empr.$restrict_localisation.$restrict_statut.$restrict_categ.$restrict_codestat." and ".$restricts;
		$requete .= $restrict_requete;
		$requete.=" and empr_mail!=''";
		$requete .= " and empr_statut=idstatut";
		$requete .= " ORDER BY empr_nom, empr_prenom ";
		return $requete;
	}
	
	protected function get_mail_content($id_empr=0, $id_groupe=0) {
		$mail_content = '';
		if($this->get_parameter_value('madame_monsieur')) {
			$mail_content .= $this->get_parameter_value('madame_monsieur')."\r\n\r\n";
		}
		$mail_content .= $this->get_parameter_value('texte')."\r\n";
		if($this->get_parameter_value('fdp')) {
			$mail_content .= $this->get_parameter_value('fdp')."\r\n\r\n";
		}
		$mail_content .= $this->get_mail_bloc_adresse();
		return $mail_content;
	}
	
	public function send_mail($id_empr=0, $id_groupe=0) {
		global $msg, $charset;
		global $action;
		global $biblio_name, $biblio_email, $PMBuseremailbcc;
	
		$headers = "Content-type: text/plain; charset=".$charset."\n";
		
		if ($action=="print_all") {
			$requete = $this->get_query_all();
			$res = @pmb_mysql_query($requete);
			while(($empr=pmb_mysql_fetch_object($res))) {
				$coords = $this->get_empr_coords($empr->id_empr);
				$mail_content = $this->get_mail_content($empr->id_empr);
				$mail_content = str_replace("!!date_fin_adhesion!!", $coords->aff_date_expiration, $mail_content);
				
				//remplacement nom et prenom
				$mail_content=str_replace("!!empr_name!!", $coords->empr_nom,$mail_content);
				$mail_content=str_replace("!!empr_first_name!!", $coords->empr_prenom,$mail_content);
				
				$res_envoi=mailpmb($coords->empr_prenom." ".$coords->empr_nom, $coords->empr_mail, $this->get_mail_object(),$mail_content, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc,1);
				if ($res_envoi) echo "<h3>".sprintf($msg["mail_retard_succeed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a><br /><br />".nl2br($mail_content);
				else echo "<h3>".sprintf($msg["mail_retard_failed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
			}
		} else {
			$coords = $this->get_empr_coords($id_empr);
			$mail_content = $this->get_mail_content($id_empr, $id_groupe);
			$mail_content = str_replace("!!date_fin_adhesion!!", $coords->aff_date_expiration, $mail_content);
			
			//remplacement nom et prenom
			$mail_content=str_replace("!!empr_name!!", $coords->empr_nom,$mail_content);
			$mail_content=str_replace("!!empr_first_name!!", $coords->empr_prenom,$mail_content);
			
			$res_envoi=mailpmb($coords->empr_prenom." ".$coords->empr_nom, $coords->empr_mail, $this->get_mail_object(),$mail_content, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc,1);
			if ($res_envoi) echo "<h3>".sprintf($msg["mail_retard_succeed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a><br /><br />".nl2br($mail_content);
			else echo "<h3>".sprintf($msg["mail_retard_failed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
		}
	}
}