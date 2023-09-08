<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: more_results.inc.php,v 1.111 2019-01-16 17:02:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

	// récupération configuration
	require_once($base_path."/includes/opac_config.inc.php");

	// récupération paramètres MySQL et connection à la base
	require_once($base_path."/includes/opac_db_param.inc.php");
	require_once($base_path."/includes/opac_mysql_connect.inc.php");
	if(!$dbh){
	   $dbh = connection_mysql();
	}
	require_once($base_path."/includes/start.inc.php");

	// récupération localisation
	require_once($base_path."/includes/localisation.inc.php");
	// les mots vides sont importants pour la requête à appliquer
	require_once($base_path."/includes/marc_tables/$pmb_indexation_lang/empty_words");
	// version actuelle de l'opac
	require_once($base_path."/includes/opac_version.inc.php");

	// fonctions de formattage requêtes
	require_once($base_path."/includes/misc.inc.php");


	// fonctions de gestion de formulaire
	require_once($base_path."/includes/javascript/form.inc.php");
	require_once($base_path."/includes/templates/common.tpl.php");
	
	require_once($base_path."/includes/rec_history.inc.php");
	
	require_once($include_path.'/surlignage.inc.php');
	
	require_once($class_path."/searcher.class.php");

	require_once($class_path."/skos/skos_concept.class.php");
	
	require_once($class_path."/search_view.class.php");
	
	//recherche affiliées
	require_once($class_path."/affiliate_search.class.php");
	require_once($class_path."/more_results.class.php");
	
	if(!isset($tab)) $tab = '';
	if(!isset($mode)) $mode = '';
	if(!isset($count)) $count = 0;
	
	if($search_type == 'tags_search') {
		rec_history();
		$_SESSION["new_last_query"]=$_SESSION["nb_queries"];
	}
	
	//Affectation du numéro de page avant l'enregistrement en session, ca évite certains problèmes...
	$page = more_results::get_page();
	
	if ($opac_search_other_function) {
		require_once($include_path."/".$opac_search_other_function);
	}
	
	//réinitialisation des facettes sur la recherche dans les sources affiliées
	if($tab=="affiliate"){
		//$get_last_query = 1; //MB: 2014/10/28
		$reinit_facette = 1;
	}
	
	if ($get_last_query) {
		get_last_history();
	} else {
		if($tab!="affiliate"){
			//hack un peu tordu pour un clic sur une facette depuis une page autorité...
		    if($mode == "extended" && isset($_SESSION['last_module_search']) && strpos($_SERVER['HTTP_REFERER'],$_SESSION['last_module_search']['search_mod']) !== false && !$search_type_asked){
				if(strpos($_SERVER['HTTP_REFERER'],$_SESSION['last_module_search']['search_mod']) !== false ){
					if($_SESSION['last_module_search']['need_new_search']){
						//ajout de la recherche dans l'historique 
						$_SESSION["nb_queries"]=$_SESSION["nb_queries"]+1;
						$n=$_SESSION["nb_queries"];
						$_SESSION["notice_view".$n]=$_SESSION["last_module_search"];
						$_SESSION["human_query".$n] = search_view::get_last_human_query();
						$_SESSION["search_type".$n]="module";
					}
					$_SESSION["new_last_query"] = $_SESSION["nb_queries"];
				}
			}
		}
		
		if (!empty($_SESSION["new_last_query"])) {
			$_SESSION["last_query"]=$_SESSION["new_last_query"];
			$_SESSION["new_last_query"]="";
			unset($_SESSION["facette"]);
			unset($_SESSION["lq_facette_search"]);
			unset($_SESSION["lq_facette_test"]);
		}
	 	rec_last_history();
	}
	
	// affichage recherche
	more_results::set_url_base($base_path.'/index.php?');
	more_results::set_user_query(stripslashes($user_query));
	more_results::set_search_type($search_type);
	more_results::proceed();