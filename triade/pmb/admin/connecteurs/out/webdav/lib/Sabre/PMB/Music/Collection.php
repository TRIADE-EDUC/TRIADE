<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Collection.php,v 1.9.12.1 2025/03/13 16:31:11 qvarin Exp $
namespace Sabre\PMB\Music;

use Sabre\DAV;
use Sabre\PMB;

class Collection extends PMB\Collection {

	protected $sub_manifestations;

	public function get_code_from_name($name){
	    global $matches;
		$val="";
		if(preg_match("/\(([ERMBKWCFPIVA][0-9]{1,})\)$/i",$name,$matches)){
			$val=$matches[1];
		}elseif(preg_match("/\(([ERMBKWCFPIVA][0-9]{1,})\)\./i",$name,$matches)){
			$val=$matches[1];
		}
		return $val;
	}

	public function getChildren() {
		return [];
	}


	public function getChild($name){
		switch($name){
			default :
				$code = $this->get_code_from_name($name);
				if(substr($code,1)*1 > 0){
					switch(substr($code,0,1)){
						//explnum
						case "E" :
							$child = new PMB\Explnum("(".$code.")");
							break;
							//scan_request
						case "K" :
							$child = new Event("(".$code.")", $this->config);
							break;
							//notice
						case "M" :
							$child = new Manifestation("(".$code.")", $this->config);
							break;
							//bulletin
						case "W" :
							$child = new Work("(".$code.")", $this->config);
							break;
						case "C" :
							$child = new Moment("(".$code.")", $this->config);
							break;
						case "F" :
							$child = new Formation("(".$code.")", $this->config);
							break;
						case "P" :
							$child = new Musicstand("(".$code.")", $this->config);
							break;
						case "I" :
							$child = new SubManifestation("(".$code.")", $this->config);
							break;
						case "V" :
							$child = new Voice("(".$code.")", $this->config);
							break;
						case "A" :
							$child = new Workshop("(".$code.")", $this->config);
							break;
						default :
							throw new DAV\Exception\BadRequest('Bad Request: ' . $name);
							break;
					}
				} else if ((substr($code,1)*1 == 0) && (substr($code,0,1) == 'P')) {
					// Cas particulier des instruments non standards
					$child = new Musicstand("(".$code.")", $this->config);
				} else {
					//document numérique d'une notice
					$query = "select distinct explnum_id,notice_id from explnum join notices on explnum_bulletin = 0 and explnum_notice = notice_id where explnum_nomfichier = '".addslashes($name)."' and explnum_mimetype != 'URL'";
					//document numériques d'une notice de bulletin
					$query.= "union select distinct explnum_id,notice_id from explnum join bulletins on explnum_notice = 0 and explnum_bulletin = bulletin_id join notices on num_notice != 0 and num_notice = notice_id where explnum_nomfichier = '".addslashes($name)."' and explnum_mimetype != 'URL'";
					//$query = $this->filterExplnums($query);
					$result  = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$child = new PMB\Explnum("(E".$row->explnum_id.")");
					}else{
					    throw new DAV\Exception\NotFound('File not found: ' . $name);
					}
					break;
				}
		}
		return $child;
	}



	public function getName(){
		//must be defined
		return '';
	}

	public function createFile($name, $data = null) {
		throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $name . ')');
    }

    public function filter_sub_manifestations($query){
    	//on remonte d'abord les parents...
    	$current = $this;
    	$parents = array();
    	while($current->type != "manifestation"){
    		$parents[] = $current->parentNode;
    		$current=$current->parentNode;
    	}
    	$parents = array_reverse($parents);
    	foreach($parents as $parent){
    		$parent->get_submanifestations();
    	}
    	global $gestion_acces_active,$gestion_acces_user_notice,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
    	global $webdav_current_user_id;
    	switch($this->config['authentication']){
    		case "gestion" :
    			$acces_j='';
    			//soit les droits d'accès sont activés et il est possible que la notice ne soit pas visible pour certaines personnes
    			//soit c'est la requete de base
    			if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	    			$ac= new \acces();
	    			$dom_1= $ac->setDomain(1);
	    			$acces_j = $dom_1->getJoin($webdav_current_user_id,3,'notice_id');
					$query = "select notice_id from (".$query.") as uni ".$acces_j;
    				if($this->parentNode && $this->parentNode->restricted_objects){
						$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
	    			}
    			}elseif($this->parentNode && $this->parentNode->restricted_objects){//Si la gestion des droits n'est pas activé il faut quand même restreindre la recherche
    				$query = "select notice_id from (".$query.") as uni ";
    				$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
    			}
    			break;
    		case "opac" :
    			$acces_j='';
    			//droit d'accès ou statut
    			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    				$ac= new \acces();
	    			$dom_1= $ac->setDomain(2);
	   				$acces_j = $dom_1->getJoin($webdav_current_user_id,16,'notice_id');
	   				$query = "select notice_id from (".$query.") as uni ".$acces_j;
					if($this->parentNode && $this->parentNode->restricted_objects){
	   						$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
	   				}
   				}else{
   					$query = "select uni.notice_id from (".$query.") as uni join notices on notices.notice_id = uni.notice_id join notice_statut on notices.statut= id_notice_statut where ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($webdav_current_user_id ?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
					if($this->parentNode && $this->parentNode->restricted_objects){
	   					$query.= " and uni.notice_id in (".$this->parentNode->restricted_objects.")";
	   				}
   				}
   				break;
   			case "anonymous" :
   				//on doit regarder
   				//droit d'accès ou statut
   				if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
   					$ac= new \acces();
   					$dom_1= $ac->setDomain(2);
   					$acces_j = $dom_1->getJoin(0,16,'notice_id');
   					$query = "select notice_id from (".$query.") as uni ".$acces_j;
   					if($this->parentNode && $this->parentNode->restricted_objects){
    					$query.= " where uni.notice_id in (".$this->parentNode->restricted_objects.")";
					}
				}else{
					$query = "select uni.notice_id from (".$query.") as uni join notices on notices.notice_id = uni.notice_id join notice_statut on notices.statut= id_notice_statut where explnum_visible_opac=1 and explnum_visible_opac_abon=0";
					if($this->parentNode && $this->parentNode->restricted_objects){
   						$query.= " and uni.notice_id in (".$this->parentNode->restricted_objects.")";
   					}
				}
				break;
			default ://On ne doit jamais passer dans ce cas là
   				$query="";
   				break;
    	}
    	$this->sub_manifestations =array();
		if (!$this->check_write_permission()) {
			//vérification des droits sur les documents numériques
			switch($this->config['authentication']){
				case "opac" :
					if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
						$ac= new \acces();
						$dom_3= $ac->setDomain(3);
						$acces_j = $dom_3->getJoin($webdav_current_user_id,16,'explnum_id');
						$query = "select distinct explnum_notice as notice_id from explnum $acces_j where explnum_notice in ($query)";
					}else{
						// vérification du statut de chaque document
						$query = "select distinct explnum_notice as notice_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac=1 and explnum_notice in ($query)";
					}
					break;
				case "anonymous" :
					//on doit requeter les droits d'accès propre à chaque document
					if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
						$ac= new \acces();
						$dom_3= $ac->setDomain(3);
						$acces_j = $dom_3->getJoin(0,16,'explnum_id');
						$query = "select distinct explnum_notice as notice_id from explnum $acces_j where explnum_notice in ($query)";
					}else{
						// vérification du statut de chaque document
						$query = "select distinct explnum_notice as notice_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac=1 and explnum_visible_opac_abon=0 and explnum_notice in ($query)";
					}
					break;
				case "gestion" :
				default :
					$query = 'select distinct explnum_notice as notice_id from explnum where explnum_notice in ('.$query.')';
					break;
			}
		}
    	$result = pmb_mysql_query($query);
    	if(pmb_mysql_num_rows($result)){
    		while($row = pmb_mysql_fetch_object($result)){
    			$this->sub_manifestations[] = $row->notice_id;
    		}
    	}else{//Si j'ai plus de notice dans cette branche il faut le garde en mémoire sinon dans la branche du dessous on repart avec toute les notices
    		$this->sub_manifestations[] = "'ensemble_vide'";
    	}
    	$this->restricted_objects = implode(",",$this->sub_manifestations);
    }

}