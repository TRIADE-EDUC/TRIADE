<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_tpl.inc.php,v 1.182.2.4.2.6 2025/05/21 06:52:24 dgoron Exp $

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $include_path, $class_path, $func_format;

require_once ($include_path . '/misc.inc.php');

//gestion des droits
require_once($class_path."/acces.class.php");
require_once($class_path."/skos/skos_concepts_list.class.php");
require_once($class_path."/skos/skos_view_concepts.class.php");
require_once($class_path."/bannette.class.php");

if(empty($func_format)) {
	$func_format= array();
}
$func_format['b_empty']= 'aff_b_empty';
$func_format['a_empty']= 'aff_a_empty';
$func_format['not_empty']= 'aff_not_empty';
$func_format['if_empty']= 'aff_if_empty';
$func_format['if']= 'aff_if';
$func_format['gen_tpl']= 'aff_gen_tpl';
$func_format['gen_plus']= 'aff_gen_plus';
$func_format['replace']= 'replace_str';
$func_format['htmlentities'] = 'aff_htmlentities';
$func_format['set_variable'] = 'aff_set_variable';
$func_format['get_variable'] = 'aff_get_variable';
$func_format['get_global_variable'] = 'aff_get_global_variable';

$func_format['isbd']= 'aff_isbd';
$func_format['title']= 'aff_title';
$func_format['parallel_title']= 'aff_parallel_title';
$func_format['complement_title']= 'aff_complement_title';
$func_format['typdoc']= 'aff_typdoc';
$func_format['icondoc']= 'aff_icondoc';
$func_format['iconcart']= 'aff_iconcart';
$func_format['authors']= 'aff_auteurs';
$func_format['author']= 'aff_auteur_principal';
$func_format['author_1']= 'aff_auteur_autre';
$func_format['author_2']= 'aff_auteur_secondaire';
$func_format['publisher']= 'aff_ed1';
$func_format['publisher_1']= 'aff_ed2';
$func_format['year_publication']= 'aff_year_publication';
$func_format['date_publication']= 'aff_date_publication';
$func_format['resume']= 'aff_resume';
$func_format['contenu']= 'aff_contenu';
$func_format['note']= 'aff_note';
$func_format['categories']= 'aff_categories';
$func_format['indexint']= 'aff_indexint';
$func_format['header_link']= 'aff_header_link';
$func_format['is_article']= 'aff_is_article';
$func_format['is_serial']= 'aff_is_serial';
$func_format['is_bull']= 'aff_is_bull';
$func_format['is_mono']= 'aff_is_mono';
$func_format['nom_revue']= 'aff_nom_revue';
$func_format['date_bulletin']= 'aff_date_bulletin';
$func_format['numero_bulletin']= 'aff_numero_bulletin';
$func_format['expl_num_number']= 'aff_expl_num_number';
$func_format['expl_num']= 'aff_expl_num';
$func_format['expl_number']= 'aff_expl_number';
$func_format['expl']= 'aff_expl';
$func_format['expl_by_field']= 'aff_expl_by_field';
$func_format['expl_with_tpl']= 'aff_expl_with_tpl';

$func_format['isbn']= 'aff_isbn';
$func_format['issn']= 'aff_issn';
$func_format['img_url']= 'aff_img_url';
$func_format['img_base64']= 'aff_img_base64';
$func_format['img']= 'aff_img';
$func_format['no_image']= 'aff_no_image';
$func_format['vignette']= 'aff_vignette';
$func_format['get_notice']= 'aff_get_notice';
$func_format['get_expl']= 'aff_get_expl';
$func_format['collection']= 'aff_collection';
$func_format['collation']= 'aff_collation';
$func_format['page']= 'aff_page';
$func_format['lang']= 'aff_lang';
$func_format['lang_or']= 'aff_lang_or';
$func_format['cost']= 'aff_cost';
$func_format['url']= 'aff_url';
$func_format['p_perso']= 'aff_p_perso';
$func_format['p_authperso']= 'aff_p_authperso';
$func_format['notice_field']= 'aff_notice_field';
$func_format['statut']= 'aff_statut';

$func_format['extract_path']= 'aff_extract_path';
$func_format['extract_explode']= 'aff_extract_explode';
$func_format['format_date']= 'aff_format_date';
$func_format['month_in_letters']= 'aff_month_in_letters';
$func_format['trim']= 'aff_trim';
$func_format['strpos']= 'aff_strpos';
$func_format['substr']= 'aff_substr';
$func_format['limitstring']= 'aff_limitstring';
$func_format['ifequal']= 'aff_ifequal';
$func_format['lastchr']= 'aff_lastchr';
$func_format['strtoupper']= 'aff_strtoupper';
$func_format['strtolower']= 'aff_strtolower';
$func_format['ucfirst']= 'aff_ucfirst';
$func_format['ucwords']= 'aff_ucwords';
$func_format['initiale']= 'aff_initiale';

$func_format['publisher_name']= 'aff_publisher_name';
$func_format['publisher_place']= 'aff_publisher_place';
$func_format['publisher_1_name']= 'aff_publisher2_name';
$func_format['publisher_1_place']= 'aff_publisher2_place';
$func_format['mention_edition']= 'aff_mention_edition';
$func_format['get_notice_tpl']= 'aff_get_notice_tpl';

$func_format['get_parents_in_tpl']= 'aff_get_parents_in_tpl';
$func_format['get_childs_in_tpl']= 'aff_get_childs_in_tpl';

$func_format['authors_by_type']= 'aff_authors_by_type';
$func_format['authors_by_type_with_tpl']= 'aff_authors_by_type_with_tpl';
$func_format['authors_by_type_dir']= 'aff_authors_by_type_dir';

$func_format['permalink']= 'aff_permalink';

$func_format['parents_page']= 'aff_parents_page';
$func_format['parents_authors_by_type_with_tpl']= 'aff_parents_authors_by_type_with_tpl';
$func_format['parents_title']= 'aff_parents_title';
$func_format['parents_mention_edition']= 'aff_parents_mention_edition';
$func_format['parents_publisher_name']= 'aff_parents_publisher_name';
$func_format['parents_publisher_place']= 'aff_parents_publisher_place';
$func_format['parents_year_publication']= 'aff_parents_year_publication';

$func_format['str_replace']= 'aff_str_replace';

$func_format['collstate']= 'aff_collstate';
$func_format['get_collstate']= 'get_collstate';

$func_format['titre_bulletin'] = 'aff_titre_bulletin';

$func_format['linked_id'] = 'linked_id';
$func_format['serial_linked_id'] = 'serial_linked_id';
$func_format['group'] = 'group';
$func_format['filter_from_notice_info']= 'filter_from_notice_info';

$func_format['publisher_with_tpl'] = 'aff_ed1_with_tpl';
$func_format['publisher_1_with_tpl'] = 'aff_ed2_with_tpl';
$func_format['collection_with_tpl'] = 'aff_collection_with_tpl';
$func_format['subcollection_with_tpl'] = 'aff_subcollection_with_tpl';
$func_format['categories_with_tpl'] = 'aff_categories_with_tpl';
$func_format['indexint_with_tpl'] = 'aff_indexint_with_tpl';
$func_format['expl_num_by_field']= 'aff_expl_num_by_field';
$func_format['expl_num_with_tpl'] = 'aff_expl_num_with_tpl';
$func_format['msg'] = 'aff_msg';
$func_format['serie_with_tpl'] = 'aff_serie_with_tpl';
$func_format['ellipse'] = 'aff_ellipse';
$func_format['titre_uniforme_with_tpl'] = 'aff_titre_uniforme_with_tpl';

$func_format['avis']= 'aff_avis';
$func_format['avis_with_tpl']= 'aff_avis_with_tpl';

$func_format['expl_num_vign_reduit'] = 'aff_expl_num_vign_reduit';

$func_format['map_isbd'] = 'aff_map_isbd';
$func_format['map_echelle'] = 'aff_map_echelle';
$func_format['map_projection'] = 'aff_map_projection';
$func_format['map_ref'] = 'aff_map_ref';
$func_format['map_equinoxe'] = 'aff_map_equinoxe';
$func_format['map'] = 'aff_map';

$func_format['authperso_name'] = 'aff_authperso_name';
$func_format['authperso_isbd'] = 'aff_authperso_isbd';

$func_format['notes_avg']= 'aff_notes_avg';

$func_format['nb_expl_available']= 'aff_nb_expl_available';

$func_format['opac_url']= 'aff_opac_url';

$func_format['bull_for_art_expl_num']= 'aff_bull_for_art_expl_num';
$func_format['bull_for_art_expl_num_vign_reduit'] = 'aff_bull_for_art_expl_num_vign_reduit';
$func_format['bull_for_art_expl_num_with_tpl'] = 'aff_bull_for_art_expl_num_with_tpl';

$func_format['bannette_id'] = 'aff_bannette_id';
$func_format['bulletin_id'] = 'aff_bulletin_id';

$func_format['strip_tags'] = 'aff_strip_tags';
$func_format['concepts'] = 'aff_concepts';
$func_format['keywords'] = 'aff_keywords';
$func_format['creator'] = 'aff_creator';

$parser_environnement = array();

function replace_str($param) {
    global $parser_environnement;
    if(!$parser_environnement['id_notice']) return '';
    $notice=gere_global();
    return str_replace("!!".$param[1]."!!", $notice['notice_info']->{$param[1]}, $param[0]);
}

function aff_p_perso($param) {
	global $parser_environnement;
	global $pmb_perso_sep;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if (!isset($notice['notice_info']->parametres_perso[$param[0]])) {
		return '';
	}
	if (!isset($param[1]) || !$param[1]) {
		if (!$notice['notice_info']->parametres_perso[$param[0]]["VALUE"]) $notice['notice_info']->parametres_perso[$param[0]]["VALUE"]=array();
		return implode($pmb_perso_sep,$notice['notice_info']->parametres_perso[$param[0]]["VALUE"]);
	} elseif ($param[1] == 1) {
		return $notice['notice_info']->parametres_perso[$param[0]]['TITRE'];
	} elseif ($param[1] == 2) {
		return implode($pmb_perso_sep,$notice['notice_info']->parametres_perso[$param[0]]["VALUE_IN_DATABASE"]);
	}else{
		return '';
	}
}

function aff_p_authperso($param) {
	global $parser_environnement;
	global $pmb_perso_sep;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if(!isset($param[1]) || !$param[1]) $separator = $pmb_perso_sep;
	else $separator = $param[1];
	if(!isset($param[2]) || !$param[2]) $limit = 0;
	else $limit = $param[2];
	if(!isset($param[3]) || !$param[3]) {
		$values = array();
		if(is_array($notice['notice_info']->parametres_auth_perso[$param[0]]["VALUE"])) {
			foreach ($notice['notice_info']->parametres_auth_perso[$param[0]]["VALUE"] as $field) {
				$values[] = implode($pmb_perso_sep, $field);
			}
			if($limit) $values = array_slice($values, 0, $limit);
			return implode($separator,$values);
		}
	}else {
		$values = array();
		if($limit) $values = array_slice($notice['notice_info']->parametres_auth_perso[$param[0]]['TITRE'], 0, $limit);
		else $values = $notice['notice_info']->parametres_auth_perso[$param[0]]['TITRE'];
		return implode($separator, $values);
	}
}

function aff_notice_field($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->notice->{$param[0]};
}

function aff_url($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->notice->lien;
}

function aff_page($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->notice->npages;
}

function aff_cost($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->notice->prix;
}

function aff_collation($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_collation;
}

function aff_lang($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$display=array();
	foreach($notice['notice_info']->memo_lang as $line){
		$display[]=$line['langue'];
	}
	if(!isset($param[0]) || !$param[0]) $sep="; "; else $sep=$param[0];
	return implode ($sep,$display);
}

function aff_lang_or($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$display=array();
	foreach($notice['notice_info']->memo_lang_or as $line){
		$display[]=$line['langue'];
	}
	if(!isset($param[0]) || !$param[0]) $sep="; "; else $sep=$param[0];
	return implode ($sep,$display);
}

function aff_collection($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_collection;
}

function aff_collection_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "collection", cf classes/collection.class.php
	// $param[0] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$collec_infos = new collection($notice['notice_info']->notice->coll_id);

	return _get_aut_infos($collec_infos, $param[0]);
}

function aff_subcollection_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "subcollection", cf classes/subcollection.class.php
	// $param[0] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$subcoll_infos = new subcollection($notice['notice_info']->notice->subcoll_id);

	return _get_aut_infos($subcoll_infos, $param[0]);
}

