<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_categories_controller.class.php,v 1.31.2.1.2.1 2025/04/24 12:37:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/entities/entities_authorities_controller.class.php");
require_once($class_path.'/noeuds.class.php');
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/aut_link.class.php");
include("$include_path/templates/thesaurus.tpl.php");
include($include_path.'/templates/category.tpl.php');

class entities_categories_controller extends entities_authorities_controller {

	protected $model_class_name = 'category';

	protected $parent;

	protected $id_thes;

	public $num_auth_present;

	public function get_display_hierarchical_list() {
		global $msg, $charset;
		global $page, $nb_per_page_gestion;
		global $last_param, $limit_param;
		global $authority_statut;
		global $lang;

		$display = '';
		$browser_top = '';
		$browser_header = '';
		$nav_bar="";

		if(!$this->user_input) $this->user_input = '*';

		$this->search_form();

		if(!$page) {
			$page=1;
			$this->page = $page;
		} else {
		    $this->page = (int) $page;
		}
		$debut =($this->page-1)*$nb_per_page_gestion;

		if ($this->id_thes == -1) { //on affiche la liste des thesaurus
			$odd_even = 0;
			$liste_thesaurus = thesaurus::getThesaurusList();
			foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				if ($odd_even==0) {
					$display .= "	<tr class='odd'>";
					$odd_even=1;
				} else if ($odd_even==1) {
					$display .= "	<tr class='even'>";
					$odd_even=0;
				}
				$display.= "<td><a href='".$this->url_base."&id_thes=".$id_thesaurus."'>".htmlentities($libelle_thesaurus,ENT_QUOTES, $charset)."</a>";
				$display.= "</td></tr>";
			}
		} else {
			$thes = new thesaurus($this->id_thes);

			//si le parent n'est pas passe, on positionne
			//le parent comme étant le noeud racine du thesaurus
			if (!$this->parent) {
				$this->parent = $thes->num_noeud_racine;
			}
			//Si le parent n'as pas de fils, on remonte au noeud supérieur.
			if (!noeuds::hasChild($this->parent)) {
				$noeud = new noeuds($this->parent);
				$this->parent = $noeud->num_parent;
			}
			if($thes == NULL){
				$display .= $msg[4051];
				if (function_exists('affiche')) {
					affiche();
				}
				exit;
			}
			$authority_statut = intval($authority_statut);
			if($authority_statut){
				$join_statut_filter = "join authorities on authorities.num_object=noeuds.id_noeud and authorities.type_object=".AUT_TABLE_CATEG." ";
				$where_statut_filter = "and authorities.num_statut=".$authority_statut." ";
			} else {
				$join_statut_filter = '';
				$where_statut_filter = '';
			}
			$query = 'select count(1) from noeuds '.$join_statut_filter.' where noeuds.num_thesaurus = "'.$this->id_thes.'" '.$where_statut_filter;
			if (!$last_param) $query .= "and noeuds.num_parent = '".$this->parent."' ";
			if ($last_param) $query .= $limit_param;
			$result = pmb_mysql_query($query);
			$this->nbr_lignes = pmb_mysql_result($result, 0, 0);
			if($this->nbr_lignes) {
				$query = "select noeuds.id_noeud, ";
				$query.= "autorite, ";
				$query.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as lib ";
				$query.= "from noeuds ".$join_statut_filter." left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
				$query.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
				$query.= "where ";
				$query.= 'noeuds.num_thesaurus = "'.$this->id_thes.'" '.$where_statut_filter;
				if ($last_param) {
					$query .= $this->get_last_order()." ";
					$query .= $limit_param." ";
				} else {
					$query .= "and noeuds.num_parent = '".$this->parent."' ";
					$query .= "order by lib ";
					if($this->nbr_lignes>$nb_per_page_gestion){
						$query .= "limit ".$debut.",".$nb_per_page_gestion." ";
					}
				}
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					$browser_top = "<a href='".$this->url_base."&sub=&parent=0&id=0'>";
					$browser_top.= "<img src='".get_url_icon('top.gif')."' style='border:0px; margin:3px 3px' class='align_middle'></a>";

					if (!$last_param) {
						// récupération de la 1ère entrée et création du header
						$cat = pmb_mysql_fetch_row($result);
						$tcateg =  new category($cat[0]);
						if (!empty($tcateg->path_table)) {
						    $nb_paths_table = count($tcateg->path_table);
						    for ($i = 0; $i < $nb_paths_table - 1; $i++) {
							    if ($browser_header) {
							        $browser_header .= '&gt;';
							    } else {
							        $browser_header = '';
							    }
								$browser_header .= "<a href='";
								$browser_header .= $this->url_base."&parent=";
								$browser_header .= $tcateg->path_table[$i]['id'];
								$browser_header .= "' title='".$tcateg->path_table[$i]['commentaire']."'>";
								$browser_header .= $tcateg->path_table[$i]['libelle'];
								$browser_header .= "</a>";
							}
							if ($browser_header) {
							    $browser_header .= '&gt;<strong>';
							} else {
							    $browser_header = '<strong>';
							}
							$browser_header .= $tcateg->path_table[count($tcateg->path_table) - 1]['libelle'];
							$browser_header .= '</strong>';
						}
					}

					$display .= $this->get_display_header_list();
					$odd_even=0;
					pmb_mysql_data_seek($result, 0);
					while($cat = pmb_mysql_fetch_row($result)) {
						$authority = new authority(0, $cat[0], AUT_TABLE_CATEG);
						$display .= $this->get_display_line($authority->get_id());
					}
					if (!$last_param) $nav_bar = aff_pagination ($this->get_pagination_link(), $this->nbr_lignes, $nb_per_page_gestion, $this->page, 10, false, true) ;
				} else {

				}
			} else {
				// la requête n'a produit aucun résultat
				$display .= $msg[4051];
			}
		}
		$display = "<br />
					<div class='row'>
						".$browser_top."
						".$browser_header."<hr />
					</div>
					<div class='row'>
						<script type='text/javascript' src='./javascript/sorttable.js'></script>
						<table border='0' class='sortable'>
							".$display."
						</table>
					</div>".$nav_bar;
		print $display;
	}

	public function proceed() {
		global $sub, $save_and_continue, $category_parent_id, $PMBuserid, $parent;

		switch($sub) {
			case 'delete':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
			    // On déclenche un événement sur la supression
			    $evt_handler = events_handler::get_instance();
			    $event = new event_entity("entity", "has_deletion_rights");
			    $event->set_entity_id($this->id);
			    $event->set_entity_type($this->get_aut_const());
			    $event->set_user_id($PMBuserid);
			    $evt_handler->send($event);
			    if($event->get_error_message()){
			    	information_message('', $event->get_error_message(), 1, $this->get_permalink());
			    } else {
			    	$this->proceed_delete();
			    }
				break;
			case 'update':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
			    $updated_id = $this->proceed_update();
			    if($updated_id) {
			        if ($save_and_continue) {
			            $this->id = 0;
			            $parent = $category_parent_id;
			            $this->proceed_form();
			        } else {
			            print $this->get_display_view($updated_id);
			        }
			    }
			    break;
			case 'categ_replace':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
				$this->proceed_replace();
				break;
			case 'duplicate' :
				$this->proceed_duplicate();
				break;
			case 'categ_form':
			    if($this->id){
			        $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			        if($entity_locking->is_locked()){
			            print $entity_locking->get_locked_form();
			            break;
			        }
			    }
			    $this->proceed_form();
				break;
			case 'categorie_last':
				$this->proceed_last();
				break;
			case 'search':
				print $this->get_display_list();
				break;
			case 'thes' :
				$this->proceed_thesaurus_default();
				break;
			case 'thes_form' :
				$this->proceed_thesaurus_form();
				break;
			case 'thes_update' :
				$this->proceed_thesaurus_update();
				break;
			case 'thes_delete' :
				$this->proceed_thesaurus_delete();
				break;
			default:
				$this->proceed_default();
				break;
		}
	}

	public function proceed_delete() {
		if ($this->id) {
			$object_instance = $this->get_object_instance();
			$response_deleted_category = $object_instance->delete();

			if ($response_deleted_category) {
				print $response_deleted_category;
			}
		}
		print $this->get_display_hierarchical_list();
	}

	public function proceed_replace() {
		global $msg;
		global $by, $aut_link_save;

		$noeuds = new noeuds($this->id);
		if(method_exists('noeuds', 'set_controller')) {
			noeuds::set_controller($this);
		}
		if(!$by) {
			if (noeuds::hasChild($this->id)) {//On regarde si le noeud remplacé a des enfants
				error_message($msg[321],$msg["categ_imposible_remplace_avec_fille"], 1, $this->url_base."&id=".$this->id."&sub=categ_form&parent=".$this->parent);
				exit();
			}else{
				$noeuds->replace_categ_form($this->parent);
			}
		}else {
			$rep=$noeuds->replace($by,$aut_link_save);
			if(!$rep){
				$this->id=0;
				$this->parent=0;
				print $this->get_display_hierarchical_list();
			}else{
				error_message($msg[132], $rep, 1, $this->url_base."&sub=categ_replace&id=".$this->id."&parent=".$this->parent);
			}
		}
	}

	public function proceed_duplicate() {
		$object_instance = $this->get_object_instance();
		$id = 0;
// 		$object_instance->show_form($object_instance->type,true);
	}

	public function proceed_update() {
		global $msg;
		global $include_path;
		global $category_na, $category_cm, $category_libelle;
		global $max_categ;
		global $category_parent, $category_parent_id;
		global $category_voir, $category_voir_id;
		global $authority_import_denied;
		global $not_use_in_indexation;
		global $num_aut;
		global $authority_statut;
		global $authority_thumbnail_url;
		global $pmb_synchro_rdf;
		global $thesaurus_concepts_active;

		if (noeuds::isRacine($this->id)) {
			error_form_message($msg['categ_forb']);
			return;
		}

		if(!strlen($category_parent)) $category_parent_id = 0;
		if(!strlen($category_voir)) $category_voir_id = 0;

		if ($this->id && ($category_parent_id==$this->id || $category_voir_id==$this->id)) {
			error_form_message($msg["categ_update_error_parent_see"]);
			exit ;
		}

		$parent_ancestors = noeuds::listAncestors($category_parent_id);
		if ($this->id && in_array($this->id, $parent_ancestors)) {
			error_form_message($msg["categ_update_error_parent_daughter"]);
			exit ;
		}

		//recuperation de la table des langues
		$langages = new XMLlist("$include_path/messages/languages.xml", 1);
		$langages->analyser();
		$lg = $langages->table;

		//recuperation du thesaurus session
		$this->id_thes = thesaurus::getSessionThesaurusId();
		$thes = new thesaurus($this->id_thes);

		// libelle langue defaut thesaurus non renseigne
		if ( (trim($category_libelle[$thes->langue_defaut])) == '' ) {
			error_form_message($msg["thes_libelle_categ_ref_manquant"].'\n('.$lg[$thes->langue_defaut].')');
			exit ;
		}

		//Vérification de l'unicité du numéro d'autorité
		$num_aut=trim(stripslashes($num_aut));

		if ($num_aut && !noeuds::isUnique($this->id_thes, $num_aut,$this->id) ) {
			error_form_message($msg['categ_num_aut_not_unique']);
			exit;
		}
		//synchro_rdf : on empile les noeuds impactés pour les traiter plus loin
		if($pmb_synchro_rdf){
			$arrayIdImpactes=array();
			if($this->id){
				$noeud=new noeuds($this->id);
				//on est en mise à jour
				$arrayIdImpactes[]=$this->id;
				//parent
				if($noeud->num_parent != $thes->num_noeud_racine && $this->id_thes == $noeud->num_thesaurus){
					$arrayIdImpactes[]=$noeud->num_parent;
				}
				//enfants
				$res=noeuds::listChilds($this->id,1);
				if(pmb_mysql_num_rows($res)){
					while($row=pmb_mysql_fetch_array($res)){
						$arrayIdImpactes[]=$row[0];
					}
				}
				//renvoi_voir
				if($noeud->num_renvoi_voir){
					$arrayIdImpactes[]=$noeud->num_renvoi_voir;
				}
			}else{
				//on est en création : rien à supprimer
			}
		}
		//traitement noeud

		$authority_statut = (int) $authority_statut;
		if(!$authority_statut){
			$authority_statut = 1;
		}
		if($this->id) {
			//noeud existant
			$noeud = new noeuds($this->id);
			//Si pas de parent, le parent est le noeud racine du thesaurus
			if (!$category_parent_id) {
			    //on teste d'abord si le thesaurus en session est le même que celui du noeud
			    if ($this->id_thes == $noeud->num_thesaurus) {
			        $category_parent_id = $thes->num_noeud_racine;
			    } else {
			        $this->id_thes = $noeud->num_thesaurus;
			        $thes = new thesaurus($this->id_thes);
			        $category_parent_id = $thes->num_noeud_racine;
			    }
			}
			if (!noeuds::isProtected($this->id)) {
				$noeud->num_parent = $category_parent_id;
				$noeud->num_renvoi_voir = $category_voir_id;
				$noeud->authority_import_denied = (isset($authority_import_denied) ? $authority_import_denied : 0);
				$noeud->not_use_in_indexation = (int) $not_use_in_indexation;
				$noeud->autorite = $num_aut;
				$noeud->num_statut = $authority_statut;
				$noeud->thumbnail_url = $authority_thumbnail_url;
				$noeud->save();
			}
		} else {
		    //Si pas de parent, le parent est le noeud racine du thesaurus
		    if (!$category_parent_id) {
		        $category_parent_id = $thes->num_noeud_racine;
		    }
			//noeud a creer
			$noeud = new noeuds();
			$noeud->num_parent = $category_parent_id;
			$noeud->num_renvoi_voir = $category_voir_id;
			$noeud->autorite = $num_aut;
			$noeud->num_thesaurus = $thes->id_thesaurus;
			$noeud->authority_import_denied = (isset($authority_import_denied) ? $authority_import_denied : 0);
			$noeud->not_use_in_indexation = (int) $not_use_in_indexation;
			$noeud->num_statut = $authority_statut;
			$noeud->thumbnail_url = $authority_thumbnail_url;
			$noeud->save();
			$this->id = $noeud->id_noeud;
		}
		// Indexation concepts
		if($thesaurus_concepts_active == 1 ){
			$index_concept = new index_concept($this->id, TYPE_CATEGORY);
			$index_concept->save();
		}
		// liens entre autorités
		$aut_link= new aut_link(AUT_TABLE_CATEG,$this->id);
		$aut_link->save_form();

		global $pmb_map_activate;
		if($pmb_map_activate){
			$map = new map_edition_controler(AUT_TABLE_CATEG, $this->id);
			$map->save_form();
		}

		//traitement categories
		foreach($lg as $key=>$value) {
			if (isset($category_libelle[$key]) && ($category_libelle[$key]) !== NULL ) {

				if ( ($category_libelle[$key] !== '')  ||
						( ($category_libelle[$key] === '') && (categories::exists($this->id, $key)) ) ){

							$cat = new categories($this->id, $key);
							$cat->libelle_categorie = stripslashes($category_libelle[$key]);
							$cat->note_application = stripslashes($category_na[$key]);
							$cat->comment_public = stripslashes($category_cm[$key]);
							$cat->index_categorie = strip_empty_words($category_libelle[$key]);
							$cat->save();
				}
			}
		}

		$aut_pperso= new aut_pperso("categ",$this->id);
		if($aut_pperso->save_form()){ //Traitement des erreurs de champs persos
			error_message($msg['319'], $aut_pperso->error_message, 1, $this->url_base.'&sub=categ_form&id='.$this->id);
			return false;
		}

		if (!noeuds::isProtected($this->id)) {

			//Ajout des renvois "voir aussi"
			$requete="DELETE FROM voir_aussi WHERE num_noeud_orig=".$this->id;
			pmb_mysql_query($requete);
			for ($i=0; $i<$max_categ; $i++) {
				$categ_id="f_categ_id".$i;
				$categ_rec = "f_categ_rec".$i;
				global ${$categ_id}, ${$categ_rec};
				if (${$categ_id} && ${$categ_id}!=$this->id) {
					$requete="INSERT INTO voir_aussi (num_noeud_orig, num_noeud_dest, langue) VALUES ($this->id,".${$categ_id}.",'".$thes->langue_defaut."' )";
					pmb_mysql_query($requete);
					if (${$categ_rec}) {
						$requete="INSERT INTO voir_aussi (num_noeud_orig, num_noeud_dest, langue) VALUES (".${$categ_id}.",".$this->id.",'".$thes->langue_defaut."' )";
						$indexation_authority = new indexation_authority($include_path."/indexation/authorities/categories/champs_base.xml", "authorities", AUT_TABLE_CATEG);
						$indexation_authority->maj(${$categ_id}, 'subject');
					} else {
						$requete="DELETE from voir_aussi where num_noeud_dest = '".$this->id."' and num_noeud_orig = '".${$categ_id}."'	";
					}
					pmb_mysql_query($requete);

				}
			}
		}
		//synchro_rdf : le noeud a été créé/modifié
		if($pmb_synchro_rdf){
			//De nouveaux noeuds impactés ?
			if((!count($arrayIdImpactes))||(!in_array($this->id,$arrayIdImpactes))){
				$arrayIdImpactes[]=$this->id;
			}
			if($noeud->num_parent!=$thes->num_noeud_racine){
				if((!count($arrayIdImpactes))||(!in_array($noeud->num_parent,$arrayIdImpactes))){
					$arrayIdImpactes[]=$noeud->num_parent;
				}
			}
			//enfants
			$res=noeuds::listChilds($this->id,1);
			if(pmb_mysql_num_rows($res)){
				while($row=pmb_mysql_fetch_array($res)){
					if((!count($arrayIdImpactes))||(!in_array($row[0],$arrayIdImpactes))){
						$arrayIdImpactes[]=$row[0];
					}
				}
			}
			//renvoi_voir
			if($noeud->num_renvoi_voir){
				if((!count($arrayIdImpactes))||(!in_array($noeud->num_renvoi_voir,$arrayIdImpactes))){
					$arrayIdImpactes[]=$noeud->num_renvoi_voir;
				}
			}
			//on met le tout à jour
			$synchro_rdf=new synchro_rdf();
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
			//On met à jour le thésaurus pour les topConcepts
			$synchro_rdf->updateAuthority($this->id_thes,'thesaurus');
		}
		return $this->id;
	}

	public function proceed_last() {
		global $last_param;
		global $tri_param, $limit_param;
		global $pmb_nb_lastautorities;
		global $clef, $nbr_lignes;

		$last_param=1;
		$tri_param = $this->get_last_order();
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = '';
		$nbr_lignes = 0 ;
		print $this->get_display_hierarchical_list();
	}

	public function proceed_default() {

		print $this->get_display_hierarchical_list();
	}

	public function get_thesaurus_display_list() {
		global $msg;
		global $thes_browser;

		$query = "select id_thesaurus, libelle_thesaurus, num_noeud_racine from thesaurus ORDER BY 2 ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result) == 0) {
			$browser_content = $msg[4051];
		} else {
			$odd_even = 1;
			$browser_content = '';
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($odd_even==0) {
					$browser_content .= "	<tr class='odd'>";
					$odd_even=1;
				} else {
					$browser_content .= "	<tr class='even'>";
					$odd_even=0;
				}
				$browser_content .= "<td>";
				$browser_content .= "<a href='".$this->url_base."&sub=thes_form&id_thes=".$row->id_thesaurus."' >".$row->libelle_thesaurus."</a>";
				$browser_content .= "</td></tr>";
			}
		}
		$display = $thes_browser;
		$display = str_replace('!!browser_content!!', $browser_content, $display);
		$display = str_replace('!!action!!', $this->url_base."&sub=thes_form&id_thes=0", $display);
		return $display;
	}

	public function proceed_thesaurus_default() {
		print $this->get_thesaurus_display_list();
	}

	public function proceed_thesaurus_form() {
		$thesaurus = new thesaurus($this->id_thes);
		print $thesaurus->get_form();
	}

	public function proceed_thesaurus_update() {
		global $msg;
		global $libelle_thesaurus, $langue_defaut;

		// libelle thesaurus non renseigne
		if ( (trim($libelle_thesaurus)) == '' ) {
			error_form_message($msg["thes_libelle_manquant"]);
			return;
		}
		if($this->id_thes) {
			//thesaurus existant
			$thes = new thesaurus($this->id_thes);
			$thes->libelle_thesaurus = $libelle_thesaurus;
			$thes->save();
		} else {
			//thesaurus a creer
			$thes = new thesaurus();
			$thes->libelle_thesaurus = $libelle_thesaurus;
			$thes->langue_defaut = $langue_defaut;
			$thes->save();
		}
		print $this->get_thesaurus_display_list();
	}

	public function proceed_thesaurus_delete() {
		global $msg;
		global $opac_thesaurus_defaut, $thesaurus_defaut, $deflt_thesaurus;

		if (thesaurus::hasNotices($this->id_thes)){		//le thesaurus est utilisé dans les notices.
			error_form_message($msg["thes_suppr_impossible"]);
			exit;
		} else {
			if(($opac_thesaurus_defaut === $this->id_thes) or ($thesaurus_defaut === $this->id_thes) or ($deflt_thesaurus === $this->id_thes)){
				error_form_message($msg["thes_suppr_categ_utilisee"]);
			}else{
				thesaurus::delete($this->id_thes);
				thesaurus::setSessionThesaurusId(-1);
			}
		}
		print $this->get_thesaurus_display_list();
	}

	public function proceed_form() {
		global $msg, $id;

		if (noeuds::isRacine($this->id)) {
			error_form_message($msg['categ_forb']);
			exit();
		}
		$unlock_unload_script = "";
		if($this->id){
		    $entity_locking = new entity_locking($id, $this->get_type_const());
		    $entity_locking->lock_entity();
		    $unlock_unload_script = $entity_locking->get_polling_script();
		}

		$object_instance = $this->get_object_instance();
		ob_start();
		$object_instance->show_form();
		$entity_form = ob_get_contents();
		$entity_form = str_replace('<form', '<form data-advanced-form="true"', $entity_form);
		ob_end_clean();
		print $entity_form;
		print $this->get_selector_js_script();
		print $unlock_unload_script;
	}

	public function get_searcher_instance() {
		return searcher_factory::get_searcher('categories', '', $this->user_input);
	}

	protected function get_display_header_list() {
		global $msg;
		global $sub;
		$this->num_auth_present = searcher_authorities_categories::has_authorities_sources('category');

		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			".($this->num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '');
		if($sub == 'search') {
			$display .= "<th>".$msg["categ_na"]."</th>";
		}
		$display .= "<th>".$msg["count_notices_assoc"]."</th>";
		$display.= "<th></th>";
		$display.= "</tr>";
		return $display;
	}

	protected function get_display_columns() {
		global $charset;
		global $page, $nb_per_page_gestion;
		global $sub;

		$display = '';
		$object_instance = $this->authority->get_object_instance();

		switch($sub) {
			case 'search':
				$link_categ = $this->url_base."&sub=categ_form&parent=0&id=".$object_instance->id."&id_thes=".$object_instance->thes->id_thesaurus."&user_input=".rawurlencode($this->user_input)."&nbr_lignes=".$this->nbr_lignes."&page=".$page."&nb_per_page=".$nb_per_page_gestion;
				$display .= "<td style='vertical-align:top' onmousedown=\"document.location='$link_categ';\">";
				$display .= $this->authority->get_display_statut_class_html();
				if($object_instance->has_child) {
				    $display .= "<a href='".$this->url_base."&parent=".$object_instance->id."'>";
				    $display .= "<img src='".get_url_icon('folderclosed.gif')."' style='border:0px; margin:3px 3px'></a>";
				} else {
				    $display .= "<img src='".get_url_icon('doc.gif')."' style='border:0px; margin:3px 3px'>";
				}
				if ($this->id_thes == -1) {
					$display .= '['.htmlentities($object_instance->thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
				}
				if (isset($lg_search) && $lg_search) $display.= '['.$lg[$object_instance->langue].'] ';
				if($object_instance->voir_id) {
					$temp = authorities_collection::get_authority(AUT_TABLE_CATEG, $object_instance->voir_id);
					$display .= $object_instance->libelle." -&gt; <i>";
					$display .= $temp->catalog_form;
					$display.= "@</i>";
				} else {
					$display .= $object_instance->catalog_form;
				}
				$display .= "
				</td>
				<td style='vertical-align:top' onmousedown=\"document.location='$link_categ';\">
					".$object_instance->commentaire."
				</td>";
				break;
			default:
			    if (!empty($object_instance)) {
    				$display .= '<td>'.$this->authority->get_display_statut_class_html();
    				if(!empty($object_instance->has_child)) {
    					$display .= "<a href='".$this->url_base."&parent=".$object_instance->id."'>";
    					$display .= "<img src='".get_url_icon('folderclosed.gif')."' style='border:0px; margin:3px 3px'></a>";
    				} else {
    					$display .= "<img src='".get_url_icon('doc.gif')."' style='border:0px; margin:3px 3px'>";
    				}
    				if ($object_instance->autorite || $object_instance->commentaire) {
    					$zoom_comment = "<div id='zoom_comment".$object_instance->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
    					if ($object_instance->autorite) $zoom_comment.=htmlentities('('.$object_instance->autorite.') ', ENT_QUOTES, $charset);
    					if ($object_instance->commentaire) $zoom_comment.= htmlentities($object_instance->commentaire,ENT_QUOTES, $charset);
    					$zoom_comment.="</div>";
    					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$object_instance->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$object_instance->id."'); z.style.display='none'; \"" ;
    				} else {
    					$zoom_comment = "" ;
    					$java_comment = "" ;
    				}
    				$display .= "<a href='".$this->get_edit_link($this->authority->get_num_object())."&nbr_lignes=".$this->nbr_lignes."&page=".$page."' $java_comment >";
    				$display .= $object_instance->get_isbd();
    				$display .= '</a>';
    				$display .= $zoom_comment.'</td>';
			    }
				break;
		}
		//Numéros d'autorite
		if($this->num_auth_present){
			$display .= "<td>".searcher_authorities_categories::get_display_authorities_sources($this->authority->get_num_object(), 'category')."</td>";
		}
		return $display;
	}

	protected function search_form() {
		global $msg, $charset;
		global $thesaurus_mode_pmb;
		global $user_query;
		global $authority_statut;
		global $lg_search;

		$liste_thesaurus = thesaurus::getThesaurusList();
		$lien_thesaurus = '';
		if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
			$lien_thesaurus = "<a href='".$this->url_base."&sub=thes'>".$msg['thes_lien']."</a>";
		}

		$user_query=str_replace('<!-- sel_thesaurus -->', thesaurus::getSelector($this->id_thes, $this->url_base), $user_query);
		$user_query=str_replace('<!-- lien_thesaurus -->', $lien_thesaurus, $user_query);
		$authority_statut = intval($authority_statut);
		$user_query = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_CATEG, $authority_statut, true), $user_query);

		if ($this->id_thes >= 1)
			$lien_imprimer_thesaurus = "&nbsp;<a href='#' onClick=\"openPopUp('./print_thesaurus.php?current_print=2&action=print_prepare&aff_num_thesaurus=".$this->id_thes."','print'); return false;\">".$msg['print_thesaurus']."</a> ";
		else
			$lien_imprimer_thesaurus = "" ;
		$user_query=str_replace('<!-- imprimer_thesaurus -->',$lien_imprimer_thesaurus,$user_query);

		//affichage du choix de langue pour la recherche
		$sel_langue = "<div class='row'>";
		$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' ".($lg_search ? " checked='checked' " : "")." />&nbsp;<label for='lg_search' class='etiquette'>".htmlentities($msg['thes_sel_langue'],ENT_QUOTES, $charset)."</label>";
		$sel_langue.= "</div><br />";
		$user_query = str_replace("<!-- sel_langue -->", $sel_langue, $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities($this->user_input, ENT_QUOTES, $charset), $user_query);

		categ_browser::search_form($this->parent);
	}

	protected function get_pagination_link() {
		global $authority_statut, $parent;
		global $lg_search;
		global $sub;

		$parent = intval($parent);
		return $this->url_base."&sub=".$sub."&user_input=".rawurlencode($this->user_input).'&authority_statut='.$authority_statut.($parent ? "&parent=".$parent : "").'&id_thes='.$this->id_thes.'&lg_search='.$lg_search;
	}

	protected function get_query_notice_count() {
		$object_instance = $this->authority->get_object_instance();
		if (empty($object_instance)) {
		    // Si il y a un mauvais enregistrement, au moins ça ne plante pas
		    return 0;
		}
		return $object_instance->notice_count(false);
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=category&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=categ_form&parent=".$this->parent."&id=".$id;
	}

	protected function get_results_title() {
		global $msg;

		return $msg[1320];
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[211],
			str_replace('!!categ_cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg['categ_no_categ_found_with']),
			0,
			$this->url_base.'&sub=search'
		);
	}

	protected function get_search_mode() {
		return 1;
	}

	protected function get_aut_type() {
		return "categ";
	}

	protected function get_last_order() {
		return 'order by id_noeud desc ';
	}

	public function set_parent($parent) {
	    $this->parent = (int) $parent;
	}

	public function set_id_thes($id_thes) {
	    $this->id_thes = (int) $id_thes;
	}

	public function get_back_url() {
		global $parent;

		$this->back_url = parent::get_back_url();
		if($parent) $this->back_url .= "&parent=".$parent;
		return $this->back_url;
	}

	public function get_delete_url() {
		global $parent;

		$this->delete_url = parent::get_delete_url();
		if($parent) $this->delete_url .= "&parent=".$parent;
		return $this->delete_url;
	}

	protected function get_aut_const(){
	    return TYPE_CATEGORY;
	}
}
