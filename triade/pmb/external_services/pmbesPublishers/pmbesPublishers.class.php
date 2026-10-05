<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesPublishers.class.php,v 1.10 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

class pmbesPublishers extends external_services_api_class {
	
	public function list_publisher_notices($publisher_id, $OPACUserId=-1) {
		$result = array();

		$publisher_id = intval($publisher_id);
		if (!$publisher_id)
			throw new Exception("Missing parameter: author_id");
			
		$requete  = "SELECT notice_id FROM notices WHERE (ed1_id='$publisher_id' or ed2_id='$publisher_id')"; 
		$res = pmb_mysql_query($requete);
		if ($res)
			while($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = $row["notice_id"];
			}
	
		//Je filtre les notices en fonction des droits
		$result=$this->filter_tabl_notices($result);
		
		return $result;
	}
	
	public function get_publisher_information($publisher_id) {
		$result = array();

		$publisher_id = intval($publisher_id);
		if (!$publisher_id)
			throw new Exception("Missing parameter: publisher_id");
			
		$sql = "SELECT * FROM publishers WHERE ed_id = ".$publisher_id;
		$res = pmb_mysql_query($sql);
		if (!$res)
			throw new Exception("Not found: publisher_id = ".$publisher_id);
		$row = pmb_mysql_fetch_assoc($res);

		$result = array(
			"publisher_id" => $row["ed_id"],
			"publisher_name" => encoding_normalize::utf8_normalize($row["ed_name"]),
			"publisher_address1" => encoding_normalize::utf8_normalize($row["ed_adr1"]),
			"publisher_address2" => encoding_normalize::utf8_normalize($row["ed_adr2"]),
			"publisher_zipcode" => encoding_normalize::utf8_normalize($row["ed_cp"]),
			"publisher_city" => encoding_normalize::utf8_normalize($row["ed_ville"]),
			"publisher_country" => encoding_normalize::utf8_normalize($row["ed_pays"]),
			"publisher_web" => encoding_normalize::utf8_normalize($row["ed_web"]),
			"publisher_comment" => encoding_normalize::utf8_normalize($row["ed_comment"]),
			"publisher_links" => $this->proxy_parent->pmbesAutLinks_getLinks(3, $publisher_id),		
		);
		
		return $result;
	}

	public function get_publisher_information_and_notices($publisher_id, $OPACUserId=-1) {
		$publisher_id = intval($publisher_id);
		return array(
			"information" => $this->get_publisher_information($publisher_id),
			"notice_ids" => $this->list_publisher_notices($publisher_id, $OPACUserId=-1)
		);
	}
}




?>