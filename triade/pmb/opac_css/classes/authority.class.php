<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority.class.php,v 1.58 2024/04/12 08:35:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkAuthority;
use Pmb\Ark\Models\ArkModel;

require_once($include_path."/h2o/pmb_h2o.inc.php");
require_once($class_path."/authorities_collection.class.php");
require_once($class_path.'/skos/skos_concepts_list.class.php');
require_once($class_path.'/aut_link.class.php');
require_once($class_path.'/elements_list/elements_authorities_list_ui.class.php');
require_once($class_path.'/thumbnail.class.php');
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/custom_parametres_perso.class.php");
require_once $class_path."/contribution_area/contribution_area_form.class.php";
require_once $class_path."/entities.class.php";

class authority {

    /**
     * Identifiant
     * @var int
     */
    private $id;

	/**
	 * Type de l'autorité
	 * @var int
	 */
	private $type_object;


	/**
	 *
	 * @var aut_link
	 */
	private $autlink_class;

	/**
	 * Identifiant de l'autorité
	 * @var int
	 */
	private $num_object;

	/**
	 *
	 * @var string
	 */
	private $string_type_object;

	/**
	 * Array d'onglet d'autorité
	 * @var authority_tabs
	 */
	private $authority_tabs;

	/**
	 * Libellé du type d'autorité
	 * @var string
	 */
	private $type_label;

	/**
	 * Identifiant du statut
	 * @var int
	 */
	private $num_statut = 1;

	/**
	 * Class HTML du statut
	 * @var string
	 */
	private $statut_class_html = 'statutnot1';

	/**
	 * Label du statut
	 * @var string
	 */
	private $statut_label = '';

	/**
	 * Classe d'affichage de la liste d'éléments
	 * @var elements_list_ui
	 */
	private $authority_list_ui;

	/**
	 * Tableau des paramètres perso de l'autorité
	 * @var array
	 */
	private $p_perso;

	/**
	 *
	 * @var string
	 */
	private $audit_type;

	/**
	 * Tableau des identifiants de concepts composés utilisant cette autorité
	 * @var array
	 */
	private $concepts_ids;

	/**
	 * Rendu HTML de la liste de notices associées à l'autorité
	 * @var string
	 */
	private $recordslist;

	/**
	 * URL de l'icône du type d'autorité
	 * @var string
	 */
	private $type_icon;

	/**
	 * Identifiant unique
	 * @var string
	 */
	private $uid;

	/**
	 * Constante utilisée dans les vedettes
	 * @var string
	 */
	private $vedette_type;

	/**
	 * url de la vignette associée à l'autorité
	 * @var string
	 */
	private $thumbnail_url;

	private $isbd;

	private $context_parameters;

	private $detail;

	private $authority_page;

	/**
	 * Lien ARK pointant vers l'autorité
	 * @var string
	 */
	private $ark_link;

	private $comment;

	private $commentaire;

	private $author_comment;

	private $ed_comment;

	private $title;

	public static $properties = array();

	public function __construct($id=0, $num_object=0, $type_object=0){
	    $this->id = intval($id);
	    $this->num_object = intval($num_object);
	    $this->type_object = intval($type_object);
	    $this->get_datas();
	    $this->uid = 'authority_'.md5(microtime(true));
	}

