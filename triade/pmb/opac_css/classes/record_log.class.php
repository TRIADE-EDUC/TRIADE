<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_log.class.php,v 1.21 2019-01-15 14:14:59 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class record_log{
	
	public $date='';
	public $url_asked='';
	public $get_log = array();
	public $post_log = array();
	public $url_ref = '';
	public $num_session = 0;
	public $empr = array();
	public $serveur = array();
	public $doc = array();
	public $expl = array();
	public $nb_results = array();
	public $generique= array();
	
	
	public function __construct(){
		$this->init_environnement();
	}
	
	//initialisation de l'environnement
	public function init_environnement(){
		if($_GET) $this->get_log = $_GET;
		if ($_POST) $this->post_log = $_POST;
		if($_SERVER) $this->serveur = $_SERVER;
		if(isset($_SERVER['REQUEST_URI'])) $this->url_asked = $_SERVER['REQUEST_URI'];															
		if(isset($_SERVER['HTTP_REFERER'])) $this->url_ref = $_SERVER['HTTP_REFERER'];		
	}
	
	//Ajout d'un nouvel élément
	public function add_log($nom='',$value=0){
		switch($nom){
			case 'num_session':
				if($value) $this->num_session = $value;
				break;
			case 'empr':
				if($value) $this->empr = $value;				 
				break;
			case 'docs':
				if($value) $this->doc = $value;
				break;
			case 'expl':
				if($value) $this->expl = $value;
				break;
			case 'nb_results':
				if($value) $this->nb_results = $value;
				break;
			default:
				if(!isset($this->generique) || !is_array($this->generique)){
					$this->generique=array();
				}
				if($value) $this->generique[$nom]=$value;
				break;
							
		}		
	}
	
	
	//Enregistrement dans la table de log
	public function save(){
		global $dbh;
		global $pmb_perio_vidage_log, $pmb_perio_vidage_stat;
		global $pmb_logs_exclude_robots;
		
		$tab_logs_exclude_robots = array();
		$tab_logs_exclude_robots = explode(",", $pmb_logs_exclude_robots);
		if ($tab_logs_exclude_robots[0]) {
			$robots = array('BOT','SPIDER','CRAWL','QWANTIFY','SLURP');
			foreach ($robots as $robot) {
				if (preg_match('/'.$robot.'/i',$_SERVER['HTTP_USER_AGENT'])){
					return;
				}
			}
		}
		if (count($tab_logs_exclude_robots) > 1) {
			$ip_adress = array();
			for($i=1;$i<count($tab_logs_exclude_robots);$i++) {
				$ip_adress[] = $tab_logs_exclude_robots[$i];
			}
			if (in_array($_SERVER['REMOTE_ADDR'], $ip_adress)) {
				return;
			}
		}
		//Opposition à l'utilisation des cookies, aucun enregistrement de logs
		if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] == "false") {
			return;
		}
		
		$rqt = "INSERT INTO logopac (url_demandee,url_referente,get_log,post_log,num_session,server_log,empr_carac,empr_doc,empr_expl,nb_result, gen_stat) VALUES ('";
		$rqt .= addslashes($this->url_asked)."','".addslashes($this->url_ref)."','".addslashes(serialize($this->get_log))."','".addslashes(serialize($this->post_log))."','".addslashes($this->num_session)."','".addslashes(serialize($this->serveur))."','".addslashes(serialize($this->empr))."','".addslashes(serialize($this->doc))."','".addslashes(serialize($this->expl))."','".addslashes(serialize($this->nb_results))."','".addslashes(serialize($this->generique))."')";
		$first_day = $this->sql_value("SELECT date_log FROM logopac order by date_log limit 1");
		$periodicite = $this->sql_value("SELECT DATEDIFF(CURRENT_DATE(),'".addslashes($first_day)."')");
		if($periodicite >= $pmb_perio_vidage_log){	
			//On copie la table log dans stat et on la vide
			pmb_mysql_query("INSERT INTO statopac (date_log, url_demandee, url_referente, get_log, post_log,num_session, server_log, empr_carac, empr_doc, empr_expl,nb_result, gen_stat) 
					SELECT date_log, url_demandee, url_referente, get_log, post_log, num_session, server_log, empr_carac, empr_doc, empr_expl, nb_result, gen_stat FROM logopac",$dbh);
			pmb_mysql_query("TRUNCATE TABLE logopac",$dbh);			
		} 
		pmb_mysql_query($rqt,$dbh);
		
		//Gestion du verrou
		$uncached_internal_emptylogstatopac = 0;
		$res_uncache=pmb_mysql_query("SELECT valeur_param FROM parametres_uncached WHERE type_param='internal' and sstype_param='emptylogstatopac'");
		if($res_uncache && pmb_mysql_num_rows($res_uncache)){
			$uncached_internal_emptylogstatopac = pmb_mysql_result($res_uncache,0,0);
		}
		
		if ($uncached_internal_emptylogstatopac) {
			$date_internal=explode(" ",$uncached_internal_emptylogstatopac);
			if ((time()-$date_internal[1])>86400) {
				pmb_mysql_query("update parametres_uncached set valeur_param=0 where type_param='internal' and sstype_param='emptylogstatopac'",$dbh);
				$uncached_internal_emptylogstatopac=0;
			}
		}
		
		if (!$uncached_internal_emptylogstatopac) {
			$perio_stat = explode(",",$pmb_perio_vidage_stat);
			$mode=$perio_stat[0];
			$nb_jours=$perio_stat[1];
			$first_day_stat = $this->sql_value("SELECT date_log FROM statopac order by date_log limit 1");
			switch($mode){
				case '1' :
					//On vide tous les x jours
					$periodicite = $this->sql_value("SELECT DATEDIFF(CURRENT_DATE(),'".addslashes($first_day_stat)."')");
					if($periodicite >= $nb_jours){
						pmb_mysql_query("update parametres_uncached set valeur_param='1 ".(time())."' where type_param='internal' and sstype_param='emptylogstatopac'",$dbh);
						pmb_mysql_query("TRUNCATE TABLE statopac",$dbh);
						pmb_mysql_query("update parametres_uncached set valeur_param=0 where type_param='internal' and sstype_param='emptylogstatopac'",$dbh);
					}
					break;
				case '2':
					//On vide tout ce qui a plus de x jours
					$periodicite = $this->sql_value("SELECT DATEDIFF(CURRENT_DATE(),'".addslashes($first_day_stat)."')");
					if($periodicite >= $nb_jours){
						pmb_mysql_query("update parametres_uncached set valeur_param='1 ".(time())."' where type_param='internal' and sstype_param='emptylogstatopac'",$dbh);
						$rqt = "DELETE from statopac where date_log< DATE_SUB(CURRENT_DATE() , INTERVAL ".addslashes($nb_jours)." DAY)";
						pmb_mysql_query($rqt,$dbh);
						pmb_mysql_query("update parametres_uncached set valeur_param=0 where type_param='internal' and sstype_param='emptylogstatopac'",$dbh);
					}
					break;
			}
		}
	}
	
	//retourne le resultat d'une requete
	public function sql_value($rqt) {
		$result=pmb_mysql_query($rqt);
		if ($result) $row = pmb_mysql_fetch_row($result);
		return $row[0];
	}
	
	public function get_empr_query() {
		$query = " select empr_prof,empr_cp, empr_ville as ville, empr_year, empr_sexe, empr_login, empr_date_adhesion, empr_date_expiration, count(pret_idexpl) as nbprets, count(resa.id_resa) as nbresa, code.libelle as codestat, es.statut_libelle as statut, categ.libelle as categ, gr.libelle_groupe as groupe,dl.location_libelle as location
			from empr e
			left join empr_codestat code on code.idcode=e.empr_codestat
			left join empr_statut es on e.empr_statut=es.idstatut
			left join empr_categ categ on categ.id_categ_empr=e.empr_categ
			left join empr_groupe eg on eg.empr_id=e.id_empr
			left join groupe gr on eg.groupe_id=gr.id_groupe
			left join docs_location dl on e.empr_location=dl.idlocation
			left join resa on e.id_empr=resa_idempr
			left join pret on e.id_empr=pret_idempr
			where e.empr_login='".addslashes($_SESSION['user_code'])."'
			group by resa_idempr, pret_idempr";
		return $query;
	}
}
?>