<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_item.class.php,v 1.14 2023/08/28 14:04:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class docwatch_item
 * 
 */
class docwatch_item{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Titre de l'item
	 * @access public
	 */
	protected $title;

	/**
	 * Date de publication
	 * @access public
	 */
	protected $publication_date;

	/**
	 * Date de collecte
	 * @access public
	 */
	protected $added_date;

	/**
	 * Identifiant de la source
	 * @access public
	 */
	protected $source_id;

	/**
	 * Hash de l'item pour éviter les doublons
	 * @access public
	 */
	protected $hash;

	/**
	 * Résumé
	 * @access public
	 */
	protected $summary;

	/**
	 * Détail du contenu de l'item
	 * @access public
	 */
	protected $content;

	/**
	 * URL de la ressource liée à  l'item
	 * @access public
	 */
	protected $url;

	/**
	 * URL du logo (facultatif)
	 * @access public
	 */
	protected $logo_url;

	/**
	 * Tableau des descripteurs de thésaurus
	 * @access public
	 */
	protected $descriptors;

	/**
	 * Concepts skos
	 * @access public
	 */
	protected $concepts;

	/**
	 * Tableau des tags libres
	 * @access public
	 */
	protected $tags;

	/**
	 * Non lu /  lu
	 * @access public
	 */
	protected $status;

	/**
	 * Interessant (0=non interessant)
	 * @access public
	 */
	protected $interesting;

	/**
	 * Type de l'item (item de flux RSS, notice PMB, Contenu éditorial article, etc.)
	 * @access public
	 */
	protected $type;

	/**
	 * Identifiant de la notice cataloguée (si il y a)
	 * @access public
	 */
	protected $num_notice;

	/**
	 * Id de l'article
	 * @access public
	 */
	protected $num_article;

	/**
	 * Id de la rubrique
	 * @access public
	 */
	protected $num_section;

	/**
	 * Id de la veille
	 * @access public
	 */
	protected $num_watch;
	
	/**
	 * Identifiant de l'item dans la base
	 * @access protected
	 */
	protected $id;
	
	/**
	 * Tableau d'information sur la veille
	 * @access public
	 */
	protected $watch;
	
	/**
	 * Tableau d'information sur la source
	 * @access public
	 */
	protected $source;
	
	/**
	 * Format ISBD des catégories
	 */
	protected $descriptors_isbd;
	
	/**
	 * Format ISBD des concepts
	 */
	protected $concepts_isbd;
	
	/**
	 * Format ISBD des tags
	 */
	protected $tags_isbd;
	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct

	
	/*
	 * Getters & setters
	 */
	 
	public function get_id(){
		return $this->id;
	}
	
	public function set_id($id){
		$this->id = $id;
	}
	
	public function get_title(){
		return $this->title;
	}
	
	public function set_title($title){
		$this->title = $title;
	}
	 
	public function get_added_date(){
		return $this->added_date;
	}
	
	public function set_added_date($added_date){
		$this->added_date = $added_date;
	}
	 
	public function get_publication_date(){
		return $this->publication_date;
	}
	
	public function set_publication_date($publication_date){
		$this->publication_date = $publication_date;
	}
	
	public function get_source_id(){
		return $this->source_id;
	}
	
	public function set_source_id($source_id){
		$this->source_id = $source_id;
	}
	
	public function get_source() {
		return $this->source;
	}
	
	public function get_watch() {
		return $this->watch;
	}
	
	public function get_hash(){
		return $this->hash;
	}
	
	public function set_hash($hash){
		//$this->hash = $this->gen_hash(); ?
	}
	
	public function get_summary(){
		return $this->summary;
	}
	
	public function set_summary($summary){
		$this->summary = $summary;
	}
	
	public function get_content(){
		return $this->content;
	}
	
	public function set_content($content){
		$this->content = $content;
	}
	
	public function get_url(){
		return $this->url;
	}
	
	public function set_url($url){
		$this->url = $url;
	}
	
	public function get_logo_url(){
		return $this->logo_url;
	}
	
	public function set_logo_url($logo_url){
		$this->logo_url = $logo_url;
	}
	
