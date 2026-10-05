<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_lvl.class.php,v 1.15.6.1 2025/01/16 08:45:29 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_lvl extends cms_module_common_selector{
	protected $lvl=array(); 
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->lvl = array(
			"author_see",
			"categ_see",
			"indexint_see",
			"coll_see",
			"more_results",
			"notice_display",
			"bulletin_display",
			"publisher_see",
			"titre_uniforme_see",
			"serie_see",
			"search_result",
			"subcoll_see",
			"search_history",
			"etagere_see",
			"etageres_see",
			"show_cart",
			"show_list",
			"section_see",
			"rss_see",
			"concept_see",
			"authperso_see",
			//"doc_command",
			"sort",
			"lastrecords",
			"infopages",
			"extend",
			"external_authorities",
			"perio_a2z_see",
			"cmspage",
			"index",
			//search_type_asked	
			"simple_search",
			"simple_search_mode_auteur",
			"simple_search_mode_categorie",
			"simple_search_mode_collection",
			"simple_search_mode_concept",
			"simple_search_mode_docnum",
			"simple_search_mode_indexint",
			"simple_search_mode_keyword",
			"simple_search_mode_editeur",
			"simple_search_mode_souscollection",
			"simple_search_mode_title",
			"simple_search_mode_titre_uniforme",
			"simple_search_mode_abstract",
			"extended_search",
			"term_search",
			"tags_search",
			"search_perso",
			"external_search",
			"perio_a2z",
			"bannette_see",
			"faq",
			"empr",
			"askmdp",
			"subscribe",
			"contact_form",
			"collstate_bulletins_display",
		    "search_segment",
		    "search_universe",
		    "animation_see",
		    "animations_see"
		);
	}
	
	public function get_form(){
		//si on est sur une page de type Page en création de cadre, on propose la condition pré-remplie...
		switch($this->cms_build_env['input']){
			case "empr.php" :
				if(!$this->id){
					$this->parameters[] = "empr";
				}
				break;
			case "askmdp.php" :
				if(!$this->id){
					$this->parameters[] = "askmdp";
				}
				break;
			case "subscribe.php" :
				if(!$this->id){
					$this->parameters[] = "subscribe";
				}
				break;
			default :
				if ($this->cms_build_env['search_type_asked']){
					if(!$this->id){
						$this->parameters[] = $this->cms_build_env['search_type_asked'];
					}
				}else if($this->cms_build_env['lvl']){
					if(!$this->id){
						$this->parameters[] = $this->cms_build_env['lvl'];
					}
				}
				break;
		}
		if (!$this->parameters) $this->parameters=array();
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_lvl'>".$this->format_text($this->msg['cms_module_common_selector_lvl'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='".$this->get_form_value_name("lvl")."[]' multiple='yes'>";
		$sorted_lvl = array();
		foreach($this->lvl as $lvl){
			$sorted_lvl[$lvl] = $this->format_text($this->msg['cms_module_common_selector_lvl_'.$lvl]);
		}
		asort($sorted_lvl);
		foreach($sorted_lvl as $lvl=>$label){
			$form.="
						<option value='".$lvl."' ".(in_array($lvl, $this->parameters) ? "selected='selected'" : "").">".$label."</option>";
		}
		$form.="
					</select>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("lvl");
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
	
	public function get_human_description_selector()
	{
		$description = "";
		$i = 1;

		if (!isset($this->parameters)) {
		    $this->parameters = [];
		}

		if (!empty($this->parameters) && !is_array($this->parameters)) {
		    $this->parameters = [$this->parameters];
		}

		foreach ($this->lvl as $lvl) {
		    if (is_array($this->parameters) && in_array($lvl, $this->parameters)) {
				if ($i <= 3) {
					if (array_search($lvl, $this->parameters) == 0) {
						$description .= $this->format_text($this->msg['cms_module_common_selector_lvl_'.$lvl]);
					} else {
						$description .= ", ".$this->format_text($this->msg['cms_module_common_selector_lvl_'.$lvl]);
					}
				} else {
					$description .= " (...+".(count($this->parameters)-3).")";
					break;
				}
				$i++;
			}
		}
		return $description;
	}
}