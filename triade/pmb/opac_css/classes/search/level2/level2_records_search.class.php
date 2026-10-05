<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level2_records_search.class.php,v 1.15.2.5.2.1 2025/02/28 10:39:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/level2_search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/shorturl/shorturl_type_search.class.php");
require_once($class_path."/shorturl/shorturl_type_search_tags.class.php");
require_once($class_path."/suggest.class.php");
require_once($class_path."/sort.class.php");
require_once($class_path."/bannette.class.php");

class level2_records_search extends level2_search {

	protected function get_title() {
    	global $msg;
    	global $opac_allow_tags_search;
    	global $opac_indexation_docnum_allfields;

    	$title = '';
    	switch($this->type) {
    		case 'titres':
    			$title .= $msg["titles_found"];
    			break;
    		case 'keywords':
    			if($opac_allow_tags_search == 1) {
    				$title .= $msg['tags_found'];
    			} else {
    				$title .= $msg['keywords_found'];
    			}
    			break;
    		case 'tous':
    			$msg_docnum = ($opac_indexation_docnum_allfields ? $msg['docnum_found_allfield'] : '');
    			$title .= $msg_docnum." ".$msg['titles_found'];
    			break;
    		default:
    			$title .= parent::get_title();
    			break;
    	}
    	return $title;
    }

    public function get_searcher_instance() {
    	switch($this->type) {
    		case 'abstract':
    			$searcher = new searcher_abstract($this->user_query);
    			break;
    		case 'titres':
    			$searcher = new searcher_title($this->user_query);
    			break;
    		case 'keywords':
    			global $tags;
    			if ($tags == "ok") {
    				//recherche par tags
    				$searcher = new searcher_tags($this->user_query);
    			} else {
    				$searcher = new searcher_keywords($this->user_query);
    			}
    			break;
    		case 'tous':
    			global $search_all_fields, $map_emprises_query;
    			if (!$search_all_fields) {
    				$searcher = searcher_factory::get_searcher('records', 'all_fields', $this->user_query, $map_emprises_query);
    			} else {
    				$searcher = $search_all_fields;
    			}
    			break;
    	}
    	return $searcher;
    }

