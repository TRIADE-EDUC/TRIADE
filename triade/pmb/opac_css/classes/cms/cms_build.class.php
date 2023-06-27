<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_build.class.php,v 1.87 2019-06-11 06:53:05 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if (PHP_MAJOR_VERSION == "5") @ini_set("zend.ze1_compatibility_mode", "0");

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("cms_modules",true);		
require_once($class_path."/cms/cms_modules_parser.class.php");

class cms_build{	
	public $dom;
	public $headers = array(
		'add' => array(),
		'replace' => array()
	);
	public $id_version; // version du portail
	public $fixed_cadres = array();
	public static $hash_cache_cadres = array();
	
	//Constructeur	 
	public function __construct(){

	}	
	
	public function transform_html($html){
		global $lvl,$pageid,$search_type_asked;
		global $charset, $is_opac_included;
		global $opac_compress_css;

		if($charset=='utf-8') $html = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
		'|[\x00-\x7F][\x80-\xBF]+'.
		'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
		'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
		'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
		'?', $html );

		$pageid+=0;
		@ini_set("zend.ze1_compatibility_mode", "0");
		$this->cadre_portail_list=array();
		$this->dom = new DomDocument();
		$this->dom->encoding = $charset;
		
		// si pas d'entete on ajoute le charset 
		if($is_opac_included) {
			$html = '<meta http-equiv="content-type" content="text/html; charset='.$charset.'" />'.$html;
		}
		if(!@$this->dom->loadHTML($html)) return $html;

