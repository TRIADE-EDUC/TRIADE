<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: consolidation.inc.php,v 1.52 2019-06-10 08:57:12 btafforeau Exp $

global $include_path, $class_path, $base_path;
require_once "$include_path/misc.inc.php";
require_once "$class_path/XMLlist.class.php";
require_once "$class_path/search.class.php";
require_once "$class_path/consolidation.class.php";

$func_format['mots_saisis']= 'aff_mots_saisis';
$func_format['url_ori']= 'aff_url_ori';
$func_format['url_asked']= 'aff_url_asked';
$func_format['num_session']='aff_num_session';
$func_format['login']='aff_login';
$func_format['adresse_ip']='aff_adresse_ip';
$func_format['adresse_ip_forward']='aff_adresse_ip_forward';
$func_format['user_agent']='aff_user_agent';
$func_format['top_level_domain']='aff_top_level_domain';
$func_format['host_ip_info']='aff_host_ip_info';
$func_format['var_post']='aff_var_post';
$func_format['var_get']='aff_var_get';
$func_format['var_server']='aff_var_server';
$func_format['type_page']='aff_type_page';
$func_format['sous_type_page']='aff_sous_type_page';
$func_format['type_page_lib']='aff_libelle_type_page';
$liste_libelle_type_page=array();
$func_format['sous_type_page_lib']='aff_libelle_sous_type_page';
$liste_libelle_sous_type_page=array();
$func_format['multi_libelle']='aff_libelle_multicritere';
$func_format['multi_contenu']='aff_contenu_multicritere';
$func_format['multi_intitule']='aff_intitule_multicritere';
$func_format['multi_facettes']='aff_facettes_multicritere';
$func_format['recherche_predefinie']='aff_recherche_predefinie';
$func_format['vue_num']='aff_vue_num';
$func_format['vue_libelle']='aff_vue_libelle';
$func_format['url_externe']='aff_url_externe';
$func_format['url_externe_type']='aff_url_externe_type';
$func_format['notice_id']='aff_notice_id';
$func_format['bulletin_id']='aff_bulletin_id';

//Fonctions emprunteur
$func_format['empr_age']='aff_age_user';
$func_format['empr_groupe']='aff_groupe_user';
$func_format['empr_codestat']='aff_codestat_user';
$func_format['empr_categ']='aff_categ_user';
$func_format['empr_statut']='aff_statut_user';
$func_format['empr_location']='aff_location_user';
$func_format['empr_ville']='aff_ville_user';


//Fonctions date/heure
$func_format['timestamp']='aff_timestamp';
$func_format['date']='aff_date';
$func_format['year']='aff_year';
$func_format['month']='aff_month';
$func_format['day']='aff_day';
$func_format['hour']='aff_hour';
$func_format['minute']='aff_minute';
$func_format['seconde']='aff_seconde';
$func_format['elapsed_time']='aff_elapsed_time';

//Fonctions sur les nombres de résultats
$func_format['nb_all'] = 'aff_nb_all_result';
$func_format['nb_auteurs'] = 'aff_nb_auteurs';
$func_format['nb_collectivites'] = 'aff_nb_auteurs_collectivites';
$func_format['nb_congres'] = 'aff_nb_auteurs_congres';
$func_format['nb_physiques'] = 'aff_nb_auteurs_physiques';
$func_format['nb_editeurs'] = 'aff_nb_editeurs';
$func_format['nb_titres'] = 'aff_nb_titres';
$func_format['nb_titres_uniformes'] = 'aff_nb_titres_uniformes';
$func_format['nb_abstract'] = 'aff_nb_abstract';
$func_format['nb_categories'] = 'aff_nb_categories';
$func_format['nb_collections'] = 'aff_nb_collections';
$func_format['nb_subcollections'] = 'aff_nb_subcollections';
$func_format['nb_docnum'] = 'aff_nb_docnum';
$func_format['nb_keywords'] = 'aff_nb_keywords';
$func_format['nb_indexint'] = 'aff_nb_indexint';
$func_format['nb_total'] = 'aff_nb_result_total';

//Function sur les documents numériques
$func_format['explnum_localisation'] = 'aff_explnum_localisation';
$func_format['explnum_nom'] = 'aff_explnum_nom';
$func_format['explnum_nomfichier'] = 'aff_explnum_nomfichier';
$func_format['explnum_nomrepertoire'] = 'aff_explnum_nomrepertoire';
$func_format['explnum_path'] = 'aff_explnum_path';
$func_format['explnum_extfichier'] = 'aff_explnum_extfichier';
$func_format['explnum_mimetype'] = 'aff_explnum_mimetype';
$func_format['explnum_url'] = 'aff_explnum_url';
$func_format['explnum_notice'] = 'aff_explnum_notice';
$func_format['explnum_notice_type'] = 'aff_explnum_notice_type';
$func_format['explnum_bulletin'] = 'aff_explnum_bulletin';
$func_format['explnum_id'] = 'aff_explnum_id';

//Function sur les bannettes
$func_format['abo_bannette']='aff_abo_bannette';
$func_format['desabo_bannette']='aff_desabo_bannette';


/************************************************
 * 										        *							
 *   FONCTIONS SUR LES DOCUMENTS NUMERIQUES		*		
 *  									        *							
 ************************************************/
 
/**
 * Localisation du document numérique
 */
function aff_explnum_localisation($param,$parser){
	$tab = get_info_generique($param,$parser);
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['location_libelle'];
	}else{
		return "";
	}
}
 
/**
 * Nom du document numérique
 */
function aff_explnum_nom($param,$parser){
	$tab = get_info_generique($param,$parser);
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_nom'];
	}else{
		return "";
	}
}

/**
 * Nom du fichier du document numérique
 */
function aff_explnum_nomfichier($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['nomfichier'];
	}else{
		return "";
	}
}

/**
 * Nom du répertoire du document numérique
 */
function aff_explnum_nomrepertoire($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['nomrepertoire'];
	}else{
		return "";
	}
}

