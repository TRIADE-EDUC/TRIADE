<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_resa_PDF.class.php,v 1.6.2.1 2024/06/06 13:51:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/reader/lettre_reader_PDF.class.php");

class lettre_reader_resa_PDF extends lettre_reader_PDF {
	
    protected static function get_parameter_prefix() {
		return "pdflettreresa";
	}
	
	protected function _init_default_parameters() {
	    $this->_init_parameter_value('list_order', 'resa_date_debut');
	}
	
	protected function _init_default_positions() {
		$this->_init_position_values('biblio_info', array($this->get_parameter_value('marge_page_gauche'),10));
		$this->_init_position_values('lecteur_adresse', array($this->get_parameter_value('marge_page_gauche'),45));
		$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),125,0,0,12));
	}
	
	protected function get_query_list_order() {
	    return "order by ".$this->get_parameter_value('list_order');
	}
	    
	protected function get_query_list($id) {
		$id = intval($id);
	    return "select id_resa from resa where resa_idempr='$id' and resa_cb is not null and resa_cb!='' ".$this->get_query_list_order();
	}
	
	public function doLettre($id_empr) {
		global $nb_page;
		global $pmb_afficher_numero_lecteur_lettres;
		
		//Génération de la lettre dans la langue du lecteur
		$this->set_language(emprunteur::get_lang_empr($id_empr));
		
		$this->PDF->addPage();
		$this->display_biblio_info() ;
		$this->display_lecteur_adresse($id_empr, 90, 0, !$pmb_afficher_numero_lecteur_lettres,true,true);
		
		$this->display_madame_monsieur($id_empr);
		$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('before_list'), 0, 'J', 0);
		$req = pmb_mysql_query($this->get_query_list($id_empr));
		
		$i=0;
		$nb_page=0;
		$indice_page = 0 ;
		while ($data = pmb_mysql_fetch_array($req)) {
			if ($nb_page==0 && $i==$this->get_parameter_value('nb_1ere_page')) {
				$this->PDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			} elseif ((($nb_page>=1) && ((($i-$this->get_parameter_value('nb_1ere_page')) % $this->get_parameter_value('nb_par_page'))==0)) || ($this->PDF->GetY()>$this->get_parameter_value('limite_after_list'))) {
				$this->PDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			}
		
			if ($nb_page==0) $pos_page = $this->get_parameter_value('debut_expl_1er_page')+$this->get_parameter_value('taille_bloc_expl')*$indice_page;
			else $pos_page = $this->get_parameter_value('debut_expl_page')+$this->get_parameter_value('taille_bloc_expl')*$indice_page;
			$this->display_notice_resa($data['id_resa'],$this->get_parameter_value('marge_page_gauche'),$pos_page,$this->w, 10);
			$i++;
			$indice_page++;
		}
		$this->PDF->setFont($this->font, '', 12);
		// dépassement sur autre page de cette partie
		if (($pos_page+$this->get_parameter_value('taille_bloc_expl'))>$this->get_parameter_value('limite_after_list')) {
			$this->PDF->addPage();
			$pos_after_list = $this->get_parameter_value('debut_expl_page');
		} else {
			$pos_after_list = $pos_page+$this->get_parameter_value('taille_bloc_expl');
		}
		$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),($pos_after_list));
		$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('after_list')."\n\n", 0, 'J', 0);
		$this->PDF->setFont($this->font, 'I', 12);
		$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('fdp'), 0, 'R', 0);
		
		//Restauration de la langue de l'interface
		$this->restaure_language();
	}
	
	protected function get_query_notice_resa($id_resa_print) {
		global $msg;
		
		$dates_resa_sql = " date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin " ;
		$query = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, resa_date_debut, resa_date_fin, resa_cb, resa_loc_retrait, ";
		$query .= "trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')), ";
		$query .= "if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')), ";
		$query .= "ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql ;
		$query .= "FROM (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ";
		$query .= "LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id ) ";
		$query .= "LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
		$query .= "LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id ";
		$query .= "LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id ) ";
		$query .= "WHERE id_resa='".$id_resa_print."' ";
		return $query;
	}
	
	protected function get_query_detail_notice_resa($id_resa_print) {
		$query_detail = "select resa_confirmee, resa_cb,location_libelle, expl_cote from resa
		left join exemplaires on expl_cb=resa_cb
		left join docs_location on idlocation=expl_location
		where id_resa =$id_resa_print  and resa_cb is not null and resa_cb!='' ";
		return $query_detail;
	}
	
	// ************************* Imprime la ligne de resa pour une notice sur la lettre de confirmation de réservation
	protected function display_notice_resa($id_resa_print, $x, $y, $largeur, $retrait) {
		global $msg;
		global $pmb_transferts_actif,$transferts_choix_lieu_opac;
	
		$query = $this->get_query_notice_resa($id_resa_print);
		$res = pmb_mysql_query($query);
		$expl = pmb_mysql_fetch_object($res);
	
		$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
		$header_aut= gen_authors_header($responsabilites);
		$header_aut ? $auteur=" / ".$header_aut : $auteur="";
	
		$query_detail = $this->get_query_detail_notice_resa($id_resa_print);
		$res_detail = pmb_mysql_query($query_detail) ;
		$expl_detail = pmb_mysql_fetch_object($res_detail);
	
		$this->PDF->SetXY ($x,$y);
		$this->PDF->setFont($this->font, 'BU', 10);
		$this->PDF->multiCell(($largeur - $x), 5, $expl->tit.$auteur,0, 'L', 0);
		$this->PDF->SetXY ($x+$retrait,$y+7);
		$this->PDF->setFont($this->font, '', 10);
		$this->PDF->multiCell(($largeur - $retrait - $x), 7, strip_tags($msg[291]." : ".$expl_detail->resa_cb." $msg[296] : ".$expl_detail->expl_cote), 0, 'L', 0);
		$this->PDF->SetXY ($x+$retrait,$y+10);
		$this->PDF->setFont($this->font, '', 10);
		$this->PDF->multiCell(($largeur - $retrait - $x), 10, $msg['fpdf_valide']." ".$expl->aff_resa_date_debut."  ".$msg['fpdf_valable']." ", 0, 'L', 0);
		$this->PDF->SetXY (($x+$retrait+65),$y+10);
		$this->PDF->setFont($this->font, 'B', 10);
		$this->PDF->multiCell(($largeur - $x - $retrait - 65), 10, $expl->aff_resa_date_fin, 0, 'L', 0);
	
		if($pmb_transferts_actif && $transferts_choix_lieu_opac==3) {
			$rqt = "select resa_confirmee, resa_cb,resa_loc_retrait from resa where id_resa =$id_resa_print  and resa_cb is not null and resa_cb!='' ";
			$res = pmb_mysql_query($rqt) ;
			if(($resa_lue = pmb_mysql_fetch_object($res))) {
				if ($resa_lue->resa_confirmee) {
					if ($resa_lue->resa_loc_retrait) {
						$loc_retait=$resa_lue->resa_loc_retrait;
					} else {
						$rqt = "select expl_location from exemplaires where expl_cb='".$resa_lue->resa_cb."' ";
						$res = pmb_mysql_query($rqt) ;
						if(($res_expl = pmb_mysql_fetch_object($res))) {
							$loc_retait=$res_expl->expl_location;
						}
					}
					$rqt = "select location_libelle from docs_location where idlocation=".$loc_retait;
					$res = pmb_mysql_query($rqt) ;
					if(($res_expl = pmb_mysql_fetch_object($res))) {
						$lieu_retrait=str_replace("!!location!!",$res_expl->location_libelle,$msg["resa_lettre_lieu_retrait"]);
					}
					$this->PDF->SetXY (($x+$retrait+110),$y+8);
					$this->PDF->setFont($this->font, 'B', 10);
					$this->PDF->multiCell(($largeur - $x - $retrait - 82), 8, $lieu_retrait, 0, 'L', 0);
				}
			}
		} else {
			$this->PDF->SetXY (($x+$retrait+110),$y+8);
			$this->PDF->setFont($this->font, 'B', 10);
			$lieu_retrait=str_replace("!!location!!",$expl_detail->location_libelle,$msg["resa_lettre_lieu_retrait"]);
			$this->PDF->multiCell(($largeur - $x - $retrait - 82), 8, $lieu_retrait, 0, 'L', 0);
		}
	}
}