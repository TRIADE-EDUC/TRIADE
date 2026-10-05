<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_item.class.php,v 1.87.2.1.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Library\CSRF\CollectionCSRF;

require_once $class_path.'/onto/common/onto_common_class.class.php';
require_once $class_path.'/onto/onto_assertion.class.php';
require_once($include_path.'/templates/onto/common/onto_common_item.tpl.php');
require_once $class_path.'/onto/common/onto_common_datatype.class.php';


/**
 * class onto_common_item
 *
 */
class onto_common_item {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 *
	 * @access protected
	 * @var onto_common_class
	 */
	public $onto_class;

	/**
	 *
	 * @access private
	 */
	protected $uri;

	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;

	/**
	 * Tableau associatif de tableau des valeurs avec les URIs de propriétés comme
	 * étiquettes.
	 * @access private
	 */
	protected $datatypes;


	/**
	 * Tableau d'erreurs de check_values
	 * @access private
	 */
	protected $checking_errors;


	/**
	 * Parametrage du framework
	 */
	protected $framework_params;

	/**
	 * Tableau de données complémentaires
	 * @var array
	 */
	protected $additionnal_data = [];

	/**
	 *
	 *
	 * @param onto_common_class onto_class

	 * @param string uri

	 * @return void
	 * @access public
	 */
	public function __construct($onto_class, $uri) {
		$this->onto_class=$onto_class;
		$this->uri=$uri;
		if(!$this->uri){
			//pas d'uri, on prend une temporaire
			$this->uri = onto_common_uri::get_temp_uri($this->onto_class->uri);
		}
	} // end of member function __construct


	protected function order_datatypes(){
		//on ordonne les datatypes
		$temp_datatype_tab=array();
		if (!empty($this->datatypes)) {
			foreach($this->datatypes as $key=>$datatypes){
				foreach($datatypes as $datatype){
					$temp_datatype_tab[$key][$datatype->get_datatype_ui_class_name()][]=$datatype;
				}
			}
		}
		return $temp_datatype_tab;
	}

	public function replace_temp_uri(){
		global $opac_url_base;
		if(onto_common_uri::is_temp_uri($this->uri)){
		    //TODO traiter les URI relatives
		    $base_url = $opac_url_base;
		    if(strpos($opac_url_base,"http") !== 0){
		        $base_url = "http://www.pmbservices.fr/";
		    }
		    $this->uri = onto_common_uri::replace_temp_uri($this->uri,$this->onto_class->uri,$base_url.$this->get_onto_class()->get_ontology()->name."/".$this->get_onto_class_pmb_name()."#");
		}
	}

	/**
	 * Appel les fonctions static get_form et articule le formulaire de l'item courant
	 *
	 * on itère sur les propriétés de l'onto_class, on envoi aussi le datatype si présent
	 *
	 * @param string $prefix_url  Préfixe de l'url de soumission du formulaire
	 * @param string $flag  Nom du flag à utiliser pour limiter aux champs concernés
	 *
	 * @return string
	 * @access public
	 */
	public function get_form($prefix_url="",$flag="",$action="save") {
		global $msg,$charset,$ontology_tpl;
		$temp_datatype_tab=$this->order_datatypes();
		$form=$ontology_tpl['form_body'];
		$form=str_replace("!!uri!!",$this->uri,$form);
		
		$form_script = $ontology_tpl['form_scripts'];
		if(strpos($form_script, '!!tokens_csrf!!') !== false) {
    		$collectionCSRF = new CollectionCSRF();
    		$tokens = json_encode($collectionCSRF->getArrayTokens());
    		
    		$form_script = str_replace('!!tokens_csrf!!', $tokens, $form_script);
		}
		
		$form=str_replace("!!onto_form_scripts!!",$form_script, $form);
		$form=str_replace("!!caller!!",rawurlencode(onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name)), $form);

