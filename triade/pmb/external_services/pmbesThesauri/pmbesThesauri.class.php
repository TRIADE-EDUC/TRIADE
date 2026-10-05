<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesThesauri.class.php,v 1.10 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

class pmbesThesauri extends external_services_api_class {
	
	public function list_thesauri($OPACUserId=-1) {
		$thesauri = thesaurus::getThesaurusList();
		$results = array();
		global $opac_thesaurus, $opac_thesaurus_defaut;
		foreach ($thesauri as $id => $caption) {
			if ($OPACUserId != -1 && $opac_thesaurus == 0 && $opac_thesaurus_defaut != $id)
				continue;
			$athesaurus = new thesaurus($id);
			$results[] = array(
				'thesaurus_id' => $id,
				'thesaurus_caption' => encoding_normalize::utf8_normalize($caption),
				'thesaurus_num_root_node' => $athesaurus->num_noeud_racine,
				'thesaurus_num_unclassed_node' => $athesaurus->num_noeud_nonclasses,
				'thesaurus_num_orphans_node' => $athesaurus->num_noeud_orphelins,
			);
		}

		return $results;
	}

	public function fetch_node_notice_ids($node_id, $OPACUserId=-1) {
		$node_id = intval($node_id);
		if (!$node_id)
			return FALSE;

		global $opac_auto_postage_nb_descendant, $opac_auto_postage_nb_montant;
		$nb_level_descendant=$opac_auto_postage_nb_descendant;
		$nb_level_montant=$opac_auto_postage_nb_montant;
		
		$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
		$_SESSION["nb_level_parents"]=	$nb_level_montant;
		
		$q = "select path from noeuds where id_noeud = '".$node_id."' ";
		$r = pmb_mysql_query($q);
		$path=pmb_mysql_result($r, 0, 0);
		$nb_pere=substr_count($path,'/');

			
		// Si un path est renseigné et le paramètrage activé
		global $opac_auto_postage_descendant, $opac_auto_postage_montant;
		if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant) && ($nb_level_montant || $nb_level_descendant)){
			//Recherche des fils 
			if(($opac_auto_postage_descendant)&& $nb_level_descendant) {
				if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
					$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
				else 
					$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
			} else {
				$liste_fils=" id_noeud='".$node_id."' ";
			}
					
			// recherche des pères
			if(($opac_auto_postage_montant) && $nb_level_montant ) {
				
				$id_list_pere=explode('/',$path);	
				$stop_pere=0;
				if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere = $nb_pere - intval($nb_level_montant);
				if($stop_pere<0) $stop_pere=0;
				for($i=$nb_pere;$i>=$stop_pere; $i--) {
					$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
				}
			}			
			// requete permettant de remonter les notices associées à la liste des catégories trouvées;
			//$suite_req = " FROM noeuds inner join notices_categories on id_noeud=num_noeud inner join notices on notcateg_notice=notice_id, notice_statut 
			//	WHERE ($liste_fils $liste_pere)	and (notices.statut = notice_statut.id_notice_statut 
			//	and ((notice_statut.notice_visible_opac = 1 and notice_statut.notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_statut.notice_visible_opac_abon=1 and notice_statut.notice_visible_opac = 1)":"").")) ";
			$suite_req = " FROM noeuds join notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id !!opac_phototeque!! ";
			$suite_req.= "WHERE ($liste_fils $liste_pere) ";
			
		} else {	
			// cas normal d'avant		
			//$suite_req=" FROM notices_categories, notices, notice_statut WHERE (notices_categories.num_noeud = '".$id."' and notices_categories.notcateg_notice = notices.notice_id) and (notices.statut = notice_statut.id_notice_statut and ((notice_statut.notice_visible_opac = 1 and notice_statut.notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_statut.notice_visible_opac_abon=1 and notice_statut.notice_visible_opac = 1)":"").")) ";
			$suite_req = " FROM notices_categories join notices on notcateg_notice=notice_id !!opac_phototeque!! ";
			$suite_req.= "WHERE num_noeud=".$node_id." ";
		}

		$requete = "SELECT distinct notice_id ".str_replace("!!opac_phototeque!!","",$suite_req);
		$res = pmb_mysql_query($requete);
		$results = array();
		while($row = pmb_mysql_fetch_row($res)) {
			$results[] = $row[0];
		}
		
		//Je filtre les notices en fonction des droits
		$results=$this->filter_tabl_notices($results);
		
		return $results;
		
	}
	
	public function fetch_node_full($node_id, $OPACUserId=-1) {
		return array(
			'node' => $this->proxy_parent->pmbesThesauri_fetch_node($node_id, $OPACUserId),
			'notice_ids' => $this->proxy_parent->pmbesThesauri_fetch_node_notice_ids($node_id, $OPACUserId)
		);
	}
	
	public function fetch_node($node_id, $OPACUserId=-1) {
		$node_id = intval($node_id);
		if (!$node_id)
			return FALSE;
		$node = new noeuds($node_id);
		$result = array(
			'node_id' => $node->id_noeud,
			'node_thesaurus' => $node->num_thesaurus,
			'node_target_id' => 0,
			'node_target_categories' => array()
		);
		if ($node->num_renvoi_voir) {
			$result['node_target_id'] = $node->num_renvoi_voir;
			$q = "select * from categories where num_noeud = '".$node->num_renvoi_voir."'";
			$r = pmb_mysql_query($q);
			$result['node_target_categories'] = array();
			while($obj = pmb_mysql_fetch_object($r)) {
				$categ = array();
				$categ['node_id'] = $node_id;
				$categ['category_caption'] = encoding_normalize::utf8_normalize($obj->libelle_categorie);
				$categ['category_lang'] = encoding_normalize::utf8_normalize($obj->langue);
				$result['node_target_categories'][] = $categ;
			}
		}

		$q = "select * from categories where num_noeud = '".$node_id."'";
		$r = pmb_mysql_query($q);
		$result['node_categories'] = array();
		while($obj = pmb_mysql_fetch_object($r)) {
			$categ = array();
			$categ['node_id'] = $node_id;
			$categ['category_caption'] = encoding_normalize::utf8_normalize($obj->libelle_categorie);
			$categ['category_lang'] = encoding_normalize::utf8_normalize($obj->langue);
			$result['node_categories'][] = $categ;
		}

		$path_ids = noeuds::listAncestors($node_id);
		$result['node_path'] = array();
		if ($path_ids) {
			$q = "select * from categories where num_noeud IN(".implode(',', $path_ids).") order by num_noeud";
			$r = pmb_mysql_query($q);
			$result['node_path'] = array();
			$current_node_id = 0;
			$categs = array();
			while($obj = pmb_mysql_fetch_object($r)) {
				if (!$current_node_id)
					$current_node_id = $obj->num_noeud;
				if ($current_node_id != $obj->num_noeud) {
					$result['node_path'][] = array(
						'node_id' => $current_node_id,
						'categories' => $categs,
					);
					$categs = array();
					$current_node_id = $obj->num_noeud;
				}
				$categ = array();
				$categ['node_id'] = $current_node_id;
				$categ['category_caption'] = encoding_normalize::utf8_normalize($obj->libelle_categorie);
				$categ['category_lang'] = encoding_normalize::utf8_normalize($obj->langue);
				$categs[] = $categ;
			}
			if ($current_node_id)
				$result['node_path'][] = array(
					'node_id' => $current_node_id,
					'categories' => $categs,
				);
		}
		
		$children = array();
		$children_res = noeuds::listChilds($node_id, 1);
		while($row=pmb_mysql_fetch_assoc($children_res)) {
			$children[] = $row['id_noeud'];
		}
		$result['node_children'] = array();
		if ($children) {
			$q = "select noeuds.id_noeud, noeuds.num_renvoi_voir, categories.* from categories left join noeuds on (noeuds.id_noeud = categories.num_noeud) where noeuds.id_noeud IN(".implode(',', $children).") order by num_noeud, libelle_categorie";
			$r = pmb_mysql_query($q);
			$result['node_children'] = array();
			$current_node_id = 0;
			$current_islink = false;
			$categs = array();
			while($obj = pmb_mysql_fetch_object($r)) {
				if (!$current_node_id)
					$current_node_id = $obj->num_noeud;
				if ($current_node_id != $obj->num_noeud) {
					$result['node_children'][] = array(
						'node_id' => $current_node_id,
						'categories' => $categs,
						'is_link' => $current_islink,
					);
					$categs = array();
					$current_node_id = $obj->num_noeud;
					$current_islink = $obj->num_renvoi_voir > 0 ? true : false;
				}
				$categ = array();
				$categ['node_id'] = $current_node_id;
				$categ['category_caption'] = encoding_normalize::utf8_normalize($obj->libelle_categorie);
				$categ['category_lang'] = encoding_normalize::utf8_normalize($obj->langue);
				$categs[] = $categ;
			}
			if ($current_node_id)
				$result['node_children'][] = array(
					'node_id' => $current_node_id,
					'categories' => $categs,
					'is_link' => $current_islink,
				);
		}
		
		$result['node_seealso'] = array();
		$q = "select voir_aussi.num_noeud_dest, categories.* from voir_aussi left join categories on (voir_aussi.num_noeud_dest = categories.num_noeud) where num_noeud_orig = ".$node_id." order by voir_aussi.num_noeud_dest";
		$r = pmb_mysql_query($q);
		$current_node_id = 0;
		$categs = array();
		while($obj = pmb_mysql_fetch_object($r)) {
			if (!$current_node_id)
				$current_node_id = $obj->num_noeud;
			if ($current_node_id != $obj->num_noeud) {
				$result['node_seealso'][] = array(
					'node_id' => $current_node_id,
					'categories' => $categs,
				);
				$categs = array();
				$current_node_id = $obj->num_noeud;
			}
			$categ = array();
			$categ['node_id'] = $current_node_id;
			$categ['category_caption'] = encoding_normalize::utf8_normalize($obj->libelle_categorie);
			$categ['category_lang'] = encoding_normalize::utf8_normalize($obj->langue);
			$categs[] = $categ;
		}
		if ($current_node_id)
			$result['node_seealso'][] = array(
				'node_id' => $current_node_id,
				'categories' => $categs,
			);

		return $result;
	}
}




?>