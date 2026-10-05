<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: consolidation.inc.php,v 1.62.4.7 2025/05/06 13:05:04 dgoron Exp $
use Pmb\Common\Helper\Portal;

global $include_path, $class_path, $base_path, $func_format;

require_once "$include_path/misc.inc.php";
require_once "$class_path/XMLlist.class.php";
require_once "$class_path/search.class.php";
require_once "$class_path/consolidation.class.php";

if(empty($func_format)) {
	$func_format= array();
}
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
$func_format['bulletin_id']='show_bulletin_id';

//Fonctions emprunteur
$func_format['empr_age']='aff_age_user';
$func_format['empr_groupe']='aff_groupe_user';
$func_format['empr_codestat']='aff_codestat_user';
$func_format['empr_categ']='aff_categ_user';
$func_format['empr_statut']='aff_statut_user';
$func_format['empr_location']='aff_location_user';
$func_format['empr_ville']='aff_ville_user';
$func_format['empr_sexe']='aff_sexe_user';
$func_format['empr_pays']='aff_pays_user';

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

//Function sur les animations
$func_format['animation_id']='aff_animation_id';

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
	return (isset($post['user_query']) ? $post['user_query'] : '');
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
	return (isset($post[$param[0]]) ? $post[$param[0]] : '');
}

/**
 * Retourne une valeur de la variable $_GET
 */
function aff_var_get($param,$parser){
	$get = get_var_get($param,$parser);
	return (isset($get[$param[0]]) ? $get[$param[0]] : '');
}

/**
 * Retourne une valeur de la variable $_SERVER
 */
function aff_var_server($param,$parser){
	$server = get_var_server($param,$parser);
	return (isset($server[$param[0]]) ? $server[$param[0]] : '');
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
	return (isset($info_user['groupe']) ? $info_user['groupe'] : '');
}

/**
 * Retourne le code statistique de l'utilisateur
 */
function aff_codestat_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['codestat']) ? $info_user['codestat'] : '');
}

/**
 * Retourne le statut de l'utilisateur
 */
function aff_statut_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['statut']) ? $info_user['statut'] : '');
}

/**
 * Retourne la catégorie de l'utilisateur
 */
function aff_categ_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['categ']) ? $info_user['categ'] : '');
}

/**
 * Retourne la localisation de l'utilisateur
 */
function aff_location_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['location']) ? $info_user['location'] : '');
}

/**
 * Retourne la ville de l'utilisateur
 */
function aff_ville_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['ville']) ? $info_user['ville'] : '');
}

/**
 * Retourne le sexe de l'utilisateur
 */
function aff_sexe_user($param,$parser){
    $info_user = get_info_user($param,$parser);
    return (isset($info_user['empr_sexe']) ? $info_user['empr_sexe'] : '');
}

/**
 * Retourne le pays de l'utilisateur
 */
function aff_pays_user($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['empr_pays']) ? $info_user['empr_pays'] : '');
}

/**
 * Retourne le login de l'utilisateur
 */
function aff_login($param,$parser){
	$info_user = get_info_user($param,$parser);
	return (isset($info_user['empr_login']) ? $info_user['empr_login'] : '');
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
function aff_type_page($param, $parser) {
	Portal::setVarGET(get_var_post($param, $parser));
	Portal::setVarPost(get_var_get($param, $parser));
	return Portal::getTypePage(aff_url_asked($param, $parser));
}

/**
 * Fonction qui permet de classifier le sous type des pages selon un code
 */
function aff_sous_type_page($param,$parser){
	Portal::setVarGET(get_var_post($param, $parser));
	Portal::setVarPost(get_var_get($param, $parser));
	return Portal::getSubTypePage(aff_url_asked($param, $parser), get_info_notice($param, $parser));
}


function aff_libelle_type_page($param,$parser){
	global $lang, $include_path;
	global $liste_libelle_type_page;

	if (empty($liste_libelle_type_page)) {
		if(file_exists($include_path."/interpreter/statopac/$lang.xml")){
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/$lang.xml");
		} else {
			$liste_libelle = new XMLlist($include_path."/interpreter/statopac/fr_FR.xml");
		}
		$liste_libelle->analyser();
		$liste_libelle_type_page = $liste_libelle->table;
	}
	$value_page = aff_type_page($param,$parser);

	$label = "";
	if (!empty($liste_libelle_type_page[$value_page])) {
	    $label = $liste_libelle_type_page[$value_page];
	}
	return $label;
}

function aff_libelle_sous_type_page($param,$parser){
	global $lang, $include_path;
	global $liste_libelle_sous_type_page;
	global $cms_active, $class_path;

	if (empty($liste_libelle_sous_type_page)) {
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

	$label = "";
	if (!empty($liste_libelle_sous_type_page[$value_page])) {
	    $label = $liste_libelle_sous_type_page[$value_page];
	}
	return $label;
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
		foreach ($nb_result as $value){
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
		if (!empty($search) && is_countable($search)) {
    		for ($i=0; $i<count($search); $i++) {
    	   		$s=explode("_",$search[$i]);
    	   		if ($s[0]=="f") {
    	   		    if (!empty($sc->fixedfields[$s[1]]["TITLE"])) {
    	   		        $title[]=$sc->fixedfields[$s[1]]["TITLE"];
    	   		    }
    	   		} elseif ($s[0]=="d") {
    	   		    if (!empty($sc->pp[$s[0]]->t_fields[$s[1]]["TITRE"])) {
                        $title[]=$sc->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    	   		    }
    	   		} elseif ($s[0]=="s") {
    	   		    if (!empty($sc->specialfields[$s[1]]["TITLE"])) {
    	   		        $title[]=$sc->specialfields[$s[1]]["TITLE"];
    	   		    }
    	   		}
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
		if (is_countable($search)) {
    		for ($i=0; $i<count($search); $i++) {
    	   		$field = "field_".$i."_".$search[$i];
    	   		${$field} = $to_unserialize[$i]["FIELD"][0];
    	   		$mots[] = ${$field};
    		}
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
			$out=array();
			if(preg_match_all('`'.$opac_language_messages['search_facette'].' '.$opac_language_messages['eq_query'].' \((.+?)\)`',$tmp,$out)){
				if(!$param[0]){
					return implode(", ",$out[1]);
				}elseif($param[0]==1){
					$tmpArray=array();
					foreach($out[1] as $v){
						$v=trim($v);
						$outBis=array();
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
	$tab['onglet_persopac'] = intval($tab['onglet_persopac']);
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

	return intval($tab['opac_view']);
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

	if (empty($liste_libelle_types)) {
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
	return (isset($liste_libelle_types[$tab['type_url']]) ? $liste_libelle_types[$tab['type_url']] : '');
}

/**
 * Retourne l'identifiant de la notice cliquée ou développée
 */
function aff_notice_id($param,$parser){
	$notice = get_info_notice($param,$parser);
	return (isset($notice['notice_id']) ? $notice['notice_id'] : 0);
}

/**
 * Retourne l'identifiant du bulletin cliqué ou développé
 */
function show_bulletin_id($param,$parser){
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

/****************************************
 * 										*
 *   FONCTIONS SUR LES ANIMATIONS		*
 *  									*
 ****************************************/

/**
 * Retourne l'identifiant de l'animation cliquée
 */
function aff_animation_id($param, $parser) {
    if(!empty($parser->environnement['num_ligne'])) {
        $get = get_var_get($param, $parser);
        if (!empty($get['lvl']) && !empty($get['id']) && $get['lvl'] == 'animation_see') {
            return $get['id'];
        }
    }
    return '';
}
?>