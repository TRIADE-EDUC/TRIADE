<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_series_view_serieslist.class.php,v 1.4 2021/03/01 11:04:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_series_view_serieslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for serie in series %}
<h3>{{serie.name}}</h3>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_series_view_serieslist_title"];
		$render_datas['series'] = array();
		if(is_array($datas)){
			foreach($datas as $serie_id){
				$render_datas['series'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $serie_id, 'type_object' => AUT_TABLE_SERIES]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_series'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_series'][$key])) {
		            $render_datas['grouped_series'][$key] = [];
		        }
		        $render_datas['grouped_series'][$key]['label'] = $group["label"];
		        $render_datas['grouped_series'][$key]["values"] = [];
		        foreach ($group["values"] as $serie_id) {
		            $render_datas['grouped_series'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $serie_id, 'type_object' => AUT_TABLE_SERIES]);
		        }
		    }
		    usort($render_datas['grouped_series'], function ($item1, $item2) {
		        return $item1['label'] <=> $item2['label'];
		    });
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_series_view_title']
		);
		$series = array(
			'var' => "series",
			'desc' => $this->msg['frbr_entity_series_view_series_desc'],
			'children' => authority::get_properties(AUT_TABLE_SERIES,"series[i]")
		);
		$format[] = $series;
		$format[] = array(
		    'var' => "grouped_seres",
		    'desc' => $this->msg['frbr_entity_seres_view_grouped_seres'],
		    'children' => [
		        [
		            'var' => "grouped_seres.key.label",
		            'desc' => $this->msg['frbr_entity_seres_view_grouped_seres_label']
		            
		        ],
		        [
		            'var' => "grouped_seres.key.values",
		            'desc' => $this->msg['frbr_entity_seres_view_grouped_seres_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}