		//bon, l'histoire se répète, c'est quand on pense que c'est simple que c'est vraiment complexe...
		// on commence par récupérer les zones...
		$this->id_version=$this->get_version_public();
		if(!$this->id_version) return $html;
		//On vide ce qui est trop vieux dans la table de cache des cadres
		$this->manage_cache_cadres("clean");
		$cache_cadre_object=array();//Tableau qui sert à stocker les objets générés pour les cadres.
		$query_zones = "select distinct build_parent from cms_build where build_type='cadre' and build_version_num= '".$this->id_version."'";
		$result_zones = pmb_mysql_query($query_zones);
		if(pmb_mysql_num_rows($result_zones)){
			while($row_zones = pmb_mysql_fetch_object($result_zones)){
				
				//pour chaque zone, on récupère les cadres fixes...
				$query_cadres = "select cms_build.*,cadre_url from cms_build 
				LEFT JOIN cms_cadres ON build_obj=CONCAT(cadre_object,'_',id_cadre) AND cadre_memo_url=1
				where build_parent = '".$row_zones->build_parent."'
				and build_fixed = 1 and build_type='cadre' and build_version_num= '".$this->id_version."' ";
				$result_cadres = pmb_mysql_query($query_cadres);
				if(pmb_mysql_num_rows($result_cadres)){
					$cadres = array();
					//on place les cadres dans un tableau
					while($row_cadres = pmb_mysql_fetch_object($result_cadres)){
						//Si on a récupéré un cadre_url
						$cadreOk=true;
						if($row_cadres->cadre_url){
							$url=substr($row_cadres->cadre_url, strpos($row_cadres->cadre_url, '?')+1);
							foreach (explode('&',$url) as $idParam=>$param){
								$tmp=array();
								$tmp=explode('=', $param);
								if(sizeof($tmp)==2){
									if(${$tmp[0]}!=$tmp[1] && ($tmp[0]=="lvl" || $tmp[0]=="search_type_asked" || $tmp[0]=="pageid")){
										//si le cadre rentre dans le cas ou il n'appartient pas à la page courante.
										$cadreOk=false;
									}
								}
							}
						}
						if($cadreOk){
							$cadres[]=$row_cadres;
						}
					}
					$ordered_cadres = $this->order_cadres($cadres,$cache_cadre_object);
					$this->fixed_cadres[$row_zones->build_parent] = $ordered_cadres;
					foreach($ordered_cadres as $cadre){
						$this->apply_change($cadre,$cache_cadre_object);
						if(!empty($cadre->build_div)){
							$this->add_div($cadre->build_obj);
						}
					}
//					print "pour chaque zone cadres fixes: ";
//					printr($cadres);
				}	
				//on passe au cadre dynamiques
				$query_dynamics = "select cms_build.*,cadre_url from cms_build 
				LEFT JOIN cms_cadres ON build_obj=CONCAT(cadre_object,'_',id_cadre) AND cadre_memo_url=1
				where build_parent = '".$row_zones->build_parent."' 
				and build_fixed = 0 and build_type='cadre' and  build_version_num= '".$this->id_version."' 
				order by id_build ";
				$result_dynamics = pmb_mysql_query($query_dynamics);
				if(pmb_mysql_num_rows($result_dynamics)){
					$cadres = array();
					while($row_dynamics = pmb_mysql_fetch_object($result_dynamics)){
						//Si on a récupéré un cadre_url
						$cadreOk=true;
						if($row_dynamics->cadre_url){
							$url=substr($row_dynamics->cadre_url, strpos($row_dynamics->cadre_url, '?')+1);
							foreach (explode('&',$url) as $idParam=>$param){
								$tmp=array();
								$tmp=explode('=', $param);
								if(sizeof($tmp)==2){
									global ${$tmp[0]};
									if(${$tmp[0]} != $tmp[1] && ($tmp[0]=="lvl" || $tmp[0]=="search_type_asked" || $tmp[0]=="pageid")){
										//si le cadre rentre dans le cas ou il n'appartient pas à la page courante.
										$cadreOk=false;
									}
								}
							}
						}
						if($cadreOk){
							$cadres[]=$row_dynamics;
						}
					}
					$ordered_cadres = $this->order_cadres($cadres,$cache_cadre_object);
					foreach($ordered_cadres as $cadre){
						$this->apply_change($cadre,$cache_cadre_object);
						if(is_object($cadre) && $cadre->build_div){
							$this->add_div($cadre->build_obj);
						}
					}
				}
			}
		}
		//on traite la css des Zones. A voir plus tard pour la gestion du placement
		$query_css_zones = "select * from cms_build where build_type='zone' and  build_version_num= '".$this->id_version."' ";
		$res = pmb_mysql_query($query_css_zones);
		if(pmb_mysql_num_rows($res)){
			while($r = pmb_mysql_fetch_object($res)){
				$node = $this->dom->getElementById($r->build_obj);
				if($node){
					if( $r->build_css){
						$this->add_css($node,$r->build_css);
					}	
					if($r->build_div){
						$this->add_div($r->build_obj);
					}
				}	
			}
		}
		//gestion du placement des zones du contener
		$query_zones = "select * from cms_build where build_type='zone' and  build_version_num= '".$this->id_version."' and build_parent='container' ";
		$res = pmb_mysql_query($query_zones);
		$contener = $this->dom->getElementById("container");
		$zones=array();
		if(pmb_mysql_num_rows($res)){
			while($r = pmb_mysql_fetch_object($res)){
				$zones[]=$r;
			}
			$ordered_zones = $this->order_cadres($zones,$cache_cadre_object);
			foreach($ordered_zones as $zone){
				$this->apply_change($zone,$cache_cadre_object);
				if($cadre->build_div){
				//	$this->add_div($cadre->build_obj);
				}
			}
		}				
		//on insère les entêtes des modules dans le head
		$this->insert_headers();
		
		//compression de la CSS si activé!
		if($opac_compress_css == 1){
			$compressed_file_exist = file_exists("./temp/full.css");
			$links = $this->dom->getElementsByTagName("link");
			$dom_css = array();
			for($i=0 ; $i<$links->length ; $i++){
				$dom_css[] = $links->item($i);
				if(!$compressed_file_exist && $links->item($i)->hasAttribute("type") && $links->item($i)->getAttribute("type") == "text/css"){
					$css_buffer.= loadandcompresscss(html_entity_decode($links->item($i)->getAttribute("href")));
				}
			}
			$styles = $this->dom->getElementsByTagName("style");
			for($i=0 ; $i<$styles->length ; $i++){
				$dom_css[] = $styles->item($i);
				if(!$compressed_file_exist){
					$css_buffer.= compresscss($styles->item($i)->nodeValue,"");
				}
			}
			foreach($dom_css as $link){
				$link->parentNode->removeChild($link);
			}
			if(!$compressed_file_exist){
				file_put_contents("./temp/full.css",$css_buffer);
			}
			$link = $this->dom->createElement("link");
			$link->setAttribute("href", "./temp/full.css");
			$link->setAttribute("rel", "stylesheet");
			$link->setAttribute("type", "text/css");
			$this->dom->getElementsByTagName("head")->item(0)->appendChild($link);
		}else if (file_exists("./temp/full.css")){
			unlink("./temp/full.css");
		}
		$html = $this->dom->saveHTML();
		@ini_set("zend.ze1_compatibility_mode", "1");