    protected function get_display_elements_list() {
    	global $msg, $charset;
    	global $opac_visionneuse_allow;
    	global $count;
    	global $opac_nb_max_tri;
    	global $debut, $opac_search_results_per_page;
    	global $opac_notices_depliable;
    	global $filtre_compare;
    	global $begin_result_liste;
    	global $add_cart_link;
    	global $link_to_visionneuse;
    	global $sendToVisionneuseByPost;
    	global $opac_search_allow_refinement;
    	global $base_path;
    	global $opac_short_url;
    	global $opac_allow_external_search;
    	global $search_terms;
    	global $opac_allow_bannette_priv, $allow_dsi_priv;
    	global $link_to_print_search_result;
    	global $page;
    	global $searcher; //C'est SALE mais pas le choix pour gérer l'historique
    	global $catal_navbar, $nb_per_page_custom;
    	global $opac_rgaa_active;

    	$display = '';
    	if($opac_rgaa_active){
    	    // ouverture div pour contenir toutes les fonctionnalités
    	    $display.= "<div id='resultatrech_tools' class='result_tools'>";
    	}
    	if($this->type == 'tous') {
    		// pour la DSI - création d'une alerte
    		if ($opac_allow_bannette_priv && $allow_dsi_priv && ((isset($_SESSION['abon_cree_bannette_priv']) && $_SESSION['abon_cree_bannette_priv']==1) || $opac_allow_bannette_priv==2)) {
    		    if ($opac_rgaa_active) {
    		        $display .= "<a href='".$base_path."/empr.php?lvl=bannette_creer' class='bouton btn_dsi btn_dsi_add' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\">$msg[dsi_bt_bannette_priv]</a>";
    		    }else{
    		        $display .= "<input role='link' type='button' class='bouton btn_dsi btn_dsi_add' name='dsi_priv' value='".htmlspecialchars($msg['dsi_bt_bannette_priv'], ENT_QUOTES, $charset)."' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\">";
    		    }
    		    $display .= "<span class=\"espaceResultSearch\">&nbsp;</span>";
    		}

    		// pour la DSI - Modification d'une alerte
    		if(!empty($_SESSION['abon_edit_bannette_priv']) && !empty($_SESSION['abon_edit_bannette_priv_visibility_until']) && $_SESSION['abon_edit_bannette_priv_visibility_until'] < time()) {
    			unset($_SESSION['abon_edit_bannette_priv']);
    		}
    		if ($opac_allow_bannette_priv && $allow_dsi_priv && (isset($_SESSION['abon_edit_bannette_priv']) && $_SESSION['abon_edit_bannette_priv']==1)) {
    		    if ($opac_rgaa_active) {
    		        $display .= "<a href='".$base_path."/empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."' class='bouton btn_dsi btn_dsi_edit' id='dsi_priv_tooltip' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\">$msg[dsi_bannette_edit]</a>";
    		    }else{
    		        $display .= "<input role='link' type='button' class='bouton btn_dsi btn_dsi_edit' id='dsi_priv_tooltip' name='dsi_priv' value='".htmlspecialchars($msg['dsi_bannette_edit'], ENT_QUOTES, $charset)."' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\">";
    		    }
    		    $display .= bannette::get_tooltip_private_edition($_SESSION['abon_edit_bannette_id'], 'dsi_priv_tooltip')."<span class=\"espaceResultSearch\">&nbsp;</span>";
    		}
    	}

    	//gestion du tri
    	if (isset($_GET["sort"])) {
    		$_SESSION["last_sortnotices"]=$_GET["sort"];
    	}
    	if ($count>$opac_nb_max_tri) {
    		$_SESSION["last_sortnotices"]="";
    	}

    	$searcher = $this->get_searcher_instance();
    	if($opac_visionneuse_allow){
    		$nbexplnum_to_photo = $searcher->get_nb_explnums();
    	}
    	if($count){
    		if (!$nb_per_page_custom) {
    			$nb_per_page_custom = $opac_search_results_per_page;
    		}

    		if(!$page) {
    			$debut = 0;
    		} else {
    			$debut = ($page-1)*$nb_per_page_custom;
    		}

    		if(isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!==""){
    			$notices = $searcher->get_sorted_result($_SESSION["last_sortnotices"],$debut,$nb_per_page_custom);
    		}else{
    			$notices = $searcher->get_sorted_result("default",$debut,$nb_per_page_custom);
    		}

    		if (is_countable($notices) && count($notices)) {
    			$_SESSION['tab_result_current_page'] = implode(",", $notices);
    		} else {
    			$_SESSION['tab_result_current_page'] = "";
    		}
    		$display .= searcher::get_current_search_map(0);
    	}
    	if ($opac_notices_depliable) {
    		if($filtre_compare=='compare'){
    			$display .= facette_search_compare::get_begin_result_list();
    		}else{
    			$display .= $begin_result_liste;
    		}
    	}

    	//impression
    	$display .= "<span class='print_search_result'>".$link_to_print_search_result."</span>";

    	//gestion du tri
    	$display .= sort::show_tris_in_result_list($count);

    	$display .= $add_cart_link;

    	if($opac_visionneuse_allow && $nbexplnum_to_photo){
    		$display .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
    		$display .= $sendToVisionneuseByPost;
    	}
    	//affinage
    	//enregistrement de l'endroit actuel dans la session
    	if ($_SESSION["last_query"]) {
    	    $n=$_SESSION["last_query"];
    	} else {
    	    $n=$_SESSION["nb_queries"];
    	}

    	if (empty($_SESSION["notice_view".$n])) $_SESSION["notice_view".$n] = array();

    	switch($this->type) {
    		case 'abstract':
    			$_SESSION["notice_view".$n]["search_mod"]="abstract";
    			break;
    		case 'titres':
    			$_SESSION["notice_view".$n]["search_mod"]="title";
    			break;
    		case 'keywords':
    			$_SESSION["notice_view".$n]["search_mod"]="keyword";
    			break;
    		case 'tous':
    			$_SESSION["notice_view".$n]["search_mod"]="all";
    			break;
    	}

    	$_SESSION["notice_view".$n]["search_page"]=$page;

    	//affichage
    	if($opac_search_allow_refinement){
    		$display .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_simple_search' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
    	}
    	//fin affinage
    	// url courte
    	if($opac_short_url) {
    		if($this->type == 'keywords') {
    			$shorturl_search = new shorturl_type_search_tags();
    		} else {
    			$shorturl_search = new shorturl_type_search();
    		}
    		$display .= $shorturl_search->get_display_shorturl_in_result();
    	}

    	//Etendre
    	if ($opac_allow_external_search) $display .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
    	//fin etendre


    	$display .= suggest::get_add_link();
    	if($opac_rgaa_active){
    	    // fermeture div fonctionnalités
    	    $display.= "</div>";
    	}
    	
    	$search_terms = unserialize(stripslashes($search_terms ?? ""));

    	//on suis le flag filtre/compare
    	facettes::session_filtre_compare();

    	$display .= "<blockquote role='presentation'>";
    	if($filtre_compare=='compare'){
    		//on valide la variable session qui comprend les critères de comparaisons
    		facette_search_compare::session_facette_compare();
    		//affichage comparateur
    		$facette_compare= new facette_search_compare();
    		$compare=$facette_compare->compare($searcher);
    		if($compare===true){
    			$display .= $facette_compare->display_compare();
    		}else{
    			$display .= $msg[$compare];
    		}
    	}else{
    		//si demande de réinitialisation
    		if(isset($reinit_compare) && $reinit_compare==1){
    			facette_search_compare::session_facette_compare(null,$reinit_compare);
    		}
    		$display .= aff_notice(-1);
    		$nb=0;
    		$recherche_ajax_mode=0;

    		if (!empty($notices) && is_countable($notices) && is_array($notices)) {
        		for ($i =0 ; $i<count($notices);$i++){
        			if($i>4)$recherche_ajax_mode=1;
        			$display .= pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
        		}
    		}

    		$display .= aff_notice(-2);
    	}
    	$display .= "</blockquote>";
    	if($filtre_compare=='compare'){
    		$display .= "<div id='navbar'><hr></div>";
    		$catal_navbar="";
    	}
    	return $display;
    }

    protected function search_affiliate() {
    	global $tab;
    	global $pmb_logs_activate;

    	if($tab == "affiliate"){
    		//l'onglet source affiliées est actif, il faut son contenu...
    		switch($this->type) {
    			case 'abstract':
    				$as=new affiliate_search_abstract($this->user_query);
    				$affiliate_indice = 'abstract_affiliate';
    				break;
    			case 'titres':
    				$as=new affiliate_search_title($this->user_query);
    				$affiliate_indice = 'title_affiliate';
    				break;
    			case 'keywords':
    				$as=new affiliate_search_keywords($this->user_query);
    				$affiliate_indice = 'keyword_affiliate';
    				break;
    			case 'tous':
    				$as=new affiliate_search_all($this->user_query);
    				$affiliate_indice = 'tous_affiliate';
    				break;
    		}
    		print $as->getResults();
    	}
    	print "
			</div>
			<div class='row'><span class=\"espaceResultSearch\">&nbsp;</span></div>";
    	//Enregistrement des stats
    	if($pmb_logs_activate && $tab == "affiliate"){
    		global $nb_results_tab;
			$nb_results_tab[$affiliate_indice] = $as->getTotalNbResults();
    	}
    }
}
?>