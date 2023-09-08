<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_search_perso.class.php,v 1.1 2018-04-17 12:48:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/search_universes/search_segment_search_perso.tpl.php");

class search_segment_search_perso {
	
	protected $num_segment;
	
	protected $search_perso;
	
	public function __construct($num_segment = 0){
		$this->num_segment = $num_segment+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->search_perso = array();
		$this->opac = 1;
		$this->order = 0;
		if ($this->num_segment) {
			$query = "SELECT num_search_perso FROM search_segments_search_perso WHERE num_search_segment = '".$this->num_segment."' and search_segment_search_perso_opac = 1 order by search_segment_search_perso_order";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_assoc($result)) {
					$this->search_perso[] = $row['num_search_perso'];
				}
			}
		}
	}
	
	public function get_search_perso() {
		return $this->search_perso;
	}
	
	public function get_form($type = 'record') {
	    global $msg, $charset, $base_url;
	    global $segment_search_perso_list_form, $segment_search_perso_list_line_form;
	    
	    $lst = "";
	    $query = "SELECT * FROM search_persopac WHERE search_type = '".$type."' ORDER BY search_order, search_name";
	    $result = pmb_mysql_query($query);
	    $i = 0;
	    while ($row = pmb_mysql_fetch_assoc($result)) {
	        if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";
	        $line = $segment_search_perso_list_line_form;
	        $line = str_replace('!!search_perso_class!!', $pair_impair, $line);
	        $line = str_replace('!!search_perso_type!!', 'segment_search_perso[]', $line);
	        $line = str_replace('!!search_perso_checked!!', (in_array($row['search_id'], $this->search_perso) ? "checked" : ""), $line);
	        $line = str_replace('!!search_perso_id!!', $row['search_id'], $line);
	        $line = str_replace('!!search_perso_name!!', htmlentities($row['search_name'], ENT_QUOTES, $charset), $line);
	        $line = str_replace('!!search_perso_shortname!!', htmlentities($row['search_shortname'], ENT_QUOTES, $charset), $line);
	        $line = str_replace('!!search_perso_human!!', $row['search_human'], $line);
	        $line = str_replace('!!search_perso_link!!', $base_url."/admin.php?categ=opac&sub=search_persopac&section=liste&action=form&id=".$row['search_id'], $line);
	        $lst.= $line;
	        $i++;
	    }
	    
	    $segment_search_perso_list_form = str_replace('!!search_perso_list!!', $lst, $segment_search_perso_list_form);
	    return $segment_search_perso_list_form;
	}
		
	public function set_properties_from_form(){
        global $segment_search_perso;
        $this->search_perso = array();
	    if (!empty($segment_search_perso)) {
	        $this->search_perso = $segment_search_perso;
	    }
	}
	
	public function save() {
		static::delete($this->num_segment);
		foreach($this->search_perso as $order=>$num_search_perso) {
			$query = 'INSERT INTO search_segments_search_perso SET
				num_search_segment = '.$this->num_segment.',
				num_search_perso = "'.$num_search_perso.'",
				search_segment_search_perso_opac = "1",
				search_segment_search_perso_order = "'.$order.'"';
			pmb_mysql_query($query);
		}
	}
	
	public static function delete($id=0) {
		$id += 0;
		if (!$id) {
			return;
		}
		$query = "delete from search_segments_search_perso where num_search_segment = ".$id;
		pmb_mysql_query($query);
	}
}