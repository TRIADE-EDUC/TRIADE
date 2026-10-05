<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_item.class.php,v 1.68.4.1 2025/03/17 08:15:42 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/notice.class.php");
require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/docwatch/docwatch_watch.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/cms/cms_concept.class.php");

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

	protected $detail;

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
		global $lang;

		if(!isset($this->descriptors)) {
			$this->descriptors = array();
			$query = "select docwatch_items_descriptors.num_noeud, categories.libelle_categorie from docwatch_items_descriptors join categories on docwatch_items_descriptors.num_noeud = categories.num_noeud where langue='".$lang."' and docwatch_items_descriptors.num_item ='".$this->id."'";
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
					$concept = new concept($row->num_concept);
					$label = $concept->get_display_label();
					$this->concepts[] = array(
							"id" => $concept->get_uri(),
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
		global $class_path,$gestion_acces_active,$gestion_acces_user_notice,$gestion_acces_empr_notice;
		global $pmb_notice_controle_doublons;

		if(docwatch_watch::check_watch_rights($this->num_watch)){
			/** Récupération des paramètres défini dans la veille pour la création de notice à partir de ses items **/
			$query = "select watch_record_default_type, watch_record_default_status, watch_record_default_index_lang, watch_record_default_lang, watch_record_default_is_new from docwatch_watches where id_watch =".$this->num_watch;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$record_type = $row->watch_record_default_type;
				$record_status = $row->watch_record_default_status;
				$index_lang = $row->watch_record_default_index_lang;
				$create_lang = $row->watch_record_default_lang;
				$is_new = $row->watch_record_default_is_new;
			}else{
				return array();
			}
			//Editeur = Source
			$data = array('name' => addslashes($this->source['title']));
			$editeur_id = editeur::check_if_exists($data);
			if(!$editeur_id) {
				$editeur = new editeur();
				$editeur->update($data);
				$editeur_id = $editeur->id;
			}
			// Mots clés
			$tab_tag= array();
			$this->get_tags();
			foreach ($this->tags as $tag){
				$tab_tag[]=$tag["label"];
			}
			$fields="
				tit1='".addslashes($this->title)."',
				ed1_id='".addslashes($editeur_id)."',
				n_contenu='".addslashes($this->content)."',
				n_resume='".addslashes($this->summary)."',
				lien='".addslashes($this->url)."',
				thumbnail_url='".addslashes($this->logo_url)."',
				date_parution='".addslashes($this->publication_date)."',
				year='".addslashes(format_date($this->publication_date))."',
				typdoc='".addslashes($record_type)."',
				statut='".addslashes($record_status)."',
				index_l='".addslashes(implode($pmb_keyword_sep,$tab_tag))."',
				indexation_lang='".addslashes($index_lang)."',
				notice_is_new='".addslashes($is_new)."',";
			if ($is_new) {
				$fields .= "notice_date_is_new=sysdate(),";
			}
			$fields .= "
				create_date=sysdate(),
				update_date=sysdate()
			";
			$req="INSERT INTO notices SET $fields ";

			pmb_mysql_query($req);
			$num_notice=pmb_mysql_insert_id();
			if(!$num_notice) return array();

			$this->get_descriptors();
			foreach ($this->descriptors as $categ){
				$query = "insert into notices_categories set notcateg_notice = '".$num_notice."', num_noeud='".$categ["id"]."'";
				pmb_mysql_query($query);
			}

			$this->get_concepts();
			if(count($this->concepts)) {
			    $index_concept = new index_concept($num_notice, TYPE_NOTICE);
			    foreach ($this->concepts as $concept){
			        $index_concept->add_concept(new concept($concept['id']));
			    }
			    $index_concept->save(false);
			}

			if ($create_lang){
				$query = "insert into notices_langues set num_notice=".$num_notice.", code_langue='".addslashes($create_lang)."';";
				pmb_mysql_query($query);
			}

			$query = "update docwatch_items set	item_num_notice = '".$num_notice."' where id_item = '".$this->id."'";
			pmb_mysql_query($query);

			$this->set_num_notice($num_notice);

			// Mise à jour de tous les index de la notice
			notice::majNoticesTotal($num_notice);

			//Calcul de la signature
			$is_doublon = 0;

			if ($pmb_notice_controle_doublons != 0) {
				$sign= new notice_doublon();
				$val= $sign->gen_signature($num_notice);
				pmb_mysql_query("update notices set signature='$val' where notice_id=".$num_notice);
				$result=pmb_mysql_query("select notice_id from notices where signature='$val' and notice_id != '$num_notice' ");
				if (pmb_mysql_num_rows($result)) {
					$is_doublon = 1;
				}
			}

			//droits d'acces
			if ($gestion_acces_active==1) {
				require_once("$class_path/acces.class.php");
				$ac= new acces();

				if ($gestion_acces_user_notice==1) {
					$dom_1 = $ac->setDomain(1);
					$dom_1->applyRessourceRights($num_notice);
				}
				//pour l'opac
				if ($gestion_acces_empr_notice==1) {
					$dom_2 = $ac->setDomain(2);
					$dom_2->applyRessourceRights($num_notice);
				}
			}

			return array('id'=>$num_notice, 'title'=> $this->title,'link'=>notice::get_permalink($num_notice),'is_doublon'=>$is_doublon);
		}
	}

	public function create_section($section_num_parent=0) {
		if(docwatch_watch::check_watch_rights($this->num_watch)){
			$query = "select watch_section_default_parent, watch_section_default_content_type,watch_section_default_publication_status from docwatch_watches where id_watch =".$this->num_watch;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$section_type = $row->watch_section_default_content_type;
				$section_status = $row->watch_section_default_publication_status;
				$section_num_parent = $row->watch_section_default_parent;
			}else{
				return array();
			}
			$section = new cms_section();
			$section->id = 0;
			$section->title = $this->title;
			$section->resume = $this->summary;
			$section->start_date = $this->publication_date;
			$section->publication_state = $section_status;
			$descriptors = array();
			$this->get_descriptors();
			if(count($this->descriptors)) {
			    foreach($this->descriptors as $descriptor){
			        $descriptors[] = $descriptor['id'];
			    }
			}
			$section->set_descriptors($descriptors);
			$section->num_parent = $section_num_parent;
			$section->num_type = $section_type;
			if ($this->url) {
				$section->resume.= "<br /><a href='".$this->url."'>".$this->url."</a>";
			}
			$section->save();
			if(!$section->id) return array();

			$query = "update docwatch_items set	item_num_section = '".$section->id."' where id_item = '".$this->id."'";
			pmb_mysql_query($query);

			$this->set_num_section($section->id);

			if ($this->logo_url) {
				if ($logo_url_content = $this->get_logo_content_from_outside($this->logo_url)) {
					$section->logo->id = $section->id;
					if($section->logo->save_from_content($logo_url_content)){
						$section->save();
					}
				}
			}

			return array('id'=>$section->id, 'title'=> $this->title,'link'=>"./cms.php?categ=section&sub=edit&id=".$section->id);
		}
	}

	public function create_article($section_num_parent=0) {
		if(docwatch_watch::check_watch_rights($this->num_watch)){
			$query = "select watch_article_default_parent, watch_article_default_content_type,watch_article_default_publication_status from docwatch_watches where id_watch =".$this->num_watch;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$article_type = $row->watch_article_default_content_type;
				$article_status = $row->watch_article_default_publication_status;
				$article_parent = $row->watch_article_default_parent;
			}else{
				return array();
			}

			$article = new cms_article();
			$article->id = 0;
			$article->num_type = $article_type;
			$article->num_parent = $article_parent;
			$article->title = $this->title;
			$article->resume = $this->summary;
			$article->contenu = $this->content;
			$article->start_date = $this->publication_date;
			$article->publication_state = $article_status;
			$descriptors = array();
			$this->get_descriptors();
			if(count($this->descriptors)) {
			    foreach($this->descriptors as $descriptor){
			        $descriptors[] = $descriptor['id'];
			    }
			}
			$article->set_descriptors($descriptors);
			if ($this->url) {
				$article->resume.= "<br /><a href='".$this->url."'>".$this->url."</a>";
			}
			$article->save();
			if(!$article->id) return array();

			$query = "update docwatch_items set	item_num_article = '".$article->id."' where id_item = '".$this->id."'";
			pmb_mysql_query($query);

			$this->set_num_article($article->id);

			if ($this->logo_url) {
				if ($logo_url_content = $this->get_logo_content_from_outside($this->logo_url)) {
					$article->logo->id = $article->id;
					if($article->logo->save_from_content($logo_url_content)){
						$article->save();
					}
				}
			}

			return array('id'=>$article->id, 'title'=> $this->title,'link'=>"./cms.php?categ=article&sub=edit&id=".$article->id);
		}
	}

	public function get_logo_content_from_outside($url_image){
		global $pmb_vignette_x ;
		global $pmb_vignette_y ;
		global $base_path;
		global $pmb_curl_available;

		if (!$pmb_vignette_x) $pmb_vignette_x=100 ;
		if (!$pmb_vignette_y) $pmb_vignette_y=100 ;

		//Il s'agit d'une url, on copie le fichier en local
		$nom_temp = session_id().microtime();
		$nom_temp = str_replace(' ','_',$nom_temp);
		$nom_temp = str_replace('.','_',$nom_temp);
		$fichier_tmp = $base_path."/temp/".$nom_temp;
		if ($pmb_curl_available) {
			$aCurl = new Curl();
			$aCurl->save_file_name=$fichier_tmp;
			$aCurl->get($url_image);
		} else {
			$handle = fopen($url_image, "rb");
			$filecontent = stream_get_contents($handle);
			fclose($handle);
			$fd = fopen($fichier_tmp,"w");
			fwrite($fd,$filecontent);
			fclose($fd);
		}
		$source_file = realpath($fichier_tmp);

		$error = true;
		if(extension_loaded('imagick')) {
			mysql_set_wait_timeout(3600);
			$error=false;
			try {
				$img = new Imagick();
				$img->readImage($source_file);
				if(($img->getImageWidth() > $pmb_vignette_x) || ($img->getImageHeight() > $pmb_vignette_y)){// Si l'image est trop grande on la réduit
					$img->thumbnailimage($pmb_vignette_x,$pmb_vignette_y,true);
				}
				$img->setImageFormat( "png" );
				$img->setCompression(Imagick::COMPRESSION_LZW);
				$img->setCompressionQuality(90);
				$contenu_vignette = $img->getImageBlob();
			} catch(Exception $ex) {
				$error=true;
			}
			if($fichier_tmp && file_exists($fichier_tmp)){
				unlink($fichier_tmp);
			}
		}
		if ($error) {
			$size =@getimagesize($url_image);
			/*   ".gif"=>"1",
			 ".jpg"=>"2",
			".jpeg"=>"2",
			".png"=>"3",
			".swf"=>"4",
			".psd"=>"5",
			".bmp"=>"6");
			*/
			switch ($size[2]) {
				case 1:
					$src_img = imagecreatefromgif($url_image);
					break;
				case 2:
					$src_img = imagecreatefromjpeg($url_image);
					break;
				case 3:
					$src_img = imagecreatefrompng($url_image);
					break;
				case 6:
					$src_img = imagecreatefromwbmp($url_image);
					break;
				default:
					break;
			}
			$erreur_vignette = 0 ;
			if ($src_img) {
				$rs=$pmb_vignette_x/$pmb_vignette_y;
				$taillex=imagesx($src_img);
				$tailley=imagesy($src_img);
				if (!$taillex || !$tailley) return "" ;
				if (($taillex>$pmb_vignette_x)||($tailley>$pmb_vignette_y)) {
					$r=$taillex/$tailley;
					if (($r<1)&&($rs<1)) {
						//Si x plus petit que y et taille finale portrait
						//Si le format final est plus large en proportion
						if ($rs>$r) {
							$new_h=$pmb_vignette_y;
							$new_w=$new_h*$r;
						} else {
							$new_w=$pmb_vignette_x;
							$new_h=$new_w/$r;
						}
					} else if (($r<1)&&($rs>=1)){
						//Si x plus petit que y et taille finale paysage
						$new_h=$pmb_vignette_y;
						$new_w=$new_h*$r;
					} else if (($r>1)&&($rs<1)) {
						//Si x plus grand que y et taille finale portrait
						$new_w=$pmb_vignette_x;
						$new_h=$new_w/$r;
					} else {
						//Si x plus grand que y et taille finale paysage
						if ($rs<$r) {
							$new_w=$pmb_vignette_x;
							$new_h=$new_w/$r;
						} else {
							$new_h=$pmb_vignette_y;
							$new_w=$new_h*$r;
						}
					}
				} else {
					$new_h = $tailley ;
					$new_w = $taillex ;
				}
				$dst_img=imagecreatetruecolor($pmb_vignette_x,$pmb_vignette_y);
				ImageSaveAlpha($dst_img, true);
				ImageAlphaBlending($dst_img, false);
				imagefilledrectangle($dst_img,0,0,$pmb_vignette_x,$pmb_vignette_y,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
				imagecopyresized($dst_img,$src_img,round(($pmb_vignette_x-$new_w)/2),round(($pmb_vignette_y-$new_h)/2),0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
				imagepng($dst_img, $base_path."/temp/".SESSid);
				$fp = fopen($base_path."/temp/".SESSid , "r" ) ;
				$contenu_vignette = fread ($fp, filesize($base_path."/temp/".SESSid));
				if (!$fp || $contenu_vignette=="") $erreur_vignette++ ;
				fclose ($fp) ;
				unlink($base_path."/temp/".SESSid);
			} else {
				$contenu_vignette = '' ;
			}
		}
		return $contenu_vignette ;
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
		$this->publication_date = "";
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
				if($this->publication_date =="0000-00-00 00:00:00") $this->publication_date="";
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
	 * @return void
	 * @access public
	 */
	public function gen_hash() {
		$this->hash = md5($this->num_watch."_".$this->source_id."_".$this->url);
	}

	/**
	 * Fonction de sauvegarde d'un item
	 * @return boolean
	 */
	public function save(){
		if(docwatch_watch::check_watch_rights($this->num_watch)){
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
					item_num_watch = '".$this->num_watch."',
					item_index_sew = ' ".addslashes($this->get_index_sew())." ',
					item_index_wew = '".addslashes($this->get_index_wew())."'";
			$query.=$clause;
			if(!pmb_mysql_query($query)){
				return false;
			}else{
				if(!$this->id) $this->id = pmb_mysql_insert_id();
				$query = "delete from docwatch_items_descriptors where num_item = '".$this->id."' ";
				if(!pmb_mysql_query($query)){
				    return false;
				}
				if(is_array($this->descriptors) && count($this->descriptors)) {
				    foreach($this->descriptors as $descriptor_info){
				        $query = "insert into docwatch_items_descriptors set num_noeud='".$descriptor_info['id']."', num_item = '".$this->id."' ";
				        pmb_mysql_query($query);
				    }
				}
				$this->delete_item_in_entrepot();
				return true;
			}
		}
		return false;
	}

	public function delete(){
		if(docwatch_watch::check_watch_rights($this->num_watch)){
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
	}

	public function index($data){
		global $charset;
		if(docwatch_watch::check_watch_rights($this->num_watch)){
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

		    $concepts_list = $data['concepts_list'];
		    $item_id = $data['item_id'];
		    $index_concept = new index_concept($item_id, TYPE_DOCWATCH);
			if(count($data["concepts_list"])) {
			    foreach ($concepts_list as $uri){
			        $query = "SELECT uri_id FROM onto_uri WHERE uri = '$uri'";
			        $result = pmb_mysql_query($query);
			        if (pmb_mysql_num_rows($result)) {
			            while ($row = pmb_mysql_fetch_assoc($result,0,0)) {
			                $id_concept = $row['uri_id'];
			                $concept = new concept($id_concept);
			                $index_concept->add_concept($concept);
			            }
			        }
			    }
			}
		    $index_concept->save(false);

			$this->fetch_datas();
			return true;
		}
	}

	public function get_index_wew() {
	    return ' '.strip_tags($this->title).' '.strip_tags($this->summary).' '.strip_tags($this->content).' ';
	}

	public function get_index_sew() {
		return strip_empty_words($this->get_index_wew());
	}

	public function mark_as_deleted(){
		if(docwatch_watch::check_watch_rights($this->num_watch)){
		    $this->delete_item_in_entrepot();
			$query = "update docwatch_items set item_status = '2' where id_item = '".$this->id."' ";
			if(pmb_mysql_query($query)){
				return true;
			}else{
				return false;
			}
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

		$publication_date="";
		$formated_publication_date="";
		if($this->publication_date)	$publication_date=formatdate($this->publication_date,1);
		if($this->publication_date)$formated_publication_date=date("c",strtotime($this->publication_date));

		$retour = array("id"=>$this->id,
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
					"datasource_title"=>$this->source["title"] ?? "",
					"num_watch"=>$this->num_watch,
					"watch"=>$this->watch,
					"publication_date"=>$publication_date,
        		    "formated_publication_date"=>$formated_publication_date,
        		    "raw_publication_date"=>$this->publication_date,
					"interesting"=>$this->interesting,
					"descriptors_isbd"=>$this->get_descriptors_isbd(),
					"tags_isbd"=>$this->get_tags_isbd(),
					"descriptors"=>$this->get_descriptors(),
					"tags"=>$this->get_tags(),
		            "concepts_isbd" => $this->get_concepts_isbd() ?? "",
		            "detail" => $this->get_detail() ?? ""
				);

		if($this->num_notice){
			$retour['record_link'] = notice::get_permalink($this->num_notice);
		}
		if($this->num_article){
			$retour['article_link'] = "./cms.php?categ=article&sub=edit&id=".$this->num_article;
		}
		if($this->num_section){
			$retour['section_link'] = "./cms.php?categ=section&sub=edit&id=".$this->num_section;
		}
		$this->get_concepts();
		if(!empty($this->concepts)){
		    $retour['concepts'] = $this->concepts;
		}
		return $retour;
	}
	public static function get_format_data_structure(){
		global $msg;
		return array(
				array(
						'var' => "id",
						'desc'=> $msg['cms_module_item_datasource_desc_id']
				),
				array(
						'var' => "title",
						'desc' => $msg['cms_module_item_datasource_desc_title']
				),
				array(
						'var' => "summary",
						'desc' => $msg['cms_module_item_datasource_desc_summary']
				),
				array(
						'var' => "content",
						'desc' => $msg['cms_module_item_datasource_desc_content']
				),
				array(
						'var' => "added_date",
						'desc' => $msg['cms_module_item_datasource_desc_added_date']
				),
				array(
						'var' => "publication_date",
						'desc' => $msg['cms_module_item_datasource_desc_publication_date']
				),
				array(
						'var' => "raw_publication_date",
						'desc' => $msg['cms_module_item_datasource_desc_publication_date']
				),
				array(
						'var' => "url",
						'desc' => $msg['cms_module_item_datasource_desc_url']
				),
				array(
						'var' => "logo_url",
						'desc' => $msg['cms_module_item_datasource_desc_logo_url']
				),
				array(
						'var' => "status",
						'desc' => $msg['cms_module_item_datasource_desc_status']
				),
				array(
						'var' => "interesting",
						'desc' => $msg['cms_module_item_datasource_desc_interesting']
				),
				array(
						'var' => "descriptors",
						'desc' => $msg['cms_module_item_datasource_desc_descriptors'],
						'children' => array(
								array(
										'var' => "descriptors[i].id",
										'desc' => $msg['cms_module_item_datasource_desc_descriptor_id']
								),
								array(
										'var' => "descriptors[i].label",
										'desc' => $msg['cms_module_item_datasource_desc_descriptor_label']
								),
								array(
										'var' => "descriptors[i].comment",
										'desc' => $msg['cms_module_item_datasource_desc_descriptor_comment']
								),
								array(
										'var' => "descriptors[i].lang",
										'desc' => $msg['cms_module_item_datasource_desc_descriptor_lang']
								),
						)
				),
    		    array(
        		        'var' => "concepts",
        		        'desc' => $msg['cms_module_item_datasource_desc_concepts'],
        		        'children' => docwatch_root::prefix_var_tree(cms_concept::get_format_data_structure(), "concepts[i]")
    		    ),
				array(
						'var' => "tags",
						'desc' => $msg['cms_module_item_datasource_desc_tags'],
						'children' => array(
								array(
										'var' => "tags[i].id",
										'desc' => $msg['cms_module_item_datasource_desc_tag_id']
								),
								array(
										'var' => "tags[i].label",
										'desc' => $msg['cms_module_item_datasource_desc_tag_label']
								)
						)
				),
				array(
						'var' => "watch",
						'desc' => $msg['cms_module_item_datasource_desc_watch'],
						'children' => array(
								array(
										'var' => "watch.id",
										'desc' => $msg['cms_module_item_datasource_desc_watch_id']
								),
								array(
										'var' => "watch.title",
										'desc' => $msg['cms_module_item_datasource_desc_watch_title']
								),
								array(
										'var' => "watch.last_date",
										'desc' => $msg['cms_module_item_datasource_desc_watch_last_date']
								),
								array(
										'var' => "watch.desc",
										'desc' => $msg['cms_module_item_datasource_desc_watch_desc']
								),
								array(
										'var' => "watch.logo_url",
										'desc' => $msg['cms_module_item_datasource_desc_watch_logo_url']
								)
						)
				),
				array(
						'var' => "source.title",
						'desc' => $msg['cms_module_item_datasource_desc_source_title']
				)
		);
	}

	public static function get_available_datatags(){
		$tags=array();
		$query = "select id_tag, tag_title from docwatch_tags order by tag_title";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row=pmb_mysql_fetch_object($result)){
				$tags[]=array(
					'id' => $row->id_tag,
					'label'=>$row->tag_title
				);
			}
		}
		return $tags;
	}

	public function delete_item_in_entrepot(){
	    // On va recupere les entrepots de veille
	    $query = "SELECT source_id FROM connectors_sources WHERE id_connector = 'veille'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_assoc($result)){
	            $query_select = "SELECT * FROM entrepot_source_" . $row['source_id'] . " WHERE ref = '" . $this->id. "'";
	            $result_select = pmb_mysql_query($query_select);
	            if (pmb_mysql_num_rows($result_select)) {
	                //On supprime les entrées de l'entrepot pour le record
	                $q =  "DELETE FROM entrepot_source_".$row['source_id'] ." WHERE ref ='" . $this->id . "'";
	                pmb_mysql_query($q);
	                //On supprime de external_count
	                $recid = addslashes("veille " . $row['source_id'] . " " . $this->id);
	                $q="DELETE FROM external_count WHERE recid='" . $recid . "' AND source_id = " . $row['source_id'];
	                pmb_mysql_query($q);
        	    }
	        }
	    }
	}

	public function get_detail()
	{
		global $include_path;

		if (isset($this->detail)) {
			return $this->detail;
		}

		$template = "{$include_path}/templates/docwatch/docwatch_detail_subst.tpl.html";
		if (!is_file($template)) {
			$template = "{$include_path}/templates/docwatch/docwatch_detail.tpl.html";
		}

		$this->detail = "";
		if (is_file($template)) {
			$h2o = \H2o_collection::get_instance($template);
			$this->detail = $h2o->render([
				"itemwatch" => $this
			]);
		}
		return $this->detail;
	}
} // end of docwatch_item