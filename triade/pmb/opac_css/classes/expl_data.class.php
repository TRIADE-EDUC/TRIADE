<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_data.class.php,v 1.2 2024/01/12 16:00:45 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe de donees d'un exemplaire
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/bulletin_affichage.inc.php");

class expl_data {
	
	public $expl_id = 0;
	public $cb = '';
	public $id_notice = 0;
	public $id_bulletin = 0;
	public $id_bulletin_notice = 0;
	public $id_num_notice = 0;
	public $notice_title = '';
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
	
	public $ajax_cote_fields = '';
	public $error = false;
	public static $digital_ids = array();
	
	protected static $properties;
	protected static $custom_fields;
	protected static $expl_data;
	
	// constructeur
	public function __construct($cb = '', $id = 0) {	
		global $pmb_sur_location_activate;
		
		// on checke si l'exemplaire est connu
		if ($cb && !$id) $clause_where = " WHERE expl_cb like '$cb' ";
		
		if ((!$cb && $id) || ($cb && $id)) $clause_where = " WHERE expl_id = '$id' ";
		
		if ($cb || $id) {
			$requete = "SELECT * FROM exemplaires 
					LEFT JOIN docs_type ON (idtyp_doc = expl_typdoc)";
			$requete.= $clause_where ;
			$result = @pmb_mysql_query($requete);
	
			if (pmb_mysql_num_rows($result)) {
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
				
				if ($pmb_sur_location_activate) {
					$requete = "SELECT surloc_libelle, surloc_id FROM sur_location WHERE surloc_id='".$item->expl_location."' LIMIT 1";
					$res = pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($res)) {
					    $row = pmb_mysql_fetch_object($res);					    
					    $this->sur_loc_libelle = $row->surloc_libelle;
					    $this->sur_loc_id = $row->surloc_id;
					}					
				}		
			    $this->notice_title = $this->get_notice_title();
			}
		}
	}	
	
	public function get_notice_title() {
		if ($this->id_bulletin) {
		    return bulletin_header($this->id_bulletin);
		}
		return notice::get_notice_title($this->id_notice);
	}
	
	public static function get_getters($methods_list = array()) {
	    $getters = array();
	    foreach ($methods_list as $method) {
	        if ((strpos($method, 'get') === 0) || (strpos($method, 'is') === 0)) {
	            $getters[] = preg_replace('/get_|get/', '', $method);
	        }
	    }
	    return $getters;
	}
	
	public static function get_opac_displayable_custom_fields($prefix = 'expl') {
	    if (!isset(static::$custom_fields)) {
	        static::$custom_fields = array();
	        if ($prefix == "titre_uniforme") {
	            $prefix = "tu";
	        } else if($prefix == "category") {
	            $prefix = "categ";
	        }
	        $parametres_perso = new parametres_perso($prefix);
	        $fields = $parametres_perso->get_t_fields();
	        foreach ($fields as $field) {
	            if ($field['OPAC_SHOW']) {
	                static::$custom_fields[] = $field;
	            }
	        }
	    }
	    return static::$custom_fields;
	}
	
	public static function get_properties($prefix) {
	    if (!isset(self::$properties)) {
	        static::$properties = array();
	        $props = array_keys(get_class_vars('expl_data')); 
	        $methods = get_class_methods('expl_data');
	        $methods = static::get_getters($methods);	        
	        $properties = array_unique(array_merge($props, $methods));
	        sort($properties);
	        $final_properties = array();
	        foreach ($properties as $property) {
	            /**
	             * TODO: ajouter un message cohérent en fonction de la propriété
	             */
	            if ($property != "properties") {
	                $final_properties[] = array(
	                    'var' => "$prefix.$property",
	                    'desc' => "aut_$property"
	                );
	                if($property == "p_perso"){
	                    $custom_fields = static::get_opac_displayable_custom_fields();
	                    $custom_fields_props = array();
	                    
	                    foreach($custom_fields as $field){
	                        $custom_fields_props[] = array(
	                            'var' => $prefix . '.' . $property . '.' . $field['NAME'],
	                            'desc' => $field['TITRE']
	                        );
	                    }
	                    $final_properties[count($final_properties)-1]['children'] = $custom_fields_props;
	                }
	            }
	        }
	        self::$properties = $final_properties;
	    }
	    return self::$properties;
	}
	
	/**
	 * Retourne une instance de record_datas
	 * @param int $notice_id Identifiant de la notice
	 * @return record_datas
	 */
	static public function get_record_data($expl_id) {
	    if (!isset(self::$expl_data[$expl_id])) {
	        self::$expl_data[$expl_id] = new expl_data('', $expl_id);
	    }
	    return self::$expl_data[$expl_id];
	}
	
	public static function is_digital($id) {
	    if (!isset(static::$digital_ids[$id])) {
	        $id = intval($id);
	        $query = "select pnb_order_expl_num from pnb_orders_expl where pnb_order_expl_num =$id";
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            static::$digital_ids[$id] = true;
	        } else {
	            static::$digital_ids[$id] = false;
	        }
	    }
	    return static::$digital_ids[$id];
	}
	
	// récupère l'id d'un exemplaire d'après son code barre
	public static function get_expl_id_from_cb($cb) {
		if (!$cb) return FALSE;
		$query = "select expl_id as id from exemplaires where expl_cb='$cb' limit 1";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, '0', 'id');
	}
	
	// Donne l'id de la notice par son identifiant d'expl
	public static function get_expl_notice_from_id($expl_id=0) {
		$expl_id = intval($expl_id);
		$query = "select expl_notice, expl_bulletin from exemplaires where expl_id = $expl_id";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if ($row->expl_notice) {
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
	    $query = "select expl_bulletin from exemplaires where expl_id = $expl_id";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 'expl_bulletin');
	}
	
	public static function get_nb_prets_from_id($expl_id=0) {
		$nb_prets = 0;
		$expl_id = intval($expl_id);
		$query = "select count(arc_expl_id) as nb_prets from pret_archive where arc_expl_id = $expl_id";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$nb_prets = $row->nb_prets;
		}
		return $nb_prets;
	}
	
} # fin de la classe                    
       