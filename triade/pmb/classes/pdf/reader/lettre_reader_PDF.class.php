<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_PDF.class.php,v 1.21.2.3.2.1 2025/04/17 15:07:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/lettre_PDF.class.php");

class lettre_reader_PDF extends lettre_PDF {
	
    public $x_biblio_info = 0;
    public $y_biblio_info = 0;
    public $x_lecteur_info = 0;
    public $y_lecteur_info = 0;
    public $x_date_jour = 0;
    public $y_date_jour = 0;
    public $fs_date_jour = 0;
    public $x_date_edition = 0;
    public $y_date_edition = 0;
    public $x_lecteur_adresse = 0;
    public $y_lecteur_adresse = 0;
    public $x_groupe_adresse = 0;
    public $y_groupe_adresse = 0;
    public $x_madame_monsieur = 0;
    public $y_madame_monsieur = 0;
    public $fs_madame_monsieur = 0;
    public $x_after_sign = 0;
    public $y_after_sign = 0;
    public $fs_after_sign = 0;
    public $x_title_list = 0;
    public $y_title_list = 0;
    public $fs_title_list = 0;
    public $x_expl_info = 0;
    public $y_expl_info = 0;
    
	protected $expl_info = array();
	
	protected function _init_PDF() {
		if(!empty($this->get_parameter_value('format_page'))) {
			$this->PDF = pdf_factory::make($this->get_parameter_value('format_page'), $this->unit, array($this->get_parameter_value('largeur_page'), $this->get_parameter_value('hauteur_page')));
		} else {
			$this->PDF = pdf_factory::make('P', 'mm', 'A4');
		}
	}
	
	protected static function get_parameter_prefix() {
		return '';
	}
	
	protected function is_exist_parameter($parameter_name) {
		global ${$parameter_name};
		if(isset(${$parameter_name})) {
			return true;
		}
		return false;
	}
	
	protected function get_evaluated_parameter($parameter_name) {
	    global $biblio_name, $biblio_phone, $biblio_email, $biblio_commentaire;
	
		global $$parameter_name;
		if (!empty(${$parameter_name})) {
    		eval ("\$evaluated=\"".addslashes(${$parameter_name})."\";");
    		if (isset($evaluated)) {
    		    return stripslashes($evaluated);
    		}
		}
		return '';
	}
	
	protected function get_parameter_value($name) {
	    $id_param = $this->get_parameter_id(static::get_parameter_prefix(), $name);
	    $parameter_value = translation::get_translated_text($id_param, 'parametres', 'valeur_param', '', static::$language);
	    if($parameter_value) {
	        return $parameter_value;
	    } else {
	        $parameter_name = static::get_parameter_name($name);
	        return $this->get_evaluated_parameter($parameter_name);
	    }
	}
	
	protected function _init_parameter_value($name, $value) {
		$parameter_name = static::get_parameter_name($name);
		global $$parameter_name;
		if(empty(${$parameter_name})) {
			${$parameter_name} = $value;
		}
	}
	
	protected function _init_position_values($name, $default_values) {
		$parameter_value = $this->get_parameter_value('pos_'.$name);
		if($parameter_value) {
			$position_values = explode(',', $parameter_value);
		} else {
			$position_values = $default_values;
		}
		$this->_init_position($name, $position_values);
	}
	
	protected function _adjust_position($name, $position=array()) {
		if (isset($position[0]) && $position[0]) {
			if(!isset($this->{"x_".$name})) {
				$this->{"x_".$name} = 0;
			}
			$this->{"x_".$name} += $position[0];
		}
		if (isset($position[1]) && $position[1]) {
			if(!isset($this->{"y_".$name})) {
				$this->{"y_".$name} = 0;
			}
			$this->{"y_".$name} += $position[1];
		}
	}
	
