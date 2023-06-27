<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans.class.php,v 1.1 2019-04-12 13:08:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/mail/reader/mail_reader.class.php");

require_once ("$include_path/notice_authors.inc.php");
require_once ($include_path."/mail.inc.php") ;
require_once ("$class_path/author.class.php");
require_once ($class_path."/serie.class.php");

class mail_reader_loans extends mail_reader {
	
	protected function get_parameter_prefix() {
// 		return "pdflettreloans";
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg["prets_en_cours"];
	}
	
	protected function get_expl_informations($expl_cb) {
		global $msg;
	
		$query = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ifnull(notices_s.date_parution, '0000-00-00') as date_parution, ";
		$query.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$query.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$query.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ;
		$query.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
		$query.= "WHERE expl_cb='".addslashes($expl_cb)."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
		$result = pmb_mysql_query($query);
		return pmb_mysql_fetch_object($result);
	}
	
	protected function get_mail_expl_content($expl_cb) {
		global $msg, $charset;
		
		$mail_expl_content = '';
		
		$expl = $this->get_expl_informations($expl_cb);
		
		$libelle=$expl->tdoc_libelle;
		
		$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
		$header_aut = gen_authors_header($responsabilites);
		$header_aut ? $auteur=" / ".$header_aut : $auteur="";
		
		// récupération du titre de série
		$tit_serie = "";
		if ($expl->tparent_id && $expl->m_id) {
			$parent = new serie($expl->tparent_id);
			$tit_serie = $parent->name;
			if($expl->tnvol)
				$tit_serie .= ', '.$expl->tnvol;
		}
		if($tit_serie) {
			$expl->tit = $tit_serie.'. '.$expl->tit;
		}
		if($header_aut) {
			$libelle .= " / ".$header_aut;
		}
		if ($expl->date_parution != '0000-00-00') {
			$libelle .= " - ".htmlentities(formatdate($expl->date_parution), ENT_QUOTES, $charset);
		}
		
		$mail_expl_content .= $expl->tit." (".$libelle.")\r\n";
		$mail_expl_content .= "    -".$msg['fpdf_date_pret']." : ".$expl->aff_pret_date." ".$msg['fpdf_retour_prevu']." : ".$expl->aff_pret_retour."\r\n";
		$mail_expl_content .= "    -".$expl->location_libelle.": ".$expl->section_libelle." (".$expl->expl_cb.")\r\n\r\n";
		return $mail_expl_content;
	}
	
	protected function get_mail_content($id_empr=0, $id_groupe=0) {
		global $msg;
		
		$mail_content = $this->get_mail_object()."\r\n";
		$mail_content .= $msg['fpdf_edite']." ".formatdate(date("Y-m-d",time()))."\r\n\r\n";
		
		if ($id_groupe) {
			//requete par rapport à un groupe d'emprunteurs
			$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where groupe_id='".$id_groupe."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
			$req1 = pmb_mysql_query($rqt1);
		}
		
		if ($id_empr) {
			//requete par rapport à un emprunteur
			$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where id_empr='".$id_empr."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
			$req1 = pmb_mysql_query($rqt1);
		}
		
		while ($data1=pmb_mysql_fetch_array($req1)) {
			$id_empr=$data1['id_empr'];
			$mail_content .= $data1['empr_nom']." ".$data1['empr_prenom']."\r\n\r\n";
			
			//Récupération des exemplaires
			$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;
			$req = pmb_mysql_query($rqt);
			while ($data = pmb_mysql_fetch_array($req)) {
				$mail_content .= $this->get_mail_expl_content($data['expl_cb']);
			}
		}
		global $mailretard_1fdp;
		$mail_content .= $mailretard_1fdp."\r\n\r\n".$this->get_mail_bloc_adresse();
		return $mail_content;
	}
	
	public function send_mail($id_empr=0, $id_groupe=0) {
		global $msg, $charset;
		global $biblio_name, $biblio_email, $PMBuseremailbcc;
		
		$coords = $this->get_empr_coords($id_empr, $id_groupe);
		$headers = "Content-type: text/plain; charset=".$charset."\n";
		$mail_content = $this->get_mail_content($id_empr, $id_groupe);
		$res_envoi=mailpmb($coords->empr_prenom." ".$coords->empr_nom, $coords->empr_mail, $this->get_mail_object(),$mail_content, $biblio_name, $biblio_email,$headers, "", $PMBuseremailbcc,1);
		if ($res_envoi) echo "<h3>".sprintf($msg["mail_retard_succeed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a><br /><br />".nl2br($mail_content);
		else echo "<h3>".sprintf($msg["mail_retard_failed"],$coords->empr_mail)."</h3><br /><a href=\"\" onClick=\"self.close(); return false;\">".$msg["mail_retard_close"]."</a>";
	}
}