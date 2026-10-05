<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: consolidation.class.php,v 1.21 2020/08/18 01:15:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once ($class_path . "/parse_format.class.php");

if(!defined('DEFAULT_CONSO')) define('DEFAULT_CONSO',1);
if(!defined('INTERVAL_CONSO')) define('INTERVAL_CONSO',2);
if(!defined('ECHEANCE_CONSO')) define('ECHEANCE_CONSO',3);

class consolidation {
	
	public $mode=1;
	public $date_debut='';
	public $date_fin='';
	public $echeance='';
	public $list_idview = array();
	public $remove_data=0;
	public $remove_data_interval=0;
	public $remove_data_interval_date_debut='';
	public $remove_data_interval_date_fin='';
	public $flag = false;
	public $cols_vue=array();
	
	public $nom_vue='';
	public $date_consolidation='0000-00-00 00:00:00';
	public $date_debut_log='0000-00-00 00:00:00';
	public $date_fin_log='0000-00-00 00:00:00';
	
	public static $displayed_progress_bar = false;
	
	public function __construct($mode=1,$date_debut='',$date_fin='',$echeance='',$list_ck='',$remove_data=0,$remove_data_interval=0){
		global $pmb_set_time_limit;
		$this->mode = $mode;
		$this->date_debut = $date_debut;
		$this->date_fin = $date_fin;
		$this->echeance = $echeance;
		$this->list_idview = $list_ck;
		$this->remove_data = $remove_data;
		$this->remove_data_interval = $remove_data_interval;
		//Gestion du timeout au niveau de mysql pour ne pas perdre la connection
		//Ne peut pas être fait dans consolider car les fonctions de calcul peuvent aussi prendre du temps
		if($pmb_set_time_limit){
			$res=pmb_mysql_query("SHOW SESSION VARIABLES like 'wait_timeout'");
			$timeout_default=0;
			if($res && pmb_mysql_num_rows($res)){
				$timeout_default=pmb_mysql_result($res,0,1);
			}
			pmb_mysql_query("SET SESSION wait_timeout=".($timeout_default+(($pmb_set_time_limit)*1)));
		}else{
			//Si pmb_set_time_limit vaut 0 alors c'est illimité -> Dans ce cas je prends 12h
			pmb_mysql_query("SET SESSION wait_timeout=43200");
		}
	}

	
	public function make_consolidation(){
		$details = array();
		foreach($this->list_idview as $id_vue){
			
			$req_vue=pmb_mysql_query('select * from statopac_vues where id_vue='.$id_vue.' ');
			if (pmb_mysql_num_rows($req_vue)) {
				$row = pmb_mysql_fetch_object($req_vue);
				$this->nom_vue = $row->nom_vue;
				$this->date_debut_log = $row->date_debut_log;
				$this->date_fin_log = $row->date_fin_log;
				$this->date_consolidation = $row->date_consolidation;
			} else {
				continue;
			}
			switch($this->mode){
				
				case INTERVAL_CONSO:
					$this->check_structure($id_vue);
					$calculated = $this->calculer_sur_periode($id_vue, $this->date_debut,$this->date_fin);
					if($calculated) {
						$this->consolider($id_vue);
					}
					break;
				
				case ECHEANCE_CONSO:
					$this->check_structure($id_vue);
					$calculated = $this->calculer_until($id_vue, $this->echeance);
					if($calculated) {
						$this->consolider($id_vue);
					}
					break;
					
				default:
					$this->check_structure($id_vue);
					$calculated = $this->calculer_since_last($id_vue);
					if($calculated) {
						$this->consolider($id_vue);
					}
					break;			
				
			}
			$details[$id_vue] = array(
					'nom_vue' => $this->nom_vue,
					'date_debut_log' => $row->date_debut_log,
					'date_fin_log' => $row->date_fin_log,
					'date_consolidation' => $row->date_consolidation,
					'calculated' => $calculated
			);
		}
		return $details;
	}

	
	/**
	 * Fonction qui permet d'extraire un lot de log entre des dates précises
	 */
	public function calculer_sur_periode($id_vue, $date_deb, $date_fin){
		$req = "create temporary table logs_filtre_$id_vue select * from statopac where date_log between '".addslashes($date_deb)."' and '".addslashes($date_fin)."'";
		$result = pmb_mysql_query($req);
		if($result) {
			if ($this->flag || $this->remove_data || $this->is_data_interval_selected()) {
				$this->set_dates_log($id_vue);
			} else {
				$this->set_dates_log($id_vue,true);
			}
			return true;
		}
		return false;
	}

	
	/**
	 * Fonction qui permet d'extraire un lot de log depuis le début des enregistrements jusqu'à l'échéance fixée
	 */
	public function calculer_until($id_vue, $echeance){
		if ($this->flag || $this->remove_data || $this->is_data_interval_selected() || $this->date_fin_log=='0000-00-00 00:00:00') {
			$req = "create temporary table logs_filtre_$id_vue select * from statopac where date_log <='".addslashes($echeance)."'";
			$result = pmb_mysql_query($req);
			if($result) {
				$this->set_dates_log($id_vue);
				return true;
			}
		} else {
			$req = "create temporary table logs_filtre_$id_vue select * from statopac where date_log > '".$this->date_fin_log."' and  date_log <= '".addslashes($echeance)."' ";
			$result = pmb_mysql_query($req);
			if($result) {
				$this->set_dates_log($id_vue,true);
				return true;
			}
		}
		return false;
	}

	
	/**
	 * Fonction qui permet d'extraire un lot de log depuis la date de dernière consolidation
	 */	
	public function calculer_since_last($id_vue){
		//$req_vue = "select date_consolidation from statopac_vues where id_vue='".addslashes($id_vue)."'";
		//$res_vue = pmb_mysql_query($req_vue);
		if ($this->flag || $this->remove_data || $this->is_data_interval_selected() || $this->date_fin_log=='0000-00-00 00:00:00') {
			$req = "create temporary table logs_filtre_$id_vue select * from statopac where date_log <= now()";
			$result = pmb_mysql_query($req);
			if($result) {
				$this->set_dates_log($id_vue);
				return true;
			}
		} else {
			$req = "create temporary table logs_filtre_$id_vue select * from statopac where date_log > '".$this->date_fin_log."' ";
			//Si on prend date_consolidation on risque de perdre des informations
			$result = pmb_mysql_query($req);
			if($result) {
				$this->set_dates_log($id_vue,true);
				return true;
			}
		}
		return false;
	}