/**
 * Arborescence du document numérique dans le répertoire
 */
function aff_explnum_path($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_path'];
	}else{
		return "";
	}
}

/**
 * Extension du document numérique
 */
function aff_explnum_extfichier($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_extfichier'];
	}else{
		return "";
	}
}

/**
 * Type de document numérique
 */
function aff_explnum_mimetype($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_mimetype'];
	}else{
		return "";
	}
}

/**
 * URL du document numérique
 */
function aff_explnum_url($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_url'];
	}else{
		return "";
	}
}

/**
 * Id de la notice reliée au document numérique
 */
function aff_explnum_notice($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_notice'];
	}else{
		return "";
	}
}

/**
 * Type de la notice reliée au document numérique
 */
function aff_explnum_notice_type($param,$parser){
	global $lang, $include_path;
	global $liste_libelle_types;
	
	$tab = get_info_generique($param,$parser);

	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		$type_notice = sql_value("SELECT niveau_biblio FROM notices WHERE notice_id=".($tab['explnum'][0]['explnum_notice']*1));
		if (!$param[0]) {
			return $type_notice;
		} elseif ($param[0]==1) {
			if (!count($liste_libelle_types)) {
				if(file_exists($include_path."/interpreter/statopac/$lang.xml")){
					$liste_libelle = new XMLlist($include_path."/interpreter/statopac/$lang.xml");
				} else {
					$liste_libelle = new XMLlist($include_path."/interpreter/statopac/fr_FR.xml");
				}
				$liste_libelle->analyser();
				$liste_libelle_types = $liste_libelle->table;
			}
			return $liste_libelle_types['notice_type_'.$type_notice];
		} else {
			return "";
		}
	}else{
		return "";
	}
}

/**
 * Id du bulletin relié au document numérique
 */
function aff_explnum_bulletin($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_bulletin'];
	}else{
		return "";
	}
}

/**
 * Id du document numérique
 */
function aff_explnum_id($param,$parser){
	$tab = get_info_generique($param,$parser);

	if(isset($tab['explnum']) && is_array($tab['explnum'][0])){
		return $tab['explnum'][0]['explnum_id'];
	}else{
		return "";
	}
}
/********************************************************************
 * 																	*
 *      FONCTIONS DE CALCULS QUI RETOURNE LES VALEURS DESIREES      *
 *  																*
 ********************************************************************/

/**
 * Retourne l'url appelante
 */
function aff_url_ori($param, $parser){
	return $parser->environnement['ligne']['url_referente'];
}

/**
 * Retourne l'url appelée
 */
function aff_url_asked($param, $parser){
	return $parser->environnement['ligne']['url_demandee'];
}

/**
 * Retourne le numéro de session du log
 */
function aff_num_session($param,$parser){
	return $parser->environnement['ligne']['num_session'];
}

/**
 * Retourne le mot saisi
 */
function aff_mots_saisis($param,$parser){	
	$post = get_var_post($param,$parser);
	return $post['user_query'];
}

/**
 * Retourne le login de l'utilisateur
 
function aff_login($param,$parser){
	return get_info_user($param,$parser,'empr_login');	
}*/

/**
 * Retourne l'adresse IP de l'utilisateur
 */
function aff_adresse_ip($param,$parser){
	$server = get_var_server($param,$parser);
	return $server['REMOTE_ADDR'];
}

/**
 * Retourne l'adresse IP de l'utilisateur en cas de proxy
 */
function aff_adresse_ip_forward($param,$parser){
	$server = get_var_server($param,$parser);
	return $server['HTTP_X_FORWARDED_FOR'];
}

/**
 * Retourne le user agent de l'utilisateur
 */
function aff_user_agent($param,$parser){
	$server = get_var_server($param,$parser);
	return $server['HTTP_USER_AGENT'];
}

/**
 * Retourne le domaine de premier niveau (Top-Level Domain)
 */
function aff_top_level_domain($param,$parser){
    global $opac_url_base;
    $domain = parse_url(aff_url_ori($param, $parser), PHP_URL_HOST); //Récupération de l'hote ayant demandé la page OPAC
    if (!$domain || $domain == parse_url($opac_url_base, PHP_URL_HOST)) return ''; //On ne retourne rien si ce n'est pas une arrivée depuis l'exterieur du site
    $domain = substr($domain, strrpos($domain, '.')); //On enlève le point suivant les "www"
    return $domain;
}

/**
 * Retourne des informations sur la position géographique de l'utilisateur
 */
function aff_host_ip_info($param,$parser){
	$adresse_ip = aff_adresse_ip($param,$parser);
	
	$aCurl = new Curl();
	$json_content = $aCurl->get('http://ip-api.com/json/'.$adresse_ip);
	if($json_content) {
		$content = encoding_normalize::json_decode($json_content, true);
		if(isset($content[$param[0]]) && $content['status'] != 'fail') {
			return $content[$param[0]];
		}
	}
	return '';
}

/**
 * Retourne une valeur de la variable $_POST
 */
function aff_var_post($param,$parser){
	$post = get_var_post($param,$parser);
	return $post[$param[0]];
}

/**
 * Retourne une valeur de la variable $_GET
 */
function aff_var_get($param,$parser){
	$get = get_var_get($param,$parser);
	return $get[$param[0]];
}

/**
 * Retourne une valeur de la variable $_SERVER
 */
function aff_var_server($param,$parser){
	$server = get_var_server($param,$parser);
	return $server[$param[0]];
}


/****************************************************************************
 * 																	        *
 *  FONCTIONS DE CALCULS QUI RETOURNE LES CARACTERISTIQUES DE L'EMPRUNTEUR  *
 *  																        *
 ****************************************************************************/

/**
 * Retourne l'âge de l'utilisateur
 */
function aff_age_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	$birth_date = $info_user['empr_year'];
	$today = explode('-',today());
	if($birth_date){
		return ($today[0]-$birth_date);
	}
}

/**
 * Retourne le groupe de l'utilisateur
 */
function aff_groupe_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['groupe'];	
}

