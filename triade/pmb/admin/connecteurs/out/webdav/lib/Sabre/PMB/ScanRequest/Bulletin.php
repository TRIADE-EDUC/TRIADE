<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Bulletin.php,v 1.6.12.1 2025/03/13 16:31:11 qvarin Exp $
namespace Sabre\PMB\ScanRequest;

class Bulletin extends Collection {
	protected $bulletin_id;

	public function __construct($name,$config) {
		parent::__construct($config);

		$this->bulletin_id = substr($this->get_code_from_name($name),1);
		$this->type = "scan_request_bulletin";
	}

	public function getName() {
		$query = "select concat(bulletins.bulletin_numero, ' - ',bulletins.bulletin_titre) as title from bulletins where bulletin_id = ".$this->bulletin_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$name = $row->title." (B".$this->bulletin_id.")";
		}
		return $this->format_name($name);
	}

	public function getChildren() {
		$children = array();
		$query = "select scan_request_explnum_num_explnum as explnum_id from scan_request_explnum join explnum on scan_request_explnum_num_explnum = explnum_id where explnum_mimetype!= 'URL' and scan_request_explnum_num_bulletin = ".$this->bulletin_id." and scan_request_explnum_num_notice = 0 and scan_request_explnum_num_request = ".$this->parentNode->get_scan_request()->get_id();
		$query = $this->filterExplnums($query);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$children[] = $this->getChild("(E".$row->explnum_id.")");
			}
		}
		return $children;
	}

	public function createFile($name, $data = null) {
		return $this->get_parent_by_type('scan_request')->create_scan_request_file(0, $this->bulletin_id, $name, $data);
	}

    public function getLastModified() {
    	$query = 'select date_date from bulletins where bulletin_id = '.$this->bulletin_id;
    	$result = pmb_mysql_query($query);
    	if (pmb_mysql_num_rows($result)) {
    		$row = pmb_mysql_fetch_object($result);
    		return $row->date_date;
    	}
		return null;
    }
}