function aff_categories_with_tpl($param) {
	// $param[0] = 0=tous thésaurus, sinon thesaurus id in $param[0]
	// $param[1] = séparateur entre categories
	// $param[2] = séparateur entre thesaurus
	// $param[3] = langue à prendre en compte
	// $param[4] = afficher le nom du ou des thesaurus en entête, 0 ou 1
	// $param[5] = template
	//Attention : la propriété commentaire_public n'est disponible qu'en opac !

	global $parser_environnement;
	global $lang;

	if(!$parser_environnement['id_notice']) return '';

	$oldLangue=$lang;

	//Descripteurs
	if (isset($param[0]) && $param[0]>0) $restrict_thes=" AND num_thesaurus in (".$param[0].")";
	$requete="SELECT DISTINCT libelle_thesaurus as thesnom, notices_categories.num_noeud FROM notices_categories LEFT JOIN categories ON categories.num_noeud = notices_categories.num_noeud LEFT JOIN thesaurus ON thesaurus.id_thesaurus = categories.num_thesaurus WHERE notcateg_notice='".$parser_environnement['id_notice']."' $restrict_thes ORDER BY libelle_thesaurus, ordre_categorie";
	$resultat=pmb_mysql_query($requete);
	$thes_conserve="";$res="";
	$juste_apresthes=true;
	while (($cat = pmb_mysql_fetch_object($resultat))) {
		if ($thes_conserve!=$cat->thesnom) {
			if (!$res && $param[4]) $res="[".$cat->thesnom."] ";
			elseif ($param[4]) $res.=$param[2]."[".$cat->thesnom."] ";
			elseif ($res) $res.=$param[2];
			$thes_conserve=$cat->thesnom;
			$juste_apresthes=true;
		}
		//On demande une langue précise
		if(trim($param[3])){
			$lang=$param[3];
		}
		$categ_infos = new category($cat->num_noeud);
		$libelle=_get_categ_infos($categ_infos, $param[5]);
		if ($juste_apresthes) {
			$res.=$libelle;
			$juste_apresthes=false;
		} elseif ($res){
			$res.=$param[1].$libelle;
		} else {
			$res.=$libelle;
		}
	}

	//On remet la langue par défaut
	$lang=$oldLangue;

	return $res;
}

function _get_categ_infos($cat,$tpl){
	$matches=array();
	if($tpl != "" && preg_match_all("/{([^}]*)}/",$tpl,$matches)){
		for ($i=0 ; $i<count($matches[1]) ; $i++){
		    $tpl = str_replace($matches[0][$i],$cat->{$matches[1][$i]},$tpl);
		}
	}else{
		return $cat->isbd_entry_lien_gestion;
	}
	return $tpl;
}

function aff_indexint_with_tpl($param) {
	// $param[0] = séparateur
	// $param[1] = afficher le plan de classement ? 0 ou 1
	// $param[2] = séparateur
	// $param[3] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	// Indexation décimale
	$requete="SELECT name_pclass, indexint_id FROM notices, indexint, pclassement where notice_id='".$parser_environnement['id_notice']."' and id_pclass=num_pclass and indexint_id=indexint";
	$resultat=pmb_mysql_query($requete);
	if ($int = pmb_mysql_fetch_object($resultat)) {
		$res = (isset($param[0]) ? $param[0] : '');
		if (isset($param[1]) && $param[1]==1) $res.=$int->name_pclass;
		$res.= (isset($param[2]) ? $param[2] : '');
		$indexint_infos = new indexint($int->indexint_id);
		$res.= _get_indexint_infos($indexint_infos, (isset($param[3]) ? $param[3] : ''));
	}
	return $res;
}

function _get_indexint_infos($ind,$tpl){
	$matches=array();
	if($tpl != "" && preg_match_all("/{([^}]*)}/",$tpl,$matches)){
		for ($i=0 ; $i<count($matches[1]) ; $i++){
		    $tpl = str_replace($matches[0][$i],$ind->{$matches[1][$i]},$tpl);
		}
	}else{
		return $ind->isbd_entry_lien_gestion;
	}
	return $tpl;
}

function aff_gen_tpl($param) {
	global $parser_environnement;
	global $pmb_perso_sep;
	if(!$parser_environnement['id_notice']) return '';
	$display='';
	$notice=gere_global();
	$i = 0;
	if (!empty($param[0]) && is_countable($param[0])) {
	    foreach($param[0] as $line){
	        $tpl=$param[1];
	        $tpl=str_replace("!!parity!!", ($i++&1) ? "odd" : "even", $tpl) ;
	        foreach($line as $key=>$val){
	            if(is_string($val) || is_int($val)) {
	                $tpl=str_replace("!!$key!!", $val, $tpl);
	                $tpl=str_replace("!!p_perso_$key!!", $val, $tpl);
	            }
	        }
	        // p_perso de la notice
	        while(($p_perso=strstr($tpl,"p_perso_notice"))){
	            $pos_end=strpos($p_perso,"!!");
	            $name=substr($p_perso,0,$pos_end);
	            $name=substr($name,14);
	            $val= $notice['notice_info']->parametres_perso[$name]["VALUE"];
	            if(is_array($val)) {
	                $sub_tpl = array();
	                foreach ($val as $sub_val) {
	                    $sub_tpl[] = str_replace("!!p_perso_notice_$name!!", $sub_val, $tpl);
	                }
	                $tpl=implode($pmb_perso_sep, $sub_tpl);
	            } else {
	                $tpl=str_replace("!!p_perso_notice_$name!!", $val, $tpl);
	            }
	        }
	        // p_perso de l'exemplaire
	        while(($p_perso=strstr($tpl,"p_perso_"))){
	            $pos_end=strpos($p_perso,"!!");
	            $name=substr($p_perso,0,$pos_end);
	            $name=substr($name,8);
	            
	            $val= $line->parametres_perso[$name]["VALUE"];
	            if(is_array($val)) {
	                $sub_tpl = array();
	                foreach ($val as $sub_val) {
	                    $sub_tpl[] = str_replace("!!p_perso_$name!!", $sub_val, $tpl);
	                }
	                $tpl=implode($pmb_perso_sep, $sub_tpl);
	            } else {
	                $tpl=str_replace("!!p_perso_$name!!", $val, $tpl);
	            }
	        }
	        $display.=$tpl;
	    }
	}
	return $display;
}

function aff_get_notice($param) {
	global $parser_environnement;
	$res=array();
	if(!$parser_environnement['id_notice']) return $res;
	$notice=gere_global();

	if(!empty($notice['notice_info'])) {
		$memo_notice = $notice['notice_info']->get_memo_notice();
		$memo_notice->parametres_perso = $notice['notice_info']->get_parametres_perso();
		$res = array($memo_notice);
	}
	return $res;
}

function aff_get_expl($param) {
	global $parser_environnement;
	$res=array();
	if(!$parser_environnement['id_notice']) return $res;
	$notice=gere_global();

	if(count($notice['notice_info']->memo_exemplaires)) {
		$res =$notice['notice_info']->memo_exemplaires;
	}
	return $res;
}

function aff_img_url($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_url_image;
}

function aff_img_base64($param) {
	global $parser_environnement;
	if (!$parser_environnement['id_notice']) {
		return '';
	}

	$allowed_mimetypes = [
	    'image/jpg',
	    'image/jpeg',
	    'image/png',
	    'image/gif',
	    'image/webp',
	    'image/tiff'
	];

	if (!empty($param) && "" != $param[0]) {
	    if (false !== strpos($param[0], "./")) {
	        $mimetype = pathinfo($param[0], PATHINFO_EXTENSION);
	        if (in_array("image/" . $mimetype, $allowed_mimetypes)) {
    	        $content = file_get_contents("$param[0]");
    	        $base64 = base64_encode($content);
        		return "data:".$mimetype.";base64,".$base64;
	        } else {
	            return '';
	        }
	    }
	}
	$notice = gere_global();
	$thumbnailSourcesHandler = new ThumbnailSourcesHandler();
	return $thumbnailSourcesHandler->generateSrcBase64(TYPE_NOTICE, $notice['notice_info']->notice_id);
}

function aff_no_image($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return notice::get_picture_url_no_image($notice['notice_info']->niveau_biblio, $notice['notice_info']->notice->typdoc);
}

function aff_vignette($param) {
	global $parser_environnement;
	global $opac_show_book_pics;

	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	$image='';
	if ($opac_show_book_pics == '1') {
	    $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
	    $url_image_ok = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $notice['notice_info']->notice_id);
		$image = "<img class='tpl_vignette' width='120' src='".$url_image_ok."' alt=''/>";
	}
	return $image;
}

function aff_img($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_image;
}

function aff_issn($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_isbn;
}

function aff_isbn($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_isbn;
}

function aff_ed1($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_ed1;
}

