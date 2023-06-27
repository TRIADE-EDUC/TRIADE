<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.class.php,v 1.16 2018-09-21 08:15:52 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/upload_folder.class.php");
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
		protected $explnum_create_date;
		protected $explnum_update_date;
		protected $explnum_file_size;
		
		// constructeur
		public function __construct($id=0, $id_notice=0, $id_bulletin=0) {
			global $dbh, $pmb_indexation_docnum_default;
			$this->unzipped_files = array();
			$id += 0;
			$id_notice += 0;
			$id_bulletin += 0;
			if ($id) {
		
				$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_extfichier, explnum_url, explnum_data, explnum_vignette, 
				explnum_statut, explnum_index_sew, explnum_index_wew, explnum_repertoire, explnum_nomfichier, explnum_path, repertoire_nom, repertoire_path, group_concat(num_location SEPARATOR ',') as loc,
				explnum_create_date, explnum_update_date, explnum_file_size
				FROM explnum left join upload_repertoire on explnum_repertoire=repertoire_id left join explnum_location on num_explnum=explnum_id where explnum_id='$id' group by explnum_id";
				$result = pmb_mysql_query($requete, $dbh);
				
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
					$this->explnum_location = $item->loc ? explode(",",$item->loc) : '';
					$this->explnum_create_date = $item->explnum_create_date;
					$this->explnum_update_date = $item->explnum_update_date;
					$this->explnum_file_size = $item->explnum_file_size;					
				} else { // rien trouvé en base, on va faire comme pour une création
						$req = "select repertoire_nom, repertoire_path from  upload_repertoire, users where repertoire_id=deflt_upload_repertoire and username='".SESSlogin."'";
						$res = pmb_mysql_query($req,$dbh);
						if(pmb_mysql_num_rows($res)){
							$item = pmb_mysql_fetch_object($res);
							$this->explnum_rep_nom = $item->repertoire_nom;
							$this->explnum_rep_path = $item->repertoire_path;
						} else {
							$this->explnum_rep_nom = '';
							$this->explnum_rep_path = '';
						}
						$this->explnum_id = 0;
						$this->explnum_notice = $id_notice;
						$this->explnum_bulletin = $id_bulletin;
						$this->explnum_nom = '';
						$this->explnum_mimetype = '';
						$this->explnum_url = '';
						$this->explnum_data = '';
						$this->explnum_vignette  = '' ;
						$this->explnum_statut = '0';
						$this->explnum_index = ($pmb_indexation_docnum_default ? 'checked' : '');
						$this->explnum_repertoire = 0;
						$this->explnum_path = '';
						$this->explnum_nomfichier = '';
						$this->explnum_extfichier = '';
						$this->explnum_location= '';
						$this->explnum_create_date = '0000-00-00 00:00:00';
						$this->explnum_update_date = '0000-00-00 00:00:00';
						$this->explnum_file_size = 0;
				}
				
			} else { // rien de fourni apparemment : création
				$req = "select repertoire_id, repertoire_nom, repertoire_path from  upload_repertoire, users where repertoire_id=deflt_upload_repertoire and username='".SESSlogin."'";
				$res = pmb_mysql_query($req,$dbh);
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
				$this->explnum_id = $id;
				$this->explnum_notice = $id_notice;
				$this->explnum_bulletin = $id_bulletin;
				$this->explnum_nom = '';
				$this->explnum_mimetype = '';
				$this->explnum_url = '';
				$this->explnum_data = '';
				$this->explnum_vignette  = '' ;
				$this->explnum_statut = '0';
				$this->explnum_index = ($pmb_indexation_docnum_default ? 'checked' : '');;
				$this->explnum_path = '';
				$this->explnum_nomfichier='';
				$this->explnum_extfichier = '';
				$this->explnum_location = '';
				$this->explnum_create_date = '0000-00-00 00:00:00';
				$this->explnum_update_date = '0000-00-00 00:00:00';
				$this->explnum_file_size = 0;
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

		function get_is_file() {
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
		
		public function get_file_name(){
			$nomfichier = "";
			if ($this->explnum_nomfichier) {
				$nomfichier = $this->explnum_nomfichier;
			} elseif($this->explnum_extfichier) {
				if ($this->explnum_nom) {
					$nomfichier=$this->explnum_nom;
					if(!preg_match("/\.".$this->explnum_extfichier."$/",$nomfichier)){
						$nomfichier.=".".$this->explnum_extfichier;
					}
				} else {
					$nomfichier="pmb".$this->explnum_id.".".$this->explnum_extfichier;
				}
			}
			$nomfichier = static::clean_explnum_file_name($nomfichier);
			return $nomfichier;
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

		function get_create_date() {
			return $this->explnum_create_date;
		}
		
		function get_update_date() {
			return $this->explnum_update_date;
		}
		
		public function get_explnum_infos(){
			$infos_explnum = array();
            $location_libelle = '';
            $nomrepertoire = '';
                        
            $rqt = "SELECT IF(location_libelle IS null, '', location_libelle) AS location_libelle, IF(rep.repertoire_nom IS null, '', rep.repertoire_nom) AS nomrepertoire
                    FROM explnum ex_n
                    LEFT JOIN explnum_location ex_l ON ex_n.explnum_id= ex_l.num_explnum
                    LEFT JOIN docs_location dl ON ex_l.num_location= dl.idlocation
                    LEFT JOIN upload_repertoire rep ON ex_n.explnum_repertoire= rep.repertoire_id
                    WHERE explnum_id='".$this->explnum_id."'";
			$res=pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				$row = pmb_mysql_fetch_object($res);
                $location_libelle = $row->location_libelle;
                $nomrepertoire = $row->location_libelle;
            }
			
			$infos_explnum['explnum_id'] = $this->explnum_id;
			$infos_explnum['explnum_notice'] = $this->explnum_notice;
			$infos_explnum['explnum_bulletin'] = $this->explnum_bulletin;
			$infos_explnum['location_libelle'] = $location_libelle;
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
		
	} # fin de la classe explnum
                                                  
} # fin de définition                             