	protected function get_query_expl_info($cb_doc) {
		global $msg;
		
		$query = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, expl_cote, expl_prix, pret_date, pret_retour, expl_typdoc, tdoc_libelle, expl_section, section_libelle, expl_location, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
		$query.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$query.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$query.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ;
		$query.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
		$query.= " WHERE expl_cb='".addslashes($cb_doc)."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
		return $query;
	}
	
	protected function get_expl_info($cb_doc) {
		if(empty($this->expl_info[$cb_doc])) {
			$query = $this->get_query_expl_info($cb_doc);
			$result = pmb_mysql_query($query);
			$expl = pmb_mysql_fetch_object($result);
			
			// récupération du titre de série
			if ($expl->tparent_id && $expl->m_id) {
				$parent = new serie($expl->tparent_id);
				$tit_serie = $parent->name;
				if($expl->tnvol) {
					$tit_serie .= ', '.$expl->tnvol;
				}
				$expl->tit = $tit_serie.'. '.$expl->tit;
			}
			if (!empty($expl->expl_location)) {
			    $expl->location_libelle = translation::get_translated_text($expl->expl_location, 'docs_location', 'location_libelle', $expl->location_libelle, static::$language);
			}
			if (!empty($expl->expl_section)) {
			    $expl->section_libelle = translation::get_translated_text($expl->expl_section, 'docs_section', 'section_libelle', $expl->section_libelle, static::$language);
			}
			if (!empty($expl->expl_typdoc)) {
                $expl->tdoc_libelle = translation::get_translated_text($expl->expl_typdoc, 'docs_type', 'tdoc_libelle', $expl->tdoc_libelle, static::$language);
			}
			$this->expl_info[$cb_doc] = $expl;
		}
		return $this->expl_info[$cb_doc];
	}
	
	protected function display_expl_info_notice_description($cb_doc, $short=0, $longmax=99999) {
		global $pmb_pdf_font;
		
		$expl = $this->get_expl_info($cb_doc);
		$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
		$header_aut= gen_authors_header($responsabilites);
		$header_aut ? $auteur=" / ".$header_aut : $auteur="";
		if ($short==1) {
			$this->PDF->setFont($pmb_pdf_font, 'B', 10);
			$this->PDF->multiCell(190, 8, substr($expl->tit.$auteur,0,$longmax) , 0, 'L', 0);
		} else {
			$this->PDF->setFont($pmb_pdf_font, 'BU', 14);
			$nb = $this->PDF->NbLines(190,substr($expl->tit." (".$expl->tdoc_libelle.")",0,$longmax));
			if ($nb > 1) {
				$font_size = $this->PDF->getFontSizePt();
				$font_size--;
				for($s=$font_size; $s>=10; $s--) {
					$this->PDF->setFont($pmb_pdf_font, 'BU', $s);
					$nb = $this->PDF->NbLines(190,substr($expl->tit." (".$expl->tdoc_libelle.")",0,$longmax));
					if ($nb == 1) break;
				}
			}
			$this->PDF->multiCell(190, 8, substr($expl->tit." (".$expl->tdoc_libelle.")",0,$longmax), 0, 'L', 0);
		}
	}
	
	protected function display_expl_info_dates($cb_doc, $short=0) {
		global $msg, $pmb_pdf_font;
		
		$expl = $this->get_expl_info($cb_doc);
		if ($short==1) {
			$this->PDF->SetXY ($this->x_expl_info+10,$this->y_expl_info+4);
			$this->PDF->setFont($pmb_pdf_font, '', 9);
			$this->PDF->multiCell(140, 8, $msg['fpdf_date_pret']." ".$expl->aff_pret_date, 0, 'L', 0);
			$this->PDF->SetXY ($this->x_expl_info+70,$this->y_expl_info+4);
			$this->PDF->setFont($pmb_pdf_font, 'B', 9);
			$this->PDF->multiCell(70, 8, $msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour, 0, 'L', 0);
		} else {
			$this->PDF->SetXY ($this->x_expl_info+10,$this->y_expl_info+6);
			$this->PDF->setFont($pmb_pdf_font, '', 10);
			$this->PDF->multiCell(190-30, 8, $msg['fpdf_date_pret']." ".$expl->aff_pret_date, 0, 'L', 0);
			$this->PDF->SetXY ($this->x_expl_info+70,$this->y_expl_info+6);
			$this->PDF->setFont($pmb_pdf_font, 'B', 10);
			$this->PDF->multiCell((190 - 70), 8, $msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour, 0, 'L', 0);
		}
	}
	