function aff_ed1_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "editeur", cf classes/editor.class.php
	// $param[0] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

    $params = [
        "type_object" => AUT_TABLE_PUBLISHERS,
        "num_object" => $notice['notice_info']->notice->ed1_id ?? 0
    ];
    $publisher_infos = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, $params);

	$aut_pp= new parametres_perso("publisher");
	$aut_pp->get_values($notice['notice_info']->notice->ed1_id);
	$values_pp = $aut_pp->values;
	$publisher_infos->parametres_perso=array();
	foreach ( $values_pp as $field_id => $vals ) {
		$publisher_infos->parametres_perso[$aut_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$aut_pp->t_fields[$field_id]["TITRE"];
		foreach ( $vals as $value ) {
			$publisher_infos->parametres_perso[$aut_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$aut_pp->get_formatted_output(array($value),$field_id);
		}
	}

	return _get_aut_infos($publisher_infos, $param[0]);
}

function aff_ed2($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_ed2;
}

function aff_ed2_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "editeur", cf classes/editor.class.php
	// $param[0] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

    $params = [
        "type_object" => AUT_TABLE_PUBLISHERS,
        "num_object" => $notice['notice_info']->notice->ed2_id ?? 0
    ];
    $publisher_infos = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, $params);

	$aut_pp= new parametres_perso("publisher");
	$aut_pp->get_values($notice['notice_info']->notice->ed2_id);
	$values_pp = $aut_pp->values;
	$publisher_infos->parametres_perso=array();
	foreach ( $values_pp as $field_id => $vals ) {
		$publisher_infos->parametres_perso[$aut_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$aut_pp->t_fields[$field_id]["TITRE"];
		foreach ( $vals as $value ) {
			$publisher_infos->parametres_perso[$aut_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$aut_pp->get_formatted_output(array($value),$field_id);
		}
	}

	return _get_aut_infos($publisher_infos, $param[0]);
}

function aff_year_publication($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_year;
}

function aff_date_publication($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->notice->date_parution;
}

/*

 * Minimaliste
<b>
	#header_link(#title(); #a_empty(#author();, / );,2);
</b>
<br />
<span class="resume">
	#resume();
</span>
<br />
<span class="source">
#if(#is_article();,
#nom_revue(); - #date_publication();,
#publisher(); #a_empty(#year_publication();, - ););
</span>

***********************
  Exemple pour l'ARNT
***********************
<h2>
#header_link(#title();,2);
</h2>
<br />
<p class="resume">
	#resume();
</p>
<p class="source">
#b_empty(#author();#b_empty(#author_1();, - );,<br />);
#if(#is_article();,
#nom_revue();#a_empty(#numero_bulletin();, - ); - #date_bulletin();,
#b_empty(#publisher();, - ); #year_publication(););
</p>


#gen_plus(
#header_link(#title(); #a_empty(#author();, / );,2);
,

<span class="resume">#resume();</span>
<br>
<span class="source">
#if(#is_article();,
#nom_revue(); - #date_publication();,
#publisher(); #a_empty(#year_publication();, - ););
</span>
);

 *
 */

function gere_global(){
	global $notice_data,$parser_environnement;

	if(!isset($notice_data[$parser_environnement['id_notice']]['notice_info'])) {
		$notice_data[$parser_environnement['id_notice']] ['notice_info']= new notice_info($parser_environnement['id_notice']);
	}
	return	$notice_data[$parser_environnement['id_notice']];
}

function gere_expl_bulletin($bulletin_id){
	global $opac_sur_location_activate;
	$bulletin = new stdClass();

	//Exemplaires
	$bulletin->memo_exemplaires=array();
	$requete = "select expl_id, expl_cb, expl_cote, expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment, expl_section, section_libelle, ";
	$requete.= "expl_owner, lender_libelle, expl_codestat, codestat_libelle, expl_date_retour, expl_date_depot, expl_note, pret_flag, expl_location, location_libelle, expl_prix ";
	if($opac_sur_location_activate) {
		$requete.= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
	}
	$requete.= "from exemplaires, docs_statut, docs_type, docs_section, docs_codestat, lenders, docs_location ";
	if($opac_sur_location_activate) {
		$requete.= "left join sur_location on surloc_num=surloc_id ";
	}
	$requete.= "where expl_bulletin=".$bulletin_id." and expl_statut=idstatut and expl_typdoc=idtyp_doc and expl_section=idsection and expl_owner=idlender and expl_codestat=idcode ";
	$requete.= "and expl_location=idlocation ";
	$resultat = pmb_mysql_query($requete);
	while (($ex = pmb_mysql_fetch_object($resultat))) {
		//Champs perso d'exemplaires
		$parametres_perso=array();
		$mes_pp=new parametres_perso("expl");
		if (!$mes_pp->no_special_fields) {
			$mes_pp->get_values($ex->expl_id);
			$values = $mes_pp->values;
			foreach ( $values as $field_id => $vals ) {
				$parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$mes_pp->t_fields[$field_id]["TITRE"];
				foreach ( $vals as $value ) {
					$parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$mes_pp->get_formatted_output(array($value),$field_id);
				}
			}
		}
		$ex->parametres_perso=$parametres_perso;
		$bulletin->memo_exemplaires[]=$ex;
	}

	//Exemplaires numériques
	$paramaff=array();
	$paramaff["mine_type"]=1;
	$bulletin->memo_explnum_assoc=show_explnum_per_notice(0, $bulletin_id,"",$paramaff);

	return $bulletin;
}

function aff_gen_plus($param) {
	global $parser_environnement;

	if(isset($param[2]) && $param[2]) {
		$max=" startOpen=\"Yes\"";
	} else {
		$max='';
	}
	return"
	<script src='./javascript/tablist.js'></script>

	<div class='row'></div>
	<div id='".$parser_environnement['id_notice']."' class='notice-parent'>
		<img src='./getgif.php?nomgif=plus' class='img_plus' name='imEx' id='".$parser_environnement['id_notice']."Img' title='".addslashes($msg['plus_detail'])."' alt='' onClick=\"expandBase('".$parser_environnement['id_notice']."', true); return false;\" >
		<span class='notice-heada'>
			".$param[0]."
		</span>
	</div>
	<div id='".$parser_environnement['id_notice'].""."Child' class='notice-child' style='margin-bottom:6px;display:none;width:94%' $max>
		".$param[1]."
	</div>
	";

}

function aff_b_empty($param) {
	if(!empty($param[0])) {
		return $param[0].($param[1] ?? '');
	}
	return '';
}

function aff_a_empty($param) {
	if(!empty($param[0])) {
		return ($param[1] ?? '').$param[0];
	}
	return '';
}

function aff_not_empty($param) {
	if(!empty($param[0])) {
		return $param[1];
	}
	return $param[0];
}

function aff_if_empty($param) {
	if(!empty($param[0])) {
		return $param[0];
	}
	return $param[1];
}

function aff_if($param) {
	if(!empty($param[0])) {
		return $param[1];
	}
	if(!empty($param[2])) {
	   return $param[2];
	}
	return '';
}

function aff_is_article($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if (($notice['notice_info']->niveau_biblio=="a")&&($notice['notice_info']->niveau_hierar==2)) return 1; else return 0;
}

function aff_is_serial($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if (($notice['notice_info']->niveau_biblio=="s")&&($notice['notice_info']->niveau_hierar==1)) return 1; else return 0;
}

function aff_is_mono($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if (($notice['notice_info']->niveau_biblio=="m")&&($notice['notice_info']->niveau_hierar==0)) return 1; else return 0;
}

function aff_is_bull($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if (($notice['notice_info']->niveau_biblio=="b")&&($notice['notice_info']->niveau_hierar==2)) return 1; else return 0;
}

function aff_isbd($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->isbd;
}

function aff_title($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_titre;
}

function aff_parallel_title($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_titre_parallele;
}

function aff_complement_title($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_complement_titre;
}

function aff_typdoc($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_typdoc;
}

function aff_icondoc($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_icondoc;
}

function aff_iconcart($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return $notice['notice_info']->memo_iconcart;
}

function aff_auteur_principal($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_auteur_principal;
}

function aff_auteur_autre($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if(isset($param[0]) && $param[0]) {
	    $sep = $param[0];
	} else {
	    $sep= " ; ";
	}
	if(isset($param[1]) && $param[1]){
		$aut = array();
		for($i=0 ; $i < $param[1] ; $i++){
			if ($notice['notice_info']->memo_auteur_autre_tab[$i]) $aut[]=$notice['notice_info']->memo_auteur_autre_tab[$i];
		}
		if(count($notice['notice_info']->memo_auteur_autre_tab) > $param[1]) $aut[] = "et al.";
		return implode($sep,$aut);
	}
	return implode($sep,$notice['notice_info']->memo_auteur_autre_tab);
}

function aff_auteur_secondaire($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if(isset($param[0]) && $param[0]) {
	    $sep = $param[0];
	} else {
	    $sep= " ; ";
	}
	if(isset($param[1]) && $param[1]){
		$aut = array();
		for($i=0 ; $i < $param[1] ; $i++){
			if ($notice['notice_info']->memo_auteur_secondaire_tab[$i]) $aut[]=$notice['notice_info']->memo_auteur_secondaire_tab[$i];
		}
		if(count($notice['notice_info']->memo_auteur_secondaire_tab) > $param[1]) $aut[] = "et al.";
		return implode($sep,$aut);
	}
	return implode($sep,$notice['notice_info']->memo_auteur_secondaire_tab);
}

function aff_auteurs($param) {
	// $param[0] = 0=principal seul, 1=principal+autres, 2=tous, 3=autres, 4=secondaires, 5=autres+secondaires
	// $param[1] = nombre maxi d'auteurs à afficher
	// $param[2] = séparateur entre auteurs
	// $param[3] = séparateur entre principal/autres/secondaires
	// $param[4] = afficher la fonction : 0=non, 1=toujours
	// $param[5] = afficher "et al." si plus d'auteurs que le maxi
	// $param[6] = $tpl_num ???
	// $param[7] = filtre sur code fonction
	// $param[8] = séparateur avant le dernier auteur si inférieur au maxi
	return interpreter_notice_tpl::exec($param, 'aff_authors');
}

function aff_resume($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return nl2br($notice['notice_info']->notice->n_resume);
}
function aff_contenu($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return nl2br($notice['notice_info']->notice->n_contenu);
}
function aff_note($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	return nl2br($notice['notice_info']->notice->n_gen);
}

function aff_categories($param) {
	// $param[0] = 0=tous thésaurus, sinon thesaurus id in $param[0]
	// $param[1] = séparateur entre categories
	// $param[2] = séparateur entre thesaurus
	// $param[3] = langue à prendre en compte
	// $param[4] = afficher le nom du ou des thesaurus en entête, 0 ou 1
	// $param[5] = nb maxi de catégories à afficher

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	//Descripteurs
	if (isset($param[0]) && $param[0]>0) $restrict_thes=" and catdef.num_thesaurus in (".$param[0].") ";
	else $restrict_thes="";
	if(!isset($param[3])) $param[3] = '';
	$requete="SELECT libelle_thesaurus as thesnom, if(catlg.libelle_categorie is not null,catlg.libelle_categorie,catdef.libelle_categorie) as categnom FROM notices_categories left join categories catlg on (catlg.num_noeud = notices_categories.num_noeud and catlg.langue='".$param[3]."') left join categories catdef on (catdef.num_noeud = notices_categories.num_noeud), thesaurus thesdef  where catdef.num_thesaurus=thesdef.id_thesaurus and notcateg_notice='".$parser_environnement['id_notice']."' and (catdef.langue=thesdef.langue_defaut or catdef.langue is null) $restrict_thes ORDER BY libelle_thesaurus, ordre_categorie";
	if (!empty($param[5])) $requete .= " limit 0,".$param[5] ;
	$resultat=pmb_mysql_query($requete);
	$thes_conserve="";$res="";
	$juste_apresthes=true;
	while (($cat = pmb_mysql_fetch_object($resultat))) {
		if ($thes_conserve!=$cat->thesnom) {
			if (!$res && isset($param[4]) && $param[4]) $res="[".$cat->thesnom."] ";
			elseif ($param[4]) $res.=$param[2]."[".$cat->thesnom."] ";
			elseif ($res) $res.=$param[2];
			$thes_conserve=$cat->thesnom;
			$juste_apresthes=true;
		}
		if ($juste_apresthes) {
			$res.=$cat->categnom;
			$juste_apresthes=false;
		} elseif ($res) $res.=(isset($param[1]) ? $param[1] : '').$cat->categnom;
		else $res.=$cat->categnom;
	}
	return $res;

}

function aff_indexint($param) {
	// $param[0] = séparateur
	// $param[1] = afficher le plan de classement ? 0 ou 1
	// $param[2] = séparateur
	// $param[3] = afficher l'indexation indexint_name
	// $param[4] = séparateur
	// $param[5] = afficher le libellé indexint_comment

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	// Indexation décimale
	$requete="SELECT name_pclass, indexint_name, indexint_comment FROM notices, indexint, pclassement where notice_id='".$parser_environnement['id_notice']."' and id_pclass=num_pclass and indexint_id=indexint";
	$resultat=pmb_mysql_query($requete);
	$res = '';
	if ($int = pmb_mysql_fetch_object($resultat)) {
		$res = (isset($param[0]) ? $param[0] : '');
		if (isset($param[1]) && $param[1]==1) $res.=$int->name_pclass;
		$res.= (isset($param[2]) ? $param[2] : '');
		if (isset($param[3]) && $param[3]==1) $res.=$int->indexint_name;
		$res.= (isset($param[4]) ? $param[4] : '');
		if (isset($param[5]) && $param[5]==1) $res.=$int->indexint_comment;
	}
	return $res;
}

function aff_header_link($param) {
	global $pmb_opac_url;

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	if(!isset($param[1])) $param[1] = 0;
	switch($param[1]){
		case 2:
			if($notice['notice_info']->notice->lien) {
				$libelle="<a href=\"".$notice['notice_info']->notice->lien."\">".$param[0];
				$libelle.= "<img src=\"".get_url_icon('globe.gif')."\" class='align_middle' ";
				$libelle.= "alt=\"". $notice['notice_info']->notice->eformat. "\" title=\"". $notice['notice_info']->notice->eformat ."\">";
				$libelle.="</a>";
			} else	{
				$libelle="<a href=\"".$pmb_opac_url."?lvl=notice_display&id=".$parser_environnement['id_notice'].bannette::get_url_connexion_auto()."\">$param[0]</a>";
			}
		break;
		default:
			$libelle="<a href=\"".$pmb_opac_url."?lvl=notice_display&id=".$parser_environnement['id_notice'].bannette::get_url_connexion_auto()."\">$param[0]</a>";
		break;
	}
	return $libelle;
}

function aff_nom_revue($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return "Voila";
	$notice=gere_global();
	return $notice["notice_info"]->serial_title;
}

function aff_date_bulletin($param) {
    global $parser_environnement;
    if(!$parser_environnement['id_notice']) return "1er janvier 1970";
    $notice=gere_global();
    switch ($param[0]){
        case "1":
            return $notice["notice_info"]->bulletin_date_date;
            break;
        case "2":
            return $notice["notice_info"]->bulletin_mention_date;
            break;
        default :
            if ($notice["notice_info"]->bulletin_mention_date) $format=$notice["notice_info"]->bulletin_mention_date." (%s)"; else $format="%s";
            return sprintf($format,$notice["notice_info"]->bulletin_date_date);
            break;
    }
}

function aff_bulletin_id($param) {
    global $parser_environnement;
    if(!$parser_environnement['id_notice']) return '';
    $notice=gere_global();
    if ($notice["notice_info"]->notice->niveau_biblio == 'a') {
        $requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
        $result = pmb_mysql_query($requete);
        if ($result && pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            return $row->analysis_bulletin;
        }
    }
    return '';
}

function aff_numero_bulletin($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return "1er janvier 1970";
	$notice=gere_global();

	return $notice["notice_info"]->bulletin_numero;
}

function aff_expl_num_number($param) {
	// $param[0] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui)
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return "1er janvier 1970";

	if(is_array($param) && count($param) && ($param[0] ==1) && !explnum_test_rights($parser_environnement['id_notice'])) return '';

	$notice=gere_global();

	return $notice["notice_info"]->memo_explnum_assoc_number;
}

function aff_expl_num($param) {
	// $param[0] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui)
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return "1er janvier 1970";

	if(is_array($param) && count($param) && ($param[0] ==1) && !explnum_test_rights($parser_environnement['id_notice'])) return '';

	$notice=gere_global();

	return $notice["notice_info"]->memo_explnum_assoc;
}

/*
 * $param[0] : nombre d'élément maximum
 * $param[1] : séparateur
 * $param[2] : champ demandé
 * $param[3] : critères de filtrage des documents numériques. format accepté: explnum_ext=pdf ou [explnum_ext=pdf;odt] ou [explnum_ext=pdf;odt,explnum_docnum_statut=0;1]
 * liste des champs possible (param 2 et 3) : explnum_id, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
 * $param[4] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui) //Utilisé à l'Opac
 */
function aff_expl_num_by_field($param){
	global $parser_environnement;

	$res=$nb_elem=$sep=$field="";
	if(isset($param) && is_array($param)){
	    $nb_elem = intval($param[0]);
		$sep=$param[1];
		$field=trim($param[2]);
		$filters=array();
		if(isset($param[3]) && $param[3] != ""){
			$matches=array();
			if(!preg_match("#\[|\]#",$param[3])){
				$filters[]=$param[3];
			}elseif(preg_match("/^\[(.*?)\]$/",$param[3],$matches)){
				$filters=explode(",",$matches[1]);
			}
			if(count($filters)){
				$tmp=array();
				foreach ( $filters as $key => $value ) {
					$tmp2=explode("=",$value);
					if(count($tmp2) == 2){
						$tmp[$tmp2[0]]=explode(";",$tmp2[1]);
					}else{
						return $res;//Le format n'est pas correct
					}
				}
				$filters=$tmp;
			}
		}
	}

	if(($sep !== "") && $field){
		$tot=false;
		if(!$nb_elem) $tot=true;
		$notice=gere_global();

		$is_article = false;
		if ($notice["notice_info"]->notice->niveau_biblio == 'a') {
			//on va chercher le bulletin de l'article
			$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
			$result = pmb_mysql_query($requete);
			if ($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$bulletin_id = $row->analysis_bulletin;
			} else {
				return '';
			}

			//on va chercher les exemplaires du bulletin
			$bulletin = gere_expl_bulletin($bulletin_id);
			$is_article = true;
		}

		if ($is_article) {
			$object = $bulletin;
			$object->memo_explnum = $notice["notice_info"]->memo_explnum;
		} else {
			$object = $notice["notice_info"];
		}

		if(count($object->memo_explnum)){
			for ($i = 0 ; $i < count($object->memo_explnum); $i++) {
				$explnum_tabl = (array) $object->memo_explnum[$i];
				$pass=true;
				if ($param[4]==1) {
					if (!explnum_test_rights_per_id($explnum_tabl['explnum_id'])) {
						$pass=false;
					}
				}
				if(count($filters)){
					foreach ( $filters as $key => $value ) {
						if(!$explnum_tabl[$key] || !in_array($explnum_tabl[$key],$value)){
							$pass=false;
							break;
						}
					}
				}
				if($pass){
					$val=trim($explnum_tabl[$field]);
					if($val !== ""){
						if($res)$res.=$sep;
						$res.=$val;
						if(!$tot){
							$nb_elem--;
							if($nb_elem <=0){
								break;
							}
						}
					}else{
						$val=$explnum_tabl["parametres_perso"][$field];
						if(is_array($val)){
							$need_break=false;
							foreach($val["VALUE"] as $value){
								if($res)$res.=$sep;
								$res.=$value;
								if(!$tot){
									$nb_elem--;
									if($nb_elem <=0){
										$need_break=true;
										break;
									}
								}
							}
							if($need_break){
								break;
							}
						}
					}
				}
			}
		}
	}

	return $res;
}

function aff_expl_num_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "explnum", cf classes/explnum.class.php
	// $param[0] = nb maxi d'explnum à afficher
	// $param[1] = séparateur entre les explnum
	// $param[2] = 0 : tous les documents, 1 : documents sans statut spécifique, 2 : documents avec statut spécifique
	// $param[3] = template
	// Pour afficher la vignette : <img src='./vig_num.php?explnum_id={explnum_id}' />
	// $param[4] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui)
	// $param[5] = Par quel(s) mimetype doit-on filtrer ? Si vide pas de filtre, si plusieurs séparer par \,
	// $param[6] = Par quel(s) identifiants de statut de document numérique doit-on filtrer ? Si vide pas de filtre, si plusieurs séparer par \,
	global $parser_environnement;
	global $opac_explnum_order;
	if(!$parser_environnement['id_notice']) return "1er janvier 1970";
	if(is_array($param) && (count($param) >= 6) && ($param[4] == 1) && !explnum_test_rights($parser_environnement['id_notice'])) return '';
	$notice=gere_global();

	$mime_type="";
	if(isset($param[5]) && $tmp=trim($param[5])){
		$mime_type="'".str_replace(',',"','",$tmp)."'";
	}

	$query = "SELECT explnum_id FROM explnum WHERE explnum_notice = '".$parser_environnement['id_notice']."'";

	//S'il s'agit d'une notice de bulletin, l'exemplaire numérique est relié au bulletin
	if($notice["notice_info"]->niveau_biblio=="b" && $notice["notice_info"]->niveau_hierar=="2"){
		$result = pmb_mysql_query("SELECT bulletin_id FROM bulletins WHERE num_notice= '".$parser_environnement['id_notice']."'");
		if($result && pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$query = "SELECT explnum_id FROM explnum WHERE explnum_bulletin = '".$row->bulletin_id."'";
		}
	}

	if($mime_type){
		$query.=" AND explnum_mimetype IN(".$mime_type.")";
	}
	$id_statut=(isset($param[6]) ? trim($param[6]) : 0);
	if($id_statut){
		$query .= " AND explnum_docnum_statut IN (".$id_statut.")";
	}
	$query .= " ORDER BY ".$opac_explnum_order;
	$nb_max_elements = intval($param[0]);
    $tab_explnum = array();
	$result = pmb_mysql_query($query);
	if ($result && pmb_mysql_num_rows($result)) {
		while ($explnum = pmb_mysql_fetch_object($result)) {
			$affiche = false;
			if ($param[4]==1) {
			if (explnum_test_rights_per_id($explnum->explnum_id)) {
					$affiche = true;
				}
			} else {
				$affiche = true;
			}
			if ($affiche) {
				$explnum_infos = new explnum($explnum->explnum_id);
				if (!$param[2] || (($param[2] == 1) && (!$explnum_infos->explnum_statut)) || (($param[2] == 2) && ($explnum_infos->explnum_statut))) {
					if(!$nb_max_elements || count($tab_explnum)<$nb_max_elements){
						$tab_explnum[] = _get_aut_infos($explnum_infos, $param[3]);
					}
				}
			}
		}
	}
	if (count($tab_explnum)) return implode($param[1],$tab_explnum);
	return '';
}