/**
 * Retourne le code statistique de l'utilisateur
 */
function aff_codestat_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['codestat'];	
}

/**
 * Retourne le statut de l'utilisateur
 */
function aff_statut_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['statut'];	
}

/**
 * Retourne la catégorie de l'utilisateur
 */
function aff_categ_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['categ'];	
}

/**
 * Retourne la localisation de l'utilisateur
 */
function aff_location_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['location'];	
}

/**
 * Retourne la ville de l'utilisateur
 */
function aff_ville_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['ville'];	
}

/**
 * Retourne le login de l'utilisateur
 */
function aff_login($param,$parser){
	$info_user = get_info_user($param,$parser);
	return $info_user['empr_login'];	
}
/********************************************************************
 * 																	*
 *           FONCTIONS SUR LA DATE ET l'HEURE DES LOGS				*
 *  																*
 ********************************************************************/

/**
 * Retourne l'heure du log HH:MM:SS du log
 */
function aff_timestamp($param,$parser){	
	return $parser->environnement['ligne']['date_log'];
}

/**
 * Retourne la date du log
 */
function aff_date($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],0,10);
}

/**
 * Retourne l'heure du log
 */
function aff_hour($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],11,2);
}

/**
 * Retourne l'année du log
 */
function aff_year($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],0,4);
}

/**
 * Retourne le jour du log
 */
function aff_day($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],8,2);
}

/**
 * Retourne le mois du log
 */
function aff_month($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],5,2);
}

/**
 * Retourne les minutes du log
 */
function aff_minute($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],14,2);
}

/**
 * Retourne les secondes du log
 */
function aff_seconde($param,$parser){	
	return substr($parser->environnement['ligne']['date_log'],17,2);
}

/**
 * Retourne le temps écoulé dans un intervalle
 */
function aff_elapsed_time($param,$parser){
	$filtre = $parser->environnement['filtre'];
	$timestamp_current = sql_value("SELECT date_log from ".$parser->environnement['tempo']." where id_log=".$parser->environnement['num_ligne']);
	return sql_value("SELECT TIME_TO_SEC(TIMEDIFF(date_log,'".$timestamp_current."')) from ".$filtre." where date_log > '".$timestamp_current."' limit 1");
}

/********************************************************************
 * 																	*
 *               CLASSIFICATION DES TYPES DE PAGE					*
 *  																*
 ********************************************************************/


/**
 * Retourne le type de page consultée
 */
function aff_type_page($param, $parser){
	
	$post = get_var_post($param,$parser);
	$get = get_var_get($param,$parser);
	
	if(!empty($post['lvl'])){
		$niveau = $post['lvl'];
	} elseif (!empty($get['lvl'])){
		$niveau = $get['lvl'];
	} else $niveau='';
	
	if(!empty($post['search_type_asked'])){
		$type = $post['search_type_asked'];
	} elseif (!empty($get['search_type_asked'])){
		$type = $get['search_type_asked'];
	} else $type='';
	
	if(!empty($post['mode'])){
		$mode = $post['mode'];
	} elseif (!empty($get['mode'])){
		$mode = $get['mode'];
	} else $mode='';
	
	if (!empty($get['oresa'])){
		$sugg = $get['oresa'];
	} else $sugg='';
	
	//Note : Type page = 30 pour les URLs externes
	$page = array("recherche" => 1, "result" => 2, "result_noti" => 3, "result_aut" => 4, "aut" => 5, 
				"display" => 6, "empr" => 7, "caddie" => 8, "histo" => 9, "etagere" => 10, "infopage" => 11,
				"tag" => 12, "notation" => 13, "sugg" => 14, "rss" => 15, "section" => 16,
				"sort" => 17, "information" => 18, "doc_command" => 19, "doc_num" => 20,
				"authperso" => 21, "perio_a2z" => 22, "bannette" => 23, "faq" => 24, "cms" => 25,
				"extend" => 26, "result_docnum" => 27, "accueil" => 28, "ajax" => 29, "contact_form" => 31,
				"collstate_bulletins_display" => 32, "pixel" => 33
	);
	
	//pour le panier
	if(!empty($post['action'])){
		$action = $post['action'];
	} elseif (!empty($get['action'])){
		$action = $get['action'];
	} else $action='';
	
	//url
	$url = aff_url_asked($param,$parser);
	
	//Avis et tags
	if(strpos($url,'avis.php') && strpos($url,'liste')){
		return $page['notation'];
	} elseif (strpos($url,'avis.php') && strpos($url,'add')){
		return $page['notation'];
	} elseif (strpos($url,'addtags.php')){
		return $page['tag'];
	}
	
	//tags recherche
	if(!empty($post['tags'])){
		$tags = $post['tags'];
	} elseif (!empty($get['tags'])){
		$tags = $get['tags'];
	} else {
		$tags='';
	}
	
	//Document numérique
	if(strpos($url,'doc_num.php') || strpos($url,'doc_num_data.php') || strpos($url,'visionneuse.php')){
		return $page['doc_num'];
	}
	
	//appel AJAX
	if(strpos($url,'ajax.php')) {
		return $page['ajax'];
	}
	
	//pixel blanc
	if(strpos($url, 'pixel.php')) {
		return $page['pixel'];
	}
			
	$type_page='';
	switch($niveau){		
		case 'author_see':
		case 'titre_uniforme_see':
		case 'serie_see':
		case 'categ_see':
		case 'indexint_see':
		case 'publisher_see':
		case 'coll_see':
		case 'subcoll_see':
		case 'concept_see' :
			$type_page=$page['aut'];
			break;		
		case 'more_results':
			if ($mode) {
				switch($mode) {
					case 'tous':
					case 'titre':
					case 'title':
					case 'extended':
					case 'abstract':
						$type_page=$page['result_noti'];
						break;
					case 'auteur':
					case 'editeur':
					case 'titre_uniforme':
					case 'collection':
					case 'souscollection':
					case 'categorie':
					case 'indexint':
						$type_page=$page['result_aut'];
						break;
					case 'keyword':
						if($tags){
							$type_page=$page['result_noti'];
						}else{
							$type_page=$page['result_aut'];
						}
						break;
					case 'docnum':
						$type_page=$page['result_docnum'];
						break;
					default:
						if(substr($mode, 0,10) == "authperso_"){
							$type_page=$page['result_aut'];
						} else {
							$type_page=$page['result_noti'];
						}
						break;
				}
			} else {
				//autolevel
				$type_page=$page['result_noti'];
			}
			break;		
		case 'notice_display':
		case 'bulletin_display':
			$type_page=$page['display'];
			break;			
		case 'search_result':
			$type_page=$page['result'];
			break;		
		case 'search_history':
			$type_page=$page['histo'];
			break;	
		case 'etagere_see':
		case 'etageres_see':
			$type_page=$page['etagere'];
			break;
		case 'cart':
		case 'show_cart':
		case 'resa_cart':
		case 'transform_to_sugg':
		case 'show_list':
			$type_page=$page['caddie'];
			break;
		case 'section_see':
			$type_page=$page['section'];
			break;
		case 'rss_see':
			$type_page=$page['rss'];
			break;
		case 'doc_command':	
			$type_page=$page['doc_command'];
			break;
		case 'sort':
			$type_page=$page['sort'];
			break;
		case 'lastrecords':
			$type_page=$page['result_noti'];
			break;
		case 'authperso_see':
			$type_page=$page['authperso'];
			break;
		case 'information':
			$type_page=$page['information'];
			break;
		case 'infopages':
			$type_page=$page['infopage'];
			break;
		case 'extend':
			$type_page=$page['extend'];
			break;
		case 'perio_a2z_see':
			$type_page=$page['perio_a2z'];
			break;
		case 'cmspage':
			$type_page=$page['cms'];
			break;
		case 'bannette_see':
			$type_page=$page['bannette'];
			break;
		case "faq" :
			$type_page=$page['faq'];
			break;
		case "contact_form" :
			$type_page=$page['contact_form'];
			break;
		case "collstate_bulletins_display" :
			$type_page=$page['collstate_bulletins_display'];
			break;
		case 'index':
			$type_page=$page['recherche'];	
			break;			
		case 'make_sugg':
			if($sugg) $type_page=$page['sugg'];
			else $type_page=$page['empr'];
			break;
		case 'valid_sugg':
		case 'view_sugg':
		case 'late':
		case 'change_password':
		case 'valid_change_password':
		case 'message':
		case 'all':			
		case 'old':
		case 'pret':
		case 'retour':
		case 'resa':
		case 'resa_planning':
		case 'bannette':
		case 'bannette_gerer':
		case 'bannette_creer':
		case 'bannette_edit':
		case 'bannette_unsubscribe':
		case 'make_multi_sugg':
		case 'import_sugg':
		case 'private_list':
		case 'public_list':
		case 'demande_list':		
		case 'do_dmde':
		case 'list_dmde':
		case 'scan_requests_list':
			$type_page=$page['empr'];
			break;	
		default:
			//pas de lvl
			if($type){
				$type_page=$page['recherche'];
			}elseif(strpos($url,'empr.php')){
				$type_page=$page['empr'];
			}elseif((strpos($url,'index.php')) || (!strpos($url,'.php'))){
				$type_page=$page['accueil'];
			}else{
				$type_page=$page['recherche'];
			}
			if($action == 'export')
				$type_page = $page['caddie'];
			break;
		
	}
	
	return $type_page;
	
}