	protected function display_expl_info_description($cb_doc, $short=0) {
		global $pmb_pdf_font;
		
		$expl = $this->get_expl_info($cb_doc);
		if ($short==1) {
			$this->PDF->SetXY ($this->x_expl_info+10,$this->y_expl_info+8);
		} else {
			$this->PDF->SetXY ($this->x_expl_info+10,$this->y_expl_info+10);
		}
		$this->PDF->setFont($pmb_pdf_font, 'I', 8);
		$this->PDF->multiCell(190, 8, strip_tags($expl->location_libelle.": ".parseHTML($expl->section_libelle).": ".$expl->expl_cote." (".$expl->expl_cb.")"), 0, 'L', 0);
	}
	
	protected function display_expl_info($cb_doc, $x=0, $y=0, $short=0, $longmax=99999) {
		$this->_adjust_position('expl_info', array($x));
		//Position y calculée avant l'appel
		$this->y_expl_info = $y;
		
		$this->PDF->SetXY ($this->x_expl_info,$this->y_expl_info);
		$this->display_expl_info_notice_description($cb_doc, $short, $longmax);
		$this->display_expl_info_dates($cb_doc, $short);
		$this->display_expl_info_description($cb_doc, $short);
	}
	
	protected function get_query_not_bull_info_resa($notice, $bulletin) {
		global $msg;
		
		$dates_resa_sql = "date_format(resa_date, '".$msg["format_date"]."') as date_pose_resa, IF(resa_date_fin>sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, if(resa_date_debut='0000-00-00', '', date_format(resa_date_debut, '".$msg["format_date"]."')) as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin " ;
		if ($notice) {
			$query = "SELECT notice_id, resa_date, resa_idempr, tit1 as tit, ".$dates_resa_sql;
			$query.= "FROM notices, resa ";
			$query.= "WHERE notice_id='".$notice."' and resa_idnotice=notice_id order by resa_date ";
		} else {
			$query = "SELECT notice_id, resa_date, resa_idempr, trim(concat(tit1,' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql;
			$query.= "FROM bulletins, resa, notices ";
			$query.= "WHERE resa_idbulletin='$bulletin' and resa_idbulletin = bulletins.bulletin_id and bulletin_notice = notice_id order by resa_date ";
		}
		return $query;
	}
	
