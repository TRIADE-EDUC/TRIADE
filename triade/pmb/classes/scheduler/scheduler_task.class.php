<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_task.class.php,v 1.8 2019-06-11 09:16:34 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path, $class_path;
require_once($include_path."/parser.inc.php");
require_once($include_path."/templates/taches.tpl.php");
require_once($include_path."/connecteurs_out_common.inc.php");
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/scheduler/scheduler_task_docnum.class.php");
require_once($class_path."/upload_folder.class.php");
require_once($class_path."/xml_dom.class.php");

//commands
define(RESUME,'1');
define(SUSPEND,'2');
define(STOP,'3');
define(RETRY,'4');
define(ABORT,'5');
define(FAIL,'6');

//status
define(WAITING,'1');
define(RUNNING,'2');
define(ENDED,'3');
define(SUSPENDED,'4');
define(STOPPED,'5');
define(FAILED,'6');
define(ABORTED,'7');
		
class scheduler_task {
	protected $msg;							// Messages propres au type de tâche
	public $proxy;							// classe contenant les méthodes de l'API
	public $id_tache=0;					//identifiant de la tâche
	public $num_planificateur = 0;
	public $start_at = '0000-00-00 00:00:00';
	public $end_at = '0000-00-00 00:00:00';
	public $indicat_progress = 0;
	public $report=array();				// rapport de la tâche
	public $statut;
	public $num_type_tache = 0;
	public $libelle_tache = '';
	public $params=array();
	
