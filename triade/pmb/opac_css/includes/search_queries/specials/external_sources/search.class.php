<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.35 2022/10/27 10:10:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path;
require_once($class_path."/connecteurs.class.php");

//Classe de gestion de la recherche spécial "combine"

class external_sources {
    
	public $id;
	public $n_ligne;	//Numéro de ligne du critère dans la multi-critère
	public $params;		//
	public $search;		//Classe d'origine de la recherche
	
	const REPOSITORY_SYNC = 2;

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
    /**
     * fonction de récupération des opérateurs disponibles pour ce champ spécial (renvoie un tableau d'opérateurs)
     * @return array
     */
    public function get_op() {
    	$operators = array();
    	$operators["EQ"]="=";
    	return $operators;
    }
    
    public function get_input_box() {
    	global $msg,$charset;
    	
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	${$valeur_} = array_map('intval',${$valeur_});
    	array_unique(${$valeur_});
    	$valeur=${$valeur_};
    	
    	if ((!$valeur)&&(isset($_SESSION["checked_sources"]))) $valeur=$_SESSION["checked_sources"];
    	if (!is_array($valeur)) $valeur=array();
    	
    	//Recherche des sources
    	$requete="SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) WHERE connectors_sources.opac_allowed=1 ORDER BY connectors_categ_sources.num_categ DESC, connectors_sources.name";
    	$resultat=pmb_mysql_query($requete);
    	$r="<select name='field_".$this->n_ligne."_s_".$this->id."[]' multiple='yes'>";
    	$current_categ=0;
    	$count = 0;
    	while ($source=pmb_mysql_fetch_object($resultat)) {
    		if ($current_categ !== $source->num_categ) {
    			$current_categ = $source->num_categ;
    			$source->categ_name = $source->categ_name ? $source->categ_name : $msg["source_no_category"];
    			$r .= "<optgroup label='".$source->categ_name."'>";
    			$count++;
    		}
    		$r.="<option id='op_".$source->source_id."_".$count."' value='".$source->source_id."'".(array_search($source->source_id,$valeur)!==false?" selected":"").">".htmlentities($source->name.($source->comment?" : ".$source->comment:""),ENT_QUOTES,$charset)."</option>\n";
    	}
    	$r.="</select>";
    	return $r;
    }
    
   
    /**
     * fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
     */
    public function transform_input() {
    }
    
    protected function has_search_exists($source_id, $search_id) {
    	$requete="select count(1) from entrepot_source_$source_id where search_id='".addslashes($search_id)."'";
    	$resultat=pmb_mysql_query($requete);
    	if(pmb_mysql_result($resultat,0,0)) {
    		return true;
    	}
    	return false;
    }
    
    protected function has_search_outdated($source_id, $search_id, $ttl) {
    	$requete="select count(1) from entrepot_source_$source_id where search_id='".addslashes($search_id)."' and unix_timestamp(now())-unix_timestamp(date_import)>".$ttl;
    	$resultat=pmb_mysql_query($requete);
    	if(pmb_mysql_result($resultat,0,0)) {
    		return true;
    	}
    	return false;
    }
    