function aff_expl_number($param) {
	global $parser_environnement;
	$res=0;
	if(!$parser_environnement['id_notice']) return $res;
	$notice=gere_global();

	if(count($notice['notice_info']->memo_exemplaires)) {
		$res = count($notice['notice_info']->memo_exemplaires);
	}
	return $res;
}

/*
 * $param[0] : id localisation format accepté: 1 ou [1] ou [1;2;3]
 * $param[1] : colonne à afficher format accepté: expl_cote ou [expl_cote] ou [expl_cote;location_libelle]
 * liste des champs possible: expl_id, expl_cb,expl_cote,expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment, expl_section, section_libelle, expl_owner, lender_libelle, codestat_libelle, expl_date_retour, expl_date_depot, expl_note, pret_flag, location_libelle
 */
function aff_expl($param) {
	global $parser_environnement;
	global $opac_sur_location_activate;
	if(!$parser_environnement['id_notice']) return '';

	$notice=gere_global();

	$is_article = false;
	if ($notice["notice_info"]->notice->niveau_biblio == 'a') {
		//on va chercher le bulletin de l'article
		$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
		$result = pmb_mysql_query($requete);
		if ($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$bulletin_id = $row->analysis_bulletin;
		} else {
			return '';
		}

		//on va chercher les exemplaires du bulletin
		$bulletin = gere_expl_bulletin($bulletin_id);
		$is_article = true;
	}

	if ($is_article) {
		$object = $bulletin;
	} else {
		$object = $notice["notice_info"];
	}

	$id_loc=array();
	$list_colonne=array();
	if(isset($param) && is_array($param)){
		if($param[0] != ""){
			$matches=array();
			if(preg_match("/^\[([0-9;]+)\]$/",$param[0],$matches)){
				$id_loc=explode(";",$matches[1]);
			}elseif(preg_match("/^[0-9]+$/",$param[0])){
				$id_loc[]=$param[0];
			}
		}

		if(isset($param[1]) && $param[1] != ""){
			$matches=array();
			if(preg_match("/^\[(.*?)\]$/",$param[1],$matches)){
				$list_colonne=explode(";",$matches[1]);
			}else{
				$list_colonne[]=$param[1];
			}
		}else{
			$list_colonne=array("tdoc_libelle","surloc_libelle","location_libelle","section_libelle","expl_cote","expl_cb","statut_libelle");
		}

	}

	$res="";
	for ($i = 0 ; $i < count($object->memo_exemplaires); $i++) {
		if(!count($id_loc) || in_array($object->memo_exemplaires[$i]->expl_location,$id_loc)){
			$res.="<tr>";
			$expl_tabl = (array) $object->memo_exemplaires[$i];
			foreach ( $list_colonne as $value ) {
				if($value == "surloc_libelle" && !$opac_sur_location_activate){
					continue;
				}
				$res.="<td id='".$value."'>";
   				if($expl_tabl[$value] !== ""){
   					$res.=$expl_tabl[$value];
   				}else{
   					$res.="&nbsp;";
   				}
   				$res.="</td>";
			}
			$res.="</tr>";
		}
	}
	if($res){
		$res="<table>".$res."</table>";
	}
	return $res;
}

/*
 * $param[0] : nombre d'élément maximum
 * $param[1] : séparateur
 * $param[2] : champ demandé
 * $param[3] : critères de filtrage des exemplaires. format accepté: expl_location=1 ou [expl_location=1;2] ou [expl_location=1;2,expl_section=2;3]
 * liste des champs possible (param 2 et 3) : expl_id, expl_cb,expl_cote,expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment, expl_section, section_libelle, expl_owner, lender_libelle, codestat_libelle, expl_date_retour, expl_date_depot, expl_note, pret_flag, location_libelle
 * $param[4] : unique (0 = tous, 1 = dédoublonnage)
 */
function aff_expl_by_field($param){
	global $parser_environnement;

	$res=$nb_elem=$sep=$field="";
	if(isset($param) && is_array($param)){
	    $nb_elem = intval($param[0]);
		$sep=$param[1];
		$field=trim($param[2]);
		$filters=array();
		if(!empty($param[3])){
			$matches=array();
			if(!preg_match("#\[|\]#",$param[3])){
				$filters[]=$param[3];
			}elseif(preg_match("/^\[(.*?)\]$/",$param[3],$matches)){
					$filters=explode(",",$matches[1]);
			}
			if(count($filters)){
				$tmp=array();
				foreach ( $filters as $key => $value ) {
	       			$tmp2=explode("=",$value);
	       			if(count($tmp2) == 2){
	       				$tmp[$tmp2[0]]=explode(";",$tmp2[1]);
	       			}else{
	       				return $res;//Le format n'est pas correct
	       			}
				}
				$filters=$tmp;
			}
		}
	}

	if(($sep !== "") && $field){
		$tot=false;
		if(!$nb_elem) $tot=true;
		$notice=gere_global();

		$is_article = false;
		if ($notice["notice_info"]->notice->niveau_biblio == 'a') {
			//on va chercher le bulletin de l'article
			$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
			$result = pmb_mysql_query($requete);
			if ($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$bulletin_id = $row->analysis_bulletin;
			} else {
				return '';
			}

			//on va chercher les exemplaires du bulletin
			$bulletin = gere_expl_bulletin($bulletin_id);
			$is_article = true;
		}

		if ($is_article) {
			$object = $bulletin;
		} else {
			$object = $notice["notice_info"];
		}

		if(count($object->memo_exemplaires)){
			for ($i = 0 ; $i < count($object->memo_exemplaires); $i++) {
				$expl_tabl = (array) $object->memo_exemplaires[$i];
				$pass=true;
				if(count($filters)){
				    foreach ( $filters as $key => $value ) {
				        if(isset($expl_tabl[$key]) && is_array($expl_tabl[$key])) {
				            $found = false;
				            foreach ($expl_tabl[$key] as $expl_tab_value) {
				                if(in_array($expl_tab_value,$value)){
				                    $found=true;
				                    break;
				                }
				            }
				            if(!$found) {
				                $pass=false;
				                break;
				            }
				        } elseif(!$expl_tabl[$key] || !in_array($expl_tabl[$key],$value)){
				            $pass=false;
				            break;
				        }
				    }
				}
				if($pass){
					$val=trim($expl_tabl[$field]);
					if($val !== ""){
						if($res)$res.=$sep;
						$res.=$val;
						if(!$tot){
							$nb_elem--;
							if($nb_elem <=0){
								break;
							}
						}
					}else{
						$val=$expl_tabl["parametres_perso"][$field];
						if(is_array($val)){
							$need_break=false;
							foreach($val["VALUE"] as $value){
								if($res)$res.=$sep;
								$res.=$value;
								if(!$tot){
									$nb_elem--;
									if($nb_elem <=0){
										$need_break=true;
										break;
									}
								}
							}
							if($need_break){
								break;
							}
						}
					}
				}
			}
		}
		if($res && !empty($param[4])){
			$array_unique = array_unique(explode($sep, $res));
			$res = implode($sep, $array_unique);
		}
	}

	return $res;
}

