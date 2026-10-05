<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Collection.php,v 1.10.4.2 2025/03/13 16:31:11 qvarin Exp $
namespace Sabre\PMB\ScanRequest;

use Sabre\DAV;
use Sabre\PMB;
use encoding_normalize;

class Collection extends PMB\Collection {

	protected $scan_requests = array();

	public function get_code_from_name($name){
	    global $matches;
		$val="";
		if(preg_match("/\(([ERNBPSMI][0-9]{1,})\)$/i",$name,$matches)){
			$val=$matches[1];
		}elseif(preg_match("/\(([ERNBPSMI][0-9]{1,})\)\./i",$name,$matches)){
			$val=$matches[1];
		}
		return $val;
	}

	public function getChildren(){
		$children = array();
		$children_type = "";
		if($this->type == "rootNode"){
			$children_type = $this->config['tree'][0];
		}else{
			for($i=0 ; $i<count($this->config['tree']) ; $i++){
				if($this->config['tree'][$i] == $this->type){
					if($this->config['tree'][$i+1]){
						$children_type = $this->config['tree'][$i+1];
					}
					break;
				}
			}
		}
		$tmp=$this->getScanRequests();//On calcule les restrictions
		switch($children_type){
			case "scan_request_status" :
				$query = "select distinct id_scan_request_status from scan_request_status
						join scan_requests on scan_requests.scan_request_num_status = scan_request_status.id_scan_request_status";
				if($this->restricted_objects){
					$query.= "and id_scan_request in (".$this->restricted_objects.")";
				}
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$children[] = new Status("(S".$row->id_scan_request_status.")", $this->config);
					}
				}
				break;
			case "scan_request_priority" :
				$query = "select distinct id_scan_request_priority from scan_request_priorities
						join scan_requests on scan_requests.scan_request_num_priority = scan_request_priorities.id_scan_request_priority";
				if($this->restricted_objects){
					$query.= "and id_scan_request in (".$this->restricted_objects.")";
				}
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$children[] = new Priority("(P".$row->id_scan_request_priority.")", $this->config);
					}
				}
				break;
			default :
				break;
		}
		usort($children,"sortChildren");
		if((count($tmp)>0) && ($tmp[0] != "'ensemble_vide'")){
			$children = array_merge(array(new ScanRequests($tmp,$this->config)),$children);
		}
		return $children;
	}

	public function getChild($name){
		switch($name){
			case "[Demandes]" :
				$child = new ScanRequests($this->getScanRequests(),$this->config);
				break;
			default :
				$code = $this->get_code_from_name($name);
				if(substr($code,1)*1 > 0){
					switch(substr($code,0,1)){
						//explnum
						case "E" :
							$child = new PMB\Explnum("(".$code.")");
							break;
						//scan_request
						case "R" :
							$child = new ScanRequest("(".$code.")", $this->config);
							break;
						//notice
						case "N" :
							$child = new Notice("(".$code.")", $this->config);
							break;
						//bulletin
						case "B" :
							$child = new Bulletin("(".$code.")", $this->config);
							break;
						//priority
						case "P" :
							$child = new Priority("(".$code.")", $this->config);
							break;
						//status
						case "S" :
							$child = new Status("(".$code.")", $this->config);
							break;
						//Manifestation
						case "M" :
						    $child = new PMB\Music\Manifestation("(".$code.")", $this->config);
							break;
						//Manifestation
						case "I" :
						    $child = new PMB\Music\SubManifestation("(".$code.")", $this->config);
							break;
						default :
							throw new DAV\Exception\BadRequest('Bad Request: ' . $name);
							break;
					}
				}else{
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


	public function childExists($name){
		//pour les besoin des tests, on veut passer par la méthode de création...
		return false;
		switch($name){
			case "[Demandes]" :
				if(count($this->getScanRequests())>0){
					return true;
				}else return false;
				break;
			default :
				$code = $this->get_code_from_name($name);
				if(substr($code,1)*1 > 0){
					switch(substr($code,0,1)){
						case "E" :
						case "R" :
						case "N" :
						case "B" :
						case "P" :
						case "S" :
							return true;
							break;
						default :
							return false;
							break;
					}
				}else{
					$query = "select distinct explnum_id from explnum where explnum_nomfichier = '".addslashes($name)."'";
					$result  = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						return true;
					}else{
						return false;
					}
					break;
				}
		}
	}

	public function getName(){
		//must be defined
		return '';
	}

	public function createFile($name, $data = null) {
		if($this->check_write_permission()){
			global $base_path;
			global $id_rep;
			global $charset;

			$name = str_replace('\"', '', str_replace('\'', '', $name));

			if($charset !=='utf-8'){
				$name=encoding_normalize::utf8_decode($name);
			}
			$filename = realpath($base_path."/temp/")."/webdav_".md5($name.time()).".".extension_fichier($name);
			$fp = fopen($filename, "w");
			if(!$fp){
				//on a pas le droit d'écriture
				throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $filename . ')');
			}

			while ($buf = fread($data, 1024)){
				fwrite($fp, $buf);
			}
			fclose($fp);
			if(!file_exists($filename)){
				//Erreur de copie du fichier
				unlink($filename);
				throw new DAV\Exception\NotFound('Empty file (filename ' . $filename . ')');
			}
			if(!filesize($filename)){
				//Premier PUT d'un client Windows...
				unlink($filename);
				return;
			}

			$notice_id = $this->get_notice_by_meta($name,$filename);
			$bulletin_id = 0;
			$this->update_notice($notice_id);

			$query = "SELECT CONCAT(niveau_biblio, niveau_hierar) AS niveau FROM notices WHERE notice_id = ".$notice_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				if ($row->niveau == "b2") {
					$query = "SELECT bulletin_id FROM bulletins WHERE num_notice = ".$notice_id;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$notice_id = 0;
						$bulletin_id = $row->bulletin_id;
					}
				}
			}
			$explnum = new \explnum(0, $notice_id, $bulletin_id);
			$id_rep = $this->config['upload_rep'];
			$explnum->get_file_from_temp($filename,$name,$this->config['up_place']);
			$explnum->params['explnum_statut'] = $this->config['default_docnum_statut'];
			$explnum->update();
			if(file_exists($filename)){
				unlink($filename);
			}
		}else{
			//on a pas le droit d'écriture
			throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $name . ')');
		}
    }

    public function update_scan_request_infos($scan_request_id){
    	//must be defined
    }

    public function filterScanRequests($query){
    	//on remonte d'abord les parents...
    	$current = $this;
    	$parents = array();
    	while($current->parentNode != null && $current->parentNode->type != "rootNode"){
    		$parents[] = $current->parentNode;
    		$current=$current->parentNode;
    	}
    	$parents = array_reverse($parents);
    	foreach($parents as $parent){
    		$parent->getScanRequests();
    	}

		global $webdav_current_user_id;
 		switch($this->config['authentication']){
			case "gestion" :
				$query = "select uni.id_scan_request from (".$query.") as uni join scan_requests on scan_requests.id_scan_request = uni.id_scan_request join scan_request_status on scan_requests.scan_request_num_status= scan_request_status.id_scan_request_status";
				if($this->parentNode && $this->parentNode->restricted_objects){
					$query.= " and uni.id_scan_request in (".$this->parentNode->restricted_objects.")";
				}
				break;
			case "opac" :
				$query = "select uni.id_scan_request from (".$query.") as uni join scan_requests on scan_requests.id_scan_request = uni.id_scan_request join scan_request_status on scan_requests.scan_request_num_status= scan_request_status.id_scan_request_status where scan_request_status_opac_show=1";
				if($this->parentNode && $this->parentNode->restricted_objects){
					$query.= " and uni.id_scan_request in (".$this->parentNode->restricted_objects.")";
				}
				$query.= " and scan_request_num_dest_empr = ".$webdav_current_user_id;
				break;
			case "anonymous" :
				$query = "select uni.id_scan_request from (".$query.") as uni join scan_requests on scan_requests.id_scan_request = uni.id_scan_request join scan_request_status on scan_requests.scan_request_num_status= scan_request_status.id_scan_request_status";
				if($this->parentNode && $this->parentNode->restricted_objects){
					$query.= " and uni.id_scan_request in (".$this->parentNode->restricted_objects.")";
				}
				break;
			default ://On ne doit jamais passer dans ce cas là
				$query="";
				break;
		}
		$this->scan_requests =array();
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->scan_requests[] = $row->id_scan_request;
			}
		}else{//Si j'ai plus de demande dans cette branche il faut le garde en mémoire sinon dans la branche du dessous on repart avec toute les demandes
			$this->scan_requests[] = "'ensemble_vide'";
		}
		$this->restricted_objects = implode(",",$this->scan_requests);
    }

    public function getScanRequests(){
    	return array();
    }

    public function getQueryFilterNotices($query){
    	global $gestion_acces_active,$gestion_acces_user_notice,$gestion_acces_empr_notice;
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
				}
    			break;
    		case "opac" :
    			$acces_j='';
    			//droit d'accès ou statut
    			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    				$ac= new \acces();
    				$dom_1= $ac->setDomain(2);
    				$acces_j = $dom_1->getJoin($webdav_current_user_id,32,'notice_id');
    				$query = "select notice_id from (".$query.") as uni ".$acces_j;
    			}else{
    				$query = "select uni.notice_id from (".$query.") as uni join notices on notices.notice_id = uni.notice_id
    					join notice_statut on notices.statut= id_notice_statut
    					where ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($webdav_current_user_id ?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")
    					and ((notice_scan_request_opac=1 and notice_scan_request_opac_abon=0)".($webdav_current_user_id ?" or (notice_scan_request_opac_abon=1 and notice_scan_request_opac=1)":"").")";
    			}
    			break;
    		case "anonymous" :
    			//on doit regarder
    			//droit d'accès ou statut
    			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    				$ac= new \acces();
    				$dom_1= $ac->setDomain(2);
    				$acces_j = $dom_1->getJoin(0,32,'notice_id');
    				$query = "select notice_id from (".$query.") as uni ".$acces_j;
    			}else{
    				$query = "select uni.notice_id from (".$query.") as uni join notices on notices.notice_id = uni.notice_id
    						join notice_statut on notices.statut= id_notice_statut
    						where explnum_visible_opac=1 and explnum_visible_opac_abon=0 and notice_scan_request_opac=1 and notice_scan_request_opac_abon=0";
    			}
    			break;
    		default ://On ne doit jamais passer dans ce cas là
    			$query="";
    			break;
    	}
    	return $query;
    }

    public function getQueryFilterBulletins($query){
    	global $gestion_acces_active,$gestion_acces_user_notice,$gestion_acces_empr_notice;
    	global $webdav_current_user_id;
    	switch($this->config['authentication']){
    		case "gestion" :
    			$acces_j='';
    			//soit les droits d'accès sont activés et il est possible que la notice ne soit pas visible pour certaines personnes
    			//soit c'est la requete de base
    			if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
    				$ac= new \acces();
    				$dom_1= $ac->setDomain(1);
    				$acces_j = $dom_1->getJoin($webdav_current_user_id,3,'bulletin_num_notice');
    				$query = "select uni.bulletin_id, uni.bulletin_num_notice from (".$query.") as uni
    					join bulletins on bulletins.bulletin_id = uni.bulletin_id
    					".$acces_j;
				}
    			break;
    		case "opac" :
    			$acces_j='';
    			//droit d'accès ou statut
    			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    				$ac= new \acces();
    				$dom_1= $ac->setDomain(2);
    				$acces_j = $dom_1->getJoin($webdav_current_user_id,32,'bulletin_num_notice');
    				$query = "select uni.bulletin_id, uni.bulletin_num_notice from (".$query.") as uni
    					join bulletins on bulletins.bulletin_id = uni.bulletin_id
    					".$acces_j;
    			}else{
    				$query = "select uni.bulletin_id, uni.bulletin_num_notice from (".$query.") as uni
    					join bulletins on bulletins.bulletin_id = uni.bulletin_id
    					join notices on notices.notice_id = uni.bulletin_num_notice
    					join notice_statut on notices.statut= id_notice_statut
    					where ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($webdav_current_user_id ?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")
    					and ((notice_scan_request_opac=1 and notice_scan_request_opac_abon=0)".($webdav_current_user_id ?" or (notice_scan_request_opac_abon=1 and notice_scan_request_opac=1)":"").")";
    			}
    			break;
    		case "anonymous" :
    			//on doit regarder
    			//droit d'accès ou statut
    			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    				$ac= new \acces();
    				$dom_1= $ac->setDomain(2);
    				$acces_j = $dom_1->getJoin(0,32,'bulletin_num_notice');
    				$query = "select uni.bulletin_id, uni.bulletin_num_notice from (".$query.") as uni
    					join bulletins on bulletins.bulletin_id = uni.bulletin_id
    					".$acces_j;
    			}else{
    				$query = "select uni.bulletin_id, uni.bulletin_num_notice from (".$query.") as uni
    					join bulletins on bulletins.bulletin_id = uni.bulletin_id
    					join notices on notices.notice_id = uni.bulletin_num_notice
    					join notice_statut on notices.statut= id_notice_statut
    					where explnum_visible_opac=1 and explnum_visible_opac_abon=0 and notice_scan_request_opac=1 and notice_scan_request_opac_abon=0";
    			}
    			break;
    		default ://On ne doit jamais passer dans ce cas là
    			$query="";
    			break;
    	}
    	return $query;
    }
}