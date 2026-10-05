<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_skos_concepts.class.php,v 1.4 2020/04/30 15:13:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_concepts.class.php');

class searcher_sphinx_skos_concepts extends searcher_sphinx_concepts {

	protected function _get_objects_ids(){
		if(isset($this->objects_ids)){
			return $this->objects_ids;
		}
		parent::_get_objects_ids();
		if (!$this->objects_ids) {
			return $this->objects_ids;
		}
		if ($this->sphinx_query == '*' && count($this->get_filters()) == 0){
		    return $this->objects_ids;
		}
		$query = 'select num_object, id_authority from authorities where id_authority in ('.$this->objects_ids.')'.(($this->sphinx_query != '*') ? ' order by field (id_authority,'.$this->objects_ids.')' : '');
		$this->objects_ids = '';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($this->objects_ids) {
					$this->objects_ids.= ',';
				}
				$this->objects_ids.= $row->num_object;
			}
		}
		return $this->objects_ids;
	}

	protected function get_full_raw_query(){
		return 'select num_object as id, 100 as weight from authorities where type_object = '.$this->authority_type;
	}
}