	protected function display_not_bull_info_resa($id_empr, $notice, $bulletin, $x=0, $y=0, $longmax=99999) {
		global $msg;
		global $pmb_pdf_font;
	
		$this->_adjust_position('not_bull_info_resa', array($x));
		//Position y calculée avant l'appel
		$this->y_not_bull_info_resa = $y;
		
		
		$query = $this->get_query_not_bull_info_resa($notice, $bulletin);
		$result = pmb_mysql_query($query);
		$nb_resa = pmb_mysql_num_rows($result) ;
	
		for ($j=0 ; $j<$nb_resa ; $j++ ) {
			$resa = pmb_mysql_fetch_object($result);
			if ($resa->resa_idempr == $id_empr) {
				$responsabilites = get_notice_authors($resa->notice_id) ;
				$as = array_search ("0", $responsabilites["responsabilites"]) ;
				if ($as!== FALSE && $as!== NULL) {
					$auteur_0 = $responsabilites["auteurs"][$as] ;
					$auteur = new auteur($auteur_0["id"]);
					$header_aut = $auteur->get_isbd();
				} else {
					$aut1_libelle=array();
					$as = array_keys ($responsabilites["responsabilites"], "1" ) ;
					for ($i = 0 ; $i < count($as) ; $i++) {
						$indice = $as[$i] ;
						$auteur_1 = $responsabilites["auteurs"][$indice] ;
						$auteur = new auteur($auteur_1["id"]);
						$aut1_libelle[]= $auteur->get_isbd();
					}
					$header_aut = implode (", ",$aut1_libelle) ;
				}
				$header_aut ? $auteur=" / ".$header_aut : $auteur="";
	
				$this->PDF->SetXY ($this->x_not_bull_info_resa,$this->y_not_bull_info_resa);
				$this->PDF->setFont($pmb_pdf_font, 'BU', 14);
				$this->PDF->multiCell(190, 8, substr($resa->tit.$auteur,0,$longmax), 0, 'L', 0);
	
				if ($resa->aff_resa_date_debut) $tmpmsg_res = $msg['fpdf_reserve_du']." ".$resa->aff_resa_date_debut." ".$msg['fpdf_adherent_au']." ".$resa->aff_resa_date_fin;
				else $tmpmsg_res = $msg['fpdf_attente_valid'];
				$this->PDF->SetXY ($this->x_not_bull_info_resa+10,$this->y_not_bull_info_resa+6);
				$this->PDF->setFont('Arial', '', 10);
				$this->PDF->multiCell(140, 8, $tmpmsg_res, 0, 'L', 0);
	
				$date_resa = " ".$msg['fpdf_reserv_enreg']." ".$resa->date_pose_resa."." ;
				$this->PDF->SetXY ($this->x_not_bull_info_resa+10,$this->y_not_bull_info_resa+10);
				$this->PDF->setFont('Arial', '', 8);
				$this->PDF->multiCell(140, 8, $msg['fpdf_rang']." ".($j+1).$date_resa, 0, 'L', 0);
				return ;
			}
		} // fin for
	}
	
	protected function display_date_jour($x=0, $y=0) {
		global $msg,$biblio_town;
		
		$this->_adjust_position('date_jour', array($x, $y));
		
		$this->PDF->SetXY ($this->x_date_jour,$this->y_date_jour);
		$this->PDF->setFont($this->font, '', $this->fs_date_jour);
		$c=str_replace("!!ville!!",$biblio_town,$msg['lettre_date_header']);
		$c=str_replace("!!date!!",formatdate(date("Y-m-d",time())),$c);
		$this->PDF->multiCell(100, 8, $c, 0, 'R', 0);
	}
	
	protected function display_date_edition($x=0, $y=0) {
		global $msg;
		global $pmb_pdf_fontfixed;
	
		$this->_adjust_position('date_edition', array($x, $y));
		
		$this->PDF->SetXY ($this->x_date_edition,$this->y_date_edition);
		$this->PDF->setFont($pmb_pdf_fontfixed, 'I', $this->fs_date_edition);
		$this->PDF->multiCell(140, 8, $msg['fpdf_edite']." ".formatdate(date("Y-m-d",time())), 0, 'L', 0);
	}
	
	protected function get_query_group_info($id_empr) {
	    $query = "SELECT group_concat(libelle_groupe SEPARATOR ', ') as_all_groupes, 1 as rien from groupe join empr_groupe on groupe_id=id_groupe WHERE empr_id='$id_empr' ";
	    if(strpos(get_called_class(), 'reader_resa') !== false) {
	        $query .= " AND lettre_resa_show_nomgroup=1";
	    } else {
	        $query .= " AND lettre_rappel_show_nomgroup=1";
	    }
	    $query .= " GROUP BY rien";
	    return $query;
	}
	
	protected function display_lecteur_adresse_nomgroupe($id_empr) {
		$requete = $this->get_query_group_info($id_empr);
		$lib_all_groupes=pmb_sql_value($requete);
		if($lib_all_groupes) {
			$this->PDF->multiCell(100, 6, $lib_all_groupes, 0, 'L', true);
		}
	}
	
