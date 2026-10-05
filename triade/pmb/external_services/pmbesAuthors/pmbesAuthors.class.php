<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesAuthors.class.php,v 1.12 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

class pmbesAuthors extends external_services_api_class {
	
	public function list_author_notices($author_id, $OPACUserId=-1) {
		$result = array();

		$author_id = intval($author_id);
		if (!$author_id)
			throw new Exception("Missing parameter: author_id");

		$rqt_auteurs = "select author_id as aut from authors where author_see='$author_id' and author_id!=0 ";
		$rqt_auteurs .= "union select author_see as aut from authors where author_id='$author_id' and author_see!=0 " ;
		$res_auteurs = pmb_mysql_query($rqt_auteurs);
		$clause_auteurs = " in ('$author_id' ";
		while(($id_aut=pmb_mysql_fetch_object($res_auteurs))) {
			$clause_auteurs .= ", '".$id_aut->aut."' ";
			$rqt_auteursuite = "select author_id as aut from authors where author_see='$id_aut->aut' and author_id!=0 ";
			$res_auteursuite = pmb_mysql_query($rqt_auteursuite);
			while(($id_autsuite=pmb_mysql_fetch_object($res_auteursuite))) $clause_auteurs .= ", '".$id_autsuite->aut."' "; 
		} 
		$clause_auteurs .= " ) " ;
		
		$requete = "SELECT distinct notices.notice_id FROM notices, responsability ";
		$requete.= "where responsability_author $clause_auteurs and notice_id=responsability_notice ";
		$requete.= "ORDER BY index_serie,tnvol,index_sew";
		
		$res = pmb_mysql_query($requete);
		if ($res)
			while($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = $row["notice_id"];
			}
	
		//Je filtre les notices en fonction des droits
		$result=$this->filter_tabl_notices($result);
	
		return $result;
	}
	
	public function get_author_information($author_id) {
		$result = array();

		$author_id = intval($author_id);
		if (!$author_id)
			throw new Exception("Missing parameter: author_id");
			
		$sql = "SELECT * FROM authors WHERE author_id = ".$author_id;
		$res = pmb_mysql_query($sql);
		if (!$res)
			throw new Exception("Not found: author_id = ".$author_id);
		$row = pmb_mysql_fetch_assoc($res);
		
		$result = array(
			"author_id" => $row["author_id"],
			"author_type" => $row["author_type"],
			"author_name" => encoding_normalize::utf8_normalize($row["author_name"]),
			"author_rejete" => encoding_normalize::utf8_normalize($row["author_rejete"]),
			"author_see" => $row["author_see"],
			"author_date" => encoding_normalize::utf8_normalize($row["author_date"]),
		    "author_web" => encoding_normalize::utf8_normalize($row["author_web"]),
		    "author_isni" => encoding_normalize::utf8_normalize($row["author_isni"]),
			"author_comment" => encoding_normalize::utf8_normalize($row["author_comment"]),
			"author_lieu" => encoding_normalize::utf8_normalize($row["author_lieu"]),
			"author_ville" => encoding_normalize::utf8_normalize($row["author_ville"]),
			"author_pays" => encoding_normalize::utf8_normalize($row["author_pays"]),
			"author_subdivision" => encoding_normalize::utf8_normalize($row["author_subdivision"]),
			"author_numero" => encoding_normalize::utf8_normalize($row["author_numero"])
		);
		if(method_exists($this->proxy_parent,"pmbesAutLinks_getLinks")){
			$result['author_links'] =$this->proxy_parent->pmbesAutLinks_getLinks(1, $author_id);
		}else{
			$result['author_links'] = array();
		}
		return $result;
	}
	
	public function get_author_information_and_notices($author_id, $OPACUserId=-1) {
		$result = array(
			"information" => $this->get_author_information($author_id),
			"notice_ids" => $this->list_author_notices($author_id, $OPACUserId)
		);
		return $result;
	}

}




?>