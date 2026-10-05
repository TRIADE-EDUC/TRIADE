<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.class.php,v 1.27.4.1 2025/02/19 11:11:04 tsamson Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $class_path;
require_once($class_path."/upload_folder.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/event/events/event_explnum.class.php");
require_once($class_path."/event/events_handler.class.php");
// classe de gestion des exemplaires numériques

if ( ! defined( 'EXPLNUM_CLASS' ) ) {
  define( 'EXPLNUM_CLASS', 1 );

	class explnum {
		
		public $explnum_id = 0;
		public $explnum_notice = 0;
		public $explnum_bulletin = 0;
		public $explnum_nom = '';
		public $explnum_mimetype = '';
		public $explnum_url = '';
		public $explnum_data = '';
		public $explnum_vignette = ''; 
		public $explnum_statut = '0';
		public $explnum_index = '';
		public $explnum_repertoire = 0;
		public $explnum_path = '';
		public $explnum_nomfichier = '';
		public $explnum_rep_nom ='';
		public $explnum_rep_path ='';
		public $explnum_index_wew ='';
		public $explnum_index_sew ='';
		public $explnum_extfichier ='';
		public $explnum_location = '';
		public $infos_docnum = array();
		public $params = array();
		public $unzipped_files = array();
		public $explnum_docnum_statut = '1';
		protected $explnum_create_date;
		protected $explnum_update_date;
		protected $explnum_file_size;
		
		// constructeur
		public function __construct($id=0, $id_notice=0, $id_bulletin=0) {
			$this->unzipped_files = array();
			$this->explnum_id = intval($id);
			$this->explnum_notice = intval($id_notice);
			$this->explnum_bulletin = intval($id_bulletin);
			$this->fetch_data();
		}
		
		protected function init_repertoire() {
		    if(!defined('SESSlogin')) {
		        return;
		    }
			$req = "select repertoire_id, repertoire_nom, repertoire_path from  upload_repertoire, users where repertoire_id=deflt_upload_repertoire and username='".SESSlogin."'";
			$res = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
				$item = pmb_mysql_fetch_object($res);
				$this->explnum_rep_nom = $item->repertoire_nom;
				$this->explnum_rep_path = $item->repertoire_path;
				$this->explnum_repertoire = $item->repertoire_id;
			} else {
				$this->explnum_rep_nom = '';
				$this->explnum_rep_path = '';
				$this->explnum_repertoire = 0;
			}
		}
		
		protected function fetch_data() {
			global $pmb_indexation_docnum_default;
			
			$this->explnum_nom = '';
			$this->explnum_mimetype = '';
			$this->explnum_url = '';
			$this->explnum_data = '';
			$this->explnum_vignette  = '' ;
			$this->explnum_statut = '0';
			$this->init_repertoire();
			$this->explnum_index = ($pmb_indexation_docnum_default ? 'checked' : '');
			$this->explnum_repertoire = 0;
			$this->explnum_path = '';
			$this->explnum_nomfichier = '';
			$this->explnum_extfichier = '';
			$this->explnum_location= '';
			$this->explnum_docnum_statut = '1';
			$this->explnum_create_date = '0000-00-00 00:00:00';
			$this->explnum_update_date = '0000-00-00 00:00:00';
			$this->explnum_file_size = 0;
			if ($this->explnum_id) {
				$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_extfichier, explnum_url, explnum_data, explnum_vignette,
				explnum_statut, explnum_index_sew, explnum_index_wew, explnum_repertoire, explnum_nomfichier, explnum_path, repertoire_nom, repertoire_path, group_concat(num_location SEPARATOR ',') as loc,
				explnum_create_date, explnum_update_date, explnum_file_size, explnum_docnum_statut
				FROM explnum left join upload_repertoire on explnum_repertoire=repertoire_id left join explnum_location on num_explnum=explnum_id where explnum_id='".$this->explnum_id."' group by explnum_id";
				$result = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($result)) {
					$item = pmb_mysql_fetch_object($result);
					$this->explnum_id        = $item->explnum_id       ;
					$this->explnum_notice    = $item->explnum_notice   ;
					$this->explnum_bulletin  = $item->explnum_bulletin ;
					$this->explnum_nom       = $item->explnum_nom      ;
					$this->explnum_mimetype  = $item->explnum_mimetype ;
					$this->explnum_url       = $item->explnum_url      ;
					$this->explnum_data      = $item->explnum_data     ;
					$this->explnum_vignette  = $item->explnum_vignette ;
					$this->explnum_statut    = $item->explnum_statut ;
					$this->explnum_index_wew = $item->explnum_index_wew;
					$this->explnum_index_sew = $item->explnum_index_sew;
					$this->explnum_index     = (($item->explnum_index_wew || $item->explnum_index_sew || $pmb_indexation_docnum_default) ? 'checked' : '');
					$this->explnum_repertoire = $item->explnum_repertoire;
					$this->explnum_path = $item->explnum_path;
					$this->explnum_rep_nom = $item->repertoire_nom;
					$this->explnum_rep_path = $item->repertoire_path;
					$this->explnum_nomfichier = $item->explnum_nomfichier;
					$this->explnum_extfichier = $item->explnum_extfichier;
					$this->explnum_location = $item->loc ? explode(",",$item->loc) : [];
					$this->explnum_docnum_statut = $item->explnum_docnum_statut;
					$this->explnum_create_date = $item->explnum_create_date;
					$this->explnum_update_date = $item->explnum_update_date;
					$this->explnum_file_size = $item->explnum_file_size;
				}
			}
		}
		
		public function get_file_content(){
			$data = "";
			/**
			 * Publication d'un évenement avant la récupération
			 */
			$evt_handler = events_handler::get_instance();
			$event = new event_explnum("explnum", "before_get_file_content");
			$event->set_explnum($this);
			$evt_handler->send($event);
			
			if (!$this->explnum_id) {
				exit ;
			}
		
			if ($this->explnum_data && ($this->explnum_data != 'NULL')) {
				$data = $this->explnum_data;
			} else if ($this->explnum_path) {
				$up = new upload_folder($this->explnum_repertoire);
				$path = str_replace("//","/",$this->explnum_rep_path.$this->explnum_path.$this->explnum_nomfichier);
				$path = $up->encoder_chaine($path);
				if (file_exists($path)) {
					$fo = fopen($path,'rb');
					if ($fo) {
						while(!feof($fo)){
							$data.=fread($fo,4096);
						}
						fclose($fo);
					}
				}
			}
		
			return $data;
		}

		public function get_is_file() {
			$path = '';
			if (! $this->explnum_id) {
				return '';
			}
			if ($this->explnum_data && ($this->explnum_data != 'NULL')) {
				return '';
			} else if ($this->explnum_path) {
				$up = new upload_folder($this->explnum_repertoire);
				$path = str_replace("//", "/", $this->explnum_rep_path . $this->explnum_path . $this->explnum_nomfichier);
				$path = $up->encoder_chaine($path);
				if (file_exists($path)) {
					return $path;
				}
			}
			return '';
		}
		
		public function get_file_name() {
		    if ($this->explnum_nomfichier && pmb_substr($this->explnum_nomfichier, 0, 5) != 'file_') {
		        return static::clean_explnum_file_name($this->explnum_nomfichier);
		    }
		    if ($this->explnum_extfichier) {
		        $nomfichier = static::clean_explnum_file_name($this->explnum_nom);
		        if ($nomfichier) {
		            if (! pmb_preg_match("/\." . $this->explnum_extfichier . "$/", $nomfichier)) {
		                $nomfichier .= "." . $this->explnum_extfichier;
		            }
		            return $nomfichier;
		        } elseif ($this->explnum_nomfichier) {
		            return static::clean_explnum_file_name($this->explnum_nomfichier);
		        } else {
		            return "pmb" . $this->explnum_id . "." . $this->explnum_extfichier;
		        }
		    }
		    if ($this->explnum_nomfichier) {
		        return static::clean_explnum_file_name($this->explnum_nomfichier);
		    }
		}
		
		public function get_file_size(){
			if (!$this->explnum_file_size) {
				if ($this->explnum_data) {
					$this->explnum_file_size = strlen($this->explnum_data);
				} elseif ($this->explnum_path) {
					$up = new upload_folder($this->explnum_repertoire);
					$path = str_replace("//","/",$this->explnum_rep_path.$this->explnum_path.$this->explnum_nomfichier);
					$path = $up->encoder_chaine($path);
					$this->explnum_file_size = filesize($path);
				}
			}
			return $this->explnum_file_size;
		}
		
		public static function clean_explnum_file_name($filename){
			
			$filename = convert_diacrit($filename);
			$filename = preg_replace('/[^\x20-\x7E]/','_', $filename);			
			$filename = str_replace(',', '_', $filename);
			return $filename;
		}

		public function get_create_date() {
			return $this->explnum_create_date;
		}
		
		public function get_update_date() {
			return $this->explnum_update_date;
		}
		
		public function get_explnum_infos(){
			$infos_explnum = array();
            $location_libelle = '';
            $nomrepertoire = '';
                        
            $rqt = "SELECT IF(rep.repertoire_nom IS null, '', rep.repertoire_nom) AS nomrepertoire
                    FROM explnum ex_n
                    LEFT JOIN upload_repertoire rep ON ex_n.explnum_repertoire= rep.repertoire_id
                    WHERE explnum_id='".$this->explnum_id."'";
			$res=pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				$row = pmb_mysql_fetch_object($res);
				$nomrepertoire = $row->nomrepertoire;
            }
			
			$infos_explnum['explnum_id'] = $this->explnum_id;
			$infos_explnum['explnum_notice'] = $this->explnum_notice;
			$infos_explnum['explnum_bulletin'] = $this->explnum_bulletin;
			$infos_explnum['location_libelle'] = translation::get_translated_text($this->explnum_location[0] ?? 0, "docs_location", "location_libelle");
			$infos_explnum['explnum_nom'] = $this->explnum_nom;
			$infos_explnum['explnum_mimetype'] = $this->explnum_mimetype;
			$infos_explnum['explnum_url'] = $this->explnum_url;
			$infos_explnum['explnum_extfichier'] = $this->explnum_extfichier;
			$infos_explnum['nomfichier'] = $this->explnum_nomfichier;
			$infos_explnum['explnum_path'] = $this->explnum_path;
			$infos_explnum['nomrepertoire'] = $nomrepertoire;
			$infos_explnum['create_date'] = $this->explnum_create_date;
			$infos_explnum['update_date'] = $this->explnum_update_date;
			$infos_explnum['file_size'] = $this->get_file_size();
			
			return array(0=>$infos_explnum);
		}
		
		/*
		 * Teste si l'exemplaire est stocké sur le disque
		 */
		public function isEnUpload() {
			if ($this->explnum_repertoire && $this->explnum_path)
				return true;
			return false;
		}
		
		public static function get_p_perso($id_explnum) {
	        $p_perso= array();
	        
            $p_perso_explnums = new parametres_perso("explnum");
            $ppersos = $p_perso_explnums->show_fields($id_explnum);
	        // Filtre ceux qui ne sont pas visibles à l'OPAC ou qui n'ont pas de valeur
	        if (!empty($ppersos['FIELDS']) && is_array($ppersos['FIELDS'])) {
	            foreach ($ppersos['FIELDS'] as $pperso) {
	                if ($pperso['OPAC_SHOW'] && $pperso['AFF']) {
	                    if ($pperso["TYPE"] !== 'html') {
	                        $pperso['AFF'] = nl2br($pperso["AFF"]);
	                    }
	                    $p_perso[$pperso['NAME']] = $pperso;
	                }
	            }
	        }
	        return $p_perso;
		}
		
		public static function get_explnum_name($explnum_id) {
		    $requete = "SELECT explnum_nom
				        FROM explnum where explnum_id='$explnum_id'";
		    $result = pmb_mysql_query($requete);
		    
		    if(pmb_mysql_num_rows($result)) {
		        $item = pmb_mysql_fetch_object($result);
    		    return $item->explnum_nom;
		    }
		}
		
		public static function get_thumbnail_url($explnum_vignette, $explnum_id) {
		    global $pmb_docnum_img_folder_id;
		    global $prefix_url_image ;
		    global $opac_url_base;
		    
		    if ($pmb_docnum_img_folder_id) {
		        static::upload_thumbnail($explnum_vignette, $explnum_id);
		    }
		    if ($prefix_url_image) {
		        $tmpprefix_url_image = $prefix_url_image;
		    } else {
		        $tmpprefix_url_image = "$opac_url_base" ;
		    }
		    return $tmpprefix_url_image."vig_num.php?explnum_id=".$explnum_id;
		}
		
		public static function upload_thumbnail($explnum_vignette, $explnum_id) {
		    if ($explnum_vignette) {
		        $query = "select repertoire_path from upload_repertoire where repertoire_id ='".thumbnail::get_parameter_img_folder_id("docnum")."'";
		        $result = pmb_mysql_query($query);
		        if(pmb_mysql_num_rows($result)){
		            $row=pmb_mysql_fetch_object($result);
		            $filename_output=$row->repertoire_path.thumbnail::get_img_prefix("docnum").$explnum_id;
		            if (file_put_contents($filename_output, $explnum_vignette)) {
		                $query = "update explnum set explnum_vignette='' where explnum_id='" . $explnum_id . "'";
		                pmb_mysql_query($query);
		            }
		        }
		    }
		}
		
		/**
		 * Droit d'acces pour la vignette
		 * @param int $explnum_id
		 * @param int $explnum_notice
		 * @return boolean
		 */
		public static function has_acces_vignette($explnum_id, $explnum_notice) {
		    global $opac_show_links_invisible_docnums, $context_dsi_id_bannette;
		    global $gestion_acces_active, $gestion_acces_empr_docnum, $gestion_acces_empr_notice;
		    
		    if ($opac_show_links_invisible_docnums) {
		        return true;
		    }
		    
		    $explnum_id = intval($explnum_id);
		    $explnum_notice = intval($explnum_notice);
		    
		    // Droits d'acces emprunteur/notice
		    if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
		        $ac = new acces();
		        $dom_2 = $ac->setDomain(2);
		        $notice_rights = $dom_2->getRights($_SESSION['id_empr_session'], $explnum_notice);
		    }
		    
		    $query = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id='".$explnum_notice."' AND statut=id_notice_statut";
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)) {
		        $expl_num = pmb_mysql_fetch_assoc($result);
		    }
		    
		    $statut_not_account = false;
		    if ($context_dsi_id_bannette) {
		        $bannette = new bannette($context_dsi_id_bannette);
		        $statut_not_account = $bannette->statut_not_account;
		    }
		    
		    if (!(($notice_rights & 16) || is_null($dom_2) && $expl_num['explnum_visible_opac'] && (!$expl_num['explnum_visible_opac_abon'] || ($expl_num['explnum_visible_opac_abon'] && $_SESSION["user_code"])||($expl_num['explnum_visible_opac_abon'] && $statut_not_account)))) {
		        return false;
		    }
		    
		    // Droits d'acces emprunteur/document numérique
		    if ($gestion_acces_active == 1 && $gestion_acces_empr_docnum == 1) {
		        $ac = new acces();
		        $dom_3 = $ac->setDomain(3);
		        $docnum_rights = $dom_3->getRights($_SESSION['id_empr_session'], $explnum_id);
		    }
		    
		    $query = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_thumbnail_visible_opac_override FROM explnum,explnum_statut WHERE explnum_id='".$explnum_id."' AND explnum_docnum_statut=id_explnum_statut ";
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)) {
		        $docnum_expl_num = pmb_mysql_fetch_assoc($result);
		    }
		    
		    if (!($docnum_expl_num['explnum_thumbnail_visible_opac_override'] || $docnum_rights & 16 || (is_null($dom_3) && $docnum_expl_num['explnum_visible_opac'] && (!$docnum_expl_num['explnum_visible_opac_abon'] || ($docnum_expl_num['explnum_visible_opac_abon'] && $_SESSION["user_code"]))))) {
		        return false;
		    }
		    return true;
		}
		
	} # fin de la classe explnum
                                                  
} # fin de définition                             
