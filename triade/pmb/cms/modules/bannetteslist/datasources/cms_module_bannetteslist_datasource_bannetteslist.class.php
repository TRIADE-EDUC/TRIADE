<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslist_datasource_bannetteslist.class.php,v 1.7 2022/02/18 08:53:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/bannette.class.php");

class cms_module_bannetteslist_datasource_bannetteslist extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = false;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_bannettes_generic"
		);
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"nom_bannette",
			"comment_public",
			"date_last_remplissage",
			"date_last_envoi",
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if ($selector) {
			$return = array();
			if (is_array($selector->get_value()) && count($selector->get_value()) > 0) {				
				foreach ($selector->get_value() as $value) {
					$return[] = intval($value);
				}
			}
			
			if(count($return)) {
			    $bannettes = $this->filter_datas("bannetteslist", $return);
			    $query = "select id_bannette, nom_bannette, comment_public, nb_notices_diff from bannettes where id_bannette in ('".implode("','",$bannettes)."')";
				if (!empty($this->parameters["sort_by"])) {
				    $query .= " order by " . addslashes($this->parameters["sort_by"]);
				    if (!empty($this->parameters["sort_order"])) $query .= " ".addslashes($this->parameters["sort_order"]);
				}
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$return = array();
					while($row=pmb_mysql_fetch_object($result)){
						$flux_rss = array();
						$i=0;
						$query2 = "select * from rss_flux_content, rss_flux where id_rss_flux =num_rss_flux and type_contenant='BAN' and num_contenant='".$row->id_bannette."'";
						$result2 = pmb_mysql_query($query2);						
						if (pmb_mysql_num_rows($result2)) {
							while ($row2 = pmb_mysql_fetch_object($result2)) {
								$flux_rss[$i]['id'] = $row2->num_rss_flux;
								$flux_rss[$i]['name'] = $row2->nom_rss_flux;
								$flux_rss[$i]['opac_link'] = "./rss.php?id=".$row2->num_rss_flux;
								$flux_rss[$i]['link'] = $row2->link_rss_flux;
								$flux_rss[$i]['descr'] = $row2->descr_rss_flux;
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
								$flux_rss[$i]['template '] = $row2->tpl_rss_flux;					
								
								$i++;
							}
						}
						$bannette = bannette::get_instance($row->id_bannette);
						$return[] = array("id" => $row->id_bannette, "name" => $row->nom_bannette, "comment" => $bannette->get_render_comment_public(), "record_number" => $row->nb_notices_diff, "flux_rss" => $flux_rss);
					}
				}
			}
			return array('bannettes' => $return);
		}
		return false;
	}
}