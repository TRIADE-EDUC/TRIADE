<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_document.class.php,v 1.10.2.2.2.1 2025/02/12 12:34:08 dbellamy Exp $

use Pmb\AI\Orm\AiSharedListDocnumOrm;
use Pmb\Common\Orm\OpacListeLectureOrm;

global $class_path;
require_once($class_path."/cms/cms_document.class.php");

class pmb_document extends base_params implements params {
	public $listeDocs = array();		//tableau de documents
	public $current = 0;				//position courante dans le tableau
	public $currentDoc = array();			//tableau décrivant le document courant
	public $params;					//tableau de paramètres utiles pour la recontructions des requetes...et même voir plus
	public $watermark = array();			//Url du watermark si défini  + transparence
	public $visionneuse_path;

    public function __construct($params,$visionneuse_path) {
    	global $opac_photo_mean_size_x,$opac_photo_mean_size_y;
    	$this->driver_name = "pmb_document";
    	$this->params = $params;
    	$this->params["maxX"] = $opac_photo_mean_size_x;
    	$this->params["maxY"] = $opac_photo_mean_size_y;
    	$this->visionneuse_path = $visionneuse_path;
    	if($this->params["lvl"] == "ajax") {
	    	$this->getDocById($this->params["explnum_id"]);
	    } else if($this->params["lvl"] != "afficheur"){
	    	$this->recupListDocNum();
	    } else{
	    	$this->getDocById($this->params["explnum"]);
	    }
    }

	public function getDocById($id){
		$this->current = 0;
		$this->listeDocs = array($id);
	}

 	public function recupListDocNum(){
 		if(!count($this->listeDocs)){
			$this->listeDocs = array();
	 		switch($this->params['type']){
	 			case "article" :
	 				$id_article = ($this->params['num_type']*1);
					$query = "select document_link_num_document from cms_documents_links where document_link_type_object = 'article' and document_link_num_object = '".$id_article."'";

					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$i=0;
						while($row = pmb_mysql_fetch_object($result)){
							if($this->params['explnum'] == $row->document_link_num_document){
								$this->current = $i;
							}
							$this->listeDocs[] = intval($row->document_link_num_document);
							$i++;
						}
					}
	 				break;
 				case "section" :
 					$id_section = ($this->params['num_type']*1);
 					$query = "select document_link_num_document from cms_documents_links where document_link_type_object = 'section' and document_link_num_object = '".$id_section."'";

 					$result = pmb_mysql_query($query);
 					if(pmb_mysql_num_rows($result)){
 						$i=0;
 						while($row = pmb_mysql_fetch_object($result)){
 							if($this->params['explnum'] == $row->document_link_num_document){
 								$this->current = $i;
 							}
 							$this->listeDocs[] = intval($row->document_link_num_document);
 							$i++;
 						}
 					}
 					break;
				case 'shared_list':
					if (!AiSharedListDocnumOrm::exist($this->params['id'])) {
						// Le document n'existe pas
						break;
					}

					$aiSharedListDocnum = new AiSharedListDocnumOrm($this->params['id']);
					if (!OpacListeLectureOrm::has_access($aiSharedListDocnum->num_list_ai_shared_list_docnum, $_SESSION['id_empr_session'])) {
						// L'emprunteur n'a pas l'accès à la liste de lecture
						break;
					}

					$this->current = 0;
					$this->params['position'] = 0;
					$this->listeDocs = [
						$aiSharedListDocnum->id_ai_shared_list_docnum
					];
					break;
	 		}
 		}
 		if (isset($this->params['position'])) {
 			$this->current = intval($this->params['position']);
 		}
  	}

	public function getCurrentDoc(){
		$this->currentDoc = array();

		//on peut récup déjà un certain nombre d'infos...
		$this->currentDoc["id"] = $this->listeDocs[$this->current];

		if ($this->params['type'] === 'shared_list') {
			$aiSharedListDocnum = new AiSharedListDocnumOrm($this->params['id']);
			$this->currentDoc["titre"] = $aiSharedListDocnum->name_ai_shared_list_docnum;
			$this->currentDoc["searchterms"] = '';
			$this->currentDoc["mimetype"] = $aiSharedListDocnum->mimetype_ai_shared_list_docnum;
			$this->currentDoc["desc"] = "";
			$this->currentDoc["path"] = $aiSharedListDocnum->getPath();
			$this->currentDoc['extension'] = $aiSharedListDocnum->extfile_ai_shared_list_docnum;
		} else {
			$document = new cms_document($this->currentDoc["id"]);
			$this->currentDoc["titre"] = $document->title ? $document->title : $document->filename;
			$this->currentDoc["searchterms"] = $this->params["user_query"];
			$this->currentDoc["mimetype"] = $document->mimetype;
			//pour le moment, on s'emmerde pas aevc l'article...
			$this->currentDoc["desc"] = "";
			$this->currentDoc["path"] = $document->get_document_in_tmp();
			$this->currentDoc['extension'] = substr($document->filename,strrpos($document->filename,'.')*1+1);
		}
		return $this->currentDoc;
	}

