<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource.class.php,v 1.57.4.2 2025/04/29 10:34:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/docwatch_root.class.php");
require_once($class_path."/docwatch/docwatch_item.class.php");
require_once($class_path."/docwatch/docwatch_category.class.php");
require_once($class_path."/docwatch/docwatch_watches.class.php");

/**
 * class docwatch_datasource
 *
 */
class docwatch_datasource extends docwatch_root{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Titre de la veille
	 * @access protected
	 */
	protected $title;

	/**
	 * Identifiant de la source en base
	 * @access protected
	 */
	protected $id;

	/**
	 * Identifiant du classement
	 * @access protected
	 */
	protected $num_category;

	/**
	 * Category
	 * @access protected
	 */
	protected $category;

	/**
	 * Durée de validité maximale d'un item
	 * @access protected
	 */
	protected $ttl;

	/**
	 * Date de dernier remplissage
	 * @access protected
	 */
	protected $last_date;

	/**
	 * Tableau des propriétés à sauvegarder
	 * @access protected
	 */
	protected $parameters;


	/**
	 * Interêt par défaut
	 * @access protected
	 */
	protected $default_interesting = 0;

	/**
	 * Eliminer le contenu HTML
	 * @access protected
	 */
	protected $clean_html = 1;

	/**
	 * Expression booléènne
	 * @access protected
	 */
	protected $boolean_expression = '';

	protected $aq_members;
	protected $selectors;

	/**
	 * Identifiant de la veille liée
	 * @access protected
	 */
	protected $num_watch;

	/**
	 * Indicateur de mise a jour
	 * @access protected
	 */
	protected $is_up_to_date = false;

