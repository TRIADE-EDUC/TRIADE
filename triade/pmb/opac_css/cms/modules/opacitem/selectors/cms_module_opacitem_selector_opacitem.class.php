<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_opacitem_selector_opacitem.class.php,v 1.11 2023/08/17 09:47:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_opacitem_selector_opacitem extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
		$form = parent::get_form();
		$form.= "
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("opacitem")."'>".$this->format_text($this->msg['cms_module_opacitem_selector_opacitem_choose'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form .= "</div>
			</div>";
		$form .="<div id='".$this->get_form_value_name("globals")."'></div>";
		return $form;
	}

	public function gen_select(){
		$select="<select name='".$this->get_form_value_name("opacitem")."' onchange='load_opacitem_globals_".$this->get_form_value_name("opacitem")."(this.value)'>";
		foreach($this->opacitem_list() as $itemName=>$itemValue){
			if($this->parameters['opacitem']==$itemName){
				$select .="<option value='".$itemName."' selected='selected'>".$itemValue."</option>";
			}else{
				$select .="<option value='".$itemName."'>".$itemValue."</option>";
			}
		}
		$select .="</select>";
		$select .="<script>
		function load_opacitem_globals_".$this->get_form_value_name("opacitem")."(opacitem){
			dojo.xhrGet({
				url : '".$this->get_ajax_link(array($this->class_name."_hash[]" => $this->hash))."&opacitem='+opacitem,
				handelAs : 'text/html',
				load : function(data){
					dojo.byId('".$this->get_form_value_name("globals")."').innerHTML = data;
				}
			});
		}
		</script>";
		
		if($this->parameters['opacitem']){
			$select.="
			<script>
				load_opacitem_globals_".$this->get_form_value_name("opacitem")."('".$this->parameters['opacitem']."');
			</script>";
		}
		
		return $select;
	}


	public function save_form(){
		$this->parameters['opacitem'] = $this->get_value_from_form("opacitem");
		$this->parameters['globals'] = $this->get_value_from_form("globals");
		return parent::save_form();	
	}	
	

	/*
	 * Retourne la valeur sélectionné
	*/
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}

	public function execute_ajax(){
		global $opacitem;
		
		$response = array();
		if($opacitem){
			//ici surcharger les globals
			$response['content'] = "";
			foreach($this->opacitem_globals_list($opacitem) as $globalName=>$globalValue){
				$response['content'].="
				<div class='row' title='".(isset($globalValue['comment_param']) ? $this->format_text($globalValue['comment_param']) : '')."'>
					<div class='colonne3'>";
				if(isset($globalValue['sstype_param']) && $globalValue['sstype_param']){
					$response['content'].="<label>".$this->format_text($globalValue['sstype_param'])."</label>";
				}else{
					$response['content'].="<label>".$this->format_text($globalName)."</label>";
				}		
				$response['content'].="</div>
					<div class='colonne_suite'>
						<input id='' type='text' value='".$this->format_text($globalValue['value'])."' name='".$this->get_form_value_name("globals")."[".$this->format_text($globalName)."][value]'>
					</div>
				</div>";
			}
				
		}else{
			$response['content'] = "";
		}
		$response['content-type'] = "text/html";
		return $response;
	}
	
	
	private function opacitem_list(){
		$tabReturn=array();
		foreach($this->msg as $name=>$value){
			if(preg_match('/^cms_module_opacitem_item_/', $name)){
				$tabReturn[$this->format_text($name)]=$this->format_text($value);
			}
		}
		return $tabReturn;
	}
	
	private function opacitem_globals_list($opacitem) {
		$globals_list = array();
		//on recharge du formulaire si il a déjà été saisi.
		if ($this->parameters['opacitem'] == $opacitem && !empty($this->parameters['globals'])) {
			foreach ($this->parameters['globals'] as $globalName => $globalValue) {
				$globals_list[$globalName] = $globalValue;
			}
		} else {
			switch ($opacitem) {
				case 'cms_module_opacitem_item_infopage':
					global $opac_show_infopages_id;
					global $opac_show_infopages_id_top;
					
					$globals_list['opac_show_infopages_id']['value'] = $opac_show_infopages_id;
					$globals_list['opac_show_infopages_id_top']['value'] = $opac_show_infopages_id_top;
					break;
				case 'cms_module_opacitem_item_navperio':
					global $opac_perio_a2z_abc_search;
					global $opac_perio_a2z_max_per_onglet;
					global $opac_bull_results_per_page;
					global $opac_notices_depliable;
					global $opac_sur_location_activate;
					global $opac_fonction_affichage_liste_bull;
					global $opac_visionneuse_allow;
					global $opac_cart_allow;
					global $opac_max_resa;
					global $opac_resa_planning;
					global $opac_show_exemplaires;
					global $opac_resa_popup;
					global $opac_resa;
					global $opac_perio_a2z_show_bulletin_notice;
					
					$globals_list['opac_perio_a2z_abc_search']['value'] = $opac_perio_a2z_abc_search;
					$globals_list['opac_perio_a2z_max_per_onglet']['value'] = $opac_perio_a2z_max_per_onglet;
					$globals_list['opac_bull_results_per_page']['value'] = $opac_bull_results_per_page;
					$globals_list['opac_notices_depliable']['value'] = $opac_notices_depliable;
					$globals_list['opac_sur_location_activate']['value'] = $opac_sur_location_activate;
					$globals_list['opac_fonction_affichage_liste_bull']['value'] = $opac_fonction_affichage_liste_bull;
					$globals_list['opac_visionneuse_allow']['value'] = $opac_visionneuse_allow;
					$globals_list['opac_cart_allow']['value'] = $opac_cart_allow;
					$globals_list['opac_max_resa']['value'] = $opac_max_resa;
					$globals_list['opac_resa_planning']['value'] = $opac_resa_planning;
					$globals_list['opac_show_exemplaires']['value'] = $opac_show_exemplaires;
					$globals_list['opac_resa_popup']['value'] = $opac_resa_popup;
					$globals_list['opac_resa']['value'] = $opac_resa;
					$globals_list['opac_perio_a2z_show_bulletin_notice']['value'] = $opac_perio_a2z_show_bulletin_notice;
					break;
				case 'cms_module_opacitem_item_categ':
					global $opac_show_categ_browser;
					global $opac_show_categ_browser_tab;
					global $opac_show_categ_browser_home_id_thes;
					global $opac_categories_max_display;
					global $opac_categories_nav_max_display;
					global $opac_thesaurus_defaut;
					global $opac_categories_sub_mode;
					global $opac_categories_sub_display;
					global $opac_thesaurus;
					global $opac_categories_columns;
					
					$globals_list['opac_show_categ_browser']['value'] = $opac_show_categ_browser;
					$globals_list['opac_show_categ_browser_tab']['value'] = $opac_show_categ_browser_tab;
					$globals_list['opac_show_categ_browser_home_id_thes']['value'] = $opac_show_categ_browser_home_id_thes;
					$globals_list['opac_categories_max_display']['value'] = $opac_categories_max_display;
					$globals_list['opac_categories_nav_max_display']['value'] = $opac_categories_nav_max_display;
					$globals_list['opac_thesaurus_defaut']['value'] = $opac_thesaurus_defaut;
					$globals_list['opac_categories_sub_mode']['value'] = $opac_categories_sub_mode;
					$globals_list['opac_categories_sub_display']['value'] = $opac_categories_sub_display;
					$globals_list['opac_thesaurus']['value'] = $opac_thesaurus;
					$globals_list['opac_categories_columns']['value'] = $opac_categories_columns;
					break;
					
				case 'cms_module_opacitem_item_bannettes_abo':
					global $opac_show_subscribed_bannettes;
					global $opac_bannette_nb_liste;
					global $opac_bannette_notices_format;
					global $opac_bannette_notices_depliables;
					
					$globals_list['opac_show_subscribed_bannettes']['value'] = $opac_show_subscribed_bannettes;
					$globals_list['opac_bannette_nb_liste']['value'] = $opac_bannette_nb_liste;
					$globals_list['opac_bannette_notices_format']['value'] = $opac_bannette_notices_format;
					$globals_list['opac_bannette_notices_depliables']['value'] = $opac_bannette_notices_depliables;
					break;
				case 'cms_module_opacitem_item_bannettes_pub':
					global $opac_show_public_bannettes;
					global $opac_bannette_nb_liste;
					global $opac_bannette_notices_format;
					global $opac_bannette_notices_depliables;
						
					$globals_list['opac_show_public_bannettes']['value'] = $opac_show_public_bannettes;
					$globals_list['opac_bannette_nb_liste']['value'] = $opac_bannette_nb_liste;
					$globals_list['opac_bannette_notices_format']['value'] = $opac_bannette_notices_format;
					$globals_list['opac_bannette_notices_depliables']['value'] = $opac_bannette_notices_depliables;
					break;
					
					
				case 'cms_module_opacitem_item_section':
					global $opac_show_section_browser;
					global $opac_sur_location_activate;
					global $opac_nb_localisations_per_line;
					
					$globals_list['opac_show_section_browser']['value'] = $opac_show_section_browser;
					$globals_list['opac_sur_location_activate']['value'] = $opac_sur_location_activate;
					$globals_list['opac_nb_localisations_per_line']['value'] = $opac_nb_localisations_per_line;
					break;
				case 'cms_module_opacitem_item_margueritte':
					global $opac_show_marguerite_browser;
					
					$globals_list['opac_show_marguerite_browser']['value'] = $opac_show_marguerite_browser;
					break;
				case 'cms_module_opacitem_item_centcases':
					global $opac_show_100cases_browser;
					
					$globals_list['opac_show_100cases_browser']['value'] = $opac_show_100cases_browser;
					break;
				case 'cms_module_opacitem_item_dernotices':
					global $opac_show_dernieresnotices;
					global $opac_show_dernieresnotices_nb;
					
					$globals_list['opac_show_dernieresnotices']['value'] = $opac_show_dernieresnotices;
					$globals_list['opac_show_dernieresnotices_nb']['value'] = $opac_show_dernieresnotices_nb;
					break;
				case 'cms_module_opacitem_item_etageres':
					global $opac_show_etageresaccueil;
					global $opac_etagere_nbnotices_accueil;
					global $opac_etagere_notices_format;
					global $opac_etagere_notices_depliables;
					global $opac_websubscribe_show;
					global $opac_password_forgotten_show;
					global $opac_photo_filtre_mimetype;
					global $opac_explnum_order;
					global $opac_show_links_invisible_docnums;
					global $opac_photo_mean_size_x ;
					global $opac_photo_mean_size_y ;
					global $opac_photo_watermark;
					global $opac_photo_watermark_transparency;
					global $opac_default_sort;
					global $opac_default_sort_list;
					global $opac_nb_max_criteres_tri;
					global $opac_etagere_order ;
					global $opac_etagere_notices_order;
					
					$globals_list['opac_show_etageresaccueil']['value'] = $opac_show_etageresaccueil;
					$globals_list['opac_etagere_nbnotices_accueil']['value'] = $opac_etagere_nbnotices_accueil;
					$globals_list['opac_etagere_notices_format']['value'] = $opac_etagere_notices_format;
					$globals_list['opac_etagere_notices_depliables']['value'] = $opac_etagere_notices_depliables;
					$globals_list['opac_websubscribe_show']['value'] = $opac_websubscribe_show;
					$globals_list['opac_password_forgotten_show']['value'] = $opac_password_forgotten_show;
					$globals_list['opac_photo_filtre_mimetype']['value'] = $opac_photo_filtre_mimetype;
					$globals_list['opac_explnum_order']['value'] = $opac_explnum_order;
					$globals_list['opac_show_links_invisible_docnums']['value'] = $opac_show_links_invisible_docnums;
					$globals_list['opac_photo_mean_size_x']['value'] = $opac_photo_mean_size_x;
					$globals_list['opac_photo_mean_size_y']['value'] = $opac_photo_mean_size_y;
					$globals_list['opac_photo_watermark']['value'] = $opac_photo_watermark;
					$globals_list['opac_photo_watermark_transparency']['value'] = $opac_photo_watermark_transparency;
					$globals_list['opac_default_sort']['value'] = $opac_default_sort;
					$globals_list['opac_default_sort_list']['value'] = $opac_default_sort_list;
					$globals_list['opac_nb_max_criteres_tri']['value'] = $opac_nb_max_criteres_tri;
					$globals_list['opac_etagere_order']['value'] = $opac_etagere_order;
					$globals_list['opac_etagere_notices_order']['value'] = $opac_etagere_notices_order;
					break;
				case 'cms_module_opacitem_item_rssflux':
					global $opac_show_rss_browser;
					global $opac_curl_available;
					
					$globals_list['opac_show_rss_browser']['value'] = $opac_show_rss_browser;
					$globals_list['opac_curl_available']['value'] = $opac_curl_available;
					break;
				case 'cms_module_opacitem_item_contact_form':
					break;
				case 'cms_module_opacitem_item_collstate_bulletins_display':
					break;
			}
		}
		
		$query = 'SELECT type_param,sstype_param,comment_param FROM parametres WHERE CONCAT(type_param,"_",sstype_param) IN ("'.implode('","', array_keys($globals_list)).'")';
		$result = pmb_mysql_query($query);
		if (!pmb_mysql_error() && pmb_mysql_num_rows($result)) {
			while ($param = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC)) {
				if (!empty($globals_list[$param['type_param'].'_'.$param['sstype_param']])) {
					$globals_list[$param['type_param'].'_'.$param['sstype_param']] += $param;
				}
			}
		}
		return $globals_list;
	}
}