	public function get_descriptors(){
		if(!isset($this->descriptors)) {
			$this->descriptors = array();
			$query = "select docwatch_items_descriptors.num_noeud, categories.libelle_categorie from docwatch_items_descriptors join categories on docwatch_items_descriptors.num_noeud = categories.num_noeud where docwatch_items_descriptors.num_item ='".$this->id."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row=pmb_mysql_fetch_object($result)){
					$this->descriptors[] = array(
							"id" => $row->num_noeud,
							"label" => $row->libelle_categorie
					);
				}
			}
		}
		return $this->descriptors;
	}
	
	public function set_descriptors($descriptors){
		$this->descriptors = $descriptors;
	}
	
	public function get_concepts(){
		if(!isset($this->concepts)) {
			$this->concepts = array();
			$query = "select num_concept from index_concept where index_concept.num_object ='".$this->id."' and type_object = '".TYPE_DOCWATCH."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row=pmb_mysql_fetch_object($result)){
					$label = index_concept::get_concept_label_from_id($row->num_concept);
					$this->concepts[] = array(
							"id" => $row->num_concept,
							"label" => $label
					);
				}
			}
		}
		return $this->concepts;
	}
	
	public function set_concepts($concepts){
		$this->concepts = $concepts;
	}
	
	public function get_tags(){
		if(!isset($this->tags)) {
			$this->tags = array();
			$query = "select docwatch_items_tags.num_tag, docwatch_tags.tag_title from docwatch_items_tags join docwatch_tags on docwatch_items_tags.num_tag = docwatch_tags.id_tag where docwatch_items_tags.num_item = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row=pmb_mysql_fetch_object($result)){
					$this->tags[] = array(
							"id" => $row->num_tag,
							"label" => $row->tag_title
					);
				}
			}
		}
		return $this->tags;
	}
	
	public function set_tags($tags){
		$this->tags = $tags;
	}
	
	public function get_status(){
		return $this->status;
	}
	
	public function set_status($status){
		$this->status = $status;
	}
	 
	public function get_interesting(){
		return $this->interesting;
	}
	
	public function set_interesting($interesting){
		$this->interesting = $interesting;
	}
	
	public function get_type(){
		return $this->type;
	}
	
	public function set_type($type){
		$this->type = $type;
	}
	
	public function get_num_article(){
		return $this->num_article;
	}
	
	public function set_num_article($num_article){
		$this->num_article = $num_article;
	}
	 
	public function get_num_notice(){
		return $this->num_notice;
	}
	
	public function set_num_notice($num_notice){
		$this->num_notice = $num_notice;
	}
	
	public function get_num_section(){
		return $this->num_section;
	}
	
	public function set_num_section($num_section){
		$this->num_section = $num_section;
	}
	
	    
	public function get_num_watch() {
	  return $this->num_watch;
	}
	
	public function set_num_watch($num_watch) {
	  $this->num_watch = $num_watch;
	}
	
	public function get_descriptors_isbd() {
		if(!isset($this->descriptors_isbd)) {
			$this->get_descriptors();
			$this->descriptors_isbd = '';
			foreach ($this->descriptors as $descriptor) {
				if($this->descriptors_isbd)$this->descriptors_isbd.="; ";
				$this->descriptors_isbd.= $descriptor['label'];
			}
		}
		return $this->descriptors_isbd;
	}
	
	public function get_concepts_isbd() {
		if(!isset($this->concepts_isbd)) {
			$this->get_concepts();
			$this->concepts_isbd = '';
			foreach ($this->concepts as $concept) {
				if($this->concepts_isbd)$this->concepts_isbd.="; ";
				$this->concepts_isbd.= $concept['label'];
			}
		}
	    return $this->concepts_isbd;
	}
	
	public function get_tags_isbd() {
		if(!isset($this->tags_isbd)) {
			$this->get_tags();
			$this->tags_isbd = '';
			foreach ($this->tags as $tag) {
				if($this->tags_isbd)$this->tags_isbd.="; ";
				$this->tags_isbd.= $tag['label'];
			}
		}
		return $this->tags_isbd;
	}
	
	public function create_notice() {		
		global $pmb_keyword_sep;
		
		$fields="
			tit1='".addslashes($this->title)."',
			n_resume='".addslashes($this->summary)."',
			lien='".addslashes($this->url)."',
			thumbnail_url='".addslashes($this->logo_url)."',
			date_parution='".addslashes($this->publication_date)."',
			index_l='".addslashes(implode($pmb_keyword_sep,$this->get_tags()))."'
		";
		$req="INSERT INTO notices SET $fields ";
		
		
		pmb_mysql_query($req);
		$num_notice=pmb_mysql_insert_id();
		if(!$num_notice) return array();
		
		$query.= "update docwatch_items set	item_num_notice = '".$num_notice."' where id_item = '".$this->id."'";
		pmb_mysql_query($query);
		
		// Mise à jour de tous les index de la notice
		notice::majNoticesTotal($num_notice);
		return array('id'=>$num_notice, 'title'=> $this->title,'link'=>notice::get_permalink($num_notice));
	}

	public function create_section($section_num_parent=0) {
		$section = new cms_section();
		$section->id = 0;
		$section->num_type = 0; // ?
		$section->num_parent = 0; // ?
		$section->title = $this->title;
		$section->resume = $this->summary;
		$section->start_date = $this->publication_date;
		$section->publication_state = 0; // ?
		$descriptors = array();
		$this->get_descriptors();
		if(count($this->descriptors)) {
		    foreach($this->descriptors as $descriptor){
		        $descriptors[] = $descriptor['id'];
		    }
		}
		$section->set_descriptors($descriptors);
		//$section->logo->id ?
		
		$section->save();	
		if(!$section->id) return array();
	
		$query.= "update docwatch_items set	item_num_section = '".$section->id."' where id_item = '".$this->id."'";
		pmb_mysql_query($query);
		
		return array('id'=>$section->id, 'title'=> $this->title,'link'=>"");
	}
	
	public function create_article($section_num_parent=0) {
		$article = new cms_article();
		$article->id = 0;
		$article->num_type = 0; // ?
		$article->num_parent = 0; // ?
		$article->title = $this->title;
		$article->resume = $this->summary;
		$article->contenu = $this->content;
		$article->start_date = $this->publication_date;
		$article->publication_state = 0; // ?
		$descriptors = array();
		$this->get_descriptors();
		if(count($this->descriptors)) {
		    foreach($this->descriptors as $descriptor){
		        $descriptors[] = $descriptor['id'];
		    }
		}
		$article->set_descriptors($descriptors);
		//$article->logo->id ?
		
		$article->save();	
		if(!$article->id) return array();
		
		$query.= "update docwatch_items set	item_num_article = '".$article->id."' where id_item = '".$this->id."'";
		pmb_mysql_query($query);
		
		return array('id'=>$article->id, 'title'=> $this->title,'link'=>"");		
	}
	
	/**
	 * Méthode permettant d'initialiser un item
	 * 
	 * @return void
	 * @access public
	 */
	public function fetch_datas(){
		global $msg;
		
		$this->title = "";
		$this->added_date = "0000-00-00 00:00:00";
		$this->publication_date = "0000-00-00 00:00:00";
		$this->source_id = 0;
		$this->hash = "";
		$this->summary = "";
		$this->content = "";
		$this->url = "";
		$this->logo_url = "";
		$this->status = 0;
		$this->interesting = 0;
		$this->type = "";
		$this->num_notice = 0;
		$this->num_article = 0;
		$this->num_section = 0;
		$this->num_watch = 0;
		if($this->id){
			$query = "select * from docwatch_items where id_item = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->item_title;
				$this->added_date = $row->item_added_date;
				$this->publication_date = $row->item_publication_date;
				$this->source_id = $row->item_num_datasource;
				$this->hash = $row->item_hash;
				$this->summary = $row->item_summary;
				$this->content = $row->item_content;
				$this->url = $row->item_url;
				
				if($row->item_logo_url) $this->logo_url = $row->item_logo_url;
				else $this->logo_url = "";

				$this->status = $row->item_status;
				$this->interesting = $row->item_interesting;
				$this->type = $row->item_type;
				$this->num_notice = $row->item_num_notice;
				$this->num_article = $row->item_num_article;
				$this->num_section = $row->item_num_section;
				$this->num_watch = $row->item_num_watch;
				$query = "select datasource_title from docwatch_datasources where id_datasource ='".$this->source_id."'";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					if($row=pmb_mysql_fetch_object($result)){
						$this->source = array(
							"title" => $row->datasource_title
						);
					}
				} else {
					$this->source = array(
							"title" => $msg['dsi_docwatch_datasource_deleted']
					);
				}
				$query = "select id_watch,watch_title,watch_last_date, watch_desc, watch_logo_url from docwatch_watches where id_watch ='".$this->num_watch."'";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					if($row=pmb_mysql_fetch_object($result)){
						$this->watch = array(
						    "id" => $row->id_watch,
							"title" => $row->watch_title,
							"last_date" => $row->watch_last_date,
							"desc" => $row->watch_desc,
							"logo_url" => $row->watch_logo_url
						);
					}
				}
			}
		}
	}
	
	/**
	 * Calcul du hash
	 *
	 * @return string
	 * @access public
	 */
	public function gen_hash() {
		$this->hash = md5($this->num_watch."_".$this->source_id."_".$this->url);
	} // end of member function gen_hash
	
	/**
	 * Fonction de sauvegarde d'un item
	 * @return boolean
	 */
	public function save(){
		$query = "";
		$clause = "";
		if(!$this->id){
			$query .= "insert into ";
		}else{
			$query.= "update ";
			$clause.= " where id_item = '".$this->id."'";
		}		
		$query.= "docwatch_items set
				item_type = '".addslashes($this->type)."',
				item_title = '".addslashes($this->title)."',
				item_summary = '".addslashes($this->summary)."',
				item_content = '".addslashes($this->content)."',
				item_added_date = now(),
				item_publication_date = '".$this->publication_date."',
				item_hash = '".addslashes($this->hash)."',
				item_url = '".addslashes($this->url)."',
				item_logo_url = '".addslashes($this->logo_url)."',
				item_status = '".$this->status."',
				item_interesting = '".$this->interesting."',
				item_num_article = '".$this->num_article."',
				item_num_section = '".$this->num_section."',
				item_num_notice = '".$this->num_notice."',
				item_num_datasource = '".$this->source_id."',
				item_num_watch = '".$this->num_watch."'";
		$query.=$clause;
		if(!pmb_mysql_query($query)){
			return false;
		}else{
			if(!$this->id) $this->id = pmb_mysql_insert_id();
			return true;
		}
	}
	
	public function delete(){
		$query = "delete from docwatch_items where id_item = '".$this->id."' ";
		if(pmb_mysql_query($query)){
			$query = "delete from docwatch_items_tags where num_item = '".$this->id."'";
			if(!pmb_mysql_query($query)){
				return false;
			}
			
			$query = "delete from docwatch_tags where id_tag not in (select num_tag from docwatch_items_tags)";
			if(!pmb_mysql_query($query)){
				return false;
			}

			$query = "delete from docwatch_items_descriptors where num_item = '".$this->id."' ";
			if(!pmb_mysql_query($query)){
				return false;
			}
		}else{
			return false;
		}
		return true;
	}
	
	public function index($data){
		global $charset;
		
		$query = "delete from docwatch_items_descriptors where num_item = '".$this->id."' ";
		if(!pmb_mysql_query($query)){
			return false;
		}
		if(count($data["descriptors"])) {
			foreach($data["descriptors"] as $id){
				$query = "insert into docwatch_items_descriptors set num_noeud='".$id."', num_item = '".$this->id."' ";
				pmb_mysql_query($query);
			}		
		}
		$query = "delete from docwatch_items_tags where num_item = '".$this->id."' ";
		if(!pmb_mysql_query($query)){
			return false;
		}
		if(count($data["tags"])) {
			foreach($data["tags"] as $label){
				if($charset != 'utf-8'){
					$label=encoding_normalize::utf8_decode($label);
				}
				$query = "select id_tag from docwatch_tags where tag_title = '".addslashes($label)."'";
				$result = pmb_mysql_query($query);
				if (!pmb_mysql_num_rows($result)) {
					$query = "insert into docwatch_tags set tag_title = '".addslashes($label)."'";
				 	pmb_mysql_query($query);
				 	$num_tag = pmb_mysql_insert_id();
				}else{
					$row=pmb_mysql_fetch_object($result);
					$num_tag=$row->id_tag;
				}
				$query = "insert into docwatch_items_tags set num_tag='".$num_tag."', num_item = '".$this->id."' ";
				pmb_mysql_query($query);
			}	
		}
		$this->fetch_datas();
		return true;
	}
	
	public function mark_as_deleted(){
		$query = "update docwatch_items set item_status = '2' where id_item = '".$this->id."' ";
		if(pmb_mysql_query($query)){
			return true;
		}else{
			return false;
		}
	}
	
	public function mark_as_purged(){
		if(docwatch_watch::check_watch_rights($this->num_watch)){
			$query = "update docwatch_items set item_status = '3' where id_item = '".$this->id."' ";
			if(pmb_mysql_query($query)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function get_normalized_item(){
			
		return array(
			"id"=>$this->id, 
			"type"=>$this->type, 
			"title"=>$this->title, 
			"content"=>$this->content,
			"summary"=>$this->summary,
			"hash"=>$this->hash,
			"url"=>$this->url,
			"logo_url"=>$this->logo_url,
			"status"=>$this->status,
			"num_article"=>$this->num_article,
			"num_section"=>$this->num_section,
			"num_notice"=>$this->num_notice,
			"num_datasource"=>$this->source_id,
			"source"=>$this->source,
			"datasource_title"=>$this->source["title"],
			"num_watch"=>$this->num_watch,
			"watch"=>$this->watch,
			"publication_date"=>formatdate($this->publication_date,1),
			"formated_publication_date"=>date("c",strtotime($this->publication_date)),
		    "raw_publication_date"=>$this->publication_date,
			"interesting"=>$this->interesting,
			"descriptors_isbd"=>$this->get_descriptors_isbd(),
			"tags_isbd"=>$this->get_tags_isbd(),
			"descriptors"=>$this->get_descriptors(),
			"tags"=>$this->get_tags()
		);
	}
	
} // end of docwatch_item

