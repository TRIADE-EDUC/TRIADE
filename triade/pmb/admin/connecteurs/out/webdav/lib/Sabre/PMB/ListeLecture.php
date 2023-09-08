<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ListeLecture.php,v 1.2 2019-04-03 13:34:40 ngantier Exp $
namespace Sabre\PMB;

class ListeLecture extends Collection {
	protected $liste_lecture;

	function __construct($name,$config) {
		parent::__construct($config);
		$this->type = "liste_lecture";
		$code = $this->get_code_from_name($name);
		$id = substr($code,1);
		if($id){
			$this->liste_lecture = new \liste_lecture($id);
		}
	}
	
	function getName() {
		return $this->format_name($this->liste_lecture->nom_liste." (L".$this->liste_lecture->id_liste.")");
	}
	
	function getNotices() {
		$this->notices = array();
		if ($this->liste_lecture->id_liste) {
		    $liste = new \liste_lecture($this->liste_lecture->id_liste);
		    $notices = $liste->notices;
		    if (is_array($notices) && count($notices)) {
		        $query = "select notice_id from notices where notice_id in (".implode(',', $notices).")";
		        $this->filterNotices($query);
		    }
		}
		return $this->notices;
	}
}