	protected function display_lecteur_adresse_name($empr) {
		$this->PDF->multiCell(100, 6, $empr->empr_prenom." ".$empr->empr_nom, 0, 'L', true);
	}
	
	protected function display_lecteur_adresse_coords($empr) {
		$lines = array();
		if ($empr->empr_adr1 != "") {
			$lines['adr1'] = $empr->empr_adr1;
		}
		if ($empr->empr_adr2 != "") {
			$lines['adr2'] = $empr->empr_adr2;
		}
		if (($empr->empr_cp != "") || ($empr->empr_ville != "")) {
			$lines['cp_ville'] = $empr->empr_cp." ".$empr->empr_ville;
		}
		if ($empr->empr_pays != "") {
			$lines['pays'] = $empr->empr_pays;
		}
		if(!empty($lines)) {
			//adresse
			$this->PDF->multiCell(100, 6, implode("\n", $lines), 0, 'L', true);
		}
	}
			
	protected function display_lecteur_adresse($id_empr, $x=0, $y=0, $no_cb=false, $show_nomgroupe=false, $use_param_bloc_adresse=false) {
		global $msg;
		global $pmb_pdf_font;
		global $pmb_afficher_numero_lecteur_lettres;
		
		$this->_adjust_position('lecteur_adresse', array($x, $y));
		
		$x_code = 0;
		$y_code = 0;
		//Vérifions si l'on demande un positionnement absolu
		if ($use_param_bloc_adresse) {
			global $pmb_lettres_bloc_adresse_position_absolue;
			$absolue_config = explode(" ", $pmb_lettres_bloc_adresse_position_absolue);
			if ((count($absolue_config) == 3) && ($absolue_config[0] != 0)) {
				$this->x_lecteur_adresse = intval($absolue_config[1]);
				$this->y_lecteur_adresse = intval($absolue_config[2]);
			}
	
			global $pmb_lettres_code_mail_position_absolue;
			$absolue_config_code = explode(" ", $pmb_lettres_code_mail_position_absolue);
			if ((count($absolue_config_code) == 3) && ($absolue_config_code[0] != 0)) {
				$x_code = intval($absolue_config_code[1]);
				$y_code = intval($absolue_config_code[2]);
			}
		}
		$concerne="";
		$temp_id_empr=$id_empr;
		if($show_nomgroupe) {
			//Recherche du groupe d'appartenance
			$requete="SELECT id_groupe,resp_groupe FROM groupe,empr_groupe WHERE id_groupe=groupe_id AND empr_id=$id_empr AND resp_groupe ";
            if(strpos(get_called_class(), 'reader_resa') !== false) {
                $requete .= " AND lettre_resa ";
            } else {
                $requete .= " AND lettre_rappel ";
            }
            $requete .= " LIMIT 1";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)) {
				$temp_id_empr=pmb_mysql_result($res,0,1);
			} else  $temp_id_empr=$id_empr;
	
