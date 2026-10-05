<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_cms_parametres_perso.class.php,v 1.5 2023/05/05 09:48:09 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");

class onto_cms_parametres_perso extends onto_parametres_perso{
	
	public $num_type;
	public $cms_types;
	public $option_visibilite;
	
	/**
	 * déclaration des uri liées aux préfixes cms
	 *
	 * @var array
	 */
	public static $cms_entities_uri = array(
			'cms_article' => 'http://www.pmbservices.fr/ontology#cms_article',
			'cms_section' => 'http://www.pmbservices.fr/ontology#cms_section',
	);
	
	public static $st_fields;
	
	public function  __construct() {
	
		$this->option_visibilite = array(
				'multiple' => "none",
				'opac_sort' => "none",
				'exclusion' => "none"
		);
	
		$this->prefix="cms_editorial";
		
		$this->fetch_data();
	}
	protected function fetch_data(){
		global $charset;
		
		$this->get_cms_types();
		
// 		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		if(!isset(static::$st_fields)){
			
			foreach($this->cms_types as $id => $detail){
				$requete="
						SELECT idchamp, name, titre, type, datatype, obligatoire, options, multiple, search, export, exclusion_obligatoire, pond, opac_sort, comment 
						FROM cms_editorial_custom
						JOIN cms_editorial_types
						ON num_type = id_editorial_type
						WHERE (num_type=".$id." OR editorial_type_element = '".$detail['type']."_generic')
						ORDER BY ordre";
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)==0)
					static::$st_fields[$detail['type'].'_'.$id] = false;
				else {
					while ($r=pmb_mysql_fetch_object($resultat)) {
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["DATATYPE"]=$r->datatype;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["NAME"]=$r->name;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["TITRE"]=$r->titre;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["TYPE"]=$r->type;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["OPTIONS"][0] =_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["MANDATORY"]=$r->obligatoire;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["OPAC_SHOW"]=$r->multiple;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["SEARCH"]=$r->search;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["EXPORT"]=$r->export;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["EXCLUSION"]=$r->exclusion_obligatoire;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["POND"]=$r->pond;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["OPAC_SORT"]=$r->opac_sort;
						static::$st_fields[$detail['type'].'_'.$id][$r->idchamp]["COMMENT"]=$r->comment;
					}
				}	
			}
		}
		
		
// 		if(static::$st_fields[$detail['type'].'_'.$id] == false){
// 			$this->no_special_fields=1;
// 		}else{
		
// 		}
		$this->t_fields = static::$st_fields;
	}
	
	public function get_cms_types() {
		if (!isset($this->cms_types)) {
			$this->init_cms_types();
		}
		return $this->cms_types;
	}
	
	protected function init_cms_types() {
		if (!isset($this->cms_types)) {
			$this->cms_types = array();
			$query = '
					SELECT id_editorial_type, editorial_type_element, editorial_type_label, editorial_type_comment 
					FROM cms_editorial_types
					ORDER BY editorial_type_element, editorial_type_label';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) { 
					if ($row['editorial_type_element'] == 'article' || $row['editorial_type_element'] == 'section') {
						$this->cms_types[$row['id_editorial_type']] = array(
								'id' => $row['id_editorial_type'],
								'type' => str_replace('_generic', '', $row['editorial_type_element']), //cela permet d'ajouter les champ perso generiques 
								'label' => $row['editorial_type_label'],
								'comment' => $row['editorial_type_comment'],
						);
					}
				}
			}
		}
		return $this->cms_types;
	}
	
	public function build_onto () {
		$onto = '';
		
		$onto.= $this->build_onto_cms_class();
		
		$onto .= "
		<!-- Champs perso ".$this->prefix." PMB -->";
		//On boucle sur les champs perso d'un sous type de contenu éditorial
		if (is_array($this->t_fields)) {
    		foreach ($this->t_fields as $cms_type => $t_fields) {
    			if(is_array($t_fields)){
    				foreach($t_fields as $field_id => $t_field){
    					
    					$this->init_attributes();
    					$this->set_uri_description($t_field["NAME"]);
    					$this->set_datatype_from_field($field_id, $t_field);
    					$this->set_restrictions($t_field);
    								
    					$onto.= "
    						<rdf:Description rdf:about='http://www.pmbservices.fr/ontology#" . $this->uri_description. "'>
    							<rdfs:label>" . htmlspecialchars(encoding_normalize::utf8_normalize($t_field["TITRE"]), ENT_QUOTES, 'utf-8') . "</rdfs:label>
    							<rdfs:comment>" . htmlspecialchars(encoding_normalize::utf8_normalize($t_field["COMMENT"]), ENT_QUOTES, 'utf-8') . "</rdfs:comment>
    							<rdfs:isDefinedBy rdf:resource='http://www.pmbservices.fr/ontology#'/>
    					       	<rdf:type rdf:resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'/>
    							<rdfs:domain rdf:resource='http://www.pmbservices.fr/ontology#" .$cms_type. "'/>
    							<rdfs:range rdf:resource='" . $this->uri_range . "'/>
    							<pmb:datatype rdf:resource='" . $this->uri_datatype . "'/>";
    					$onto.= $this->optional_properties;
    			
    					$onto.= "
    							<pmb:name>" . $this->uri_description . "</pmb:name>
    					    </rdf:Description>
    					";
    					// On n'oublie pas les noeuds blancs
    					$onto.= $this->blank_nodes;
    				}
    			}
    		}
		}
		return $onto;
	}
	
	protected function build_onto_cms_class() {
		$onto = '';
		if (!empty($this->cms_types)) {
			foreach ($this->cms_types as $id => $detail) {
					$onto.= '
				<!-- Classe cms perso '.$detail['label'].' PMB -->
				<rdf:Description rdf:about="http://www.pmbservices.fr/ontology#'.$detail['type'].'_'.$id.'">
			        <rdfs:label xml:lang="fr">'.htmlspecialchars(encoding_normalize::utf8_normalize($detail['label']), ENT_QUOTES, 'utf-8').'</rdfs:label>
			        <rdfs:comment>'.htmlspecialchars(encoding_normalize::utf8_normalize($detail['comment']), ENT_QUOTES, 'utf-8').'</rdfs:comment>
			        <rdfs:isDefinedBy rdf:resource="http://www.pmbservices.fr/ontology#" />
			        <rdf:type rdf:resource="http://www.w3.org/2002/07/owl#Class"/>
			        <rdfs:subClassOf rdf:resource="http://www.pmbservices.fr/ontology#cms_'.$detail['type'].'"/>
			        <pmb:displayLabel rdf:resource="http://www.pmbservices.fr/ontology#label"/>
        			<pmb:flag>pmb_entity</pmb:flag>
			        <pmb:name>'.$detail['type'].'_'.$id.'</pmb:name>
		    	</rdf:Description>';
			}
		}
		return $onto;		
	}
}