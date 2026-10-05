<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_cadre.class.php,v 1.1.2.2 2025/01/31 15:46:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_cadre{

	protected $id = 0;

	protected $hash = '';
	
	protected $name = '';
	
	protected $fixed = 0;
	
	protected $styles = [];
	
	protected $dom_parent = '';
	
	protected $dom_after = '';
	
	protected $memo_url = 0;
	
	protected $cadre_url = '';
	
	protected $classement = '';
	
	protected $modcache = '';
	
	protected $css_class = '';

	protected $datasource = array();

	protected $filters = array();
	
	protected $view = array();
	
	protected $conditions = array();

	public function __construct($id=0){
		$this->id = intval($id);
		$this->fetch_data();
	}

	protected function fetch_data() {
		$query = "SELECT * FROM cms_cadres where id_cadre = '".$this->id."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->hash = $row->cadre_hash;
			$this->name = $row->cadre_name;
			$this->fixed = $row->cadre_fixed;
			$this->styles = unserialize($row->cadre_styles);
			$this->dom_parent = $row->cadre_dom_parent;
			$this->dom_after = $row->cadre_dom_after;
			$this->memo_url = $row->cadre_memo_url;
			$this->cadre_url = $row->cadre_url;
			$this->classement = $row->cadre_classement;
			$this->modcache = $row->cadre_modcache;
			$this->css_class = $row->cadre_css_class;
			
			$query = "select id_cadre_content,cadre_content_object,cadre_content_type from cms_cadre_content where cadre_content_num_cadre = '" . $this->id . "' and cadre_content_num_cadre_content=0";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
			    while ($ligne = pmb_mysql_fetch_object($result)) {
			        switch ($ligne->cadre_content_type) {
			            case "datasource":
			                $this->datasource = array(
			                'id' => $ligne->id_cadre_content,
			                'name' => $ligne->cadre_content_object
			                );
			                break;
			            case "filter":
			                $this->filters[] = array(
			                'id' => $ligne->id_cadre_content,
			                'name' => $ligne->cadre_content_object
			                );
			                break;
			            case "view":
			                $this->view = array(
			                'id' => $ligne->id_cadre_content,
			                'name' => $ligne->cadre_content_object
			                );
			                break;
			            case "condition":
			                $this->conditions[] = array(
			                'id' => $ligne->id_cadre_content,
			                'name' => $ligne->cadre_content_object
			                );
			                break;
			            default:
			                break;
			        }
			    }
			}
		}
	}

	public function get_name() {
		return $this->name;
	}
	
	public function get_fixed() {
	    return $this->fixed;
	}
	
	public function get_styles() {
	    return $this->styles;
	}
	
	public function get_memo_url() {
	    return $this->memo_url;
	}
	
	public function get_classement() {
	    return $this->classement;
	}
	
	public function get_modcache() {
	    return $this->modcache;
	}

	public function get_datasource() {
	    return $this->datasource;
	}
	
	public function get_filters() {
	    return $this->filters;
	}
	
	public function get_view() {
	    return $this->view;
	}
	
	public function get_conditions() {
	    return $this->conditions;
	}
}