			//Si le responsable n'est pas l'emprunteur, on précise qui est relancé
			if ($temp_id_empr!=$id_empr) {
				$requete="select concat(empr_prenom,' ',empr_nom) from empr where id_empr=$id_empr"; //Idée de Quentin
				$res=pmb_mysql_query($requete);
				if(static::class == 'lettre_reader_resa_PDF') {
				    $concerne=sprintf($msg["adresse_resa_concerne"],pmb_mysql_result($res,0,0))."\n";
				} else {
				    $concerne=sprintf($msg["adresse_retard_concerne"],pmb_mysql_result($res,0,0))."\n";
				}
			}
		}
	
		$requete = "SELECT id_empr, empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2  FROM empr WHERE id_empr='$temp_id_empr' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$empr = pmb_mysql_fetch_object($res);
	
		$this->PDF->SetXY ($this->x_lecteur_adresse,$this->y_lecteur_adresse);
		$this->PDF->setFont($pmb_pdf_font, '', 12);
	
		$this->PDF->SetDrawColor(255,255,255);
		$this->PDF->SetFillColor(255,255,255);
		if($show_nomgroupe!=false) {
			$this->display_lecteur_adresse_nomgroupe($id_empr);
			$this->PDF->SetXY ($this->x_lecteur_adresse,$this->PDF->GetY());
		}
		$this->display_lecteur_adresse_name($empr);
		$this->PDF->SetXY ($this->x_lecteur_adresse,$this->PDF->GetY());
		$this->display_lecteur_adresse_coords($empr);
		if ($no_cb==false || $concerne !="") {
			$no_cb_empr = $empr->empr_cb." ".$empr->empr_mail."\n";
			$this->PDF->SetXY (($x_code ? $x_code : $this->x_lecteur_adresse),($y_code ? $this->PDF->GetY()+$y_code :$this->PDF->GetY()));
			$this->PDF->setFont($pmb_pdf_font, 'I', 10);
			$this->PDF->multiCell(100, 6, ($pmb_afficher_numero_lecteur_lettres ? $msg['fpdf_carte']." ".$no_cb_empr : "").$concerne, 0, 'L', true);
		}
	}
	
	protected function get_query_groupe_adresse($id_groupe) {
		return "SELECT libelle_groupe, resp_groupe  FROM groupe WHERE id_groupe='$id_groupe' ";
	}
	
	protected function display_groupe_adresse($id_groupe, $x=0, $y=0, $no_cb=false) {
		global $pmb_pdf_font;
		global $pmb_afficher_numero_lecteur_lettres;
	
		$this->_adjust_position('groupe_adresse', array($x, $y));
		
		$query = $this->get_query_groupe_adresse($id_groupe);
		$result = pmb_mysql_query($query);
		$groupe = pmb_mysql_fetch_object($result);
	
		$this->PDF->SetXY ($this->x_groupe_adresse,$this->y_groupe_adresse);
		$this->PDF->setFont($pmb_pdf_font, '', $this->fs_groupe_adresse);
		$this->PDF->multiCell(100, 8, $groupe->libelle_groupe, 0, 'L', 0);
	
		if ($groupe->resp_groupe) {
		    $y=$y+8;
			$this->display_lecteur_adresse($groupe->resp_groupe, $x,$y, $no_cb || !$pmb_afficher_numero_lecteur_lettres) ;
		}
	}
	
	protected function display_lecteur_info_name($empr, $droite=0) {
		
		if ($droite) $this->PDF->multiCell(100, 8, $empr->empr_prenom." ".$empr->empr_nom, 0, 'R', 0);
		else $this->PDF->multiCell(100, 8, $empr->empr_prenom." ".$empr->empr_nom, 0, 'L', 0);
	}
	
	protected function display_lecteur_info_coords($empr) {
		$lines = array();
		if ($empr->empr_adr1 != "") {
			$lines['adr1'] = $empr->empr_adr1;
		}
		if ($empr->empr_adr2 != "") {
			$lines['adr2'] = $empr->empr_adr2;
		}
		if (($empr->empr_cp != "") || ($empr->empr_ville != "")) {
			$lines['cp_ville'] = $empr->empr_cp." ".$empr->empr_ville;
		}
		if ($empr->empr_pays != "") {
			$lines['pays'] = $empr->empr_pays;
		}
		if(!empty($lines)) {
			$this->PDF->multiCell(100, 8, implode("\n", $lines), 0, 'L', 0);
		}
		
	}
	
	protected function display_lecteur_info($id_empr, $x=0, $y=0, $short=0, $droite=0,$use_param_bloc_adresse=false) {
		global $msg;
		global $pmb_afficher_numero_lecteur_lettres;
	
		$this->_adjust_position('lecteur_info', array($x, $y));
		
		if ($use_param_bloc_adresse) {
			//Vérifions si l'on demande un positionnement absolu
			global $pmb_lettres_bloc_adresse_position_absolue;
			$absolue_config = explode(" ", $pmb_lettres_bloc_adresse_position_absolue);
			if ((count($absolue_config) == 3) && ($absolue_config[0] != 0)) {
				$this->x_lecteur_info = intval($absolue_config[1]);
				$this->y_lecteur_info = intval($absolue_config[2]);
			}
		}
	
		$requete = "SELECT id_empr, empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_date_adhesion, empr_date_expiration, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration FROM empr WHERE id_empr='$id_empr' ";
		$res = pmb_mysql_query($requete);
		$empr = pmb_mysql_fetch_object($res);
	
		$requete = $this->get_query_group_info($id_empr);
		$lib_all_groupes=pmb_sql_value($requete);
		if ($lib_all_groupes) $lib_all_groupes="\n".$lib_all_groupes;
	
		$this->PDF->SetXY ($this->x_lecteur_info,$this->y_lecteur_info);
		$this->PDF->setFont($this->font, 'B', $this->fs_lecteur_info);
		$this->display_lecteur_info_name($empr, $droite);
	
		if ($short==1) return ;
	
		$this->PDF->SetXY ($this->x_lecteur_info,$this->y_lecteur_info+8);
		$this->PDF->setFont($this->font, '', $this->fs_lecteur_info);
		$this->display_lecteur_info_coords($empr);
		
		$tel = "";
		if ($empr->empr_tel1 != "") {
			$tel = $msg['fpdf_tel']." ".$empr->empr_tel1." " ;
		}
		if ($empr->empr_tel2 != "") {
			$tel = $tel.$msg['fpdf_tel2']." ".$empr->empr_tel2;
		}
		if ($empr->empr_mail != "") {
			if ($tel) $tel = $tel."\n" ;
			$mail = $msg['fpdf_email']." ".$empr->empr_mail;
		} else {
			$mail = "";
		}
	
		$this->PDF->SetXY ($this->x_lecteur_info,$this->y_lecteur_info+32);
		$this->PDF->setFont($this->font, '', $this->fs_lecteur_info);
		$this->PDF->multiCell(100, 7, "\n".$tel.$mail.$lib_all_groupes, 0, 'L', 0);
	
		$this->PDF->SetXY ($this->x_lecteur_info,$this->y_lecteur_info+58);
		$this->PDF->setFont($this->font, 'I', $this->fs_lecteur_info);
		$this->PDF->multiCell(100, 7, ($pmb_afficher_numero_lecteur_lettres ? $msg['fpdf_carte']." ".$empr->empr_cb : "")."\n".$msg['fpdf_adherent']." ".$empr->aff_empr_date_adhesion." ".$msg['fpdf_adherent_au']." ".$empr->aff_empr_date_expiration.".", 0, 'L', 0);
	}
	
	protected function get_lines_text_biblio_info() {
		global $msg;
		global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website;
		
		$lines = array();
		if($biblio_adr1) {
			$lines['adr1'] = trim($biblio_adr1);
		}
		if ($biblio_adr2 != "") {
			$lines['adr2'] = trim($biblio_adr2);
		}
		if (($biblio_cp != "") || ($biblio_town != "")) {
			$lines['cp_town'] = trim($biblio_cp." ".$biblio_town);
		}
		if (($biblio_state != "") || ($biblio_country != "")) {
			$lines['state_country'] = trim($biblio_state." ".$biblio_country);
		}
		if ($biblio_phone != "") {
			$lines['phone'] = $msg['lettre_titre_tel'].$biblio_phone;
		}
		if ($biblio_email != "") {
			$lines['email'] = $msg['lettre_biblio_info_email'].$biblio_email;
		}
		if ($biblio_website != "") {
			$lines['website'] = $msg['lettre_biblio_info_website'].$biblio_website;
		}
		return $lines;
	}
	
	protected function get_display_text_biblio_info() {
		$lines = $this->get_lines_text_biblio_info();
		$txt_biblio_info = "";
		if(!empty($lines)) {
			$txt_biblio_info = implode("\n", $lines);
		}
		return $txt_biblio_info;
	}
	
	protected function display_biblio_name($x=0, $y=0, $short=0) {
	    global $biblio_name;
	    global $pmb_pdf_font;
	    
	    $this->PDF->setFont($pmb_pdf_font, 'B', 16);
	    if ($short==1) {
	        $this->PDF->multiCell(120, 8, $biblio_name, 0, 'L', 0);
	    } else {
	        $this->PDF->multiCell(120, 8, $biblio_name, 0, 'C', 0);
	    }
	}
	
	protected function display_biblio_info($x=0, $y=0, $short=0) {
		global $base_path;
		global $pmb_hide_biblioinfo_letter;
		global $biblio_logo;
		global $txt_biblio_info ;
		global $pmb_pdf_font;
	
		if($pmb_hide_biblioinfo_letter) {
			return false;
		}
		$this->_adjust_position('biblio_info', array($x, $y));
		if ($short==1) {
		    $this->PDF->SetXY ($this->x_biblio_info,$this->y_biblio_info);
		    $this->display_biblio_name($x, $y, $short);
		} else {
			// afin de ne générer qu'une fois l'adr et compagnie
			if (!$txt_biblio_info) {
				$txt_biblio_info = $this->get_display_text_biblio_info();
			}
	
			if ($biblio_logo) {
				$this->PDF->Image($base_path."/images/".$biblio_logo, $this->x_biblio_info, $this->y_biblio_info );
				$this->PDF->SetXY ($this->x_biblio_info,$this->y_biblio_info+50);
			} else {
				$this->PDF->SetXY ($this->x_biblio_info,$this->y_biblio_info+10);
			}
			$this->PDF->setFont($pmb_pdf_font, '', 9);
			$this->PDF->multiCell(0, 5, $txt_biblio_info, 0, 'L', 0);
	
			$this->PDF->SetXY ($this->x_biblio_info+60,$this->y_biblio_info);
			$this->display_biblio_name($x, $y, $short);
		}
	}
	
	protected function display_objet($x=0, $y=0) {
		global $msg;
		
		$this->_adjust_position('objet', array($x,$y));
		$text_objet = $this->get_parameter_value('objet');
		if(!empty($text_objet)) {
			$this->PDF->SetXY ($this->x_objet,$this->y_objet);
			$this->PDF->setFont($this->font, 'B', $this->fs_objet);
			$this->PDF->multiCell($this->w, 8, $msg['fpdf_objet']." ".$text_objet, 0, 'L', 0);
		}
	}
	
	protected function get_text_madame_monsieur($id) {
		$id = intval($id);
		$query = "select empr_nom, empr_prenom from empr where id_empr='".$id."'";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$text_madame_monsieur=str_replace("!!empr_name!!", $row->empr_nom,$this->get_parameter_value('madame_monsieur'));
		$text_madame_monsieur=str_replace("!!empr_first_name!!", $row->empr_prenom,$text_madame_monsieur);
		return $text_madame_monsieur;
	}
	
	protected function display_madame_monsieur($id_empr, $x=0, $y=0) {
		$this->_adjust_position('madame_monsieur', array($x,$y));
		$text_madame_monsieur = $this->get_text_madame_monsieur($id_empr);
		$this->PDF->SetXY ($this->x_madame_monsieur,$this->y_madame_monsieur);
		$this->PDF->setFont($this->font, '', $this->fs_madame_monsieur);
		$this->PDF->multiCell($this->w, 8, $text_madame_monsieur, 0, 'L', 0);
	}
	
	protected function display_after_sign($x=0, $y=0) {
		$this->_adjust_position('after_sign', array($x,$y));
		$text_after_sign = $this->get_parameter_value('after_sign');
		if(!empty($text_after_sign)) {
			$this->PDF->setFont($this->font, '', $this->fs_after_sign);
			$this->PDF->multiCell($this->w, 5, $text_after_sign, 0, 'L', 0);
		}
	}
}