	public function __construct($id_tache=0) {
		$this->id_tache = $id_tache+0;
		$this->get_messages();
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		
		if($this->id_tache) {
			$query = "select * from taches join planificateur on id_planificateur=num_planificateur where id_tache=".$this->id_tache;
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->num_planificateur = $row->num_planificateur;
				$this->start_at = $row->start_at;
				$this->end_at = $row->end_at;
				$this->indicat_progress = $row->indicat_progress;
				$this->num_type_tache = $row->num_type_tache;
				$this->libelle_tache = $row->libelle_tache;
				$this->params = unserialize($row->param);
			}
		}
	}
	
	public function get_id_type() {
		return $this->id_type;
	}
	
	//messages 
	public function get_messages() {
		global $base_path, $lang;
		
		$tache_path = $base_path."/admin/planificateur/".str_replace('scheduler_', '', static::class);
		if (file_exists($tache_path."/messages/".$lang.".xml")) {
			$file_name=$tache_path."/messages/".$lang.".xml";
		} else if (file_exists($tache_path."/messages/fr_FR.xml")) {
			$file_name=$tache_path."/messages/fr_FR.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			$this->msg=$xmllist->table;
		}
	}
	
	public function setEsProxy($proxy) {
		$this->proxy = $proxy;
	}
	
	public function listen_commande($methode_callback) {
		$query_commande = "select status, commande, next_state from taches where id_tache=".$this->id_tache;
		$result = pmb_mysql_query($query_commande);
		
		if (pmb_mysql_result($result,0,"commande") != '0') {
			$cmd = pmb_mysql_result($result,0,"commande");			
			$requete = "update taches set status=".pmb_mysql_result($result,0, "next_state").", commande=0, next_state=0 where id_tache=".$this->id_tache."";
			$res = pmb_mysql_query($requete);
			if ($res) {
				$this->statut = pmb_mysql_result($result,0, "next_state");
				call_user_func($methode_callback,$cmd);	
			}
		}
	}
	
	// Envoi d'une commande par la tache, changement du statut de la tâche...
	public function send_command($state=''){
		if ($state != '') {
			$this->statut = $state;
			switch ($this->statut) {
				case STOPPED: // 5
					$query = "update taches set status=5,";
					if ($this->start_at == '0000-00-00 00:00:00') {
						$query .= "start_at=CURRENT_TIMESTAMP, ";
					}
					$query .= "end_at=CURRENT_TIMESTAMP, id_process=0, commande=0 where id_tache=".$this->id_tache;
					pmb_mysql_query($query);
					break;
				default :
					pmb_mysql_query("update taches set status=".$this->statut." where id_tache='".$this->id_tache."'");
					break;
			}
			
		}
	}
	
	public function send_mail() {
		global $PMBuseremail, $PMBusernom, $PMBuserprenom;
		global $include_path, $class_path, $lang;
		global $pmb_url_base;
		global $charset;
	
		if ($this->params["alert_mail_on_failure"] != "") {
			$params_alert_mail = explode(",",$this->params["alert_mail_on_failure"]);
			if ($params_alert_mail[0]) {
				$mails = explode(";",$params_alert_mail[1]);
				if(preg_match("#.*@.*#",$PMBuseremail)) {
					if (count($mails)) {
						//Allons chercher les messages
						if (file_exists("$include_path/messages/".$lang.".xml")) {
							//Allons chercher les messages
							require_once($class_path."/XMLlist.class.php");
							$messages = new XMLlist($include_path."/messages/".$lang.".xml", 0);
							$messages->analyser();
							$msg = $messages->table;
				
							$objet = $msg["task_alert_user_mail_obj"];
							$corps = str_replace("!!task_name!!",$this->libelle_tache,$msg["task_alert_user_mail_corps"]) ;
							$corps = str_replace("!!percent!!",$this->indicat_progress,$corps) ;
							$corps = str_replace("!!pmb_url_base!!",$pmb_url_base,$corps) ;
							foreach ($mails as $mail) {
								if(preg_match("#.*@.*#",$mail)) {
									@mailpmb("", $mail, $objet, $corps, $PMBusernom." ".$PMBuserprenom, $PMBuseremail, "Content-Type: text/plain; charset=\"".$charset."\"", '', '', 0, '');
								}
							}
						}
					}
				}
			}
		}
	}
	
	protected function add_section_report($content='', $css_class='scheduler_report_section') {
		$this->report[] = "<tr><th class='".$css_class."'>".$content."</th></tr>";
	}
	
	protected function add_content_report($content='', $css_class='scheduler_report_content') {
		$this->report[] = "<tr><td class='".$css_class."'>".$content."</td></tr>";
	}
	
	protected function add_function_rights_report($method='', $group='') {
		global $msg;
		global $PMBusername;
		
		$this->report[] = "<tr><td>".sprintf($msg["planificateur_function_rights"],$method,$group,$PMBusername)."</td></tr>";
	}
	
	protected function add_rights_bad_user_report() {
		global $msg;
		global $PMBusername;
	
		$this->report[] = "<tr><th>".sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername)."</th></tr>";
	}
	
	/*
	 * Exécution de la tâche - Méthode appelée par la classe spécifique
	 * Modification des données de la base
	 */
	public function execute() {
		global $charset;
			 
		//initialisation de la tâche planifiée sur la base
		$this->initialize();
		//appel de la méthode spécifique
		$this->execution();
		//finalisation de la tâche planifiée sur la base
		$this->finalize();

		$result_success = pmb_mysql_query("select num_planificateur from taches where id_tache=".$this->id_tache);
		//mise à jour de la prochaine exec
		if (pmb_mysql_num_rows($result_success) == 1) {
			//planification d'une nouvelle tâche
			$scheduler_planning = new scheduler_planning(pmb_mysql_result($result_success,0,"num_planificateur"));
			$scheduler_planning->calcul_execution();
			$scheduler_planning->insertOfTask();
		}
	}
	
	public function get_task_params() {
		$params = "";
		if ($this->id_tache) {
			$result = pmb_mysql_query("select param from planificateur, taches where id_planificateur=num_planificateur and id_tache=".$this->id_tache);
			if ($result) $params = unserialize(pmb_mysql_result($result, 0,"param"));
		}
		return $params; 
	} 
	
	public function initialize() {
		$this->statut = RUNNING;

		$requete = "update taches set start_at = CURRENT_TIMESTAMP, status = ".$this->statut."
			where id_tache='".$this->id_tache."'";
		pmb_mysql_query($requete);
	}

	public function finalize() {
		global $base_path,$charset;
							
		if ($this->indicat_progress == 100) {
			$this->statut=ENDED;
		} else {
			$this->statut = FAILED;
			if($this->params['alert_mail_on_failure']) {
				$this->send_mail();
			}
		}
		//fin de l'exécution, mise à jour sur la base
		$req = "update taches set end_at = CURRENT_TIMESTAMP, status = ".$this->statut.", commande=0, rapport = '".htmlspecialchars(serialize($this->report), ENT_QUOTES,$charset)."',id_process=0
			where id_tache='".$this->id_tache."'";
		pmb_mysql_query($req);
	}
	
	public function update_progression($percent) {
		global $charset;
		
		if ($this->id_tache) {
			$this->indicat_progress = $percent;
			$requete = "update taches set indicat_progress ='".$percent."', rapport='".htmlspecialchars(serialize($this->report), ENT_QUOTES,$charset)."' where id_tache=".$this->id_tache;
			pmb_mysql_query($requete);
		}
	}
	
	public function isUploadValide($id_tache) {
		$query_sel = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
			left join taches t on t.num_planificateur = p.id_planificateur
			left join taches_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
			where t.id_tache=".$id_tache;
		$res_query = pmb_mysql_query($query_sel);
		if ($res_query) {
			$row = pmb_mysql_fetch_object($res_query);
			
			$up = new upload_folder($row->rep_upload);
			$nom_chemin = $up->formate_nom_to_path($up->repertoire_nom.$row->path_upload);
			if ((is_dir($nom_chemin)) && (is_writable($nom_chemin)))
				return true;
		}
		return false;
	}
	
	// que passer à cette fonction datas ou object ?? (objet pdf , contenu xls)
	public function generate_docnum($id_tache, $content, $mimetype="application/pdf", $ext_fichier="pdf") {
		global $msg, $base_path;
		
		$tdn = new scheduler_task_docnum();
		
		$tdn->num_tache = $id_tache;
		
		$query_sel = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
			left join taches t on t.num_planificateur = p.id_planificateur
			left join taches_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
			where t.id_tache=".$tdn->num_tache;
		$res_query = pmb_mysql_query($query_sel);
		if ($res_query) {
			$row = pmb_mysql_fetch_object($res_query);
			
			$up = new upload_folder($row->rep_upload);
			$nom_chemin = $up->formate_nom_to_path($up->repertoire_nom.$row->path_upload);
//			if ((!is_dir($nom_chemin)) || (!is_writable($nom_chemin))) {
//				$nom_chemin = $base_path."/temp/";
//			}
			//appel de fonction pour le calcul de nom de fichier
			$date_now = date('Ymd');
//			$tdn->tache_docnum_nomfichier = str_replace(" ", "_", $row->libelle_tache)."_".$date_now;
			$tdn->tache_docnum_nomfichier = clean_string_to_base($row->libelle_tache)."_".$date_now;
			$tdn->tache_docnum_contenu = $content;
			$tdn->tache_docnum_extfichier= $ext_fichier;
			$tdn->tache_docnum_file = "";
			$tdn->tache_docnum_mimetype = $mimetype;
			$tdn->tache_docnum_repertoire = $row->rep_upload;
			$tdn->tache_docnum_path = $row->path_upload;
			$path_absolu = $nom_chemin.$tdn->tache_docnum_nomfichier.".".$tdn->tache_docnum_extfichier;
			if (file_exists($path_absolu)) {
				$i=2;
				while (file_exists($nom_chemin.$tdn->tache_docnum_nomfichier."_".$i.".".$tdn->tache_docnum_extfichier)) {
					$i++;
				}
				$path_absolu = $nom_chemin.$tdn->tache_docnum_nomfichier."_".$i.".".$tdn->tache_docnum_extfichier;
				$tdn->tache_docnum_nomfichier = $tdn->tache_docnum_nomfichier."_".$i;
			}
			$path_absolu = $up->encoder_chaine($path_absolu);
						
			//verifier permissions d'ecriture...
			if (is_writable($nom_chemin)) {
				switch ($mimetype) {
					case "application/pdf" :
						$content->Output($path_absolu,"F");
						break;
					case "application/ms-excel" :
						file_put_contents($path_absolu, $content);
						break;
				}
//				if ($mimetype == "application/pdf") {
//					$content->Output($path_absolu,"F");	
//				} else if ($mimetype == "application/ms-excel") {
//					file_put_contents($path_absolu, $content);
//				}
				
				$tdn->save();
				$this->report[] = "<tr><td>".$msg["planificateur_write_success"]." : <a target='_blank' href='./tache_docnum.php?tache_docnum_id=".$tdn->id_tache_docnum."'>".$tdn->tache_docnum_nomfichier.".".$tdn->tache_docnum_extfichier."</a></td></tr>";
				return true;
			} else {
				$this->report[] = "<tr><td>".sprintf($msg["planificateur_write_error"],$path_absolu)."</td></tr>";
				return false;
			}		
		}
	}
	
	public function unserialize_task_params() {
		return $this->get_task_params();
	}
	
	public function suspend() {
		while ($this->statut == SUSPENDED) {
			sleep(20);
			$this->statut = $this->listen_commande(array(&$this,"traite_commande"));
		}
	}
	
	public function traite_commande($cmd,$message = '') {
		switch ($cmd) {
			case RESUME :
				$this->send_command(WAITING);
				break;
			case SUSPEND :
				$this->suspend();
				break;
			case STOP :
				$this->finalize();
				die();
				break;
			case ABORT :
				$this->abort();
				$this->finalize();
				die();
				break;
			case FAIL :
				$this->finalize();
				die();
				break;
		}
	}
	
	public static function delete($id) {
		$id += 0;
		$query = "delete from taches where id_tache = ".$id." and status <> '".RUNNING."'";
		pmb_mysql_query($query);
		return true;
	}
	
	public function is_param_active($name) {
		if($this->params[$name]) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_id_tache() {
		return $this->id_tache;
	}
	
	public function get_num_planificateur() {
		return $this->num_planificateur;
	}
	
	public function get_num_type_tache() {
		return $this->num_type_tache;
	}
	
	public function get_param($name) {
		return $this->params[$name];
	}
}