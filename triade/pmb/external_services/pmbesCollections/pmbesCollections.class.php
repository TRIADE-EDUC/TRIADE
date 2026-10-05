<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesCollections.class.php,v 1.12 2023/08/28 14:01:14 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

class pmbesCollections extends external_services_api_class {
	
	public function list_collection_notices($collection_id, $OPACUserId=-1) {
		$result = array();

		$collection_id = intval($collection_id);
		if (!$collection_id)
			throw new Exception("Missing parameter: collection_id");

		$requete  = "SELECT notice_id FROM notices WHERE (coll_id='$collection_id') "; 
			
		$res = pmb_mysql_query($requete);
		if ($res)
			while($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = $row["notice_id"];
			}
	
		//Je filtre les notices en fonction des droits
		$result=$this->filter_tabl_notices($result);
		
		return $result;
	}
	
	public function list_subcollection_notices($subcollection_id, $OPACUserId=-1) {
		$result = array();

		$subcollection_id = intval($subcollection_id);
		if (!$subcollection_id)
			throw new Exception("Missing parameter: collection_id");
			
		$requete  = "SELECT notice_id FROM notices WHERE (subcoll_id='$subcollection_id') "; 
			
		$res = pmb_mysql_query($requete);
		if ($res)
			while($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = $row["notice_id"];
			}
	
		//Je filtre les notices en fonction des droits
		$result=$this->filter_tabl_notices($result);
		
		return $result;
	}
	
	public function get_collection_information($collection_id) {
		$result = array();

		$collection_id = intval($collection_id);
		if (!$collection_id)
			throw new Exception("Missing parameter: collection_id");
			
		$sql = "SELECT * FROM collections WHERE collection_id = ".$collection_id;
		$res = pmb_mysql_query($sql);
		if (!$res)
			throw new Exception("Not found: collection_id = ".$collection_id);
		$row = pmb_mysql_fetch_assoc($res);

		$result = array(
			"collection_id" => $row["collection_id"],
			"collection_name" => encoding_normalize::utf8_normalize($row["collection_name"]),
			"collection_parent" => $row["collection_parent"],
			"collection_issn" => encoding_normalize::utf8_normalize($row["collection_issn"]),
			"collection_web" => encoding_normalize::utf8_normalize($row["collection_web"]),
			"collection_links" => $this->proxy_parent->pmbesAutLinks_getLinks(4, $collection_id),			
		);
		
		return $result;
	}
	
	public function get_subcollection_information($subcollection_id) {
		$result = array();

		$subcollection_id = intval($subcollection_id);
		if (!$subcollection_id)
			throw new Exception("Missing parameter: sub_coll_id");
			
		$sql = "SELECT * FROM sub_collections WHERE sub_coll_id = ".$subcollection_id;
		$res = pmb_mysql_query($sql);
		if (!$res)
			throw new Exception("Not found: sub_coll_id = ".$subcollection_id);
		$row = pmb_mysql_fetch_assoc($res);
		
		$result = array(
			"sous_collection_id" => $row["sub_coll_id"],
			"sous_collection_name" => encoding_normalize::utf8_normalize($row["sub_coll_name"]),
			"sous_collection_parent" => $row["sub_coll_parent"],
			"sous_collection_issn" => encoding_normalize::utf8_normalize($row["sub_coll_issn"]),
			"sous_collection_web" => encoding_normalize::utf8_normalize($row["subcollection_web"]),
			"sous_collection_links" => $this->proxy_parent->pmbesAutLinks_getLinks(5, $subcollection_id),			
		);
		
		return $result;
	}

	public function get_collection_information_and_notices($collection_id, $OPACUserId=-1) {
		return array(
			"information" => $this->get_collection_information($collection_id),
			"notice_ids" => $this->list_collection_notices($collection_id, $OPACUserId=-1)
		);
	}
	
	public function get_subcollection_information_and_notices($subcollection_id, $OPACUserId=-1) {
		return array(
			"information" => $this->get_subcollection_information($subcollection_id),
			"notice_ids" => $this->list_subcollection_notices($subcollection_id, $OPACUserId=-1)
		);
	}
	
	public function list_collection_subcollections($collection_id) {
		$result = array();

		$collection_id = intval($collection_id);
		if (!$collection_id)
			throw new Exception("Missing parameter: collection_id");
			
		$sql = "SELECT * FROM sub_collections WHERE sub_coll_parent = ".$collection_id;
		$res = pmb_mysql_query($sql);
		if ($res)
			while($row = pmb_mysql_fetch_assoc($res)) {
				$aresult = array(
					"sous_collection_id" => $row["sub_coll_id"],
					"sous_collection_name" => encoding_normalize::utf8_normalize($row["sub_coll_name"]),
					"sous_collection_parent" => $row["sub_coll_parent"],
					"sous_collection_issn" => encoding_normalize::utf8_normalize($row["sub_coll_issn"]),
					"sous_collection_web" => encoding_normalize::utf8_normalize($row["subcollection_web"]),
					"sous_collection_links" => $this->proxy_parent->pmbesAutLinks_getLinks(5, $collection_id),
				);
				$result[] = $aresult;
			}
	
		return $result;
	}
}




?>