    /**
     * Fonction de création de la requête (retourne une table temporaire)
     * @return string
     */
    public function make_search() {	
    	global $search;
    	global $selected_sources;
        global $search_dont_check;
        global $tsearched_sources;
        
        //On modifie l'opérateur suivant !!
		$inter_next = "inter_".($this->n_ligne+1)."_".$search[$this->n_ligne+1];
		global ${$inter_next};
		if (${$inter_next}) ${$inter_next} = "or";

    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	${$valeur_} = array_map('intval',${$valeur_});
    	array_unique(${$valeur_});
    	$valeur=${$valeur_};
    	if(is_array($valeur)) {
    		$_SESSION["checked_sources"] = $valeur;
    	}
    	global $charset, $class_path,$include_path,$base_path;
    	
    	//Override le timeout du serveur mysql, pour être sûr que le socket dure assez longtemps pour aller jusqu'aux ajouts des résultats dans la base. 
		$sql = "set wait_timeout = 300";
		pmb_mysql_query($sql);
    	
        $tsearched_sources=[];
        $tselected_sources=[];
        
    	for ($i=0; $i<count($valeur); $i++) {
    		if(!$valeur[$i]) continue;
    		//Recherche de la source
    		$source = connecteurs::get_class_name($valeur[$i]);
    		if ($source && file_exists($base_path."/admin/connecteurs/in/$source/$source.class.php")) {
				require_once($base_path."/admin/connecteurs/in/$source/$source.class.php");
				eval("\$src=new $source(\"".$base_path."/admin/connecteurs/in/".$source."\");");
				$params = $src->get_source_params($valeur[$i]);
    		}
    		/**
    		 * On vérifie si le connecteur est asynchrone ou synchrone
    		 */
    		if ($source && $params["REPOSITORY"] == self::REPOSITORY_SYNC) {
                $tsearched_sources[] = $valeur[$i];
                $source_id = $valeur[$i];
    			$unimarc_query = $this->search->make_unimarc_query();
    			$search_id = md5(serialize($unimarc_query));

				//Suppression des vieilles notices
				//Vérification du ttl
				$ttl=$params["TTL"];
				$requete="delete from entrepot_source_$source_id where unix_timestamp(now())-unix_timestamp(date_import)>".$ttl;
				pmb_mysql_query($requete);
				
				if (empty($search_dont_check)) {
					$search_exists = $this->has_search_exists($source_id, $search_id);
				} else $search_exists=false;
				$search_outdated = $this->has_search_outdated($source_id, $search_id, $ttl);
				if ($search_outdated || (!$search_outdated && !$search_exists)) {
					if ($search_outdated) {
						//Suppression des notices
						$requete="delete from entrepot_source_$source_id where search_id='".addslashes($search_id)."'";
						pmb_mysql_query($requete);
					}
					//Recherche si on a le droit
					$flag_search=true;
					$now=time();
					if(isset($_SESSION["source_".$source_id."_last_cancel"])){
						if($now-$_SESSION["source_".$source_id."_last_cancel"]>300){
							unset($_SESSION["source_".$source_id."_last_cancel"]);
							unset($_SESSION["source_".$source_id."_cancel"]);
						} else $flag_search=false;
					}
					if ($flag_search) {
						$flag_error=false;
						for ($j=0; $j<$params["RETRY"]; $j++) {
							$src->search($valeur[$i],$unimarc_query,$search_id);
							if (!$src->error) {
								break;
							} else {
								$flag_error=true;
							}
						}
						//Il y a eu trois essais infructueux, on désactive pendant 5 min !!
						if ($flag_error) {
							$_SESSION["source_".$source_id."_last_cancel"]=$now;
							$_SESSION["source_".$source_id."_cancel"]=2;
						}
					}
    			}
    		}
			else {
            	$tselected_sources[]=$valeur[$i];
			}
       	}
       	
       	//Sources
       	$tvaleur=array();
       	for ($i=0; $i<count($valeur); $i++) {
       		$tvaleur[]=$valeur[$i];
       	}
       	$selected_sources=implode(",",$tvaleur);
		$t_table="t_sources_".$this->n_ligne;
		
		pmb_mysql_query("drop table if exists $t_table");
		$requete="create temporary table ".$t_table." (notice_id integer unsigned not null)";
		pmb_mysql_query($requete);
		global $search_previous_table;
		$search_previous_table=$t_table."_save";
		pmb_mysql_query("drop table if exists $search_previous_table");
		$requete="create temporary table ".$search_previous_table." (notice_id integer unsigned not null, i_value varchar(255), idiot int(1), pert decimal(16,1) default 1)";
		pmb_mysql_query($requete);
		$query = "alter table ".$search_previous_table." add unique(notice_id)";
		pmb_mysql_query($query);
		for ($i=0; $i<count($tsearched_sources); $i++) {
			$requete="insert into $search_previous_table select distinct recid as notice_id,'',1,1 from entrepot_source_".$tsearched_sources[$i]." where search_id='".$search_id."'";
			pmb_mysql_query($requete);
		}
		return $t_table;
    }
    
    /**
     * Fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
     * @return array
     */
    public function make_human_query() {
    	$litteral=array();
    	
    	//Récupération de la valeur de saisie 
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	${$valeur_} = array_map('intval',${$valeur_});
    	array_unique(${$valeur_});
    	$valeur=${$valeur_};
    	
    	array_walk($valeur, "intval");
    	array_unique($valeur);
    	if(isset($valeur) && is_array($valeur) && count($valeur)){
    		$requete="select name from connectors_sources where source_id in (".implode(",",$valeur).") and opac_allowed=1";
	    	$resultat=pmb_mysql_query($requete);
	    	while ($r=pmb_mysql_fetch_object($resultat)) {
	    		$litteral[]=$r->name;
	    	}
    	}
		return $litteral;    
    }
     
    public function make_unimarc_query() {
    	return array();
    }
    
	/**
	 * Fonction de vérification du champ saisi ou sélectionné
	 * @param array|object $valeur
	 * @return boolean
	 */
    public function is_empty($valeur) {
    	if (count($valeur)) return false; else return true;
    }
}
?>