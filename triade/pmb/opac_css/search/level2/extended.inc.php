<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended.inc.php,v 1.120 2019-01-16 17:03:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur titre
// inclusion classe pour affichage notices (level 1)
require_once($base_path.'/includes/templates/notice.tpl.php');
require_once($base_path.'/classes/notice.class.php');
require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($base_path.'/classes/facette_search.class.php');
require_once($base_path.'/classes/suggest.class.php');
require_once($class_path."/map/map_search_controler.class.php");
require_once($class_path."/shorturl/shorturl_type_search.class.php");
require_once($class_path."/sort.class.php");

global $es;
global $searcher;
global $facette_test;
global $opac_allow_external_search;
global $opac_allow_affiliate_search;
global $opac_visionneuse_allow;
global $search_result_extended_affiliate_lvl2_head;
global $tab;
global $opac_allow_bannette_priv;
global $allow_dsi_priv;
global $opac_search_results_per_page;
global $opac_notices_depliable;
global $filtre_compare;
global $begin_result_liste;
global $link_to_print_search_result;
global $link_to_visionneuse;
global $opac_search_allow_refinement;
global $opac_short_url;
global $pmb_logs_activate;
global $debut;
global $search_result_extended_affiliate_lvl2_head_wo_link;
global $page;
global $add_cart_link;
global $opac_simple_search_suggestions;
global $count;
global $opac_nb_max_tri;

$es=new search();

$sr_form='';

$allow_search_affiliate_and_external=true;
if($opac_allow_affiliate_search || $opac_allow_external_search){
	$es_uni=new search("search_fields_unimarc");
	if((isset($_SESSION['facette']) && count($_SESSION['facette'])) || $facette_test || $es_uni->has_forbidden_fields()){
		$allow_search_affiliate_and_external=false;
	}
}

if($opac_allow_affiliate_search && $allow_search_affiliate_and_external){
	$sr_form.= $search_result_extended_affiliate_lvl2_head;
} else {
	$sr_form.= "	<div id=\"resultatrech\"><h3 class='searchResult-title'>".$msg['resultat_recherche']."</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";
}