		return $html;
	}
	
	public function add_div($id){	
		
		$node = $this->dom->getElementById($id);
		if(!$node) return;
		
		$obj_div= $this->dom->createElement('div');
		$obj_div->setAttribute('id',"add_div_".$id);
		$obj_div->setAttribute('class',"row");
		$node->parentNode->insertBefore($obj_div,$node);
	}
		
	public function clear_session_version(){
		$_SESSION["build_id_version"]="";
	}
	
	public function get_version_public(){
		global $dbh,$opac_cms;
		global $build_id_version; // passer en get si constrution de l'opac en cours
		if($build_id_version){
			$_SESSION["build_id_version"]=$build_id_version;
		} else{
			$build_id_version=(isset($_SESSION["build_id_version"]) ? $_SESSION["build_id_version"] : '');
		}
		if($build_id_version ) {			
			// mode opac en constuction
			$requete = "select * from cms_version where 
			id_version='".($build_id_version*1)."'
			order by version_date desc 
			";	
		} elseif($opac_cms){
			// mode opac, on prend la dernière version
			$requete = "select * from cms_version where 
			version_cms_num= '".($opac_cms*1)."'
			order by version_date desc 
			";		
		}else{
			return"";
		}	
		$res = pmb_mysql_query($requete, $dbh);				
		if($row = pmb_mysql_fetch_object($res)){	
			return $row->id_version;
		} else {
			$_SESSION["build_id_version"]="";	
		}	
	}

	public function apply_change($cadre,&$cache_cadre_object){
		global $charset,$opac_parse_html;
		
		if(!is_object($cadre)) return false;
		if(substr($cadre->build_obj,0,strlen("cms_module_"))=="cms_module_"){
			if($cadre->empty && $_SESSION["cms_build_activate"]){
				$id_cadre= substr($cadre->build_obj,strrpos($cadre->build_obj,"_")+1);
				$obj=cms_modules_parser::get_module_class_by_id($id_cadre);
				if($obj){
					$query = "select cadre_name from cms_cadres where id_cadre = '".($id_cadre*1)."'";
					$result = pmb_mysql_query($query);
					$row = pmb_mysql_fetch_object($result);
					
					$html ="<span id='".$cadre->build_obj."' class='cmsNoStyles' type='cms_module_hidden' cadre_style='".$cadre->build_css."'><div id='".$cadre->build_obj."_conteneur' class='cms_module_hidden' style='display:none'>".$obj->get_human_description()."<div style='".$cadre->build_css."'></div></div></span>";
					$tmp_dom = new domDocument();
					if($charset == "utf-8"){
						@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
					}else{
						@$tmp_dom->loadHTML($html);
					}
					if (!$tmp_dom->getElementById($obj->get_dom_id())) $this->setAllId($tmp_dom);
					if($this->dom->getElementById($cadre->build_parent) ){
						$this->dom->getElementById($cadre->build_parent)->appendChild($this->dom->importNode($tmp_dom->getElementById($obj->get_dom_id()),true));
					}	
					$dom_id =$obj->get_dom_id();
					//on rappelle le tout histoire de récupérer les CSS and co...
					$this->apply_dom_change($obj->get_dom_id(),$cadre);	
				}
			}else if(!$cadre->empty){
				$id_cadre= substr($cadre->build_obj,strrpos($cadre->build_obj,"_")+1);
				if(isset($cache_cadre_object[$cadre->build_obj]) && $cache_cadre_object[$cadre->build_obj]){
					$obj=$cache_cadre_object[$cadre->build_obj];
				}else{
					$obj=cms_modules_parser::get_module_class_by_id($id_cadre);
					$cache_cadre_object[$cadre->build_obj]=$obj;
				}
				if($obj){
					//on va chercher ses entetes...
					$headers = $obj->get_headers();
					$this->headers['add'] = array_merge($this->headers['add'],$headers['add']);
					$this->headers['replace'] = array_merge($this->headers['replace'],$headers['replace']);
					$this->headers['add'] = array_unique($this->headers['add']);
					$this->headers['replace'] = array_unique($this->headers['replace']);
					
					//on s'occupe du cadre en lui-même
					//on récupère le contenu du cadre
					$res = $this->manage_cache_cadres("select",$cadre->build_obj,"html");
					if($res["select"]){
						$html = $res["value"];
					}else{
						$html = $obj->show_cadre();
						if($opac_parse_html){
							$html = parseHTML($html);
						}
						//on regarde si une condition n'empeche pas la mise en cache !
						if($obj->check_for_cache()){
							$this->manage_cache_cadres("insert",$cadre->build_obj,"html",$html);
						}
					}
					//ca a peut-être l'air complexe, mais c'est logique...
					$tmp_dom = new domDocument();
					if($charset == "utf-8"){
						@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
					}else{
						@$tmp_dom->loadHTML($html);
					}
					if (!$tmp_dom->getElementById($obj->get_dom_id())) $this->setAllId($tmp_dom);
					if($this->dom->getElementById($cadre->build_parent) ){
						$this->dom->getElementById($cadre->build_parent)->appendChild($this->dom->importNode($tmp_dom->getElementById($obj->get_dom_id()),true));
					}	
					$dom_id =$obj->get_dom_id();
					//on rappelle le tout histoire de récupérer les CSS and co...
					$this->apply_dom_change($obj->get_dom_id(),$cadre);	
				}					
			}
		}else{
			if($cadre->build_type == "cadre" && $cadre->empty == 1 && $_SESSION["cms_build_activate"]){
				
				$html ="<span id='".$cadre->build_obj."' class='cmsNoStyles' type='cms_module_hidden' cadre_style='".$cadre->build_css."'><div id='".$cadre->build_obj."_conteneur' class='cms_module_hidden' style='display:none'>".$cadre->build_obj."<div style='".$cadre->build_css."'></div></div></span>";
				$tmp_dom = new domDocument();
				if($charset == "utf-8"){
					@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
				}else{
					@$tmp_dom->loadHTML($html);
				}
				if (!$tmp_dom->getElementById($cadre->build_obj)) $this->setAllId($tmp_dom);
				if($this->dom->getElementById($cadre->build_parent) ){
					$this->dom->getElementById($cadre->build_parent)->appendChild($this->dom->importNode($tmp_dom->getElementById($cadre->build_obj),true));
				}
			}
			$this->apply_dom_change($cadre->build_obj,$cadre);
		}
	}
	
	
	public function order_cadres($cadres,&$cache_cadre_object){
		//on retente de mettre de l'ordre dans tout ca...
		//init
		$ordered_cadres = array();
		$cadres_dom = array();
		$zone = "";
		//on élimine ce qui n'est pas dans le dom (ou ne va pas l'être)
		for($i=0 ; $i<count($cadres) ; $i++){
			$cadres[$i]->empty=0;
			if(!$zone) $zone = $cadres[$i]->build_parent;
			if(substr($cadres[$i]->build_obj,0,strlen("cms_module_"))=="cms_module_"){
				$id_cadre= substr($cadres[$i]->build_obj,strrpos($cadres[$i]->build_obj,"_")+1);
				$res = $this->manage_cache_cadres("select",$cadres[$i]->build_obj,"object");
				if($res["select"] == true){
					if($res["value"]){
						$cadres_dom[] = $res["value"];						
					}
				}else{
					if(isset($cache_cadre_object[$cadres[$i]->build_obj])){
						$obj=$cache_cadre_object[$cadres[$i]->build_obj];
					}else{
						$obj=cms_modules_parser::get_module_class_by_id($id_cadre);
						$cache_cadre_object[$cadres[$i]->build_obj]=$obj;
					}
					if($obj && $obj->check_conditions()){
						$cadres_dom[] = $cadres[$i];
						if($obj->check_for_cache()){
							$this->manage_cache_cadres("insert",$cadres[$i]->build_obj,"object",$cadres[$i]);
						}
					}elseif($obj && $obj->check_for_cache()){
						$cadres[$i]->empty=1;
						$cadres_dom[] = $cadres[$i];
						$this->cadre_no_in_page[]=$cadres[$i];
						$this->manage_cache_cadres("insert",$cadres[$i]->build_obj,"object","");
					}else{
						$cadres[$i]->empty=1;
						$cadres_dom[] = $cadres[$i];
						$this->cadre_no_in_page[]=$cadres[$i];	
					}
				}
			}else if($this->dom->getElementById($cadres[$i]->build_obj)){
				$cadres_dom[] = $cadres[$i];
			}else{
				$cadres[$i]->empty=1;
				$cadres_dom[] = $cadres[$i];
				$this->cadre_no_in_page[]=$cadres[$i];
			}
		}		
		$cadres = $cadres_dom;
		//après ce petit tour de passe passe, il nous reste ques les éléments présent sur la page...
		$ordered_cadres[] =$this->get_next_cadre($cadres,$zone);
		$i=0;
		$nb =count($cadres);
		while(count($cadres)){
			$ordered_cadres[] =$this->get_next_cadre($cadres,$zone,(is_object($ordered_cadres[count($ordered_cadres)-1]) ? $ordered_cadres[count($ordered_cadres)-1]->build_obj : ''));
			if($i==$nb) break;
			$i++;
		}
		
		//le reste, c'est que l'on à jamais pu placer (perte de chainage via supression de cadres)...
		foreach($cadres as $cadre){
			$ordered_cadres[] = $cadre;
		}
		return $ordered_cadres;		
	}
	
	/*
	 * Permets la gestion du cache pour les cadres du portail dans l'opac
	 */
	protected function manage_cache_cadres($todo,$build_object_name="",$content_type="",$content=""){
		global $cms_cache_ttl;//Variable en seconde
		
		$return = array($todo=>false,"value"=>"");
		
		if($_SESSION["cms_build_activate"]){
			return $return;
		}		
		if($todo == "clean"){
			$requete="DELETE FROM cms_cache_cadres WHERE DATE_SUB(NOW(), INTERVAL ".($cms_cache_ttl*1)." SECOND) > cache_cadre_create_date";
			$res = pmb_mysql_query($requete);
			if(pmb_mysql_affected_rows()) {
				cms_build::$hash_cache_cadres = array();
			}
			return array($todo=>true,"value"=>"");
		}
		
		$elems = explode("_",$build_object_name);
		$id = array_pop($elems);
		$id+=0;
		$cadre_name = implode("_",$elems);
		$my_hash_cadre = call_user_func(array($cadre_name,"get_hash_cache"), $build_object_name,$id);
		//il est possible que la méthode ne nous retourne pas de cache, cela signifie que l'on ne doit pas cacher les éléments associés
		if(!$my_hash_cadre){
			return array($todo=>false,"value"=>"");
		}
		
		switch ($todo) {
			case "select":
				$requete="SELECT cache_cadre_hash,cache_cadre_content  FROM cms_cache_cadres WHERE cache_cadre_hash='".addslashes($my_hash_cadre)."' AND cache_cadre_type_content='".addslashes($content_type)."'";
				$res=pmb_mysql_query($requete);
				if($res && pmb_mysql_num_rows($res)){
					cms_build::$hash_cache_cadres[] = $my_hash_cadre.$content_type;					
					$html = pmb_mysql_result($res,0,1);
					if($html){
						if($content_type == "object"){
							$value = unserialize($html);
						}else{
							$value = $html;
						}
					}else{
						$value = "";
					}
					return array($todo=>true,"value"=>$value);
				}
				break;
			case "insert":
				if(in_array($my_hash_cadre.$content_type, cms_build::$hash_cache_cadres)) return array($todo=>true,"value"=>"");
				$cache_cadre_content="";
				if($content_type == "object"){
					if($content){
						$cache_cadre_content=serialize($content);
					}
				}else{
					$cache_cadre_content=$content;
				}
				cms_build::$hash_cache_cadres[] = $my_hash_cadre.$content_type;
				$requete="INSERT INTO cms_cache_cadres(cache_cadre_hash,cache_cadre_type_content,cache_cadre_content) VALUES('".addslashes($my_hash_cadre)."','".addslashes($content_type)."','".addslashes($cache_cadre_content)."')";
				$res2=pmb_mysql_query($requete);
				if($res2){
					return array($todo=>true,"value"=>""); 
				}
				break;
		}
		return $return;
	}
	
	public function get_next_cadre(&$cadres,$zone,$before=""){
		$next = false;
		//on commence par aller par rapport au dynamiques
		
		foreach($cadres as $key => $cadre){
			if($cadre->build_child_before == $before){
				$next = $cadre;
				unset($cadres[$key]);
				return $next;
			}
		}
		// on perd le fil, on reprend les valeurs sures, les éléments fixe
		if (!empty($this->fixed_cadres[$zone])) {
    		for($i=0 ; $i<count($this->fixed_cadres[$zone]) ; $i++){
    			foreach($cadres as $key => $cadre){
    			    if(!empty($cadre->build_child_before) && !empty($this->fixed_cadres[$zone][$i]->build_obj) &&$cadre->build_child_before == $this->fixed_cadres[$zone][$i]->build_obj){
    					$next = $cadre;
    					unset($cadres[$key]);
    					return $next;
    				}
    			}
    		}
		}
		return $next;
	}
	
	public function setAllId($DOMNode){
  		if($DOMNode->hasChildNodes()){
  			for ($i=0; $i<$DOMNode->childNodes->length;$i++) {
  				$this->setAllId($DOMNode->childNodes->item($i));
  			}
  		}
		if($DOMNode->hasAttributes()){
        	$id=$DOMNode->getAttribute("id");
        	if($id){
          		$DOMNode->setIdAttribute("id",true);
        	}
      	}
	}
	
	public function apply_dom_change($id,$infos){	
		//on s'assure que la zone existe !
		$parent = $this->dom->getElementById($infos->build_parent);
		if($parent){
			$node = $this->dom->getElementById($id);
			if($node){
				if(!isset($infos->empty)) $infos->empty = '';
				if(!$infos->empty){
					//on ajoute l'attribut fixed si on est sur un élément fixé!
					if($infos->build_fixed){
						$node->setAttribute("fixed","yes");
					}
					//on lui ajoute les éléments de la CSS
					$node = $this->add_css($node,$infos->build_css);
				}
				//on le place dans la bonne zone
				$this->place($node,$parent,$infos);
			}
		}
	}

	public function add_css($node,$css){
		if($css){
			$node->setAttribute("style",$css);
		}
		return $node;
	}

	public function get_first_child($zone) {
		$childs=$zone->childNodes;
		$first_child=null;
		for ($i=0; $i<$childs->length;$i++) {
			$child=$childs->item($i);
			if (($child->nodeType==XML_ELEMENT_NODE)&&($child->getAttribute("id"))) {
				$first_child=$child;
				break;	
			}
		}
		return $first_child;
	}
	
	public function get_nextSibling($zone,$field) {
		$childs=$zone->childNodes;
		$next=null;
		$found=0;
		for ($i=0; $i<$childs->length;$i++) {
			$child=$childs->item($i);
			if (($child->nodeType==XML_ELEMENT_NODE)&&($child->getAttribute("id"))){				
				if($child->getAttribute("id")==$field->getAttribute("id")) {					
					$found=1;
				}elseif($found){// coup suivant, c'est le bon
					$next=$child;
					break;						
				}
			}	
		}
		return $next;
	}
	
	public function get_previousSibling($zone,$field) {
		$childs=$zone->childNodes;
		$previous=null;
		for ($i=0; $i<$childs->length;$i++) {
			$child=$childs->item($i);
			if (($child->nodeType==XML_ELEMENT_NODE)&&($child->getAttribute("id"))){				
				if(($child->getAttribute("id")==$field->getAttribute("id")) ) {	
					return $previous;	
					break;	
				}else{					
					$previous=$child;						
				}
			}	
		}
		return null;
	}	

	public function place($node,$parent,$infos){
		$previous_brother = $this->get_previous_node_id($infos);
		if($previous_brother!== false){
			if($previous_brother!= ""){
				//un précédent connu, on insère le noeud juste avant le précédent, puis on remet le précédent au dessus...
				$node_next= $this->get_nextSibling($parent,$this->dom->getElementById($previous_brother));
				if($node_next && ($node->getAttribute("id") !=$node_next->getAttribute("id"))){
					$parent->insertBefore($node,$node_next);
				} elseif(!$node_next) {
					$parent->appendChild($node);
				} else {
				}
			}else{
				//pas de parent, c'est le premier...
				$node_child=$this->get_first_child($parent);
				if($node_child && ($node->getAttribute("id")!=$node_child->getAttribute("id"))){
					$parent->insertBefore($node,$node_child);
				} elseif(!$node_child) {
					$parent->appendChild($node);
				} else {
				}
			}
		}else{
			$next_brother = $this->get_next_node_id($infos);
			if($next_brother){				
				$node_previous=$this->get_previousSibling($parent,$this->dom->getElementById($next_brother));				
				if($node_previous && ($node->getAttribute("id")!=$node_previous->getAttribute("id"))){
					$node = $parent->insertBefore($node,$node_previous);
				}elseif(!$node_previous){
					$parent->appendChild($node);					
				}else {
				}
			}else{
				$parent->appendChild($node);
			}
		}
	}
	
	public function get_previous_node_id($infos){
		if($this->dom->getElementById($infos->build_child_before)){
			return $infos->build_child_before;
		}else{
			return $this->_get_previous_node_id($infos->build_child_before);
		}
	}

	public function _get_previous_node_id($node_id){
		if($node_id === ""){
			return $node_id;
		}else{
			//if($this->dom->getElementById($node_id)){
				$query = "select build_child_before from cms_build where build_obj = '".addslashes($node_id)."' and  build_version_num= '".$this->id_version."' ";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$previous = pmb_mysql_result($result,0,0);
					if($this->dom->getElementById($previous)){
						return $previous;
					}else{
						return $this->_get_previous_node_id($previous);
					}
				} else return false;
			//}else{
			//	return false;
			//}
		}

	}

	public function get_next_node_id($infos){
		return $this->_get_next_node_id($infos->build_obj);
	}
	
	public function _get_next_node_id($node_id){
		if($node_id === ""){
			return $node_id;
		}else{
			//if($this->dom->getElementById($node_id)){
				$query = "select build_child_after from cms_build where build_obj = '".addslashes($node_id)."' and  build_version_num= '".$this->id_version."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$next = pmb_mysql_result($result,0,0);
					if($this->dom->getElementById($next)){
						return $next;
					}else{
						return $this->_get_next_node_id($next);
					}
				} else return false;	
			//}else{
			//	return false;
			//}
		}
	}
	
	public function insert_headers(){
		global $charset;
		
		if(count($this->headers['add'])){
			$headers = implode("\n",$this->headers['add']);
			$tmp_dom = new domDocument();
			if($charset == "utf-8"){
				@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$headers);
			}else{
				@$tmp_dom->loadHTML($headers);
			}
			for ($i=0 ; $i<$tmp_dom->getElementsByTagName("head")->item(0)->childNodes->length ; $i++){
				if(is_object($this->dom->getElementsByTagName("head")->item(0))) {
					$this->dom->getElementsByTagName("head")->item(0)->appendChild($this->dom->importNode($tmp_dom->getElementsByTagName("head")->item(0)->childNodes->item($i),true));
				}
			}
		}
		if(count($this->headers['replace'])){
			$tmp_dom = new domDocument();
			foreach($this->headers['replace'] as $header){
				if($charset == "utf-8"){
					@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$header);
				}else{
					@$tmp_dom->loadHTML($header);
				}
				for ($i=0 ; $i<$tmp_dom->getElementsByTagName("head")->item(0)->childNodes->length ; $i++){
					$new_item = $tmp_dom->getElementsByTagName("head")->item(0)->childNodes->item($i);
					$to_check = $this->dom->getElementsByTagName($new_item->nodeName);
					if($to_check->length > 0){
						for($j=0 ; $j<$to_check->length ; $j++){
 							$to_replace =true;
 							if($new_item->hasAttributes()){
 								for($k=0 ; $k<$new_item->attributes->length ; $k++){
 									$attr = $new_item->attributes->item($k);
 									if($attr->name == "content" || $attr->name == "value"){
 										continue;
 									}else{
 										$to_test = $to_check->item($j);
 										if(!$to_test->hasAttribute($attr->name) || $to_test->getAttribute($attr->name) != $attr->value){
 											$to_replace = false;
 										}
 									}
 								}
								
							}
 							if ($to_replace){
	   							$to_check->item($j)->parentNode->removeChild($to_check->item($j));
	   							break;
 							}
						}
					}
					$this->dom->getElementsByTagName("head")->item(0)->appendChild($this->dom->importNode($new_item,true));
				}
			}
		}
	}
// class end
}