	protected $duplicate_from_id = 0;

	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
	    $this->id = (int) $id;
		$this->fetch_datas();
		parent::__construct($id);
	} // end of member function __construct

	/**
	 * Récupère la liste des sélecteurs autorisés
	 *
	 * @return array
	 * @access public
	 */
	public function get_available_selectors(){
		return array();
	}

	/**
	 * Récupération des données
	 *
	 * @return void
	 * @access public
	 */
	public function fetch_datas() {
		$this->num_category = 0;
		$this->last_date = "0000-00-00 00:00:00";
		$this->title = "";
		$this->ttl = 0;
		$this->num_watch = 0;
		$this->parameters = array();
		$this->selectors = array();
		if($this->id){
			$query = "select * from docwatch_datasources where id_datasource = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->last_date = $row->datasource_last_date;
				$this->ttl = $row->datasource_ttl;
				$this->title = $row->datasource_title;
				$this->unserialize($row->datasource_parameters);
				$this->num_category = $row->datasource_num_category;
				$this->num_watch = $row->datasource_num_watch;
				$this->default_interesting = $row->datasource_default_interesting;
				$this->clean_html = $row->datasource_clean_html;
				$this->boolean_expression = $row->datasource_boolean_expression;

 				//on va chercher les infos des sélecteurs...
				$query = "select id_selector, selector_type from docwatch_selectors where selector_num_datasource = ".$this->id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->selectors = array();
					while($row=pmb_mysql_fetch_object($result)){
					    $this->selectors[(int) $row->id_selector] = $row->selector_type;
					}
				}
			}
		}
	} // end of member function fetch_datas

	/**
	 * Formulaire de définition de la source
	 *
	 * @return void
	 * @access public
	 */
	public function get_form() {
		global $msg,$charset;

		if(!isset($this->parameters['selector'][0])) $this->parameters['selector'][0]='';
		if(!isset($this->selectors[$this->parameters['selector'][0]])) {
			if(count($this->get_available_selectors()) == 1) {
				$class=array_key_first($this->get_available_selectors());
				$this->selectors[$this->parameters['selector'][0]]=$class;
			} else {
				$this->selectors[$this->parameters['selector'][0]]='';
			}
		}

		$form = "
		<form action='' method='post' data-dojo-type='apps/docwatch/form/Source' name='docwatch_datasource'>
			<h3>".htmlentities($msg['dsi_'.get_class($this)],ENT_QUOTES,$charset)."</h3>
			<div class='form-contenu'>
				".$this->get_form_content()."
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<label>".htmlentities($msg['dsi_docwatch_datasource_selector'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<select id='selector_choice' name='selector_choice[]' data-dojo-type='dijit/form/Select' style='width:200px;'>
						<option value='0'>".htmlentities($msg['dsi_docwatch_datasource_selector_choice'],ENT_QUOTES,$charset)."</option>";
		foreach($this->get_available_selectors() as $class => $label){
			$form.="
						<option value='".$class."' ".($class == $this->selectors[$this->parameters['selector'][0]] ? "selected='selected'" : "").">".htmlentities($label,ENT_QUOTES,$charset)."</option>";
		}
		$selector_href = "";
		if($this->parameters['selector'][0] || (count($this->get_available_selectors()) == 1)){
			$selector_href = "./ajax.php?module=dsi&categ=docwatch&sub=sources&action=get_selector_form&class=".$this->selectors[$this->parameters['selector'][0]];
			if($this->parameters['selector'][0]) {
				$selector_href .= "&id=".$this->parameters['selector'][0];
			}
		}
		$form.= "
					</select>
				</div>
				<div id='selector_content' data-dojo-type='dojox/layout/ContentPane' ".($selector_href!= "" ? "data-dojo-props='preload:true,href:\"".$selector_href."\"'" : "")."></div>
				<div class='row'></div>
			</div>
			<div class='row'>
				<div class='left'>
					<button data-dojo-type='dijit/form/Button' type='submit' >".htmlentities($msg['dsi_docwatch_datasource_submit'],ENT_QUOTES,$charset)."</button>
					".($this->id ? "<button data-dojo-type='dijit/form/Button' id='docwatch_datasource_form_duplicate'>".htmlentities($msg['dsi_docwatch_datasource_duplicate'],ENT_QUOTES,$charset)."</button>" : "")."
				</div>";
		if($this->id){
			$form.="
				<div class='right'>
					<button data-dojo-type='dijit/form/Button' id='docwatch_datasource_form_delete'>".htmlentities($msg['dsi_docwatch_datasource_delete'],ENT_QUOTES,$charset)."</button>
				</div>";
		}
		$form.="
			</div>
		</form>";
		return $form;
	} // end of member function get_form

	public function get_form_content(){
		global $msg,$charset;
		$form = "
		<div class='row'>
			<label for='docwatch_datasource_title'>".htmlentities($msg['dsi_docwatch_datasource_title'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' style='width: 35em;' data-dojo-type='dijit/form/TextBox' required='true' id='docwatch_datasource_title' name='docwatch_datasource_title' value='".htmlentities($this->get_title(),ENT_QUOTES,$charset)."'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='docwatch_datasource_ttl'>".htmlentities($msg['dsi_docwatch_datasource_ttl'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' data-dojo-type='dijit/form/NumberTextBox' required='true' name='docwatch_datasource_ttl' value='".htmlentities($this->get_ttl(),ENT_QUOTES,$charset)."'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='docwatch_datasource_default_interesting'>".htmlentities($msg['dsi_docwatch_datasource_default_interesting'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			".$msg['39']."<input ".($this->get_default_interesting() ? "":"checked='checked'")." type='radio' data-dojo-type='dijit/form/RadioButton' name='docwatch_datasource_default_interesting' value='0' />&nbsp;
			".$msg['40']."<input ".($this->get_default_interesting() ? "checked='checked'":"")." type='radio' data-dojo-type='dijit/form/RadioButton' name='docwatch_datasource_default_interesting' value='1' />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='docwatch_datasource_clean_html'>".htmlentities($msg['dsi_docwatch_datasource_clean_html'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			".$msg['39']."<input ".($this->get_clean_html() ? "":"checked='checked'")." type='radio' data-dojo-type='dijit/form/RadioButton' name='docwatch_datasource_clean_html' value='0' />&nbsp;
			".$msg['40']."<input ".($this->get_clean_html() ? "checked='checked'":"")." type='radio' data-dojo-type='dijit/form/RadioButton' name='docwatch_datasource_clean_html' value='1' />
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='docwatch_datasource_boolean_expression'>".htmlentities($msg['dsi_docwatch_datasource_boolean_expression'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='text' style='width: 35em;' data-dojo-type='dijit/form/TextBox' name='docwatch_datasource_boolean_expression' value=\"".$this->get_boolean_expression()."\" />
		</div>";
		return $form;
	}

	/**
	 * Set les propriétés de l'instance depuis le formulaire
	 *
	 * @return void
	 * @access public
	 */
	public function set_from_form() {
		global $selector_choice;
		global $docwatch_datasource_title;
		global $docwatch_datasource_ttl;
		global $docwatch_datasource_num_category;
		global $docwatch_datasource_default_interesting;
		global $docwatch_datasource_clean_html;
		global $docwatch_datasource_boolean_expression;

		if (is_array($selector_choice) && count($selector_choice)) {
			$this->parameters['selector'] = array();
			foreach ($selector_choice as $selector) {
				$this->parameters['selector'][] = $selector;
			}
		}

		$this->title = strip_tags(stripslashes($docwatch_datasource_title));
		$this->ttl = $docwatch_datasource_ttl;
		$this->category = $docwatch_datasource_num_category;
		$this->default_interesting = $docwatch_datasource_default_interesting;
		$this->clean_html = $docwatch_datasource_clean_html;
		$this->boolean_expression = stripslashes($docwatch_datasource_boolean_expression);
	} // end of member function set_from_form

	/**
	 * Sauvegarde des propriétés
	 *
	 * @return void
	 * @access public
	 */
	public function save(){
		if(docwatch_watch::check_watch_rights($this->num_watch)){
			if($this->id){
				$query = "update docwatch_datasources set";
				$clause = " where id_datasource=".$this->id;
			}else{
				$query = "insert into docwatch_datasources set";
				$clause = "";
			}
			$query.= "
				datasource_type = '".addslashes(get_class($this))."',
				datasource_title = '".addslashes($this->title)."',
				datasource_ttl = '".$this->ttl."',
				datasource_last_date = now(),
				datasource_parameters = '".addslashes($this->serialize())."',
				datasource_num_category = '".$this->num_category."',
				datasource_default_interesting = '".$this->default_interesting."',
				datasource_clean_html = '".$this->clean_html."',
				datasource_boolean_expression = '".addslashes($this->boolean_expression)."',
				datasource_num_watch = '".$this->num_watch."'
				".$clause;
			$result = pmb_mysql_query($query);

			if($result){
				if(!$this->id){
					$this->id = pmb_mysql_insert_id();
				}
	 			//sélecteur
				foreach ($this->parameters['selector'] as $selector_type) {
					if (in_array($selector_type, $this->selectors)) {
						$selector_id = array_search($selector_type,$this->selectors);
					} else {
						$selector_id = 0;
					}
					$selector = new $selector_type($selector_id);
					if(!empty($this->duplicate_from_id)) {
					    $selector->set_id(0);
					} else {
					    $selector->set_from_form();
					}
					$selector->set_num_datasource($this->id);
					$result = $selector->save();
					if($result){
						$this->selectors[$selector->get_id()] = $selector_type;
						$this->parameters["selector"] = array($selector->get_id());
						$query = "update docwatch_datasources set datasource_parameters = '".addslashes($this->serialize())."' where id_datasource=".$this->id;
						pmb_mysql_query($query);
						return true;
					}
				}
			}
			return false;
		}else{
			return false;
		}
	}

	/*
	 * Méthode de suppression
	*/
	public function delete(){
		if($this->id){
			if(docwatch_watch::check_watch_rights($this->num_watch)){
				//on commence par éliminer le sélecteur associé...
				$query = "select id_selector from docwatch_selectors where selector_num_datasource = ".$this->id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$query = "delete from docwatch_selectors where selector_num_datasource = ".$this->id;
					if(!pmb_mysql_query($query)){
						return false;
					}
				}
				//on est tout seul, éliminons-nous !
				$query = "delete from docwatch_datasources where id_datasource = ".$this->id;
				$result = pmb_mysql_query($query);
				if($result){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	protected function get_selected_selector(){
		//on va chercher
		if(count($this->selectors)){
			foreach($this->selectors as $id=> $classname){
				return new $classname($id);
			}
		}
		else{
			return false;
		}
	}

	public function sync($watch_owner){
		if(docwatch_watch::check_watch_rights($this->num_watch)){
			//TODO: utiliser le watch_owner passé en parametre pour requeter sur la base avec les bons droits
			if($this->last_date) {
				$updating_date = new DateTime(date($this->last_date));
				$interval = new DateInterval('PT'.$this->ttl.'H');
				$updating_date->add($interval);

				$now = new DateTime(date("Y-m-d H:i:s"));

				if($now->format('U') >= $updating_date->format('U')){
				$this->get_new_items($watch_owner);
				$this->update_last_date();
				}
			} else {
				$this->get_new_items($watch_owner);
				$this->update_last_date();
			}
		}
	}

	public function fetch_datasource_items(){
		$query = "select id_item, item_hash from docwatch_items where item_num_datasource = '".$this->id."'";
		$result = pmb_mysql_query($query);
		$datasource_items = array();
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$datasource_items[$row->id_item] = $row->item_hash;
			}
		}
		return $datasource_items;
	}

	public function contains_boolean_expression($item_id) {
		$contains = true;
		if($this->boolean_expression != '') {
			if(!isset($this->aq_members)) {
				$aq=new analyse_query($this->boolean_expression);
				if (!$aq->error) {
					$this->aq_members=$aq->get_query_members("docwatch_items","item_index_wew","item_index_sew","id_item");
				} else {
					$this->aq_members=false;
				}
			}
			if(is_array($this->aq_members)) {
				$query = "select id_item from docwatch_items where id_item=".$item_id." and ".$this->aq_members["where"]." ";
				$result = pmb_mysql_query($query);
				if($result) {
					if(!pmb_mysql_num_rows($result)) {
						$contains = false;
					}
				}
			}
		}
		if($contains) {
			$contains = docwatch_watches::contains_boolean_expression($item_id, $this->num_watch);
		}
		return $contains;
	}

	public function get_new_items($watch_owner){
		$selector_object = $this->get_selected_selector();
		if($selector_object){
			$selector_values = $selector_object->get_value();
			$selector_values = $this->filter_datas($selector_values, $watch_owner);
			$items_datas = $this->get_items_datas($selector_values);
			$datasource_items = $this->fetch_datasource_items();
			if(!empty($items_datas)) {
				for($i = 0; $i<count($items_datas); $i++){
					$item = new docwatch_item();
					if($this->default_interesting){
						$item->set_interesting(1);
					}
					$item->set_type($items_datas[$i]['type']);
					if($this->clean_html) {
						if(isset($items_datas[$i]['title'])) {
							if(is_array($items_datas[$i]['title'])) {
								if(isset($items_datas[$i]['title'][0])) {
									$item->set_title(strip_tags($items_datas[$i]['title'][0]));
								}
							} else {
								$item->set_title(strip_tags($items_datas[$i]['title']));
							}
						}

						if(isset($items_datas[$i]['summary'])) {
							if(is_array($items_datas[$i]['summary'])) {
								if(isset($items_datas[$i]['summary'][0])) {
									$item->set_summary(strip_tags($items_datas[$i]['summary'][0]));
								}
							} else {
								$item->set_summary(strip_tags($items_datas[$i]['summary']));
							}
						}

						if(isset($items_datas[$i]['content'])) {
							if(is_array($items_datas[$i]['content'])) {
								if(isset($items_datas[$i]['content'][0])) {
									$item->set_content(strip_tags($items_datas[$i]['content'][0]));
								}
							} else {
								$item->set_content(strip_tags($items_datas[$i]['content']));
							}
						}
					} else {
					    if(isset($items_datas[$i]['title'])) {
					        if(is_array($items_datas[$i]['title'])) {
					            if(isset($items_datas[$i]['title'][0])) {
					                $item->set_title($items_datas[$i]['title'][0]);
					            }
					        } else {
					            $item->set_title($items_datas[$i]['title']);
					        }
					    } 
					    if(isset($items_datas[$i]['summary'])) {
					        if(is_array($items_datas[$i]['summary'])) {
					            if(isset($items_datas[$i]['summary'][0])) {
					                $item->set_summary($items_datas[$i]['summary'][0]);
					            }
					        } else {
					            $item->set_summary($items_datas[$i]['summary']);
					        }
					    }
					    if(isset($items_datas[$i]['content'])) {
					        if(is_array($items_datas[$i]['content'])) {
					            if(isset($items_datas[$i]['content'][0])) {
					                $item->set_content($items_datas[$i]['content'][0]);
					            }
					        } else {
					            $item->set_content($items_datas[$i]['content']);
					        }
					    }
					}
					$item->set_publication_date($items_datas[$i]['publication_date']);
					$item->set_url($items_datas[$i]['url']);
					if(isset($items_datas[$i]['logo_url'])) {
					    if(is_array($items_datas[$i]['logo_url'])) {
					        if(isset($items_datas[$i]['logo_url'][0])) {
					            $item->set_logo_url($items_datas[$i]['logo_url'][0]);
					        }
					    } else {
					        $item->set_logo_url($items_datas[$i]['logo_url']);
					    }
					}
					if(!isset($items_datas[$i]['num_notice'])) {
					    $items_datas[$i]['num_notice'] = 0;
					}
					$item->set_num_notice($items_datas[$i]['num_notice']);
					$item->set_source_id($this->id);
					$item->set_num_watch($this->num_watch);
					if(!isset($items_datas[$i]['num_article'])) {
					    $items_datas[$i]['num_article'] = 0;
					}
					$item->set_num_article($items_datas[$i]['num_article']);
					if(!isset($items_datas[$i]['num_section'])) {
					    $items_datas[$i]['num_section'] = 0;
					}
					$item->set_num_section($items_datas[$i]['num_section']);
					if(!isset($items_datas[$i]['descriptors'])) {
					    $items_datas[$i]['descriptors'] = array();
					}
					$item->set_descriptors($items_datas[$i]['descriptors']);
					$item->gen_hash();
					if(!in_array($item->get_hash(), $datasource_items)){
						$query_hash = "select id_item from docwatch_items where item_hash = '".$item->get_hash()."'";
						$resultat = pmb_mysql_query($query_hash);
						if (!pmb_mysql_num_rows($resultat)){
							$saved = $item->save();
							if($saved) {
								if(!$this->contains_boolean_expression($item->get_id())) {
									$item->mark_as_deleted();
								}
							}
						}
					}else{
						$key = array_search($item->get_hash(), $datasource_items);
						unset($datasource_items[$key]);
					}
				}
			}
			//Il y'a des items a supprimer de la table (ils ne sont plus dans le flux)
			if(count($datasource_items)){
				foreach($datasource_items as $key => $value){
					$item = new docwatch_item($key);
					//On peut supprimer directement
					if($item->get_status()>1){
						$item->delete();
					}else{//Check le ttl
						$query = "select docwatch_items.id_item from docwatch_items join docwatch_watches on docwatch_watches.id_watch=".$this->num_watch." where id_item = '".$item->get_id()."' and date_add(docwatch_items.item_added_date, interval docwatch_watches.watch_ttl hour) < now()";
						$result = pmb_mysql_query($query);
						if($result && pmb_mysql_num_rows($result)){
							$item->delete();
						}
					}
				}
			}
		}

	}

	public function update_last_date(){
		$this->set_last_date(date("Y-m-d H:i:s"));
		$query = "update docwatch_datasources set datasource_last_date = '".$this->last_date."' where id_datasource = '".$this->id."'";
		if(!pmb_mysql_query($query)){
			return false;
		}
		$this->is_up_to_date = true;
		return true;
	}

	public function get_id() {
	  return $this->id;
	}

	public function set_id($id) {
	  $this->id = $id;
	}

	public function get_title() {
	  return $this->title;
	}

	public function set_title($title) {
	  $this->title = $title;
	}

	public function get_num_category() {
	  return $this->num_category;
	}

	public function set_num_category($num_category) {
	  $this->num_category = $num_category;
	}

	public function get_ttl() {
	  return $this->ttl;
	}

	public function set_ttl($ttl) {
	  $this->ttl = $ttl;
	}

	public function get_last_date() {
	  return $this->last_date;
	}

	public function set_last_date($last_date) {
	  $this->last_date = $last_date;
	}

	public function get_formated_last_date() {
		return date("c",strtotime($this->last_date));
	}

	public function get_default_interesting() {
	  return $this->default_interesting;
	}

	public function set_default_interesting($default_interesting) {
	  $this->default_interesting = $default_interesting;
	}

	public function get_clean_html() {
		return $this->clean_html;
	}

	public function set_clean_html($clean_html) {
		$this->clean_html = $clean_html;
	}

	public function get_boolean_expression() {
		return $this->boolean_expression;
	}

	public function set_boolean_expression($boolean_expression) {
		$this->boolean_expression = $boolean_expression;
	}

	public function get_num_watch() {
	  return $this->num_watch;
	}

	public function set_num_watch($num_watch) {
	  $this->num_watch = intval($num_watch);
	}

	public function set_duplicate_from_id($duplicate_from_id) {
	    $this->duplicate_from_id = intval($duplicate_from_id);
	}

	public function change_parameter_selector_to_type() {
		if(intval($this->parameters["selector"][0])) {
			$query = "select selector_type from docwatch_selectors where id_selector = ".$this->parameters["selector"][0];
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->parameters["selector"][0] = $row->selector_type;
			}
		}
	}

	public function get_is_up_to_date() {
	  return $this->is_up_to_date;
	}

	protected function get_items_datas($items){
		return array();
	}

	public function filter_datas($datas, $user=0){
		return $datas;
	}

	protected function filter_articles($datas,$user=0){
		$valid_datas = $valid_datas = array();
		//quand on filtre un article, on cherche déjà si la rubrique parente est visible...
		$valid_sections = $sections = array();
		$query = "select distinct num_section from cms_articles where id_article in (".implode(",",$datas).")";
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$sections[] = $row->num_section;
			}
			$valid_sections = $this->filter_sections($sections);
		}

		$clause_date = "
		((article_start_date != 0 and to_days(article_start_date)<=to_days(now()) and to_days(article_end_date)>=to_days(now()))||(article_start_date != 0 and article_end_date =0 and to_days(article_start_date)<=to_days(now()))||(article_start_date=0 and article_end_date=0)||(article_start_date = 0 and to_days(article_end_date)>=to_days(now())))";

		$articles = array();
		if(count($valid_sections)){
			$query = "select id_article from cms_articles
				join cms_editorial_publications_states on id_publication_state = article_publication_state
				where num_section in (".implode(",",$valid_sections).") and id_article in (".implode(",",$datas).") and editorial_publication_state_opac_show = 1".(!$_SESSION['id_empr_session'] ? " and editorial_publication_state_auth_opac_show = 0" : "")." and ".$clause_date;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$valid_datas[]=$row->id_article;
				}
			}
			foreach($datas as $article_id){
				if(in_array($article_id,$valid_datas)){
					$articles[] = $article_id;
				}
			}
		}
		return $articles;

	}

	protected function filter_sections($datas, $user=0){
		return $datas;
		$valid_datas = array();
		//on initialise un arbre avec les sections

		$paths = array();
		$tree = $this->get_sections_tree(0,"",$paths);
		$nb_days_since_1970 = 719528;
		$nb_days_today = round((time()/(3600*24)))+$nb_days_since_1970;
		foreach($datas as $id_section){
			$valid = 1;
			$section_path_ids = explode("/",$paths[$id_section]);
			$array_path = "";
			$current_tree = $tree[$section_path_ids[0]];
			//vérification sur le statut
			if(!($current_tree['opac_show'] && (!$current_tree['auth_opac_show'] || ($current_tree['auth_opac_show'] && $_SESSION['id_empr_session'])))){
				$valid = 0;
			}else{
				//vérification sur les dates...
				if($current_tree['start_day']!= 0 && $current_tree['end_day']!=0 && ($current_tree['start_day']>$nb_days_today || $current_tree['end_day']<$nb_days_today)){
					$valid = 0;
				}else if ($current_tree['start_day']!=0 && !$current_tree['end_day'] && $current_tree['start_day']>$nb_days_today){
					$valid = 0;
				}else if ($current_tree['end_day']!=0 && !$current_tree['start_day'] && $current_tree['end_day']<$nb_days_today){
					$valid = 0;
				}
			}

			for($i=1 ; $i< count($section_path_ids) ; $i++){
				if($valid){
					$current_tree = $current_tree['children'][$section_path_ids[$i]];
					//vérification sur le statut
					if(!($current_tree['opac_show'] && (!$current_tree['auth_opac_show'] || ($current_tree['auth_opac_show'] && $_SESSION['id_empr_session'])))){
						$valid = 0;
					}else{
						//vérification sur les dates...
						if($current_tree['start_day']!= 0 && $current_tree['end_day']!=0 && ($current_tree['start_day']>$nb_days_today || $current_tree['end_day']<$nb_days_today)){
							$valid = 0;
						}else if ($current_tree['start_day']!=0 && !$current_tree['end_day'] && $current_tree['start_day']>$nb_days_today){
							$valid = 0;
						}else if ($current_tree['end_day']!=0 && !$current_tree['start_day'] && $current_tree['end_day']<$nb_days_today){
							$valid = 0;
						}
					}
				}else{
					break;
				}
			}
			if($valid){
				$valid_datas[]=$id_section;
			}
		}
		return $valid_datas;
	}

	protected function get_sections_tree($id_parent = 0, $path = "", &$paths = []){
		$id_parent = intval($id_parent);
		$tree = array();

		$clause = "((section_start_date != 0 and section_start_date<now() and section_end_date>now())||(section_start_date != 0 and section_end_date =0 and section_start_date <now())||(section_start_date = 0 and section_end_date>now()))";

		$query="select id_section,to_days(section_start_date) as start_day, to_days(section_end_date) as end_day , editorial_publication_state_opac_show,editorial_publication_state_auth_opac_show from cms_sections join cms_editorial_publications_states on id_publication_state = section_publication_state where section_num_parent = ".$id_parent;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$paths[$row->id_section] = ($path ? $path."/" : "").$row->id_section ;
				$tree[$row->id_section] = array(
						'start_day' => $row->start_day,
						'end_day' => $row->end_day,
						'opac_show'=> $row->editorial_publication_state_opac_show,
						'auth_opac_show'=> $row->editorial_publication_state_auth_opac_show,
						'children' => $this->get_sections_tree($row->id_section,($path ? $path."/" : "").$row->id_section,$paths)
				);
			}
		}
		return $tree;
	}

	public function filter_notices($datas, $user=0){
		if(count($datas)){
			$filtre = new filter_results(implode(',', $datas), $user);
			return explode(',',$filtre->get_results());
		}else{
			return array();
		}
	}

	public function get_normalized_datasource(){
		return array(
			'id' => $this->id,
			'class_name' => get_class($this),
			'type' => 'source',
			'title' => $this->title,
			'ttl' => $this->ttl,
			'num_watch' => $this->num_watch,
			'last_date' => $this->last_date,
			'formated_last_date' => date("c",strtotime($this->last_date)),
			'is_up_to_date' => ($this->is_up_to_date ? 1 : 0));
	}
} // end of docwatch_datasource

