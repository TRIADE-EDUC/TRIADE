<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ListeLectureTag.php,v 1.2 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB;

class ListeLectureTag extends Collection {
	protected $tag_name;
	
	public function __construct($name,$config) {
		parent::__construct($config);
		$this->type = "liste_lecture_tag";
		$this->tag_name = str_replace(" (R)","",$name);
	}
	
	public function getName() {
		return $this->format_name($this->tag_name." (R)");
	}
	
	public function getChildren(){
		global $msg;
		$children = array();
		global $webdav_current_user_id;
		$query = "select distinct id_liste from opac_liste_lecture
						where 
						tag = '".($msg['webdav_collection_liste_lecture_tag_no_ranking'] == $this->tag_name ? '' : addslashes($this->tag_name))."'
						and (
						id_liste in (select id_liste from opac_liste_lecture where num_empr = '".$webdav_current_user_id."')
						or id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$webdav_current_user_id."' and etat=2)
						or (public = 1 and confidential = 0)
						)";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$children[] = new ListeLecture("(L".$row->id_liste.")",$this->config);
			}
		}
		usort($children,"sortChildren");
		return $children;
	}
}