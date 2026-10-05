<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans.class.php,v 1.9.6.3 2024/11/22 11:11:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ("$include_path/notice_authors.inc.php");
require_once ($include_path."/mail.inc.php") ;
require_once ("$class_path/author.class.php");
require_once ($class_path."/serie.class.php");

class mail_reader_loans extends mail_reader {
	
	protected $expl_info = array();
	
    protected static function get_parameter_prefix() {
// 		return "pdflettreloans";
	}
	
	protected function get_mail_object() {
		global $msg;
		
		return $msg["prets_en_cours"];
	}
	
	protected function get_query_list_base() {
	    return "
            SELECT pret_idempr, expl_id, expl_cb, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, niveau_relance
            FROM pret
            join exemplaires ON pret_idexpl=expl_id
            LEFT JOIN notices as notices_m ON notices_m.notice_id = exemplaires.expl_notice and expl_notice <> 0
            LEFT JOIN bulletins ON bulletins.bulletin_id = exemplaires.expl_bulletin
            LEFT JOIN notices AS notices_s ON bulletins.bulletin_notice = notices_s.notice_id
        ";
	}
	
	protected function get_expl_informations($expl_cb) {
		global $msg;
	
		if(empty($this->expl_info[$expl_cb])) {
			$query = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, expl_cote, expl_prix, pret_date, pret_retour, expl_typdoc, tdoc_libelle, expl_section, section_libelle, expl_location, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ifnull(notices_s.date_parution, '0000-00-00') as date_parution, ";
			$query.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
			$query.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
			$query.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ;
			$query.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
			$query.= "WHERE expl_cb='".addslashes($expl_cb)."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
			$result = pmb_mysql_query($query);
			$expl = pmb_mysql_fetch_object($result);
			if (!empty($expl->expl_location)) {
			    $expl->location_libelle = translation::get_translated_text($expl->expl_location, 'docs_location', 'location_libelle', $expl->location_libelle, static::$language);
			}
			if (!empty($expl->expl_section)) {
			    $expl->section_libelle = translation::get_translated_text($expl->expl_section, 'docs_section', 'section_libelle', $expl->section_libelle, static::$language);
			}
			if (!empty($expl->expl_typdoc)) {
			    $expl->tdoc_libelle = translation::get_translated_text($expl->expl_typdoc, 'docs_type', 'tdoc_libelle', $expl->tdoc_libelle, static::$language);
			}
			$this->expl_info[$expl_cb] = $expl;
		}
		return $this->expl_info[$expl_cb];
	}
	
	protected function get_mail_expl_content_notice_description($expl_cb) {
		global $charset;
		
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
		return $expl->tit." (".$libelle.")\r\n";
	}
		
	protected function get_mail_expl_content_dates($expl_cb) {
		global $msg;
		
		$expl = $this->get_expl_informations($expl_cb);
		return "    - ".$msg['fpdf_date_pret']." ".$expl->aff_pret_date." ".$msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour."\r\n";
	}
	
	protected function get_mail_expl_content_description($expl_cb) {
		$expl = $this->get_expl_informations($expl_cb);
		return "    - ".$expl->location_libelle.": ".$expl->section_libelle.(!empty($expl->expl_cote) ? ", ".$expl->expl_cote : '')." (".$expl->expl_cb.")\r\n\r\n";
	}
	
	protected function get_mail_expl_content($expl_cb) {
		$mail_expl_content = '';
		$mail_expl_content .= $this->get_mail_expl_content_notice_description($expl_cb);
		$mail_expl_content .= $this->get_mail_expl_content_dates($expl_cb);
		$mail_expl_content .= $this->get_mail_expl_content_description($expl_cb);
		return $mail_expl_content;
	}
	
	protected function get_mail_content() {
		global $msg;
		
		$mail_content = $this->get_mail_object()."\r\n";
		$mail_content .= $msg['fpdf_edite']." ".formatdate(date("Y-m-d",time()))."\r\n\r\n";
		
		if ($this->id_group) {
			//requete par rapport à un groupe d'emprunteurs
			$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where groupe_id='".$this->id_group."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
			$req1 = pmb_mysql_query($rqt1);
		}
		
		if ($this->mail_to_id) {
			//requete par rapport à un emprunteur
			$rqt1 = "select id_empr, empr_nom, empr_prenom from empr_groupe, empr, pret where id_empr='".$this->mail_to_id."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
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
	
	public function send_mail() {
	    $sended = false;
		if($this->get_mail_to_mail()) {
			$sended = $this->mailpmb();
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