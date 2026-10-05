<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_authoritieslist.class.php,v 1.3 2016/04/22 13:00:40 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/cms/cms_authority.class.php');

class cms_module_common_view_authoritieslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for authority in authorities %}
	<p>
		<a href='{{ authority.permalink }}'>{{ authority.isbd }}</a>
		{% if authority.comment %}
			{{ authority.comment }}
		{% endif %}
	</p>
{% endfor %}
</div>";
	}
	
	public function render($datas){
		$render_datas = array();
		if(is_array($datas)){
			$render_datas['authorities'] = $datas;
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$authorities = array(
			'var' => "authorities",
			'desc' => $this->msg['cms_module_common_view_authorities_desc'],
			'children' => $this->prefix_var_tree(cms_authority::get_format_data_structure(),"authorities[i]")
		);
		$format[] = $authorities;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}