function aff_expl_with_tpl($param){
	global $fonction_auteur,$parser_environnement;
	// $param[0] : nombre d'élément maximum
	// $param[1] : séparateur entre exemplaires
	// $param[2] = template d'affichage de l'autorité
	// $param[3] : critères de filtrage des exemplaires. format accepté: expl_location=1 ou [expl_location=1;2] ou [expl_location=1;2,expl_section=2;3]
	// $param[4] : unique (0 = tous, 1 = dédoublonnage)
	if(isset($param) && is_array($param)){
	    $nb_elem = intval($param[0]);
		$filters=array();
		if($param[3] != ""){
			$matches=array();
			if(!preg_match("#\[|\]#",$param[3])){
				$filters[]=$param[3];
			}elseif(preg_match("/^\[(.*?)\]$/",$param[3],$matches)){
				$filters=explode(",",$matches[1]);
			}
			if(count($filters)){
				$tmp=array();
				foreach ( $filters as $key => $value ) {
					$tmp2=explode("=",$value);
					if(count($tmp2) == 2){
						$tmp[$tmp2[0]]=explode(";",$tmp2[1]);
					}else{
						return '';//Le format n'est pas correct
					}
				}
				$filters=$tmp;
			}
		}
	}

	if($param[2]){
		$tot=false;
		if(!$nb_elem) $tot=true;
		$notice=gere_global();
		$is_article = false;
		if ($notice["notice_info"]->notice->niveau_biblio == 'a') {
			//on va chercher le bulletin de l'article
			$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
			$result = pmb_mysql_query($requete);
			if ($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$bulletin_id = $row->analysis_bulletin;
			} else {
				return '';
			}

			//on va chercher les exemplaires du bulletin
			$bulletin = gere_expl_bulletin($bulletin_id);
			$is_article = true;
		}

		if ($is_article) {
			$object = $bulletin;
		} else {
			$object = $notice["notice_info"];
		}
		$retour=array();
		if(count($object->memo_exemplaires)){
			for ($i = 0 ; $i < count($object->memo_exemplaires); $i++) {
				$expl_tabl = (array)$object->memo_exemplaires[$i];
				$expl_object = $object->memo_exemplaires[$i];

				$pass=true;
				if(count($filters)){
				    foreach ( $filters as $key => $value ) {
				        if(isset($expl_tabl[$key]) && is_array($expl_tabl[$key])) {
				            $found = false;
				            foreach ($expl_tabl[$key] as $expl_tab_value) {
				                if(in_array($expl_tab_value,$value)){
				                    $found=true;
				                    break;
				                }
				            }
				            if(!$found) {
				                $pass=false;
				                break;
				            }
				        } elseif(!$expl_tabl[$key] || !in_array($expl_tabl[$key],$value)){
				            $pass=false;
				            break;
				        }
				    }
				}
				if($pass){
					$retour[]=_get_aut_infos($expl_object,$param[2]);
					if(!$tot){
						$nb_elem--;
						if($nb_elem <=0){
							break;
						}
					}
				}
			}
		}
	}
	if (count($retour)) {
		if(!empty($param[4])){
			$retour = array_unique($retour);
		}
		return implode($param[1],$retour);
	}
	return '';
}

/*
 * $param[0] : pattern
 * $param[1] : chaine
 */
function aff_extract_path($param){
	$output=array();
	if(preg_match("\"".$param[0]."\"",$param[1],$output)){;
		return $output[1];
	}else return '';
}

/*
 * $param[0] : chaine
 * $param[1] : separator
 * $param[2] : position
 */
function aff_extract_explode($param){
    $output=array();
    if (!empty($param[0])) {
        $output = explode($param[1], $param[0]);
        if (!isset($param[2])) {
            $param[2] = 0;
        }
        return $output[$param[2]];
    }else {
        return '';
    }
}

/*
 * $param[0] : format
 * $param[1] : date
 */
function aff_format_date($param){
	if ($param[1]=='today') {
		$param[1] = date('Y-m-d');
	}
	//si c'est pas une date potable, on arrete là...
	if(!preg_match(getDatePattern(),$param[1]) && !preg_match(getDatePattern("short"),$param[1]) && !preg_match(getDatePattern("year"),$param[1])){
		return $param[1];
	}
	$date = detectFormatDate($param[1]);
	$year = substr($date,0,4);
	$month = substr($date,5,2);
	$day = substr($date,8,2);
	return date($param[0],mktime(0,0,0,$month,$day,$year));
}

/*
 * $param[0] : mois
 */
function aff_month_in_letters($param){
	global $msg;

	if (isset($param[0])) {
		$param[0] = (int)$param[0];
		if (($param[0]>0) && ($param[0]<13)) {
			//Les mois sont dans les msg 1006 à 1017
			return $msg[($param[0]+1005)];
		}
	} else {
		return '';
	}
}

function aff_trim($param){
	return trim($param[0]);
}

function aff_strpos($param){
    if(isset($param[1])){
        if(isset($param[2])) {
            return strpos($param[0],$param[1],$param[2]);
        }
        return strpos($param[0],$param[1]);
    }
    return "";
}

function aff_substr($param){
    if(isset($param[1])){
        if(isset($param[2])) {
            return substr($param[0],$param[1],$param[2]);
        }
        return substr($param[0],$param[1]);
    }
	return "";
}

function aff_limitstring($param){
	global $charset;

	if(isset($param[0])){
		if(empty($param[1])){
			$param[1] = 50;
		}
		if(empty($param[2])){
			$param[2] = "[...]";
		}
		$param[0] = html_entity_decode ($param[0], ENT_NOQUOTES, $charset);
		if(pmb_strlen($param[0])> $param[1]){
			$param[0] = pmb_substr($param[0],0,($param[1] - pmb_strlen($param[2]))).$param[2];
		}
		return $param[0];
	}
	return "";
}

function aff_lastchr($param){
	return substr($param[0],strlen($param[0])-1);
}

/*
 * $param[0],$param[1] : chaines à comparer
 * $param[2] : valeur si égale
 * $param[3] : valeur si différente
 *
 */
function aff_ifequal($param){
	if($param[0] == $param[1]) return $param[2];
	else return $param[3];
}

function aff_strtoupper($param){
	return pmb_strtoupper($param[0]);
}

function aff_strtolower($param){
	return pmb_strtolower($param[0]);
}

function aff_ucfirst($param){
	return ucfirst($param[0]);
}

function aff_ucwords($param){
	if(!empty($param[1])) {
		return ucwords($param[0], $param[1]);
	}
	return ucwords($param[0]);
}

/*
 * $param[0] : chaines à découper
 * $param[1] : séparateur entre les initiales
 * $param[2] : caractère final
 *
 */
 function aff_initiale($param){
	$retour = '';
	if($param[0]) {
		$mots = preg_split('`[ -\.]+`', $param[0]);
		if (count($mots)) {
			foreach($mots as $k=>$mot){
				$mots[$k] = pmb_substr($mot,0,1);
			}
			$retour = implode($param[1],$mots).(isset($param[2]) ? $param[2] : '');
		}
	}
	return $retour;
}

function aff_publisher_name($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_ed1_name;
}

function aff_publisher_place($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_ed1_place;
}

function aff_publisher2_name($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_ed2_name;
}

function aff_publisher2_place($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_ed2_place;
}

function aff_mention_edition($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->memo_mention_edition;
}

function aff_get_notice_tpl($param){
	global $parser_environnement;
	global $deflt2docs_location;
	$id_notice = $parser_environnement['id_notice'];
	$template_notice = notice_tpl_gen::get_instance($parser_environnement['id_template']);
	$notice = $template_notice->build_notice($param[0],$deflt2docs_location,true);
	$parser_environnement['id_notice']=$id_notice;
	return $notice;
}

function aff_get_parents_in_tpl($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$result="";
	if (count($notice['notice_info']->memo_notice_mere)) {
		foreach($notice['notice_info']->memo_notice_mere as $parent){
			$result.= " In : ".aff_get_notice_tpl(array($parent));
		}
	}
	return $result;
}

function aff_get_childs_in_tpl($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$result="";
	$notice['notice_info']->get_memo_notice_fille();
	if (count($notice['notice_info']->memo_notice_fille)) {
		foreach($notice['notice_info']->memo_notice_fille as $child){
			$result.= aff_get_notice_tpl(array($child));
		}
	}
	return $result;
}

function aff_authors_by_type($param){
	// $param[0] = 0=principal seul, 1=principal+autres, 2=tous, 3=autres, 4=secondaires, 5=autres+secondaires
	// $param[1] = nombre maxi d'auteurs à afficher
	// $param[2] = séparateur entre auteurs
	// $param[3] = séparateur entre principal/autres/secondaires
	// $param[4] = afficher la fonction : 0=non, 1=toujours
	// $param[5] = afficher "et al." si plus d'auteurs que le maxi
	// $param[6] = 70=physique, 71=collectivités, 72=congrès (séparé parune virgule...)
	// $param[7] = séparateur avant le dernier auteur si inférieur au maxi
	return interpreter_notice_tpl::exec($param, 'aff_authors_by_type');
}

function aff_authors_by_type_dir($param){
	// $param[0] = 0=principal seul, 1=principal+autres, 2=tous, 3=autres, 4=secondaires, 5=autres+secondaires
	// $param[1] = nombre maxi d'auteurs à afficher
	// $param[2] = séparateur entre auteurs
	// $param[3] = séparateur entre principal/autres/secondaires
	// $param[4] = afficher la fonction : 0=non, 1=toujours
	// $param[5] = afficher "et al." si plus d'auteurs que le maxi
	// $param[6] = 70=physique, 71=collectivités, 72=congrès (séparé parune virgule...)
	// $param[7] = séparateur avant le dernier auteur si inférieur au maxi
    return interpreter_notice_tpl::exec($param, 'aff_authors_by_type_dir');
}

function aff_permalink($param){
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice['notice_info']->permalink;
}

function aff_authors_by_type_with_tpl($param){
	// $param[0] = 0=principal seul, 1=principal+autres, 2=tous, 3=autres, 4=secondaires, 5=autres+secondaires
	// $param[1] = nombre maxi d'auteurs à afficher
	// $param[2] = séparateur entre auteurs
	// $param[3] = séparateur entre principal/autres/secondaires
	// $param[4] = afficher la fonction : 0=non, 1=toujours
	// $param[5] = afficher "et al." si plus d'auteurs que le maxi
	// $param[6] = 70=physique, 71=collectivités, 72=congrès (séparé parune virgule...)
	// $param[7] = template d'affichage de l'autorité
	// $param[8] = filtre sur code fonction
	// $param[9] = séparateur avant le dernier auteur si inférieur au maxi
	
	return interpreter_notice_tpl::exec($param, 'aff_authors_by_type_with_tpl');
}

function _get_aut_infos($aut,$tpl){
	global $pmb_perso_sep;

	$matches=array();
	if($tpl != "" && preg_match_all("/{([^}]*)}/",$tpl,$matches)){
		for ($i=0 ; $i<count($matches[1]) ; $i++){
			$out=array();
            $value = $aut->{$matches[1][$i]};
            if(!empty($value)){
                $tpl = str_replace($matches[0][$i],$value,$tpl);
			}elseif(preg_match('`^p_perso\|(.+)\|(.+)$`',$matches[1][$i],$out)){
				if($out[2]=="VALUE"){
					if(is_array($aut->parametres_perso[$out[1]]["VALUE"])){
						$tpl = str_replace($matches[0][$i],implode($pmb_perso_sep,$aut->parametres_perso[$out[1]]["VALUE"]),$tpl);
					}else{
						$tpl = str_replace($matches[0][$i],"",$tpl);
					}
				}elseif($out[2]=="TITRE"){
					$tpl = str_replace($matches[0][$i],$aut->parametres_perso[$out[1]]["TITRE"],$tpl);
				}else{
					$tpl = str_replace($matches[0][$i],"",$tpl);
				}
			}else{
				$tpl = str_replace($matches[0][$i],"",$tpl);
			}
		}
	}else{
		return $aut->isbd_entry;
	}
	return $tpl;
}

function aff_parents_authors_by_type_with_tpl($param){
	global $parser_environnement;
	global $fonction_auteur;

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();

	$param_array = explode(",",$param[6]);
	$param[6] = implode("','",$param_array);
	$return = "";
	if(count($notice['notice_info']->memo_notice_mere)) {
		foreach($notice['notice_info']->memo_notice_mere as $noticeId){
			$aut = array();
			$aut1 = array();
			$aut2 = array();
			$rqt_count="select count(*) as nb from responsability, authors
					where responsability_notice='".$noticeId."'
						and responsability_author=author_id ";
						if($param[6]!= "") $rqt_count.=" and author_type in('".$param[6]."') ";
						$rqt_count .= "and responsability_type<='".$param[0]."'  ";
			$res_sql_count = pmb_mysql_query($rqt_count);
			$res_count=pmb_mysql_fetch_object($res_sql_count);
			$rqt = "select author_id, responsability_fonction, responsability_type
					from responsability, authors
					where responsability_notice='".$noticeId."'
						and responsability_author=author_id ";
						if($param[6]!= "") $rqt.=" and author_type in('".$param[6]."') ";
						$rqt .= "and responsability_type<='".$param[0]."'
					order by responsability_type, responsability_ordre " ;
			if ($param[1]>0) $rqt .= " limit 0,".$param[1] ;

			$res_sql = pmb_mysql_query($rqt);
			while ($authors=pmb_mysql_fetch_object($res_sql)) {
				$aut_detail=new auteur($authors->author_id);
				if ($authors->responsability_fonction && $param[4]==1) {
					$aut_detail->function = $fonction_auteur[$authors->responsability_fonction];
				}
				$parser=parse_format::get_instance('notice_tpl.inc.php');

				if ($authors->responsability_type==0){
					$parser->cmd =_get_aut_infos($aut_detail,$param[7]);
					$aut[]=$parser->exec_cmd();
				}
				if ($authors->responsability_type==1){
					$parser->cmd =_get_aut_infos($aut_detail,$param[7]);
					$aut1[]=$parser->exec_cmd();
				}
				if ($authors->responsability_type==2){
					$parser->cmd =_get_aut_infos($aut_detail,$param[7]);
					$aut2[]=$parser->exec_cmd();
				}
			}
			if (count($aut1)) $aut[]=implode($param[2],$aut1);
			if (count($aut2)) $aut[]=implode($param[2],$aut2);
			if ($param[1]!= 0 && $param[5] && $res_count->nb>$param[1]) $aut[]="et al.";
			if (count($aut)){
				if($return != "") $return.=" / ";
				$return.= implode($param[3],$aut);
			}
		}
	}
	return $return;
}

