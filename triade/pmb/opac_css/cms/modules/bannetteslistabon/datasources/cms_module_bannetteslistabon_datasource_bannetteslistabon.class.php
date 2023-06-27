<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslistabon_datasource_bannetteslistabon.class.php,v 1.1 2019-06-11 15:11:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Pour gérer la différence gestion/opac
if(file_exists($base_path.'/includes/empr_func.inc.php')){
	require_once($base_path.'/includes/empr_func.inc.php');
}
if(file_exists($base_path.'/includes/websubscribe.inc.php')){
	require_once($base_path.'/includes/websubscribe.inc.php');
}

class cms_module_bannetteslistabon_datasource_bannetteslistabon extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = false;
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
			"title"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $opac_url_base;
		
		$selector = $this->get_selected_selector();
		if ($selector) {
			$return = array();
			if (count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$return[] = $value;
				}
			}
			
			if(count($return)){
				$query = "select id_bannette, nom_bannette, comment_public from bannettes where id_bannette in (".implode(",",$return).")";

				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$return = array();
					while($row=pmb_mysql_fetch_object($result)){
						$flux_rss = array();
						$i=0;
						$query2 = "select num_rss_flux from  rss_flux_content where type_contenant='BAN' and num_contenant=".$row->id_bannette;
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
						$return[] = array("id" => $row->id_bannette, "name" => $row->nom_bannette, "comment" => $row->comment_public, "flux_rss" => $flux_rss);
					}
				}
			}
			return array(
					'bannettes' => $return,
					'ajax_link_connect' => $this->get_ajax_link(array('do'=>'connect')),
					'ajax_link_subscribe' => $this->get_ajax_link(array('do'=>'subscribe'))
			);
		}
		return false;
	}
	
	public function execute_ajax(){
		global $do,$charset;
		global $login,$password;
		global $f_nom,$f_prenom,$f_email,$f_login,$f_password,$f_passwordv,$f_verifcode;
		global $enregistrer, $lvl, $new_connexion, $tab, $bannette_abon;
		
		$response = array();
		switch($do){
			case "connect" :
				$log_ok=connexion_empr();
				if($log_ok){
					$response['content'] = 'ok_connect';
					$response['content-type'] = 'text/html';
				}else{
					$response['content'] = 'error_connect_1';
					$response['content-type'] = 'text/html';
				}
				break;
			case "subscribe" :
				if (md5($f_verifcode) == $_SESSION['image_random_value']) {
					$_SESSION['image_is_logged_in'] = true;
					$verif=verif_validite_compte();
					switch($verif[0]){
						case "0" :
							$res=pmb_mysql_query("SELECT id_empr FROM empr WHERE empr_mail='".addslashes($f_email)."'");
							if($res && (pmb_mysql_num_rows($res))){
								$row=pmb_mysql_fetch_object($res);
								$id_empr=$row->id_empr;
								//Abonnement bannettes sur inscription
								if(is_array($bannette_abon)){
									foreach($bannette_abon as $idban=>$ok){
										pmb_mysql_query("INSERT INTO bannette_abon SET num_bannette=".$idban.", num_empr=".$id_empr);
									}
								}
							}
							$_SESSION['image_random_value'] = '';
							$response['content'] = 'ok_subscribe';
							$response['content-type'] = 'text/html';
							break;
						case "1" :
							$response['content'] = 'error_subscribe_1';
							$response['content-type'] = 'text/html';
							break;
						case "2" :
							$response['content'] = 'error_subscribe_2';
							$response['content-type'] = 'text/html';
							break;
						case "3" :
							$response['content'] = 'error_subscribe_3';
							$response['content-type'] = 'text/html';
							break;
						case "4" :
							//IDENTIQUE A 1 DANS LE FICHIER WEBSUBSCRIBE.INC.PHP
							//$response['content'] = '';
							//$response['content-type'] = 'text/html';
							break;
						/*case "5" : //Inutilisé : codes retour qui servent sur validation du lien envoyé
							$response['content'] = 'non';
							$response['content-type'] = 'text/html';
							break;
						case "6" :
							$response['content'] = 'non';
							$response['content-type'] = 'text/html';
							break;
						case "7" :
							$response['content'] = 'non';
							$response['content-type'] = 'text/html';
							break;*/
						case "8" :
							$response['content'] = 'error_subscribe_8';
							$response['content-type'] = 'text/html';
							break;
					}					
				} else {
					$response['content'] = 'error_subscribe_9';
					$response['content-type'] = 'text/html';
				}
				break;
		}
		return $response;
	}

}