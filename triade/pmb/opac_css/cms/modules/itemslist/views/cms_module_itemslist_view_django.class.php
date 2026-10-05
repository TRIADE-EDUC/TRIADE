<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_view_django.class.php,v 1.5.2.2 2025/01/21 15:29:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_view_django extends cms_module_common_view_django{

	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for item in items %}
{% if item.interesting %}
{% if item.status!=2 %}
<div>
    <a href='{{item.url}}' title='Source' target='_blank'>
        <h3>{{item.title}}</h3>
    </a>
    <div>{{item.publication_date}} / {{item.source.title}}</div>
    <div>{{item.summary}}</div>
</div>
{% endif %}
{% endif %}
{% endfor %}
</div>";
	}

	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_itemslist_view_item_link'>".$this->format_text($this->msg['cms_module_itemslist_view_django_build_item_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("item");
		$form.="
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_itemslist_view_tag_link'>".$this->format_text($this->msg['cms_module_itemslist_view_django_build_tag_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("tag");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}

	public function save_form(){
		$this->save_constructor_link_form("item");
		$this->save_constructor_link_form("tag");
		return parent::save_form();
	}

	public function render($datas){
		if(is_array($datas['items']) && count($datas['items'])){
			for($i=0 ; $i<count($datas['items']) ; $i++){
				$datas['items'][$i]['link'] = $this->get_constructed_link('item',$datas['items'][$i]['id']);
				$tags = $datas['items'][$i]['tags'];
				if (is_countable($tags)) {
					for($j=0 ; $j<count($tags) ; $j++){
						$datas['items'][$i]['tags'][$j]['link'] = $this->get_constructed_link('tag',$tags[$j]['id']);
					}
				}
			}
		}
		return parent::render($datas);
	}

	public function get_format_data_structure(){
		$datasource = new cms_module_itemslist_datasource_items();
		$datas = $datasource->get_format_data_structure();

		$datas[0]['children'][] = array(
				'var' => "items[i].item.link",
				'desc'=> $this->msg['cms_module_itemslist_view_django_item_link_desc']
		);
		$datas[0]['children'][11]['children'][] = array(
				'var' => "items[i].item.tags[j].link",
				'desc'=> $this->msg['cms_module_itemslist_view_django_tag_link_desc']
		);

		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}