function aff_parents_title($param){
	global $parser_environnement;
	$result = "";
	if(!isset($param[0]) || !$param[0]) $separator = " / ";
	else $separator = $param[0];

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();
	$notice['notice_info']->fetch_notices_parents();
	for($i=0 ; $i<count($notice['notice_info']->notices_parents) ; $i++){
		if($result != "") $result.=$separator;
		$result.= $notice['notice_info']->notices_parents[$i]->get_memo_titre();
	}
	return $result;
}

function aff_parents_page($param){
	global $parser_environnement;
	$result = "";
	if(!isset($param[0]) || !$param[0]) $separator = " / ";
	else $separator = $param[0];

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();
	$notice['notice_info']->fetch_notices_parents();
	for($i=0 ; $i<count($notice['notice_info']->notices_parents) ; $i++){
		if($result != "") $result.=$separator;
		$result.= $notice['notice_info']->notices_parents[$i]->get_memo_notice()->npages;
	}
	return $result;
}

function aff_parents_mention_edition($param){
	global $parser_environnement;
	$result = "";
	if(!isset($param[0]) || !$param[0]) $separator = " / ";
	else $separator = $param[0];

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();
	$notice['notice_info']->fetch_notices_parents();
	for($i=0 ; $i<count($notice['notice_info']->notices_parents) ; $i++){
		if($result != "") $result.=$separator;
		$result.= $notice['notice_info']->notices_parents[$i]->get_memo_mention_edition();
	}
	return $result;
}

function aff_parents_publisher_place($param){
	global $parser_environnement;
	$result = "";
	if(!isset($param[0]) || !$param[0]) $separator = " / ";
	else $separator = $param[0];

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();
	$notice['notice_info']->fetch_notices_parents();
	for($i=0 ; $i<count($notice['notice_info']->notices_parents) ; $i++){
		if($result != "") $result.=$separator;
		$result.= $notice['notice_info']->notices_parents[$i]->get_memo_ed1_place();
	}
	return $result;
}

function aff_parents_publisher_name($param){
	global $parser_environnement;
	$result = "";
	if(!isset($param[0]) || !$param[0]) $separator = " / ";
	else $separator = $param[0];

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();
	$notice['notice_info']->fetch_notices_parents();
	for($i=0 ; $i<count($notice['notice_info']->notices_parents) ; $i++){
		if($result != "") $result.=$separator;
		$result.= $notice['notice_info']->notices_parents[$i]->get_memo_ed1_name();
	}
	return $result;
}

function aff_parents_year_publication($param){
	global $parser_environnement;
	$result = "";
	if(!isset($param[0]) || !$param[0]) $separator = " / ";
	else $separator = $param[0];

	if(!$parser_environnement['id_notice']) return '';
	//on récupère les parents
	$notice=gere_global();
	$notice['notice_info']->fetch_notices_parents();
	for($i=0 ; $i<count($notice['notice_info']->notices_parents) ; $i++){
		if($result != "") $result.=$separator;
		$result.= $notice['notice_info']->notices_parents[$i]->get_memo_year();
	}
	return $result;
}

function aff_str_replace($param){
	return str_replace($param[0],$param[1],$param[2]);
}

function get_collstate($param) {
	global $parser_environnement;
	$res=array();
	if(!$parser_environnement['id_notice']) return $res;
	$notice=gere_global();
	if(count($notice['notice_info']->memo_collstate)) {
		$res = $notice['notice_info']->memo_collstate;
	}
	return $res;
}

function aff_collstate($param) {
	global $parser_environnement;
	global $opac_sur_location_activate;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	$res='';
	$id_loc=array();
	if(isset($param) && is_array($param) && ($param[0] != "")){
		$matches=array();
		if(preg_match("/^\[([0-9;]+)\]$/",$param[0],$matches)){
			$id_loc=explode(";",$matches[1]);
		}elseif(preg_match("/^[0-9]+$/",$param[0])){
			$id_loc[]=$param[0];
		}
	}
	if (count($notice['notice_info']->memo_collstate)) {
		for ($i = 0 ; $i < count($notice['notice_info']->memo_collstate) ; $i++) {
			if(!count($id_loc) || in_array($notice['notice_info']->memo_collstate[$i]->idlocation,$id_loc)){
				$res.="<tr>";
				if ($opac_sur_location_activate) {
					$res.="<td>".$notice['notice_info']->memo_collstate[$i]->surloc_libelle."</td>";
				}
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->location_libelle."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->archempla_libelle."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->collstate_cote."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->archtype_libelle."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->archstatut_opac_libelle."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->collstate_origine."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->state_collections."</td>";
				$res.="<td>".$notice['notice_info']->memo_collstate[$i]->collstate_lacune."</td>";
				$res.="</tr>";
			}
		}
	}
	if($res){
		$res="<table>".$res."</table>";
	}
	return $res;
}

function aff_titre_bulletin($param){
	global $parser_environnement;
	$notice=gere_global();

	return $notice["notice_info"]->memo_notice_bulletin->bulletin_titre;
}

/*
 * @param[0] = sens (up/down/both),
 * @param[1,...n] = types de relations recherchées
 * @return
 */
function linked_id($param) {
	$link=array();
	//Sens de la relation : up, down ou both
	$link_direction=$param[0];

	unset($param[0]);
	$allowed_link_types=$param;

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if($link_direction=='up'){
		$notice['notice_info']->get_memo_notice_mere();
		if (count($notice['notice_info']->memo_notice_mere)) {
			foreach($notice['notice_info']->memo_notice_mere as $id=>$noticeId){
				if(in_array($notice['notice_info']->memo_notice_mere_relation_type[$id],$allowed_link_types)){
					$link[]=$noticeId;
				}
			}
		}
	}elseif($link_direction=='down'){
		$notice['notice_info']->get_memo_notice_fille();
		if (count($notice['notice_info']->memo_notice_fille)) {
			foreach($notice['notice_info']->memo_notice_fille as $id=>$noticeId){
				if(in_array($notice['notice_info']->memo_notice_fille_relation_type[$id],$allowed_link_types)){
					$link[]=$noticeId;
				}
			}
		}
	}elseif($link_direction=='both'){
		$notice['notice_info']->get_memo_notice_horizontale();
		if (count($notice['notice_info']->memo_notice_horizontale)) {
			foreach($notice['notice_info']->memo_notice_horizontale as $id=>$noticeId){
				if(in_array($notice['notice_info']->memo_notice_horizontale_relation_type[$id],$allowed_link_types)){
					$link[]=$noticeId;
				}
			}
		}
	}

	return $link;
}

/*
 * @param[0] = niveau de notices liées demandées (s,b,a)
 */
function serial_linked_id($param) {
	$link=array();

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	switch ($notice['notice_info']->niveau_biblio) {
		case 's' :
			//on est sur la notice de périodique
			switch ($param[0]) {
				case 'b' :
					//on veut les notices de bulletin rattachées
					$requete = "SELECT num_notice FROM bulletins WHERE bulletin_notice='".$notice['notice_info']->notice_id."' ORDER BY date_date";
					$res = pmb_mysql_query($requete);
					if ($res && pmb_mysql_num_rows($res)) {
						while ($row = pmb_mysql_fetch_object($res)) {
							$link[] = $row->num_notice;
						}
					}
					break;
				case 'a' :
					//on veut les notices d'articles rattachées
					$requete = "SELECT analysis_notice FROM analysis,bulletins WHERE analysis_bulletin = bulletin_id AND bulletin_notice='".$notice['notice_info']->notice_id."' ORDER BY date_date";
					$res = pmb_mysql_query($requete);
					if ($res && pmb_mysql_num_rows($res)) {
						while ($row = pmb_mysql_fetch_object($res)) {
							$link[] = $row->analysis_notice;
						}
					}
					break;
			}
			break;
		case 'b' :
			//on est sur la notice de bulletin
			switch ($param[0]) {
				case 's' :
					//on veut la notice de périodique rattachée
					$requete = "SELECT bulletin_notice FROM bulletins WHERE num_notice='".$notice['notice_info']->notice_id."'";
					$res = pmb_mysql_query($requete);
					if ($res && pmb_mysql_num_rows($res)) {
						$row = pmb_mysql_fetch_object($res);
						$link[] = $row->bulletin_notice;
					}
					break;
				case 'a' :
					//on veut les notices d'articles rattachées
					$requete = "SELECT analysis_notice FROM analysis,bulletins WHERE analysis_bulletin = bulletin_id AND num_notice='".$notice['notice_info']->notice_id."'";
					$res = pmb_mysql_query($requete);
					if ($res && pmb_mysql_num_rows($res)) {
						while ($row = pmb_mysql_fetch_object($res)) {
							$link[] = $row->analysis_notice;
						}
					}
					break;
			}
			break;
		case 'a' :
			//on est sur la notice d'article
			switch ($param[0]) {
				case 's' :
					//on veut la notice de périodique rattachée
					$requete = "SELECT bulletin_notice FROM bulletins, analysis WHERE bulletin_id = analysis_bulletin AND analysis_notice='".$notice['notice_info']->notice_id."'";
					$res = pmb_mysql_query($requete);
					if ($res && pmb_mysql_num_rows($res)) {
						$row = pmb_mysql_fetch_object($res);
						$link[] = $row->bulletin_notice;
					}
					break;
				case 'b' :
					//on veut la notice de bulletin rattachée
					$requete = "SELECT num_notice FROM bulletins, analysis WHERE bulletin_id = analysis_bulletin AND analysis_notice='".$notice['notice_info']->notice_id."'";
					$res = pmb_mysql_query($requete);
					if ($res && pmb_mysql_num_rows($res)) {
						$row = pmb_mysql_fetch_object($res);
						$link[] = $row->num_notice;
					}
					break;
			}
			break;
	}

	return $link;
}

function group($param){
	global $parser_environnement;

	$sav_parser_environnement=array();
	$sav_parser_environnement=$parser_environnement;

	$tpl='';

	$array_id=$param[0];
	$array_id_length = is_countable($array_id) ? count($array_id) : 0;
	$display_number=$param[1];
	$separator=$param[2];
	$code=$param[3];
	$texte_complement=$param[4];

	if(!$display_number || $display_number==0){
		$display_number = $array_id_length;
	}
	if($display_number > $array_id_length){
		$display_number = $array_id_length;
	}
	if (empty($separator)) {
		$separator=' - ';
	}

	for($i=0;$i<$display_number;$i++){

		global $parser_environnement;
		$parser_environnement=array();

		$parser_environnement['id_notice']=$array_id[$i];
		$parser_environnement['id_template']='';

		$notice=gere_global();

		$parser=parse_format::get_instance('notice_tpl.inc.php');

		$parser->cmd = $code;
		if($tpl){
			$tpl.=$separator;
		}
		$tpl.=$parser->exec_cmd();
	}

	if($display_number < $array_id_length){
		$tpl.=$separator.$texte_complement;
	}

	$parser_environnement=$sav_parser_environnement;
	return $tpl;
}

/*
 * @param[0] = Tableau d'identifiants de notices ou identifiants séparés par une virgule
 * @param[1] = Nom de la propriété pour le filtrage
 * @param[2] = Valeur du champ pour le filtrage
 */
function filter_from_notice_info($param) {
    global $parser_environnement;
    if(!$parser_environnement['id_notice']) return '';

    if(!isset($param[0])) {
        $param[0] = array();
    } elseif(!is_array($param[0])) {
        $param[0] = explode(',', $param[0]);
    }
    $record_ids = array();
    if(count($param[0])) {
        foreach ($param[0] as $record_id) {
            $notice_info = new notice_info($record_id);
            $method_name = 'get_'.$param[1];
            if(method_exists($notice_info, $method_name)) {
                $property_value = $notice_info->{$method_name}();
                if(is_array($property_value)) {
                    foreach ($property_value as $value) {
                        switch ($param[1]) {
                            case 'memo_lang_or':
                            case 'memo_lang':
                                if($value['lang_code'] == $param[2]) {
                                    $record_ids[] =$record_id;
                                }
                                break;
                            default:
                                if($value == $param[2]) {
                                    $record_ids[] =$record_id;
                                }
                                break;
                        }
                    }
                } else {
                    if($property_value == $param[2]) {
                        $record_ids[] =$record_id;
                    }
                }
            } else {
                if($property_value == $param[2]) {
                    $record_ids[] =$record_id;
                }
            }
        }
    }
    return $record_ids;
}

function aff_msg($param) {
	// Attention : l'aperçu en gestion n'utilise pas le même fichier de messages que le rendu en OPAC
	// il faut donc utiliser les codes des fichiers langues de l'OPAC
	// $param[0] = code du message à afficher
	global $msg;

	return $msg[$param[0]];
}

