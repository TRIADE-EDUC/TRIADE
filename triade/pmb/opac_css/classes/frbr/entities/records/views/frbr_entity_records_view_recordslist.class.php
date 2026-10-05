<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_view_recordslist.class.php,v 1.10 2022/09/16 09:43:23 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_view_recordslist extends frbr_entity_common_view_django{
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
		{% for record in records %}
			{{record.content}}
		{% endfor %}";
	}

	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_records_view_recordslist_title"];
		$render_datas['records'] = array();
		if(is_array($datas)){                        
			foreach($datas as $record){
                //Récupération des oeuvres associées
                $works=array();
                $requete="select id_authority from notices_titres_uniformes join authorities on num_object=ntu_num_tu and type_object=7 where ntu_num_notice=".$record;
                $resultat=pmb_mysql_query($requete);
                if ($resultat) {
                    while ($work=pmb_mysql_fetch_object($resultat)) {
                        $authority = authorities_collection::get_authority('authority', $work->id_authority);
                        $titre_uniforme=$authority->get_object_instance();
                        $infos= $titre_uniforme->format_datas();
                        $works[]=$infos;
                    }
                }
				$render_datas['records'][]= array(
						'content' => record_display::get_display_in_result($record, (isset($this->parameters->django_directory) ? $this->parameters->django_directory : "")),
				        'object' => new record_datas($record),
                        'works'=>$works,
                        'items'=>record_display::get_display_expl_list($record),
                        'explnums'=>record_display::get_display_explnums($record)
                );
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_records_view_title']
		);
		$format[] =	array(
			'var' => "records",
			'desc' => $this->msg['frbr_entity_records_view_records_desc'],
			'children' => array(
				array(
					'var' => "records[i].content",
					'desc'=> $this->msg['frbr_entity_records_view_record_content_desc']
				),
				array(
					'var' => "records[i].object",
					'desc'=> $this->msg['frbr_entity_records_view_record_object_desc']
				),
                array(
                    'var' => "records[i].works",
                    'desc' => $this->msg['frbr_entity_records_view_works_desc'],
                    'children' => $this->prefix_var_tree(titre_uniforme::get_format_data_structure(),"records[i].works[i]")
                )
			)
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