/**
 * Fonction qui permet de classifier le sous type des pages selon un code 
 */
function aff_sous_type_page($param,$parser){
	
	$post = get_var_post($param,$parser);
	$get = get_var_get($param,$parser);
	$notice = get_info_notice($param,$parser);
	
	//récuperation des différentes variables nécessaires à l'identification des pages
	if($post['lvl']){
		$niveau = $post['lvl'];
	} elseif ($get['lvl']){
		$niveau = $get['lvl'];
	} else $niveau='';
	
	//type recherche
	if($post['search_type_asked']){
		$type = $post['search_type_asked'];
	} elseif ($get['search_type_asked']){
		$type = $get['search_type_asked'];
	} else $type='';
	
	//pour recherche prédéfinie
	if ($post['onglet_persopac']){
		$perso = $post['onglet_persopac'];
	} elseif ($get['onglet_persopac']){
		$perso = $get['onglet_persopac'];
	} else $perso='';	
	
	//pour les types d'autorité
	if($post['mode']){
		$mode = $post['mode'];
	} elseif ($get['mode']){
		$mode = $get['mode'];
	} else $mode='';
	
	//nivo biblio
	if($notice['niveau_biblio']){
		$biblio = $notice['niveau_biblio'];
	} else $biblio='';
	
	//suggestion
	if ($get['oresa']){
		$sugg = $get['oresa'];
	} else {
		$url_ref = aff_url_ori($param,$parser);
		$sugg = strpos($url_ref,'oresa=popup');
	}
	
	//pour le panier
	if($post['action']){
		$action = $post['action'];
	} elseif ($get['action']){
		$action = $get['action'];
	} else $action='';
	
	//url
	$url = aff_url_asked($param,$parser);
	
	//Avis et tags
	if(strpos($url,'avis.php') && strpos($url,'liste')){
		return '1301';
	} elseif (strpos($url,'avis.php') && strpos($url,'add')){
		return '1302';
	} elseif (strpos($url,'addtags.php')){
		return '1201';
	}
	
	//Document numérique
	if(strpos($url,'doc_num.php') || strpos($url,'doc_num_data.php')){
		return '2001';
	}elseif(strpos($url,'visionneuse.php')){
		return '2002';
	}
	
	//facettes
	if($get['reinit_facette'] || isset($get['param_delete_facette'])) { //param_delete_facette peut être égal à 0
		return '308';
	}elseif($get['facette_test']){
		return '307';
	}
	
	//recherches affiliées
	if($get['tab']){
		$tab=$get['tab'];
	}
	
	//tags recherche
	if($post['tags']){
		$tags = $post['tags'];
	} elseif ($get['tags']){
		$tags = $get['tags'];
	} else {
		$tags='';
	}
	
	//appel AJAX - Log expand notice
	if(strpos($url,'ajax.php') && (strpos($url,'storage') || strpos($url,'expand_notice'))) {
		switch($biblio){
			case 's':
				return '2902';
				break;
			case 'b':
				return '2903';
				break;
			case 'a':
				return '2904';
				break;
			default:
				return '2901';
				break;
		}
	}
	
	//appel AJAX - Log url externe
	if(strpos($url,'ajax.php') && strpos($url,'log')) {
		return '3001';
	}
	
	$search_type='';
	switch($niveau){		
		case 'author_see':
			$search_type = '501'; 
			break;
		case 'categ_see':
			$search_type = '503'; 
			break;		
		case 'indexint_see':
			$search_type = '507'; 
			break;		
		case 'coll_see':
			$search_type = '505'; 
			break;		
		case 'concept_see':
			$search_type = '509'; 
			break;		
		case 'more_results':
			if($tab=='affiliate'){
				$search_type = '306';
				break;
			}
			switch($mode){
				case 'titre':
				case 'title':
					$search_type = '301'; 
					break;
				case 'tous':
					$search_type = '302'; 
					break;
				case 'docnum':
					$search_type = '303'; 
					break;
				case 'auteur':
					$search_type = '401'; 
					break;	
				case 'editeur':
					$search_type = '402'; 
					break;
				case 'categorie':
					$search_type = '403'; 
					break;
				case 'titre_uniforme':
					$search_type = '404'; 
					break;
				case 'collection':
					$search_type = '405'; 
					break;
				case 'souscollection':
					$search_type = '406'; 
					break;	
				case 'indexint':
					$search_type = '407'; 
					break;
				case 'keyword':
					if($tags){
						$search_type = '309';
					}else{
						$search_type = '408';
					}
					break;
				case 'abstract':
					$search_type = '409'; 
					break;
				case 'extended':
					$search_type = '304';
					break;
				case 'external':
					$search_type = '305';  
					break;
				default:
					$search_type = '302';
					break;
			}
			break;		
		case 'notice_display':
			switch($biblio){
				case 's':
					$search_type = '602'; 
				   break;
				case 'b':
					$search_type = '603'; 
					break;
				case 'a':
					$search_type = '604'; 
					break;
				default:
					$search_type = '601'; 
					break;
			}
			break;
		case 'bulletin_display':
			$search_type = '603'; 
			break;			
		case 'publisher_see':
			$search_type = '502'; 
			break;	
		case 'titre_uniforme_see':
			$search_type = '504'; 
			break;		
		case 'serie_see':
			$search_type = '508'; 
			break;		
		case 'search_result':
			switch($type){		
				case 'external_search': 
					$search_type = '204'; 
					break;	
				case 'term_search':
					$search_type = '203';
					break;
				case 'extended_search':
					if($perso) 
						$search_type = '206';
					else $search_type = '202'; 			
					break;	
				case 'search_perso':
					$search_type='206';
					break;
				case 'tags_search':
					$search_type = '205'; 
					break;
				case 'simple_search':
					$search_type = '201'; 
					break;
				default:
					$search_type = '207'; 
					break;
			}	
			break;		
		case 'subcoll_see':
			$search_type = '506'; 
			break;
		case 'search_history':
			$search_type = '901'; 
			break;	
		case 'etagere_see':
			$search_type = '1001'; 
			break;	
		case 'etageres_see':
			$search_type = '1002'; 
			break;
		case 'show_cart':
			if ($get['raz_cart']) {
				$search_type = '805';
			} elseif ($get['action']=='del') {
				$search_type = '806';
			} elseif (isset($get['sort'])) { //Peut être égal à 0
				$search_type = '808';
			} else {
				$search_type = '801';
			}
			break;
		case 'resa_cart':
			$search_type = '804';
			break;
		case 'section_see':
			$search_type = '1601'; 
			break;
		case 'rss_see':
			$search_type = '1501';
			break;
		case 'doc_command':	
			$search_type = '1901';
			break;
		case 'sort':
			$search_type = '1701';
			break;
		case 'lastrecords':
			$search_type = '303';
			break;
		case 'authperso_see':
			$search_type = '2101';
			break;
		case 'information':
			$search_type = '1801';
			break;
		case 'infopages':
			$search_type = '1101';
			break;
		case 'extend':
			$search_type = '2601';
			break;
		case 'perio_a2z_see':
			$search_type = '2201';
			break;
		case 'cmspage':
			// pageid
			if(strpos($url,'pageid')){
				//sous-type commence par 25 suivi de l'identifiant de la page
				if (strpos(substr($url, strpos($url,'pageid')+7), '&')) {
					$search_type = "25".str_pad(substr($url, strpos($url,'pageid')+7,strpos(substr($url, strpos($url,'pageid')+7), '&')),2,"0",STR_PAD_LEFT);
				} else {
					$search_type = "25".str_pad(substr($url, strpos($url,'pageid')+7),2,"0",STR_PAD_LEFT);
				}
			} else {
				$search_type = "2500";
			}
			break;
		case 'bannette_see':
			$search_type = '2301';
			break;
		case "faq" :
			$search_type = '2401';
			break;
		case "contact_form" :
			$search_type = '3101';
			break;
		case "collstate_bulletins_display" :
			$search_type = '3201';
			break;
		case 'index':
				switch($type){		
					case 'external_search': 
						$search_type = '104'; 
						break;	
					case 'term_search':
						$search_type = '103';
						break;
					case 'extended_search':
						if($perso) 
							$search_type = '106';
						else $search_type = '102'; 			
						break;	
					case 'search_perso':
						$search_type='106';
						break;
					case 'tags_search':
						$search_type = '105'; 
						break;
					case 'simple_search':
						$search_type = '101'; 
						break;
					case 'perio_a2z':
						$search_type = '108'; 
						break;
					default:
						$search_type = '107'; 
						break;
				}	
			break;			
		case 'change_password':
			$search_type = '704';
			break;
		case 'valid_change_password':
			$search_type = '705';
			break;
		case 'message':
			//$type_page=$page['empr'];
			break;
		case 'all':			
			$search_type = '702';	
			break;
		case 'old':
			$search_type = '719';
			break;
		case 'pret':
			$search_type = '720';
			break;
		case 'retour':
			$search_type = '721';
			break;
		case 'resa':
			$search_type = '703';
			break;
		case 'resa_planning':
			$search_type = '724';
			break;
		case 'bannette':
			$search_type = '706';
			break;
		case 'bannette_gerer':
			$search_type = '707';
			break;
		case 'bannette_creer':
			$search_type = '708';
			break;
		case 'bannette_edit':
			$search_type = '727';
			break;
		case 'bannette_unsubscribe':
			$search_type = '728';
			break;
		case 'make_sugg':
			if($sugg)
				$search_type = '1401';
			else $search_type = '709';
			break;
		case 'valid_sugg':
			if($sugg)
				$search_type = '1402';
			else $search_type = '710';
			break;
		case 'view_sugg':
			$search_type = '711';
			break;
		case 'late':
			$search_type = '701';
			break;
		case 'make_multi_sugg':
			$search_type = '712';
			break;
		case 'import_sugg':
			$search_type = '722';
			break;
		case 'transform_to_sugg':
			$search_type = '809';
			break;
		case 'show_list':
			$search_type = '810';
			break;
		case 'private_list':
			$search_type = '713';
			break;
		case 'public_list':
			$search_type = '714';
			break;
		case 'demande_list':		
			$search_type = '715';
			break;
		case 'do_dmde':
			$search_type = '717';
			break;
		case 'list_dmde':
			$search_type = '718';
			break;
		case 'scan_requests_list':
			$search_type = '726';
			break;
		case 'cart':
			if (strpos($url,'print.php')) {
				if ($action) {
					$search_type = '802';
				} else {
					$search_type = '807';
				}
			} else {
				$search_type = '801';
			}
			break;
		case 'list':
			switch($action){
				case 'print_list':
					$search_type = '716';
					break;
			}
			break;
		default:	
			switch($type){		
				case 'external_search': 
					$search_type = '104'; 
					break;	
				case 'term_search':
					$search_type = '103';
					break;
				case 'extended_search':
					if($perso) 
						$search_type = '106';
					else $search_type = '102'; 			
					break;	
				case 'search_perso':
					$search_type='106';
					break;
				case 'tags_search':
					$search_type = '105'; 
					break;
				case 'simple_search':
					$search_type = '101'; 
					break;
				case 'perio_a2z':
					$search_type = '108'; 
					break;
				default:
					//pas de lvl ni de type
					if(strpos($url,'empr.php')){
						$search_type = '725';
					}elseif((strpos($url,'index.php')) || (!strpos($url,'.php'))){
						$search_type = '2801';
					}else{
						$search_type = '107';
					} 
					break;
			}	
			if($action == 'export')
					$search_type = '803';
			break;
		
	}
	
	return $search_type;
}


