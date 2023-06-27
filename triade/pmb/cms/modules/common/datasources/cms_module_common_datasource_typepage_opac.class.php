<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_typepage_opac.class.php,v 1.11 2018-05-23 14:19:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_typepage_opac {	
	
	public static function get_type_page(){
		$post = $_POST;
		$get = $_GET;
	
		if(isset($post['lvl']) && $post['lvl']){
			$niveau = $post['lvl'];
		} elseif (isset($get['lvl']) && $get['lvl']){
			$niveau = $get['lvl'];
		} else $niveau='';
		
		if(isset($post['search_type_asked']) && $post['search_type_asked']){
			$type = $post['search_type_asked'];
		} elseif (isset($get['search_type_asked']) && $get['search_type_asked']){
			$type = $get['search_type_asked'];
		} else $type='';
	
		if(isset($post['mode']) && $post['mode']){
			$mode = $post['mode'];
		} elseif (isset($get['mode']) && $get['mode']){
			$mode = $get['mode'];
		} else $mode='';
	
		if (isset($get['oresa']) && $get['oresa']){
			$sugg = $get['oresa'];
		} else $sugg='';
	
		//Note : Type page = 30 pour les URLs externes
		$page = array("recherche" => 1, "result" => 2, "result_noti" => 3, "result_aut" => 4, "aut" => 5,
				"display" => 6, "empr" => 7, "caddie" => 8, "histo" => 9, "etagere" => 10, "infopage" => 11,
				"tag" => 12, "notation" => 13, "sugg" => 14, "rss" => 15, "section" => 16,
				"sort" => 17, "information" => 18, "doc_command" => 19, "doc_num" => 20,
				"authperso" => 21, "perio_a2z" => 22, "bannette" => 23, "faq" => 24, "cms" => 25,
				"extend" => 26, "result_docnum" => 27, "accueil" => 28, "ajax" => 29, "contact_form" => 31,
				"collstate_bulletins_display" => 32, 
				// pixel blanc : 33 				
				"search_universe" => 34, "search_segment" => 35
		);
		
		//pour le panier
		if(isset($post['action']) && $post['action']){
			$action = $post['action'];
		} elseif (isset($get['action']) && $get['action']){
			$action = $get['action'];
		} else $action='';
	
		//url
		$url = $_SERVER['REQUEST_URI'];
	
		//Avis et tags
		if(strpos($url,'avis.php') && strpos($url,'liste')){
			return $page['notation'];
		} elseif (strpos($url,'avis.php') && strpos($url,'add')){
			return $page['notation'];
		} elseif (strpos($url,'addtags.php')){
			return $page['tag'];
		}
		
		//tags recherche
		if(isset($post['tags']) && $post['tags']){
			$tags = $post['tags'];
		} elseif (isset($get['tags']) && $get['tags']){
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
			case 'transform_to_sugg':
			case 'show_list':
			case 'cart':
			case 'show_cart':
			case 'resa_cart':
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
			case "collstate_bulletins_display":
				$type_page=$page['collstate_bulletins_display'];
				break;
			case 'search_universe':
				$type_page = $page['search_universe'];
				break;
			case 'search_segment':
				$type_page = $page['search_segment'];
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
	
	public static function get_subtype_page(){
	
		$post = $_POST;
		$get = $_GET;
		// 		$notice = get_info_notice($param,$parser);
	
		//récuperation des différentes variables nécessaires à l'identification des pages
		if(isset($post['lvl']) && $post['lvl']){
			$niveau = $post['lvl'];
		} elseif (isset($get['lvl']) && $get['lvl']){
			$niveau = $get['lvl'];
		} else $niveau='';
	
		//type recherche
		if(isset($post['search_type_asked']) && $post['search_type_asked']){
			$type = $post['search_type_asked'];
		} elseif (isset($get['search_type_asked']) && $get['search_type_asked']){
			$type = $get['search_type_asked'];
		} else $type='';
	
		//pour recherche prédéfinie
		if (isset($post['onglet_persopac']) && $post['onglet_persopac']){
			$perso = $post['onglet_persopac'];
		} elseif (isset($get['onglet_persopac']) && $get['onglet_persopac']){
			$perso = $get['onglet_persopac'];
		} else $perso='';
	
		//pour les types d'autorité
		if(isset($post['mode']) && $post['mode']){
			$mode = $post['mode'];
		} elseif (isset($get['mode']) && $get['mode']){
			$mode = $get['mode'];
		} else $mode='';
	
		//nivo biblio
		if(isset($notice['niveau_biblio']) && $notice['niveau_biblio']){
			$biblio = $notice['niveau_biblio'];
		} else $biblio='';
	
		//suggestion
		if (isset($get['oresa']) && $get['oresa']){
			$sugg = $get['oresa'];
		} else {
			$url_ref = $_SERVER['REQUEST_URI'];
			$sugg = strpos($url_ref,'oresa=popup');
		}
	
		//pour le panier
		if(isset($post['action']) && $post['action']){
			$action = $post['action'];
		} elseif (isset($get['action']) && $get['action']){
			$action = $get['action'];
		} else $action='';
	
		//url
		$url = $_SERVER['REQUEST_URI'];
	
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
		if((isset($get['reinit_facette']) && $get['reinit_facette']) || isset($get['param_delete_facette'])) { //param_delete_facette peut être égal à 0
			return '308';
		}elseif(isset($get['facette_test']) && $get['facette_test']){
			return '307';
		}
		
		//recherches affiliées
		if(isset($get['tab']) && $get['tab']){
			$tab=$get['tab'];
		} else {
			$tab='';
		}
		
		//tags recherche
		if(isset($post['tags']) && $post['tags']){
			$tags = $post['tags'];
		} elseif (isset($get['tags']) && $get['tags']){
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
				$search_type =  '509';
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
				if (isset($get['raz_cart']) && $get['raz_cart']) {
					$search_type = '805';
				} elseif (isset($get['action']) && $get['action']=='del') {
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
			case 'search_universe':
				$search_type = '3301';
				break;
			case 'search_segment':
				$search_type = '3401';
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
	
	function get_label($type){
		global $lang, $include_path;
		global $dbh;
		global $type_page_opac;
		if (!count($type_page_opac)) {
			if(file_exists($include_path."/interpreter/statopac/$lang.xml")){
				$liste_libelle = new XMLlist($include_path."/interpreter/statopac/$lang.xml");
			} else {
				$liste_libelle = new XMLlist($include_path."/interpreter/statopac/fr_FR.xml");
			}
			$liste_libelle->analyser();
			$type_page_opac = $liste_libelle->table;
			$query = "select id_page, page_name from cms_pages";
			$result = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_object($result)){
					$type_page_opac["25".str_pad($row->id_page,2,"0",STR_PAD_LEFT)] = $row->page_name;
				}
			}
		}
		return $type_page_opac[$type];
	}
}