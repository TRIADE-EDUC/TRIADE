<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page.class.php,v 1.49.2.4.2.1 2025/04/24 08:05:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/facette_search.class.php');
require_once($class_path."/suggest.class.php");
require_once($class_path."/sort.class.php");
require_once($class_path."/acces.class.php");
require_once ($class_path."/frbr/frbr_build.class.php");

/**
 * class authority_page
 * Controler Générique d'une page d'autorité
 */
class authority_page {	

	/**
	 * Instance de la classe authority
	 * @var authority
	 */
	protected $authority;
	
	protected $id;
	protected $acces_j;
	protected $statut_j;
	protected $statut_r;

	protected $dom;
	
	protected static $template_directory;
	
	/**
	 * Constructeur
	 * @param authority $authority Instance d'autorité
	 */
	public function __construct($authority){
		$this->authority = $authority;
	}
	
	public function proceed($entity_type,$context=array()){
		global $facettes_tpl;
		global $msg, $charset;
		
		facettes_root::set_facet_type('notices');
		
		if(!isset($this->authority) || !is_object($this->authority)) {
			print "<h3>".htmlentities($msg['authority_display_forbidden'],ENT_QUOTES,$charset).'</h3>';
			return;
		}
		static::$template_directory = "";		
		
		$frbr_build = $this->get_frbr_build_instance($entity_type);
		
		$facettes_tpl = '';
		$display_graph = false;
		if($frbr_build->has_page() && $frbr_build->has_cadres()) {
			//Nous avons aussi besoin de calculer les notices si les facettes sont affichées 
			if($frbr_build->get_page()->get_parameter_value('records_list') || $frbr_build->get_page()->get_parameter_value('facettes_list')) {
				// LISTE DE NOTICES ASSOCIEES
				if ($frbr_build->get_page()->get_parameter_value('record_template_directory')) {
					static::$template_directory = $frbr_build->get_page()->get_parameter_value('record_template_directory');
				}
				$this->authority->set_recordslist($this->get_recordslist());
			}
			//récupération des données des jeux de données
			$datanodes_data = $frbr_build->get_datanodes_data();
			$this->dom = new DOMDocument();
			$this->dom->encoding = $charset;
			$old_errors_value = false;
			if(libxml_use_internal_errors(true)){
				$old_errors_value = true;
			}
			$html = $this->authority->render($context);
			if($charset == "utf-8"){
				$this->dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
			}else{
				$this->dom->loadHTML($html);
			}
			
			if (!$this->dom->getElementById('aut_details')) {
				$this->dom = $this->setAllId($this->dom);
			}
			foreach ($frbr_build->get_cadres() as $cadre) {
				if ($cadre['place_visibility']) {
					if($cadre['cadre_type']) {
					    switch ($cadre['cadre_type']) {
					        case 'isbd':
					            if ($this->dom->getElementById("aut_see")) {
					                $this->dom->getElementById("aut_details")->parentNode->appendChild($this->dom->importNode($this->dom->getElementById("aut_see"),true));
					            }
					            break;
					        case 'records_list':
					            if ($this->dom->getElementById("aut_details_liste")) {
					                $this->dom->getElementById("aut_details")->parentNode->appendChild($this->dom->importNode($this->dom->getElementById("aut_details_liste"),true));
					            }
					            break;
							case 'frbr_graph' :
								$graph_node = $this->dom->createElement("div");
								$graph_node->setAttribute('id', 'frbr_entity_graph');
								$this->dom->getElementById("aut_details")->parentNode->appendChild($graph_node);
								break;
						}					
					} else {					
						$view_instance = new $cadre['cadre_object']($cadre['id']);
						$html = $view_instance->show_cadre($datanodes_data);
						$tmp_dom = new domDocument();
						if($charset == "utf-8"){
							@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
						}else{
							@$tmp_dom->loadHTML($html);
						}
						if (!$tmp_dom->getElementById($view_instance->get_dom_id())) {
							$tmp_dom = $this->setAllId($tmp_dom);
						}
						$this->dom->getElementById("aut_details")->parentNode->appendChild($this->dom->importNode($tmp_dom->getElementById($view_instance->get_dom_id()),true));
					}
				}
				if ($cadre['cadre_visible_in_graph']) {
					$display_graph = true;
				}
			}			
			
			if(!$frbr_build->get_page()->get_parameter_value('isbd')) {
				$this->dom->getElementById("aut_details_container")->removeChild($this->dom->getElementById('aut_see'));
			}
			if(!$frbr_build->get_page()->get_parameter_value('records_list')) {
				$this->dom->getElementById("aut_details_container")->removeChild($this->dom->getElementById('aut_details_liste'));
			
			}
			//frbr_graph
			if ($this->dom->getElementById("frbr_entity_graph")) {
				if ($display_graph) {
					$this->build_graph();
				} else {
					$this->dom->getElementById("frbr_entity_graph")->parentNode->removeChild($this->dom->getElementById("frbr_entity_graph"));
				}
			}
			
			print $this->dom->saveHTML();
			libxml_use_internal_errors($old_errors_value);
			if($frbr_build->get_page()->get_parameter_value('facettes_list')) {
				// FACETTES
				$facettes_tpl = $this->get_facetteslist();
			}
		} else {
			// LISTE DE NOTICES ASSOCIEES
			$this->authority->set_recordslist($this->get_recordslist());
			print $this->authority->render($context);
			// FACETTES
			$facettes_tpl = $this->get_facetteslist();
		}
	}
	