function aff_libelle_type_page($param,$parser){
	global $lang, $include_path;
	global $liste_libelle_type_page;
	
	if (!count($liste_libelle_type_page)) {
		if(file_exists($include_path."/interpreter/statopac/$lang.xml")){
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/$lang.xml");
		} else {
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/fr_FR.xml");
		}
		$liste_libelle->analyser();
		$liste_libelle_type_page = $liste_libelle->table;
	}	
	$value_page = aff_type_page($param,$parser);
	return $liste_libelle_type_page[$value_page];
}

function aff_libelle_sous_type_page($param,$parser){
	global $lang, $include_path;
	global $liste_libelle_sous_type_page;
	global $cms_active, $class_path;
	
	if (!count($liste_libelle_sous_type_page)) {
		if(file_exists($include_path."/interpreter/statopac/$lang.xml")){
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/$lang.xml");
		} else {
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/fr_FR.xml");
		}
		$liste_libelle->analyser();
		$liste_libelle_sous_type_page = $liste_libelle->table;
		
		//Libellés des pages du portail
		if ($cms_active) {
			require_once ($class_path."/cms/cms_pages.class.php");
			$cms_pages = new cms_pages();
			if (count($cms_pages->data)) {
				foreach ($cms_pages->data as $page) {
					$liste_libelle_sous_type_page["25".str_pad($page["id"],2,"0",STR_PAD_LEFT)] = $page["name"];
				}
			}
		}
	}
	$value_page = aff_sous_type_page($param,$parser);
		
	return $liste_libelle_sous_type_page[$value_page];
}
/********************************************************************
 * 																	*
 *              FONCTIONS SUR LE NOMBRE DE RESULTATS           		*
 *  																*
 ********************************************************************/

