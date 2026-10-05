<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannette_datasource_bannette.class.php,v 1.6 2022/08/04 14:13:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/bannette.class.php");

class cms_module_bannette_datasource_bannette extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_bannette_generic"
		);
	}
	
	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;
		
		$this->parameters= array();
		$this->parameters['selector'] = $selector_choice;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if ($selector) {
			$id_bannette = $selector->get_value();
			
			if(is_array($id_bannette) && count($id_bannette)){
				$id_bannette[0] = intval($id_bannette[0]);
				$query = "select id_bannette, nom_bannette, comment_public, nb_notices_diff, entete_mail, piedpage_mail from bannettes where id_bannette = '".$id_bannette[0]."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$row=pmb_mysql_fetch_object($result);
					$flux_rss = array();
					$i=0;
					$query2 = "SELECT * FROM rss_flux_content 
                        JOIN rss_flux ON id_rss_flux = num_rss_flux 
                        WHERE type_contenant='BAN' AND num_contenant='".$row->id_bannette."'";
					$result2 = pmb_mysql_query($query2);						
					if (pmb_mysql_num_rows($result2)) {
						while ($row2 = pmb_mysql_fetch_object($result2)) {
							$flux_rss[$i]['id'] = $row2->num_rss_flux;
							$flux_rss[$i]['name'] = $row2->nom_rss_flux;
							$flux_rss[$i]['opac_link'] = "./rss.php?id=".$row2->num_rss_flux;
							$flux_rss[$i]['link'] = $row2->link_rss_flux;
							$flux_rss[$i]['lang'] = $row2->lang_rss_flux;
							$flux_rss[$i]['copy'] = $row2->copy_rss_flux;
							$flux_rss[$i]['editor_mail'] = $row2->editor_rss_flux;
							$flux_rss[$i]['webmaster_mail'] = $row2->webmaster_rss_flux;
							$flux_rss[$i]['ttl'] = $row2->ttl_rss_flux;
							$flux_rss[$i]['img_url'] = $row2->img_url_rss_flux;
							$flux_rss[$i]['img_title'] = $row2->img_title_rss_flux;
							$flux_rss[$i]['img_link'] = $row2->img_link_rss_flux;
							$flux_rss[$i]['format'] = $row2->format_flux;
							$flux_rss[$i]['content'] = $row2->rss_flux_content;
							$flux_rss[$i]['date_last'] = $row2->rss_flux_last;
							$flux_rss[$i]['export_court'] = $row2->export_court_flux;
							$flux_rss[$i]['link'] = $row2->link_rss_flux;
							$flux_rss[$i]['template '] = $row2->tpl_rss_flux;					
							
							$i++;
						}
					}
					$bannette = bannette::get_instance($row->id_bannette);
					return array("id" => $row->id_bannette, "name" => $row->nom_bannette, "comment" => $bannette->get_render_comment_public(), "record_number" => $row->nb_notices_diff, "info" => array("header" => $row->entete_mail, "footer" => $row->piedpage_mail), "flux_rss" => $flux_rss);
				}
			}
		}
		return false;
	}
}