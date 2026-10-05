<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_view_django_by_descriptors.class.php,v 1.2.2.2 2025/01/21 15:29:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_view_django_by_descriptors extends cms_module_common_view_django{

	public function __construct($id=0){
		global $charset;

		parent::__construct($id);
		$this->default_template = "
{% for descriptor in descriptors %}
<div>
    <h3>{{descriptor.label}}</h3>
    {% for item in descriptor.items %}
    {% if item.interesting %}
    {% if item.status!=2 %}
    <div>
        <a href='{{item.url}}' title='Source' target='_blank'><h4>{{item.title}}</h4></a>
        <div>{{item.publication_date}} / {{item.source.title}}</div>
        <div>{{item.summary}}</div>
    </div>
    {% endif %}
    {% endif %}
    {% endfor %}
</div>
{% endfor %}
{% if items %}
<div>
    <h3>Non classés</h3>
    {% for item in items %}
    {% if item.interesting %}
    {% if item.status!=2 %}
    <div>
        <a href='{{item.url}}' title='Source' target='_blank'><h4>{{item.title}}</h4></a>
        <div>{{item.publication_date}} / {{item.source.title}}</div>
        <div>{{item.summary}}</div>
    </div>
    {% endif %}
    {% endif %}
    {% endfor %}
</div>
{% endif %}";
		if ($charset=="utf-8") {
			$this->default_template = encoding_normalize::utf8_normalize($this->default_template);
		}
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
				<label for='cms_module_itemslist_view_descriptor_link'>".$this->format_text($this->msg['cms_module_itemslist_view_django_build_descriptor_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("descriptor");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}

	public function save_form(){
		$this->save_constructor_link_form("item");
		$this->save_constructor_link_form("descriptor");
		return parent::save_form();
	}

	public function render($datas){
		$newdatas = array();
		$descriptors = array();

		if (is_countable($datas['items'])) {
			for ($i=0 ; $i<count($datas['items']) ; $i++){
				$datas['items'][$i]['link'] = $this->get_constructed_link('item',$datas['items'][$i]['id']);
				if (is_countable($datas['items'][$i]['descriptors']) && count($datas['items'][$i]['descriptors'])) {
					for($j=0 ; $j<count($datas['items'][$i]['descriptors']) ; $j++){
						$datas['items'][$i]['descriptors'][$j]['link'] = $this->get_constructed_link('descriptor',$datas['items'][$i]['descriptors'][$j]['id']);
						$descriptors[$datas['items'][$i]['descriptors'][$j]['label']]['items'][] = $datas['items'][$i];
						$descriptors[$datas['items'][$i]['descriptors'][$j]['label']]['label'] = $datas['items'][$i]['descriptors'][$j]['label'];
						$descriptors[$datas['items'][$i]['descriptors'][$j]['label']]['link'] = $datas['items'][$i]['descriptors'][$j]['link'];
					}
				} else {
					$newdatas['items'][] = $datas['items'][$i];
				}
			}
		}

		ksort($descriptors);
		$newdatas['descriptors'] = $descriptors;

		return parent::render($newdatas);
	}

	public function get_format_data_structure() {
		$datas = array(
				array(
						'var' => "descriptors",
						'desc' => $this->msg['cms_module_itemslist_view_django_by_descriptors_descriptors_desc'],
						'children' => array(
								array(
										'var' => "descriptors[i].id",
										'desc' => $this->msg['cms_module_itemslist_view_django_by_descriptors_descriptors_id_desc'],

								),
								array(
										'var' => "descriptors[i].label",
										'desc' => $this->msg['cms_module_itemslist_view_django_by_descriptors_descriptors_label_desc'],
								),
								array(
										'var' => "descriptors[i].items",
										'desc' => $this->msg['cms_module_itemslist_view_django_by_descriptors_descriptors_items_desc'],
										'children' => $this->prefix_var_tree(docwatch_item::get_format_data_structure(),"descriptors[i].items[j]")
								),
								array(
										'var' => "descriptors[i].link",
										'desc' => $this->msg['cms_module_itemslist_view_django_by_descriptors_descriptor_link_desc']
								)
						),
						array(
								'var' => "items",
								'desc' => $this->msg['cms_module_itemslist_view_django_by_descriptors_items_desc'],
								'children' => $this->prefix_var_tree(docwatch_item::get_format_data_structure(),"items[i]")
						),
				)
		);
		$datas[0]['children'][2]['children'][] = array(
				'var' => "descriptors[i].items[j].link",
				'desc'=> $this->msg['cms_module_itemslist_view_django_by_descriptors_item_link_desc']
		);
		$datas[0]['children'][2]['children'][11]['children'][] = array(
				'var' => "descriptors[i].items[j].descriptors[i].link",
				'desc'=> $this->msg['cms_module_itemslist_view_django_by_descriptors_descriptor_link_desc']
		);
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}