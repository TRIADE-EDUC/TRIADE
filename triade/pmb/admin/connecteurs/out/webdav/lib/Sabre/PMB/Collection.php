<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Collection.php,v 1.51.4.2 2025/03/14 08:07:35 qvarin Exp $
namespace Sabre\PMB;

use Sabre\DAV;
use Sabre\PMB;
use encoding_normalize;

class Collection extends DAV\Collection {
	public $type;
	public $config;
	public $restricted_objects;
	public $parentNode;
	protected $notices;
	protected $formatted_name;
	protected static $acces;
	protected static $domain = array();

	public function __construct($config){
		$this->config = $config;
	}

	public function get_code_from_name($name){
	    global $matches;
		$val="";
		if(preg_match("/\((T|R|[ODNTCSIEL][0-9]{1,})\)$/i",$name,$matches)){
			$val=$matches[1];
		}elseif(preg_match("/\((T|R|[ODNTCSIEL][0-9]{1,})\)\./i",$name,$matches)){
			$val=$matches[1];
		}
		return $val;
	}

	public function get_notice_by_meta($name,$filename){
		\create_tableau_mimetype();
		$mimetype = \trouve_mimetype($filename,extension_fichier($name));
		//on commence avec la gymnatisque des métas...
		if($mimetype == "application/epub+zip"){
			//pour les ebook, on gère ca directement ici !
			$epub = new \epub_Data(realpath($filename));
			$metas=$epub->metas;
			$img = imagecreatefromstring($epub->getCoverContent());
			$file=tempnam(sys_get_temp_dir(),"vign");
			imagepng($img,$file);
			$metas['thumbnail_content'] = file_get_contents($file);
			unlink($file);
		}else{
			$metas = \extract_metas(realpath($filename),$mimetype);
		}
		$metasMapper = $this->load_metas_mapper();
		return $metasMapper->get_notice_id($metas, $mimetype,$name,false);
	}

	protected function load_metas_mapper(){
		if($this->config['metasMapper_class']){
			$this->load_class_mapper($this->config['metasMapper_class']);
			if(class_exists($this->config['metasMapper_class'])){
				$class_name = $this->config['metasMapper_class'];
				return new $class_name($this->config);
			}
		}
		$this->load_class_mapper("metasMapper");
		return new \metasMapper($this->config);
	}

	protected function load_class_mapper($class_name){
		global $class_path;
		require_once($class_path."/webdav_mapper/".$class_name.".class.php");
	}