//le contenu du catalogue est calculé dans 2 cas  :
// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
// 2- la recherche affiliée est active et on demande l'onglet catalog...
if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
	//gestion du tri
	if (isset($_GET["sort"])) {	
		$_SESSION["last_sortnotices"]=$_GET["sort"];
	}
	if (isset($count) && $count>$opac_nb_max_tri) {
		$_SESSION["last_sortnotices"]="";
	}
	
	if($facette_test==1){
		global $search;

		facettes::checked_facette_search();
		
		$_SESSION["lq_facette"]=$_SESSION["facette"];
		$_SESSION["lq_facette_search"]["lq_search"]=$es->serialize_search();
	}else if(isset($from_see) && $from_see == 1 && isset($filtre_compare) && $filtre_compare == "compare"){
		//from_see est un élément posté dans un formulaire d'une page d'autorité 
		//il flage l'origine qui nécessite une reconstruction de l'environnement de la multi-critère pour faire les filtres multiples ou le comparateur
		facettes::make_facette_search_env();
	}
	$lib_recherche=$es->make_human_query();
	
	$searcher = new searcher_extended();
	if($opac_visionneuse_allow){
		$nbexplnum_to_photo = $searcher->get_nb_explnums();	
	}
	$count = $searcher->get_nb_results();
	$l_typdoc= implode(",",$searcher->get_typdocs());// La variable global $l_typdoc est utilisée pour la photothèque
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['extended'] = $count;
	}
	
	if($count){
		if(isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!==""){
			$notices = $searcher->get_sorted_result($_SESSION["last_sortnotices"],$debut,$opac_search_results_per_page);	
		}else{
			$notices = $searcher->get_sorted_result("default",$debut,$opac_search_results_per_page);	
		}
		if (count($notices)) {
			$_SESSION['tab_result_current_page'] = implode(",", $notices);
		} else {
			$_SESSION['tab_result_current_page'] = "";
		}
	}
	$sr_form.= pmb_bidi("<h3 class='searchResult-search'><span class='searchResult-equation'><span class='search-found'>$count $msg[titles_found]</span> ".$lib_recherche."</span></h3>");
	
	// pour la DSI - création d'une alerte
	if ($opac_allow_bannette_priv && $allow_dsi_priv && ((isset($_SESSION['abon_cree_bannette_priv']) && $_SESSION['abon_cree_bannette_priv']==1) || $opac_allow_bannette_priv==2)) {
		$sr_form.= "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.form_values.action='./empr.php?lvl=bannette_creer'; document.form_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}
	
	// pour la DSI - Modification d'une alerte
	if ($opac_allow_bannette_priv && $allow_dsi_priv && (isset($_SESSION['abon_edit_bannette_priv']) && $_SESSION['abon_edit_bannette_priv']==1)) {
		$sr_form.= "<input type='button' class='bouton' name='dsi_priv' value=\"".$msg['dsi_bannette_edit']."\" onClick=\"document.form_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.form_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}
	
	if(!$opac_allow_affiliate_search || !$allow_search_affiliate_and_external) {
		$sr_form.= "</div>";
	}
	$sr_form.= "<div id=\"resultatrech_liste\">";
	
	if ($count) {
		if ($opac_notices_depliable) {
			if(isset($filtre_compare) && $filtre_compare=='compare'){
				$sr_form.=facette_search_compare::get_begin_result_list();
			}else{
				$sr_form.= 	$begin_result_liste;
			}
		}
		
		//impression
		$sr_form.= "<span class='print_search_result'>".$link_to_print_search_result."</span>";
		
		//gestion du tri
		$sr_form.= sort::show_tris_in_result_list($count);
		
		$sr_form.= $add_cart_link;
		
		if($opac_visionneuse_allow && $nbexplnum_to_photo){
			$search_to_post = $es->serialize_search(false,true);
			$sr_form.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
	
			$sr_form.= "
		<script type='text/javascript'>
			function sendToVisionneuse(explnum_id){
				if (typeof(explnum_id)!= 'undefined') {
					var explnum =document.createElement('input');
					explnum.setAttribute('type','hidden');
					explnum.setAttribute('name','explnum_id');
					explnum.setAttribute('value',explnum_id);
					document.form_values.appendChild(explnum);
				}
				var mode = document.createElement('input');
				mode.setAttribute('type','hidden');
				mode.setAttribute('name','mode');
				mode.setAttribute('value','extended');
				var input = document.createElement('input');
				input.setAttribute('id','serialized_search');
				input.setAttribute('name','serialized_search');
				input.setAttribute('type','hidden');
				input.setAttribute('value',\"".addslashes($search_to_post)."\");
				oldAction=document.form_values.action;
				document.form_values.appendChild(input);
				document.form_values.appendChild(mode);
			
		
				document.form_values.action='visionneuse.php';
				document.form_values.target='visionneuse';
				document.form_values.submit();
			}
		</script>";
			
		}
	}

	//affinage
	//enregistrement de l'endroit actuel dans la session
	if ($_SESSION["last_query"]) {	$n=$_SESSION["last_query"]; } else { $n=$_SESSION["nb_queries"]; }
	
	if(empty($_SESSION['facette']) || count($_SESSION['facette'])==0){
		$_SESSION["notice_view".$n]["search_mod"]="extended";
		$_SESSION["notice_view".$n]["search_page"]=$page;
	}
	
	
	//affichage
	if($opac_search_allow_refinement){
		$sr_form.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&get_query=$n' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
	}
	//fin affinage
	// url courte
	if($opac_short_url) {
		
			$shorturl_search = new shorturl_type_search();
			$sr_form.= $shorturl_search->get_display_shorturl_in_result();
		
	}
	
	//Etendre
	if ($opac_allow_external_search  && $allow_search_affiliate_and_external) {
		$sr_form.= 	"<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='javascript:document.form_values.action=\"$base_path/index.php?search_type_asked=external_search&external_type=multi\"; document.form_values.submit();' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
	}
	//fin etendre
	
	$sr_form.= suggest::get_add_link();
	
	//on suis le flag filtre/compare
	facettes::session_filtre_compare();
	$sr_form.= "<blockquote>";
	if($filtre_compare=='compare'){
		//on valide la variable session qui comprend les critères de comparaisons
		facette_search_compare::session_facette_compare();
		//affichage comparateur
		$facette_compare= new facette_search_compare();
		$compare=$facette_compare->compare($searcher);
		if($compare===true){
			$sr_form.=  $facette_compare->display_compare();
		}else{
			$sr_form.=  $msg[$compare];
		}
	}else{
		//si demande de réinitialisation
		if(isset($reinit_compare) && $reinit_compare==1){
			facette_search_compare::session_facette_compare(null,$reinit_compare);
		}
	
		$sr_form.= $searcher::get_current_search_map();
		
		
		$sr_form.= aff_notice(-1);
		$nb=0;
		$recherche_ajax_mode=0;
		if(isset($notices) && is_array($notices) && count($notices)){
			for ($i =0 ; $i<count($notices);$i++){
				if($i>4)$recherche_ajax_mode=1;
				$sr_form.= pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
			}
		}
		$sr_form.= aff_notice(-2);
	}
	$sr_form.= "</blockquote></div></div>";
	
	$sr_form.= "<div class='row'><span class=\"espaceResultSearch\">&nbsp;</span></div>";
	
	//Si pas de résultats, affichage des suggestions
	if(!$count && $opac_simple_search_suggestions){
		$tableSuggest="";
		if ($opac_autolevel2==2) {
			$actionSuggest = $base_path."/index.php?lvl=more_results&autolevel1=1";
		} else {
			$actionSuggest = $base_path."/index.php?lvl=search_result&search_type_asked=simple_search";
		}
		
		$termes = "";

		//on va chercher le premier champ
    	$s=explode("_",$search[0]);
    	$field_="field_0_".$search[0];
    	global ${$field_};
    	$field=${$field_};

    	$termes=str_replace('*','',stripslashes($_SESSION["user_query".$field[0]]));
		if (trim($termes)){
			$suggestion = new suggest($termes);
			$tmpArray = array();
			$tmpArray = $suggestion->listUniqueSimilars();
			
			if(count($tmpArray)){
				$tableSuggest.="<table class='facette_suggest'><tbody>";
				foreach($tmpArray as $word){
					$tableSuggest.="<tr>
						<td>
							<a href='".$actionSuggest."&user_query=".rawurlencode($word)."'>
								<span class='facette_libelle'>".$word."</span>
							</a>
						</td>
					</tr>";
				}
				$tableSuggest.="</tbody></table>";
				
				$sr_form.= "<div id='facette_suggest'><h3>".$msg['facette_suggest']."</h3>".$tableSuggest."</div>";
			}
		}
	}
	
	if($filtre_compare=='compare'){
		$sr_form.="<div id='navbar'><hr></div>";
	}elseif($count){
		if(!$opac_allow_affiliate_search){
			$url_page = "javascript:document.form_values.page.value=!!page!!; document.form_values.submit()";
			$nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
			$action = "javascript:document.form_values.page.value=document.form.page.value; document.form_values.submit()";
		}else{
			$url_page = "javascript:document.form_values.page.value=!!page!!; document.form_values.catalog_page.value=document.form_values.page.value; document.form_values.action = \"./index.php?lvl=more_results&mode=extended&tab=catalog\"; document.form_values.submit()";
			$nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
			$action = "javascript:document.form_values.page.value=document.form.page.value; document.form_values.catalog_page.value=document.form_values.page.value; document.form_values.action = \"./index.php?lvl=more_results&mode=extended&tab=catalog\"; document.form_values.submit()";
		}
		$sr_form.="<div id='navbar'><hr />\n<div style='text-align:center'>".printnavbar($page, $count, $opac_search_results_per_page, $url_page, $nb_per_page_custom_url, $action)."</div></div>";
	}
	
	if(!$opac_allow_affiliate_search  || !$allow_search_affiliate_and_external) {
		$sr_form.= "	</div>";
	}
	$sr_form = str_replace('<!-- search_result_extended_affiliate_lvl2_head_link -->',$search_result_extended_affiliate_lvl2_head_wo_link,$sr_form);
	
} else {
	
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$query = $es->serialize_search();
		$as=new affiliate_search_extended($query);
		$as->getResults();
		$sr_form.= $as->results;
	}
	$sr_form.= "
	</div>
	<div class='row'><span class=\"espaceResultSearch\">&nbsp;</span></div>";
	
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['extended_affiliate'] = $as->getTotalNbResults();
	}

	$es->unserialize_search($query);
	
}
print $sr_form;


function extended_get_current_search_map($mode_search=0){
	global $opac_map_activate;
	global $opac_map_max_holds;
	global $dbh;
	global $javascript_path;
	global $opac_map_size_search_result;
	global $page;
	global $aut_id;
	$map = "";
	if($opac_map_activate==1 || $opac_map_activate==2){
		$map_hold = null;

		$current_search=$_SESSION["nb_queries"];

		if($current_search<=0) $current_search = 0;
		$map_search_controler = new map_search_controler($map_hold, $current_search, $opac_map_max_holds,true);
		$map_search_controler->set_mode($current_search);

		$size=explode("*",$opac_map_size_search_result);
		if(count($size)!=2) {
                    $map_size="width:100%; height:400px;";
  		} else {
                    if (is_numeric($size[0])) {
                        $size[0] = $size[0] . "px";
                    }
                    if (is_numeric($size[1])) {
                        $size[1] = $size[1] . "px";
                    }
                    $map_size= "width:".$size[0]."; height:".$size[1].";";
  		}
			
		$map_search_controler->ajax = true;
		$map = "
		<div id='map_search' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$map_search_controler->get_json_informations()."'></div>
		";
			
	}
	return $map;
}
		