<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_piwik_datasource_piwik.class.php,v 1.5.6.1 2025/01/17 10:40:44 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_piwik_datasource_piwik extends cms_module_common_datasource{

	public $datas;

	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_piwik_selector_piwik_server"
		);
	}

	public function get_form(){
		$form = parent::get_form();
		return $form;
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $user_query;
		global $opac_autolevel2,$mode,$get_last_query;
		if(!$this->datas){
			$selector = $this->get_selected_selector();
			if($selector){
				$query = "select managed_module_box from cms_managed_modules join cms_cadres on id_cadre = '".($this->cadre_parent*1)."' and cadre_object = managed_module_name";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$box = pmb_mysql_result($result,0,0);
					$infos =unserialize($box);
					$server = $infos['module']['servers'][$selector->get_value()];
				}
				$type_page = cms_module_common_datasource_typepage_opac::get_type_page();
				$server['page']['type'] = cms_module_common_datasource_typepage_opac::get_label($type_page);
				$ss_type_page = cms_module_common_datasource_typepage_opac::get_subtype_page();
				$server['page']['subtype'] = cms_module_common_datasource_typepage_opac::get_label($ss_type_page);
				if($_SESSION['id_empr_session']){
					//récupération des informations liés au lecteur
					$query = "select empr_year, empr_sexe, empr_categ.libelle as categ, empr_codestat.libelle as codestat, location_libelle as location , empr_ville from empr join empr_categ on id_categ_empr = empr_categ join empr_codestat on idcode = empr_codestat join docs_location on idlocation = empr_location where id_empr = '".($_SESSION['id_empr_session']*1)."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$server['visitor']['year'] = $row->year;
							$server['visitor']['categ'] = $row->categ;
							$server['visitor']['codestat'] = $row->codestat;
							$server['visitor']['location'] = ($row->location ? $row->location : $row->empr_ville);
							$server['visitor']['sexe'] = $row->empr_sexe;
						}
					}
				}
				//tracking de recherche
				switch($type_page){
					//recherche
					case "1" :

						break;
					//résultats
					case "2" :
						switch($ss_type_page){
							case "204" :
								//recherche externe
								global $nb_result_partial;
								$server['search']['user_query'] = $user_query;
								$server['search']['type'] = $_SESSION['search_type'];
								$server['search']['count'] = $nb_result_partial;
								break;
							case "202" :
							case "206" :
								//RMC - Prédéfinie
								global $searcher_extended;
								$server['search']['user_query'] = strip_tags($_SESSION['human_query'.$_SESSION['nb_queries']]);
								$server['search']['type'] =   $_SESSION['search_type'.$_SESSION['nb_queries']];
								$server['search']['count'] =  $searcher_extended->get_nb_results();
								break;
							case "201":
							case "207":
							default :
								//simple
								if(isset($user_query) && $user_query){
									if(!$_SESSION['level1'.$_SESSION['nb_queries']]){
										$server['search']['user_query'] = $user_query;
										$server['search']['type'] = $_SESSION['search_type'];
										$server['search']['count'] = 0;
									}else{
										$server['search']['user_query'] = $user_query;
										if (is_countable($_SESSION['level1']) && count($_SESSION['level1']) >1 ){
											$server['search']['type'] = $_SESSION['search_type']." / tous";
											$server['search']['count'] = $_SESSION['level1']['tous']['count'];
										} else if (is_countable($_SESSION['level1']) && count($_SESSION['level1']) == 1 ){
											foreach ($_SESSION['level1'] as $lvl=>$level1){
												$server['search']['type'] = $_SESSION['search_type']." / ".$lvl;
												$server['search']['count'] = $level1['count'];
											}
										}else {
											$server['search']['type'] = $_SESSION['search_type']." / tous";
											$server['search']['count'] = $_SESSION['level1']['tous']['count'];
										}
									}
								}
								break;
						}

						break;
					//résultats notices
					case "3" :
						if($opac_autolevel2 == 2 && !$get_last_query){
							switch($ss_type_page){
								//simple
								case "301":
								case "302":
								default :
									if(isset($user_query) && $user_query && !isset($_GET['page']) && !isset($_POST['page'])){
										if(!$_SESSION['level1'.$_SESSION['nb_queries']]){
											$server['search']['user_query'] = $user_query;
											$server['search']['type'] = $_SESSION['search_type'];
											$server['search']['count'] = 0;
										}else{
											$server['search']['user_query'] = $user_query;
											$server['search']['type'] = $_SESSION['search_type']." / ".$mode;
											$server['search']['count'] = $_SESSION['level1'.$_SESSION['nb_queries']][$mode]['count'];
										}
									}
									break;
							}
						}
						break;
					//résultats autorités
					case "4" :
						global $count;
						switch($ss_type_page){
							case "408" :
								//recherche par tag
								if(!isset($_GET['page']) && !isset($_POST['page'])){
									$server['search']['type'] = $_SESSION['search_type'];
									$server['search']['user_query'] = $user_query;
									$server['search']['count'] = $count;
								}
						}
						break;

				}
				$this->datas = $server;
			}
		}
		return $this->datas;
	}
}