function aff_nb_all_result($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['tous'];
}

function aff_nb_auteurs($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['auteurs'];
}

function aff_nb_auteurs_collectivites($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['collectivites'];
}

function aff_nb_auteurs_congres($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['congres'];
}

function aff_nb_auteurs_physiques($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['physiques'];
}

function aff_nb_editeurs($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['editeurs'];
}

function aff_nb_titres($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['titres'];
}
function aff_nb_titres_uniformes($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['titres_uniformes'];
}

function aff_nb_abstract($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['abstract'];
}

function aff_nb_categories($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['categories'];
}

function aff_nb_collections($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['collections'];	
}

function aff_nb_subcollections($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['subcollections'];
}

function aff_nb_docnum($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['docnum'];
}

function aff_nb_keywords($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['keywords'];
}

function aff_nb_indexint($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	return $nb_result['indexint'];
}

function aff_nb_result_total($param,$parser){
	$nb_result = get_nb_result($param,$parser);
	if(!count($nb_result))
		return 0;
	else {
		$nb=0;
		foreach ($nb_result as $key=>$value){
			if(is_array($value)) {
				for($i=0;$i<count($value);$i++){
					$nb = $nb + $value[$i];
				}
			} else 
				$nb = $nb + $value;
		}
		return $nb;
	}
}

