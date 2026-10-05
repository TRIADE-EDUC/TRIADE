<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category_browse.php,v 1.18.4.1 2025/01/30 09:08:07 tsamson Exp $
//
// Navigation simple dans l'arbre des catégories

$base_path="..";
$base_auth = "";
$base_title="";

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');

// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/misc.inc.php");

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

// version actuelle de l'opac
require_once($base_path.'/includes/opac_version.inc.php');

// fonctions de gestion de formulaire
require_once($base_path.'/includes/javascript/form.inc.php');
require_once($base_path.'/includes/templates/common.tpl.php');

require_once($base_path.'/includes/divers.inc.php');
require_once($base_path.'/includes/navbar.inc.php');

require_once("$class_path/marc_table.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/thesaurus.class.php");

if(!isset($user_input)) $user_input = '';
if(!isset($id_thes)) $id_thes = 0;
if(!isset($id_thes_unique)) $id_thes_unique = '';
if(!isset($perso_id)) $perso_id = 0;
if(!isset($aj)) $aj = '';
if(!isset($page)) $page = 0;
if(!isset($no_display)) $no_display = '';
if(!isset($bt_ajouter)) $bt_ajouter = '';
if(!isset($max_field)) $max_field = '';
if(!isset($keep_tilde)) $keep_tilde = '';
if(!isset($id2)) $id2 = '';
if(!isset($add_field)) $add_field = '';
if(!isset($callback)) $callback = '';
if(!isset($field_id)) $field_id = '';
if(!isset($field_name_id)) $field_name_id = '';
if(!isset($nbr_lignes)) $nbr_lignes = '';
if(!isset($p2)) $p2 = '';

include("$base_path/selectors/templates/category.tpl.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/authority.class.php");

print $popup_header;

// modules propres à select.php ou à ses sous-modules
print "<script src='".$javascript_path."/misc.js'></script>";
print $jscript;

$browser_top = '';