function aff_serie_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "serie", cf classes/serie.class.php
	// $param[0] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if ($notice['notice_info']->notice->tparent_id) {
		$serie_infos = new serie($notice['notice_info']->notice->tparent_id);
		return _get_aut_infos($serie_infos, $param[0]);
	}
	return '';
}
function aff_ellipse($param) {
    global $charset;

	// $param[0] = chaine de caractère à réduire
	// $param[1] = nb de caractères max à afficher
	// $param[2] = chaine de remplacement
	// $param[3] = Tronquer sur nb de mots au lieu du nombre de caractères
	if(isset($param[3]) && $param[3]){
		$array_words=explode(" ",$param[0]);
		if (count($array_words) <= $param[1]) {
			return $param[0];
		} else {
			array_splice($array_words, $param[1]);
			return implode(" ",$array_words).$param[2];
		}
	}else{
		if (pmb_strlen($param[0]) <= $param[1]) {
			return $param[0];
		} else {
		    $entities = false;
		    $tmp = html_entity_decode($param[0],ENT_NOQUOTES, $charset);
		    if($tmp != $param[0]){
                $entities = true;
		    }
		    $param[0] = $tmp;
		    $param[0] = pmb_substr_replace($param[0], $param[2], $param[1]);
		    if($entities){
		        $param[0] = htmlentities($param[0],ENT_NOQUOTES, $charset);
		    }
		    return $param[0];
		}
	}
}

function aff_avis($param) {
	// $param[0] = mode d'affichage de la note 0=note non visible, 1=sous forme d'étoiles, 2=sous forme textuelle, 3 =sous forme textuelle et étoiles
	// $param[1] = affiche le nom du lecteur : 0=non, 1=oui
	// $param[2] = nombre maxi d'avis à afficher
	global $parser_environnement;

	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	$res="";
	if(!isset($param[0])) $param[0] = '';
	if (count($notice["notice_info"]->memo_avis)) {
		if (isset($param[2]) && $param[2] && ($param[2] < count($notice["notice_info"]->memo_avis))) $max_avis = $param[2];
		else $max_avis = count($notice["notice_info"]->memo_avis);
		for ($i = 0 ; $i < $max_avis; $i++) {
			$res.= "<div id='avis_".$notice["notice_info"]->memo_avis[$i]->id_avis."'>
				<div class='left'>";
			if($param[0]){
				if($param[0]!=1){
					$categ_avis=$notice["notice_info"]->memo_avis[$i]->note_textuelle." ";
				}
				if($param[0]!=2){
					$etoiles="";$cpt_star = 4;
					for ($j = 1; $j <= $notice["notice_info"]->memo_avis[$i]->note; $j++) {
						$etoiles.="<img src='".get_url_icon('star.png')."'  alt=''/>";
					}
					for ( $j = round($notice["notice_info"]->memo_avis[$i]->note);$j <= $cpt_star ; $j++) {
						$etoiles .= "<img src='".get_url_icon('star_unlight.png')."'  alt=''/>";
					}
				}
				if($param[0]==3)$note=$etoiles."<br />".$categ_avis;
				else $note=$etoiles.$categ_avis;
			} else $note="";

			if (!$notice["notice_info"]->memo_avis[$i]->valide)
				$res.=  "<span style='color:#CC0000'>$note<b>".$notice["notice_info"]->memo_avis[$i]->sujet."</b></span>";
			else
				$res.=  "<span style='color:#00BB00'>$note<b>".$notice["notice_info"]->memo_avis[$i]->sujet."</b></span>";
			$res.=  ", ".$notice["notice_info"]->memo_avis[$i]->ladate."  ".(isset($param[1]) && $param[1] ? $notice["notice_info"]->memo_avis[$i]->empr_prenom." ".$notice["notice_info"]->memo_avis[$i]->empr_nom : "")."
			</div>
			<div class='row'>".$notice["notice_info"]->memo_avis[$i]->commentaire."	</div>
			</div>
			<div id='update_".$notice["notice_info"]->memo_avis[$i]->id_avis."'></div>
			<br />
			";
		}
	}
	return $res;
}

function aff_avis_with_tpl($param) {
	// Utiliser pour le template les attributs suivants : id_avis,note,sujet,commentaire,ladate,empr_login,empr_nom,empr_prenom,valide,note_textuelle
	// $param[0] = nombre maxi d'avis à afficher
	// $param[1] = séparateur entre les avis
	// $param[2] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	$tab_avis = array();
	if (count($notice["notice_info"]->memo_avis)) {
		if (($param[0]) && ($param[0] < count($notice["notice_info"]->memo_avis))) $max_avis = $param[0];
		else $max_avis = count($notice["notice_info"]->memo_avis);
		for ($i = 0 ; $i < $max_avis; $i++) {
			$tab_avis[] = _get_aut_infos($notice["notice_info"]->memo_avis[$i], $param[2]);
		}
	}

	if (count($tab_avis)) return implode($param[1],$tab_avis);

	return '';
}

function aff_expl_num_vign_reduit($param) {
	// Affiche pour une notice une icone cliquable pour ouvrir le document numérique (en tenant compte de la visionneuse)
	// $param[0] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui)
	// $param[1] = Texte de l'info-bulle dans le cas ou il y a plusieurs documents numériques // Si vide c'est celui par défaut qui est utilisé
	// $param[2] = Url de l'image dans le cas ou il y a plusieurs documents numériques // Si vide c'est celui par défaut qui est utilisé
	// $param[3] = Texte de l'info-bulle dans le cas ou il n'y a qu'un document numérique // Si vide c'est celui par défaut qui est utilisé
	// $param[4] = Url de l'image dans le cas ou il n'y a qu'un document numérique // Si vide c'est celui par défaut qui est utilisé
	// $param[5] = Par quel(s) identifiants de statut de document numérique doit-on filtrer ? Si vide pas de filtre, si plusieurs séparer par \,

	global $parser_environnement,$opac_visionneuse_allow,$opac_photo_filtre_mimetype,$msg,$opac_url_base;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if(is_array($param) && count($param) && ($param[0] == 1) && !explnum_test_rights($parser_environnement['id_notice'])) return '';

	$query = "SELECT explnum_id,explnum_mimetype,explnum_nom,explnum_nomfichier,explnum_url FROM explnum WHERE explnum_notice = '".$parser_environnement['id_notice']."'";
	//S'il s'agit d'une notice de bulletin, l'exemplaire numérique est relié au bulletin
	if($notice["notice_info"]->niveau_biblio=="b" && $notice["notice_info"]->niveau_hierar=="2"){
		$result = pmb_mysql_query("SELECT bulletin_id FROM bulletins WHERE num_notice= '".$parser_environnement['id_notice']."'");
		if($result && pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$query = "SELECT explnum_id,explnum_mimetype,explnum_nom,explnum_nomfichier,explnum_url FROM explnum WHERE explnum_bulletin = '".$row->bulletin_id."'";
		}
	}

	$id_statut=(isset($param[5]) ? trim($param[5]) : 0);
	if($id_statut){
		$query .= " AND explnum_docnum_statut IN (".$id_statut.")";
	}

	$result = pmb_mysql_query($query);
	if ($result && pmb_mysql_num_rows($result)) {
		$tab_explnum = array();
		while ($explnum = pmb_mysql_fetch_object($result)) {
			if (explnum_test_rights_per_id($explnum->explnum_id)) {
				$tab_explnum[] = $explnum;
			}
		}
		if(count($tab_explnum) > 1){
			$info_bulle=$msg["info_docs_num_notice"];
			if(is_array($param) && (count($param) >= 2) && (trim($param[1]))) $info_bulle=$param[1];
			$img_multip_doc=get_url_icon("globe_rouge.png");
			if(is_array($param) && (count($param) >= 3) && (trim($param[2]))) $img_multip_doc=trim($param[2]);
			return "<span class=\"expl_num_vign_reduit\" ><img src=\"".$img_multip_doc."\" alt=\"".$info_bulle."\" title=\"".$info_bulle."\" class='align_middle' /></span>";
		}elseif(count($tab_explnum) == 1){
			$explnumrow=$tab_explnum[0];
			$info_bulle="";
			if(is_array($param) && (count($param) >= 4) && (trim($param[3]))){
				$info_bulle=$param[3];
			}else{
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}
			}
			$img_multip_doc=get_url_icon("globe_orange.png");
			if(is_array($param) && (count($param) >= 5) && (trim($param[4]))) $img_multip_doc=trim($param[4]);

			$allowed_mimetype=array();
			if ($opac_visionneuse_allow){
				$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
			}
			$ret="<span class=\"expl_num_vign_reduit\" >";
			if ($opac_visionneuse_allow && in_array($explnumrow->explnum_mimetype,$allowed_mimetype)){
				$ret.="<script>
							if(typeof(sendToVisionneuse) == 'undefined'){
								var sendToVisionneuse = function (explnum_id){
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
								}
							}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" title=\"".$info_bulle."\">";
			}else{
				$ret.="<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
			}

			$ret.="<img src=\"".$img_multip_doc."\" class='align_middle' alt=\"".$info_bulle."\" title=\"".$info_bulle."\"></a></span>";
			return $ret;
		}
	}

	return '';
}

function explnum_test_rights($id_notice) {
	global $gestion_acces_active, $gestion_acces_empr_notice;

	// Droits d'acces emprunteur/notice
	$rights = 0;
	if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
		$ac = new acces();
		$dom_2 = $ac->setDomain(2);
		$rights = $dom_2->getRights($_SESSION['id_empr_session'], $id_notice);
	}

	// Accessibilité des documents numériques aux abonnés en opac
	$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices,notice_statut WHERE notice_id='$id_notice' AND statut=id_notice_statut ";

	$result = pmb_mysql_query($req_restriction_abo);
	if ($result && pmb_mysql_num_rows($result)) {
		$expl_num = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC);
		if ($rights & 16 || (!isset($dom_2) && $expl_num["explnum_visible_opac"] && (!$expl_num["explnum_visible_opac_abon"] || ($expl_num["explnum_visible_opac_abon"] && $_SESSION["user_code"])))) {
			return true;
		}
	}

	return false;
}

function explnum_test_rights_per_id($id_explnum) {
	global $gestion_acces_active, $gestion_acces_empr_docnum;

	// Droits d'acces emprunteur/document numérique
	$rights = 0;
	if ($gestion_acces_active == 1 && $gestion_acces_empr_docnum == 1) {
		$ac = new acces();
		$dom_3 = $ac->setDomain(3);
 		$rights = $dom_3->getRights($_SESSION['id_empr_session'], $id_explnum);
	}

	// Accessibilité du document numérique aux abonnés en opac
	$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM explnum,explnum_statut WHERE explnum_id='$id_explnum' AND explnum_docnum_statut=id_explnum_statut ";

	$result = pmb_mysql_query($req_restriction_abo);
	if ($result && pmb_mysql_num_rows($result)) {
		$expl_num = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC);
		if ($rights & 16 || (!isset($dom_3) && $expl_num["explnum_visible_opac"] && (!$expl_num["explnum_visible_opac_abon"] || ($expl_num["explnum_visible_opac_abon"] && $_SESSION["user_code"])))) {
			return true;
		}
	}

	return false;
}

// map
function aff_map_isbd($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice["notice_info"]->memo_map_isbd;
}

function aff_map_echelle($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice["notice_info"]->memo_map_echelle;
}

function aff_map_projection($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice["notice_info"]->memo_map_projection;
}

function aff_map_ref($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice["notice_info"]->memo_map_ref;
}

function aff_map_equinoxe($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice["notice_info"]->memo_map_equinoxe;
}

function aff_map($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();
	return $notice["notice_info"]->memo_map;
}

//authpersos

/* authperso_isbd
 * retourne l'isbd d'une autorité perso
 *
 * $param[0] : id de l'autorité perso
 * 	Si $param[0]=0, retourne l'isbd de toutes les autorités perso
 */
function aff_authperso_isbd($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if(!isset($param[0]) || !$param[0]) return $notice["notice_info"]->memo_authperso_all_isbd;
	return $notice["notice_info"]->memo_authperso_all_isbd_list[$param[0]]['isbd'];
}
/* authperso_name
* retourne l'isbd d'une autorité perso
*
* $param[0] : id de l'autorité perso
*
*/
function aff_authperso_name($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if(!isset($param[0]) || !$param[0]) return '';
	return $notice["notice_info"]->memo_authperso_all_isbd_list[$param[0]]['name'];
}

// Renvoie "Nombre d'avis|moyenne des avis" pour une notice
function aff_notes_avg($param) {
	// Renvoie "Nombre d'avis|moyenne des avis" pour une notice
	// $param[0] =

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	$avis = new avis($parser_environnement['id_notice']);
	return $avis->get_notes_avg();
}

// Renvoie "Nombre total d'exemplaires|Nombre d'exemplaires disponibles|date de retour du premier exemplaire si au moins un en prêt"
function aff_nb_expl_available($param) {
	// Renvoie "Nombre total d'exemplaires|Nombre d'exemplaires disponibles|date de retour du premier exemplaire si au moins un en prêt"
	// $param[0] =

	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	$query = "SELECT count(*) as combien FROM exemplaires join docs_statut on idstatut=expl_statut WHERE expl_notice = '".$parser_environnement['id_notice']."' and pret_flag=1 ";
	$result = pmb_mysql_query($query);
	$explnumrow=pmb_mysql_fetch_object($result);
	$total_expl = $explnumrow->combien;

	$query = "SELECT count(*) as en_pret, min(pret_retour) as retour, pret_idexpl FROM exemplaires join docs_statut on idstatut=expl_statut join pret on pret_idexpl=expl_id WHERE expl_notice = '".$parser_environnement['id_notice']."' and pret_flag=1 group by pret_idexpl order by pret_retour";
	$result = pmb_mysql_query($query);
	$explnumrow=pmb_mysql_fetch_object($result);
	$total_en_pret = $explnumrow->en_pret;
	$retour=$explnumrow->retour;

	return $total_expl."|".($total_expl-$total_en_pret)."|".$retour;
}

