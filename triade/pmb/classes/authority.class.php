<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority.class.php,v 1.119.2.1.2.2 2025/05/07 12:44:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Models\ArkModel;
use Pmb\Ark\Entities\ArkEntityPmb;
use Pmb\Ark\Entities\ArkAuthority;

global $class_path, $include_path;
require_once($include_path."/h2o/pmb_h2o.inc.php");
require_once($class_path.'/skos/skos_concepts_list.class.php');
require_once($class_path.'/skos/skos_view_concepts.class.php');
require_once($class_path.'/aut_link.class.php');
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
require_once($class_path.'/elements_list/elements_authorities_list_ui.class.php');
require_once($class_path.'/elements_list/elements_docnums_list_ui.class.php');
require_once($class_path.'/elements_list/elements_cms_editorial_sections_list_ui.class.php');
require_once($class_path.'/elements_list/elements_cms_editorial_articles_list_ui.class.php');
require_once($class_path.'/elements_list/elements_graph_ui.class.php');
require_once($class_path.'/elements_list/elements_expl_list_ui.class.php');
require_once($class_path.'/form_mapper/form_mapper.class.php');
require_once($class_path.'/thumbnail.class.php');
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/custom_parametres_perso.class.php");
require_once($class_path.'/authorities_caddie.class.php');
require_once ($class_path.'/indexation_record.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/indexation_stack.class.php');


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
	 * Tableau des identifiants de notices utilisant cette autorité comme vedette
	 * @var array
	 */
	private $records_ids;

	/**
	 * Tableau des identifiants d'oeuvres utilisant cette autorité comme vedette
	 * @var array
	 */
	private $tus_ids;

	/**
	 * URL de l'icône du type d'autorité
	 * @var string
	 */
	private $type_icon;

	/**
	 * Nom de la table temporaire mémorisant l'usage de l'autorité
	 * @var string
	 */
	private $table_tempo;

	/**
	 * Tableau des element utilisant cette autorité comme paramètre personalisé
	 * @var array
	 */
	private $used_in_pperso_authorities;

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

	private $icon_pointe_in_cart;

	private $icon_del_in_cart;

	private static $indexation_record;

	private $isbd;

	private $context_parameters;

	private $detail;

	private $commentaire;
	
	private $author_comment;
	
	private $ed_comment;
	
	public static $properties = array();

	public static $custom_fields = array();

	public static $type_table = array(
			TYPE_AUTHOR => AUT_TABLE_AUTHORS,
			TYPE_CATEGORY => AUT_TABLE_CATEG,
			TYPE_PUBLISHER => AUT_TABLE_PUBLISHERS,
			TYPE_COLLECTION => AUT_TABLE_COLLECTIONS,
			TYPE_SUBCOLLECTION => AUT_TABLE_SUB_COLLECTIONS,
			TYPE_SERIE => AUT_TABLE_SERIES,
			TYPE_TITRE_UNIFORME => AUT_TABLE_TITRES_UNIFORMES,
			TYPE_INDEXINT => AUT_TABLE_INDEXINT,
			TYPE_AUTHPERSO => AUT_TABLE_AUTHPERSO,
			TYPE_CONCEPT => AUT_TABLE_CONCEPT,
	);

	/**
	 * Lien ARK pointant vers l'autorité
	 * @var string
	 */
	private $ark_link;

	public $comment;
	
	public function __construct($id=0, $num_object=0, $type_object=0){
	    $this->id = intval($id);
	    $this->num_object = intval($num_object);
	    $this->type_object = intval($type_object);
	    $this->get_datas();
		$this->table_tempo = 'pperso_authorities'.md5(microtime(true));
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

		return "<span><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' class='".$this->get_statut_class_html()."' style='width:7px; height:7px; vertical-align:middle; margin-left:7px' alt='".htmlentities($this->get_statut_label(),ENT_QUOTES, $charset)."' /></a></span>
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
		global $pmb_ark_activate;
		if($this->num_object && $this->type_object) {
			$query = "update authorities set num_statut='".$this->num_statut."', thumbnail_url = '".addslashes($this->thumbnail_url)."'  where num_object=".$this->num_object." and type_object=".$this->type_object;
			$result = pmb_mysql_query($query);
			if($result) {
			    if ($pmb_ark_activate) {
			        ArkModel::saveArkFromEntity($this);
			    }
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
		    $this->string_type_object = static::aut_const_to_string($this->type_object);
		}
	    return $this->string_type_object;
	}

	public function get_type_const() {
		return static::aut_const_to_type_const($this->type_object);
	}

	public static function aut_const_to_string($aut_const){
		switch ($aut_const) {
			case AUT_TABLE_AUTHORS :
				return 'author';
			case AUT_TABLE_CATEG :
				return 'category';
			case AUT_TABLE_PUBLISHERS :
				return 'publisher';
			case AUT_TABLE_COLLECTIONS :
				return 'collection';
			case AUT_TABLE_SUB_COLLECTIONS :
				return 'subcollection';
			case AUT_TABLE_SERIES :
				return 'serie';
			case AUT_TABLE_TITRES_UNIFORMES :
				return 'titre_uniforme';
			case AUT_TABLE_INDEXINT :
				return 'indexint';
			case AUT_TABLE_CONCEPT :
				return 'concept';
			case AUT_TABLE_AUTHPERSO :
				return 'authperso';
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

	public function delete() {
	    global $pmb_ark_activate;
		//Suppression de cet item dans les paniers
		$authorities_caddie = new authorities_caddie();
		$authorities_caddie->del_item_all_caddies($this->id, $this->type_object);

		//Suppression de la vignette de l'autorité si il y en a une d'uploadée
		thumbnail::delete($this->id, 'authority');

		if ($this->get_prefix_for_pperso() != "authperso") {
		    $query = "DELETE FROM " . $this->get_prefix_for_pperso() . "_custom_values where " . $this->get_prefix_for_pperso() ."_custom_origine=" . $this->num_object;
		    pmb_mysql_query($query);
		    $query = "DELETE FROM " . $this->get_prefix_for_pperso() . "_custom_dates where " . $this->get_prefix_for_pperso() ."_custom_origine=" . $this->num_object;
		    pmb_mysql_query($query);
		}
		if ($pmb_ark_activate) {
		    $ark = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $this->id);
    		$ark->markAsDeleted();
		}
	    $query = "delete from authorities where num_object=".$this->num_object." and type_object=".$this->type_object;
	    $result = pmb_mysql_query($query);
	    if($result) {
	        return true;
	    } else {
	        return false;
	    }
	}

	public function get_object_instance($params = array()) {
	    return authorities_collection::get_authority($this->type_object, $this->num_object, $params);
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
			    $obj = (!empty($obj[$attributes[$i]]) ? $obj[$attributes[$i]] : null );
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

	public function render($context=array()){
		$template_path =  $this->find_template();
		if(file_exists($template_path)){
			$h2o = new H2o($template_path);

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

			$h2o->addLookup(array($this,"lookup"));
			$this->init_autlink_class();
			$h2o->set('aut_link', $this->autlink_class);
			echo $h2o->render($context);
		}
	}

	public function find_template($what="")
	{
	    global  $include_path;
	    // Le rep de templates
	    $template_path= $include_path.'/templates/authorities/';
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

	    // On est encore là... désolé, ça ne devrait arriver, on n'a aucun template à utiliser !
	    return false;
	}

	/**
	 * Retourn la classe d'affichage des éléments des onglets
	 * @return elements_list_ui
	 */
	public function get_authority_list_ui(){
		global $quoi;

		if(!$this->authority_list_ui){
			$tab = null;

			foreach($this->authority_tabs->get_tabs() as $current_tab){
				if (!$tab && $current_tab->can_display_tab()) {
					$tab = $current_tab;
				}
				if(($current_tab->get_name() == $quoi) && $current_tab->can_display_tab()){
					$tab = $current_tab;
					break;
				}
			}
			if ($tab) {
				$quoi = $tab->get_name();
				switch($tab->get_content_type()){
					case 'records':
						$this->authority_list_ui = new elements_records_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
						break;
					case 'authorities':
						$this->authority_list_ui = new elements_authorities_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
						break;
					case 'docnums':
						$this->authority_list_ui = new elements_docnums_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
						break;
					case 'sections':
						$this->authority_list_ui = new elements_cms_editorial_sections_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
						break;
					case 'articles':
						$this->authority_list_ui = new elements_cms_editorial_articles_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
						break;
					case 'graph':
						$this->authority_list_ui = new elements_graph_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed());
						break;
					case 'expl':
					    $this->authority_list_ui = new elements_expl_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed());
						break;
				}
			}
		}
		return $this->authority_list_ui;
	}

	public function init_autlink_class(){
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
			}elseif($this->get_type_object() == AUT_TABLE_AUTHPERSO) {
				$auth_datas = $this->get_object_instance()->get_data();
				$this->type_label = $auth_datas['authperso']['name'];
			} else {
				$auth_datas = $this->get_object_instance()->get_data();
				$this->type_label = $auth_datas['name'];
			}
		}
		return $this->type_label;
	}

	public static function get_type_label_from_type_id($type_id) {
		global $msg;
		$type_id = (int) $type_id;
		switch($type_id) {
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
				return $msg['concept_menu'];
			case AUT_TABLE_AUTHPERSO :
				return $msg['notice_authperso'];
			default:
			    return '';
		}
	}

	public function build_isbd_entry_lien_gestion() {

	    switch ($this->type_object) {
	        case AUT_TABLE_AUTHORS :
	            $sub_val = 'author';
	            break;
	        case AUT_TABLE_CATEG :
	            $sub_val = 'category';
	            break;
	        case AUT_TABLE_PUBLISHERS :
	            $sub_val = 'publisher';
	            break;
	        case AUT_TABLE_COLLECTIONS :
	            $sub_val = 'collection';
	            break;
	        case AUT_TABLE_SUB_COLLECTIONS :
	            $sub_val = 'subcollection';
	            break;
	        case AUT_TABLE_SERIES :
	            $sub_val = 'serie';
	            break;
	        case AUT_TABLE_INDEXINT :
	            $sub_val = 'indexint';
	            break;
	        case AUT_TABLE_TITRES_UNIFORMES :
	            $sub_val = 'titre_uniforme';
	            break;
	        case AUT_TABLE_CONCEPT :
	            $sub_val = 'concept';
	            break;
	        case AUT_TABLE_AUTHPERSO :
	            $sub_val = 'authperso';
	            break;
	        default :
	            return '';
	    }
	    // construit le lien si l'utilisateur à accès aux autorités
	    if (SESSrights & AUTORITES_AUTH) {
	        return "<a href='./autorites.php?categ=see&sub=" . $sub_val . "&id=" .$this->num_object ."' class='lien_gestion' title=''>" . $this->get_isbd() ."</a>";
	    } else {
	        return $this->get_isbd();
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
		    if ($this->get_prefix_for_pperso() == "authperso") {
		        $query = "select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority='" . $this->num_object . "' ";
		        $result = pmb_mysql_query($query);
		        if (!pmb_mysql_num_rows($result)) return array();
		        $r  = pmb_mysql_fetch_object($result);
		        $parametres_perso = new custom_parametres_perso("authperso","authperso",$r->authperso_authority_authperso_num);
		    } else {
                  $parametres_perso = new parametres_perso($this->get_prefix_for_pperso());
		    }
			$ppersos = $parametres_perso->show_fields($this->num_object);
			if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS'])){
			    foreach ($ppersos['FIELDS'] as $pperso) {
			        if ($pperso["TYPE"] !== 'html') {
			            $pperso['AFF'] = nl2br($pperso["AFF"]);
			        }
					$this->p_perso[$pperso['NAME']] = $pperso;
				}
			}
		}
		return $this->p_perso;
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

	public function get_mapping_profiles(){
		$returnedDatas = array();
		switch($this->type_object){
			case AUT_TABLE_AUTHORS :

				break;
			case AUT_TABLE_CATEG :

				break;
			case AUT_TABLE_PUBLISHERS :

				break;
			case AUT_TABLE_COLLECTIONS :

				break;
			case AUT_TABLE_SUB_COLLECTIONS :

				break;
			case AUT_TABLE_SERIES :

				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				$mapper = form_mapper::getMapper('tu');
				break;
			case AUT_TABLE_INDEXINT :

				break;
			case AUT_TABLE_CONCEPT :

				break;
			case AUT_TABLE_AUTHPERSO :

				break;
		}

		if($mapper){
			$mapper->setId($this->num_object);
			$destinations = $mapper->getDestinations();
			foreach($destinations as $dest){
			    $profile = $mapper->getProfiles($dest);
			    if($profile){
			        $returnedDatas[] = $profile;
			    }
			}
		}
		return $returnedDatas;
	}

	/**
	 * Renvoie le tableau des identifiants de concepts composés utilisant cette autorité
	 * @return array
	 */
	public function get_concepts_ids() {
		if (!isset($this->concepts_ids)) {
			$this->concepts_ids = array();
			$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->get_num_object(), $this->get_type_const());
			foreach($vedette_composee_found as $vedette_id){
				// toutes les vedettes composées ne sont pas des concepts
				if($concepts_id = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL)) {
					$this->concepts_ids[] = $concepts_id;
				}
			}
		}
		return $this->concepts_ids;
	}

	/**
	 * Renvoie le tableau des identifiants de notices utilisant cette autorité comme vedette
	 * @return array
	 */
	public function get_records_ids() {
		if (!isset($this->records_ids)) {
			$this->records_ids = array();
			$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->get_num_object(), $this->get_type_const());
			foreach($vedette_composee_found as $vedette_id){

				if($record_id = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_NOTICE_RESPONSABILITY_PRINCIPAL)) {
					$this->records_ids[] = $record_id;
				}
				if($record_id = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_NOTICE_RESPONSABILITY_AUTRE)) {
					$this->records_ids[] = $record_id;
				}
				if($record_id = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_NOTICE_RESPONSABILITY_SECONDAIRE)) {
					$this->records_ids[] = $record_id;
				}
			}
			$this->records_ids = array_unique($this->records_ids);
		}
		return $this->records_ids;
	}

	/**
	 * Renvoie le tableau des identifiants d'oeuvres utilisant cette autorité comme vedette
	 * @return array
	 */
	public function get_tus_ids() {
		if (!isset($this->tus_ids)) {
			$this->tus_ids = array();
			$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->get_num_object(), $this->get_type_const());
			foreach($vedette_composee_found as $vedette_id){
				if($tu_id = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_TU_RESPONSABILITY)) {
					$this->tus_ids[] = $tu_id;
				}
				if($tu_id = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_TU_RESPONSABILITY_INTERPRETER)) {
					$this->tus_ids[] = $tu_id;
				}
			}
			$this->tus_ids = array_unique($this->tus_ids);
		}
		return $this->tus_ids;
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
			if (empty($this->type_icon)) {
				$this->type_icon = get_url_icon('authorities/'.$auth_type.'_icon.png');
			}
		}
		return $this->type_icon;
	}

	public static function get_indexation_directory($const) {
		$indexation_directory = "";
		switch ($const) {
			case AUT_TABLE_AUTHORS :
				$indexation_directory = "authors";
				break;
			case AUT_TABLE_CATEG :
				$indexation_directory = "categories";
				break;
			case AUT_TABLE_PUBLISHERS :
				$indexation_directory = "publishers";
				break;
			case AUT_TABLE_COLLECTIONS :
				$indexation_directory = "collections";
				break;
			case AUT_TABLE_SUB_COLLECTIONS :
				$indexation_directory = "subcollections";
				break;
			case AUT_TABLE_SERIES :
				$indexation_directory = "series";
				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				$indexation_directory = "titres_uniformes";
				break;
			case AUT_TABLE_INDEXINT :
				$indexation_directory = "indexint";
				break;
			case AUT_TABLE_CONCEPT :
				$indexation_directory = "concepts";
				break;
			case AUT_TABLE_AUTHPERSO :
				$indexation_directory = "authperso";
				break;
		}
		return $indexation_directory;
	}

	public function get_used_in_pperso_authorities() {
		if (!isset($this->used_in_pperso_authorities)) {
	   		$this->used_in_pperso_authorities=aut_pperso::get_used($this->type_object, $this->num_object,$this->table_tempo);
		}
		return $this->used_in_pperso_authorities;
	}

	public function get_used_in_pperso_authorities_ids($prefix) {
		switch($prefix){
			case 'article':$type_object=20;	break;
			case 'section':$type_object=21;	break;
			case 'notices': $type_object=50; break;
			case 'author': $type_object=AUT_TABLE_AUTHORS; break;
			case 'authperso': $type_object=AUT_TABLE_AUTHPERSO;  break;
			case 'categ': $type_object=AUT_TABLE_CATEG; break;
			case 'collection': $type_object=AUT_TABLE_COLLECTIONS; break;
			case 'indexint': $type_object=AUT_TABLE_INDEXINT; break;
			case 'publisher': $type_object=AUT_TABLE_PUBLISHERS; break;
			case 'serie': $type_object=AUT_TABLE_SERIES; break;
			case 'subcollection':  $type_object=AUT_TABLE_SUB_COLLECTIONS; break;
			case 'tu':  $type_object=AUT_TABLE_TITRES_UNIFORMES; break;
			default: return array();
		}

		$ids=array();
		$query= "SELECT distinct id from ".$this->table_tempo." where type_object = '".$type_object."' order by id";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$ids[]=$row->id;
			}
		}
		return $ids;
	}

	public static function get_const_type_object($string_type_object) {
			switch ($string_type_object) {
			    case 'author':
			    case 'authors':
					return AUT_TABLE_AUTHORS;
			    case 'categ':
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
			    case 'sub_collections' :
					return AUT_TABLE_SUB_COLLECTIONS;
			    case 'serie':
			    case 'series':
					return AUT_TABLE_SERIES;
			    case 'tu' :
			    case 'work' :
			    case 'works' :
			    case 'titre_uniforme' :
			    case 'titres_uniformes' :
					return AUT_TABLE_TITRES_UNIFORMES;
				case 'indexint' :
					return AUT_TABLE_INDEXINT;
				case 'concept' :
				case 'concepts' :
				case 'skos' :
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

	public function get_uid() {
		return $this->uid;
	}

	public function get_authority_link(){
		return './autorites.php?categ=see&sub='.$this->get_string_type_object().'&id='.$this->get_num_object();
	}

	public function get_entity_type(){
		return 'authority';
	}

	public function get_caddie() {
		global $msg;
		$cart_click = "onClick=\"openPopUp('./cart.php?object_type=".authorities_caddie::get_type_from_const($this->type_object)."&item=".$this->get_id()."', 'cart')\"";
		$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$this->get_id().", '".authorities_caddie::get_type_from_const($this->get_type_object())."');\" onMouseOut=\"set_flag_info_div(false);\"";
		return "<img src='".get_url_icon("basket_small_20x20.gif")."' class='align_middle' alt='basket' title=\"".$msg[400]."\" $cart_click $cart_over_out>";
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

	public function get_icon_pointe_in_cart() {
		return $this->icon_pointe_in_cart;
	}

	public function set_icon_pointe_in_cart($icon_pointe_in_cart) {
		$this->icon_pointe_in_cart = $icon_pointe_in_cart;
	}

	public function get_icon_del_in_cart() {
		return $this->icon_del_in_cart;
	}

	public function set_icon_del_in_cart($icon_del_in_cart) {
		$this->icon_del_in_cart = $icon_del_in_cart;
	}

	public static function prefix_var_tree($tree,$prefix){
		for($i=0 ; $i<count($tree) ; $i++){
			$tree[$i]['var'] = $prefix.".".$tree[$i]['var'];
			if(isset($tree[$i]['children']) && $tree[$i]['children']){
				$tree[$i]['children'] = self::prefix_var_tree($tree[$i]['children'],$prefix);
			}
		}
		return $tree;
	}

	public function get_format_data_structure() {
		global $msg;

		$main_fields = array();
		$main_fields[] = array(
				'var' => "id",
				'desc' => $msg['1601']
		);
		$main_fields[] = array(
				'var' => "num_object",
				'desc' => $msg['cms_authority_format_data_db_id']
		);
		$main_fields[] = array(
				'var' => "statut",
				'desc' => $msg['authorities_statut_label']
		);
		$main_fields[] = array(
				'var' => "thumbnail_url",
				'desc' => $msg['notice_thumbnail_url']
		);
// 		$main_fields[] = array(
// 				'var' => "thumbnail",
// 				'desc' => $msg['']
// 		);
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
				global $num_page;
				$frbr_page = new frbr_page($num_page);
				$parametres_perso = new custom_parametres_perso("authperso","authperso", $frbr_page->get_parameter_value('authperso'));
				break;
			default :
				$parametres_perso = new parametres_perso($type_object);
				break;
		}
		$main_fields[] = array(
				'var' => "customs",
				'desc' => $msg['authority_champs_perso'],
				'children' => authority::prefix_var_tree($parametres_perso->get_format_data_structure(),"customs")
		);
		$main_fields[] = array(
				'var' => "concepts",
				'desc' => $msg['ontology_skos_concept'],
				'children' => authority::prefix_var_tree(skos_concept::get_format_data_structure(),"concepts[i]")
		);

		//TODO Autorités liées
		//TODO Notices liées

		return $main_fields;
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
				$parametres_perso = new custom_parametres_perso("authperso","authperso", $this->get_object_instance()->info['authperso_num']);
				break;
			default :
				$parametres_perso = new parametres_perso($type_object);
				break;
		}
		$formatted_data['customs'] = $parametres_perso->get_out_values($this->get_num_object());

		$skos_concept = new skos_concept($this->get_num_object());
		$formatted_data['concepts'] = $skos_concept->format_datas();

		//TODO Autorités liées
		//TODO Notices liées

		return $formatted_data;
	}

	public static function update_records_index($query, $datatype = 'all') {
		global $include_path;

		$notices_ids = array();
		$found = pmb_mysql_query($query);
		while (($mesNotices = pmb_mysql_fetch_object($found))) {
			$notices_ids[] = $mesNotices->notice_id;
		}
		if (count($notices_ids)) {
			foreach ($notices_ids as $notice_id) {
				indexation_stack::push($notice_id, TYPE_NOTICE, $datatype);
			}
		}
	}

	public function get_isbd() {
		if (!empty($this->isbd)) {
			return $this->isbd;
		}

		if (empty($this->get_object_instance())) {
		    return '';
		}

		$template_path = $this->get_isbd_template();
		if (!empty($template_path)) {
			$h2o = H2o_collection::get_instance($template_path);
			$isbd = $h2o->render(array('authority' => $this));
			$this->isbd =  trim(str_replace(array("\n", "\t", "\r"), '', strip_tags($isbd)));
		} else {
    		$this->isbd = $this->get_object_instance()->get_isbd();
		}
		return $this->isbd;
	}

	public function get_isbd_template() {
	    $template_path = $this->find_template("isbd");
	    if(false === $template_path) {
	        return '';
	    }
	    return $template_path;
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

	protected function get_hidden_values_already_exist() {
		$hidden_values = '';
		//champs perso
		$param_perso = new parametres_perso($this->get_prefix_for_pperso());
		foreach($param_perso->get_t_fields() as $field) {
			$hidden_values .= $this->put_global_in_hidden_field($field['NAME']);
		}
		return $hidden_values;
	}

	public function get_display_forcing_button($label='') {
		global $charset;
		return "<input type='submit' class='bouton' id='forcing_button' value='".htmlentities($label, ENT_QUOTES, $charset)."'/>";
	}

	public function get_display_authority_already_exist($error_title, $error_message, $values=array()) {
		global $current_module, $charset;

		$display = "<form class='form-".$current_module."' id='forcing_authority_already_exist' name='forcing_authority_already_exist' method='post' action='!!action!!' enctype='multipart/form-data'>
		    <div class='row'>
				<img src='".get_url_icon('error.gif')."'>
		        <strong>".htmlentities($error_title, ENT_QUOTES, $charset)."</strong>
		        <br/>
		        ".htmlentities($error_message, ENT_QUOTES, $charset)."
		    </div>
		    <div class='row'>
		        ".$this->get_hidden_values_already_exist()."
				!!hidden_specific_values!!
				<input type='hidden' id='forcing_values' name='forcing_values' value='".encoding_normalize::json_encode($values)."'/>
		        !!forcing_button!!
		    </div>";
		$this->init_autlink_class();
		$display .= "
		    <div class='row'>
                ".$this->autlink_class->get_hidden_values_already_exist()."
            </div>";
		$display .= "</form>";
		return $display;
	}

	public function put_global_in_hidden_field($global_name) {
		global ${$global_name};
		$global_var = ${$global_name};
		$hidden_global_field = $this->create_hidden_field($global_name, $global_var);
		return $hidden_global_field;
	}

	public function create_hidden_field($name, $var) {
		global $charset;

		$html = "";
		if (is_array($var)) {
			foreach($var as $key => $value) {
				$html .= $this->create_hidden_field($name."[".$key."]", $value);
			}
		} else {
			$html .= "<input type='hidden' name='".$name."' value='" . htmlentities(stripslashes($var), ENT_QUOTES, $charset) . "'/>";
		}
		return $html;
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

	/**
	 * Retourne le type de vedette selon le type
	 */
	public function get_vedette_class(){
		$this->get_vedette_type();
		switch ($this->vedette_type) {
			case TYPE_AUTHOR :
				return 'vedette_authors';
			case TYPE_CATEGORY :
				return 'vedette_categories';
			case TYPE_PUBLISHER :
				return 'vedette_publishers';
			case TYPE_COLLECTION :
				return 'vedette_collections';
			case TYPE_SUBCOLLECTION :
				return 'vedette_subcollections';
			case TYPE_SERIE :
				return 'vedette_series';
			case TYPE_TITRE_UNIFORME :
				return 'vedette_titres_uniformes';
			case TYPE_INDEXINT :
				return 'vedette_indexint';
			case TYPE_CONCEPT_PREFLABEL:
				return 'vedette_concepts';
			case TYPE_AUTHPERSO :
				return 'vedette_authpersos';
		}
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

	public function get_detail_tooltip($target_node_id) {
		$html = '
		<script type="text/javascript">
			require(["dijit/Tooltip", "dojo/dom", "dojo/on", "dojo/mouse", "dojo/domReady!"], function(Tooltip, dom, on, mouse) {
				var node = dom.byId("'.$target_node_id.'");
				on(node, mouse.enter, function(){
					Tooltip.show("'.addslashes(str_replace(array("\n", "\t", "\r"), '', $this->get_detail())).'", node);
					on.once(node, mouse.leave, function(){
						Tooltip.hide(node);
					});
				});
			})
		</script>';
		return $html;
	}

	public function get_linked_concepts_id() {
		$index_concept = new index_concept($this->num_object, $this->get_vedette_type());
		return $index_concept->get_concepts_id();
	}

	public function get_linked_entities_id($type, $property = '', $arguments = array()) {
		$entities_linked = array();
		switch ($type) {
			case TYPE_NOTICE :
				if ($property) {
					$linked_records_id = $this->look_for_attribute_in_class($this->get_object_instance(), $property, $arguments);
					if (is_array($linked_records_id)) {
						foreach ($linked_records_id as $id) {
							$entities_linked[]= array(
									'id' => $id,
									'link_type' => array(),
							);
						}
					}
				}
				break;
			case TYPE_CONCEPT :
				$concepts_id = $this->get_linked_concepts_id();
				foreach ($concepts_id as $id) {
					$entities_linked[]= array(
							'id' => authority::get_authority_id_from_entity($id, AUT_TABLE_CONCEPT),
							'link_type' => array(),
					);
				}
				break;
			default :
				if ($property) {
					$entities_id = $this->look_for_attribute_in_class($this->get_object_instance(), $property, $arguments);
					if (is_array($entities_id)) {//plusieurs entites liees
						foreach ($entities_id as $entity) {
							if (is_array($entity)) {
								$entities_linked[]= array(
										'id' => authority::get_authority_id_from_entity($entity['id'], static::$type_table[$type]),
										'link_type' => $entity['link_type'],
								);
							} else {
								$entities_linked[]= array(
										'id' => authority::get_authority_id_from_entity($entity, static::$type_table[$type]),
										'link_type' => array(),
								);
							}
						}
					} elseif ($entities_id) { //une seule entite liee
						$entities_linked[]= array(
								'id' => authority::get_authority_id_from_entity($entities_id, static::$type_table[$type]),
								'link_type' => array(),
						);
					}
				}
				break;
		}
		return $entities_linked;
	}

	public static function get_properties($type, $prefix){
		if(!isset(self::$properties[$type])){
			static::$properties[$type] = array();
			$authority_props = array_keys(get_class_vars('authority'));

			$sub_class = static::get_class_name_from_type($type);
			$sub_class_props = array_keys(get_class_vars($sub_class));


			$authority_methods = get_class_methods('authority');
			$sub_class_methods = get_class_methods($sub_class);

			$authority_methods = static::get_getters($authority_methods);
			$sub_class_methods = static::get_getters($sub_class_methods);
			$properties = array_unique(array_merge($authority_props, $sub_class_props, $authority_methods, $sub_class_methods));
			sort($properties);
			$final_properties = array();
			foreach($properties as $property){
				/**
				 * TODO: ajouter un message cohérent en fonction de la propriété
				 */
				if($property != "properties"){
					$final_properties[] = array(
							'var' => $prefix.'.'.$property,
							'desc' => 'aut_'.$property
					);
					if($property == "p_perso"){
						$custom_fields = static::get_opac_displayable_custom_fields($type);
						$custom_fields_props = array();

						foreach($custom_fields as $field){
							$custom_fields_props[] = array(
									'var' => $prefix.'.'.$property.'.'.$field['NAME'],
									'desc' => $field['TITRE']
							);
						}
						$final_properties[count($final_properties)-1]['children'] = $custom_fields_props;
					}
				}
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

	public static function get_opac_displayable_custom_fields($type){
		if (!isset(static::$custom_fields[$type])) {
			static::$custom_fields[$type] = array();
			$prefix = static::aut_const_to_string($type);
			if($prefix == "titre_uniforme"){
				$prefix = "tu";
			}else if($prefix == "category"){
				$prefix = "categ";
			}
			$parametres_perso = new parametres_perso($prefix);
			$fields = $parametres_perso->get_t_fields();
			foreach($fields as $field){
				if($field['OPAC_SHOW']){
					static::$custom_fields[$type][] = $field;
				}
			}
		}
		return static::$custom_fields[$type];
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
				return 'authperso_authority';
			default :
				return '';
		}
	}

	public static function get_url_from_type($type) {
	    switch (self::get_const_type_object($type)){
	        case AUT_TABLE_AUTHORS:
	            return LIEN_AUTEUR;
	        case AUT_TABLE_CATEG :
	            return LIEN_CATEG;
	        case AUT_TABLE_PUBLISHERS :
	            return LIEN_EDITEUR;
	        case AUT_TABLE_COLLECTIONS :
	            return LIEN_COLLECTION;
	        case AUT_TABLE_SUB_COLLECTIONS :
	            return LIEN_SUBCOLLECTION;
	        case AUT_TABLE_SERIES :
	            return LIEN_SERIE;
	        case AUT_TABLE_TITRES_UNIFORMES :
	            return LIEN_TITRE_UNIFORM;
	        case AUT_TABLE_INDEXINT :
	            return LIEN_INDEXINT;
	        case AUT_TABLE_CONCEPT :
	        case AUT_TABLE_INDEX_CONCEPT :
	            return LIEN_CONCEPT;
	        case AUT_TABLE_AUTHPERSO :
	            return LIEN_AUTHPERSO;
	        default:
	            return "";
	    }
	}

	public static function check_available_autority($id, $type){
	    if (AUT_TABLE_AUTHPERSO == $type) {
            $query = "
                SELECT * from authperso_authorities,authperso
                WHERE id_authperso=authperso_authority_authperso_num
                AND id_authperso_authority=".$id;
	    } else {
    	    $query = "
                SELECT id_authority
                FROM authorities
                JOIN authorities_statuts
                ON authorities_statuts.id_authorities_statut = authorities.num_statut
                WHERE num_object=" . $id . "
                AND type_object=". $type;
	    }

	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        return true;
	    }
        return false;
	}

	public function get_ark_link() {
	    if (empty($this->ark_link)) {
	        global $pmb_ark_activate;
	        if ($pmb_ark_activate) {
	            $arkEntity = new ArkAuthority(intval($this->id));
	            $ark = ArkModel::getArkFromEntity($arkEntity);
	            $this->ark_link = $ark->getArkLink();
	        }
	    }
	    return $this->ark_link;
	}

	public function get_permalink() {
	    if (!empty($this->get_ark_link())) {
	        return $this->get_ark_link();
	    }
	    global $pmb_opac_url;
	    if ($this->num_object) {
	        $type_see = "";
            switch ($this->type_object) {
                case AUT_TABLE_AUTHORS :
                    $type_see = "author_see";
                    break;
                case AUT_TABLE_CATEG :
                     $type_see = "categ_see";
                    break;
                case AUT_TABLE_COLLECTIONS :
                     $type_see = "coll_see";
                    break;
                case AUT_TABLE_CONCEPT :
                     $type_see = "concept_see";
                    break;
                case AUT_TABLE_INDEXINT :
                     $type_see = "indexint_see";
                    break;
                case AUT_TABLE_PUBLISHERS :
                     $type_see = "publisher_see";
                    break;
                case AUT_TABLE_SERIES :
                     $type_see = "serie_see";
                    break;
                case AUT_TABLE_SUB_COLLECTIONS :
                     $type_see = "subcoll_see";
                    break;
                case AUT_TABLE_TITRES_UNIFORMES :
                     $type_see = "titre_uniforme_see";
                    break;
            }
            if ($type_see) {
                return $pmb_opac_url.'index.php?lvl='.$type_see.'&id='.$this->num_object;
            }
	    }
	    return "";
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
				$query = "SELECT 1 FROM authperso_authorities WHERE id_authperso_authority = " . intval($this->num_object);
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