<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_resa_planning_PDF.class.php,v 1.2 2019-04-26 15:59:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/pdf/reader/resa/lettre_reader_resa_PDF.class.php");

class lettre_reader_resa_planning_PDF extends lettre_reader_resa_PDF {
	
	protected function get_query($id_empr) {
		return "select id_resa from resa_planning where resa_idempr='$id_empr' and resa_validee=1 order by resa_date_debut ";
	}
	
	protected function display_notice_resa($id_resa_print, $x, $y, $largeur, $retrait) {
		global $msg;
	
		$dates_resa_sql = " date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin " ;
		$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, resa_date_debut, resa_date_fin, ";
		$requete .= "trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')), ";
		$requete .= "if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')), ";
		$requete .= "ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql ;
		$requete .= "FROM (((resa_planning LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ";
		$requete .= "LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id ) ";
		$requete .= "LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
		$requete .= "LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id ";
		$requete .= "LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id ) ";
		$requete .= "WHERE id_resa='".$id_resa_print."' ";
	
		$res = pmb_mysql_query($requete);
		$expl = pmb_mysql_fetch_object($res);
	
		$responsabilites = get_notice_authors($expl->notice_id) ;
		$header_aut= gen_authors_header($responsabilites);
		$header_aut ? $auteur=" / ".$header_aut : $auteur="";
	
		$this->PDF->SetXY ($x,$y);
		$this->PDF->setFont($this->font, 'BU', 10);
		$this->PDF->multiCell(($largeur - $x), 8, $expl->tit.$auteur, 0, 'L', 0);
	
		$this->PDF->SetXY ($x+$retrait,$y+4);
		$this->PDF->setFont($this->font, '', 10);
		$this->PDF->multiCell(($largeur - $retrait - $x), 8, $msg['resa_planning_date_debut']." ".$expl->aff_resa_date_debut." ".$msg['resa_planning_date_fin']." ".$expl->aff_resa_date_fin, 0, 'L', 0);
	}
}