	/**
	 * Calcul des dates de debut et de fin de log 
	 */
	public function set_dates_log($id_vue,$only_last=false) {
		if (!$id_vue) return;
		$q = 'select min(date_log) as min_date, max(date_log) as max_date from logs_filtre_'.$id_vue;
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$row = pmb_mysql_fetch_object($r);
			if(!$this->date_debut_log || $this->date_debut_log === "0000-00-00 00:00:00"){
				$this->date_debut_log = $row->min_date;
			}else{
				//On regarde quel date on doit garder
				$res=pmb_mysql_query("SELECT DATEDIFF('".$this->date_debut_log."','".$row->min_date."')");
				if(pmb_mysql_num_rows($res)){
					if((pmb_mysql_result($res,0,0))*1 > 1){
						$this->date_debut_log=$row->min_date;
					}else{
						//La date de début est déjà entérieur aux logs que l'on va ajouter
					}
				}
			}				
			if(!$this->date_fin_log || $this->date_fin_log === "0000-00-00 00:00:00"){
				$this->date_fin_log = $row->max_date;
			}else{
				//On regarde quel date on doit garder
				$res=pmb_mysql_query("SELECT DATEDIFF('".$this->date_fin_log."','".$row->max_date."')");
				if(pmb_mysql_num_rows($res)){
					if((pmb_mysql_result($res,0,0))*1 > 1){
						//La date de fin est déjà suppérieur aux logs que l'on va ajouter
					}else{
						$this->date_fin_log=$row->max_date;
						
					}
				}
			}
		}
	}
	
	
	/**
	 * Fonction qui vérifie que la structure des tables de vues n'a pas été modifiée 
	 */
	public function check_structure($id_vue) {
		//Test pour savoir si la structure des colonnes a été modifiée
		$rqt_sum="select sum(maj_flag) from statopac_vues_col where num_vue=$id_vue";
		$res_sum=pmb_mysql_query($rqt_sum);
		$this->flag = pmb_mysql_result($res_sum,0,0);
		
		//On supprime la table dynamique si la structure a été modifiée
		if($this->flag) {
			$rqt_trunc = "DROP TABLE statopac_vue_".addslashes($id_vue);
			@pmb_mysql_query($rqt_trunc);
		}
		$rqt_create = "CREATE TABLE IF NOT EXISTS statopac_vue_".addslashes($id_vue)." (id_ligne INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY)";
		pmb_mysql_query($rqt_create);	
		
		//On supprime les données si demandé
		if($this->remove_data) {
			$rqt_trunc = "TRUNCATE TABLE statopac_vue_".addslashes($id_vue);
			@pmb_mysql_query($rqt_trunc);
		}
		//On supprime les données si demandé
		if($this->is_data_interval_selected()) {
			$rqt_trunc = "DELETE FROM statopac_vue_".addslashes($id_vue);
			$rqt_trunc .= " WHERE ";
			$filters = array();
			if(!empty($this->remove_data_interval_date_debut)) {
				$filters[] = "date >= '".$this->remove_data_interval_date_debut."'";
			}
			if(!empty($this->remove_data_interval_date_fin)) {
				$filters[] = "date < '".$this->remove_data_interval_date_fin."'";
			}
			$rqt_trunc .= implode(' AND ', $filters);
			@pmb_mysql_query($rqt_trunc);
		}
		
		// création des colonnes de la table de la vue
		$rqt_col = "select id_col, nom_col, expression, filtre, datatype from statopac_vues_col where num_vue='".addslashes($id_vue)."'";
		$res_col=pmb_mysql_query($rqt_col);
		$this->cols_vue=array();
		while(($col=pmb_mysql_fetch_object($res_col))){			
			//On ajoute les champs (indicateurs)
			$this->cols_vue[]=$col;
			$rqt_exists = "SHOW COLUMNS FROM statopac_vue_".addslashes($id_vue)." LIKE '".addslashes(trim($col->nom_col))."'";
			$res_exists = pmb_mysql_query($rqt_exists);
			if(pmb_mysql_num_rows($res_exists) == 0) {
				if($col->datatype == 'small_text')
					$type_col = 'varchar(255)';
				else $type_col = $col->datatype; 
				$rqt_addfield = "ALTER TABLE statopac_vue_".addslashes($id_vue)." ADD ".addslashes(trim($col->nom_col))." ".addslashes($type_col)." NOT NULL";
				pmb_mysql_query($rqt_addfield);
			}
		}
	}
	
	
	/**
	 * Fonction qui créée les tables dynamiques consolidées
	 */
	public function consolider($id_vue){
		global $tab_val, $liste_tabfiltre, $pmb_set_time_limit;
		//Gestion du timeout au niveau de php pour ne pas perdre la connection
		set_time_limit($pmb_set_time_limit);
		$q_ins='';
		$champ='';

		$rqt_tempo="SELECT * from logs_filtre_$id_vue";
		$res_tempo=pmb_mysql_query($rqt_tempo);
		$n_total=pmb_mysql_num_rows($res_tempo);
				
		$tab_val =array();
		$liste_tabfiltre = array();
		$n=0;
		
		$s_ins=pmb_mysql_num_rows($res_tempo);
		if ($s_ins) {

			$this->show_progress_bar();
			$this->set_progress_text(' '.$this->nom_vue.' : ');
			$this->set_progress_percent(0);
			
			$print_format=new parse_format('consolidation.inc.php');
			$percent_conserve='0';
			
			while( ($ligne=pmb_mysql_fetch_array($res_tempo))){
				$n++;
				$percent=round(($n/$n_total)*100);
				if ($percent_conserve!=$percent) { // $percent%5==0 && 
					$this->set_progress_percent($percent);
					$percent_conserve=$percent;
				}
				
				$resultat =array();
				
				foreach ($this->cols_vue as $col) {			
					
					// si filtre, pour chaque ligne de log :
					if($col->filtre){
						$rqt_create = "CREATE TEMPORARY TABLE  filtre_".$ligne[0]."_".$col->id_col." (
							`id_log` int( 8 ) unsigned NOT NULL AUTO_INCREMENT ,
							`date_log` timestamp NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
							`url_demandee` varchar( 255 ) NOT NULL default '',
							`url_referente` varchar( 255 ) NOT NULL default '',
							`get_log` blob NOT NULL ,
							`post_log` blob NOT NULL ,
							`num_session` varchar( 255 ) NOT NULL default '',
							`server_log` blob NOT NULL ,
							`empr_carac` blob NOT NULL ,
							`empr_doc` blob NOT NULL ,
							`empr_expl` blob NOT NULL ,
							`nb_result` blob NOT NULL ,
							 `gen_stat` blob NOT NULL ,
							PRIMARY KEY ( `id_log` )
						)";
						pmb_mysql_query($rqt_create);
						$print_format->cmd = $col->filtre ;
						$print_format->environnement['tempo']="logs_filtre_$id_vue";
						$print_format->environnement['num_ligne']=$ligne[0];
						$print_format->environnement['ligne']=$ligne;
						$val_filtre = $print_format->exec_cmd_conso();
						$filtre_tab = $this->creer_filtre($col->filtre,$val_filtre,$id_vue,$ligne[0],"filtre_".$ligne[0]."_".$col->id_col);
					}	
					
					$print_format->cmd = $col->expression;
					$print_format->environnement['tempo']="logs_filtre_$id_vue";
					$print_format->environnement['num_ligne']=$ligne[0];
					$print_format->environnement['ligne']=$ligne;
					if($col->filtre)$print_format->environnement['filtre']= $filtre_tab;
					$resultat[$col->nom_col] = $print_format->exec_cmd_conso();
					
				}
				$values='';	
				if(!$champ) $champ = implode(',',array_keys($resultat));
				foreach($resultat as $valeur){
					$values .= ($values ? ',\''.addslashes($valeur).'\'' : '\''.addslashes($valeur).'\'');
				}
				if (strlen($q_ins)>=1000000) {
					pmb_mysql_query($q_ins);	
					unset($q_ins);$q_ins='';
				}
				$q_ins.= ($q_ins)?',('.$values.')' : 'insert into  statopac_vue_'.addslashes($id_vue).' ('.$champ.') values ('.$values.')';
	
			}
			
			pmb_mysql_query($q_ins);	
			unset($q_ins);
			//On supprime les tables de filtres
			foreach ($liste_tabfiltre as $key=>$val){
				pmb_mysql_query("DROP TABLE ".$val);
			}
			pmb_mysql_query("drop table logs_filtre_$id_vue");
			
			//mise à jour des dates
			$q = "UPDATE statopac_vues SET date_consolidation=now(), date_debut_log='".$this->date_debut_log."', date_fin_log='".$this->date_fin_log."' WHERE id_vue='".addslashes($id_vue)."'";
			pmb_mysql_query($q);
			if($this->flag) pmb_mysql_query("UPDATE statopac_vues_col SET maj_flag=0 WHERE num_vue='".addslashes($id_vue)."'");
		}
	}

	
	/**
	 * Fonction qui permet de créer un filtre de résultat par rapport à une valeur 
	 */
	public function creer_filtre($filtre,$valeur_filtre,$id_vue,$ligne_repere,$table){
		global $tab_val, $liste_tabfiltre;
		
		$table_filtre ="";
		foreach($tab_val as $key=>$val){
			if($filtre == $val['filtre']){ 
				if($valeur_filtre == $val['valeur_filtre']){
					$table_filtre = $val['table_filtre'];
					 pmb_mysql_query("DROP TABLE ".$table);
					break;
				} else {
					pmb_mysql_query("DROP TABLE ".$val['table_filtre']);
					$ind=array_search($val['table_filtre'],$liste_tabfiltre) ;
					if($ind != false)
						unset($liste_tabfiltre[$ind]);	
					unset($tab_val[$key]);
				}
			} 				
		}
		if(!$table_filtre){
			$liste_tabfiltre[] = $table;
			$rqt="SELECT * from logs_filtre_$id_vue";
			$res=pmb_mysql_query($rqt);
		
			while($ligne_log=pmb_mysql_fetch_object($res)) {
			
				$format=new parse_format('consolidation.inc.php');	
				$format->environnement['tempo']="logs_filtre_$id_vue";
				$format->environnement['num_ligne']=$ligne_log->id_log;									
				$format->cmd = $filtre;
				$val_filtre_courant = $format->exec_cmd_conso();
				if($val_filtre_courant == $valeur_filtre){				
					$rqt="insert ignore into ".$table."  select * from logs_filtre_$id_vue where id_log='".addslashes($ligne_log->id_log)."'";
					pmb_mysql_query($rqt);				
				}
			}				
		}
		if(!$valeur_filtre) 
			$valeur_filtre="no_value";
		if(!$table_filtre)
			$tab_val[] = array('valeur_filtre' => $valeur_filtre, 'filtre' => $filtre, 'table_filtre' => $table);	
			
		return ($table_filtre ? $table_filtre : $table);
	}
	
	public function show_progress_bar(){
		if(empty(static::$displayed_progress_bar)) {
			print "<div class='row' style='text-align:center; width:80%; border: 1px solid #000000; padding: 4px;'>
				<div style='text-align:left; width:100%; height:16px;'>
					<img id='progress' src='".get_url_icon('jauge.png')."' style='width:1px; height:16px'/>
				</div>
				<div style='text-align:center'>
					<span id='progress_text'></span>&nbsp;
					<span id='progress_percent'></span>
				</div>
			</div>";
			flush();
			static::$displayed_progress_bar = true;
		}
	}
	
	public function init_progress_bar() {
		print "<script>document.getElementById('progress').src='".get_url_icon('jauge.png')."'</script>";
		flush();
	}
	
	public function set_progress_percent($percent) {
		print "<script>document.getElementById('progress').style.width='$percent%';
				document.getElementById('progress_percent').innerHTML='$percent%';
		</script>";
		flush();
	}
	
	public function set_progress_text($text){
		global $charset;
		print "<script>document.getElementById('progress_text').innerHTML='".htmlentities($text,ENT_QUOTES,$charset)."';</script>";
		flush();
	}
	
	public function set_remove_data_interval_date_debut($remove_data_interval_date_debut) {
		$this->remove_data_interval_date_debut = $remove_data_interval_date_debut;
	}
	
	public function set_remove_data_interval_date_fin($remove_data_interval_date_fin) {
		$this->remove_data_interval_date_fin = $remove_data_interval_date_fin;
	}
	
	public function is_data_interval_selected() {
		if($this->remove_data_interval && (!empty($this->remove_data_interval_date_debut) || !empty($this->remove_data_interval_date_fin))) {
			return true;
		}
		return false;
	}
}
	