/*******************************************************************
*  Renvoie le contenu du document brut et gère le cache si besoin  *
******************************************************************/
	public function openCurrentDoc(){
		return file_get_contents($this->currentDoc['path']);
	}

	public function forbidden_callback(){
		global $opac_show_links_invisible_docnums;

		$display ="";
		if(!$_SESSION['user_code'] && $opac_show_links_invisible_docnums){
			$auth_popup = new auth_popup();
			$display.= "
			<script>
				auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=pmb_visionneuse_refresh');
				function pmb_visionneuse_refresh(){
					window.location.reload();
				}
			</script>";
		}
		return $display;
	}

	public function getBnfClass($mimetype){
		global $base_path,$class_path,$include_path;

		switch($mimetype){
			case "application/bnf" :
				require_once($class_path."/docbnf.class.php");
				$classname = "docbnf";
				break;
			case "application/bnf+zip" :
				require_once($class_path."/docbnf_zip.class.php");
				$classname = "docbnf_zip";
				break;
		}

		return $classname;
	}

	public function getVisionneuseUrl($params){
		global $base_path;

		$url = $base_path."/visionneuse.php?driver=pmb_document";
		if ($this->params['type'] === 'shared_list') {
			// C'est moche, mais c'est la seule solution pour le moment
			parse_str($params, $result);
			$result['cms_type'] = 'shared_list';
			$result['id'] = $result['explnum'];
			unset($result['explnum']);

			$url .= "&".http_build_query($result);
			return $url;
		}


		if ($params){
			$url.= "&".$params;
		}
		return $url;
	}

	public function getDocumentUrl($id){
		global $opac_url_base;

		if ($this->params['type'] === 'shared_list') {
			return '';
		}
		return $opac_url_base."/ajax.php?module=cms&categ=document&action=render&id=".$id;
	}

	public function getCurrentBiblioInfos(){
// 		global $msg;

// 		$current = $this->listeDocs[$this->current]->explnum_id;
// 		if(!isset($this->biblioInfos[$current])){
// 			$query = "select explnum_notice,explnum_bulletin from explnum where explnum_id = ".$current;
// 			$result = pmb_mysql_query($query);
// 			if(pmb_mysql_num_rows($result)){
// 				$row = pmb_mysql_fetch_object($result);
// 				if($row->explnum_notice){
// 					$query = "select notice_id, tit1, year from notices where notice_id = ".$row->explnum_notice;
// 					$result = pmb_mysql_query($query);
// 					if(pmb_mysql_num_rows($result)){
// 						$row = pmb_mysql_fetch_object($result);
// 						$this->biblioInfos[$current]['title']['value'] = $row->tit1;
// 						$this->biblioInfos[$current]['date']['value'] = $row->year;
// 						$this->biblioInfos[$current]['permalink']['value'] = "./index.php?lvl=notice_display&id=".$row->notice_id;
// 						$aut_query = "select responsability_author from responsability where responsability_notice = ".$row->notice_id." order by responsability_type asc, responsability_ordre asc limit 1";
// 					}
// 				}else{
// 					$query = "select bulletin_id, bulletin_titre,mention_date,date_date,notices.tit1,perio.tit1 as perio_title, notices.notice_id, perio.notice_id as serial_id from bulletins join notices as perio on bulletin_notice = perio.notice_id left join notices on num_notice = notices.notice_id where bulletin_id = ".$row->explnum_bulletin;
// 					$result = pmb_mysql_query($query);
// 					if(pmb_mysql_num_rows($result)){
// 						$row = pmb_mysql_fetch_object($result);
// 						$titre = $row->tit1;
// 						if(!$titre) $titre = $row->bulletin_titre;
// 						$this->biblioInfos[$current]['title']['value'] = $row->perio_title.", ".$titre;
// 						$this->biblioInfos[$current]['date']['value'] = ($row->mention_date ? $row->mention_date : format_date($row->date_date));
// 						$this->biblioInfos[$current]['permalink']['value'] = "./index.php?lvl=bulletin_display&id=".$row->bulletin_id;
// 						$aut_query = "select responsability_author from responsability where responsability_notice = ".($row->notice_id ? $row->notice_id:$row->serial_id)." order by responsability_type asc, responsability_ordre asc limit 1";
// 					}
// 				}
// 				$result = pmb_mysql_query($aut_query);
// 				if(pmb_mysql_num_rows($result)){
// 					$author_id = pmb_mysql_result($result,0,0);
// 					$author= new auteur($author_id);
// 					$this->biblioInfos[$current]['author']['value'] =$author->isbd_entry;
// 				}
// 				$this->biblioInfos[$current]['title']['label'] = $msg['title'];
// 				$this->biblioInfos[$current]['date']['label'] = $msg['serialcirc_ask_date'];
// 				$this->biblioInfos[$current]['permalink']['label'] = $msg['location_more_info'];
// 				$this->biblioInfos[$current]['author']['label'] = $msg['author_search'];
// 			}

// 		}
// 		return $this->biblioInfos[$current];
	}
}
?>