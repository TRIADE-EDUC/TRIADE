<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_pperso.class.php,v 1.30 2023/08/30 14:32:32 qvarin Exp $

use Pmb\Animations\Models\AnimationModel;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion champs perso des autorités

global $class_path;
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/author.class.php");

class aut_pperso {
	
	public $aut = ""; // prefixe de l'autorité	
	
	public $id = 0; // id de l'autorité
	
	public $error_message = "";
	public $p_perso = null;

	public static $custom_fields_using_datatype_by_prefix = array();
	
	public static $fields_recherche_mot = array();

	public static $fields_recherche = array();

	public static $fields_recherche_mot_array = array();
	
	public function __construct($aut,$id=0) {
		$this->aut = $aut;
		$this->id = intval($id);
		$this->p_perso=new parametres_perso($this->aut);
		$this->getdata();
	}	

	public function getdata() {
		$this->error_message="";
	}

	public function get_form() {
		global $charset;
		$perso="";
		$perso_=$this->p_perso->show_editable_fields($this->id);
		if(isset($perso_["FIELDS"])) {
			if (count($perso_["FIELDS"])) $perso .= "<div class='row'></div>" ;
			$class="colonne2";
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				
				$perso.="<div id='el9Child_".$p["ID"]."' class='row' movable='yes' title=\"".htmlentities($p["TITRE"], ENT_QUOTES, $charset)."\">";
				$perso.="<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."</div>\n";
				$perso.="<div class='row'>";
				$perso.=$p["AFF"]."</div>";
				$perso.="</div>";
				if ($class=="colonne2") $class="colonne_suite"; else $class="colonne2";
			}
			if ($class=="colonne_suite") $perso.="<div class='$class'>&nbsp;</div>";
			$perso.=$perso_["CHECK_SCRIPTS"];
		}
		return $perso;
	}
	
	public function save_form() {
		$nberrors=$this->p_perso->check_submited_fields();
		$this->error_message=$this->p_perso->error_message;
		if(!$nberrors){
			$this->p_perso->rec_fields_perso($this->id);
			return 0;
		}
		return 	$nberrors;
			
	}
	
	public function delete() {
		$this->p_perso->delete_values($this->id);
	}
	
	public function get_base_values($name,$id){
		return $this->p_perso->read_base_fields_perso_values($name,$id);
	}
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité
	public function get_fields_recherche($id){
		if (!isset(static::$fields_recherche[$id])) {
			if(isset(static::$fields_recherche) && count(static::$fields_recherche) > 500) {
				// Parade pour éviter le dépassement de mémoire
				static::$fields_recherche = array();
			}
			static::$fields_recherche[$id] = $this->p_perso->get_fields_recherche($id);
		}
		return static::$fields_recherche[$id];
	}
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité sous forme d'un tableau par champ perso
	public function get_fields_recherche_mot($id) {
		if (!isset(static::$fields_recherche_mot[$id])) {
			if(isset(static::$fields_recherche_mot) && count(static::$fields_recherche_mot) > 500) {
				// Parade pour éviter le dépassement de mémoire
				static::$fields_recherche_mot = array();
			}
			static::$fields_recherche_mot[$id] = $this->p_perso->get_fields_recherche_mot($id);
		}
		return static::$fields_recherche_mot[$id];
	}		
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité sous forme d'un tableau par champ perso
	public function get_fields_recherche_mot_array($id){
		if (!isset(static::$fields_recherche_mot_array[$id])) {
			if(isset(static::$fields_recherche_mot_array) && count(static::$fields_recherche_mot_array) > 500) {
				// Parade pour éviter le dépassement de mémoire
				static::$fields_recherche_mot_array = array();
			}
			static::$fields_recherche_mot_array[$id] = $this->p_perso->get_fields_recherche_mot_array($id);
		}
		return static::$fields_recherche_mot_array[$id];
	}
	
	protected static function get_data_type($aut_tab, $id) {
		
		$data_type=$aut_tab;
		
		switch($aut_tab){
			case AUT_TABLE_INDEXINT :
				$data_type=7;
				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				$data_type=8;
				break;
			case AUT_TABLE_AUTHPERSO :
				$auth=new authperso(0,$id);
				$data_type=1000+$auth->id;
				break;
			case AUT_TABLE_CONCEPT :
				$data_type=9;
				break;
		}
		
		return $data_type;
	}
	
	protected static function get_all_table_prefix() {
		return array(
			'author',
			'authperso',
			'categ',
			'cms_editorial',
			'collection',
			'indexint',
			'notices',
			'publisher',
			'serie',
			'subcollection',
			'tu',		    
		    'empr',
		    'skos',
		    'collstate',
		    'demandes',
		    'expl',
		    'explnum',
		    'pret',
			'gestfic0',
			'anim_animation'
		);
	}
	
	public static function delete_pperso($aut_tab,$id, $force_to_delete=0) {
		if(!$aut_tab || !$id) return;
		/*
			<select onchange="option_data_type_change(this.value);" name="DATA_TYPE">
			<option value="1">Auteurs</option>
			<option value="2">Catégories</option>
			<option value="3">Éditeurs</option>
			<option value="4">Collections</option>
			<option value="5">Sous-collections</option>
			<option value="6">Titres de série</option>
			<option value="7">Index. décimales</option>
			<option value="8">Titre uniforme</option>
			<option value="9">Concepts</option>
			<option value="1001">Les pays</option>
			<option value="1003">Publications</option>
			<option value="1002">Ville</option>
			</select>
	
			define('AUT_TABLE_AUTHORS',1);
			define('AUT_TABLE_CATEG',2);
			define('AUT_TABLE_PUBLISHERS',3);
			define('AUT_TABLE_COLLECTIONS',4);
			define('AUT_TABLE_SUB_COLLECTIONS',5);
			define('AUT_TABLE_SERIES',6);
			define('AUT_TABLE_TITRES_UNIFORMES',7);
			define('AUT_TABLE_INDEXINT',8);
			define('AUT_TABLE_AUTHPERSO',9);
			define('AUT_TABLE_CONCEPT',10);
            define('AUT_TABLE_INDEX_CONCEPT',11);
            // Pour la classe authorities_collection
            define('AUT_TABLE_CATEGORIES',12);
            define('AUT_TABLE_AUTHORITY',13);
            // authperso >1000
            define('AUT_TABLE_ANIMATION',14);
		*/
		
		$data_type = self::get_data_type($aut_tab, $id);
		
		$all_table_prefix = self::get_all_table_prefix();
		
		$usage=array();
		$query_to_del=array();
		foreach($all_table_prefix as $prefix){
			// recherche dans xx_custom le nom du champ ou est mémorisé l'id de l'autorité à supprimer
			$query= "SELECT * FROM ".$prefix."_custom where ExtractValue(options, '//DATA_TYPE') = '".$data_type."' and type='query_auth'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$row_name = $prefix.'_custom_'.$row->datatype;
					// Mémorisation des usages pour une demande de forcage avant suppression
					$query_to_view= "SELECT * FROM ".$prefix."_custom_values where ".$row_name." = '".$id."' and ".$prefix."_custom_champ='".$row->idchamp."'";
					$result_to_view = pmb_mysql_query($query_to_view);
					if(pmb_mysql_num_rows($result_to_view)){
						$usage['data'][$prefix][$row->idchamp]['field']=$row;
						$usage['display'].=$row->name.'<br/>';
						while($row_to_view = pmb_mysql_fetch_object($result_to_view)){
							$usage['data'][$prefix][$row->idchamp]['objects'][]=$row_to_view;
							$id_name=$prefix.'_custom_origine';
							$display='';
							switch($prefix){
								case 'author': $auth=new auteur($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'authperso': $auth=new authperso(); $display='<a class="lien_gestion" title="" href="./autorites.php?categ=see&sub=authperso&id='.$row_to_view->$id_name.'">'.$auth->get_view($row_to_view->$id_name).'</a>'; break;
								case 'categ': $auth=new category($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'cms_editorial':$article = new cms_article($row_to_view->$id_name); $display=$article->title." ( id : ".$row_to_view->$id_name." )"; break;
								case 'collection': $auth=new collection($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'indexint': $auth=new indexint($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'notices': $display=notice::get_notice_view_link($row_to_view->$id_name); break;									
								case 'publisher': $auth=new editeur($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'serie': $auth=new serie($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'subcollection': $auth=new subcollection($row_to_view->$id_name); $display=$auth->isbd_entry_lien_gestion; break;
								case 'tu': $auth=new titre_uniforme($row_to_view->$id_name); $display='<a class="lien_gestion" title="" href="./autorites.php?categ=see&sub=titre_uniforme&id='.$row_to_view->$id_name.'">'.$auth->get_isbd_simple().'</a>'; break;								
								case 'anim_animation':
								    $animation = new AnimationModel($row_to_view->$id_name);
								    $display = '<a class="lien_gestion" title="" href="./animations.php?categ=animations&action=view&id='.$row_to_view->$id_name.'">'.$animation->name.'</a>';
								    break;
							}
							$usage['display'].=$display.'<br/>';
						}
					}
					// Pour suppression
					$query_to_del[]= "DELETE FROM ".$prefix."_custom_values where ".$row_name." = '".$id."' and ".$prefix."_custom_champ='".$row->idchamp."'";										
				}
			}
		}
		if($force_to_delete || !count($usage)){
			foreach ($query_to_del as $query){
				pmb_mysql_query($query);
			}			
		}			
		return $usage;
	}
	
	public static function replace_pperso($aut_tab, $id, $by) {
		if(!$aut_tab || !$id ||!$by) return;
	
		$data_type = self::get_data_type($aut_tab, $id);
		
		$all_table_prefix = self::get_all_table_prefix();
		
		foreach($all_table_prefix as $prefix){
			// recherche dans xx_custom le nom du champ ou est mémorisé l'id de l'autorité à supprimer
			$query= "SELECT * FROM ".$prefix."_custom where ExtractValue(options, '//DATA_TYPE') = '".$data_type."' and type='query_auth'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$row_name = $prefix.'_custom_'.$row->datatype;
					$query_replace= "update ".$prefix."_custom_values set ".$row_name." = '".$by."' where ".$row_name." = '".$id."' and ".$prefix."_custom_champ=".$row->idchamp;
					pmb_mysql_query($query_replace);
				}
			}
		}
	}

	public static function get_used($aut_tab, $id, $tmp_used_in_pperso_authorities) {
	    global $default_tmp_storage_engine;

		if(!$aut_tab || !$id) return;
		
		$data_type = self::get_data_type($aut_tab, $id);
		$all_table_prefix = self::get_all_table_prefix();
		
		$aut_queries = array();	
		$notice_queries = array();	
		$cms_editorial_queries = array();
		
		$query = 'CREATE TEMPORARY TABLE '.$tmp_used_in_pperso_authorities.' (type_object int, id int ) ENGINE='. $default_tmp_storage_engine .' ';
		pmb_mysql_query($query);
		
		static::get_custom_fields_using_datatype_by_prefix($data_type);
		foreach($all_table_prefix as $prefix) {
		    $custom_fields = static::$custom_fields_using_datatype_by_prefix[$data_type][$prefix];
		    if (empty($custom_fields)) {
		        continue;
		    }
		    
		    $index = count($custom_fields);
		    for ($i = 0; $i < $index; $i++) {
		        $custom_field = $custom_fields[$i];
		        
		        $colunm_with_value = $prefix . '_custom_' . $custom_field['datatype'];
		        $colunm_with_origine = $prefix . '_custom_origine';
		        
		        $clause_where = $colunm_with_value . "='" . $id . "'";
		        if ($colunm_with_value == $prefix . '_custom_small_text' && $aut_tab == AUT_TABLE_CONCEPT) {
	                // Pour les concepts, on peut avoir des URI
	                $clause_where = "($colunm_with_value = '$id' or $colunm_with_value = '" . onto_common_uri::get_uri($id) . "')";
		        }
		        
		        $query = "SELECT $colunm_with_origine FROM " . $prefix . "_custom_values";
		        $query .= " WHERE $clause_where and " . $prefix . "_custom_champ=" . $custom_field['idchamp'];
		        $result = pmb_mysql_query($query);
		        if (pmb_mysql_num_rows($result)) {
		            while ($row = pmb_mysql_fetch_assoc($result)) {
		                $type_object = 0;

		                switch($prefix) {
		                    case 'cms_editorial':
		                        $query_editorial_type = "SELECT editorial_type_element FROM cms_editorial_types WHERE id_editorial_type = " . $custom_field['num_type'];
		                        $result_editorial_type = pmb_mysql_query($query_editorial_type);
		                        if (pmb_mysql_num_rows($result_editorial_type)) {
		                            $editorial_type_element = pmb_mysql_result($result_editorial_type, 1, 0);
		                            $type = 0;
		                            switch($editorial_type_element){
		                                case 'article_generic':
		                                case 'article':	
		                                    $type= 20;
		                                    break;
		                                case 'section_generic':
		                                case 'section':	
		                                    $type= 21;
		                                    break;
		                            }
		                            if($type) $cms_editorial_queries[]=" ( " . $type . ", " . $row[$colunm_with_origine] . ") ";
		                        }
		                        break;
		                    case 'notices':
		                        $notice_queries[]=" ( 50, " . $row[$colunm_with_origine] . ") "; break;
		                    case 'author': 
		                        $type_object=AUT_TABLE_AUTHORS; break;
		                    case 'authperso': 
		                        $type_object=AUT_TABLE_AUTHPERSO;  break;
		                    case 'categ': 
		                        $type_object=AUT_TABLE_CATEG; break;
		                    case 'collection': 
		                        $type_object=AUT_TABLE_COLLECTIONS; break;
		                    case 'indexint': 
		                        $type_object=AUT_TABLE_INDEXINT; break;
		                    case 'publisher': 
		                        $type_object=AUT_TABLE_PUBLISHERS; break;
		                    case 'serie': 
		                        $type_object=AUT_TABLE_SERIES; break;
		                    case 'subcollection':  
		                        $type_object=AUT_TABLE_SUB_COLLECTIONS; break;
		                    case 'tu':  
		                        $type_object=AUT_TABLE_TITRES_UNIFORMES; break;
		                    case 'anim_animation':
		                        $type_object = AUT_TABLE_ANIMATION; break;
		                }
		                if ($type_object) {
		                    $aut_queries[] = "( type_object =".$type_object." and num_object =".$row[$colunm_with_origine].") ";
		                }
		                if (count($aut_queries) > 300) {
		                    $query_auth= "INSERT INTO ".$tmp_used_in_pperso_authorities." (type_object, id) SELECT type_object, id_authority FROM authorities WHERE ".implode(' OR ',$aut_queries)." ";
		                    pmb_mysql_query($query_auth);
		                    $aut_queries=array();
		                }
		                if (count($notice_queries) > 300) {
		                    $query_auth= "INSERT INTO ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$notice_queries)." ";
		                    pmb_mysql_query($query_auth);
		                    $notice_queries=array();
		                }
		                if (count($cms_editorial_queries) > 300) {
		                    $query_auth= "INSERT INTO ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$cms_editorial_queries)." ";
		                    pmb_mysql_query($query_auth);
		                    $cms_editorial_queries=array();
		                }
		            }
		        }
		    }
		}
		if(!empty($aut_queries) && count($aut_queries)) {
		    $query_auth= "INSERT INTO ".$tmp_used_in_pperso_authorities." (type_object, id) SELECT type_object, id_authority  FROM authorities WHERE ".implode(' OR ',$aut_queries)." ";
		    pmb_mysql_query($query_auth);
		}
		if(!empty($notice_queries) && count($notice_queries)) {
		    $query_auth= "INSERT INTO ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$notice_queries)." ";
		    pmb_mysql_query($query_auth);
		}
		if(!empty($cms_editorial_queries) && count($cms_editorial_queries)) {
		    $query_auth= "INSERT INTO ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$cms_editorial_queries)." ";
		    pmb_mysql_query($query_auth);
		}
		return 1;
	}	

	public static function get_custom_fields_using_datatype_by_prefix($data_type) 
	{
		if (!isset(static::$custom_fields_using_datatype_by_prefix[$data_type])) {
			if(isset(static::$custom_fields_using_datatype_by_prefix) && count(static::$custom_fields_using_datatype_by_prefix) > 500) {
				// Parade pour éviter le dépassement de mémoire
				static::$custom_fields_using_datatype_by_prefix = array();
			}
    	    static::$custom_fields_using_datatype_by_prefix[$data_type] = [];
    	    
    	    foreach(self::get_all_table_prefix() as $prefix) {
    	        if (!isset(static::$custom_fields_using_datatype_by_prefix[$data_type][$prefix])) {
    	            static::$custom_fields_using_datatype_by_prefix[$data_type][$prefix] = [];
    	        }
    	        
        	    $query= "SELECT * FROM " . $prefix . "_custom where ExtractValue(options, '//DATA_TYPE') = '$data_type' and type='query_auth'";
        	    $result = pmb_mysql_query($query);
        	    if (pmb_mysql_num_rows($result)) {
        	        while ($row = pmb_mysql_fetch_assoc($result)) {
        	            static::$custom_fields_using_datatype_by_prefix[$data_type][$prefix][] = $row;
        	        }
        	        pmb_mysql_free_result($result);
        	    }
    	    }
	    }
	    return static::$custom_fields_using_datatype_by_prefix[$data_type];
	}
	
// fin class
}