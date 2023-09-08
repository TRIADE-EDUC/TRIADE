<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_pperso.class.php,v 1.23 2018-11-21 13:07:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion champs perso des autorités

require_once($class_path."/parametres_perso.class.php");

require_once($class_path."/author.class.php");

class aut_pperso {
	public $aut=""; // prefixe de l'autorité	
	public $id=0; // id de l'autorité
	public $error_message="";
	
	public function __construct($aut,$id=0) {
		$this->aut = $aut;
		$this->id = $id+0;
		$this->p_perso=new parametres_perso($this->aut);
		$this->getdata();
	}	

	public function getdata() {
		global $charset,$dbh,$msg;
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
		global $dbh;
		
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
		return $this->p_perso->get_fields_recherche($id);
	}
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité sous forme d'un tableau par champ perso
	public function get_fields_recherche_mot($id){
		return $this->p_perso->get_fields_recherche_mot($id);
	}		
	
	// retourne la liste des valeurs des champs perso cherchable d'une autorité sous forme d'un tableau par champ perso
	public function get_fields_recherche_mot_array($id){
		return $this->p_perso->get_fields_recherche_mot_array($id);
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
			'gestfic0'
		);
	}
	
	public static function delete_pperso($aut_tab,$id, $force_to_delete=0) {
		global $dbh;
		
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
		global $dbh;
	
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
		return;
	}

	public static function get_used($aut_tab,$id,$tmp_used_in_pperso_authorities) {
		global $dbh;
	
		if(!$aut_tab || !$id) return;
		
		$data_type = self::get_data_type($aut_tab, $id);
		
		$all_table_prefix = self::get_all_table_prefix();
		
		$aut_queries=array();	
		$notice_queries=array();	
		$cms_editorial_queries=array();					
		$query='create temporary table '.$tmp_used_in_pperso_authorities.' (type_object int, id int ) ENGINE=MyISAM ';
		pmb_mysql_query($query,$dbh);
		
		foreach($all_table_prefix as $prefix){
			// recherche dans xx_custom le nom du champ ou est mémorisé l'id de l'autorité
			$query= "SELECT * FROM ".$prefix."_custom where ExtractValue(options, '//DATA_TYPE') = '$data_type' and type='query_auth'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$row_name = $prefix.'_custom_'.$row->datatype;
					$test_id = $id;
					if($row_name == $prefix.'_custom_small_text'){
						$test_id =  "'".$id."'";
					}
					$id_name=$prefix.'_custom_origine';
					// Mémorisation des usages
					$query_to_view= "SELECT ".$id_name." FROM ".$prefix."_custom_values where $row_name = ".$test_id." and ".$prefix."_custom_champ=".$row->idchamp;
					$result_to_view = pmb_mysql_query($query_to_view);
					if(pmb_mysql_num_rows($result_to_view)){
						while($row_to_view = pmb_mysql_fetch_object($result_to_view)){
							$type_object=0;
							switch($prefix){
								case 'cms_editorial':
									$query_editorial_type = "select editorial_type_element from cms_editorial_types where id_editorial_type =$row->num_type";
									$result_editorial_type = pmb_mysql_query($query_editorial_type,$dbh);
									if(pmb_mysql_num_rows($result_editorial_type)){
										$row_editorial_type = pmb_mysql_fetch_object($result_editorial_type);
										$type=0;
										switch($row_editorial_type->editorial_type_element){
											case 'article_generic':
											case 'article':	$type= 20; break;
											case 'section_generic':
											case 'section':	$type= 21; break;
										}										
										if($type) $cms_editorial_queries[]=" ( ".$type.", ".$row_to_view->$id_name.") ";
									}										
									break;
								case 'notices':	
									$notice_queries[]=" ( 50, ".$row_to_view->$id_name.") ";
									break;
								case 'author': $type_object=AUT_TABLE_AUTHORS; break;
								case 'authperso': $type_object=AUT_TABLE_AUTHPERSO;  break;
								case 'categ': $type_object=AUT_TABLE_CATEG; break;
								case 'collection': $type_object=AUT_TABLE_COLLECTIONS; break;
								case 'indexint': $type_object=AUT_TABLE_INDEXINT; break;
								case 'publisher': $type_object=AUT_TABLE_PUBLISHERS; break;
								case 'serie': $type_object=AUT_TABLE_SERIES; break;
								case 'subcollection':  $type_object=AUT_TABLE_SUB_COLLECTIONS; break;
								case 'tu':  $type_object=AUT_TABLE_TITRES_UNIFORMES; break;
							}
							if($type_object){
								$aut_queries[]="( type_object =".$type_object." and num_object =".$row_to_view->$id_name.") ";
							}
							if(count($aut_queries)>300){
								$query_auth= "insert into ".$tmp_used_in_pperso_authorities." (type_object, id) SELECT type_object, id_authority  FROM authorities where ".implode(' or ',$aut_queries)." ";
								pmb_mysql_query($query_auth);								
								$aut_queries=array();								
							}
							if(count($notice_queries)>300){
								$query_auth= "insert into ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$notice_queries)." ";
								pmb_mysql_query($query_auth);								
								$notice_queries=array();								
							}
							if(count($cms_editorial_queries)>300){
								$query_auth= "insert into ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$cms_editorial_queries)." ";
								pmb_mysql_query($query_auth);								
								$cms_editorial_queries=array();								
							}
						}
					}
				}
			}
		}
		if(count($aut_queries)){
			$query_auth= "insert into ".$tmp_used_in_pperso_authorities." (type_object, id) SELECT type_object, id_authority  FROM authorities where ".implode(' or ',$aut_queries)." ";
			pmb_mysql_query($query_auth);											
		}
		if(count($notice_queries)){
			$query_auth= "insert into ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$notice_queries)." ";
			pmb_mysql_query($query_auth);
		}
		if(count($cms_editorial_queries)){
			$query_auth= "insert into ".$tmp_used_in_pperso_authorities." (type_object, id) VALUES ".implode(', ',$cms_editorial_queries)." ";
			pmb_mysql_query($query_auth);								
			$cms_editorial_queries=array();								
		}
		
		
		return 1;
	}	
	
// fin class
}