// Renvoie l'URL de l'OPAC
function aff_opac_url() {
	global $opac_url_base;

	return $opac_url_base;
}

function aff_bull_for_art_expl_num($param) {
	// $param[0] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui) //Utilisé à l'Opac

	global $parser_environnement;

	//on va chercher le bulletin de l'article
	$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
	$result = pmb_mysql_query($requete);
	if ($result && pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_object($result);
		$bulletin_id = $row->analysis_bulletin;
	} else {
		return '';
	}

	//on va chercher les exemplaires du bulletin
	$bulletin = gere_expl_bulletin($bulletin_id);

	return $bulletin->memo_explnum_assoc;
}

function aff_bull_for_art_expl_num_vign_reduit($param) {
	// Affiche pour une notice une icone cliquable pour ouvrir le document numérique
	// $param[0] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui)
	// $param[1] = Texte de l'info-bulle dans le cas ou il y a plusieurs documents numériques // Si vide c'est celui par défaut qui est utilisé
	// $param[2] = Url de l'image dans le cas ou il y a plusieurs documents numériques // Si vide c'est celui par défaut qui est utilisé
	// $param[3] = Texte de l'info-bulle dans le cas ou il n'y a qu'un document numérique // Si vide c'est celui par défaut qui est utilisé
	// $param[4] = Url de l'image dans le cas ou il n'y a qu'un document numérique // Si vide c'est celui par défaut qui est utilisé
	// $param[5] = Par quel(s) identifiants de statut de document numérique doit-on filtrer ? Si vide pas de filtre, si plusieurs séparer par \,

	global $parser_environnement,$opac_visionneuse_allow,$opac_photo_filtre_mimetype,$msg,$opac_url_base;
	if(!$parser_environnement['id_notice']) return '';

	if(is_array($param) && count($param) && ($param[0] == 1) && !explnum_test_rights($parser_environnement['id_notice'])) return '';

	//on va chercher le bulletin de l'article
	$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
	$result = pmb_mysql_query($requete);
	if ($result && pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_object($result);
		$bulletin_id = $row->analysis_bulletin;
	} else {
		return '';
	}

	$query = "SELECT explnum_id,explnum_mimetype,explnum_nom,explnum_nomfichier,explnum_url FROM explnum WHERE explnum_bulletin = '".$bulletin_id."'";

	$id_statut=trim($param[5]);
	if($id_statut){
		$query .= " AND explnum_docnum_statut IN (".$id_statut.")";
	}

	$result = pmb_mysql_query($query);
	if ($result && pmb_mysql_num_rows($result)) {
		$tab_explnum = array();
		while ($explnum = pmb_mysql_fetch_object($result)) {
			if (explnum_test_rights_per_id($explnum->explnum_id)) {
				$tab_explnum[] = $explnum;
			}
		}
		if(count($tab_explnum) > 1){
			$info_bulle=$msg["info_docs_num_notice"];
			if(is_array($param) && (count($param) >= 2) && (trim($param[1]))) $info_bulle=$param[1];
			$img_multip_doc=get_url_icon("globe_rouge.png");
			if(is_array($param) && (count($param) >= 3) && (trim($param[2]))) $img_multip_doc=trim($param[2]);
			return "<span class=\"expl_num_vign_reduit\" ><img src=\"".$img_multip_doc."\" alt=\"".$info_bulle."\" title=\"".$info_bulle."\" class='align_middle' /></span>";
		}elseif(count($tab_explnum) == 1){
			$explnumrow=$tab_explnum[0];
			$info_bulle="";
			if(is_array($param) && (count($param) >= 4) && (trim($param[3]))){
				$info_bulle=$param[3];
			}else{
				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}
			}
			$img_multip_doc=get_url_icon("globe_orange.png");
			if(is_array($param) && (count($param) >= 5) && (trim($param[4]))) $img_multip_doc=trim($param[4]);

			$allowed_mimetype=array();
			if ($opac_visionneuse_allow){
				$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
			}
			$ret="<span class=\"expl_num_vign_reduit\" >";
			if ($opac_visionneuse_allow && in_array($explnumrow->explnum_mimetype,$allowed_mimetype)){
				$ret.="<script>
							if(typeof(sendToVisionneuse) == 'undefined'){
								var sendToVisionneuse = function (explnum_id){
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
								}
							}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\" title=\"".$info_bulle."\">";
			}else{
				$ret.="<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
			}

			$ret.="<img src=\"".$img_multip_doc."\" class='align_middle' alt=\"".$info_bulle."\" title=\"".$info_bulle."\"></a></span>";
			return $ret;
		}
	}

	return '';
}

function aff_bull_for_art_expl_num_with_tpl($param) {
	// Utiliser pour le template les attributs de la classe "explnum", cf classes/explnum.class.php
	// $param[0] = nb maxi d'explnum à afficher
	// $param[1] = séparateur entre les explnum
	// $param[2] = 0 : tous les documents, 1 : documents sans statut spécifique, 2 : documents avec statut spécifique
	// $param[3] = template
	// Pour afficher la vignette : <img src='./vig_num.php?explnum_id={explnum_id}' />
	// $param[4] = Est-ce que l'on tient compte des droits (0 ou vide -> Non, 1 -> Oui)
	// $param[5] = Par quel(s) mimetype doit-on filtrer ? Si vide pas de filtre, si plusieurs séparer par \,
	// $param[6] = Par quel(s) identifiants de statut de document numérique doit-on filtrer ? Si vide pas de filtre, si plusieurs séparer par \,
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	//on va chercher le bulletin de l'article
	$requete = "SELECT analysis_bulletin FROM analysis WHERE analysis_notice='".$parser_environnement['id_notice']."'";
	$result = pmb_mysql_query($requete);
	if ($result && pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_object($result);
		$bulletin_id = $row->analysis_bulletin;
	} else {
		return '';
	}
	$tab_explnum = array();

	$mime_type="";
	if($tmp=trim($param[5])){
		$mime_type="'".str_replace(',',"','",$tmp)."'";
	}

	$query = "SELECT explnum_id FROM explnum WHERE explnum_bulletin = '".$bulletin_id."'";

	if($mime_type){
		$query.=" AND explnum_mimetype IN(".$mime_type.")";
	}
	$id_statut=trim($param[6]);
	if($id_statut){
		$query .= " AND explnum_docnum_statut IN (".$id_statut.")";
	}
	if(($param[0])*1){
		$query.=" LIMIT ".(($param[0])*1);
	}
	$result = pmb_mysql_query($query);
	if ($result && pmb_mysql_num_rows($result)) {
	    $param[4] = (isset($param[4]) ? intval($param[4]) : 0);
		while ($explnum = pmb_mysql_fetch_object($result)) {
		    if (!$param[4] || explnum_test_rights_per_id($explnum->explnum_id)) {
				$explnum_infos = new explnum($explnum->explnum_id);
				if (!$param[2] || (($param[2] == 1) && (!$explnum_infos->explnum_statut)) || (($param[2] == 2) && ($explnum_infos->explnum_statut))) {
					$tab_explnum[] = _get_aut_infos($explnum_infos, $param[3]);
				}
			}
		}
	}

	if (count($tab_explnum)) return implode($param[1],$tab_explnum);

	return '';
}

function aff_titre_uniforme_with_tpl($param) {
	global $fonction_auteur;
	// Utiliser pour le template les attributs de la classe "serie", cf classes/serie.class.php
	// $param[0] = nb maxi de titres uniformes à afficher
	// $param[1] = séparateur entre les titres uniformes
	// $param[2] = séparateur entre les valeurs d'un même champ répétable
	// $param[3] = format d'affichage des auteurs (0 : nom, prénom | 1 : nom, prénom (dates)| 2 : nom, prénom, fonction | 3 : nom, prénom (dates), fonction)
	// $param[4] = template
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	$array_retour = array();
	if (count($notice['notice_info']->memo_tu)) {
		$cpt = 0;
		foreach ($notice['notice_info']->memo_tu as $tu) {
			//traitement spécial pour les champs répétables des tu
			$array_rep_field = array("distrib", "ref", "subdiv");
			foreach ($array_rep_field as $rep_field) {
			    if (empty($tu->$rep_field) || !count($tu->$rep_field)) {
					$tu->$rep_field = "";
				} else {
					$mes_valeurs = array();
					foreach ($tu->$rep_field as $valeur) {
						$mes_valeurs[] = $valeur["label"];
					}
					$tu->$rep_field = implode($param[2],$mes_valeurs);
				}
			}
			//traitement spécial pour les auteurs des tu
			$mes_auteurs = array();
			if (count($tu->responsabilites["auteurs"])) {
				foreach ($tu->responsabilites["auteurs"] as $auteur) {
					$aut_detail = new auteur($auteur["id"]);
					switch($param[3]){
						case 0 :
							$mes_auteurs[] = $aut_detail->name.", ".$aut_detail->rejete;
							break;
						case 1 :
							$mes_auteurs[] = $aut_detail->name.", ".$aut_detail->rejete." (".$aut_detail->date.")";
							break;
						case 2 :
							$mes_auteurs[] = $aut_detail->name.", ".$aut_detail->rejete.", ".$fonction_auteur[$auteur["fonction"]];
							break;
						case 3 :
							$mes_auteurs[] = $aut_detail->name.", ".$aut_detail->rejete." (".$aut_detail->date.")".", ".$fonction_auteur[$auteur["fonction"]];
							break;
					}
				}
			}
			$tu->auteurs = implode($param[2],$mes_auteurs);
			//traitement du template
			$array_retour[] = _get_aut_infos($tu, $param[4]);
			$cpt++;
			if ($param[0] && $cpt==$param[0]) {
				break;
			}
		}
	}
	return implode($param[1],$array_retour);
}

function aff_statut($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if (!$param[0]) {
		return $notice["notice_info"]->memo_statut['id_notice_statut'];
	} else {
		return $notice["notice_info"]->memo_statut['opac_statut_libelle'];
	}
}

function aff_htmlentities($param) {
	global $charset;

	return htmlentities($param[0],ENT_QUOTES,$charset);
}

function aff_set_variable($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	global $notice_tpl_vars;

	if (!isset($notice_tpl_vars)) {
		$notice_tpl_vars = array();
	}
	$notice_tpl_vars[$parser_environnement['id_notice']][$param[0]] = $param[1];

	return '';
}

function aff_get_variable($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	global $notice_tpl_vars;

	if (!isset($notice_tpl_vars[$parser_environnement['id_notice']][$param[0]])) {
		return '';
	} else {
		return $notice_tpl_vars[$parser_environnement['id_notice']][$param[0]];
	}
}

function aff_get_global_variable($param) {
	global ${$param[0]};
	return ${$param[0]};
}

function aff_bannette_id($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	if ($parser_environnement['id_bannette']) {
		return $parser_environnement['id_bannette'];
	} else {
		return '';
	}
}

function aff_strip_tags($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	return strip_tags($param[0]);
}

// $param[0], 'no_scheme', or numeric id of scheme, empty: all concepts
function aff_concepts($param) {
	global $parser_environnement;
	if(!$parser_environnement['id_notice']) return '';

	$all_links = true;
	if(isset($param[1]) && $param[1] == 0) $all_links = false;

	$concepts_list = new skos_concepts_list();
	if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $parser_environnement['id_notice'])) {
		$concepts = $concepts_list->get_concepts_by_schemes();
		$list = array();
		switch ($param[0]) {
			case 'no_scheme' :
				$list = $concepts_list->get_concepts_without_sheme();
				break;
			case '' : // tous
				return skos_view_concepts::get_list_in_notice($concepts_list, $all_links);
				break;
			case is_numeric($param[0]) : // id of scheme
				foreach ($concepts as $sheme_id => $concept) {
					if ($sheme_id == $param[0]) {
						$list = $concept['elements'];
						break;
					}
				}
				break;
		}
		if ($list) {
			$concepts_list->set_concepts($list);
			return skos_view_concepts::get_list_in_notice($concepts_list, $all_links);
		}
	}
	return '';
}

function aff_keywords($param) {
	global $parser_environnement;
	global $pmb_keyword_sep;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	if (empty($param[0])) {
		return $notice['notice_info']->notice->index_l;
	} else {
		return str_replace($pmb_keyword_sep, $param[0], $notice['notice_info']->notice->index_l);
	}
}

function aff_creator($param) {
	global $parser_environnement;
	global $pmb_type_audit;
	if(!$parser_environnement['id_notice']) return '';
	$notice=gere_global();

	$creator = '';
	if($pmb_type_audit) {
		$audit = new audit(AUDIT_NOTICE, $parser_environnement['id_notice']);
		$audit->get_all();
		switch ($param[0]) {
			case 1:
				$creator .= $audit->get_creation()->user_name;
				break;
			case 0:
			default:
				$creator .= $audit->get_creation()->prenom_nom;
				break;
		}
	}
	return $creator;
}