	public function set_parent($parent){
		$this->parentNode = $parent;
	}
	public function getChildren(){
		global $msg, $tdoc;

		$children = array();
		$children_type = "";
		if($this->type == "rootNode"){
			$children_type = $this->config['tree'][0];
		}else{
			for($i=0 ; $i<count($this->config['tree']) ; $i++){
				if($this->config['tree'][$i] == $this->type){
					if($this->config['tree'][$i+1]){
						$children_type = $this->config['tree'][$i+1];
					}
					break;
				}
			}
		}
		$tmp=$this->getNotices();//On calcule les restrictions
		switch($children_type){
			case "categorie" :
				$thes = new \thesaurus($this->config['used_thesaurus']);
				$node = new PMB\Categorie("(C".$thes->num_noeud_racine.")",$this->config);
				$node->restricted_objects=$this->restricted_objects;//On prends en compte les restrictions pour le cas on ne voudrait que les catégories avec des notices
				$children = $node->getChildren();
				break;
			case "typdoc" :
				if (empty($tdoc)) $tdoc = new \marc_list('doctype');
				foreach($tdoc->table as $label){
					$children[] = new PMB\Typdoc(PMB\Typdoc::format_typdoc($label). " (T)" ,$this->config);
				}
				break;
			case "statut" :
				$query = "select id_notice_statut from notice_statut";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$children[] = new PMB\Statut("(S".$row->id_notice_statut.")",$this->config);
					}
				}
				break;
			case "indexint" :
				$query = "select * from indexint where indexint_id not in (select child.indexint_id from indexint join indexint as child on child.indexint_name like concat(indexint.indexint_name,'%') and indexint.indexint_id != child.indexint_id group by child.indexint_id) order by indexint_name";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$children[] = new PMB\Indexint("(I".$row->indexint_id.")",$this->config);
					}
				}
				break;
			case "liste_lecture" :
				if($this->config['authentication'] == 'opac') {
					global $webdav_current_user_id;
					$query = "select distinct id_liste from opac_liste_lecture
							where num_empr = '".$webdav_current_user_id."'
							or id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$webdav_current_user_id."' and etat=2)
							or (public = 1 and confidential = 0)";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$children[] = new PMB\ListeLecture("(L".$row->id_liste.")",$this->config);
						}
					}
				}
				break;
			case "liste_lecture_tag" :
				if($this->config['authentication'] == 'opac') {
					global $webdav_current_user_id;
					$query = "select distinct tag as ranking from opac_liste_lecture
							where num_empr = '".$webdav_current_user_id."'
							or id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$webdav_current_user_id."' and etat=2)
							or (public = 1 and confidential = 0)";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$ranking = ($row->ranking == '' ? $msg['webdav_collection_liste_lecture_tag_no_ranking'] : $row->ranking);
							$children[] = new PMB\ListeLectureTag($ranking. " (R)" ,$this->config);
						}
					}
				}
				break;
			case "concept" :
			    $scheme_uri = \onto_common_uri::get_uri($this->config['used_schema']);
			    $store = \skos_datastore::get_store();

			    $sparql = "select ?uri where {
                    ?uri rdf:type skos:Concept .
                    ?uri skos:inScheme <".$scheme_uri."> .
                    optional {
                        ?uri skos:broader ?broader .
                    }
                    filter(!bound(?broader))
                }";

			    if($store->query($sparql)){
			        $results = $store->get_result();
			        for($i=0 ; $i<count($results) ; $i++){
			            $children[] = new PMB\Concept("(D".\onto_common_uri::get_id($results[$i]->uri).")" ,$this->config);
			        }
			    }
			    break;
			case "oeuvre" :
			    $links_definition = \marc_list_collection::get_instance('oeuvre_link');
			    $query = "select tu_id from titres_uniformes";
			    $result = pmb_mysql_query($query);
			    if(pmb_mysql_num_rows($result)){
			        while($row = pmb_mysql_fetch_object($result)){
			            // On regarde si au à au moins un lien descendant depuis l'oeuvre courante
			            $needed = true;
			            $query = "select oeuvre_link_type,oeuvre_link_expression from  tu_oeuvres_links where oeuvre_link_from=".$row->tu_id;
			            $res = pmb_mysql_query($query);
			            if(pmb_mysql_num_rows($res)){
			                while($r = pmb_mysql_fetch_object($res)){
			                    if(in_array($r->oeuvre_link_type ,array_keys($links_definition->table['ascendant']))){
			                        $needed=false;
			                    }
			                    if(in_array($r->oeuvre_link_type ,array_keys($links_definition->table['descendant']))){
			                        $needed=true;
			                        break;
			                    }
			                }
			            }
			            if($needed){
                            $children[] = new PMB\Oeuvre("(O".$row->tu_id.")",$this->config);
			            }
			        }
			    }
			    break;
			default :
				break;
		}
		usort($children,"sortChildren");
		if((count($tmp)>0) && ($tmp[0] != "'ensemble_vide'")){
			$children = array_merge(array(new PMB\Notices($tmp,$this->config)),$children);
		}
		return $children;
	}

	public function getChild($name){
		switch($name){
			case "[Notices]" :
				$child = new PMB\Notices($this->getNotices(),$this->config);
				break;
			default :
				$code = $this->get_code_from_name($name);
				if($code === "T" || $code === "R" || substr($code,1)*1 > 0){
					switch(substr($code,0,1)){
						//notice
						case "N" :
							//on vérifie juste pour pas se faire avoir...
							$child = new PMB\Notice("(".$code.")",$this->config);
							break;
							//typdoc
						case "T" :
							$child = new PMB\Typdoc($name,$this->config);
							break;
							//categorie
						case "C" :
							$child = new PMB\Categorie("(".$code.")",$this->config);
							break;
							//statut de notice
						case "S" :
							$child = new PMB\Statut("(".$code.")",$this->config);
							break;
							//indexint
						case "I" :
							$child = new PMB\Indexint("(".$code.")",$this->config);
							break;
							//explnum
						case "E" :
							$child = new PMB\Explnum("(".$code.")");
							break;
							//liste de lecture
						case "L" :
							$child = new PMB\ListeLecture("(".$code.")",$this->config);
							break;
							//tag de liste de lecture (classement / ranking)
						case "R" :
							$child = new PMB\ListeLectureTag($name,$this->config);
							break;
							// Oeuvre
						case "O" :
						    $child = new PMB\Oeuvre($name,$this->config);
						    break;
						    // Concept
						case "D" :
						    $child = new PMB\Concept($name,$this->config);
						    break;
						default :
							throw new DAV\Exception\BadRequest('Bad Request: ' . $name);
							break;
					}
				}else{
					//document numérique d'une notice
					$query = "select distinct explnum_id,notice_id from explnum join notices on explnum_bulletin = 0 and explnum_notice = notice_id where explnum_nomfichier = '".addslashes($name)."' and explnum_mimetype != 'URL'";
					//document numériques d'une notice de bulletin
					$query.= "union select distinct explnum_id,notice_id from explnum join bulletins on explnum_notice = 0 and explnum_bulletin = bulletin_id join notices on num_notice != 0 and num_notice = notice_id where explnum_nomfichier = '".addslashes($name)."' and explnum_mimetype != 'URL'";
					//$query = $this->filterExplnums($query);
					$result  = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$child = new PMB\Explnum("(E".$row->explnum_id.")");
					}else{
					    throw new DAV\Exception\NotFound('File not found: ' . $name);
					}
					break;
				}
		}
		return $child;
	}


	public function childExists($name){
		//pour les besoin des tests, on veut passer par la méthode de création...
		return false;
		switch($name){
			case "[Notices]" :
				if(count($this->getNotices())>0){
					return true;
				}else return false;
				break;
			default :
				$code = $this->get_code_from_name($name);
				if($code === "T" || $code === "R" || substr($code,1)*1 > 0){
					switch(substr($code,0,1)){
						//notice
						case "N" :
						case "T" :
						case "C" :
						case "S" :
						case "I" :
						case "E" :
						case "L" :
						case "R" :
						case "O" :
						case "D" :
							return true;
							break;
						default :
							return false;
							break;
					}
				}else{
					$query = "select distinct explnum_id from explnum where explnum_nomfichier = '".addslashes($name)."'";
					$result  = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						return true;
					}else{
						return false;
					}
					break;
				}
		}
	}

	public function getName() {
		//must be defined
		return '';
	}

	public function createFile($name, $data = null) {
		if($this->check_write_permission()){
			global $base_path;
			global $id_rep;
			global $gestion_acces_active,$gestion_acces_empr_docnum;
			global $charset;

			$name = str_replace('\"', '', str_replace('\'', '', $name));
			if($charset !=='utf-8'){
				$name = encoding_normalize::utf8_decode($name);
			}
			$filename = realpath($base_path."/temp/")."/webdav_".md5($name.time()).".".extension_fichier($name);
			$fp = fopen($filename, "w");
			if(!$fp){
				//on a pas le droit d'écriture
				throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $filename . ')');
			}

			while ($buf = fread($data, 1024)){
				fwrite($fp, $buf);
			}
			fclose($fp);
			if(!file_exists($filename)){
				//Erreur de copie du fichier
				unlink($filename);
				throw new DAV\Exception\NotFound('Empty file (filename ' . $filename . ')');
			}
			if(!filesize($filename)){
				//Premier PUT d'un client Windows...
				unlink($filename);
				return;
			}

			$notice_id = $this->get_notice_by_meta($name,$filename);
			$bulletin_id = 0;
			$this->update_notice($notice_id);

			$query = "SELECT CONCAT(niveau_biblio, niveau_hierar) AS niveau FROM notices WHERE notice_id = ".$notice_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				if ($row->niveau == "b2") {
					$query = "SELECT bulletin_id FROM bulletins WHERE num_notice = ".$notice_id;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$notice_id = 0;
						$bulletin_id = $row->bulletin_id;
					}
				}
			}
			$explnum = new \explnum(0, $notice_id, $bulletin_id);
			$id_rep = $this->config['upload_rep'];
			$explnum->get_file_from_temp($filename,$name,$this->config['up_place']);
			$explnum->params['explnum_statut'] = $this->config['default_docnum_statut'];

			//Enregistrement en base - Le contenu existe déjà sous cette notice
			if(!empty($explnum->infos_docnum["contenu"])) {
			    $query = "SELECT explnum_notice,explnum_id from explnum
                        WHERE explnum_notice = ".$notice_id."
                        AND explnum_bulletin = ".$bulletin_id."
                        AND explnum_nom = '".addslashes($explnum->infos_docnum["nom"])."'
                        AND explnum_data = '".addslashes($explnum->infos_docnum["contenu"])."'";
			    $result = pmb_mysql_query($query);
			    if(pmb_mysql_num_rows($result) > 1) {
			        while ($row = pmb_mysql_fetch_object($result)) {
			            $old_docnum = new \explnum($row->explnum_id);
			            $old_docnum->delete();
			        }
			    } elseif(pmb_mysql_num_rows($result) == 1) {
			        $row = pmb_mysql_fetch_object($result);
			        $explnum->explnum_id = $row->explnum_id;
			    }
			}
			$explnum->update();
			if(file_exists($filename)){
				unlink($filename);
			}

			// Calcul des droits sur le document numerique
			// Car on n'a pas les variables necessaires postees a la creation
			$ac = new \acces();
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$dom_1 = $ac->setDomain(3);
				$dom_1->applyRessourceRights($explnum->explnum_id);
			}

		}else{
			//on a pas le droit d'écriture
			throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $name . ')');
		}
	}


	public function update_notice($notice_id){
		global $pmb_type_audit;
		global $webdav_current_user_name,$webdav_current_user_id;
		global $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;

		$obj = $this;
		$type = $obj->type;
		$obj->update_notice_infos($notice_id);
		while ($obj = $obj->parentNode){
			if($obj->type != $type){
				$type = $obj->type;
				$obj->update_notice_infos($notice_id);
			}
		}
		if ($pmb_type_audit) {
			$query = "INSERT INTO audit SET ";
			$query .= "type_obj='1', ";
			$query .= "object_id='$notice_id', ";
			$query .= "user_id='$webdav_current_user_id', ";
			$query .= "user_name='$webdav_current_user_name', ";
			$query .= "type_modif=2 ";
			$result = @pmb_mysql_query($query);
		}

		\notice::majNoticesGlobalIndex($notice_id);
		\notice::majNoticesMotsGlobalIndex($notice_id);

		//TODO - Calcul des droits sur la notice dans les 2 domaines...
		//pour la gestion
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
			$dom_1= self::get_acces_domain(1);
			$dom_1->applyRessourceRights($notice_id);
		}
		//pour l'opac
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$dom_2= self::get_acces_domain(2);
			$dom_2->applyRessourceRights($notice_id);
		}
	}

	public function update_notice_infos($notice_id){
		//must be defined
	}

	public function filterNotices($query){
		//on remonte d'abord les parents...
		$current = $this;
		$parents = array();
		while($current->parentNode != null && $current->parentNode->type != "rootNode"){
			$parents[] = $current->parentNode;
			$current=$current->parentNode;
		}
		$parents = array_reverse($parents);
		foreach($parents as $parent){
			$parent->getNotices();
		}

		global $gestion_acces_active,$gestion_acces_user_notice,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
		global $webdav_current_user_id;
		switch($this->config['authentication']){
			case "gestion" :
				$acces_j='';
				//soit les droits d'accès sont activés et il est possible que la notice ne soit pas visible pour certaines personnes
				//soit c'est la requete de base
				if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
					$dom_1= self::get_acces_domain(1);
					$acces_j = $dom_1->getJoin($webdav_current_user_id,3,'notice_id');
					$query = "select notice_id from (".$query.") as uni ".$acces_j;
					if($this->parentNode && $this->parentNode->restricted_objects){
						$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
					}
				}elseif($this->parentNode && $this->parentNode->restricted_objects){//Si la gestion des droits n'est pas activé il faut quand même restreindre la recherche
					$query = "select notice_id from (".$query.") as uni ";
					$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
				}
				break;
			case "opac" :
				$acces_j='';
				//droit d'accès ou statut
				if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
					$dom_1= self::get_acces_domain(2);
					$acces_j = $dom_1->getJoin($webdav_current_user_id,16,'notice_id');
					$query = "select notice_id from (".$query.") as uni ".$acces_j;
					if($this->parentNode && $this->parentNode->restricted_objects){
						$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
					}
				}else{
					$query = "select uni.notice_id from (".$query.") as uni join notices on notices.notice_id = uni.notice_id join notice_statut on notices.statut= id_notice_statut where ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($webdav_current_user_id ?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
					if($this->parentNode && $this->parentNode->restricted_objects){
						$query.= " and uni.notice_id in (".$this->parentNode->restricted_objects.")";
					}
				}
				break;
			case "anonymous" :
				//on doit regarder
				//droit d'accès ou statut
				if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
					$dom_1= self::get_acces_domain(2);
					$acces_j = $dom_1->getJoin(0,16,'notice_id');
					$query = "select notice_id from (".$query.") as uni ".$acces_j;
					if($this->parentNode && $this->parentNode->restricted_objects){
						$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
					}
				}else{
					$query = "select uni.notice_id from (".$query.") as uni join notices on notices.notice_id = uni.notice_id join notice_statut on notices.statut= id_notice_statut where explnum_visible_opac=1 and explnum_visible_opac_abon=0";
					if($this->parentNode && $this->parentNode->restricted_objects){
						$query.= " and uni.notice_id in (".$this->parentNode->restricted_objects.")";
					}
				}
				break;
			default ://On ne doit jamais passer dans ce cas là
				$query="";
				break;
		}
		$this->notices =array();

		//vérification des droits sur les documents numériques
		switch($this->config['authentication']){
			case "opac" :
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$dom_3= self::get_acces_domain(3);
					$acces_j = $dom_3->getJoin($webdav_current_user_id,16,'explnum_id');
					$explnum_notice_query = "select explnum_notice as notice_id from explnum $acces_j where explnum_notice in ($query)";
					$explnum_bull_query = "select num_notice as notice_id from bulletins join explnum on explnum_notice=0 and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j";
					$query = "select distinct uni.notice_id from (($explnum_notice_query) union ($explnum_bull_query)) as uni ";
				}else{
					// vérification du statut de chaque document
					$explnum_notice_query = "select explnum_notice as notice_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac=1 and explnum_notice in ($query)";
					$explnum_bull_query = "select num_notice as notice_id from bulletins join explnum on explnum_notice=0 and explnum_bulletin = bulletin_id join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac=1 and num_notice in ($query)";
				}
				$query = "select distinct uni.notice_id from (($explnum_notice_query) union ($explnum_bull_query)) as uni ";
				break;
			case "anonymous" :
				//on doit requeter les droits d'accès propre à chaque document
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$dom_3= self::get_acces_domain(3);
					$acces_j = $dom_3->getJoin(0,16,'explnum_id');
					$explnum_notice_query = "select explnum_notice as notice_id from explnum $acces_j where explnum_notice in ($query)";
					$explnum_bull_query = "select num_notice as notice_id from bulletins join explnum on explnum_notice=0 and explnum_bulletin != 0 and explnum_bulletin = bulletin_id $acces_j";
				}else{
					// vérification du statut de chaque document
					$explnum_notice_query = "select explnum_notice as notice_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac=1 and explnum_visible_opac_abon=0 and explnum_notice in ($query)";
					$explnum_bull_query = "select num_notice as notice_id from bulletins join explnum on explnum_notice=0 and explnum_bulletin = bulletin_id join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac=1 and explnum_visible_opac_abon=0 and num_notice in ($query)";
				}
				$query = "select distinct uni.notice_id from (($explnum_notice_query) union ($explnum_bull_query)) as uni ";
				break;
			case "gestion" :
			default :
				//en gestion ca ne change rien...
				break;
		}
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->notices[] = $row->notice_id;
			}
		}else{//Si j'ai plus de notice dans cette branche il faut le garde en mémoire sinon dans la branche du dessous on repart avec toute les notices
			$this->notices[] = "'ensemble_vide'";
		}
		$this->restricted_objects = implode(",",$this->notices);
	}

	public function filterExplnums($query){
		global $gestion_acces_active,$gestion_acces_empr_docnum;
		global $webdav_current_user_id;

		switch($this->config['authentication']){
			case "gestion" :
				//pas de controle particulier de ce coté là...
				break;
			case "opac" :
				$acces_j='';
				//droit d'accès ou statut
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$dom_3= self::get_acces_domain(3);
					$acces_j = $dom_3->getJoin($webdav_current_user_id,16,'explnum_id');
					$query = "select distinct explnum_id from (".$query.") as uni ".$acces_j;
				}else{
					$query = "select distinct uni.explnum_id from (".$query.") as uni join explnum on uni.explnum_id = explnum.explnum_id join explnum_statut on explnum.explnum_docnum_statut = id_explnum_statut where explnum_visible_opac=1";
				}
				break;
			case "anonymous" :
				$acces_j='';
				//droit d'accès ou statut
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$dom_3= self::get_acces_domain(3);
					$acces_j = $dom_3->getJoin(0,16,'explnum_id');
					$query = "select distinct explnum_id from (".$query.") as uni ".$acces_j;
				}else{
					$query = "select distinct uni.explnum_id from (".$query.") as uni join explnum on uni.explnum_id = explnum.explnum_id join explnum_statut on explnum.explnum_docnum_statut = id_explnum_statut where explnum_visible_opac=1 and explnum_visible_opac_abon=0";
				}
				break;
		}
		return $query;
	}

	public function getNotices(){
		return array();
	}

	public function check_write_permission(){
		global $webdav_current_user_id;
		if($this->config['write_permission']){
			$tab = array();
			$query = "";
			switch($this->config['authentication']){
				case "gestion" :
					$tab = $this->config['restricted_user_write_permission'];
					$query = "select grp_num from users where userid = ".$webdav_current_user_id;
					break;
				case "opac" :
					$query = "select empr_categ from empr where id_empr = ".$webdav_current_user_id;
				case "anonymous" :
				default :
					$tab = $this->config['restricted_empr_write_permission'];
					break;
			}
			//pas de restriction, on est bon
			if (is_countable($tab) && !count($tab)) {
				return true;
			} elseif ($query != "") {
				//on doit s'assurer que la personne connectée est dispose des droits...
				$result = pmb_mysql_query($query);
				if (
					pmb_mysql_num_rows($result) &&
					in_array(pmb_mysql_result($result,0,0),$tab)
				){
					return true;
				}
			}
		}
		//si on est encore dans la fonction, c'est qu'on correspond à aucun critère !
		return false;
	}

	public function format_name($name) {
		if (!isset($this->formatted_name)) {
			$matches = array();
			$name = (str_replace('/','-',$name));
			if (preg_match('/(.*)(\s\(.*\)$)/', $name, $matches)) {
				$name = preg_replace('/[.;\:\/\+]/i', ' ', convert_diacrit(pmb_strtolower($matches[1])));
				$name = preg_replace('/[^a-z0-9\s-]/i', '', $name).$matches[2];
			}
			$this->formatted_name = \encoding_normalize::utf8_normalize(str_replace('/', '-', $name));
		}
		return $this->formatted_name;
	}

	public function get_parent_by_type($type) {
		$parent = $this->parentNode;
		while ($parent->type != $type) {
			$parent = $parent->parentNode;
		}
		return $parent;
	}

	protected static function get_acces_class(){
		if(!is_object(self::$acces)){
			self::$acces = new \acces();
		}
		return self::$acces;
	}

	protected static function get_acces_domain($id){
		if(!is_object(self::$domain[$id])){
			self::get_acces_class();
			self::$domain[$id] = self::$acces->setDomain($id);
		}
		return self::$domain[$id];
	}
}
