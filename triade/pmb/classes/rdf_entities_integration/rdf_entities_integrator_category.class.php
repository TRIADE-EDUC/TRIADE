<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_category.class.php,v 1.9 2020/11/26 13:29:06 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator_authority.class.php');
require_once($class_path.'/categories.class.php');
require_once($class_path.'/noeuds.class.php');

class rdf_entities_integrator_category extends rdf_entities_integrator_authority {
	
	protected $table_name = 'noeuds';
	
	protected $table_key = 'id_noeud';
	
	protected $ppersos_prefix = 'categ';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
            'http://www.pmbservices.fr/ontology#has_thesaurus' => 'num_thesaurus',
            'http://www.pmbservices.fr/ontology#authority_number' => 'autorite',
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
				'http://www.pmbservices.fr/ontology#parent_category' => 'num_parent',
				'http://www.pmbservices.fr/ontology#category_see' => 'num_renvoi_voir'
		));
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
		$this->linked_entities = array_merge(parent::init_linked_entities(), array(
				'http://www.pmbservices.fr/ontology#has_concept' => array(
						'table' => 'index_concept',
						'reference_field_name' => 'num_object',
						'external_field_name' => 'num_concept',
						'other_fields' => array(
								'type_object' => TYPE_CATEGORY
						)
				),
				'http://www.pmbservices.fr/ontology#category_see_also' => array(
						'table' => 'voir_aussi',
						'reference_field_name' => 'num_noeud_orig',
						'external_field_name' => 'num_noeud_dest',
						'other_fields' => array(
								'langue' => 'fr_FR'
						)
				)
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
	    $this->special_fields = array_merge(parent::init_special_fields(), array(
	        'http://www.pmbservices.fr/ontology#thumbnail_url' => array(
	            "method" => array($this,"insert_thumbnail_url"),
	            "arguments" => array(AUT_TABLE_CATEG)
	        ),
	    ));
		return $this->special_fields;
	}
	
	protected function post_create($uri) {
		global $thesaurus_defaut, $lang;
		
		// On vérifie les valeurs nécessaires
		$thesaurus_id = $thesaurus_defaut;
		$query = 'select num_thesaurus, num_parent from noeuds where id_noeud = '.$this->entity_id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if ($row->num_thesaurus) {
			$thesaurus_id = $row->num_thesaurus; 
		}
		// On récupère la catégorie parente et la langue
		$parent_id = $row->num_parent;
		$thes_lang = $lang;
		$query = 'select langue_defaut, num_noeud_racine from thesaurus where id_thesaurus = '.$thesaurus_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$thesaurus = pmb_mysql_fetch_object($result);
			if (!$parent_id) {
				$parent_id = $thesaurus->num_noeud_racine;
			}
			$thes_lang = $thesaurus->langue_defaut;
		}
		// On met à jour avec les valeurs qui vont bien
		$query = 'update noeuds set num_parent = '.$parent_id.', visible = "1", num_thesaurus = '.$thesaurus_id.' where id_noeud = '.$this->entity_id;
		pmb_mysql_query($query);
		
		// On met à jour le chemin
		$id_tmp = $this->entity_id;
		while (true) {
			$query = "select num_parent from noeuds where id_noeud = '".$id_tmp."' limit 1";
			$result = pmb_mysql_query($query);
			$id_tmp = $id_cur = pmb_mysql_result($result, 0, 0);
			if (!$id_cur || $id_cur == $thesaurus->num_noeud_racine) break;
			if ($path) $path = '/'.$path;
			$path = $id_tmp.$path;
		}
		noeuds::process_categ_path($this->entity_id, $path);
		
		// On renseigne la table categories
		$category_label = $this->store->get_property($uri,"pmb:label");
		if (count($category_label)) {
			// On supprime si il y a une entrée dans la table catégorie
			pmb_mysql_query('delete from categories where num_noeud = "'.$this->entity_id.'" and langue = "'.$thes_lang.'"');
			
			$category_note = $this->store->get_property($uri,"pmb:note");
			$category_comment = $this->store->get_property($uri,"pmb:comment");
			$query = 'insert into categories (num_thesaurus, num_noeud, langue, libelle_categorie, note_application, comment_public) values ';
			$query.= '("'.$thesaurus_id.'", "'.$this->entity_id.'", "'.$thes_lang.'", "'.addslashes($category_label[0]['value']).'", "'.addslashes($category_note[0]['value']).'", "'.addslashes($category_comment[0]['value']).'")';
			pmb_mysql_query($query);
		}
		
		// Audit
		if ($this->integration_type && $this->entity_id) {
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_CATEG.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			// Indexation
			categories::update_index($this->entity_id);
		}
	}
}