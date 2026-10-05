<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Notice.php,v 1.6.12.1 2025/03/13 16:31:11 qvarin Exp $
namespace Sabre\PMB\ScanRequest;

class Notice extends Collection {
	protected $notice_id;

	public function __construct($name,$config) {
		parent::__construct($config);
		$this->notice_id = substr($this->get_code_from_name($name),1);
		$this->type = "scan_request_notice";
	}

	public function getName() {
		$query = "select notices.tit1 as title from notices where notice_id = ".$this->notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$name = $row->title." (N".$this->notice_id.")";
		}
		return $this->format_name($name);
	}

	public function getChildren() {
		$children = array();
		$query = "select scan_request_explnum_num_explnum as explnum_id from scan_request_explnum join explnum on scan_request_explnum_num_explnum = explnum_id where explnum_mimetype!= 'URL' and scan_request_explnum_num_notice = ".$this->notice_id." and scan_request_explnum_num_bulletin = 0 and scan_request_explnum_num_request = ".$this->parentNode->get_scan_request()->get_id();
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
		return $this->get_parent_by_type('scan_request')->create_scan_request_file($this->notice_id, 0, $name, $data);
    }

    public function getLastModified() {
    	$query = 'select update_date from notices where notice_id = '.$this->notice_id;
    	$result = pmb_mysql_query($query);
    	if (pmb_mysql_num_rows($result)) {
    		$row = pmb_mysql_fetch_object($result);
    		return $row->update_date;
    	}
		return null;
    }
}