/*
 * Affiche le libelle des champs sélectionnés dans la multicritere
 */
function get_search_class(){
	global $consolidation_search_class;
	global $base_path, $pmb_opac_url,$lang;
	
	if(!isset($consolidation_search_class)){
		// Recherche du fichier lang de l'opac
		$url = $pmb_opac_url."includes/messages/$lang.xml";
		$fichier_xml = $base_path."/temp/opac_lang.xml";
		curl_load_opac_file($url,$fichier_xml);
		
		$url = $pmb_opac_url."includes/search_queries/search_fields.xml";
		$fichier_xml="$base_path/temp/search_fields_opac.xml";
		curl_load_opac_file($url,$fichier_xml);
		
		$consolidation_search_class = new search(false,"search_fields_opac",$base_path."/temp/");
	}
	
	return $consolidation_search_class;
}

function aff_libelle_multicritere($param,$parser){
	$tab = get_info_generique($param,$parser);

	if(isset($tab['multi_search'])){	 
		$to_unserialize=unserialize($tab['multi_search']);
	    $search=$to_unserialize["SEARCH"];
		$sc=get_search_class();
		$title = array();
		for ($i=0; $i<count($search); $i++) {
	   		$s=explode("_",$search[$i]);
	   		if ($s[0]=="f") {
	   			$title[]=$sc->fixedfields[$s[1]]["TITLE"];	   			
	   		} elseif ($s[0]=="d") {
	   			$title[]=$sc->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
	   		} elseif ($s[0]=="s") {
	   			$title[]=$sc->specialfields[$s[1]]["TITLE"];
	   		}
		}
		return implode(',',$title);
	}
	return '';
	
}

/********************************************************************
 * 																	*
 *  			FONCTIONS POUR LA MULTICRITERE   					*
 *  																*
 ********************************************************************/

/*
 * Affiche le contenu des champs sélectionnés dans la multicritere
 */
function aff_contenu_multicritere($param,$parser){
	
	$tab = get_info_generique($param,$parser);

	if(isset($tab['multi_search'])){	 
		$to_unserialize=unserialize($tab['multi_search']);
	    $search=$to_unserialize["SEARCH"];
		$mots = array();
		for ($i=0; $i<count($search); $i++) {
	   		$field = "field_".$i."_".$search[$i];
	   		${$field} = $to_unserialize[$i]["FIELD"][0];
	   		$mots[] = ${$field};
		}
		return implode(',',$mots);
	}
	return '';
	
}

/*
 * Affiche l'intitulé de la requête multicritère
 */
function aff_intitule_multicritere($param,$parser){
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['multi_human_query'])) {
		return strip_tags($tab['multi_human_query']);
	} else {
		return "";
	}
}

/*
 * Affiche les intitulés des facettes
 */
function aff_facettes_multicritere($param,$parser){
	global $charset, $base_path, $include_path, $pmb_opac_url;
	global $opac_languages_messages;
	// $param[0] = 0=Critère : valeur / 1=Liste ordonnée des critères
	$tab = get_info_generique($param,$parser);
	
	if(isset($tab['multi_human_query'])) {
		$tmp=strip_tags($tab['multi_human_query']);
		// récupération des codes langues
		$XMLlist = new XMLlist("$include_path/messages/languages.xml", 0);
		$XMLlist->analyser();
		$languages = $XMLlist->table;
	
		if(!isset($opac_languages_messages) && !is_array($opac_languages_messages)) {
			$opac_languages_messages = array();
			foreach ($languages as $codelang => $libelle) {
				// arabe seulement si on est en utf-8
				if (($charset != 'utf-8' and $codelang != 'ar') or ($charset == 'utf-8')) {
					// Recherche du fichier lang de l'opac
					$url=$pmb_opac_url."includes/messages/$codelang.xml";
					$fichier_xml=$base_path."/temp/opac_lang_$codelang.xml";
					curl_load_opac_file($url,$fichier_xml);
					$messages = new XMLlist("$base_path/temp/opac_lang_$codelang.xml", 0);
					$messages->analyser();
					$opac_languages_messages[$codelang] = array(
							'search_facette' => $messages->table['search_facette'],
							'eq_query' => $messages->table['eq_query']
					);
				}
			}
		}
		foreach ($opac_languages_messages as $opac_language_messages) {
			if(preg_match_all('`'.$opac_language_messages['search_facette'].' '.$opac_language_messages['eq_query'].' \((.+?)\)`',$tmp,$out)){
				if(!$param[0]){
					return implode(", ",$out[1]);
				}elseif($param[0]==1){
					$tmpArray=array();
					foreach($out[1] as $v){
						$v=trim($v);
						if(preg_match_all('`( Et )?(.+?) : &#039;(.+?)&#039;`',$v,$outBis)){
							foreach($outBis[2] as $vBis){
								if((!count($tmpArray))||(!in_array($vBis,$tmpArray))){
									$tmpArray[]=$vBis;
								}
							}
						}
					}
					asort($tmpArray);
					return implode(", ",$tmpArray);
				}else{
					return "";
				}
			}
		}
	}
	return "";
}

/*
 * Affiche le nom de la recherche prédéfinie
 */
function aff_recherche_predefinie($param, $parser){
	$tab = get_var_get($param,$parser);
	if (!isset($tab['onglet_persopac'])) {
		$tab = get_var_post($param,$parser);
		if (!isset($tab['onglet_persopac'])) {
			return '';
		}
	}
	
	$tmp_name = sql_value("SELECT search_shortname FROM search_persopac WHERE search_id=".$tab['onglet_persopac']);
	if (trim($tmp_name)) {
		return $tmp_name;
	} else {
		return sql_value("SELECT search_name FROM search_persopac WHERE search_id=".$tab['onglet_persopac']);
	}
}

