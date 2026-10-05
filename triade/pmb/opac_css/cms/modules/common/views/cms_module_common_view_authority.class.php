<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_authority.class.php,v 1.4 2016/04/22 13:00:40 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_authority extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<p>
	<a href='{{ authority.permalink }}' title='{{ authority.isbd }}'>{{ authority.isbd }}</a>
	{% if authority.comment %}
		{{ authority.comment }}
	{% endif %}
</p>";
	}
	
	public function get_format_data_structure(){
		return $this->prefix_var_tree(cms_authority::get_format_data_structure(), 'authority');
	}
	
	public function render($datas) {
		return parent::render(array('authority' => $datas));
	}
}