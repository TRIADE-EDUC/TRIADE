<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: exemplaire.class.php,v 1.6 2024/01/12 16:00:45 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class exemplaire {
	
	public $expl_id = 0;
	public $cb = '';
	public $id_notice = 0;
	public $id_bulletin = 0;
	public $id_bulletin_notice = 0;
	public $id_num_notice = 0;
	public $typdoc_id = 0;
	public $typdoc = '';
	public $duree_pret = 0;
	public $cote = '';
	public $section_id = 0;
	public $section = '';
	public $statut_id = 0;
	public $statut = '';
	public $pret = 0;
	public $location_id = 0;
	public $location = '';
	public $codestat_id = 0;
	public $codestat = '';
	public $date_depot = '0000-00-00';
	public $date_retour = '0000-00-00';
	public $note = '';
	public $prix = '';
	public $owner_id = 0;
	public $lastempr = 0;
	public $last_loan_date = '0000-00-00';
	public $create_date = '0000-00-00';
	public $update_date = '0000-00-00';
	public $type_antivol="";
	public $tranfert_location_origine = 0;
	public $tranfert_statut_origine = 0;
	public $tranfert_section_origine = 0;
	public $expl_comment='';
	public $nbparts = 1;
	public $expl_retloc = 0;
	public $expl_pnb_flag = 0;
	
	// constructeur
	public function __construct($cb='', $id=0, $id_notice=0, $id_bulletin=0) {
		// on checke si l'exemplaire est connu
		if ($cb && !$id) $clause_where = " WHERE expl_cb like '$cb' ";
		
		if ( (!$cb && $id) || ($cb && $id) ) $clause_where = " WHERE expl_id = '$id' ";
		
		if ($cb || $id) {
			$requete = "SELECT * FROM exemplaires 
					LEFT JOIN docs_type ON (idtyp_doc = expl_typdoc)";
			$requete .= $clause_where ;
			$result = pmb_mysql_query($requete);
			
			if(pmb_mysql_num_rows($result)) {
				$item = pmb_mysql_fetch_object($result);
				$this->expl_id		= $item->expl_id;
				$this->cb			= $item->expl_cb;
				$this->id_notice	= $item->expl_notice;
				$this->id_bulletin	= $item->expl_bulletin;
				$this->typdoc_id	= $item->expl_typdoc;
				$this->typdoc		= $item->tdoc_libelle;
				$this->duree_pret	= $item->duree_pret;
				$this->cote			= $item->expl_cote;
				$this->section_id	= $item->expl_section;
				$this->section		= translation::get_translated_text($item->expl_section, "docs_section", "section_libelle_opac");
				$this->statut_id	= $item->expl_statut;
				//$this->statut		= $item->statut_libelle;
				//$this->pret		= $item->pret_flag;
				$this->location_id	= $item->expl_location;
				$this->location		= translation::get_translated_text($item->expl_location, "docs_location", "location_libelle");
				$this->codestat_id	= $item->expl_codestat;
				//$this->codestat	= $item->codestat_libelle;
				$this->date_depot 	= $item->expl_date_depot ;
				$this->date_retour 	= $item->expl_date_retour ;
				$this->note			= $item->expl_note;
				$this->prix			= $item->expl_prix;
				$this->owner_id		= $item->expl_owner;
				$this->lastempr		= $item->expl_lastempr;
				$this->last_loan_date =  $item->last_loan_date;
				$this->create_date 	= $item->create_date;
				$this->update_date 	= $item->update_date;
				$this->type_antivol = $item->type_antivol ;
				$this->transfert_location_origine = $item->transfert_location_origine;
				$this->transfert_statut_origine = $item->transfert_statut_origine;
				$this->transfert_section_origine = $item->transfert_section_origine;
				$this->expl_comment	= $item->expl_comment;
				$this->nbparts		= $item->expl_nbparts;
				$this->expl_retloc	= $item->expl_retloc;
				$this->ref_num = $item->expl_ref_num;
				$this->expl_pnb_flag = $item->expl_pnb_flag;
				
			} else { // rien trouvé en base
				$this->cb = $cb;
				$this->id_notice = intval($id_notice);
				$this->id_bulletin = intval($id_bulletin);
				$this->set_deflt_typdoc_id();
			}
		} else { // rien de fourni apparemment
			$this->cb = $cb;
			$this->id_notice = intval($id_notice);
			$this->id_bulletin = intval($id_bulletin);
		}
		if ($this->id_bulletin) {
			$qb="select bulletin_notice, num_notice from bulletins where bulletin_id='".$this->id_bulletin."' ";
			$rb=pmb_mysql_query($qb);
			if (pmb_mysql_num_rows($rb)) {
				$this->id_bulletin_notice=pmb_mysql_result($rb,0,0);
				$this->id_num_notice=pmb_mysql_result($rb,0,1);
			}
		}
	}
	
	// Donne l'id de la notice par son identifiant d'expl
	public static function get_expl_notice_from_id($expl_id=0) {
		$expl_id = intval($expl_id);
		$query = "select expl_notice, expl_bulletin from exemplaires where expl_id = ".$expl_id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if($row->expl_notice) {
			return $row->expl_notice;
		} else {
			$query = "select num_notice from bulletins where bulletin_id = ".$row->expl_bulletin;
			$result = pmb_mysql_query($query);
			return pmb_mysql_result($result, 0, 'num_notice');				
		}
	}
	
	// Donne l'id du bulletin par son identifiant d'expl
	public static function get_expl_bulletin_from_id($expl_id=0) {
		$expl_id = intval($expl_id);
		$query = "select expl_bulletin from exemplaires where expl_id = ".$expl_id;
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 'expl_bulletin');
	}
	
	/**
	 * retourne un ISBD de l'exemplaire fourni en paramètre
	 * @param number $expl_id
	 * @return string
	 */
	public static function get_expl_isbd($expl_id = 0) {
	    global $msg;
	    
	    $expl_id = intval($expl_id);
	    $query = "SELECT expl_cb, expl_cote FROM exemplaires WHERE expl_id = ".$expl_id;
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
    	    $expl = pmb_mysql_fetch_object($result);
    	    
    	    $isbd = "$expl->expl_cote {$msg['title_separator']}{$msg['number']} $expl->expl_cb";
    	    $expl_notice = static::get_expl_notice_from_id($expl_id);
    	    if (!empty($expl_notice)) {
        	    $mono_display = new notice_affichage($expl_notice);
        	    $mono_display->do_isbd_simple();
        	    $isbd .= " {$msg['title_separator']}" . $mono_display->notice_isbd_simple;
    	    }
    	    return $isbd;
	    }
	    return "";
	}
	
	public function get_notice_title() {
		$title = '';
		if($this->id_bulletin) {
			if($this->id_bulletin_notice) {
				$title .= notice::get_notice_title($this->id_bulletin_notice);
			} else {
				$title .= notice::get_notice_title($this->id_num_notice);
			}
		} else {
			$title .= notice::get_notice_title($this->id_notice);
		}
		return $title;
	}
	
	/**
	 * Définition du type de support par défaut
	 */
	protected function set_deflt_typdoc_id() {
	    if ($this->typdoc_id) {
	        return;
	    }
	    //Utilisé en gestion pour l'affectation de la propriété en fonction de la pref utilisateur
	    return;
	}
}                             