/*
 * Vues
 */
function aff_vue_num($param, $parser){
	$tab = get_info_generique($param,$parser);
	
	if (!isset($tab['opac_view'])) {
		return '';
	} elseif ($tab['opac_view']=='default_opac') {
		return '';
	}
	
	return $tab['opac_view']*1;
}

function aff_vue_libelle($param, $parser){
	$tab = get_info_generique($param,$parser);
	
	if (!isset($tab['opac_view'])) {
		return '';
	} elseif ($tab['opac_view']=='default_opac') {
		return '';
	}

	return sql_value("SELECT opac_view_name FROM opac_views WHERE opac_view_id=".($tab['opac_view']*1));
}

/********************************************************************
 * 																	*
 *   FONCTIONS SUR LES VARIABLES GLOBALES ET LES CARACTERISTIQUES	*
 * 			 DES NOTICES, EXEMPLAIRES ET EMPRUNTEURS				*
 *  																*
 ********************************************************************/

/**
 * Retourne les valeurs de la variable $_POST 
 */
function get_var_post($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['post_log']);
	}
	return '';
}

/**
 * Retourne les valeurs de la variable $_GET 
 */
function get_var_get($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['get_log']);
	}
	return '';
}

/**
 * Retourne les valeurs de la variable $_SERVER 
 */
function get_var_server($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['server_log']);
	}
	return '';
}

/**
 * Retourne les informations sur l'utilisateur(année de naissance, ...) 
 */
function get_info_user($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['empr_carac']);
	}
	return '';
}

/**
 * Retourne les informations sur la notice
 */
function get_info_notice($param,$parser){
	
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['empr_doc']);
	}
	return '';
}

/**
 * Retourne les informations sur l'exemplaire 
 */
function get_info_expl($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['empr_expl']);
	}
	return '';
}

/**
 * Retourne les nombres de résultats de recherche
 */
function get_nb_result($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['nb_result']);
	}
	return 0;
}

/**
 * Retourne les informations du tableau générique
 */
function get_info_generique($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return unserialize($parser->environnement['ligne']['gen_stat']);
	}
	return '';
}

function get_infos($param,$parser){
	if(!empty($parser->environnement['num_ligne'])){
		return $parser->environnement['ligne'];
	}
	return '';
}

/**
 * Retourne l'url externe cliquée
 */
function aff_url_externe($param,$parser){
	$tab = get_var_get($param,$parser);
	if (!isset($tab['called_url'])) {
		$tab = get_var_post($param,$parser);
		if (!isset($tab['called_url'])) {
			return '';
		}
	}
	return $tab['called_url'];
}

/**
 * Retourne le type d'url externe cliquée
 */
function aff_url_externe_type($param,$parser){
	global $lang, $include_path;
	global $liste_libelle_types;
	
	if (!count($liste_libelle_types)) {
		if(file_exists($include_path."/interpreter/statopac/$lang.xml")){
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/$lang.xml");
		} else {
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/fr_FR.xml");
		}
		$liste_libelle->analyser();
		$liste_libelle_types = $liste_libelle->table;
	}
	
	$tab = get_var_get($param,$parser);
	if (!isset($tab['type_url'])) {
		$tab = get_var_post($param,$parser);
		if (!isset($tab['type_url'])) {
			return '';
		}
	}
	return $liste_libelle_types[$tab['type_url']];
}

/**
 * Retourne l'identifiant de la notice cliquée ou développée
 */
function aff_notice_id($param,$parser){
	$notice = get_info_notice($param,$parser);
	return $notice['notice_id'];
}

/**
 * Retourne l'identifiant du bulletin cliqué ou développé
 */
function aff_bulletin_id($param,$parser){
	$tab = get_var_get($param,$parser);
	if (!isset($tab['lvl']) || $tab['lvl']!="bulletin_display") {
		return '';
	}
	return $tab['id'];
}

/****************************************
 * 										*
 *   FONCTIONS SUR LES BANNETTES		*
 *  									*
 ****************************************/

/**
 * Retourne le nombre d'abonnements aux bannettes venant d'être cochées
 */
function aff_abo_bannette($param,$parser){
	$tab = get_var_post($param,$parser);
	$array_liste_avant_post = array();
	$array_liste_post = array();
	if ((isset($tab['liste_abo_bann_pub'])) && trim($tab['liste_abo_bann_pub'])) {
		$array_liste_avant_post = explode(',',$tab['liste_abo_bann_pub']);
	}
	if (isset($tab['bannette_abon'])) {
		foreach ($tab['bannette_abon'] as $k=>$v) {
			$array_liste_post[] = $k;
		}
	}
	$diff = array_diff($array_liste_post,$array_liste_avant_post);

	return count($diff);	
}

/**
 * Retourne le nombre de desabonnements aux bannettes venant d'être décochées
 */
function aff_desabo_bannette($param,$parser){
	$tab = get_var_post($param,$parser);
	$array_liste_avant_post = array();
	$array_liste_post = array();
	if ((isset($tab['liste_abo_bann_pub'])) && trim($tab['liste_abo_bann_pub'])) {
		$array_liste_avant_post = explode(',',$tab['liste_abo_bann_pub']);
	}
	if (isset($tab['bannette_abon'])) {
		foreach ($tab['bannette_abon'] as $k=>$v) {
			$array_liste_post[] = $k;
		}
	}
	$diff = array_diff($array_liste_avant_post,$array_liste_post);

	return count($diff);
}

/****************************************
 * 										*							
 *   FONCTIONS GENERIQUES USUELLES		*		
 *  									*							
 ****************************************/

/**
 * Teste si la fonction existe
 * 
 */
function func_test($f_name){
	global $func_format;
	if($func_format[$f_name]) return 1;
	return 0;
}


/**
 * Retourne la valeur associée à la requête si elle existe
 */
function sql_value($rqt) {
	if($result=pmb_mysql_query($rqt)){
		if($row = pmb_mysql_fetch_row($result))	
			return $row[0];
	}
	return '';
}
?>