	protected function calculate_restrict_access_rights() {
		global $gestion_acces_active, $gestion_acces_empr_notice;
		
		$this->acces_j='';
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$this->acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
		}
		
		if($this->acces_j) {
			$this->statut_j='';
			$this->statut_r='';
		} else {
			$this->statut_j=',notice_statut';
			if (!empty($this->get_clause_authority_id_recordslist())) {
			    $this->statut_r .= 'and ';
			}
			$this->statut_r .= "statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		}
		if(isset($_SESSION["opac_view"]) && $_SESSION["opac_view"] && isset($_SESSION["opac_view_query"]) && $_SESSION["opac_view_query"] ){
			$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
			$this->statut_r.=" and ".$opac_view_restrict;
		}
	}
	
	/**
	 * Retourne les notices associées
	 */
	public function get_recordslist($only_records = false) {
		global $msg, $base_path, $class_path, $include_path;
		global $opac_visionneuse_allow, $opac_photo_filtre_mimetype, $link_to_visionneuse_authority, $sendToVisionneuseAuthorityDisplay;
		global $opac_allow_bannette_priv, $allow_dsi_priv;
		global $opac_nb_aut_rec_per_page;
		global $opac_search_allow_refinement, $opac_allow_external_search;
		global $nbr_lignes, $page;
		global $opac_notices_depliable;
		global $begin_result_liste;
		global $add_cart_link;
		global $from;
		global $nb_per_page_custom;
		global $charset, $opac_rgaa_active;
		
		$nb_per_page_custom = intval($nb_per_page_custom);
		$nbr_lignes = intval($nbr_lignes);
		
		//droits d'acces emprunteur/notice
		$this->calculate_restrict_access_rights();
		
		// comptage des notices associées
		if(!$nbr_lignes) {
			$requete = "SELECT COUNT(distinct notice_id) FROM notices ".$this->get_join_recordslist()." ".$this->acces_j." ".$this->statut_j;
			$requete.= " where ".$this->get_clause_authority_id_recordslist()." $this->statut_r ";
			
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			
			//Recherche des types doc
			$requete = "select distinct notices.typdoc from notices ".$this->get_join_recordslist()." ".$this->acces_j." ".$this->statut_j;
			$clause = " where ".$this->get_clause_authority_id_recordslist()." ".$this->statut_r." group by notices.typdoc";
			if ($opac_visionneuse_allow){
				$requete_noti = "select distinct notices.typdoc, count(explnum_id) as nbexplnum from notices ".$this->get_join_recordslist()." left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id ".$this->acces_j." ".$this->statut_j." ";
				$requete_bull = "select distinct notices.typdoc, count(explnum_id) as nbexplnum from notices ".$this->get_join_recordslist()." left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id ".$this->acces_j." ".$this->statut_j." ";
				$requete = "select distinct uni.typdoc, sum(nbexplnum) as nbexplnum from ($requete_noti $clause union $requete_bull $clause) as uni group by uni.typdoc";
			}else{
				$requete.= $clause;
			}
		
			$res = pmb_mysql_query($requete);
			$t_typdoc=array();
			$nbexplnum_to_photo=0;
			if($res) {
				while ($tpd=pmb_mysql_fetch_object($res)) {
					$t_typdoc[]=$tpd->typdoc;
					if ($opac_visionneuse_allow)
						$nbexplnum_to_photo += $tpd->nbexplnum;
				}
			}
			$l_typdoc=implode(",",$t_typdoc);
		}else if ($opac_visionneuse_allow){
			$clause = "where ".$this->get_clause_authority_id_recordslist()." ".$this->statut_r." group by notices.typdoc";
			$requete_noti = "select distinct notices.typdoc, count(explnum_id) as nbexplnum from notices ".$this->get_join_recordslist()." left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_notice = notice_id ".$this->acces_j." ".$this->statut_j." ";
			$requete_bull = "select distinct notices.typdoc, count(explnum_id) as nbexplnum from notices ".$this->get_join_recordslist()." left join bulletins on bulletins.num_notice = notice_id and bulletins.num_notice != 0 left join explnum on explnum_mimetype in ($opac_photo_filtre_mimetype) and explnum_bulletin != 0 and explnum_bulletin = bulletin_id ".$this->acces_j." ".$this->statut_j." ";
			$requete = "select distinct uni.typdoc, sum(nbexplnum) as nbexplnum from ($requete_noti $clause union $requete_bull $clause) as uni group by uni.typdoc";
			$res = pmb_mysql_query($requete);
			$nbexplnum_to_photo = 0;
			if($res) {
				while ($tpd=pmb_mysql_fetch_object($res)) {
					$nbexplnum_to_photo += $tpd->nbexplnum;
				}
			}
		}
		if($opac_rgaa_active) {
			$recordslist = "<p><span class=\"aut_details_liste_titre\"><span id='nb_aut_details'>" . htmlentities($nbr_lignes, ENT_QUOTES, $charset) .' '. $this->get_title_recordslist()."</span></span></p>\n";
		} else {
			$recordslist = "<h3><span class=\"aut_details_liste_titre\">".$this->get_title_recordslist()." (<span id='nb_aut_details'>" . htmlentities($nbr_lignes, ENT_QUOTES, $charset) . "</span>)</span></h3>\n";
		}
		
		if (!$only_records) {
			// Ouverture du div resultatrech_liste
			$recordslist.= "<div id='resultatrech_liste'>";
			if($opac_rgaa_active){
			    // ouverture div pour contenir toutes les fonctionnalités
			    $recordslist.= "<div id='resultatrech_tools' class='result_tools'>";
			}
			
			// pour la DSI - création d'une alerte
			if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ((isset($_SESSION['abon_cree_bannette_priv']) && $_SESSION['abon_cree_bannette_priv']==1) || $opac_allow_bannette_priv==2)) {
			    if ($opac_rgaa_active) {
			        $recordslist.= "<a href='".$base_path."/empr.php?lvl=bannette_creer' class='bouton btn_dsi btn_dsi_add' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\">$msg[dsi_bt_bannette_priv]</a>";
			    }else{
			        $recordslist.= "<input role='link' type='button' class='bouton btn_dsi btn_dsi_add' name='dsi_priv' value='".htmlspecialchars($msg['dsi_bt_bannette_priv'], ENT_QUOTES, $charset)."' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\">";
			    }
			    $recordslist.="<span class=\"espaceResultSearch\">&nbsp;</span>";
			}
			
			// pour la DSI - Modification d'une alerte
			if(!empty($_SESSION['abon_edit_bannette_priv']) && !empty($_SESSION['abon_edit_bannette_priv_visibility_until']) && $_SESSION['abon_edit_bannette_priv_visibility_until'] < time()) {
				unset($_SESSION['abon_edit_bannette_priv']);
			}
			if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && (isset($_SESSION['abon_edit_bannette_priv']) && $_SESSION['abon_edit_bannette_priv']==1)) {
			    if ($opac_rgaa_active) {
			        $recordslist.= "<a href='".$base_path."/empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."' class='bouton btn_dsi btn_dsi_edit' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\">$msg[dsi_bannette_edit]</a>";
			    }else{
			        $recordslist.= "<input role='link' type='button' class='bouton btn_dsi btn_dsi_edit' name='dsi_priv' value='".htmlspecialchars($msg['dsi_bannette_edit'], ENT_QUOTES, $charset)."' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\">";
			    }
			    $recordslist.="<span class=\"espaceResultSearch\">&nbsp;</span>";
			}
		}
		
		if(!$page) $page=1;
		$debut =($page-1)*$opac_nb_aut_rec_per_page;
		
		if($nbr_lignes) {
			// on lance la requête de sélection des notices
			$requete = "SELECT distinct notices.notice_id FROM notices ".$this->get_join_recordslist()." ".$this->acces_j." ".$this->statut_j;
			$requete.= " WHERE ".$this->get_clause_authority_id_recordslist()." $this->statut_r ";
		
			//gestion du tri
			global $opac_nb_aut_rec_per_page;
			$requete = sort::get_sort_query($requete, $nbr_lignes, $debut, "notices", "notice_id", $opac_nb_aut_rec_per_page);
			
			$res = pmb_mysql_query($requete);

			if (!$only_records) {
				if ($opac_notices_depliable) $recordslist.= $begin_result_liste;
			
				//gestion du tri
				$recordslist.= sort::show_tris_in_result_list($nbr_lignes);
			
				$recordslist.= $add_cart_link;
			
				if($opac_visionneuse_allow && $nbexplnum_to_photo){
				    $recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse_authority;
					$sendToVisionneuseAuthorityDisplay = str_replace("!!mode!!", $this->get_mode_recordslist(),$sendToVisionneuseAuthorityDisplay);
					$sendToVisionneuseAuthorityDisplay = str_replace("!!idautorite!!",$this->id,$sendToVisionneuseAuthorityDisplay);
					$recordslist.= $sendToVisionneuseAuthorityDisplay;
				}
			
				$recordslist.=suggest::get_add_link();
			
				//affinage
				//enregistrement de l'endroit actuel dans la session
				rec_last_authorities();
			
				// Gestion des alertes à partir de la recherche simple
				include_once($include_path."/alert_see.inc.php");
				$recordslist.= $alert_see_mc_values;
			
				//affichage
				if($opac_search_allow_refinement){
					$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_".($from=="search" ? "simple_search" : "module")."' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
				}
				//fin affinage
				
				//Etendre
				if ($opac_allow_external_search) $recordslist.=  "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
				//fin etendre
				
				/*****Spécifique au catégories***/
				if(static::class == 'authority_page_category') {
					global $auto_postage_form;
					if ($auto_postage_form) $recordslist.= "<div id='autopostageform'>".$auto_postage_form."</div>";
				}
				/*****Spécifique au catégories***/
			}
			
			if($opac_rgaa_active){
			    // fermeture div fonctionnalités
			    $recordslist.= "</div>";
			}
			
			global $count;
			$count = $nbr_lignes;
			
			$only_recordslist = "<blockquote role='presentation'>\n";
			$only_recordslist.= aff_notice(-1);
			$nb=0;
			$recherche_ajax_mode=0;
			
			while(($obj=pmb_mysql_fetch_object($res))) {
				global $infos_notice;
				if($nb++>4) $recherche_ajax_mode=1;
				$only_recordslist.= pmb_bidi(aff_notice($obj->notice_id, 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode, 1, static::$template_directory));
				if ($opac_nb_aut_rec_per_page) {
				    $infos_notice['nb_pages'] = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
				} else {
				    $infos_notice['nb_pages'] = 0;
				}
			}
			$only_recordslist.= aff_notice(-2);
			$only_recordslist.= "</blockquote>\n";
		
			pmb_mysql_free_result($res);
			
			if ($only_records) {
				return $only_recordslist;
			}
			
			$recordslist.= $only_recordslist;
		
// 			$recordslist.= "</div><!-- fermeture #aut_details_liste -->\n";
			if (!isset($l_typdoc)) {
			   $l_typdoc = '';
			}
			
			$recordmodes = record_display_modes::get_instance();
			$nav_displayed = (is_object($recordmodes) ? $recordmodes->is_nav_displayed($recordmodes->get_current_mode()) : true);
			if ($nav_displayed) {
				$recordslist.= "<div id='navbar'><hr /><div style='text-align:center'>".printnavbar($page, $nbr_lignes, $opac_nb_aut_rec_per_page, "./index.php?lvl=".$this->get_mode_recordslist()."&id=".$this->id."&page=!!page!!&nbr_lignes=$nbr_lignes&l_typdoc=".rawurlencode($l_typdoc).($nb_per_page_custom ? "&nb_per_page_custom=".$nb_per_page_custom : ''))."</div></div>\n";
			}
		} else {
		    if($opac_rgaa_active){
		        // fermeture div fonctionnalités
		        $recordslist.= "</div>";
		    }
		    
		    switch (static::class) {
				case 'authority_page_indexint':
					$recordslist.= "<blockquote role='presentation'>".$msg['categ_empty']."</blockquote>";
					break;
				case 'authority_page_category':
					$recordslist.= $msg["categ_empty"];
					global $auto_postage_form;
					if($auto_postage_form) $recordslist.= "<br />".$auto_postage_form;
					break;
				default:
				    print "<p id='no_result_paragraph'>". $msg['no_document_found'] ."</p>";
				    break;
			}
		}
		$recordslist.= "</div>"; // Fermeture du div resultatrech_liste
		return $recordslist;
	}
	
	public function get_records_ids() {
		if(!isset($this->acces_j)) {
			$this->calculate_restrict_access_rights();
		}
		// on lance la requête de sélection des notices
		$query = "SELECT distinct notices.notice_id FROM notices ".$this->get_join_recordslist()." ".$this->acces_j." ".$this->statut_j;
		$query .= " WHERE ".$this->get_clause_authority_id_recordslist()." $this->statut_r ";
		$result = pmb_mysql_query($query);
		$records_ids = array();
		while($row = pmb_mysql_fetch_object($result)) {
			$records_ids[] = $row->notice_id;
		}
		return $records_ids;
	}
	
	public function get_facetteslist() {
		global $nbr_lignes;	
		
		if(!isset($this->acces_j)) {
			$this->calculate_restrict_access_rights();
		}
		$facettes_tpl = '';
		//comparateur de facettes : on ré-initialise
		$_SESSION['facette']=array();
		if($nbr_lignes){
			$query = "SELECT distinct notices.notice_id FROM notices ".$this->get_join_recordslist()." ".$this->acces_j." ".$this->statut_j;
			$query .= " WHERE ".$this->get_clause_authority_id_recordslist()." $this->statut_r ";
			$facettes_tpl .= facettes::get_display_list_from_query($query);
		}
		return $facettes_tpl;
	}
	
	protected function get_title_recordslist() {
		return "";
	}
	
	protected function get_join_recordslist() {
		return "";
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "";
	}
	
	protected function get_mode_recordslist() {
		return "";
	}
	
	protected function build_graph(){
		global $include_path, $charset;
		$html = '';
		
		$frbr_entity_graph = frbr_entity_graph::get_entity_graph($this->authority, 'authority');
		$entities_graphed = $frbr_entity_graph->get_entities_graphed();
		$content = $frbr_entity_graph->get_json_entities_graphed();	
		
		if (count($entities_graphed['links'])) {
			$template_path = $include_path.'/templates/frbr_entities_graph.tpl.html';
			if(file_exists($include_path.'/templates/frbr_entities_graph_subst.tpl.html')){
				$template_path = $include_path.'/templates/frbr_entities_graph_subst.tpl.html';
			}
			if(file_exists($template_path)){
				$h2o = H2o_collection::get_instance($template_path);
				// Content -> Structure json à passer au constructeur de la classe dojo permettant de générer le graphe
					
				$graph = array('nodes'=> $content['nodes'], 'links' => $content['links']);
				$html = $h2o->render(array('graph' => $graph));
				
				$tmp_dom = new domDocument();
				if($charset == "utf-8"){
					@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
				}else{
					@$tmp_dom->loadHTML($html);
				}
				if (!$tmp_dom->getElementById('entity_graph')) {
					$tmp_dom = $this->setAllId($tmp_dom);
				}
				$this->dom->getElementById("frbr_entity_graph")->appendChild($this->dom->importNode($tmp_dom->getElementById('entity_graph'),true));
			}
		}	
	}
	
	public function setAllId($DOMNode){
		if($DOMNode->hasChildNodes()){
			for ($i=0; $i<$DOMNode->childNodes->length;$i++) {
				$this->setAllId($DOMNode->childNodes->item($i));
			}
		}
		if($DOMNode->hasAttributes()){
			$id=$DOMNode->getAttribute("id");
			if($id){
				$DOMNode->setIdAttribute("id",true);
			}
		}
		return $DOMNode;
	}
	
	protected function get_frbr_build_instance($entity_type) {
	    return frbr_build::get_instance($this->id, $entity_type);
	}
}