//recuperation du thesaurus session en fonction du caller
$libelle_partiel=0;//Pour la recherche multi-critère sur une catégorie
if($id_thes_unique>0) {
	$id_thes=$id_thes_unique;
} else{
	switch ($caller) {
		case 'notice' :
			if (!$id_thes) $id_thes = thesaurus::getNoticeSessionThesaurusId();
			if (!$perso_id) thesaurus::setNoticeSessionThesaurusId($id_thes);
			break;
		case 'categ_form' :
			if (!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
			if( $dyn!=2) thesaurus::setSessionThesaurusId($id_thes);
			break;
		case 'search_form' :
			$libelle_partiel=1;
			if (!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
			thesaurus::setSessionThesaurusId($id_thes);
			break;
		default :
			if (!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
			thesaurus::setSessionThesaurusId($id_thes);
			break;
	}
}
$thes = new thesaurus($id_thes);

if (($aj=='add') && (SESSrights & THESAURUS_AUTH)) {

	// on arrive du formulaire d'ajout à la volée
	if(!strlen($category_parent)) $category_parent_id = $thes->num_noeud_racine;
	$category_voir_id = 0;

	$noeud = new noeuds();
	$noeud->num_parent = $category_parent_id;
	$noeud->num_thesaurus = $thes->id_thesaurus;
	$noeud->save();

	$cat = new categories($noeud->id_noeud, $thes->langue_defaut);
	$cat->libelle_categorie = stripslashes($category_libelle);
	$cat->note_application = stripslashes($category_comment);
	$cat->index_categorie = " ".strip_empty_words($cat->libelle_categorie)." ";
	$cat->save();

	if ($thesaurus_mode_pmb && $caller=='notice') $nom_tesaurus='['.$thes->getLibelle().'] ' ;
	else $nom_tesaurus='' ;
	$browser_content = "<a href='#' $java_comment onclick=\"set_parent('$caller', '$noeud->id_noeud', '".htmlentities(addslashes($nom_tesaurus.$cat->libelle_categorie),ENT_QUOTES, $charset)."','$callback','".$cat->num_thesaurus."')\">";
	$browser_content .= $cat->libelle_categorie;
	$browser_content .= "</a>";
}

// nombre de références par pages
//L'usager a demandé à voir plus de résultats dans sa liste paginée
if(isset($nb_per_page_custom) && $nb_per_page_custom*1) {
    $nb_per_page = $nb_per_page_custom;
}
if(!isset($nb_per_page) || !$nb_per_page) {
    $nb_per_page = 10;
}

$nb_per_page = intval($nb_per_page);
if(!$page) $page=1;
$debut =($page-1)*$nb_per_page;

$base_url = "./category_browse.php?caller=$caller&p1=$p1&p2=$p2&perso_id=$perso_id&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn&keep_tilde=$keep_tilde&callback=$callback&infield=$infield"
."&max_field=".$max_field."&field_id=".$field_id."&field_name_id=".$field_name_id."&add_field=".$add_field."&page=!!page!!".(isset($nb_per_page_custom) ? "&nb_per_page_custom=".$nb_per_page_custom : '')."&parent="; // attention parent doit etre le dernier!!

if($bt_ajouter == "no" || ($id_thes == -1)){//Ne pas mettre le bouton ajouter si pas de thésaurus sélectionné
	$bouton_ajouter="";
}else{
	$bouton_ajouter = "<input type='button' id='add_categ' class='bouton_small' value='".$msg['ajouter']."' onClick=\"top.category_browse.document.location='".$base_url."!!id_aj!!&aj=form&id_aj1=!!id_aj!!&id_thes=".$id_thes."'\" />" ;
}

if($aj!='add'){
	if(!$nbr_lignes){
		$requete = "SELECT SQL_CALC_FOUND_ROWS noeuds.id_noeud AS categ_id ";
	}else{
		$requete = "SELECT noeuds.id_noeud AS categ_id ";
	}
	$requete.= ",noeuds.num_thesaurus ";


	if($user_input){
		$aq=new analyse_query(stripslashes($user_input));
	}else{
		$aq=new analyse_query("*");
		if($id_thes != -1){
			if ($id2 == 0) {
				//creation, on affiche le thesaurus a partir de la racine
				$id_noeud = $thes->num_noeud_racine;
			} else {//modification, on affiche a partir du pere de id2
				if ($id2 == $parent) {
					$id_noeud = $id2;
				} else {
					if(noeuds::hasChild($id2)){
						$id_noeud = $id2;
					} else {
						$noeud = new noeuds($id2);
						$id_noeud = $noeud->num_parent;
					}
				}
			}
		}
	}
	if ($aq->error) {
		error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
		exit;
	}

	if(($id_thes != -1) && ($thes->langue_defaut == $lang)){
		$members = $aq->get_query_members("categories", "libelle_categorie", "index_categorie", "num_noeud");

		if(!$user_input){
			$requete.= ", categories.libelle_categorie AS index_categorie ";
		}else{
			$requete.= ", categories.index_categorie AS index_categorie ";
			$requete.= ", ".$members["select"]." AS pert ";
		}

		$requete.= "FROM noeuds JOIN categories ON noeuds.id_noeud = categories.num_noeud AND  categories.langue='".$lang."'";
		$requete.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
		if(!$user_input){
			$requete.= "AND noeuds.num_parent = '".$id_noeud."' ";
		}else{
			$requete.= "AND (".$members["where"].") ";
		}


	}else{
		$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
		$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");

		if(!$user_input){
			$requete.= ", IF (catlg.num_noeud IS NULL, catdef.libelle_categorie, catlg.libelle_categorie) as index_categorie ";
		}else{

			$requete.= ", IF (catlg.num_noeud IS NULL, catdef.index_categorie, catlg.index_categorie) as index_categorie ";
			$requete.= ", IF (catlg.num_noeud IS NULL, (".$members_catdef["select"]."), (".$members_catlg["select"].") ) AS pert ";
		}


		if(($id_thes != -1)){//Je n'ai qu'un thésaurus mais langue du thésaurus != de langue de l'inteface
			$requete.= "FROM noeuds JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = '".$thes->langue_defaut."' ";
			$requete.= "LEFT JOIN categories AS catlg ON catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
			$requete.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
			if(!$user_input){
				$requete.= "AND noeuds.num_parent = '".$id_noeud."' ";
			}else{
				$requete.= "AND ( IF (catlg.num_noeud IS NULL, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ";
			}
		}else{
			//Plusieurs thésaurus
			$requete.= "FROM noeuds JOIN thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus ";
			$requete.= "JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = thesaurus.langue_defaut ";
			$requete.= "LEFT JOIN categories AS catlg on catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
			$requete.= "WHERE 1 ";
			$requete.= "AND ( IF (catlg.num_noeud IS NULL, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ";
		}

	}

	$requete.= "ORDER BY ";
	if($user_input){
		$requete.= "pert DESC,";
	}
	$requete.= " num_thesaurus, index_categorie ";
	$requete.= "LIMIT ".$debut.",".$nb_per_page." ";

	$result = pmb_mysql_query($requete, $dbh);
	if(!$nbr_lignes){
		$qry = "SELECT FOUND_ROWS() AS NbRows";
		if($resnum = pmb_mysql_query($qry)){
			$nbr_lignes=pmb_mysql_result($resnum,0,0);
		}
	}

	if($nbr_lignes){
		$browser_top =	"<a href='".str_replace('!!page!!',$page,$base_url).$thes->num_noeud_racine.'&id_thes='.$id_thes."'><img src='".$base_path."/images/top.gif' ></a>";
		$premier=true;
		$browser_content="";
		while($cat = pmb_mysql_fetch_row($result)) {
			$tcateg =  new category($cat[0]);

			if(!$user_input && $premier){
				if(is_countable($tcateg->path_table) && sizeof($tcateg->path_table) && $id_thes !=-1) {
					for($i=0; $i < sizeof($tcateg->path_table) - 1; $i++){
					    if ($browser_header) {
					        $browser_header .= '&gt;';
					    } else {
					        $browser_header = '';
					    }
						$browser_header .= "<a href='";
						$browser_header .= $base_url;
						$browser_header .= $tcateg->path_table[$i]['id'];
						$browser_header .= '&id2='.$tcateg->path_table[$i]['id'];
						$browser_header .= '&id_thes='.$id_thes;
						$browser_header .= "'>";
						$browser_header .= $tcateg->path_table[$i]['libelle'];
						$browser_header .= "</a>";
					}
					if ($browser_header) {
					    $browser_header .= '&gt;<strong>';
					} else {
					    $browser_header = '<strong>';
					}
					$browser_header .= $tcateg->path_table[sizeof($tcateg->path_table) - 1]['libelle'];
					$browser_header .= '</strong>';
					$bouton_ajouter=str_replace("!!id_aj!!",$tcateg->path_table[sizeof($tcateg->path_table) - 1]['id'],$bouton_ajouter);
				} else {
					$browser_header = "";
					$t = thesaurus::getByEltId($cat[0]);
					$bouton_ajouter=str_replace("!!id_aj!!",$t->num_noeud_racine,$bouton_ajouter);
				}
				$premier=false;
			}
			if (!$tcateg->is_under_tilde ||($tcateg->voir_id)||($keep_tilde)) {
				$not_use_in_indexation=$tcateg->not_use_in_indexation;
				$browser_content .= "<tr><td>";

				//$authority = new authority(0,$tcateg->id, AUT_TABLE_CATEG);
				$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $tcateg->id, 'type_object' => AUT_TABLE_CATEG]);
				$browser_content.= $authority->get_display_statut_class_html();

				if($id_thes == -1 && $thesaurus_mode_pmb){
					$display = '['.htmlentities($tcateg->thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
				} else {
					$display = '';
				}
				if($tcateg->voir_id) {
					$tcateg_voir = new category($tcateg->voir_id);
					$display .= "$tcateg->libelle -&gt;<i>".$tcateg_voir->catalog_form."@</i>";
					$id_=$tcateg->voir_id;
					$not_use_in_indexation=$tcateg_voir->not_use_in_indexation;
					if($libelle_partiel){
						$libelle_=$tcateg_voir->libelle;
					}else{
						$libelle_=$tcateg_voir->catalog_form;
					}
				} else {
					$id_=$tcateg->id;
					if($libelle_partiel){
						$libelle_=$tcateg->libelle;
					}else{
						$libelle_=$tcateg->catalog_form;
					}
					$display .= $tcateg->libelle;
				}
				if($tcateg->has_child) {
					$browser_content .= "<a href='".str_replace('!!page!!',$page,$base_url).$tcateg->id."&id2=".$tcateg->id.'&id_thes='.$tcateg->thes->id_thesaurus."'>";//On mets le bon identifiant de thésaurus
					$browser_content .= "<img src='".get_url_icon('folderclosed.gif')."' style='border:0px'/></a>";
				} else {
					$browser_content .= "<img src='".get_url_icon('doc.gif')."' style='border:0px'/>";
				}
				if ($tcateg->commentaire) {
					$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>".htmlentities($tcateg->commentaire,ENT_QUOTES, $charset)."</div>" ;
					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
				} else {
						$zoom_comment = "" ;
						$java_comment = "" ;
				}
				if ($thesaurus_mode_pmb && $caller=='notice') $nom_tesaurus='['.$tcateg->thes->getLibelle().'] ' ;
					else $nom_tesaurus='' ;
				if($not_use_in_indexation && ($caller == "notice")){
					$browser_content .= "<img src='".get_url_icon('interdit.gif')."' style='border:0px'/>&nbsp;";
					$browser_content .= $display;
					$browser_content .=$zoom_comment."\n";
					$browser_content .= "</td></tr>";
				}else{
					$browser_content .= "<a href='#' $java_comment onclick=\"set_parent('$caller', '$id_', '".htmlentities(addslashes($nom_tesaurus.$libelle_),ENT_QUOTES, $charset)."','$callback','".$tcateg->thes->id_thesaurus."')\">";
					$browser_content .= $display;
					$browser_content .= "</a>$zoom_comment\n";
					$browser_content .= "</td></tr>";
				}
			}
		// constitution de la page
		}
		switch($aj){
			case 'form':
				if (!(SESSrights & THESAURUS_AUTH)) break ;
				$cat = new category($id_aj1);
				$p_value = $id_aj1;
				$p_libelle = $cat->catalog_form;
				$action = $base_url.$parent."&id2=".$parent."&id_thes=".$id_thes."&aj=add" ;
				$title = $msg[319];
				if ($thesaurus_mode_pmb != 0) $title .= ' ('.htmlentities($thes->getLibelle(), ENT_QUOTES, $charset).')';
				$select_category_form = str_replace('!!form_title!!', $title, $select_category_form);
				$select_category_form = str_replace('!!action!!', $action, $select_category_form);
				$select_category_form = str_replace('!!parent_value!!', $p_value, $select_category_form);
				$select_category_form = str_replace('!!parent_libelle!!', htmlentities($p_libelle,ENT_QUOTES, $charset), $select_category_form);
				$bouton_ajouter = "" ;
				$browser_content = $select_category_form ;
				break;
		}
		$categ_browser = str_replace('!!browser_top!!', $browser_top, $categ_browser);
		$categ_browser = str_replace('!!bt_ajouter!!', $bouton_ajouter, $categ_browser);
		$categ_browser = str_replace('!!browser_header!!', $browser_header, $categ_browser);
		$categ_browser = str_replace('!!browser_content!!', $browser_content, $categ_browser);
		$categ_browser = str_replace('!!base_url!!', $base_url, $categ_browser);
		$categ_browser = str_replace('!!base_url!!', $base_url, $categ_browser);

		print pmb_bidi($categ_browser);

		//Création barre de navigation
		$url_base=$base_url.'&id2='.$id_noeud.'&categ=categories&sub=search&id_thes='.$id_thes.'&user_input='.rawurlencode(stripslashes($user_input));
		$nav_bar = printnavbar($page, $nbr_lignes, $nb_per_page, $url_base);
		print $nav_bar;
	} else {
		print $msg["no_category_found"];
	}
}

if(($aj == 'add') || !$nbr_lignes){
	$browser_top =	"<a href='".str_replace('!!page!!',$page,$base_url).$thes->num_noeud_racine.'&id_thes='.$id_thes."'><img src='".$base_path."/images/top.gif' ></a>";
	//$browser_content="";
	$browser_header="";
	switch($aj){
		case 'form':
			if (!(SESSrights & THESAURUS_AUTH)) break ;
			$cat = new category($id_aj1);
			$p_value = $id_aj1;
			$p_libelle = $cat->catalog_form;
			$action = $base_url.$parent."&id2=".$parent."&id_thes=".$id_thes."&aj=add" ;
			$title = $msg[319];
			if ($thesaurus_mode_pmb != 0) $title .= ' ('.htmlentities($thes->getLibelle(), ENT_QUOTES, $charset).')';
			$select_category_form = str_replace('!!form_title!!', $title, $select_category_form);
			$select_category_form = str_replace('!!action!!', $action, $select_category_form);
			$select_category_form = str_replace('!!parent_value!!', $p_value, $select_category_form);
			$select_category_form = str_replace('!!parent_libelle!!', htmlentities($p_libelle,ENT_QUOTES, $charset), $select_category_form);
			$bouton_ajouter = "" ;
			$browser_content = $select_category_form ;
			break;
	}
	$categ_browser = str_replace('!!browser_top!!', $browser_top, $categ_browser);
	$categ_browser = str_replace('!!bt_ajouter!!', $bouton_ajouter, $categ_browser);
	$categ_browser = str_replace('!!browser_header!!', $browser_header, $categ_browser);
	$categ_browser = str_replace('!!browser_content!!', $browser_content, $categ_browser);
	$categ_browser = str_replace('!!base_url!!', $base_url, $categ_browser);
	print pmb_bidi($categ_browser);
	//print $bouton_ajouter;
}

print $popup_footer;