		$form=str_replace("!!onto_form_id!!",onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name) , $form);
		$form=str_replace("!!onto_form_action!!",$prefix_url."&action=".$action, $form);
		$form=str_replace("!!onto_form_title!!",htmlentities($this->onto_class->label ,ENT_QUOTES,$charset) , $form);

		$content='';
		$valid_js = "";
		if (!empty($this->onto_class->get_properties())) {
			$index = 0;
			foreach($this->onto_class->get_properties() as $uri_property){
				$property=$this->onto_class->get_property($uri_property);
				$property->set_framework_params($this->framework_params);

				if((!$flag || (in_array($flag,$property->flags))) && (!$property->is_undisplayed())) {
					$datatype_class_name =$this->resolve_datatype_class_name($property);
					$datatype_ui_class_name=$this->resolve_datatype_ui_class_name($datatype_class_name,$property,$this->onto_class->get_restriction($uri_property));

					$temp_class_name = isset($temp_datatype_tab[$uri_property][$datatype_ui_class_name]) ? $temp_datatype_tab[$uri_property][$datatype_ui_class_name] : null;
					// On encapsule dans des divs movables pour l'édition de la grille de saisie
					$movable_div = $ontology_tpl['form_movable_div'];
					$movable_div = str_replace('!!movable_index!!', $index, $movable_div);
					$movable_div = str_replace('!!movable_property_label!!', htmlentities($property->label, ENT_QUOTES, $charset), $movable_div);
					$movable_div = str_replace('!!datatype_ui_form!!', $datatype_ui_class_name::get_form($this->uri,$property,$this->onto_class->get_restriction($uri_property),$temp_class_name,onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name),$flag), $movable_div);
					$movable_div = str_replace('!!form_row_content_mandatory_sign!!', '', $movable_div);
					$movable_div = str_replace('!!form_row_content_mandatory_class!!', '', $movable_div);
					$movable_div = str_replace('!!property_name!!', $property->pmb_name, $movable_div);

					$content .= $movable_div;
					if($valid_js){
						$valid_js.= ",";
					}
					$valid_js.= $datatype_ui_class_name::get_validation_js($this->uri,$property,$this->onto_class->get_restriction($uri_property),$temp_class_name,onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name),$flag);
					$index++;
				}
			}
		}
		if (substr_count(onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name), "conceptscheme")) {
			$form = str_replace("!!onto_form_save_and_create_concept!!",
					'<input type="button" class="bouton" id="btsubmit_create_concept" onclick=" submit_onto_form(true);"
					value="'.htmlentities($msg['save_and_continue_concept'],ENT_QUOTES,$charset).'"/>', $form);
		} else {
			$form = str_replace("!!onto_form_save_and_create_concept!!", '', $form);
		}
		$form=str_replace("!!onto_form_content!!",$content , $form);

		$form=str_replace("!!onto_form_submit!!",'<input type="button" class="bouton" id="btsubmit" onclick="submit_onto_form();" value="'.htmlentities($msg['77'],ENT_QUOTES,$charset).'"/>' , $form);
		$form=str_replace("!!onto_form_submit_continue!!",'<input type="button" class="bouton" id="btsubmit_continue" onclick="submit_onto_form(true);" value="'.htmlentities($msg['save_and_continue'],ENT_QUOTES,$charset).'"/>' , $form);
		$form=str_replace("!!onto_form_history!!",'<input type="button" class="bouton" id="btcancel" onclick="history.go(-1);" value="'.htmlentities($msg['76'],ENT_QUOTES,$charset).'"/>' , $form);

		if(!onto_common_uri::is_temp_uri($this->uri)){
			$script="
					function confirmation_delete() {
        				result = confirm(\"".$msg['confirm_suppr'] ."?\");
        				if(result) document.location = \"$prefix_url\"+'&action=confirm_delete';
   				}";
			$form=str_replace("!!onto_form_del_script!!",$script , $form);
			$form=str_replace("!!onto_form_delete!!",'<input type="button" class="bouton" onclick=\'confirmation_delete();\' value="'.htmlentities($msg['63'],ENT_QUOTES,$charset).'"/>	' , $form);
		}else{
			$form=str_replace("!!onto_form_del_script!!",'' , $form);
			$form=str_replace("!!onto_form_delete!!",'' , $form);
		}
		$form = str_replace('!!document_title!!', addslashes($this->onto_class->label), $form);

		$valid_js = "var validations = [".$valid_js."];";
		$form=str_replace("!!onto_datasource_validation!!",$valid_js , $form);
		$form=str_replace("!!onto_form_name!!",onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name) , $form);

		return $form;
	} // end of member function get_form

	/**
	 * Appel les fonctions static get_display et articule l'affichage de l'item courant
	 *
	 * Ici on itère sur les datatypes de l'item, à la difference du form, ou on itère sur les propriétés de l'onto_class
	 *
	 * @return string
	 * @access public
	 */
	public function get_display() {
		$display='<div id="'.$this->uri.'">';
		$display.='<h1>'.$this->onto_class->label.'</h1>';
		if (!empty($this->datatypes)) {
			foreach($this->datatypes as $key=>$datatypes){

				$temp_datatype_tab=array();
				foreach($datatypes as $datatype){
					$temp_datatype_tab[$datatype->get_datatype_ui_class_name()][]=$datatype;
				}

				foreach($temp_datatype_tab as $class_name=>$datatype_tab){
					$display.=$class_name::get_display($datatype_tab,$this->onto_class->get_property($key),$this->uri);
				}
			}
		}
		$display.='</div>';
		return $display;
	} // end of member function get_display


	/**
	 * Instancie les datatypes à partir des données postées du formulaire
	 *
	 * @return void
	 * @access public
	 */
	public function get_values_from_form() {
		$this->datatypes = array();
		$prefix = onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name);

		if (!empty($this->onto_class->get_properties())) {
			foreach($this->onto_class->get_properties() as $uri_property){
				$property=$this->onto_class->get_property($uri_property);
				$property->set_framework_params($this->framework_params);
				$datatype_class_name = $this->resolve_datatype_class_name($property);
				$this->datatypes = array_merge($this->datatypes, $datatype_class_name::get_values_from_form($prefix, $property, $this->uri));
			}
		}
		return $this->datatypes;
	} // end of member function get_values_from_form

	/**
	 * Instancie les datatypes à partir des triplets du store
	 *
	 * @param onto_assertion assertions Tableau des déclarations à associer à l'instance

	 * @return void
	 * @access public
	 */
	public function set_assertions($assertions) {
		/* @var $assertion onto_assertion */

		foreach ($assertions as $assertion) {
			$range = $this->onto_class->get_property_range($assertion->get_predicate());
			if (count($range) && (in_array($assertion->get_object_type(), $range) || $assertion->get_object_type() == "http://www.w3.org/2000/01/rdf-schema#range") ) {
				$property = $this->onto_class->get_property($assertion->get_predicate());
				$datatype_class_name=$this->resolve_datatype_class_name($property);

				$datatype=new $datatype_class_name($assertion->get_object(), $assertion->get_object_type(), $assertion->get_object_properties());
				$datatype_ui_class_name=$this->resolve_datatype_ui_class_name($datatype_class_name,$property,$this->onto_class->get_restriction($assertion->get_predicate()));
				$datatype->set_datatype_ui_class_name($datatype_ui_class_name,$this->onto_class->get_restriction($assertion->get_predicate()));
				$this->datatypes[$assertion->get_predicate()][]=$datatype;
			}
		}
		return true;
	} // end of member function set_assertions

	/**
	 * Renvoie un tableau des déclarations associées à l'instance
	 *
	 * @return onto_assertion
	 * @access public
	 */
	public function get_assertions() {
		$assertions = array();

		// On construit manuellement l'assertion type
		$assertions[] = new onto_assertion($this->uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $this->onto_class->uri, "", array('type'=>"uri"));
		foreach ($this->datatypes as $property => $datatypes) {
			/* @var $datatype onto_common_datatype */
			foreach ($datatypes as $datatype) {
				$assertions[] = new onto_assertion($this->uri, $property, $datatype->get_raw_value(), $datatype->get_value_type(), $datatype->get_value_properties());
				if($this->onto_class->get_property($property)->inverse_of){
					$assertions[] = new onto_assertion($datatype->get_raw_value(), $this->onto_class->get_property($property)->inverse_of->uri, $this->uri, $this->onto_class->uri);
				}
			}
		}
		return $assertions;
	} // end of member function get_assertions

	/**
	 * Tableau de tableau des valeurs à afficher par URI de propriété
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_formated_values() {
		$array = array();
		foreach ($this->datatypes as $property => $datatypes) {
			/* @var $datatype onto_common_datatype */
			foreach ($datatypes as $datatype) {
				$array[$property][] = $datatype->get_formated_value();
			}
		}
		return $array;
	} // end of member function get_formated_values

	/**
	 * Vérifie l'intégrité d'un item (pour l'item, appel également  les check_value de
	 * ses datatypes)
	 *
	 * @return bool
	 * @access public
	 */
	public function check_values() {
		$this->checking_errors = array();
		$valid = true;
		foreach($this->datatypes as $property_uri => $datatypes){
			$restriction = $this->onto_class->get_restriction($property_uri);
			$distincts = $restriction->get_distinct();
			$nb_values[$property_uri] = array();
			/* @var $datatype onto_common_datatype */
			foreach($datatypes as $datatype){
				//on commence par vérifier les valeurs des datatypes...
				if(!$datatype->check_value()){
					$this->checking_errors[$property_uri]['type'] = $datatype->get_error_type();
					$this->checking_errors[$property_uri]['error'] = get_class($datatype);
					$this->checking_errors[$property_uri]['message'] = $datatype->get_error_message();
					$valid = false;
					break;
				}
				// On vérifie ques les valeurs qui doivent être distinctes le sont bien...
				if(is_array($distincts) && count($distincts)){
					foreach($distincts as $distinct_uri => $distinct_property){
						if(isset($this->datatypes[$distinct_uri])){
							foreach($this->datatypes[$distinct_uri] as $distinct_datatype){
								if($datatype->get_value() == $distinct_datatype->get_value()){
									$valid = false;
									$this->checking_errors[$property_uri]['type'] = "must be distinct";
									$this->checking_errors[$property_uri]['error'] = $distinct_uri;
									break;
								}
							}
						}
					}
				}
				//comptage des valeurs pour les cardinalités
				if($datatype->get_value() != ""){
					$lang = $datatype->get_lang();
					if ($lang) {
						if (empty($nb_values[$property_uri][$lang])) {
							$nb_values[$property_uri][$lang] = 0;
						}
						$nb_values[$property_uri][$lang]++;
					} else {
						if (empty($nb_values[$property_uri]['default'])) {
							$nb_values[$property_uri]['default'] = 0;
						}
						$nb_values[$property_uri]['default']++;
					}
				}
			}
		}

		foreach ($this->onto_class->get_properties() as $property_uri) {
		    if(!empty($this->checking_errors[$property_uri])){
		        // On a déjà un souci plus haut dans le test, on ne s'embete pas plus...
		        continue;
		    }
			$restriction = $this->onto_class->get_restriction($property_uri);
			$min = $restriction->get_min();
			$max = $restriction->get_max();

			// comptage des valeurs en prenant en compte les langues
			if ( !empty($nb_values[$property_uri]) && is_array($nb_values[$property_uri]) ) {
				$strict_nb_value = max($nb_values[$property_uri]);
			} else {
				$strict_nb_value = 0;
			}

			//check cardinalité max
			if($max != -1 && $max < $strict_nb_value){
				$this->checking_errors[$property_uri]['type'] = "card";
				$this->checking_errors[$property_uri]['error'] = "too much values";
				$valid = false;
				break;
			}

			//check cardinalité min
			if ($min > $strict_nb_value) {
				$this->checking_errors[$property_uri]['type'] = "card";
				$this->checking_errors[$property_uri]['error'] = "no minima";
				$valid = false;
				break;
			}
		}
		return $valid;
	} // end of member function check_values

	public function get_checking_errors(){
		return $this->checking_errors;
	}


	/**
	 *
	 *
	 * @return bool
	 * @access private
	 */
	private function order_datas() {

	} // end of member function order_datas

	public function get_uri() {
		return $this->uri;
	}

	/**
	 *
	 * Renvoi le nom de la class datatype_class_name à utiliser pour le datatype
	 *
	 * @return string
	 */
	public function resolve_datatype_class_name($property){
		return self::search_datatype_class_name($property,$this->onto_class->pmb_name,$this->onto_class->onto_name);
	}

	public static function search_datatype_class_exists($class_name,$onto_name='common'){
	    global $class_path;
	    
	    if (file_exists($class_path."/onto/".$onto_name."/".$class_name.".class.php") && class_exists($class_name)) {
	        return true;
	    }
	    return false;
	}
	
	/**
	 *
	 * Renvoi le nom de classe de datatype à utiliser pour une uri de pmb:datatype
	 *
	 * @param onto_common_property $property
	 * @param string $pmb_datatype
	 */
	public static function search_datatype_class_name($property,$owl_class_name,$onto_name='common'){
		$suffix = substr($property->pmb_datatype,strpos($property->pmb_datatype,"#")+1);

		//on regarde le cas propriété pour la classe de l'ontologie...
		//(ex : onto_skos_concept_datatype_preflabel)
		$class_name = "onto_".$onto_name."_".$owl_class_name."_datatype_".$property->pmb_name;

		if(!static::search_datatype_class_exists($class_name, $onto_name)){
			//loupé, on regarde si la proprité est définie pour l'ontologie
			//(ex : onto_skos_datatype_preflabel)
			$class_name = "onto_".$onto_name."_datatype_".$property->pmb_name;
			if(!static::search_datatype_class_exists($class_name, $onto_name)){
				//loupé, on regarde si le pmb_datatype est dérivé pour la classe
				//(ex : onto_skos_concept_datatype_preflabel)
				$class_name = "onto_".$onto_name."_".$owl_class_name."_datatype_".$suffix;

				if(!static::search_datatype_class_exists($class_name, $onto_name)){
					//loupé, on regarde si le pmb_datatype est dérivé pour l'ontologie
					//(ex : onto_skos_concept_datatype_preflabel)
					$class_name = "onto_".$onto_name."_datatype_".$suffix;
					if(!static::search_datatype_class_exists($class_name, $onto_name)){
						//loupé, heu là on regarde dans le common...
						if($onto_name == "common"){
							//le datatype n'est pas codé...on renvoit un datatype ultra basique...
							$class_name = "onto_common_datatype_small_text";
						}else{
							$class_name = self::search_datatype_class_name($property,$owl_class_name);
						}
					}
				}
			}
		}
		return $class_name;
	}

	/**
	 *
	 * Renvoi le nom de la class datatype_class_name à utiliser pour le datatype
	 *
	 * @return string
	 */
	public function resolve_datatype_ui_class_name($datatype_class_name,$property,$restriction=NULL){
		return self::search_datatype_ui_class_name($datatype_class_name,$this->onto_class->pmb_name, $property, $restriction, $property->onto_name);
	}

	/**
	 *
	 * Renvoi le nom de classe de datatype à utiliser pour une uri de pmb:datatype
	 *
	 * @param onto_common_property $property
	 * @param string $pmb_datatype
	 */
	public static function search_datatype_ui_class_name($datatype_class_name, $owl_class_name, $property,$restriction=NULL,$onto_name='common'){
		$pmb_datatype = substr($property->pmb_datatype,strpos($property->pmb_datatype,"#")+1);
		$suffix = "_ui";
		$pmb_datatype_suffix = $suffix;
		if ($restriction && $restriction->get_max() != -1) {
			$pmb_datatype_suffix = "_card_ui";
		}

		//(ex : onto_skos_concept_datatype_preflabel_ui)
		$class_name = "onto_".$onto_name."_".$owl_class_name."_datatype_".$property->pmb_name.$suffix;
		if(!class_exists($class_name)){
			//loupé, on regarde si la proprité est définie pour l'ontologie
			//(ex : onto_skos_datatype_preflabel)
			$class_name = "onto_".$onto_name."_datatype_".$property->pmb_name.$suffix;
			if(!class_exists($class_name)){
				//loupé, on regarde si le pmb_datatype est dérivé pour la classe
				//(ex : onto_skos_concept_datatype_preflabel)
				$class_name = "onto_".$onto_name."_".$owl_class_name."_datatype_".$pmb_datatype.$pmb_datatype_suffix;
				if(!class_exists($class_name)){
					//loupé, on regarde si le pmb_datatype est dérivé pour l'ontologie
					//(ex : onto_skos_concept_datatype_preflabel)
					$class_name = "onto_".$onto_name."_datatype_".$pmb_datatype.$pmb_datatype_suffix;
					if(!class_exists($class_name)){
						$class_name = "onto_".$onto_name."_datatype_".$pmb_datatype."_ui";
						if(!class_exists($class_name)){
							//loupé, heu là on regarde dans le common...
						    if($onto_name == "common"){
						        $class_name = "onto_common_datatype_small_text_ui";
						        if(class_exists("onto_".$onto_name."_datatype_".$pmb_datatype."_ui")){
						            $class_name = "onto_".$onto_name."_datatype_".$pmb_datatype."_ui";
						        }
							}else{
								$class_name = self::search_datatype_ui_class_name($datatype_class_name, $owl_class_name, $property,$restriction);
							}
						}
					}
				}
			}
		}

		return $class_name;
	}


	public function get_label($uri_property){
	    global $lang;
	    $values = $this->datatypes[$uri_property] ?? [];
	    $label = "";
	    $default_label = "";
	    if(count($values) == 1){
	        $label = $values[0]->get_formated_value();
	    }else if(count($values) > 1){
	        foreach($values as $value){
	            if($value->offsetget_value_property("lang") == ""){
	                $default_label = $value->get_formated_value();
	            }
	            if(!$default_label){
	                $default_label = $value->get_formated_value();
	            }
	            if($value->offsetget_value_property("lang") == substr($lang,0,2)){
	                $label = $value->get_formated_value();
	            }
	        }
	        if(!$label) $label = $default_label;
	    }
	    return $label;
	}

	public function get_id() {
		if (isset($this->id)) {
			return $this->id;
		}
		$this->id = onto_common_uri::get_id($this->uri);
		return $this->id;
	}

	public function get_framework_params(){
		return $this->framework_params;
	}

	public function set_framework_params($framework_params){
		if(!isset($this->framework_params)){
			$this->framework_params = $framework_params;
		}
	}

	public function set_uri($uri){
		$this->uri = $uri;
		$this->id = onto_common_uri::get_id($this->uri);
		return $this;
	}

	public function get_onto_class(){
		return $this->onto_class;
	}

	public function get_onto_class_pmb_name() {
	    return $this->onto_class->pmb_name;
	}

	public function set_additionnal_data($additionnal_data) {
	    $this->additionnal_data = $additionnal_data;
	}

	public function add_additionnal_data($key, $value = "") {
	    $this->additionnal_data[$key] = $value;
	}

	public function get_additionnal_data() {
	    return $this->additionnal_data;
	}
} // end of onto_common_item