	public function get_datas() {
	    if(!$this->id && $this->num_object && $this->type_object) {
			$query = "select id_authority, num_statut, authorities_statut_label, authorities_statut_class_html, thumbnail_url from authorities join authorities_statuts on authorities_statuts.id_authorities_statut = authorities.num_statut where num_object=".$this->num_object." and type_object=".$this->type_object;
	        $result = pmb_mysql_query($query);
	        if($result) {
	        	if(pmb_mysql_num_rows($result)) {
	        		$row = pmb_mysql_fetch_object($result);
	        		pmb_mysql_free_result($result);

					$this->id = $row->id_authority;
	        		$this->num_statut = $row->num_statut;
	        		$this->statut_label = $row->authorities_statut_label;
	        		$this->statut_class_html = $row->authorities_statut_class_html;
	        		$this->thumbnail_url = $row->thumbnail_url;
	        	} elseif ($this->object_exists()) {
	        		$query = "insert into authorities(id_authority, num_object, type_object) values (0, ".$this->num_object.", ".$this->type_object.")";
	        		pmb_mysql_query($query);
	        		$this->id = pmb_mysql_insert_id();
	        		$this->num_statut = 1;
	        		$this->statut_label = '';
	        		$this->statut_class_html = 'statutnot1';
	        	}
	        }
		} elseif ($this->id) {
			$query = "select num_object, type_object, num_statut, authorities_statut_label, authorities_statut_class_html, thumbnail_url from authorities join authorities_statuts on authorities_statuts.id_authorities_statut = authorities.num_statut where id_authority=".$this->id;
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);

				$this->num_object = $row->num_object;
				$this->type_object = $row->type_object;
				$this->num_statut = $row->num_statut;
				$this->statut_label = $row->authorities_statut_label;
				$this->statut_class_html = $row->authorities_statut_class_html;
				$this->thumbnail_url = $row->thumbnail_url;
			}
		}
    }
    public static function aut_const_to_type_const($aut_const){
    	switch ($aut_const) {
    		case AUT_TABLE_AUTHORS :
    			return TYPE_AUTHOR;
    		case AUT_TABLE_CATEG :
    			return TYPE_CATEGORY;
    		case AUT_TABLE_PUBLISHERS :
    			return TYPE_PUBLISHER;
    		case AUT_TABLE_COLLECTIONS :
    			return TYPE_COLLECTION;
    		case AUT_TABLE_SUB_COLLECTIONS :
    			return TYPE_SUBCOLLECTION;
    		case AUT_TABLE_SERIES :
    			return TYPE_SERIE;
    		case AUT_TABLE_TITRES_UNIFORMES :
    			return TYPE_TITRE_UNIFORME;
    		case AUT_TABLE_INDEXINT :
    			return TYPE_INDEXINT;
    		case AUT_TABLE_CONCEPT :
    			return TYPE_CONCEPT;
    		case AUT_TABLE_AUTHPERSO :
    			return TYPE_AUTHPERSO;
    	}
    }
	public function get_id() {
	    return $this->id;
	}

	public function get_num_object() {
	    return $this->num_object;
	}

	public function get_num_statut() {
		return $this->num_statut;
	}

	public function get_statut_label() {
		return $this->statut_label;
	}

	public function get_statut_class_html() {
		return $this->statut_class_html;
	}

	public function get_display_statut_class_html() {
		global $charset;

		return "<span><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' class='".$this->get_statut_class_html()."' style='width:7px; height:7px; vertical-align:middle; margin-left:7px' /></a></span>
			<div id='zoom_statut".$this->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><span style='color:black'><b>".nl2br(htmlentities($this->get_statut_label(),ENT_QUOTES, $charset))."</b></span></div>";
	}

	public function set_num_statut($num_statut) {
		$num_statut = intval($num_statut);
		if(!$num_statut){
			$num_statut = 1;
		}else{
			$query = "select id_authorities_statut from authorities_statuts where id_authorities_statut=".$num_statut;
			$result = pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($result)){
				$num_statut = 1;
			}
		}
		$this->num_statut = $num_statut;
	}

	public function update() {
		global $msg;
		if($this->num_object && $this->type_object) {
			$query = "update authorities set num_statut='".$this->num_statut."', thumbnail_url = '".addslashes($this->thumbnail_url)."'  where num_object=".$this->num_object." and type_object=".$this->type_object;
			$result = pmb_mysql_query($query);
			if($result) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function get_type_object() {
	    return $this->type_object;
	}

	public function get_string_type_object() {
		if (!$this->string_type_object) {
		    switch ($this->type_object) {
		    	case AUT_TABLE_AUTHORS :
		    	    $this->string_type_object = 'author';
		    	    break;
		    	case AUT_TABLE_CATEG :
		    	    $this->string_type_object = 'category';
		    	    break;
		    	case AUT_TABLE_PUBLISHERS :
		    	    $this->string_type_object = 'publisher';
		    	    break;
		    	case AUT_TABLE_COLLECTIONS :
		    	    $this->string_type_object = 'collection';
		    	    break;
		    	case AUT_TABLE_SUB_COLLECTIONS :
		    	    $this->string_type_object = 'subcollection';
		    	    break;
		    	case AUT_TABLE_SERIES :
		    	    $this->string_type_object = 'serie';
		    	    break;
		    	case AUT_TABLE_TITRES_UNIFORMES :
		    	    $this->string_type_object = 'titre_uniforme';
		    	    break;
		    	case AUT_TABLE_INDEXINT :
		    	    $this->string_type_object = 'indexint';
		    	    break;
		    	case AUT_TABLE_CONCEPT :
		    	    $this->string_type_object = 'concept';
		    	    break;
		    	case AUT_TABLE_AUTHPERSO :
		    	    $this->string_type_object = 'authperso';
		    	    break;
		    }
		}
	    return $this->string_type_object;
	}

	public function delete() {
		//Suppression de cet item dans les paniers
		$authorities_caddie = new authorities_caddie();
		$authorities_caddie->del_item_all_caddies($this->id, $this->type_object);

		//Suppression de la vignette de l'autorité si il y en a une d'uploadée
		thumbnail::delete($this->id, 'authority');

	    $query = "delete from authorities where num_object=".$this->num_object." and type_object=".$this->type_object;
	    $result = pmb_mysql_query($query);
	    if($result) {
	        return true;
	    } else {
	        return false;
	    }
	}

	public function get_object_instance($params = array()) {
		return authorities_collection::get_authority($this->get_string_type_object(), $this->num_object);
	}

	public function __get($name) {
		$return = $this->look_for_attribute_in_class($this, $name);
		if (!$return) {
			$return = $this->look_for_attribute_in_class($this->get_object_instance(), $name);
		}
		return $return;
	}

	public function lookup($name,$context) {
		$value = null;
		if(strpos($name,":authority.")!==false){
			$property = str_replace(":authority.","",$name);
			$value = $this->generic_lookup($this, $property);
			if(!$value){
				$value = $this->generic_lookup($this->get_object_instance(), $property);
			}
		} else if (strpos($name,":aut_link.")!==false){
			$this->init_autlink_class();
			$property = str_replace(":aut_link.","",$name);
			$value = $this->generic_lookup($this->autlink_class, $property);
		} else {
			$attributes = explode('.', $name);
			// On regarde si on a directement une instance d'objet, dans le cas des boucles for
			if (is_object($obj = $context->getVariable(substr($attributes[0], 1))) && (count($attributes) > 1)) {
				$value = $obj;
				$property = str_replace($attributes[0].'.', '', $name);
				$value = $this->generic_lookup($value, $property);
			}
		}
		if(!$value){
			$value = null;
		}
		return $value;
	}

	private function generic_lookup($obj,$property){
		$attributes = explode(".",$property);
		for($i=0 ; $i<count($attributes) ; $i++){
			if(is_array($obj)){
				$obj = $obj[$attributes[$i]];
			} else if(is_object($obj)){
				$obj = $this->look_for_attribute_in_class($obj, $attributes[$i]);
			} else{
				$obj = null;
				break;
			}
		}
		return $obj;
	}

	private function look_for_attribute_in_class($class, $attribute, $parameters = array()) {
		if (is_object($class) && isset($class->{$attribute})) {
			return $class->{$attribute};
		} else if (method_exists($class, $attribute)) {
			return call_user_func_array(array($class, $attribute), $parameters);
		} else if (method_exists($class, "get_".$attribute)) {
			return call_user_func_array(array($class, "get_".$attribute), $parameters);
		} else if (method_exists($class, "is_".$attribute)) {
			return call_user_func_array(array($class, "is_".$attribute), $parameters);
		}
		return null;
	}

	public function render($context=array(), $templates_folder = ''){

		$template_path = $this->find_template("",$templates_folder);
		switch ($this->type_object) {
		    case AUT_TABLE_TITRES_UNIFORMES:
		    case AUT_TABLE_COLLECTIONS:
		    case AUT_TABLE_SUB_COLLECTIONS:
		        $this->comment = format_value_nl2br($this->comment);
		        break;
		    case AUT_TABLE_AUTHORS:
		        $this->author_comment = format_value_nl2br($this->author_comment);
		        break;
		    case AUT_TABLE_CATEG:
		        $this->commentaire = format_value_nl2br($this->commentaire);
		        break;
		    case AUT_TABLE_PUBLISHERS:
		        $this->ed_comment = format_value_nl2br($this->ed_comment);
		        break;
		}

		if(file_exists($template_path)){
			$h2o = H2o_collection::get_instance($template_path);
			$h2o->set('authority', $this);
			$this->init_autlink_class();
			$h2o->set('aut_link', $this->autlink_class);
			return $h2o->render($context);
		}
		return '';
	}

	public function find_template($what="",$template_folder="")
	{
	    global $opac_authorities_templates_folder, $include_path;

	    if (empty($template_folder)) {
	        $template_folder = $opac_authorities_templates_folder ?? "common";
	    }
	    // Le rep de templates
	    $template_path= $include_path.'/templates/authorities/'.$template_folder."/";
	    if(!empty($what)){
	        $template_path.="$what/";
	    }


	    // On gère les quelques cas particuliers possibles...
	    switch ($this->get_string_type_object()){
	        case "titre_uniforme" :
	            // on cherche le suffix suffixe possible _<nature>_<type>
	            $template = $this->get_string_type_object()."_".$this->get_object_instance()->oeuvre_nature."_".$this->get_object_instance()->oeuvre_type.".html";
	            $subst = $this->get_string_type_object()."_".$this->get_object_instance()->oeuvre_nature."_".$this->get_object_instance()->oeuvre_type."_subst.html";
	            if (file_exists($template_path.$subst)) {
	                return $template_path.$subst;
	            }
	            if (file_exists($template_path.$template)) {
	                return $template_path.$template;
	            }
	            // on cherche le suffix suffixe possible _<nature>
	            $template = $this->get_string_type_object()."_".$this->get_object_instance()->oeuvre_nature.".html";
	            $subst = $this->get_string_type_object()."_".$this->get_object_instance()->oeuvre_nature."_subst.html";
	            if (file_exists($template_path.$subst)) {
	                return $template_path.$subst;
	            }
	            if (file_exists($template_path.$template)) {
	                return $template_path.$template;
	            }
	        case "author" :
	            //on cherche le suffix suffixe possible _<type>
	            $template = $this->get_string_type_object()."_".$this->get_object_instance()->type.".html";
	            $subst = $this->get_string_type_object()."_".$this->get_object_instance()->type."_subst.html";
	            if (file_exists($template_path.$subst)) {
	                return $template_path.$subst;
	            }
	            if (file_exists($template_path.$template)) {
	                return $template_path.$template;
	            }
	    }
	    // On est encore, la, c'est donc le cas général qui s'applique, on prend le subst en priorité...
	    $template = $this->get_string_type_object().'.html';
	    $subst = $this->get_string_type_object().'_subst.html';
	    if (file_exists($template_path.$subst)) {
	        return $template_path.$subst;
	    }
	    if (file_exists($template_path.$template)) {
	        return $template_path.$template;
	    }
	    // Si c'est pas le common, on essaye avec le common...
	    if ($template_folder != "common") {
	        return $this->find_template($what,"common");
	    }
	    // On est encore là... désolé, on n'a aucun template à utiliser !
	    return false;
	}

	private function init_autlink_class(){
		if(!$this->autlink_class){
			if ($this->type_object == AUT_TABLE_AUTHPERSO) {
				$query = "select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority= ".$this->num_object;
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$this->autlink_class = new aut_link($row->authperso_authority_authperso_num+1000, $this->num_object);
				}
			} else {
				$this->autlink_class = new aut_link($this->type_object, $this->num_object);
			}
		}
		return  $this->autlink_class;
	}

	public function get_indexing_concepts(){
 		$concepts_list = new skos_concepts_list();
 		switch($this->type_object){
 			case AUT_TABLE_AUTHORS :
 				if ($concepts_list->set_concepts_from_object(TYPE_AUTHOR, $this->num_object)) {
 					return $concepts_list->get_concepts();
 				}
 				break;
			case AUT_TABLE_PUBLISHERS :
				if ($concepts_list->set_concepts_from_object(TYPE_PUBLISHER, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_COLLECTIONS :
				if ($concepts_list->set_concepts_from_object(TYPE_COLLECTION, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_SUB_COLLECTIONS :
				if ($concepts_list->set_concepts_from_object(TYPE_SUBCOLLECTION, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_SERIES :
				if ($concepts_list->set_concepts_from_object(TYPE_SERIE, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_INDEXINT :
				if ($concepts_list->set_concepts_from_object(TYPE_INDEXINT, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				if ($concepts_list->set_concepts_from_object(TYPE_TITRE_UNIFORME, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_CATEG :
				if ($concepts_list->set_concepts_from_object(TYPE_CATEGORY, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
			case AUT_TABLE_AUTHPERSO :
				if ($concepts_list->set_concepts_from_object(TYPE_AUTHPERSO, $this->num_object)) {
					return $concepts_list->get_concepts();
				}
				break;
 		}
		return null;
	}

	public function set_authority_tabs($authority_tabs) {
		$this->authority_tabs = $authority_tabs;
	}

	public function get_authority_tabs() {
		return $this->authority_tabs;
	}

	public function get_type_label(){
		if (!$this->type_label) {
			if ($this->get_type_object() != AUT_TABLE_AUTHPERSO) {
				$this->type_label = self::get_type_label_from_type_id($this->get_type_object());
			} elseif ($this->get_type_object() == AUT_TABLE_AUTHPERSO) {
			    $this->type_label = $this->get_object_instance()->get_name();
			} else {
				$auth_datas = $this->get_object_instance()->get_data();
				$this->type_label = $auth_datas['name'];
			}
		}
		return $this->type_label;
	}

	public static function get_type_label_from_type_id($type_id) {
		global $msg;
		switch($type_id){
			case AUT_TABLE_AUTHORS :
				return $msg['isbd_author'];
			case AUT_TABLE_PUBLISHERS :
				return $msg['isbd_editeur'];
			case AUT_TABLE_COLLECTIONS :
				return $msg['isbd_collection'];
			case AUT_TABLE_SUB_COLLECTIONS :
				return $msg['isbd_subcollection'];
			case AUT_TABLE_SERIES :
				return $msg['isbd_serie'];
			case AUT_TABLE_INDEXINT :
				return $msg['isbd_indexint'];
			case AUT_TABLE_TITRES_UNIFORMES :
				return $msg['isbd_titre_uniforme'];
			case AUT_TABLE_CATEG :
				return $msg['isbd_categories'];
			case AUT_TABLE_CONCEPT :
				return $msg['skos_concept'];
		}
	}

	public function get_aut_link() {

	    return $this->init_autlink_class();
	}

	/**
	 * Retourne les paramètres persos
	 * @return array
	 */
	public function get_p_perso() {
		if (!$this->p_perso) {
			$this->p_perso = array();
			if($this->get_type_object() ){
				$parametres_perso = new parametres_perso($this->get_prefix_for_pperso());
				$ppersos = $parametres_perso->show_fields($this->num_object);
				$out_values = $parametres_perso->get_out_values($this->num_object);
				if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS'])){
					foreach ($ppersos['FIELDS'] as $pperso) {
					    if($pperso['OPAC_SHOW'] && $pperso['AFF']) {
					        if ($pperso["TYPE"] !== 'html') {
					            $pperso['AFF'] = nl2br($pperso["AFF"]);
					        }
							$this->p_perso[$pperso['NAME']] = $pperso;
							$this->p_perso[$pperso['NAME']]['values'] = $out_values[$pperso['NAME']]['values'];
						}
					}
				}
			}
		}
		return $this->p_perso;
	}

	public function get_customs() {
		return $this->get_p_perso();
	}

	public function get_prefix_for_pperso(){
		switch($this->get_type_object()){
			case AUT_TABLE_CATEG:
				return 'categ';
			case AUT_TABLE_TITRES_UNIFORMES:
				return 'tu';
			case AUT_TABLE_CONCEPT:
				return 'skos';
			default :
				return $this->get_string_type_object();
		}
	}

	public function get_audit_type() {
		if (!$this->audit_type) {
			switch ($this->type_object) {
				case AUT_TABLE_AUTHORS :
					$this->audit_type = AUDIT_AUTHOR;
					break;
				case AUT_TABLE_CATEG :
					$this->audit_type = AUDIT_CATEG;
					break;
				case AUT_TABLE_PUBLISHERS :
					$this->audit_type = AUDIT_PUBLISHER;
					break;
				case AUT_TABLE_COLLECTIONS :
					$this->audit_type = AUDIT_COLLECTION;
					break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$this->audit_type = AUDIT_SUB_COLLECTION;
					break;
				case AUT_TABLE_SERIES :
					$this->audit_type = AUDIT_SERIE;
					break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$this->audit_type = AUDIT_TITRE_UNIFORME;
					break;
				case AUT_TABLE_INDEXINT :
					$this->audit_type = AUDIT_INDEXINT;
					break;
				case AUT_TABLE_CONCEPT :
					$this->audit_type = AUDIT_CONCEPT;
					break;
				case AUT_TABLE_AUTHPERSO :
					$req="select authperso_authority_authperso_num from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $this->num_object;
					$res = pmb_mysql_query($req);
					if(($r=pmb_mysql_fetch_object($res))) {
						$this->audit_type=($r->authperso_authority_authperso_num + 1000);
					}
					break;
			}
		}
		return $this->audit_type;
	}

	public function get_special() {
		global $include_path;

		$special_file = $include_path.'/templates/authorities/special/authority_special.class.php';
		if (file_exists($special_file)) {
			require_once($special_file);
			return new authority_special($this);
		}
		return null;
	}

	/**
	 * Renvoie le tableau des identifiants de concepts composés utilisant cette autorité
	 * @return array
	 */
	public function get_concepts_ids() {
		if (!isset($this->concepts_ids)) {
			$this->concepts_ids = array();
			$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->get_num_object(), $this->get_string_type_object());
			foreach($vedette_composee_found as $vedette_id){
				$this->concepts_ids[] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
			}
		}
		return $this->concepts_ids;
	}

	public function get_uid() {
		return $this->uid;
	}

	public function get_caddie() {
		return "";
	}

	public function get_thumbnail_url() {
		return $this->thumbnail_url;
	}

	public function set_thumbnail_url($thumbnail_url) {
		$uploaded_thumbnail_url = thumbnail::create($this->get_id(), 'authority');
		if($uploaded_thumbnail_url) {
			$this->thumbnail_url = $uploaded_thumbnail_url;
		} else {
			$this->thumbnail_url = $thumbnail_url;
		}
	}

	public function get_thumbnail() {
		return thumbnail::get_image('', $this->thumbnail_url);
	}

	public function get_recordslist() {
		if (isset($this->recordslist)) {
			return $this->recordslist;
		}
		$this->get_authority_page();
		$this->recordslist = $this->authority_page->get_recordslist(true);
		return $this->recordslist;
	}

	public function set_recordslist($recordslist) {
		$this->recordslist = $recordslist;
	}

	public function format_datas(){
		$formatted_data = array(
				'id' => $this->get_id(),
				'num_object' => $this->get_num_object(),
				'statut' => $this->get_statut_label(),
				'thumbnail_url' => $this->get_thumbnail_url(),
				'thumbnail' => $this->get_thumbnail()
		);
		//CP
		$type_object = $this->get_string_type_object();
		switch ($type_object) {
			case 'titre_uniforme' :
				$parametres_perso = new parametres_perso('tu');
				break;
			case 'category' :
				$parametres_perso = new parametres_perso('categ');
				break;
			case 'authperso' :
			    $parametres_perso = new custom_parametres_perso("authperso","authperso", $this->get_object_instance()->get_authperso_num());
				break;
			default :
				$parametres_perso = new parametres_perso($type_object);
				break;
		}
		$formatted_data['customs'] = $parametres_perso->get_out_values($this->get_num_object());


		// AR - 20/06/18 -> la méthode get_indexing_concept répond parfaitement au besoin (et même mieux, car la c'était pas la bonne classe)
		//$skos_concept = new skos_concept($this->get_num_object());
		//$formatted_data['concepts'] = $skos_concept->format_datas();

		//TODO Autorités liées
		//TODO Notices liées

		return $formatted_data;
	}

	public static function get_const_type_object($string_type_object) {
		switch ($string_type_object) {
			case  'author':
			case  'authors':
				return AUT_TABLE_AUTHORS;
			case 'category':
			case 'categories':
				return AUT_TABLE_CATEG;
			case 'publisher' :
			case 'publishers' :
				return AUT_TABLE_PUBLISHERS;
			case 'collection' :
			case 'collections' :
				return AUT_TABLE_COLLECTIONS;
			case 'subcollection' :
			case 'subcollections' :
				return AUT_TABLE_SUB_COLLECTIONS;
			case 'serie':
			case 'series':
				return AUT_TABLE_SERIES;
			case 'titre_uniforme' :
			case 'work' :
			case 'works' :
				return AUT_TABLE_TITRES_UNIFORMES;
			case 'indexint' :
				return AUT_TABLE_INDEXINT;
			case 'concept' :
			case 'concepts' :
				return AUT_TABLE_CONCEPT;
			case 'authperso' :
			    return AUT_TABLE_AUTHPERSO;
			default:
			    if (strpos($string_type_object, "authperso") !== false) {
			        return AUT_TABLE_AUTHPERSO;
			    }
			    return 0;
		}
	}

	public function get_type_icon() {
	    if (!isset($this->type_icon)) {
	        $auth_type = $this->get_string_type_object();
	        switch ($auth_type) {
	            case 'author' :
	                $author_type = $this->get_object_instance()->type;
	                if (!empty($author_type)) {
	                    $this->type_icon = get_url_icon('authorities/'.$auth_type.'_'.$author_type.'_icon.png');
	                    break;
	                }
	                $this->type_icon = get_url_icon('authorities/'.$auth_type.'_icon.png');
	                break;
	            case 'titre_uniforme' :
	                // stocker comme ça ou juste les propriétés qui nous intéressent ? qu'est-ce qui est le plus performant?
	                $tu_type = $this->object_instance->oeuvre_type;
	                $tu_nature = $this->object_instance->oeuvre_nature;
	                if (!empty($tu_type) && !empty($tu_nature)) {
	                    $this->type_icon = get_url_icon('authorities/tu_'.$tu_nature.'_'.$tu_type.'_icon.png');
	                    break;
	                }
	                $this->type_icon = get_url_icon('authorities/'.$auth_type.'_icon.png');
	                break;
	            default :
	                $this->type_icon = get_url_icon('authorities/'.$auth_type.'_icon.png');
	                break;
	        }
	        //On teste et si le fichier n'existe pas on prend le fichier du type d'autorité par défaut
	        if (!file_exists($this->type_icon)) {
	            $this->type_icon = get_url_icon('authorities/'.$auth_type.'_icon.png');
	        }
	    }
	    return $this->type_icon;
	}

	public function get_permalink() {
		return $this->get_object_instance()->get_permalink();
	}

	public function get_entity_type(){
		return 'authority';
	}

	public function get_isbd() {
		if (!empty($this->isbd)) {
			return $this->isbd;
		}
		$this->isbd = $this->get_object_instance()->get_isbd();

		$template_path = $this->find_template("isbd");
		if($template_path){
			$h2o = H2o_collection::get_instance($template_path);
			$isbd = $h2o->render(array('authority' => $this));
			$this->isbd =  $isbd;
		}
		$this->isbd =  str_replace(array("\n", "\t", "\r"), '', strip_tags($this->isbd));
		return $this->isbd;
	}

	public function get_detail() {
		if (isset($this->detail)) {
			return $this->detail;
		}
		$this->detail = '';
		$template_path = $this->find_template("detail");
		if($template_path){
			$h2o = H2o_collection::get_instance($template_path);
			$this->detail = $h2o->render(array('element' => $this));
		}
		return $this->detail;
	}

	public function get_context_parameters() {
		return $this->context_parameters;
	}

	public function set_context_parameters($context_parameters=array()) {
		$this->context_parameters = $context_parameters;
	}

	public function add_context_parameter($key, $value) {
		$this->context_parameters[$key] = $value;
	}

	public function delete_context_parameter($key) {
		unset($this->context_parameters[$key]);
	}

	public function get_vedette_type(){
		if (!$this->vedette_type) {
			switch ($this->type_object) {
				case AUT_TABLE_AUTHORS :
					$this->vedette_type = TYPE_AUTHOR;
					break;
				case AUT_TABLE_CATEG :
					$this->vedette_type = TYPE_CATEGORY;
					break;
				case AUT_TABLE_PUBLISHERS :
					$this->vedette_type = TYPE_PUBLISHER;
					break;
				case AUT_TABLE_COLLECTIONS :
					$this->vedette_type = TYPE_COLLECTION;
					break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$this->vedette_type = TYPE_SUBCOLLECTION;
					break;
				case AUT_TABLE_SERIES :
					$this->vedette_type = TYPE_SERIE;
					break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$this->vedette_type = TYPE_TITRE_UNIFORME;
					break;
				case AUT_TABLE_INDEXINT :
					$this->vedette_type = TYPE_INDEXINT;
					break;
				case AUT_TABLE_CONCEPT :
					$this->vedette_type = TYPE_CONCEPT_PREFLABEL;
					break;
				case AUT_TABLE_AUTHPERSO :
					$this->vedette_type = TYPE_AUTHPERSO;
					break;
			}
		}
		return $this->vedette_type;
	}

	public static function get_authority_id_from_entity($id, $type) {
	    $query = "SELECT id_authority
				FROM authorities
				WHERE num_object = '".$id."'
				AND type_object = '".$type."'";
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)) {
	        $row = pmb_mysql_fetch_assoc($result);
	        return $row['id_authority'];
	    }
	    return 0;
	}

	public static function get_properties($type, $prefix){
		if(!isset(self::$properties[$type])){
			self::$properties[$type] = array();
			$authority_props = array_keys(get_class_vars('authority'));

			$sub_class = self::get_class_name_from_type($type);
			$sub_class_props = array_keys(get_class_vars($sub_class));


			$authority_methods = get_class_methods('authority');
			$sub_class_methods = get_class_methods($sub_class);

			$authority_methods = self::get_getters($authority_methods);
			$sub_class_methods = self::get_getters($sub_class_methods);
			$properties = array_unique(array_merge($authority_props, $sub_class_props, $authority_methods, $sub_class_methods));
			$final_properties = array();
			foreach($properties as $property){
				/**
				 * TODO: ajouter un message cohérent en fonction de la propriété
				 */
				$final_properties[] = array(
						'var' => $prefix.'.'.$property,
						'desc' => 'aut_'.$property
				);
			}
			self::$properties[$type] = $final_properties;
		}
		return self::$properties[$type];
	}

	public static function get_getters($methods_list = array()){
		$getters = array();
		foreach($methods_list as $method){
			if((strpos($method, 'get') === 0) || (strpos($method, 'is') === 0)){
				$getters[] = preg_replace('/get_|get/', '', $method);
			}
		}
		return $getters;
	}

	public static function get_class_name_from_type($type){
		switch($type){
			case AUT_TABLE_AUTHORS :
				return 'auteur';
			case AUT_TABLE_CATEG :
				return 'category';
			case AUT_TABLE_PUBLISHERS :
				return 'editeur';
			case AUT_TABLE_COLLECTIONS :
				return 'collection';
			case AUT_TABLE_SUB_COLLECTIONS :
				return 'subcollection';
			case AUT_TABLE_SERIES :
				return 'serie';
			case AUT_TABLE_INDEXINT :
				return 'indexint';
			case AUT_TABLE_TITRES_UNIFORMES :
				return 'titre_uniforme';
			case AUT_TABLE_CONCEPT :
				return 'skos_concept';
			case AUT_TABLE_INDEX_CONCEPT :
				return 'concept';
			case AUT_TABLE_AUTHPERSO :
				return 'authperso_data';
			default :
				return '';
		}
	}

	public function get_authority_page() {
		if (isset($this->authority_page)) {
			return $this->authority_page;
		}

		if ($this->type_object == AUT_TABLE_CONCEPT){
    		$class_name = 'skos_page_'.$this->get_string_type_object();
		} else {
    		$class_name = 'authority_page_'.$this->get_string_type_object();
		}
		if (class_exists($class_name)) {
			$this->authority_page = new $class_name($this->get_num_object());
			return $this->authority_page;
		}
		$this->authority_page = new authority_page($this);
		return $this->authority_page;
	}

	public function get_display_in_contribution($string_type, $template_directory = '') {
	    global $include_path;

	    if (empty($template_directory)) {
	        $template_directory = 'common';
	    }

	    $template_path = '';
	    switch (true) {
	        case file_exists("$include_path/templates/authorities/$template_directory/contribution/$string_type"."_subst.html"):
	            $template_path = "$include_path/templates/authorities/$template_directory/contribution/$string_type"."_subst.html";
	            break;
	        case file_exists("$include_path/templates/authorities/$template_directory/contribution/$string_type.html"):
	            $template_path = "$include_path/templates/authorities/$template_directory/contribution/$string_type.html";
	            break;
	        case file_exists("$include_path/templates/authorities/$template_directory/list/$string_type.html"):
	            $template_path = "$include_path/templates/authorities/$template_directory/list/$string_type.html";
	            break;
	    }

	    if ($template_path) {
	        $h2o = H2o_collection::get_instance($template_path);
	        $template = $h2o->render(array('element' => $this));
	        $template = str_replace(array('\n', '\t', '\r'), '', $template);
	    } else {
	        $template = $this->get_isbd();
	    }

	    return $template;
	}

	public function get_edit_link () {
	    $this->get_string_type_object();
	    if (contribution_area_forms_controller::can_edit($this->string_type_object, $this->num_object)) {
	        return "./index.php?lvl=contribution_area&sub=convert&action=edit_entity&entity_type=$this->string_type_object&entity_id=$this->num_object";
	    }
	    return "";
	}

	/**
	 * Liste les methodes, utile pour les templates django
	 * @return []
	 */
	public function get_methods_infos() {
	    return entities::get_methods_infos($this);
	}

	/**
	 * Liste les proprietes, utile pour les templates django
	 * @return []
	 */
	public function get_properties_infos() {
	    return entities::get_properties_infos($this);
	}

	public function get_ark_link() {
		if(empty($this->ark_link)) {
			global $pmb_ark_activate;
			if($pmb_ark_activate) {
				$arkAuthority = new ArkAuthority(intval($this->id));
				$ark = ArkModel::getArkFromEntity($arkAuthority);
				$ark->setArkEntity($arkAuthority);
				$this->ark_link = $ark->getArkLink();
			}
		}
		return $this->ark_link;
	}

	/**
	 * Retourne le titre de l'autorité selon son type
	 */
	public function get_title()
	{
		if(! empty($this->title)) {
			return $this->title;
		}

		if(method_exists($this->get_object_instance(), "getData")) {
			$this->get_object_instance()->getData();
		} else if(method_exists($this->get_object_instance(), "get_data")) {
			$this->get_object_instance()->get_data();
		} else {
			//TODO trouver quoi mettre dans le else
		}

		switch(true) {
			case ! empty($this->get_object_instance()->isbd_entry):
				$this->title = $this->get_object_instance()->isbd_entry;
				break;
			case ! empty($this->get_object_instance()->name):
				$this->title = $this->get_object_instance()->name;
				break;
			case !empty($this->get_object_instance()->libelle):
				$this->title = $this->get_object_instance()->libelle;
				break;
			case method_exists($this->get_object_instance(), "get_name"):
				$this->title = $this->get_object_instance()->get_name();
				break;
			case method_exists($this->get_object_instance(), "get_display_label"):
				$this->title = $this->get_object_instance()->get_display_label();
				break;
			default:
				$this->title = $this->get_isbd();
				break;
		}

		return $this->title;
	}

	/**
	 * Vérifie si l'objet existe dans la base
	 *
	 * @return bool
	 */
	public function object_exists() {
		switch($this->type_object) {
			case AUT_TABLE_AUTHORS :
				$query = "SELECT 1 FROM authors WHERE author_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_PUBLISHERS :
				$query = "SELECT 1 FROM publishers WHERE ed_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_COLLECTIONS :
				$query = "SELECT 1 FROM collections WHERE collection_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_SUB_COLLECTIONS :
				$query = "SELECT 1 FROM sub_collections WHERE sub_coll_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_SERIES :
				$query = "SELECT 1 FROM series WHERE serie_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_INDEXINT :
				$query = "SELECT 1 FROM indexint WHERE indexint_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_TITRES_UNIFORMES :
				$query = "SELECT 1 FROM titres_uniformes WHERE tu_id = " . intval($this->num_object);
				break;

			case AUT_TABLE_CATEG :
				$query = "SELECT 1 FROM noeuds WHERE noeuds.id_noeud = " . intval($this->num_object);
				break;

			case AUT_TABLE_AUTHPERSO :
				$query = "SELECT 1 FROM authperso WHERE id_authperso = " . intval($this->num_object);
				break;

			case AUT_TABLE_CONCEPT :
			case AUT_TABLE_INDEX_CONCEPT :
				if (is_numeric($this->num_object)) {
					$query = "SELECT 1 FROM onto_uri WHERE uri_id = " . intval($this->num_object);
				} else {
					$query = "SELECT 1 FROM onto_uri WHERE uri = '" . addslashes($this->num_object) . "'";
				}
				break;

			default :
				// On ne doit jamais arriver ici, si c'est le cas,
				// vérifier le type de l'objet ou ajouter un cas particulier
				// PS: Voir les constantes dans aut_link.class.php
				return false;
		}

		if (!empty($query)) {
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				pmb_mysql_free_result($result);
				return true;
			}
		}
		return false;
	}
}