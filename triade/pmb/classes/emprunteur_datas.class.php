<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emprunteur_datas.class.php,v 1.7.4.1 2025/04/24 12:37:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $lang, $renewal_form_fields, $subscribe_form_fields;

require_once $class_path."/emprunteur.class.php";
require_once $class_path."/parametres_perso.class.php";

/**
 * Classe qui représente les données d'un emprunteur
 * @author dbellamy
 *
*/
class emprunteur_datas {

	/**
	 * Identifiant de l'emprunteur
	 * @var int
	 */
	private $id;

	/**
	 * Tableau emprunteur fetché en base
	 * @var array
	 */
	public $emprunteur;

	/**
	 * Paramètres persos
	 * @array p_perso
	 */
	private $p_perso;

	protected $p_perso_values;
	protected $json_enabled_password_rules;

	public $opac_websubscribe_valid_limit;
	public $captcha;

	public function __construct($id) {
		$this->id = intval($id);
		if (!$this->id) return;
	}


	/**
	 * Charge les infos présentes en base de données
	 */
	private function fetch_data() {
		global $msg;
		$query = "SELECT *,
                    DATE_FORMAT(NOW(), '".$msg["format_date"]."') AS aff_empr_day_date,
                    DATE_FORMAT(empr_date_adhesion, '".$msg["format_date"]."') AS aff_empr_date_adhesion,
                    DATE_FORMAT(empr_date_expiration, '".$msg["format_date"]."') AS aff_empr_date_expiration,
                    DATEDIFF(empr_date_expiration, CURDATE()) AS nb_days_before_expiration
				FROM empr WHERE id_empr='".$this->id."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$this->emprunteur = pmb_mysql_fetch_assoc($result);
		}
	}


	/**
	 * Retourne les paramètres persos
	 * @return array
	 */
	public function get_p_perso() {

		if (!isset($this->p_perso)) {
			global $memo_p_perso_emprunteurs;

			if (!$memo_p_perso_emprunteurs) {
				$memo_p_perso_emprunteurs = new parametres_perso("empr");
			}
			$this->p_perso = array();

			$renewal_form_fields = [];
			if (class_exists("emprunteur_display")) {
				// Seulement en OPAC
				$renewal_form_fields = emprunteur_display::get_renewal_form_fields();
			}

			//affichage
			$ppersos = $memo_p_perso_emprunteurs->show_fields($this->id);
			if (!$this->id) {
			    $ppersos_obj = new parametres_perso('empr');
			    foreach ($ppersos_obj->t_fields as $key => $val) {
			        foreach ($ppersos['FIELDS'] as $key_pperso =>$pperso) {
			            if ($pperso['NAME'] == $val['NAME'] ) {
			                $ppersos['FIELDS'][$key_pperso]['EDIT'] = $ppersos_obj->get_field_form_whith_form_value($val['idchamp']);
			            }
			        }
			    }
			}

			//on filtre ceux qui ne sont pas visibles à l'OPAC
			if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS']) && count($ppersos['FIELDS'])){
				foreach ($ppersos['FIELDS'] as $pperso) {
					if ($pperso['OPAC_SHOW'] ) {
						$this->p_perso[$pperso['NAME']] = $pperso;
						if (!empty($renewal_form_fields[$pperso['NAME']])) {
							$this->p_perso[$pperso['NAME']]['renewal_form_field'] = $renewal_form_fields[$pperso['NAME']];
						}
					}
				}
			}
			//edition
			$ppersos = $memo_p_perso_emprunteurs->show_editable_fields($this->id);
			//on filtre ceux qui ne sont pas visibles à l'OPAC
			if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS']) && count($ppersos['FIELDS'])){
				foreach ($ppersos['FIELDS'] as $pperso) {
				    if ($this->id && isset($this->p_perso[$pperso['NAME']]) ) {
						$this->p_perso[$pperso['NAME']]['EDIT'] = $pperso['AFF'];
					}
				}
			}
			$this->p_perso['CHECK_SCRIPTS'] = $ppersos['CHECK_SCRIPTS'];
		}
		return $this->p_perso;
	}

	/**
	 * Retourne l'identifiant de l'emprunteur
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	public function __get($name) {
		if (!isset($this->emprunteur)) {
			$this->fetch_data();
		}
		if(is_string($name) && !empty($this->emprunteur[$name])) {
			return $this->emprunteur[$name];
		}
		if (method_exists($this, "get_".$name)) {
			return call_user_func_array(array($this, "get_".$name), array());
		}
		return '';
	}


	public function get_json_enabled_password_rules() {

		global $lang;
		if(!isset($this->json_enabled_password_rules)) {
			$this->json_enabled_password_rules = emprunteur::get_json_enabled_password_rules($this->id, $lang);
		}
		return $this->json_enabled_password_rules;
	}


	public function set_from_form() {
	    global $renewal_form_fields, $subscribe_form_fields;

		$this->emprunteur = $renewal_form_fields;
		if (empty($renewal_form_fields)) {
		    $this->emprunteur = $subscribe_form_fields;
		}

		foreach ($this->emprunteur as $field => $value) {
		    $this->emprunteur[$field] = stripslashes($value);
		}
		//on controle les donnees postees
		$this->check_posted_empr_fields();

		$this->get_p_perso();
		$this->p_perso_values = array();
		foreach ($this->p_perso as $p_perso) {
			if (empty($p_perso['NAME'])) {
				continue;
			}
			global ${$p_perso['NAME']};
			if (empty(${$p_perso['NAME']})) {
			    continue;
			}
			$values = array();
			foreach (${$p_perso['NAME']} as $value) {
			    $values[]= stripslashes($value);
			}
			$this->p_perso_values[$p_perso['NAME']] = array(
					"id" => $p_perso["ID"],
					"datatype" => $p_perso['DATATYPE'],
			        "values" => $values
			);
		}
	}

	public function save() {
		$values = array();
		foreach ($this->emprunteur as $field => $value) {
			$values[] = "$field='".addslashes($value)."'";
		}
		if (!empty($values)) {
    		pmb_mysql_query("UPDATE empr SET ".implode(",", $values)." WHERE id_empr = $this->id");
		}

		foreach ($this->p_perso_values as $p_perso) {
			pmb_mysql_query("DELETE FROM empr_custom_values WHERE empr_custom_champ = ".$p_perso["id"]." AND empr_custom_origine = $this->id");
			$values = array();
			foreach ($p_perso['values'] as $value) {
				$values[]= "(".$p_perso["id"].", ".$this->id.", '".addslashes($value)."')";
			}
			pmb_mysql_query("INSERT INTO empr_custom_values (empr_custom_champ, empr_custom_origine, empr_custom_".$p_perso['datatype'].") VALUES ".implode(",", $values));
		}
	}

	/**
	 * Limite les modifications aux champs définis en gestion
	 */
	protected function check_posted_empr_fields() {
	    if (!empty($this->emprunteur)) {
	        $cleaned_fields = [];
    	    $query = "SELECT empr_renewal_form_field_code
                    FROM empr_renewal_form_fields
                    WHERE empr_renewal_form_field_display = 1";
    	    $result = pmb_mysql_query($query);
    	    if (pmb_mysql_num_rows($result)) {
    	        while ($row = pmb_mysql_fetch_array($result)) {
    	            if (isset($this->emprunteur[$row[0]])) {
    	                $cleaned_fields[$row[0]] = $this->emprunteur[$row[0]];
    	            }
    	        }
    	    }
            $this->emprunteur = $cleaned_fields;
	    }
	}

	public function m_lecteur_info() {
	    global $msg;

	    if (!isset($this->emprunteur)) {
	    	$this->fetch_data();
	    }
	    $res_final=array();
	    $requete = "SELECT group_concat(libelle_groupe SEPARATOR ', ') as_all_groupes, 1 as rien from groupe join empr_groupe on groupe_id=id_groupe WHERE lettre_rappel_show_nomgroup=1 and empr_id='".$this->emprunteur['id_empr']."' group by rien ";
	    $lib_all_groupes=pmb_sql_value($requete);
	    if ($lib_all_groupes) $lib_all_groupes="\n".$lib_all_groupes;

	    if ($this->emprunteur['empr_prenom']) $this->emprunteur['empr_nom']=$this->emprunteur['empr_prenom']." ".$this->emprunteur['empr_nom'];
	    $res_final[]=$this->emprunteur['empr_nom'];

	    if ($this->emprunteur['empr_adr2'] != "") $this->emprunteur['empr_adr1'] = $this->emprunteur['empr_adr1']."\n" ;
	    if (($this->emprunteur['empr_cp'] != "") || ($this->emprunteur['empr_ville'] != "")) $this->emprunteur['empr_adr2'] = $this->emprunteur['empr_adr2']."\n" ;
	    $adr = $this->emprunteur['empr_adr1'].$this->emprunteur['empr_adr2'].$this->emprunteur['empr_cp']." ".$this->emprunteur['empr_ville'] ;
	    if ($this->emprunteur['empr_pays'] != "") $adr = $adr."\n".$this->emprunteur['empr_pays'] ;
	    $res_final[]=$adr;

	    $tel = '';
	    if ($this->emprunteur['empr_tel1'] != "") {
	    	$tel = $tel.$msg['fpdf_tel']." ".$this->emprunteur['empr_tel1']." " ;
	    }
	    if ($this->emprunteur['empr_tel2'] != "") {
	    	$tel = $tel.$msg['fpdf_tel2']." ".$this->emprunteur['empr_tel2'];
	    }
	    if ($this->emprunteur['empr_mail'] != "") {
	        if ($tel) $tel = $tel."\n" ;
	        $mail = $msg['fpdf_email']." ".$this->emprunteur['empr_mail'];
	    }

	    $res_final[]="\n".$tel.$mail.$lib_all_groupes;
	    $res_final[]="";
	    $res_final[]=$msg['fpdf_carte']." ".$this->emprunteur['empr_cb'];
	    $res_final[]=$msg['fpdf_adherent']." ".$this->emprunteur['aff_empr_date_adhesion']." ".$msg['fpdf_adherent_au']." ".$this->emprunteur['aff_empr_date_expiration'] ;

	    return implode("\n",$res_final);

	} /* fin m_lecteur_info */

	// ********************* Imprime l'adresse d'un lecteur **********************************
	public function m_lecteur_adresse() {
	    global $msg;

	    if (!isset($this->emprunteur)) {
	    	$this->fetch_data();
	    }
	    $res_final=array();

	    if ($this->emprunteur['empr_prenom']) $this->emprunteur['empr_nom']=$this->emprunteur['empr_prenom']." ".$this->emprunteur['empr_nom'];
	    $res_final[]=$this->emprunteur['empr_nom'];

	    if ($this->emprunteur['empr_adr2'] != "") $this->emprunteur['empr_adr1'] = $this->emprunteur['empr_adr1']."\n" ;
	    if (($this->emprunteur['empr_cp'] != "") || ($this->emprunteur['empr_ville'] != "")) $this->emprunteur['empr_adr2'] = $this->emprunteur['empr_adr2']."\n" ;
	    $adr = $this->emprunteur['empr_adr1'].$this->emprunteur['empr_adr2'].$this->emprunteur['empr_cp']." ".$this->emprunteur['empr_ville'] ;
	    if ($this->emprunteur['empr_pays'] != "") $adr = $adr."\n".$this->emprunteur['empr_pays'];
	    $res_final[]=$adr;

	    $tel = '';
	    if ($this->emprunteur['empr_tel1'] != "") {
	    	$tel = $tel.$msg['fpdf_tel']." ".$this->emprunteur['empr_tel1']." " ;
	    }
	    if ($this->emprunteur['empr_tel2'] != "") {
	    	$tel = $tel.$msg['fpdf_tel2']." ".$this->emprunteur['empr_tel2'];
	    }
	    if ($this->emprunteur['empr_mail'] != "") {
	        if ($tel) $tel = $tel."\n" ;
	        $mail = $msg['fpdf_email']." ".$this->emprunteur['empr_mail'];
	    }

	    $res_final[]="\n".$tel.$mail;

	    return implode("\n",$res_final);
	} /* fin m_lecteur_adresse */


	// Liste des prêts en cours
	public function m_liste_prets($late_only = false) {
	    global $msg;

	    if (!isset($this->emprunteur)) {
	    	$this->fetch_data();
	    }
	    $res_final=array();
	    $critere_late = "";
	    if ($late_only) {
	        $critere_late = " AND pret_retour<CURDATE()";
	    }
	    // $rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$this->emprunteur['id_empr']."' and pret_idexpl=expl_id order by pret_date " ;
	    $requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, expl_cote, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
	    $requete.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
	    $requete.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
	    $requete.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ;
	    $requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
	    $requete.= " WHERE pret_idempr='".$this->emprunteur['id_empr']."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
	    $requete.= $critere_late;

	    $req = pmb_mysql_query($requete) or die($msg['err_sql'].'<br />'.$requete.'<br />'.pmb_mysql_error());
	    while ($expl = pmb_mysql_fetch_object($req)) {

	        $responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
	        $header_aut = gen_authors_header($responsabilites);
	        $header_aut ? $auteur=" / ".$header_aut : $auteur="";

	        // récupération du titre de série
	        $tit_serie = '';
	        if ($expl->tparent_id && $expl->m_id) {
	            $parent = new serie($expl->tparent_id);
	            $tit_serie = $parent->name;
	            if($expl->tnvol) $tit_serie .= ', '.$expl->tnvol;
	        }
	        if($tit_serie) $expl->tit = $tit_serie.'. '.$expl->tit;

	        $res_final[]="<b>".$expl->tit."</b> (".$expl->tdoc_libelle.")";
	        $res_final[]="<blockquote>".$msg['fpdf_date_pret']." ".$expl->aff_pret_date."&nbsp;&nbsp;".$msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour;
	        $res_final[]=$expl->location_libelle.": ".$expl->section_libelle.": ".$expl->expl_cote." (".$expl->expl_cb.")</blockquote>";
	    }
	    return implode("\n",$res_final);
	}

	// Liste des réservations en cours
	public function m_liste_resas() {
	    global $msg;
	    if (!isset($this->emprunteur)) {
	    	$this->fetch_data();
	    }
	    $rqt = "select resa_idnotice, resa_idbulletin from resa where resa_idempr='".$this->emprunteur['id_empr']."' " ;
	    $req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
	    $all_resa="";
	    while ($data = pmb_mysql_fetch_array($req)) {
	        $all_resa.=$this->m_not_bull_info_resa ($this->emprunteur['id_empr'], $data['resa_idnotice'],$data['resa_idbulletin']);
	    }
	    return $all_resa ;
	} // fin if résas

	// Liste des réservations confirmées
	public function m_liste_resas_confirme() {
	    global $msg;
	    if (!isset($this->emprunteur)) {
	    	$this->fetch_data();
	    }
	    $rqt = "select resa_idnotice, resa_idbulletin from resa where resa_idempr='".$this->emprunteur['id_empr']."' AND resa_confirmee=1" ;
	    $req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
	    $all_resa="";
	    while ($data = pmb_mysql_fetch_array($req)) {
	        $all_resa.=$this->m_not_bull_info_resa ($this->emprunteur['id_empr'], $data['resa_idnotice'],$data['resa_idbulletin']);
	    }
	    return $all_resa ;
	} // fin if résas

	// Liste des réservations non confirmées
	public function m_liste_resas_not_confirme() {
	    global $msg;
	    if (!isset($this->emprunteur)) {
	    	$this->fetch_data();
	    }
	    $rqt = "select resa_idnotice, resa_idbulletin from resa where resa_idempr='".$this->emprunteur['id_empr']."' AND resa_confirmee=0" ;
	    $req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
	    $all_resa="";
	    while ($data = pmb_mysql_fetch_array($req)) {
	        $all_resa.=$this->m_not_bull_info_resa ($this->emprunteur['id_empr'], $data['resa_idnotice'],$data['resa_idbulletin']);
	    }
	    return $all_resa ;
	} // fin if résas

	public function m_not_bull_info_resa ($id_empr, $notice, $bulletin) {
	    global $msg;

	    $res_final=array();
	    $dates_resa_sql = "date_format(resa_date, '".$msg["format_date"]."') as date_pose_resa, IF(resa_date_fin>sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, if(resa_date_debut='0000-00-00', '', date_format(resa_date_debut, '".$msg["format_date"]."')) as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin " ;
	    if ($notice) {
	        $requete = "SELECT notice_id, resa_date, resa_idempr, tit1 as tit, ".$dates_resa_sql;
	        $requete.= "FROM notices, resa ";
	        $requete.= "WHERE notice_id='".$notice."' and resa_idnotice=notice_id order by resa_date ";
	    } else {
	        $requete = "SELECT notice_id, resa_date, resa_idempr, trim(concat(tit1,' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql;
	        $requete.= "FROM bulletins, resa, notices ";
	        $requete.= "WHERE resa_idbulletin='$bulletin' and resa_idbulletin = bulletins.bulletin_id and bulletin_notice = notice_id order by resa_date ";
	    }

	    $res = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error());
	    $nb_resa = pmb_mysql_num_rows($res) ;

	    for ($j=0 ; $j<$nb_resa ; $j++ ) {
	        $resa = pmb_mysql_fetch_object($res);
	        if ($resa->resa_idempr == $id_empr) {
	            $responsabilites = get_notice_authors($resa->notice_id) ;
	            $header_aut = gen_authors_header($responsabilites);
	            $header_aut ? $auteur=" / ".$header_aut : $auteur="";

	            if ($resa->aff_resa_date_debut) $tmpmsg_res = $msg['fpdf_reserve_du']." ".$resa->aff_resa_date_debut." ".$msg['fpdf_adherent_au']." ".$resa->aff_resa_date_fin;
	            else $tmpmsg_res = $msg['fpdf_attente_valid'];

	            $res_final[]="<b>".$resa->tit.$auteur."</b>";
	            $res_final[]="<blockquote>".$tmpmsg_res;
	            $date_resa = " ".$msg['fpdf_reserv_enreg']." ".$resa->date_pose_resa."." ;
	            $res_final[]=$msg['fpdf_rang']." ".($j+1).$date_resa."</blockquote>";
	        }
	    } // fin for
	    return implode("\n",$res_final);
	} /* fin not_bull_info_resa */
}